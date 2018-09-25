<?php

namespace ICEShop\ICECatConnect\Test\Unit\Model;

//use ICEShop\ICECatConnect\Model\ICECatConnect;
//use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\App\Bootstrap;
use Magento\Framework\App\Http;

use Magento\Framework\App\Area;
use Magento\Framework\App\State as AppState;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\View\Element\Template\Context as TemplateContext;
use Magento\Framework\Filesystem;
use Magento\Framework\View\Element\Template\File\Resolver as FileResolver;
use Magento\Framework\View\Element\Template\File\Validator as TemplateFileValidator;
use Magento\Framework\View\TemplateEnginePool;
use Magento\Framework\App\ObjectManager\ConfigLoader;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManager;
use Vendor\Module\Block\Block as TestingBlock;
use ICEShop\ICECatConnect\Test\Unit\ObjectManagerFactory as TestObjectManagerFactory;
use Magento\Framework\App\ProductMetadataInterface;

class ActionTest extends \PHPUnit_Framework_TestCase
{

    protected $objectManager;

    protected $realObjectManager;

    protected $appState;

    protected $checkVersion;

    protected $product;

    protected $active_ice;

    protected $connection = null;

    protected $resource = null;

    protected function initRealObjectManager ()
    {
        $realObjectManagerFactory = new TestObjectManagerFactory();
        $this->realObjectManager = $realObjectManagerFactory->create();
        $frontendConfigurations = $this->realObjectManager
            ->get(ConfigLoader::class)
            ->load(Area::AREA_FRONTEND);
        $this->realObjectManager->configure($frontendConfigurations);
    }

    public function setUp ()
    {

        $this->objectManager = new ObjectManager($this);
        $this->initRealObjectManager();

        $this->appState = $this->realObjectManager->get(AppState::class);
        $this->appState->setAreaCode(Area::AREA_FRONTEND);

        if (!$this->connection) {
            $resource = $this->realObjectManager->create('\Magento\Framework\App\ResourceConnection');
            $this->connection = $resource->getConnection(
                \Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION
            );
        }

        if (!$this->resource) {
            $this->resource = $this->realObjectManager->create('\Magento\Framework\App\ResourceConnection');
        }

        $objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        $this->icecatconnect = $objectManager->getObject('\ICEShop\ICECatConnect\Model\ICECatConnect');
    }

    public function testGetICEshopIcecatconnectorExtensionVersion ()
    {

        $productMetadata = $this->realObjectManager->create('Magento\Framework\App\ProductMetadataInterface');
        $version = $productMetadata->getVersion();

        $this->expectedCheckVersion = json_encode($version);
        $this->assertEquals($this->expectedCheckVersion, $this->icecatconnect->getICEshopIcecatconnectorExtensionVersion());
    }

    public function testGetProductsBatch ()
    {

        $fetch_all_id_of_product = $this->connection->fetchAll("SELECT entity_id, active_ice FROM catalog_product_entity LIMIT 1;");

        if (!empty($fetch_all_id_of_product)) {
            $this->product = $fetch_all_id_of_product[0]['entity_id'];
            $this->active_ice = $fetch_all_id_of_product[0]['active_ice'];
        }
        else {
            $this->product = 0;
            $this->active_ice = false;
        }

        $optionId = 0;
        $eav = $this->realObjectManager->get('\Magento\Eav\Model\Config');
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

        if ($this->active_ice && $this->active_ice == $optionId) {
            $product = $this->realObjectManager->create('Magento\Catalog\Model\Product')->load($this->product);

            $scopeConfig = $this->realObjectManager->get('\Magento\Framework\App\Config\ScopeConfigInterface');
            $mpn_attribute_code = $scopeConfig->getValue(
                'iceshop_icecatconnect/icecatconnect_products_mapping/products_mapping_mpn'
            );

            $brand_name_attribute_code = $scopeConfig->getValue(
                'iceshop_icecatconnect/icecatconnect_products_mapping/products_mapping_brand'
            );

            $ean_attribute_code = $scopeConfig->getValue(
                'iceshop_icecatconnect/icecatconnect_products_mapping/products_mapping_gtin'
            );

            $item['product_id'] = $product->getId();
            $item['updated'] = $product->getData('updated_ice');

            if(isset($mpn_attribute_code)) $item['mpn'] = $product->getData($mpn_attribute_code);
            if(isset($brand_name_attribute_code)) $item['brand_name'] = $product->getData($brand_name_attribute_code);
            if(isset($ean_attribute_code)) $item['ean'] = $product->getData($ean_attribute_code);

            $data['products'][] = $item;
        }

        $data['default_attribute_set'] = $this->realObjectManager->create('Magento\Catalog\Model\Product')->getDefaultAttributeSetid();
        $result = json_encode($data);
        $this->assertEquals($result, $this->icecatconnect->GetProductsBatch($this->product));
    }

    public function testGetLanguageMapping()
    {

        $table_name = $this->resource->getTableName('core_config_data');

        $selectScopeConfig = $this->connection->query("SELECT ccd.path, ccd.value FROM $table_name ccd WHERE ccd.path LIKE 'iceshop_icecatconnect/%';");
        $scopeConfig = $selectScopeConfig->fetchAll(\PDO::FETCH_KEY_PAIR);

        $is_multilingual = isset($scopeConfig['iceshop_icecatconnect/icecatconnect_language_mapping/multilingual_mode']) ? $scopeConfig['iceshop_icecatconnect/icecatconnect_language_mapping/multilingual_mode'] : null;
        $attribute_update_required = isset($scopeConfig['iceshop_icecatconnect/icecatconnect_service_settings/products_update_attributes']) ? $scopeConfig['iceshop_icecatconnect/icecatconnect_service_settings/products_update_attributes'] : null;

        $storeManager = $this->realObjectManager->get('\Magento\Store\Model\StoreManagerInterface');
        $store_id = $storeManager->getStore()->getId();

        $attribute_sort_order_update_required = 0;
        $attribute_labels_update_required = 0;
        if (!isset($attribute_update_required)) {
            $attribute_update_required = 1;
        }
        if ($attribute_update_required == 1) {
            //fetch label/sort_order settings
            $attribute_sort_order_update_required = isset($scopeConfig['iceshop_icecatconnect/icecatconnect_service_settings/products_update_sort_order']) ? $scopeConfig['iceshop_icecatconnect/icecatconnect_service_settings/products_update_sort_order'] : null;
            $attribute_labels_update_required = isset($scopeConfig['iceshop_icecatconnect/icecatconnect_service_settings/update_attribute_labels']) ? $scopeConfig['iceshop_icecatconnect/icecatconnect_service_settings/update_attribute_labels'] : null;
        }
        if ($is_multilingual == 1) {
            //multilingual mode
            $mapping = isset($scopeConfig['iceshop_icecatconnect/icecatconnect_language_mapping/multilingual_values']) ? $scopeConfig['iceshop_icecatconnect/icecatconnect_language_mapping/multilingual_values'] : null;

            //fix store_id = 0
            $mapping = json_decode($mapping, true);
            if (isset($mapping[$store_id])) {
                $mapping[0] = $mapping[$store_id];
                $mapping[0]['store_id'] = 0;
                $mapping = json_encode($mapping);
            }
        } else {
            //one language mode
            $mapping = [
                (object)[
                    'store_id' => 0,
                    'value' => isset($scopeConfig['iceshop_icecatconnect/icecatconnect_language_mapping/main_language_single']) ? $scopeConfig['iceshop_icecatconnect/icecatconnect_language_mapping/main_language_single'] : null
                ],
                (object)[
                    'store_id' => $store_id,
                    'value' => isset($scopeConfig['iceshop_icecatconnect/icecatconnect_language_mapping/main_language_single']) ? $scopeConfig['iceshop_icecatconnect/icecatconnect_language_mapping/main_language_single'] : null
                ],
                (object)[
                    'store_id' => 9999,
                    'value' => isset($scopeConfig['iceshop_icecatconnect/icecatconnect_language_mapping/insurance_language_single']) ? $scopeConfig['iceshop_icecatconnect/icecatconnect_language_mapping/insurance_language_single'] : null
                ]
            ];
            $mapping = json_encode($mapping);
        }
        $response = [
            'multi' => $is_multilingual,
            'mapping' => $mapping,
            'exact_language_import' => isset($scopeConfig['iceshop_icecatconnect/icecatconnect_language_mapping/strict_language_import']) ? $scopeConfig['iceshop_icecatconnect/icecatconnect_language_mapping/strict_language_import'] : null,
            'attribute_update_required' => $attribute_update_required,
            'attribute_sort_order_update_required' => $attribute_sort_order_update_required,
            'attribute_labels_update_required' => $attribute_labels_update_required
        ];

        $mapping = [];
        $mapping['mpn'] = isset($scopeConfig['iceshop_icecatconnect/icecatconnect_products_mapping/products_mapping_mpn']) ? $scopeConfig['iceshop_icecatconnect/icecatconnect_products_mapping/products_mapping_mpn'] : null;
        $mapping['brand_name'] = isset($scopeConfig['iceshop_icecatconnect/icecatconnect_products_mapping/products_mapping_brand']) ? $scopeConfig['iceshop_icecatconnect/icecatconnect_products_mapping/products_mapping_brand'] : null;
        $mapping['ean'] = isset($scopeConfig['iceshop_icecatconnect/icecatconnect_products_mapping/products_mapping_gtin']) ? $scopeConfig['iceshop_icecatconnect/icecatconnect_products_mapping/products_mapping_gtin'] : null;
        if (isset($scopeConfig['iceshop_icecatconnect/icecatconnect_products_mapping/default_description_attributes']) && $scopeConfig['iceshop_icecatconnect/icecatconnect_products_mapping/default_description_attributes'] == 0) {
            $mapping['description']['name'] = isset($scopeConfig['iceshop_icecatconnect/icecatconnect_products_mapping/name_attribute']) ? $scopeConfig['iceshop_icecatconnect/icecatconnect_products_mapping/name_attribute'] : null;
            $mapping['description']['short_description'] = isset($scopeConfig['iceshop_icecatconnect/icecatconnect_products_mapping/short_description_attribute']) ? $scopeConfig['iceshop_icecatconnect/icecatconnect_products_mapping/short_description_attribute'] : null;
            $mapping['description']['long_description'] = isset($scopeConfig['iceshop_icecatconnect/icecatconnect_products_mapping/long_description_attribute']) ? $scopeConfig['iceshop_icecatconnect/icecatconnect_products_mapping/long_description_attribute'] : null;
            $mapping['description']['icecat_products_name'] = isset($scopeConfig['iceshop_icecatconnect/icecatconnect_products_mapping/icecat_products_name_attribute']) ? $scopeConfig['iceshop_icecatconnect/icecatconnect_products_mapping/icecat_products_name_attribute'] : null;
        }
        // Import main mapping attributes if empty values
        $main_attributes_mapping = $mapping;
        if (($main_attributes_mapping['mpn'] != '') && ($main_attributes_mapping['brand_name'] != '')
            && ($main_attributes_mapping['ean'] != '')) {
            $import_main_attributes = [];
            $import_main_attributes['mapping'] = $main_attributes_mapping;
            $import_main_attributes['ean'] = isset($scopeConfig['iceshop_icecatconnect/icecatconnect_products_mapping/import_gtin']) ? $scopeConfig['iceshop_icecatconnect/icecatconnect_products_mapping/import_gtin'] : null;
            $import_main_attributes['brand_mpn'] = isset($scopeConfig['iceshop_icecatconnect/icecatconnect_products_mapping/import_brand_mpn']) ? $scopeConfig['iceshop_icecatconnect/icecatconnect_products_mapping/import_brand_mpn'] : null;
            $response['import_main_attributes'] = $import_main_attributes;
        }
        if (!empty($main_attributes_mapping) && array_key_exists('description', $main_attributes_mapping)) {
            $response['description'] = $main_attributes_mapping['description'];
        }
        $this->assertEquals(json_encode($response), $this->icecatconnect->getLanguageMapping());
    }
}
