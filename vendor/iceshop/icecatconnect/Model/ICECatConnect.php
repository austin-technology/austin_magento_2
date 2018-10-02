<?php

namespace ICEShop\ICECatConnect\Model;

use ICEShop\ICECatConnect\Api\ICECatConnectInterface;
use ICEShop\ICECatConnect\Model\ICECatConnectDB;
use Magento\Framework\App\Action\Context;
use \Magento\Catalog\Api\Data\ProductAttributeInterface;
use \Magento\Config\Model\ResourceModel\Config;
use \Magento\Framework\App\ObjectManager;
use Magento\Framework\System\Ftp;
use Magento\Sales\Controller\Adminhtml\Transactions\Fetch;
use Magento\Framework\Logger\Monolog;

/**
 * Defines the implementaiton class of the calculator service contract.
 */
class ICECatConnect implements ICECatConnectInterface
{
    /*
     * Constant for set mode `Scheduled` at indexer processes
     */
    const INDEXER_MODE_SCHEDULED = 'scheduled';

    /*
     * Constant for set mode `Update On Save` at indexer processes
     */
    const INDEXER_MODE_UPDATE_ON_SAVE = 'update_on_save';

    public $arFiltersMap = [
        'product_id' => 'entity_id',
        'set' => 'attribute_set_id',
        'type' => 'type_id'
    ];

    /*
     * Log path
     */

    public $loggerPath = DIRECTORY_SEPARATOR . 'var' . DIRECTORY_SEPARATOR . 'log' . DIRECTORY_SEPARATOR . 'ICECatConnect.log';

    /*
     * Instance of logger, setup at __construct()
     */

    public $logger = false;

    /**
     * @var null
     */
    public $conversions_rules = null;

    /**
     * @var null
     */
    public $conversions_types = null;

    /*
     * Writer to `core_config_data` table
     */
    public $configWriter = null;

    /**
     * @var \ICEShop\ICECatConnect\Model\ICECatConnectDB|null
     */
    public $connectDB = null;

    /**
     * @var bool
     */
    public $storeId = false;

    /**
     * @var array
     */
    public $stores = [];

    /**
     * @var bool
     */
    public $catalog_product_entity_table = false;

    /**
     * @var bool
     */
    public $ftp_connection = false;

    /**
     * @var \Magento\Indexer\Model\IndexerFactory
     */
    protected $_indexerFactory;
    /**
     * @var \Magento\Indexer\Model\Indexer\CollectionFactory
     */
    protected $_indexerCollectionFactory;

    public function __construct(
        \Magento\Indexer\Model\IndexerFactory $indexerFactory,
        \Magento\Indexer\Model\Indexer\CollectionFactory $indexerCollectionFactory
    )
    {
        $this->connectDB = new ICECatConnectDB();
        $this->_indexerFactory = $indexerFactory;
        $this->_indexerCollectionFactory = $indexerCollectionFactory;
        // setup logger
        $writer = new \Zend\Log\Writer\Stream(BP . $this->loggerPath);
        $this->logger = new \Zend\Log\Logger();
        $this->logger->addWriter($writer);
        $this->catalog_product_entity_table = $this->connectDB->resource->getTableName('catalog_product_entity');
    }

    /**
     * Get version of Magento shop
     * @return string
     */

    public function getICEshopIcecatconnectorExtensionVersion()
    {
        $this->getConfigWriter();
        $this->configWriter->save('icecatconnect_content_last_start', time());
        $productMetadata = $this->connectDB->objectManager->get('Magento\Framework\App\ProductMetadataInterface');
        $version = $productMetadata->getVersion();
        return json_encode((string)$version);
    }

    /**
     * Create _configWriter for save to `core_config_data`
     * @return null
     */
    public function getConfigWriter()
    {
        if (!$this->configWriter) {
            $this->configWriter = $this->connectDB->objectManager->create(
                'Magento\Framework\App\Config\Storage\WriterInterface'
            );
        }
        return $this->configWriter;
    }

    /**
     * Get products from shop
     * @param mixed $data
     * @return string
     */
    public function getProductsBatch($data)
    {
        $this->_dataDecode($data);
        $storeId = 0;
        $result = [];
        $page = null;
        $page_size = null;

        if (!empty($data) && is_array($data)) {
            $page = (!empty($data['page'])) ? $data['page'] : null;
            $page_size = (!empty($data['page_size'])) ? $data['page_size'] : null;
        }
        $collection = $this->connectDB->objectManager->create('Magento\Catalog\Model\ResourceModel\Product\Collection')
            ->addStoreFilter($storeId)
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('updated_ice');

        $optionId = false;
        $eav = $this->connectDB->objectManager->get('\Magento\Eav\Model\Config');
        $attribute = $eav->getAttribute('catalog_product', 'active_ice')->getData();
        if (isset($attribute['attribute_id'])) {
            $options = $eav->getAttribute('catalog_product', 'active_ice')->getSource()->getAllOptions();
            foreach ($options as $option) {
                if ($option['label'] == 'Yes') {
                    $optionId = $option['value'];
                    break;
                }
            }
        }

        if ($optionId !== false) {
            $sql = "UPDATE {$this->connectDB->resource->getTableName('catalog_product_entity')} 
                    SET active_ice = " . $optionId . "  WHERE active_ice IS NULL; ";

            $this->connectDB->connection->query($sql);
            $collection->addFieldToFilter('active_ice', $optionId);
        }

        $scopeConfig = $this->connectDB->objectManager->get('\Magento\Framework\App\Config\ScopeConfigInterface');
        $mpn_attribute_code = $scopeConfig->getValue(
            'iceshop_icecatconnect/icecatconnect_products_mapping/products_mapping_mpn'
        );
        if (!empty($mpn_attribute_code)) {
            $collection->addAttributeToSelect($mpn_attribute_code);
        }
        $brand_name_attribute_code = $scopeConfig->getValue(
            'iceshop_icecatconnect/icecatconnect_products_mapping/products_mapping_brand'
        );
        if (!empty($brand_name_attribute_code)) {
            $collection->addAttributeToSelect($brand_name_attribute_code);
        }
        $ean_attribute_code = $scopeConfig->getValue(
            'iceshop_icecatconnect/icecatconnect_products_mapping/products_mapping_gtin'
        );
        if (!empty($ean_attribute_code)) {
            $collection->addAttributeToSelect($ean_attribute_code);
        }

        $brand_name_attribute_type = false;
        if ($page !== null && $page_size !== null) {
            $collection = $collection
                ->setPageSize($page_size)
                ->setCurPage($page);
        }
        $total_products = $collection->getSize();
        if ($total_products >= ($page_size * ($page - 1))) {
            foreach ($collection as $product) {
                $item = [
                    'product_id' => $product->getId(),
                    'updated' => $product->getData('updated_ice')
                ];

                if (!empty($mpn_attribute_code)) {
                    $item['mpn'] = $product->getData($mpn_attribute_code);
                }
                if (!empty($brand_name_attribute_code)) {
                    if ($brand_name_attribute_type == false) {
                        $brand_name_attribute_type = $product->getResource()
                            ->getAttribute($brand_name_attribute_code)
                            ->getFrontend()
                            ->getInputType();
                    }
                    switch ($brand_name_attribute_type) {
                        case 'select':
                        case 'dropdown':
                            $item['brand_name'] = $product->getAttributeText($brand_name_attribute_code);
                            break;
                        case 'text':
                            $item['brand_name'] = $product->getData($brand_name_attribute_code);
                            break;
                    }
                }
                if (!empty($ean_attribute_code)) {
                    $item['ean'] = $product->getData($ean_attribute_code);
                }
                $result['products'][] = $item;
            }
        }
        $defaultSetId = $this->connectDB->objectManager->create('Magento\Catalog\Model\Product')
            ->getDefaultAttributeSetid();
        $result['default_attribute_set'] = $defaultSetId;

        return json_encode($result);
    }

    /**
     * Get language mapping
     * @return string
     */
    public function getLanguageMapping()
    {
        $scopeConfig = $this->connectDB->objectManager->get('\Magento\Framework\App\Config\ScopeConfigInterface');
        $is_multilingual = $scopeConfig->getValue(
            'iceshop_icecatconnect/icecatconnect_language_mapping/multilingual_mode'
        );
        $attribute_update_required = $scopeConfig->getValue(
            'iceshop_icecatconnect/icecatconnect_service_settings/products_update_attributes'
        );

        $attribute_sort_order_update_required = 0;
        $attribute_labels_update_required = 0;
        if (!isset($attribute_update_required)) {
            $attribute_update_required = 1;
        }
        if ($attribute_update_required == 1) {
            //fetch label/sort_order settings
            $attribute_sort_order_update_required = $scopeConfig->getValue(
                'iceshop_icecatconnect/icecatconnect_service_settings/products_update_sort_order'
            );
            $attribute_labels_update_required = $scopeConfig->getValue(
                'iceshop_icecatconnect/icecatconnect_service_settings/update_attribute_labels'
            );
        }
        if ($is_multilingual == 1) {
            //multilingual mode
            $mapping = $scopeConfig->getValue(
                'iceshop_icecatconnect/icecatconnect_language_mapping/multilingual_values'
            );

            //fix store_id = 0
            $mapping = json_decode($mapping, true);
            if (isset($mapping[$this->_getStoreId()])) {
                $mapping[0] = $mapping[$this->_getStoreId()];
                $mapping[0]['store_id'] = 0;
                $mapping = json_encode($mapping);
            }
        } else {
            //one language mode
            $mapping = [
                (object)[
                    'store_id' => 0,
                    'value' => $scopeConfig->getValue(
                        'iceshop_icecatconnect/icecatconnect_language_mapping/main_language_single'
                    )
                ],
                (object)[
                    'store_id' => $this->_getStoreId(),
                    'value' => $scopeConfig->getValue(
                        'iceshop_icecatconnect/icecatconnect_language_mapping/main_language_single'
                    )
                ],
                (object)[
                    'store_id' => 9999,
                    'value' => $scopeConfig->getValue(
                        'iceshop_icecatconnect/icecatconnect_language_mapping/insurance_language_single'
                    )
                ]
            ];
            $mapping = json_encode($mapping);
        }
        $response = [
            'multi' => $is_multilingual,
            'mapping' => $mapping,
            'exact_language_import' => $scopeConfig->getValue(
                'iceshop_icecatconnect/icecatconnect_language_mapping/strict_language_import'
            ),
            'attribute_update_required' => $attribute_update_required,
            'attribute_sort_order_update_required' => $attribute_sort_order_update_required,
            'attribute_labels_update_required' => $attribute_labels_update_required
        ];

        // Import main mapping attributes if empty values
        $main_attributes_mapping = $this->getICEShopMapping();
        if (($main_attributes_mapping['mpn'] != '') && ($main_attributes_mapping['brand_name'] != '')
            && ($main_attributes_mapping['ean'] != '')
        ) {
            $import_main_attributes = [];
            $import_main_attributes['mapping'] = $main_attributes_mapping;
            $import_main_attributes['ean'] = $scopeConfig->getValue(
                'iceshop_icecatconnect/icecatconnect_products_mapping/import_gtin'
            );
            $import_main_attributes['brand_mpn'] = $scopeConfig->getValue(
                'iceshop_icecatconnect/icecatconnect_products_mapping/import_brand_mpn'
            );
            $response['import_main_attributes'] = $import_main_attributes;
        }
        if (!empty($main_attributes_mapping) && array_key_exists('description', $main_attributes_mapping)) {
            $response['description'] = $main_attributes_mapping['description'];
        }
        return json_encode($response);
    }

    public function getICEShopMapping()
    {
        $scopeConfig = $this->connectDB->objectManager->get('\Magento\Framework\App\Config\ScopeConfigInterface');
        $mapping = [];
        $mapping['mpn'] = $scopeConfig->getValue(
            'iceshop_icecatconnect/icecatconnect_products_mapping/products_mapping_mpn'
        );
        $mapping['brand_name'] = $scopeConfig->getValue(
            'iceshop_icecatconnect/icecatconnect_products_mapping/products_mapping_brand'
        );
        $mapping['ean'] = $scopeConfig->getValue(
            'iceshop_icecatconnect/icecatconnect_products_mapping/products_mapping_gtin'
        );
        if ($scopeConfig->getValue(
                'iceshop_icecatconnect/icecatconnect_products_mapping/default_description_attributes'
            ) == 0
        ) {
            $mapping['description']['name'] = $scopeConfig->getValue(
                'iceshop_icecatconnect/icecatconnect_products_mapping/name_attribute'
            );
            $mapping['description']['short_description'] = $scopeConfig->getValue(
                'iceshop_icecatconnect/icecatconnect_products_mapping/short_description_attribute'
            );
            $mapping['description']['long_description'] = $scopeConfig->getValue(
                'iceshop_icecatconnect/icecatconnect_products_mapping/long_description_attribute'
            );
            $mapping['description']['icecat_products_name'] = $scopeConfig->getValue(
                'iceshop_icecatconnect/icecatconnect_products_mapping/icecat_products_name_attribute'
            );
        }
        return $mapping;
    }

    /**
     * Get attribute sets from Magento shop
     * @param array $data
     * @return string
     */
    public function catalogProductAttributeSetList($data)
    {
        $this->_dataDecode($data);
        $entityTypeId = $this->connectDB->objectManager->get('\Magento\Catalog\Model\Product')->getResource()
            ->getEntityType()->getId();

        // limits for attribute sets
        $limited = false;
        if (!empty($data)) {
            if (array_key_exists('start', $data)) {
                $start = (int)$data['start'];
                if ($start < 0) {
                    $start = 0;
                }
                $limited = true;
            }
            if (array_key_exists('limit', $data)) {
                $limit = (int)$data['limit'];
                if ($limit <= 0) {
                    $limit = 50;
                }
                $limited = true;
            }
        }
        // Get all attribute sets from `eav_attribute_set` table
        $eavSet = "SELECT `attribute_set_id`, `attribute_set_name` FROM `{prefix}eav_attribute_set` 
        WHERE `entity_type_id` = :entityTypeId ";

        if ($limited == true) {
            $eavSet .= " LIMIT {$start}, {$limit}";
        }
        $fetchAttributeSets = $this->connectDB->executeStatements($eavSet, [':entityTypeId' => $entityTypeId]);


        $result = [];
        if (!empty($fetchAttributeSets)) {
            foreach ($fetchAttributeSets as $attributeSet) {
                // Get all attribute groups for needed attribute set, because we also then map and import they
                $groupSet = "SELECT `attribute_group_id`, `attribute_group_name` FROM `{prefix}eav_attribute_group` 
                WHERE `attribute_set_id` = :attributeSetId ";

                $fetchGroups = $this->connectDB->executeStatements($groupSet,
                    [
                        ':attributeSetId' => $attributeSet['attribute_set_id']
                    ]
                );

                $groups = [];
                foreach ($fetchGroups as $group) {
                    $groups[] = [
                        'id' => $group['attribute_group_id'],
                        'name' => $group['attribute_group_name'],
                        'external_id' => $this->connectDB->getConversionRule(
                            $group['attribute_group_id'],
                            'attribute_group'
                        )
                    ];
                }

                // Get all attributes that belong needed attribute set and has
                // needed entity_type (by default `catalog_product`)
                $eavAttributes = "SELECT eav_a.`attribute_id`, eav_a.`attribute_code` FROM 
                `{prefix}eav_entity_attribute`eav_ea  LEFT JOIN `{prefix}eav_attribute` eav_a 
                ON eav_ea.`attribute_id` = eav_a.`attribute_id`
                WHERE eav_ea.`entity_type_id` = :entityTypeId 
                AND eav_ea.attribute_set_id = :attributeSetId ; ";

                $fetchAttributes = $this->connectDB->executeStatements($eavAttributes, [
                    ':entityTypeId' => $entityTypeId,
                    ':attributeSetId' => $attributeSet['attribute_set_id']
                ]);

                $attributes = [];
                foreach ($fetchAttributes as $attribute) {
                    if ($conversion = $this->connectDB->getConversionRule($attribute['attribute_id'], 'attribute')) {
                        $attributes_arr[] = [
                            'id' => $attribute['attribute_id'],
                            'name' => $attribute['attribute_code'],
                            'external_id' => $conversion
                        ];
                    }
                }

                $result[] = [
                    'set_id' => $attributeSet['attribute_set_id'],
                    'name' => $attributeSet['attribute_set_name'],
                    'external_id' => $this->connectDB->getConversionRule(
                        $attributeSet['attribute_set_id'],
                        'attribute_set'
                    ),
                    'groups' => $groups,
                    'attributes' => $attributes
                ];
            }
        }
        return json_encode($result);
    }

    /**
     * Get store id
     * @return mixed
     */
    public function _getStoreId()
    {
        if (!$this->storeId) {
            $storeManager = $this->connectDB->objectManager->get('\Magento\Store\Model\StoreManagerInterface');
            $this->storeId = $storeManager->getStore()->getId();
        }

        return $this->storeId;
    }

    /**
     * @param $data
     * @return string
     */
    public function getProductAttributeList($data)
    {
        $this->_dataDecode($data);
        $response = [];
        if (!empty($data) && is_array($data) && !empty($data['items'])) {
            $attribute_api_model = $this->connectDB->objectManager->create(
                'ICEShop\ICECatConnect\Model\ICECatConnectCatalogProductAttributeApi'
            );

            $attribute_api_model->connectDB = $this->connectDB;
            $attribute_api_model->product_entity_type_id = $this->connectDB->objectManager->create(
                '\Magento\Catalog\Model\Product'
            )->getResource()->getEntityType()->getId();
            $attribute_api_model->eav_attribute = $this->connectDB->objectManager->create(
                'Magento\Catalog\Model\ResourceModel\Eav\Attribute'
            );
            $attribute_api_model->eav_model_config = $this->connectDB->objectManager->get(
                '\Magento\Eav\Model\Config'
            );
            $attribute_api_model->eav_entity_attribute_table = $this->connectDB->resource->getTableName(
                'eav_entity_attribute'
            );
            $attribute_api_model->eav_attribute_table = $this->connectDB->resource->getTableName('eav_attribute');
            $attribute_api_model->catalog_eav_attribute_table = $this->connectDB->resource->getTableName(
                'catalog_eav_attribute'
            );

            foreach ($data['items'] as $attribute_set_id) {
                try {
                    $attribute_list = $attribute_api_model->getAttributes($attribute_set_id);
                    $response[$attribute_set_id] = $attribute_list;
                } catch (\Exception $e) {
                    $response['API_ERROR'][$attribute_set_id] = [
                        'comment' => $e->getMessage(),
                        'status' => 'Error while fetching attribute list'
                    ];
                }
            }
        }
        return json_encode($response);
    }

    /**
     * Save sets, attributes, groups for products
     * with data that get from server
     *
     * @param mixed $data
     * @return bool|string
     */
    public function saveAttributeSetBatch($data)
    {
        $response = [];

        $this->_dataDecode($data);

        $entityProductTypeId = $this->connectDB->objectManager->create('Magento\Eav\Model\Entity\Type')
            ->loadByCode(
                'catalog_product'
            )->getId();

        // initialize additional class and set needed instances
        $attribute_api_model = $this->connectDB->objectManager->create(
            'ICEShop\ICECatConnect\Model\ICECatConnectCatalogProductAttributeApi'
        );
        $attribute_api_model->connectDB = $this->connectDB;
        $attribute_api_model->logger = $this->logger;


        $group_id = false;

        if (!empty($data) && is_array($data)) {
            if (!empty($data['set'])) {
                if (empty($data['set']['magento_id'])) {
                    //create new attribute set
                    try {
                        $checkAttributeSetId = $this->connectDB->objectManager->get(
                            'Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\Collection'
                        )->setEntityTypeFilter($entityProductTypeId)
                            ->addFieldToFilter('attribute_set_name',  $data['set'][0])
                            ->getFirstItem()
                            ->getAttributeSetId();


                        if ($checkAttributeSetId && !empty($data['set'][2]['external_id'])) {
                            $this->connectDB->saveConversions($data['set'][2]['external_id'], $checkAttributeSetId, 'attribute_set');
                            $response['set'] = [
                                'external_id' => $data['set'][2]['external_id'],
                                'comment' => 'Attribute Set created successfully',
                                'magento_id' => $checkAttributeSetId
                            ];

                        } else {

                            $attributeSet = $this->connectDB->objectManager->create(
                                'Magento\Eav\Model\Entity\Attribute\Set'
                            );
                            $entityTypeId = $entityProductTypeId;
                            $attributeSet->setData([
                                'attribute_set_name' => $data['set'][0],
                                'entity_type_id' => $entityTypeId,
                                'sort_order' => 200,
                            ]);

                            if ($attributeSet->validate()) {
                                $attributeSet->save();
                                $attributeSet->initFromSkeleton($data['set'][1]);
                                $attributeSet->save();
                                $set_id = $attributeSet->getId();
                                if (!empty($data['set'][2]['external_id'])) {
                                    $this->connectDB->saveConversions(
                                        $data['set'][2]['external_id'],
                                        $set_id,
                                        'attribute_set'
                                    );
                                }
                                $response['set'] = [
                                    'external_id' => $data['set'][2]['external_id'],
                                    'comment' => 'Attribute Set created successfully',
                                    'magento_id' => $set_id
                                ];
                            } else {
                                $response['API_ERROR']['set'] = [
                                    'external_id' => $data['set'][2]['external_id'],
                                    'comment' => 'Validation error in attribute set',
                                    'magento_id' => null
                                ];
                            }
                        }
                    } catch (\Exception $e) {
                        $response['API_ERROR']['set'] = [
                            'external_id' => $data['set'][2]['external_id'],
                            'comment' => $e->getMessage(),
                            'magento_id' => null
                        ];
                    } catch (\Magento\Framework\Exception\LocalizedException $e) {
                        $response['API_ERROR']['set'] = [
                            'external_id' => $data['set'][2]['external_id'],
                            'comment' => 'Error with validation attribute set (Message: `' . $e->getMessage() . '`)',
                            'magento_id' => null
                        ];
                    }
                } else {
                    //fetch only attribute set ID
                    $set_id = $data['set']['magento_id'];
                    $response['set'] = [
                        'external_id' => $data['set']['external_id'],
                        'comment' => 'Attribute set exists at store',
                        'magento_id' => $set_id
                    ];
                }

                if (isset($set_id) && !empty($data['set']['groups'])) {

                    $groupCollection = $this->connectDB->objectManager->get(
                        '\Magento\Eav\Model\ResourceModel\Entity\Attribute\Group\Collection'
                    )
                        ->setAttributeSetFilter($set_id)
                        ->load();

                    foreach ($data['set']['groups'] as $group) {
                        if (empty($group['magento_id'])) {
                            //create new attribute group
                            if (!empty($group['groupName']) && !empty($group['data'])) {
                                try {
                                    $group_id = $this->groupAdd(
                                        $set_id,
                                        $group['groupName'],
                                        $group['data'],
                                        $groupCollection
                                    );

                                    $response['set']['groups'][$group_id] = [
                                        'external_id' => $group['data']['external_id'],
                                        'comment' => 'Attribute group `' . $group['groupName'] .
                                            '` created successfully',
                                        'magento_id' => $group_id
                                    ];
                                } catch (\Exception $e) {
                                    $response['API_ERROR']['set']['groups'][] = [
                                        'external_id' => $group['data']['external_id'],
                                        'comment' => $e->getMessage(),
                                        'magento_id' => null
                                    ];
                                } catch (\Magento\Framework\Exception\LocalizedException $e) {
                                    $response['API_ERROR']['set']['groups'][] = [
                                        'external_id' => $group['data']['external_id'],
                                        'comment' => $e->getMessage(),
                                        'magento_id' => null
                                    ];
                                }
                            }
                        } else {
                            //group already exists
                            $group_id = $group['magento_id'];
                            $response['set']['groups'][$group_id] = [
                                'external_id' => $group['external_id'],
                                'comment' => 'Attribute group already exists',
                                'magento_id' => $group_id
                            ];
                        }

                        if ($group_id && !empty($group['attributes']) && is_array($group['attributes'])) {
                            $response['set']['groups'][$group_id]['attributes'] = [];
                            //attributes loop (create and link to the attribute set)
                            foreach ($group['attributes'] as $attribute) {
                                $attribute_id = null;
                                // create new attribute
                                if (empty($attribute['magento_id'])) {
                                    try {
                                        $attribute_id = $attribute_api_model->create($attribute);
                                        $response['set']['groups'][$group_id]['attributes']
                                        [$attribute['external_id']] = [
                                            'external_id' => $attribute['external_id'],
                                            'comment' => 'Attribute created successfully',
                                            'magento_id' => $attribute_id,
                                            'attribute_code' => $attribute['attribute_code'],
                                            'type' => $attribute['frontend_input']
                                        ];
                                    } catch (\Exception $e) {
                                        $response['API_ERROR']['set']['groups'][$group_id]['attributes']
                                        [$attribute['external_id']] = [
                                            'external_id' => $attribute['external_id'],
                                            'comment' => 'Error with attribute: `' . $e->getMessage() . '`',
                                            'magento_id' => null
                                        ];
                                    } catch (\Magento\Framework\Exception\LocalizedException $e) {
                                        $response['API_ERROR']['set']['groups'][$group_id]['attributes']
                                        [$attribute['external_id']] = [
                                            'external_id' => $attribute['external_id'],
                                            'comment' => 'Error with attribute: `' . $e->getMessage() . '`',
                                            'magento_id' => null
                                        ];
                                    }
                                } else {
                                    //attribute already exists
                                    $attribute_id = $attribute['magento_id'];
                                    $response['set']['groups'][$group_id]['attributes'][$attribute['external_id']] = [
                                        'external_id' => $attribute['external_id'],
                                        'comment' => 'Attribute already exists',
                                        'magento_id' => $attribute_id,
                                        'attribute_code' => $attribute['attribute_code'],
                                        'type' => $attribute['field_type']
                                    ];
                                }
                                //link attribute to attribute set
                                if (!empty($attribute_id)) {
                                    try {
                                        if (array_key_exists('sort_order', $attribute)) {
                                            $attribute_api_model->attributeAdd(
                                                $attribute_id,
                                                $set_id,
                                                $group_id,
                                                $attribute['sort_order']
                                            );
                                        } else {
                                            $attribute_api_model->attributeAdd($attribute_id, $set_id, $group_id);
                                        }
                                    } catch (\Exception $e) {
                                        $reponse['API_ERROR']['set']['groups'][$group_id]['attributes'][$attribute_id]
                                        ['set_attribute_add'] = [
                                            'comment' => $e->getMessage()
                                        ];
                                    } catch (\Magento\Framework\Exception\LocalizedException $e) {
                                        $reponse['API_ERROR']['set']['groups'][$group_id]['attributes'][$attribute_id]
                                        ['set_attribute_add'] = [
                                            'comment' => $e->getMessage()
                                        ];
                                    }
                                }

                                //save all options
                                if (!empty($attribute_id) && !empty($attribute['options'])) {
                                    foreach ($attribute['options'] as $option) {
                                        try {
                                            if (empty($option['magento_id'])) {
                                                $option_id = $attribute_api_model->addOption($attribute_id, $option);

                                                $response['set']['groups'][$group_id]['attributes']
                                                [$attribute['external_id']]['options'][] = [
                                                    'comment' => 'Option added successfully',
                                                    'item' => [
                                                        'value' => $option_id,
                                                        'external_id' => $option['external_id']
                                                    ]
                                                ];
                                            } else {
                                                $option_id = $option['magento_id'];
                                                $attribute_api_model->updateOption($attribute_id, $option_id, $option);

                                                $response['set']['groups'][$group_id]['attributes']
                                                [$attribute['external_id']]['options'][] = [
                                                    'comment' => 'Option updated successfully',
                                                    'item' => [
                                                        'value' => $option_id,
                                                        'external_id' => $option['external_id']
                                                    ]
                                                ];
                                            }
                                        } catch (\Exception $e) {
                                            $response['API_ERROR']['set']['groups'][$group_id]['attributes']
                                            [$attribute['external_id']]['options'][] = [
                                                'comment' => $e->getMessage(),
                                                'item' => $option
                                            ];
                                        } catch (\Magento\Framework\Exception\LocalizedException $e) {
                                            $response['API_ERROR']['set']['groups'][$group_id]['attributes']
                                            [$attribute['external_id']]['options'][] = [
                                                'comment' => $e->getMessage(),
                                                'item' => $option
                                            ];
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
            return json_encode($response);
        }
        return false;
    }

    /**
     * Add attribute group to attribute set
     *
     * @param $attributeSetId
     * @param $groupName
     * @param array $data
     * @return int
     * @throws \Exception
     */
    public function groupAdd($attributeSetId, $groupName, $data = [], $groupCollection = false)
    {
        $groupId = false;
        $groupsInSet = [];

        if (!$groupCollection) {
            $groupCollection = $this->connectDB->objectManager->get(
                '\Magento\Eav\Model\ResourceModel\Entity\Attribute\Group\Collection'
            )
                ->setAttributeSetFilter($attributeSetId)
                ->load();
        }

        foreach ($groupCollection as $group) {
            $groupsInSet[$group->getId()] = $group->getAttributeGroupName();
        }
        // if attribute group with same name already exists in store
        // then we add conversion for it and return id of this group
        // else we save attribute and return group id
        if ($group_id = array_search($groupName, $groupsInSet)) {
            if (!empty($data['external_id'])) {
                $this->connectDB->saveConversions($data['external_id'], $group_id, 'attribute_group');
            }
            $groupId = (int)$group_id;
        } else {
            $group = $this->connectDB->objectManager->create('Magento\Eav\Model\Entity\Attribute\Group');
            $group->setAttributeSetId($attributeSetId)
                ->setAttributeGroupName(
                    $groupName
                );

            if (!empty($data['sort_order'])) {
                $group->setSortOrder((int)$data['sort_order']);
            }

            if ($group->itemExists()) {
                $this->logger->warn('Group with name `' . $groupName . '` already exists ');
            } else {
                try {
                    $group->save();
                    if (!empty($data['external_id'])) {
                        $this->connectDB->saveConversions($data['external_id'], $group->getId(), 'attribute_group');
                    }
                    $groupId = (int)$group->getId();
                } catch (\Exception $e) {
                    $this->logger->warn('Error while save group name, message: `' . $e->getMessage() . '`');
                }
            }
        }
        return $groupId;
    }

    public function saveProductsBatch($data)
    {
        $this->_dataDecode($data);
        $response = [];

        if (!empty($data) && is_array($data)) {
            $products_api_model = $this->connectDB->objectManager->create(
                'ICEShop\ICECatConnect\Model\ICECatConnectCatalogProductAttributeApi'
            );
            $products_api_model->connectDB = $this->connectDB;
            $product_entity_type_id = $products_api_model->product_entity_type_id =
                $this->connectDB->objectManager->create(
                    '\Magento\Catalog\Model\Product'
                )->getResource()->getEntityType()->getId();
            $products_api_model->eav_attribute = $this->connectDB->objectManager->create(
                'Magento\Catalog\Model\ResourceModel\Eav\Attribute'
            );
            $products_api_model->eav_model_config = $this->connectDB->objectManager->get(
                '\Magento\Eav\Model\Config'
            );
            $products_api_model->eav_entity_attribute_table = $this->connectDB->resource->getTableName(
                'eav_entity_attribute'
            );
            $products_api_model->catalog_product_website_table = $this->connectDB->resource->getTableName(
                'catalog_product_website'
            );
            $products_api_model->eav_attribute_table = $this->connectDB->resource->getTableName('eav_attribute');
            $products_api_model->catalog_eav_attribute_table = $this->connectDB->resource->getTableName(
                'catalog_eav_attribute'
            );
            $products_api_model->catalog_product_entity_table = $this->connectDB->resource->getTableName(
                'catalog_product_entity'
            );
            $products_api_model->logger = $this->logger;
            $products_api_model->modelProductApi = $this->connectDB->objectManager
                ->create('\Magento\Catalog\Model\Product');
            $products_api_model->storeManagerInterface = $this->connectDB->objectManager
                ->create('\Magento\Store\Model\StoreManagerInterface');

            $products_api_model->action = $this->connectDB->objectManager->create(
                '\Magento\Catalog\Model\Product\Action'
            );

            $select_attributes = $this->connectDB->connection->query("
                    SELECT ea.attribute_code 
                    FROM $products_api_model->eav_attribute_table ea 
                    RIGHT JOIN $products_api_model->eav_entity_attribute_table eea ON ea.attribute_id = eea.attribute_id 
                    WHERE ea.entity_type_id = $product_entity_type_id 
                    AND ea.source_model IS NULL GROUP BY ea.attribute_id;
                ");

            $products_api_model->attributes = $select_attributes->fetchAll(\PDO::FETCH_COLUMN, 'attribute_code');

            $catalog_product_entity_int_table = $this->connectDB->resource->getTableName(
                'catalog_product_entity_int'
            );

            $select_visibility = $this->connectDB->connection->query("
                SELECT cpi.entity_id, cpi.value FROM {$catalog_product_entity_int_table} cpi 
                JOIN {$products_api_model->eav_attribute_table} ea ON cpi.attribute_id = ea.attribute_id
                WHERE ea.attribute_code = 'visibility' AND cpi.store_id = 0;
            ");

            $products_api_model->visibilities = $select_visibility->fetchAll(\PDO::FETCH_KEY_PAIR);

            foreach ($data as $product) {

                try {
                    if (array_key_exists('product', $product) && !empty($product['product'])) {

                        foreach ($product['product'] as $store_id => $product_data) {

                            if ($product_data && !empty($product_data)) {
                                $products_api_model->update(
                                    $product_data['product_id'],
                                    $product_data['attribute_data'],
                                    $store_id
                                );

                                $response[$product['product_id']][] = [
                                    'comment' => 'Product updated successfully',
                                    'product_id' => $product_data['product_id'],
                                    'store_id' => $store_id
                                ];
                            }
                        }

                        try {
                            $this->connectDB->connection->query("UPDATE `$this->catalog_product_entity_table` 
                                SET `updated_ice` = NOW() WHERE `entity_id` = " . $product['product_id']);
                        } catch (\Exception $e) {
                            $this->logger->err('Error during update products `updated_ice` field: ' . $e->getMessage());
                        }
                    }

                    if (isset($product['links']) && is_array($product['links'])) {
                        if (isset($product['links']['related']) && is_array($product['links']['related'])) {
                            try {
                                $result = $this->_saveProductsLinks(
                                    $product['product_id'],
                                    $product['links']['related'],
                                    'cross_sell'
                                );
                                $response[$product['product_id']]['cross_sell'] = $result;
                            } catch (\Exception $e) {
                                $response['API_ERROR'][$product['product_id']]['cross_sell'] = [
                                    'comment' => $e->getMessage(),
                                    'status' => 'Error while saving "cross sell" links',
                                    'product_id' => $product['product_id']
                                ];
                            }
                        }

                        if (isset($product['links']['preferred']) && is_array($product['links']['preferred'])) {
                            try {
                                $result = $this->_saveProductsLinks(
                                    $product['product_id'],
                                    $product['links']['preferred'],
                                    'related'
                                );
                                $response[$product['product_id']]['related'] = $result;
                            } catch (\Exception $e) {
                                $response['API_ERROR'][$product['product_id']][] = [
                                    'comment' => $e->getMessage(),
                                    'status' => 'Error while saving "related" links',
                                    'product_id' => $product['product_id']
                                ];
                            }
                        }

                        if (isset($product['links']['alternatives']) && is_array($product['links']['alternatives'])) {
                            try {
                                $result = $this->_saveProductsLinks(
                                    $product['product_id'],
                                    $product['links']['alternatives'],
                                    'up_sell'
                                );
                                $response[$product['product_id']]['up_sell'] = $result;
                            } catch (\Exception $e) {
                                $response['API_ERROR'][$product['product_id']][] = [
                                    'comment' => $e->getMessage(),
                                    'status' => 'Error while saving "up sell" links',
                                ];
                            }
                        }
                    }
                } catch (\Exception $e) {
                    $response['API_ERROR'][$product['product_id']] = [
                        'comment' => $e->getMessage(),
                        'exception' => [
                            'class' => get_class($e),
                            'file' => $e->getFile(),
                            'line' => $e->getLine()
                        ],
                        'status' => 'Error while updating the product',
                        'item' => $product['product_id'],
                    ];
                }
            }
        }

        return json_encode($response);
    }

    /**
     * Working with products links - deleting and adding if needed
     */
    public function _saveProductsLinks($product_id, $relation_arr, $relation_type)
    {
        $response = [];
        $relation_types = [
            'related',
            'up_sell',
            'cross_sell'
        ];

        if (!empty($product_id) && is_array($relation_arr) && in_array($relation_type, $relation_types)) {
            $product_link_api_model = $this->connectDB->objectManager->create(
                'ICEShop\ICECatConnect\Model\ICECatConnectCatalogProductLink'
            );
            try {
                $links_raw = $product_link_api_model->items($relation_type, $product_id, null, true);
                $links = [];
                foreach ($links_raw as $link_id => $link_item) {
                    $links[] = $link_item[0];
                }
                $links_to_add = array_diff($relation_arr, $links);
                $links_to_delete = array_diff($links, $relation_arr);

                if (!empty($links_to_delete)) {
                    $link = implode(',', $links_to_delete);
                    try {
                        $product_link_api_model->remove($relation_type, $product_id, $links_to_delete);
                        $response[] = [
                            'comment' => 'Link deleted successfully',
                            'item' => [
                                'product_id' => $product_id,
                                'related_product_id' => $link
                            ]
                        ];
                    } catch (\Exception $e) {
                        $response['API_ERROR'][] = [
                            'comment' => $e->getMessage(),
                            'status' => 'Error while deleting link item',
                            'item' => [
                                'product_id' => $product_id,
                                'related_product_id' => $link
                            ]
                        ];
                    }
                }

                if (!empty($links_to_add)) {
                    $link_data = ['is_icecat' => '1'];
                    $link = implode(',', $links_to_add);
                    try {
                        $product_link_api_model->assign($relation_type, $product_id, $links_to_add, $link_data);
                        $response[] = [
                            'comment' => 'Link added successfully',
                            'item' => [
                                'product_id' => $product_id,
                                'related_product_id' => $link
                            ]
                        ];
                    } catch (\Exception $e) {
                        $response['API_ERROR'][] = [
                            'comment' => $e->getMessage(),
                            'status' => 'Error while adding link item',
                            'item' => [
                                'product_id' => $product_id,
                                'related_product_id' => $link
                            ]
                        ];
                    }
                }
            } catch (\Exception $e) {
                $response['API_ERROR'][] = [
                    'comment' => $e->getMessage(),
                    'status' => 'Error while fetching links',
                    'item' => $product_id
                ];
            }
        }
        return $response;
    }

    public function queueProductsImages($data)
    {
        $this->_dataDecode($data);

        $response = [];
        if (!empty($data) && is_array($data)) {
            //reset all updated products images
            $updated_products = array_keys($data);
            $fields = [];
            $fields['deleted'] = 1;
            $fields['is_default'] = 0;
            $where = $this->connectDB->connection->quoteInto('product_id IN (?)', $updated_products);
            try {
                $this->connectDB->connection->update('icecat_products_images', $fields, $where);
            } catch (\Exception $e) {
                $response['API_ERROR'] = [
                    'comment' => $e->getMessage(),
                    'status' => 'Error while setting images as deleted'
                ];
                return $response;
            }

            //update images
            foreach ($data as $product) {
                if (empty($product['images'])) {
                    continue;
                }

                try {
                    foreach ($product['images'] as $image) {
                        $table_name = $this->connectDB->connection->quoteIdentifier('icecat_products_images');
                        if (empty($image['default'])) {
                            $image['default'] = '0';
                        } else {
                            $image['default'] = '1';
                        }
                        $fields = [
                            "product_id" => $product['product_id'],
                            "external_url" => $image['external_url'],
                            "is_default" => $image['default'],
                            "deleted" => 0
                        ];

                        $sql = "INSERT INTO {$table_name} (product_id, external_url, is_default) 
                                VALUES (
                                    '" . $fields['product_id'] . "', 
                                    '" . $fields['external_url'] . "', 
                                    '" . $fields['is_default'] . "'
                                ) 
                                ON DUPLICATE KEY UPDATE 
                                deleted = '" . $fields['deleted'] . "', 
                                is_default = '" . $fields['is_default'] . "'";

                        $this->connectDB->connection->query($sql);

                        $response[] = [
                            'comment' => 'Added successfully',
                            'item' => $image,
                            'product_id' => $product['product_id']
                        ];
                    }
                } catch (\Exception $e) {
                    $response['API_ERROR'][] = [
                        'comment' => $e->getMessage(),
                        'item' => $image,
                        'product_id' => $product['product_id']
                    ];
                }
            }
        }
        return json_encode($response);
    }

    /**
     * @param $data
     * @return array|bool
     */
    public function updateLanguageCodes($data)
    {
        $this->_dataDecode($data);
        $table_name = $this->connectDB->resource->getTableName('core_config_data');
        if (!empty($data) && !empty($data['language_codes'])) {
            array_unshift(
                $data['language_codes'],
                ['value' => '-1', 'label' => '--' . __('Choose the language') . '--']
            );
            $value = serialize($data['language_codes']);
            try {
                $config_id = $this->connectDB->fetchSingleValue(
                    "SELECT `config_id` FROM `" . $table_name . "` WHERE path = :path",
                    [
                        ':path' => 'iceshop_default_icecat_languages'
                    ],
                    'config_id'
                );
                if (isset($config_id)) {
                    $sql = "UPDATE `" . $table_name . "` SET value = '" . $value . "' WHERE config_id = '"
                        . $config_id . "';";
                } else {
                    $sql = "INSERT INTO `" . $table_name . "` (scope, scope_id, path, value) 
                            VALUES ('default', '0', 'iceshop_default_icecat_languages', '" . $value . "');";
                }
                $this->connectDB->connection->query($sql);
                return json_encode([
                    'comment' => 'Language codes updated',
                    'data' => json_encode($data)
                ]);
            } catch (\Exception $e) {
                return json_encode([
                    'comment' => $e->getMessage(),
                    'status' => 'Error while saving language codes'
                ]);
            }
        }
        return false;
    }

    /**
     * @param $path
     * @param $data
     * @return bool
     * @throws Exception
     */
    public function _saveConfig($path, $data)
    {
        if (!empty($path) && !empty($data)) {
            $store_config = $this->connectDB->objectManager->create('\Magento\Config\Model\ResourceModel\Config');
            $store_config->saveConfig($path, $data, 'default', 0);
        } else {
            return false;
        }
    }

    /**
     * @param $data
     * @return array
     */
    public function setIndexerMode($data)
    {

        $this->_dataDecode($data);
        $scopeConfig = $this->connectDB->objectManager->get('\Magento\Framework\App\Config\ScopeConfigInterface');
        $changeIndexerMode = $scopeConfig->getValue(
            'iceshop_icecatconnect/icecatconnect_service_settings/change_indexer_mode'
        );
        $selectedIndexerMode = $scopeConfig->getValue(
            'iceshop_icecatconnect/icecatconnect_service_settings/selected_indexer_mode'
        );

        if ($changeIndexerMode == '1') {
            //save previous state before any change
            $indexingProcesses = $this->connectDB->objectManager->create('Magento\Indexer\Model\Indexer\Collection');
            if (!empty($indexingProcesses)) {
                $current_state = [];
                foreach ($indexingProcesses as $process) {
                    $current_state[$process->getId()] = $process->isScheduled();
                }
            }
            if ($selectedIndexerMode == $this::INDEXER_MODE_SCHEDULED) {
                foreach ($indexingProcesses as $process) {
                    $process->setScheduled(true);
                }
            }

            if ($selectedIndexerMode == $this::INDEXER_MODE_UPDATE_ON_SAVE) {
                foreach ($indexingProcesses as $process) {
                    $process->setScheduled(false);
                }
            }
            if (!empty($current_state)) {
                return json_encode(['modes' => $current_state]);
            }
        }
    }

    /**
     * Run full reindex of shops indexing processes
     *
     * @return string
     */
    public function runFullReindex()
    {
        $indexerCollection = $this->_indexerCollectionFactory->create();
        $ids = $indexerCollection->getAllIds();
        try {
            foreach ($ids as $id) {
                $idx = $this->_indexerFactory->create()->load($id);
                $idx->reindexAll($id);
            }
        } catch (\Exception $e) {
            return $e->getMessage();
        }
        return 'Reindex running';
    }

    public function lastInsertId($tableName = null, $primaryKey = null)
    {
        return $this->connectDB->connection->lastInsertId();
    }


    /**
     * Start uploading imaages that was added to queue
     *
     * @param mixed $data
     * @return array|string
     */

    public static function checkVersion()
    {
        $productMetadata = ObjectManager::getInstance()->get('Magento\Framework\App\ProductMetadataInterface');
        $version = $productMetadata->getVersion();
        $checkVersion = '';
        if ((string)substr($version, 0, 3) == '2.0') {
            $checkVersion = '2.0';
        }
        return $checkVersion;
    }

    /**
     * Delete products images
     * @param $productId
     * @param $images
     * @param $resources
     */
    protected function cleanMagentoGallery($productId, $images, $resources)
    {
        if ((!empty($productId)) && (!empty($images)) &&
            (!empty($resources['mediaGalleryId'])) && (!empty($this->connectDB->connection))
        ) {

            $imageValueQuery = "SELECT DISTINCT `value_id` 
            FROM `{$this->connectDB->resource->getTableName('catalog_product_entity_media_gallery_value')}` 
            WHERE `entity_id` = '{$productId}'";

            $imageValueIds = $this->connectDB->connection->fetchAll($imageValueQuery);
            $mappedImageValueIds = array_map(function ($element) {
                return $element['value_id'];
            }, $imageValueIds);
            $glueImageValueIds = implode(",", $mappedImageValueIds);

            $glueImageValues = implode(",", array_map(function ($item) {
                return "'" . $item . "'";
            }, $images));

            $swatchQuery = $this->connectDB->connection->query("SELECT `value_id`, `value` 
                FROM `{$this->connectDB->resource->getTableName('catalog_product_entity_varchar')}` 
                WHERE `attribute_id` = '{$resources['swatch_image_attribute_id']}' 
                AND `entity_id` = '{$productId}'
                ;");

            $swatchFetch = $swatchQuery->fetchAll(\PDO::FETCH_KEY_PAIR);
            $gluedSwatch = implode(",", array_map(function ($item) {
                return "'" . $item . "'";
            }, $swatchFetch));

            if (!empty($swatchFetch)) {
                $findSwatchAtQueueQuery = $this->connectDB->connection->query("
                    SELECT `entity_id`, `internal_url`
                    FROM `icecat_products_images`
                    WHERE internal_url IN ({$gluedSwatch})
                    AND product_id = '{$productId}'
                    ;");
                $fetchExistingImages = $findSwatchAtQueueQuery->fetchAll(\PDO::FETCH_KEY_PAIR);

                $diffOfSwatch = array_diff($swatchFetch, $fetchExistingImages);

                $glueImg = implode(",", array_map(function ($item) {
                    return "'" . $item . "'";
                }, $diffOfSwatch));

                if(!empty($diffOfSwatch)){
                    $this->connectDB->connection->query("DELETE FROM 
                            {$this->connectDB->resource->getTableName('catalog_product_entity_varchar')}
                            WHERE `attribute_id` = '{$resources['swatch_image_attribute_id']}'
                            AND `entity_id` = '{$productId}'
                            AND `value` IN ($glueImg)
                        ;");

                    $this->connectDB->connection->query("DELETE FROM 
                        {$this->connectDB->resource->getTableName('catalog_product_entity_media_gallery')}
                        WHERE `attribute_id` = '{$resources['mediaGalleryId']}' 
                        AND `value` IN ({$glueImg}) 
                      ;");
                }
            }

            if ((!empty($glueImageValues)) && (!empty($glueImageValueIds))) {
                $deleteImageQuery = " DELETE 
            FROM `{$this->connectDB->resource->getTableName('catalog_product_entity_media_gallery')}` 
            WHERE `attribute_id` = '{$resources['mediaGalleryId']}' 
            AND `value` IN ({$glueImageValues}) 
            AND `value_id` IN ({$glueImageValueIds});";

                $this->connectDB->connection->query($deleteImageQuery);

                foreach ($images as $image) {
                    $pathToImage = $resources['media_folder'] .
                        DIRECTORY_SEPARATOR . 'catalog' .
                        DIRECTORY_SEPARATOR . 'product' .
                        $image;

                    if (file_exists($pathToImage)) {
                        unlink($pathToImage);
                    }
                }
            }
        }
    }

    protected function addDefaultImageToAllStores($product_id, $image)
    {
        if (!empty($this->stores)) {
            if (!empty($this->img_attrs)) {
                foreach ($this->stores as $storeId) {
                    foreach ($this->img_attrs as $key => $img_attr) {
                        $this->connectDB->connection->query("
                            INSERT INTO {$this->connectDB->resource->getTableName('catalog_product_entity_varchar')} (
                                `attribute_id`, 
                                `store_id`, 
                                `entity_id`, 
                                `value`
                            ) VALUES (
                              '$key', 
                              '$storeId', 
                              '$product_id', 
                              '$image'
                            )
                            ON DUPLICATE KEY UPDATE `value` = '$image';"
                        );
                    }
                }
            }
        }
    }

    public function processProductsImagesQueue($data, $broken = 0)
    {

        ini_set('memory_limit', -1);
        $response = [];
        $select_stores = $this->connectDB->connection->query("
            SELECT store_id FROM {$this->connectDB->resource->getTableName('store')};"
        );
        $this->stores = $select_stores->fetchAll(\PDO::FETCH_COLUMN, 'store_id');

        $this->_dataDecode($data);

        $batch_size = !empty($data['batch_size']) ? ceil($data['batch_size']) : 100;

        $image_counter = 0;

        $scopeConfig = $this->connectDB->objectManager->get('\Magento\Framework\App\Config\ScopeConfigInterface');
        $replace_images = $scopeConfig->getValue(
            'iceshop_icecatconnect/icecatconnect_service_settings/replace_product_images_by_icecat'
        );

        $eav_attribute_table_name = $this->connectDB->resource->getTableName('eav_attribute');
        $catalog_product_entity_media_gallery = $this->connectDB->resource->getTableName(
            'catalog_product_entity_media_gallery'
        );
        $catalog_product_entity_media_gallery_value = $this->connectDB->resource->getTableName(
            'catalog_product_entity_media_gallery_value'
        );
        $catalog_product_entity_media_gallery_value_to_entity = $this->connectDB->resource->getTableName(
            'catalog_product_entity_media_gallery_value_to_entity'
        );
        $media_gallery_select_attribute_id = $this->connectDB->connection->query(
            "SELECT attribute_id FROM $eav_attribute_table_name WHERE `attribute_code` = 'media_gallery';"
        );
        $media_gallery_id = $media_gallery_select_attribute_id->fetch()['attribute_id'];

        $directory_list = $this->connectDB->objectManager->get(
            '\Magento\Framework\App\Filesystem\DirectoryList'
        );

        $media_folder = $directory_list->getPath('media') .
            '/' . 'catalog' .
            '/' . 'product';
        $connect_folder = '/icecatconnect/';

        $media_path = $media_folder . $connect_folder;

        $swatch_image_attribute_id_fetch = $this->connectDB->connection->query(" 
            SELECT `attribute_id` 
            FROM {$this->connectDB->resource->getTableName('eav_attribute')} 
            WHERE attribute_code = 'swatch_image';")
            ->fetch();
        $swatch_image_attribute_id = null;
        if (!empty($swatch_image_attribute_id_fetch['attribute_id'])) {
            $swatch_image_attribute_id = $swatch_image_attribute_id_fetch['attribute_id'];
        }

        $store_ids = implode(',', $this->stores);

        $internal_batch = 120;
        $number_of_iterations = ceil($batch_size / $internal_batch);

        $broken_count = 0;

        $filter_image_size = (bool)$scopeConfig->getValue(
            'iceshop_icecatconnect/icecatconnect_service_settings/filter_image_size'
        );
        $filter_image_size_value = (float)$scopeConfig->getValue(
            'iceshop_icecatconnect/icecatconnect_service_settings/filter_image_size_value'
        );

        for ($i = 1, $j = 0; $i <= $number_of_iterations; $i++, $j++) {

            $limit = $internal_batch;

            if ($i == 1) {
                if ((($batch_size - $internal_batch * $j) % $internal_batch != 0)) {
                    $limit = $batch_size % $internal_batch;
                }
            }

            $successDownloaded = [];

            $imagesQuery = $this->connectDB->connection->query("
                        SELECT `entity_id`, 
                        `product_id`, 
                        `external_url` as `url`, 
                        SUBSTRING_INDEX(external_url,'/',-1) as `file_name`,
                        `internal_url`,
                        `is_default`
                        FROM `icecat_products_images` 
                        WHERE `deleted` = 0 
                        AND `broken` = '{$broken}'
                        AND `internal_url` = ''
                        LIMIT {$limit};"
            );

            $fetchImages = $imagesQuery->fetchAll();
            $arProdIds = [];

            if (!empty($fetchImages)) {

                // initialization of multi curl

                $chs = [];
                $cmh = curl_multi_init();
                if ($broken == 0) {
                    foreach ($fetchImages as $key => $image) {
                        $image_name = $image['file_name'];
                        $prefix_folder_path = $media_path.substr($image_name, 0,2).'/';
                        try {
                            if (!file_exists($media_path)) {
                                mkdir($media_path, 0775, true);
                            }
                            if (!file_exists($prefix_folder_path)) {
                                mkdir($prefix_folder_path, 0775, true);
                            }
                            $tmp_file = $prefix_folder_path . $image['file_name'];

                            if (!file_exists($tmp_file)) {

                                $chs[$key] = curl_init();
                                curl_setopt($chs[$key], CURLOPT_URL, $image['url']);
                                curl_setopt($chs[$key], CURLOPT_RETURNTRANSFER, 1);
                                curl_setopt($chs[$key], CURLOPT_SSL_VERIFYHOST, 0);
                                curl_setopt($chs[$key], CURLOPT_CONNECTTIMEOUT, 20);
                                curl_setopt($chs[$key], CURLOPT_TIMEOUT, 60);
                                curl_multi_add_handle($cmh, $chs[$key]);
                            }

                            $image['internal_url'] = $connect_folder.substr($image_name, 0,2).'/'. $image['product_id']
                                . $image['file_name'];
                            $successDownloaded[$key] = $image;

                        } catch (\Exception $e) {
                            $fields = [];
                            $fields['broken'] = '1';
                            $where = $this->connectDB->connection->quoteInto('entity_id = ?', $image['entity_id']);

                            $this->connectDB->connection->update('icecat_products_images', $fields, $where);
                            $response['API_ERROR'][$image['entity_id']][] = [
                                'comment' => 'Image link is broken or file corrupted',
                                'status' => 'Error while downloading image',
                                'message' => $e->getMessage()
                            ];
                        }
                    }
                } else {
                    foreach ($fetchImages as $key => $image) {
                        $image_name = $image['file_name'];
                        $prefix_folder_path = $media_path.substr($image_name, 0,2).'/';

                        try {
                            if (!file_exists($media_path)) {
                                mkdir($media_path, 0775, true);
                            }
                            if (!file_exists($prefix_folder_path)) {
                                mkdir($prefix_folder_path, 0775, true);
                            }
                            $tmp_file = $prefix_folder_path . $image['file_name'];

                            if (!file_exists($tmp_file)) {

                                $chs[$key] = curl_init();
                                curl_setopt($chs[$key], CURLOPT_URL, $image['url']);
                                curl_setopt($chs[$key], CURLOPT_RETURNTRANSFER, 1);
                                curl_setopt($chs[$key], CURLOPT_SSL_VERIFYHOST, 0);
                                curl_multi_add_handle($cmh, $chs[$key]);
                            }

                            $image['internal_url'] = $connect_folder.substr($image_name, 0,2).'/'. $image['product_id']
                                . $image['file_name'];
                            $successDownloaded[$key] = $image;

                        } catch (\Exception $e) {
                            $fields = [];
                            $fields['broken'] = '1';
                            $where = $this->connectDB->connection->quoteInto('entity_id = ?', $image['entity_id']);

                            $this->connectDB->connection->update('icecat_products_images', $fields, $where);
                            $response['API_ERROR'][$image['entity_id']][] = [
                                'comment' => 'Image link is broken or file corrupted',
                                'status' => 'Error while downloading image',
                                'message' => $e->getMessage()
                            ];
                        }
                    }
                }

                $running = null;
                do {
                    curl_multi_exec($cmh, $running);
                } while ($running > 0);

                $skipped = [];
                foreach ($successDownloaded as $k => $image) {
                    $arProdIds[] = $image['product_id'];
                    if (isset($chs[$k])) {
                        if (curl_getinfo($chs[$k])['http_code'] == '200') {
                            if ($filter_image_size) {
                                $bytesToMB = curl_getinfo($chs[$k])['size_download'] / 1048576; //convert to MB
                                if ($bytesToMB > $filter_image_size_value) {
                                    $skipped[] = $image['entity_id'];
                                    continue;
                                }
                            }
                            file_put_contents($media_folder . $image['internal_url'], curl_multi_getcontent($chs[$k]));
                        } else {
                            unset($successDownloaded[$k]);
                            $fields = [];
                            $fields['broken'] = '1';
                            $where = $this->connectDB->connection->quoteInto('entity_id = ?', $image['entity_id']);
                            $broken_count++;
                            $this->connectDB->connection->update('icecat_products_images', $fields, $where);
                            $response['API_ERROR'][$image['entity_id']][] = [
                                'comment' => 'Image link is broken or file corrupted',
                                'status' => 'Error while downloading image',
                            ];
                        }

                        curl_multi_remove_handle($cmh, $chs[$k]);
                        curl_close($chs[$k]);
                    }
                }
                if(!empty($skipped)){
                    $fields = [];
                    $fields['deleted'] = 1;
                    $where = $this->connectDB->connection->quoteInto('entity_id IN (?)', $skipped);
                    $this->connectDB->connection->update('icecat_products_images', $fields, $where);

                }

                curl_multi_close($cmh);
            }

            $product_entity_type_id = $this->connectDB->objectManager->create(
                '\Magento\Catalog\Model\Product'
            )->getResource()->getEntityType()->getId();

            $select_img_attrs = $this->connectDB->connection->query("
            SELECT attribute_id, attribute_code 
            FROM {$this->connectDB->resource->getTableName('eav_attribute')} 
            WHERE attribute_code IN ('thumbnail', 'image', 'small_image') 
            AND entity_type_id = '$product_entity_type_id';
        ");
            $this->img_attrs = $select_img_attrs->fetchAll(\PDO::FETCH_KEY_PAIR);
            $this->_dataDecode($data);
            $mediaGalleryId = null;

            $mediaGalleryQuery = " 
            SELECT `attribute_id` 
            FROM {$this->connectDB->resource->getTableName('eav_attribute')} 
            WHERE attribute_code = 'media_gallery';";

            $mediaGalleryId = $this->connectDB->connection->fetchOne($mediaGalleryQuery);

            if (!empty($successDownloaded)) {
                $products_cleaned = [];

                $successDownloadedPath = array_map(function ($element) {
                    return $element['internal_url'];
                }, $successDownloaded);

                $arProdIdsStr = implode(',', array_unique($arProdIds));

                $select_images_to_delete = $this->connectDB->connection->query("
                            SELECT cpemav.entity_id, cpema.value
                            FROM {$this->connectDB->resource->getTableName('catalog_product_entity_media_gallery')} cpema
                            JOIN {$this->connectDB->resource->getTableName('catalog_product_entity_media_gallery_value')} cpemav
                            ON cpema.value_id = cpemav.value_id
                            WHERE cpema.value_id IN (SELECT value_id
                            FROM {$this->connectDB->resource->getTableName('catalog_product_entity_media_gallery_value_to_entity')}
                            WHERE entity_id IN ($arProdIdsStr)) AND cpemav.store_id IN ($store_ids);
                            ");

                $images_to_delete = $select_images_to_delete->fetchAll();

                $images_to_delete_processed = [];
                foreach ($images_to_delete as $image_to_delete) {
                    $images_to_delete_processed[$image_to_delete['entity_id']][] = $image_to_delete['value'];
                }

                foreach ($successDownloaded as $image) {
                    $arProdIds[] = $image['product_id'];
                    $product_id = $image['product_id'];

                    if ($replace_images == 1) {
                        if (!in_array($image['product_id'], $products_cleaned)) {
                            $products_cleaned[] = $image['product_id'];

                            //run the cleanup
                            $images_to_delete = [];
                            if (isset($images_to_delete_processed[$product_id])) {
                                $images_to_delete = $images_to_delete_processed[$product_id];
                            }

                            $icecatImages = $this->connectDB->connection->query("SELECT `entity_id`, `internal_url` 
                                FROM icecat_products_images 
                                WHERE `product_id` = '{$image['product_id']}' 
                                AND `internal_url` <> ''
                                ;");
                            $fetchIcecatImages = $icecatImages->fetchAll(\PDO::FETCH_KEY_PAIR);

                            if (!empty($fetchIcecatImages)) {
                                $images_to_delete = array_diff($images_to_delete, $fetchIcecatImages);
                            }

                            if (!empty($successDownloadedPath)) {
                                $images_to_delete = array_diff($images_to_delete, $successDownloadedPath);
                            }

                            if (!empty($images_to_delete)) {
                                $resources = [
                                    'mediaGalleryId' => $mediaGalleryId,
                                    'media_folder' => $media_folder,
                                    'swatch_image_attribute_id' => $swatch_image_attribute_id
                                ];
                                $this->cleanMagentoGallery($image['product_id'], $images_to_delete, $resources);
                            }
                        }
                    }

                    $internal_url = $image['internal_url'];

                    if ($image['is_default'] == 1) {
                        $this->addDefaultImageToAllStores($product_id, $internal_url);
                    }

                    $response[$image['entity_id']][] = [
                        'status' => 'Image was created successfully',
                        'item' => $image
                    ];
                    $image_counter++;

                    if (!empty($internal_url)) {
                        $fields = [];
                        if (file_exists($media_folder.$internal_url)) {
                            $fields['internal_url'] = $internal_url;
                        }
                        if ($broken == 1) {
                            $fields['broken'] = '0';
                        }
                        $where = $this->connectDB->connection->quoteInto('entity_id = ?', $image['entity_id']);

                        try {
                            $this->connectDB->connection->update('icecat_products_images', $fields, $where);

                            $response[$image['entity_id']][] = [
                                'status' => 'Image was added successfully',
                                'item' => $image
                            ];
                        } catch (\Exception $e) {
                            $response['API_ERROR'][$image['entity_id']][] = [
                                'comment' => $e->getMessage(),
                                'status' => 'Error while saving data to mapping table',
                                'item' => $image
                            ];
                        }
                    }

                    $entity_id = $image['product_id'];

                    if (file_exists($media_folder.$internal_url)) {

                        $this->connectDB->connection->query("INSERT INTO $catalog_product_entity_media_gallery (
                                                      `attribute_id`, 
                                                      `value`) 
                                                    VALUES (
                                                      '$media_gallery_id', 
                                                      '$internal_url'
                                                    );"
                        );
                        $insert_id = $this->lastInsertId($catalog_product_entity_media_gallery, 'value_id');

                        foreach ($this->stores as $store_id) {

                            $this->connectDB->connection->query("
                              INSERT INTO $catalog_product_entity_media_gallery_value (
                                  `value_id`,
                                  `store_id`,
                                  `entity_id`, 
                                  `position`
                              ) VALUES (
                                  '$insert_id', 
                                  '$store_id', 
                                  '$entity_id', 
                                  '$insert_id'
                              );
                           ");
                        }

                        $this->connectDB->connection->query("
                      INSERT INTO $catalog_product_entity_media_gallery_value (
                          `value_id`,
                          `store_id`,
                          `entity_id`, 
                          `position`
                      ) VALUES (
                          '$insert_id', 
                          0, 
                          '$entity_id', 
                          0
                      );");
                        $this->connectDB->connection->query("
                    INSERT INTO $catalog_product_entity_media_gallery_value_to_entity (
                        `value_id`, 
                        `entity_id`
                    ) 
                    VALUES(
                      '$insert_id', 
                      '$entity_id'
                      );");
                        unset($image, $link, $size, $image_str, $types, $internal_url);
                    }
                }
            }
        }

        $response['image_counter'] = $image_counter;
        return json_encode($response);
    }

    /**
     * Try upload broken images (that have flag  broken = 1)
     * @param mixed $data
     * @return array|string
     */
    public function processBrokenProductsImages($data)
    {
        $broken = 1;
        return $this->processProductsImagesQueue($data, $broken);
    }

    public function fixFailedAttributes()
    {
        $response = [];

        $sql = "
        DELETE
        e.*
        FROM { $this->connectDB->resource->getTableName('catalog_product_entity_int')} e
        INNER JOIN { $this->connectDB->resource->getTableName('eav_attribute')} a 
        ON e.`attribute_id` = a.`attribute_id` AND attribute_code LIKE 'i\_%\_1'
        INNER JOIN { $this->connectDB->resource->getTableName('eav_attribute_option')} o 
        ON e.`value` = o.`option_id`
        WHERE o.`attribute_id` <> a.`attribute_id`;";
        try {
            $this->connectDB->connection->query($sql);
            $response['succes'] = 1;
        } catch (\Exception $e) {
            $response['API_ERROR'] = [
                'comment' => $e->getMessage(),
                'status' => 'Error during cleanup of failed attributes.'
            ];
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $response['API_ERROR'] = [
                'comment' => $e->getMessage(),
                'status' => 'Error during cleanup of failed attributes.'
            ];
        }
        return json_encode($response);
    }

    public function filtersMap($key = false, $value = false, $get = true)
    {
        if (!empty($key)) {
            if ($get == true) {
                return !empty($this->arFiltersMap[$key]) ? $this->arFiltersMap[$key] : false;
            } else {
                $this->arFiltersMap[$key] = $value;
            }
        } else {
            return $this->arFiltersMap;
        }
    }

    /**
     * Parse filters and format them to be applicable for collection filtration
     *
     * @param null|object|array $filters
     * @param array $fieldsMap Map of field names in format: array('field_name_in_filter' => 'field_name_in_db')
     * @return array
     */
    public function parseFilters($filters, $fieldsMap = null)
    {
        // if filters are used in SOAP they must be represented in array format to be used for collection filtration
        if (is_object($filters)) {
            $parsedFilters = [];
            // parse simple filter
            if (isset($filters->filter) && is_array($filters->filter)) {
                foreach ($filters->filter as $field => $value) {
                    if (is_object($value) && isset($value->key) && isset($value->value)) {
                        $parsedFilters[$value->key] = $value->value;
                    } else {
                        $parsedFilters[$field] = $value;
                    }
                }
            }
            // parse complex filter
            if (isset($filters->complex_filter) && is_array($filters->complex_filter)) {
                $parsedFilters += $this->_parseComplexFilter($filters->complex_filter);
            }

            $filters = $parsedFilters;
        }
        // make sure that method result is always array
        if (!is_array($filters)) {
            $filters = [];
        }
        // apply fields mapping
        if (isset($fieldsMap) && is_array($fieldsMap)) {
            foreach ($filters as $field => $value) {
                if (isset($fieldsMap[$field])) {
                    unset($filters[$field]);
                    $field = $fieldsMap[$field];
                    $filters[$field] = $value;
                }
            }
        }
        return $filters;
    }

    /**
     * Parses complex filter, which may contain several nodes, e.g. when user want to fetch orders which were updated
     * between two dates.
     *
     * @param array $complexFilter
     * @return array
     */
    public function _parseComplexFilter($complexFilter)
    {
        $parsedFilters = [];

        foreach ($complexFilter as $filter) {
            if (!isset($filter->key) || !isset($filter->value)) {
                continue;
            }

            list($fieldName, $condition) = [$filter->key, $filter->value];
            $conditionName = $condition->key;
            $conditionValue = $condition->value;
            $this->formatFilterConditionValue($conditionName, $conditionValue);

            if (array_key_exists($fieldName, $parsedFilters)) {
                $parsedFilters[$fieldName] += [$conditionName => $conditionValue];
            } else {
                $parsedFilters[$fieldName] = [$conditionName => $conditionValue];
            }
        }

        return $parsedFilters;
    }

    /**
     * Convert condition value from the string into the array
     * for the condition operators that require value to be an array.
     * Condition value is changed by reference
     *
     * @param string $conditionOperator
     * @param string $conditionValue
     */
    public function formatFilterConditionValue($conditionOperator, &$conditionValue)
    {
        if (is_string($conditionOperator) && in_array($conditionOperator, ['in', 'nin', 'finset'])
            && is_string($conditionValue)
        ) {
            $delimiter = ',';
            $conditionValue = explode($delimiter, $conditionValue);
        }
    }

    /**
     * @return array
     */
    public function processDefaultProductsImages($data)
    {

        $data = json_encode($data, true);

        $response = [];

        $table_name = $this->connectDB->resource->getTableName('catalog_product_entity');
        $optionId = false;
        $eav = $this->connectDB->objectManager->get('\Magento\Eav\Model\Config');

        $attribute = $eav->getAttribute('catalog_product', 'active_ice')->getData();
        if (isset($attribute['attribute_id'])) {
            $options = $eav->getAttribute('catalog_product', 'active_ice')->getSource()->getAllOptions();
            foreach ($options as $option) {
                if ($option['label'] == 'Yes') {
                    $optionId = $option['value'];
                    break;
                }
            }
        }

        $select = $this->connectDB->connection
            ->select()
            ->from('icecat_products_images', '*')
            ->where($this->connectDB->connection->quoteInto('deleted=?', '0'))
            ->where($this->connectDB->connection->quoteInto('is_default=?', '1'))
            ->where($this->connectDB->connection->quoteInto('broken=?', '0'))
            ->where($this->connectDB->connection->quoteInto('internal_url<>?', ''));
        if ($optionId !== false) {
            $select->joinInner(['t1' => $table_name], "icecat_products_images.product_id = t1.entity_id", [])
                ->where("t1.active_ice = '" . $optionId . "'");
        }

        if (!empty($data['page_size'])) {
            if (empty($data['page'])) {
                $data['page'] = 1;
            }
            $select->limitPage($data['page'], $data['page_size']);
        }

        $query = $select->query();

        try {

            $default_images = [];
            while ($row = $query->fetch()) {
                $default_images[] = $row;
            }
        } catch (\Exception $e) {

            $response['API_ERROR'] = [
                'comment' => $e->getMessage(),
                'status' => 'Error while fetching default images'
            ];
        }

        if (!empty($default_images)) {
            foreach ($default_images as $image) {
                try {
                    $product_ = $this->connectDB->objectManager->create(
                        'ICEShop\ICECatConnect\Model\ICEShopICECatConnectProduct'
                    )->load(
                        $image['product_id']
                    );
                    $product_->setImage(
                        $product_->currentUploadFileName
                    )->setSmallImage(
                        $product_->currentUploadFileName
                    )->setThumbnail(
                        $product_->currentUploadFileName
                    );
                    $response[$image['entity_id']][] = [
                        'status' => 'Default image updated',
                        'item' => $image
                    ];
                } catch (\Exception $e) {
                    $response['API_ERROR'][$image['entity_id']][] = [
                        'comment' => 'Error with update default image (' . $e->getMessage() . ')',
                        'status' => 'Error while updating default image',
                        'item' => $image
                    ];
                }
                unset($image);
            }
        }
        unset($default_images, $read_resource, $image, $select);
        return json_encode($response);
    }

    /**
     * Import attributes GTIN or MPN/Brand in to Magento shop
     *
     * @param mixed $data
     * @return string
     * @throws \Exception
     */
    public function saveMainAttributesBatch($data)
    {

        $store_obj = $this->connectDB->objectManager->create(
            '\Magento\Store\Model\StoreManagerInterface'
        );

        $allStores = [];
        $allStores[] = 0; //default store
        foreach ($store_obj->getStores() as $str) {
            $allStores[] = $str->getId();
        }

        $response = [];
        $this->_dataDecode($data);
        if (!empty($data) && is_array($data)) {
            $main_attributes_mapping = $this->getICEShopMapping();
            $products_api_model = $this->connectDB->objectManager->create('Magento\Catalog\Model\Product');
            foreach ($data as $product) {
                if (!array_key_exists('product_id', $product)) {
                    continue;
                }
                $product_obj = $products_api_model->load($product['product_id']);
                $type_flag = false;
                if (array_key_exists('ean', $product)) {
                    if ($main_attributes_mapping['ean'] == '') {
                        continue;
                    }
                    $type_flag = 'ean';
                } elseif (array_key_exists('brand_name', $product) && array_key_exists('mpn', $product)) {
                    if ($main_attributes_mapping['brand_name'] == '' || $main_attributes_mapping['mpn'] == '') {
                        continue;
                    }
                    $type_flag = 'brand_mpn';
                }
                if ($type_flag == false) {
                    continue;
                }
                try {
                    switch ($type_flag) {
                        case 'ean':
                            //ean
                            $attribute_code = $main_attributes_mapping['ean'];
                            $product_obj->setData(
                                $attribute_code,
                                $product['ean']
                            );
                            break;

                        case 'brand_mpn':
                            //brand
                            $attribute_code = $main_attributes_mapping['brand_name'];
                            $brand_name_attribute_type = $product_obj->getResource()
                                ->getAttribute($attribute_code)
                                ->getFrontend()
                                ->getInputType();
                            switch ($brand_name_attribute_type) {
                                case 'select':
                                case 'dropdown':
                                    $product_brand_name = $product_obj->getAttributeText($attribute_code);
                                    if (strtolower($product_brand_name) != strtolower($product['brand_name'])) {
                                        $add_option_id = false;
                                        $attribute_ = $this->connectDB->objectManager->create(
                                            'Magento\Eav\Model\Config'
                                        )->getAttribute(
                                            ProductAttributeInterface::ENTITY_TYPE_CODE,
                                            $attribute_code
                                        );
                                        $options = $attribute_->getSource()->getAllOptions();
                                        if (!empty($options)) {
                                            foreach ($options as $key => $value) {
                                                if (!empty($value['label'])) {
                                                    if ($value['label'] == $product['brand_name']) {
                                                        $add_option_id = $value['value'];
                                                        break;
                                                    }
                                                }
                                            }
                                        }
                                        if ($add_option_id == false) {
                                            $attr = $this->connectDB->objectManager->create(
                                                '\Magento\Eav\Model\ResourceModel\Entity\Attribute'
                                            );
                                            $attrId = $attr->getIdByCode(
                                                ProductAttributeInterface::ENTITY_TYPE_CODE,
                                                $main_attributes_mapping['brand_name']
                                            );
                                            $option = [];
                                            $option['attribute_id'] = $attrId;
                                            $option['value'][$product['brand_name']][0] = $product['brand_name'];
                                            foreach ($allStores as $store) {
                                                $option['value'][$product['brand_name']][$store] =
                                                    $product['brand_name'];
                                            }
                                            $this->connectDB->objectManager->create(
                                                'Magento\Eav\Setup\EavSetup'
                                            )->addAttributeOption($option);

                                            $add_option_id = false;
                                            $attribute_ = $this->connectDB->objectManager->create(
                                                'Magento\Eav\Model\Config'
                                            )
                                                ->getAttribute(
                                                    ProductAttributeInterface::ENTITY_TYPE_CODE,
                                                    $attribute_code
                                                );
                                            $options = $attribute_->getSource()->getAllOptions();
                                            if (!empty($options)) {
                                                foreach ($options as $key => $value) {
                                                    if (!empty($value['label'])) {
                                                        if ($value['label'] == $product['brand_name']) {
                                                            $add_option_id = $value['value'];
                                                            break;
                                                        }
                                                    }
                                                }
                                            }
                                            if ($add_option_id !== false) {
                                                $product_obj->setData(
                                                    $attribute_code,
                                                    $add_option_id
                                                );
                                            }
                                        } else {
                                            $product_obj->setData(
                                                $attribute_code,
                                                $add_option_id
                                            );
                                        }
                                    }
                                    break;
                                case 'text':
                                    $product_brand_name = $product_obj->getData($attribute_code);
                                    if (empty($product_brand_name)) {
                                        $product_obj->setData(
                                            $attribute_code,
                                            $product['brand_name']
                                        );
                                    }
                                    break;
                            }
                            //mpn
                            $attribute_code = $main_attributes_mapping['mpn'];
                            $product_mpn = $product_obj->getData($attribute_code);
                            if (empty($product_mpn)) {
                                $product_obj->setData(
                                    $attribute_code,
                                    $product['mpn']
                                );
                            } else {
                                if (strtolower($product_mpn) != strtolower($product['mpn'])) {
                                    $product_obj->setData(
                                        $attribute_code,
                                        $product['mpn']
                                    );
                                }
                            }
                            break;
                    }

                    try {
                        if (is_array($errors = $product_obj->validate())) {
                            $strErrors = [];
                            foreach ($errors as $code => $error) {
                                if ($error === true) {
                                    $error = __('Value for "%1" is invalid.', $code)->render();
                                } else {
                                    $error = __('Value for "%1" is invalid: %2', $code, $error)->render();
                                }
                                $strErrors[] = $error;
                            }
                            $this->_fault('data_invalid', implode("\n", $strErrors));
                        }
                        $product_obj->save();
                        $response[]['product'] = $product['product_id'];
                    } catch (\Exception $e) {
                        $response['API_ERROR'][$product['product_id']] = [
                            'comment' => 'data_invalid : (' . $e->getMessage() . ')',
                            'exception' => [
                                'class' => get_class($e),
                                'file' => $e->getFile(),
                                'line' => $e->getLine(),
                                'trace' => $e->getTraceAsString()
                            ]
                        ];
                    }
                } catch (\Exception $e) {
                    $response['API_ERROR'][$product['product_id']] = [
                        'comment' => $e->getMessage(),
                        'exception' => [
                            'class' => get_class($e),
                            'file' => $e->getFile(),
                            'line' => $e->getLine(),
                            'trace' => $e->getTraceAsString()
                        ],
                        'status' => 'Error while updating the product',
                        'product_id' => $product['product_id']
                    ];
                }
            }
        }
        return json_encode($response);
    }

    /**
     * Update attributes labels, sort order
     *
     * @param mixed $data
     * @return string
     * @throws \Exception
     */
    public function attributesRefresh($data)
    {
        $response = [];

        try {
            $source = $data;
            $source = json_decode($source, true);
            $attribute = '';
            if (isset($source['data']) && isset($source['attribute'])) {
                $data = $source['data'];
                $attribute = $source['attribute'];
            } else {
                $response['API_ERROR'] = [
                    'comment' => 'Data not correct',
                ];
            }
            $model = $this->_getAttribute($attribute);
            $entityType = $this->connectDB->objectManager->get(
                '\Magento\Catalog\Model\Product'
            )->getResource()->getEntityType()->getId();
            if ($model->getEntityTypeId() != $entityType) {
                $this->_fault('Entity not same');
            }
            $data['attribute_code'] = $model->getAttributeCode();
            $data['is_user_defined'] = $model->getIsUserDefined();
            $data['frontend_input'] = $model->getFrontendInput();
            $attribute_api_model = $this->connectDB->objectManager->create(
                'ICEShop\ICECatConnect\Model\ICECatConnectCatalogProductAttributeApi'
            );
            $attribute_api_model->prepareDataForSave($data);
            $model->addData($data);
            try {
                $model->save();
                $response[] = [
                    'comment' => 'Success updated',
                ];
                // clear translation cache because attribute labels are stored in translation
            } catch (\Exception $e) {
                $response['API_ERROR'] = [
                    'comment' => 'unable_to_save (' . $e->getMessage() . ')',
                ];
            }
        } catch (\Exception $e) {
            $response['API_ERROR'] = [
                'comment' => $e->getMessage(),
            ];
        }
        return json_encode($response);
    }

    public function _getAttribute($attribute)
    {
        $model = ObjectManager::getInstance()->create('Magento\Catalog\Model\ResourceModel\Eav\Attribute');

        if (is_numeric($attribute)) {
            $model->load((int)$attribute);
        } else {
            $model->load($attribute, 'attribute_code');
        }

        if (!$model->getId()) {
            $this->_fault('not_exists');
        }

        return $model;
    }

    public function _fault($phrase, $msg = null)
    {
        if (isset($msg)) {
            $phrase = $phrase . '(' . $msg . ')';
        }
        throw new \Exception($phrase);
    }

    /**
     * Decode date that receive from server
     *
     * @param $data
     */
    public function _dataDecode(&$data)
    {
        if (is_string($data)) {
            $data = json_decode($data, true);
        }
    }
}
