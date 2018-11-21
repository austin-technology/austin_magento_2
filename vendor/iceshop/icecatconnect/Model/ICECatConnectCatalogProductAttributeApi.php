<?php
namespace ICEShop\ICECatConnect\Model;

use \Magento\Catalog\Api\ProductAttributeManagementInterface;
use Magento\Framework\Webapi\Exception;
use \Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ObjectManager;
use \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface as ScopedAttributeInterface;

/**
 * Defines the implementaiton class of the calculator service contract.
 */
class ICECatConnectCatalogProductAttributeApi implements \Magento\Catalog\Api\ProductAttributeManagementInterface
{
    /**
     * @var \Magento\Eav\Api\AttributeManagementInterface
     */
    public $eavAttributeManagement;

    public $objectManager = false;

    public $logger = false;

    public $eav_entity_attribute_table = false;

    public $eav_attribute_table = false;

    public $catalog_eav_attribute_table = false;

    public $catalog_product_website_table = false;

    public $catalog_product_entity_table = false;

    public $types = [];

    public $eav_attribute = null;

    public $connectDB = null;

    public $visibilities = [];

    public $eav_model_config = null;

    public $product_entity_type_id = false;

    public $modelProductApi = false;

    public $storeManagerInterface = false;

    public $attributes = [];

    public $action = false;

    /**
     * @param \Magento\Eav\Api\AttributeManagementInterface $eavAttributeManagement
     */
    public function __construct(
        \Magento\Eav\Api\AttributeManagementInterface $eavAttributeManagement
    )
    {
        $this->eavAttributeManagement = $eavAttributeManagement;
    }

    /**
     * {@inheritdoc}
     */
    public function assign($attributeSetId, $attributeGroupId, $attributeCode, $sortOrder)
    {
        return $this->eavAttributeManagement->assign(
            \Magento\Catalog\Api\Data\ProductAttributeInterface::ENTITY_TYPE_CODE,
            $attributeSetId,
            $attributeGroupId,
            $attributeCode,
            $sortOrder
        );
    }

    /**
     * {@inheritdoc}
     */
    public function unassign($attributeSetId, $attributeCode)
    {
        return $this->eavAttributeManagement->unassign($attributeSetId, $attributeCode);
    }

    /**
     * {@inheritdoc}
     */
    public function getAttributes($attributeSetId)
    {

        $sql = "SELECT ea.attribute_id, 
                ea.attribute_code, 
                ea.frontend_input, 
                ea.is_required, 
                CASE cea.is_global 
                WHEN " . ScopedAttributeInterface::SCOPE_GLOBAL . " THEN 'global' 
                WHEN " . ScopedAttributeInterface::SCOPE_WEBSITE . " THEN 'website' 
                ELSE 'store' 
                END as scope 
                FROM " . $this->eav_entity_attribute_table . " eet 
                LEFT JOIN " . $this->eav_attribute_table . " ea ON eet.attribute_id = ea.attribute_id 
                LEFT JOIN $this->catalog_eav_attribute_table cea ON cea.attribute_id = ea.attribute_id
                WHERE eet.entity_type_id = " . $this->product_entity_type_id . " 
                AND eet.attribute_set_id = {$attributeSetId};";

        $query = $this->connectDB->connection->query($sql);
        $result_query = [];
        while ($row = $query->fetch()) {
            $result_query[] = $row;
        }

        $result = [];

        $check_attribute_existence_array = $this->connectDB->connection->query("
            SELECT attribute_id FROM $this->eav_entity_attribute_table 
            WHERE entity_type_id = $this->product_entity_type_id AND attribute_set_id = $attributeSetId;
            ")->fetchAll(\PDO::FETCH_COLUMN, 'attribute_code');

        $check_attribute_existence_array_source = $this->connectDB->connection->query("
            SELECT eea.attribute_id FROM $this->eav_entity_attribute_table eea 
            JOIN $this->eav_attribute_table ea ON eea.attribute_id = ea.attribute_id
            WHERE (ea.frontend_input = 'select' OR ea.frontend_input = 'multiselect' OR ea.source_model IS NOT NULL) 
            AND ea.entity_type_id = $this->product_entity_type_id AND eea.attribute_set_id = $attributeSetId;
            ")->fetchAll(\PDO::FETCH_COLUMN, 'attribute_code');

        foreach ($result_query as $attribute) {

            $attribute_id = $attribute['attribute_id'];

            if (in_array($attribute_id, $check_attribute_existence_array)) {
                // set options
                $options = [];

                if (in_array($attribute_id, $check_attribute_existence_array_source)) {
                    $attribute_ = $this->eav_model_config->getAttribute(
                        'catalog_product',
                        $attribute['attribute_code']
                    );
                    $allOptions = $attribute_->getSource()->getAllOptions();

                    foreach ($allOptions as $option) {
                        $options[] = [
                            'value' => $option['value'],
                            'label' => (is_object($option['label'])) ? $option['label']->getText() : $option['label'],
                            'external_id' => $this->connectDB->getConversionRule($option['value'], 'attribute_option')
                        ];
                    }
                }

                $result[] = [
                    'attribute_id' => $attribute['attribute_id'],
                    'code' => $attribute['attribute_code'],
                    'type' => $attribute['frontend_input'],
                    'required' => $attribute['is_required'],
                    'scope' => $attribute['scope'],
                    'options' => $options,
                    'external_id' => $this->connectDB->getConversionRule($attribute['attribute_id'], 'attribute')
                ];
            }
        }
        return $result;
    }

    /**
     * Create new product attribute
     *
     * @param array $data input data
     * @return integer
     */
    public function create($data)
    {

        $model = $this->connectDB->objectManager->create('\Magento\Eav\Model\Entity\Attribute');
        $helper = $this->connectDB->objectManager->create('\Magento\Catalog\Helper\Product');

        if (empty($data['attribute_code']) || (isset($data['frontend_label']) && !is_array($data['frontend_label']))) {
            $this->logger->err('Not correct parameters in class `ICECatConnectCatalogProductAttributeApi`');
            $this->_fault('Not correct parameters in class `ICECatConnectCatalogProductAttributeApi`');
        }

        //validate attribute_code
        if (!preg_match('/^[a-z][a-z_0-9]{0,254}$/', $data['attribute_code'])) {
            $this->logger->err('Not valid attribute code');
            $this->_fault('Not valid attribute code');
        }

        //validate frontend_input
        $allowedTypes = [];
        foreach ($this->types() as $type) {
            $allowedTypes[] = $type['value'];
        }
        if (!in_array($data['frontend_input'], $allowedTypes)) {
            $this->logger->err('Not valid front end type');
            $this->_fault('Not valid front end type');
        }

        //try to search attribute
        $search_attribute = $this->connectDB->objectManager->create('\Magento\Eav\Model\Entity\Attribute');
        $findAttribute = $search_attribute->loadByCode($this->connectDB->objectManager->create(
            'Magento\Eav\Model\Entity\Type'
        )->loadByCode('catalog_product')->getId(), $data['attribute_code']);
        if (!empty($findAttribute->getId())) {
            $model = $findAttribute;
        } /*else {
            $this->logger->err('Not find needed attribute in class `ICECatConnectCatalogProductAttributeApi`');
            $this->_fault('Not find needed attribute in class `ICECatConnectCatalogProductAttributeApi`');
        }*/

        $data['source_model'] = $helper->getAttributeSourceModelByInputType($data['frontend_input']);
        $data['backend_model'] = $helper->getAttributeBackendModelByInputType($data['frontend_input']);
        if ($model->getIsUserDefined() === null || $model->getIsUserDefined() != 0) {
            $data['backend_type'] = $model->getBackendTypeByInput($data['frontend_input']);
        }

        $this->prepareDataForSave($data);
        $model->addData($data);
        $model->setEntityTypeId($this->connectDB->objectManager->create(
            '\Magento\Catalog\Model\Product'
        )->getResource()->getEntityType()->getId());
        $model->setIsUserDefined(1);

        try {
            $model->save();
            $attribute_id = (int)$model->getId();
            if (!empty($data['external_id'])) {
                $this->connectDB->saveConversions($data['external_id'], $attribute_id, 'attribute');
            }
        } catch (\Exception $e) {
            $this->_fault('Can not save attribute', $e->getMessage());
        }
        return (int)$model->getId();
    }

    /**
     * Retrieve list of possible attribute types
     *
     * @return array
     */
    public function types()
    {
        if (empty($this->types)) {
            $this->types = $this->connectDB->objectManager->create(
                '\Magento\Catalog\Model\Product\Attribute\Source\Inputtype'
            )->toOptionArray();
        }
        return $this->types;
    }


    /**
     * Prepare request input data for saving
     *
     * @param array $data input data
     * @return void
     */
    public function prepareDataForSave(&$data)
    {
        if ($data['scope'] == 'global') {
            $data['is_global'] = \Magento\Catalog\Api\Data\EavAttributeInterface::SCOPE_GLOBAL_TEXT;
        } elseif ($data['scope'] == 'website') {
            $data['is_global'] = \Magento\Catalog\Api\Data\EavAttributeInterface::SCOPE_WEBSITE_TEXT;
        } else {
            $data['is_global'] = \Magento\Catalog\Api\Data\EavAttributeInterface::SCOPE_STORE_TEXT;
        }
        if (!isset($data['is_configurable'])) {
            $data['is_configurable'] = 0;
        }
        if (!isset($data['is_filterable'])) {
            $data['is_filterable'] = 0;
        }
        if (!isset($data['is_filterable_in_search'])) {
            $data['is_filterable_in_search'] = 0;
        }
        if (!isset($data['apply_to'])) {
            $data['apply_to'] = '';
        } else {
            if (is_array($data['apply_to'])) {
                $data['apply_to'] = implode(',', $data['apply_to']);
            }
        }
        // set frontend labels array with store_id as keys
        if (isset($data['frontend_label']) && is_array($data['frontend_label'])) {
            $labels = [];
            foreach ($data['frontend_label'] as $label) {
                $storeId = $label['store_id'];
                $labelText = strip_tags($label['label']);
                if (trim($labelText) === '') {
                    $labelText = __('Attribute label was not defined');
                }
                $labels[$storeId] = $labelText;
            }
            $data['frontend_label'] = $labels;
            if (!empty($data['frontend_label'])) {
                foreach ($data['frontend_label'] as $lbl) {
                    $data['frontend_label'][0] = $lbl;
                    break;
                }
            }
        }
        // set additional fields
        if (isset($data['additional_fields']) && is_array($data['additional_fields'])) {
            $data = array_merge($data, $data['additional_fields']);
            unset($data['additional_fields']);
        }
        //default value
        if (!empty($data['default_value'])) {
            $data['default_value'] = strip_tags($data['default_value']);
        }
    }

    public function saveOptionsForAttribute($data, $id)
    {
        if (isset($data['options_'])) {
            $attribute_entity = $this->connectDB->objectManager->create('\Magento\Eav\Model\Entity\Attribute');
            if (!empty($data['options_'])) {
                foreach ($data['options_'] as $key => $value) {
                    if (isset($value['value'])) {
                        $attribute_entity->load($id);
                        $attribute_entity->addData(['option' => $data['options_'][$key]]);
                        $attribute_entity->save();
                    }
                }
            }
        }
    }

    /**
     * Add attribute to attribute set
     *
     * @param string $attributeId
     * @param string $attributeSetId
     * @param string|null $attributeGroupId
     * @param string $sortOrder
     * @return bool
     */
    public function attributeAdd($attributeId, $attributeSetId, $attributeGroupId = null, $sortOrder = '0')
    {
        $attribute_entity = $this->connectDB->objectManager->create('\Magento\Eav\Model\Entity\Attribute');
        // check if attribute with requested id exists
        $attribute = $attribute_entity->load($attributeId);
        if (!$attribute->getId()) {
            $this->logger->err('Invalid attribute id');
            $this->_fault('invalid_attribute_id');
        }
        // check if attribute set with requested id exists
        $attributeSet = $this->connectDB->objectManager->create('Magento\Eav\Model\Entity\Attribute\Set')
            ->load($attributeSetId);
        if (!$attributeSet->getId()) {
            $this->logger->err('Invalid attribute set id');
            $this->_fault('invalid_attribute_set_id');
        }
        // check if attribute group with requested id exists
        if (!empty($attributeGroupId)) {
            $attributeGroup = $this->connectDB->objectManager->create('Magento\Eav\Model\Entity\Attribute\Group');
            if (!$attributeGroup->load($attributeGroupId)->getId()) {
                $this->logger->err('Invalid attribute group id');
                $this->_fault('invalid_attribute_group_id');
            }
        } else {
            // define default attribute group id for current attribute set
            $attributeGroupId = $attributeSet->getDefaultGroupId();
        }

        try {
            $attribute->setEntityTypeId($attributeSet->getEntityTypeId())
                ->setAttributeSetId($attributeSetId)
                ->setAttributeGroupId($attributeGroupId)
                ->setSortOrder($sortOrder)
                ->save();
        } catch (Exception $e) {
            $this->logger->err('Error during add attribute : `' . $e->getMessage() . '`');
            $this->_fault('add_attribute_error', $e->getMessage());
        }
        return true;
    }

    private function _fault($phrase, $msg = null)
    {
        if (isset($msg)) {
            $phrase = $phrase . '(' . $msg . ')';
        }
        throw new \Exception($phrase);
    }

    public function addOption($attribute, $data)
    {

        $model = $this->connectDB->objectManager->create('\Magento\Eav\Model\Entity\Attribute')->load($attribute);

        if (!$model->usesSource()) {
            $this->logger->err('Not correct frontend input');
            $this->_fault('invalid_frontend_input');
        }

        $optionLabels = [];
        foreach ($data['label'] as $label) {
            $storeId = $label['store_id'];
            $labelText = strip_tags($label['value']);
            if (is_array($storeId)) {
                foreach ($storeId as $multiStoreId) {
                    $optionLabels[$multiStoreId] = $labelText;
                }
            } else {
                $optionLabels[$storeId] = $labelText;
            }
        }
        $modelData = [
            'value' => $optionLabels,
            'sort_order' => (int)$data['order'],
            'attribute_id' => (int)$model->getId(),
            'external_id' => $data['external_id']
        ];
        return $this->_saveOption($modelData);
    }

    public function updateOption($attribute, $option, $data)
    {

        $model = ObjectManager::getInstance()->create('\Magento\Eav\Model\Entity\Attribute')->load($attribute);

        if (!$model->usesSource()) {
            $this->_fault('invalid_frontend_input');
        }

        $optionLabels = [];
        foreach ($data['label'] as $label) {
            $storeId = $label['store_id'];
            $labelText = strip_tags($label['value']);
            if (is_array($storeId)) {
                foreach ($storeId as $multiStoreId) {
                    $optionLabels[$multiStoreId] = $labelText;
                }
            } else {
                $optionLabels[$storeId] = $labelText;
            }
        }
        $modelData = [
            'sort_order' => (int)$data['order'],
            'attribute_id' => (int)$model->getId(),
            'external_id' => $data['external_id']
        ];

        $scopeConfig = ObjectManager::getInstance()->get('\Magento\Framework\App\Config\ScopeConfigInterface');
        if ($scopeConfig->getValue('iceshop_icecatconnect/icecatconnect_service_settings/products_update_attributes')) {
            $modelData['value'] = $optionLabels;
        }

        return $this->_saveOption($modelData, $option);
    }

    public function _saveOption($option, $optionId = null)
    {

        if (is_array($option)) {
            $optionTable = $this->connectDB->resource->getTableName('eav_attribute_option');
            $optionValueTable = $this->connectDB->resource->getTableName('eav_attribute_option_value');

            $m_store = $this->connectDB->objectManager->create('Magento\Store\Model\StoreManagerInterface');
            $stores = $m_store->getStores(true);

            $attribute_id = $option['attribute_id'];

            if (isset($option['value'])) {

                $values = $option['value'];

                foreach ($values as $key => $value) {

                    $fetch_value_id = $this->connectDB->connection->query("SELECT eaov.option_id as option_id
FROM $optionValueTable eaov
RIGHT JOIN $optionTable eao ON eaov.option_id = eao.option_id
WHERE eao.attribute_id = '$attribute_id' AND eaov.value = {$this->connectDB->connection->quote($value)} AND store_id = '$key';")->fetch();

                    if ($fetch_value_id['option_id']) {
                        if (!empty($option['external_id'])) {
                            $this->connectDB->saveConversions(
                                $option['external_id'],
                                $fetch_value_id['option_id'],
                                'attribute_option'
                            );
                        }
                        return $fetch_value_id['option_id'];
                    }
                }

                $data = [
                    'attribute_id' => $option['attribute_id'],
                    'sort_order' => 0
                ];

                //rewriting default sort order if received
                if (isset($option['sort_order'])) {
                    $data['sort_order'] = (int)$option['sort_order'];
                }

                if (!$optionId) {
                    $this->connectDB->connection->insert($optionTable, $data);
                    $intOptionId = $this->connectDB->connection->lastInsertId();
                } else {
                    $intOptionId = $optionId;
                }

                if (!empty($option['external_id'])) {
                    $this->connectDB->saveConversions($option['external_id'], $intOptionId, 'attribute_option');
                }
                // Default value
                if (!isset($values[0])) {
                    if (!empty($values)) {
                        foreach ($values as $lbl) {
                            $values[0] = $lbl;
                            break;
                        }
                    }
                }

                foreach ($stores as $store) {
                    if (isset($values[$store->getId()])
                        && (!empty($values[$store->getId()])
                            || $values[$store->getId()] == "0")
                    ) {
                        $data = [
                            'option_id' => $intOptionId,
                            'store_id' => $store->getId(),
                            'value' => $values[$store->getId()],
                        ];
                        if ($optionId) {
                            $where = $this->connectDB->connection->quoteInto('option_id =?', $optionId);
                            $this->connectDB->connection->update($optionValueTable, $data, $where);
                        } else {
                            $this->connectDB->connection->insert($optionValueTable, $data);
                        }
                    }
                }

                return $intOptionId;
            }
        }
        return false;
    }

    /**
     * Update product data
     *
     * @param $productId
     * @param $productData
     * @param null $store
     * @param null $identifierType
     * @return bool
     */
    public function update($productId, $productData, $store = null)
    {
        $scopeConfig = ObjectManager::getInstance()->get('\Magento\Framework\App\Config\ScopeConfigInterface');
        $attribute_set_id_from_db_fetch = $this->connectDB->connection->query("
        SELECT attribute_set_id 
        FROM $this->catalog_product_entity_table 
        WHERE entity_id = $productId;"
        )->fetch();

        $attribute_set_id_from_db = '';

        if (isset($attribute_set_id_from_db_fetch['attribute_set_id'])) {
            $attribute_set_id_from_db = $attribute_set_id_from_db_fetch['attribute_set_id'];
        }

        if (!empty($productData['attribute_set'])) {

            $attribute_set_id = $productData['attribute_set'];

            if ($attribute_set_id_from_db != $attribute_set_id) {
                try {
                    //$this->cleanupProduct($productId);
                } catch (\Exception $e) {
                    throw new \Exception($e->getMessage());
                }
            }

            $this->connectDB->connection->query("
            UPDATE $this->catalog_product_entity_table 
            SET attribute_set_id = $attribute_set_id 
            WHERE entity_id = $productId;"
            );
        }

        if (isset($productData['additional_attributes']['multi_data'])) {
            $result = array_merge($productData['additional_attributes']['multi_data'], $productData);
        }
        else {
            $result = $productData;
        }

        $icecat_products_name = $scopeConfig->getValue(
            'iceshop_icecatconnect/icecatconnect_products_mapping/icecat_products_name_attribute'
        );

        foreach ($result as $key => $item) {
            if (gettype($item) == 'array' || $key == 'attribute_set' || ($key == 'icecat_products_name' && $icecat_products_name == '')) {
                unset($result[$key]);
            }
        }

        if (isset($result['name'])) {
            $result['url_path'] = $result['url_key'] = $this->modelProductApi->formatUrlKey($result['name']);
        }

        $result['visibility'] = !empty($this->visibilities[$productId]) ? $this->visibilities[$productId] : \Magento\Catalog\Model\Product\Visibility::VISIBILITY_NOT_VISIBLE;
        $website_id = $this->storeManagerInterface->getStore()->getWebsiteId();

        $this->connectDB->connection->query("
        INSERT IGNORE INTO $this->catalog_product_website_table (`product_id`, `website_id`) 
        VALUES ($productId, $website_id);
        ");

        try {
            $this->action->updateAttributes([$productId], $result, $store);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }

        if (isset($productData['stock_data']) && is_array($productData['stock_data'])) {
            $modelProductApi = ObjectManager::getInstance()->create('\Magento\Catalog\Model\Product');
            $product = $modelProductApi->load($productId);
            $product->setStockData($productData['stock_data']);
        }

        if (isset($productData['tier_price']) && is_array($productData['tier_price'])) {
            $modelProductApi = ObjectManager::getInstance()->create('\Magento\Catalog\Model\Product');
            $product = $modelProductApi->load($productId);
            $product->setData(
                \Magento\Catalog\Api\Data\ProductAttributeInterface::CODE_TIER_PRICE,
                $productData['tier_price']
            );
        }

        return true;
    }

    public function cleanupProduct($product_id, $trash_cleanup = false)
    {
        if (!empty($product_id)) {
            $product_set_id = $this->connectDB->objectManager->create(
                '\Magento\Catalog\Model\Product'
            )->load($product_id)->getAttributeSetId();
            $resource = ObjectManager::getInstance()->create('\Magento\Framework\App\ResourceConnection');

            $comparison_operation = '=';
            if ($trash_cleanup == true) {
                $comparison_operation = '<>';
            }
            $select = $this->connectDB->connection->query('SELECT
            t1.attribute_id
            FROM ' . $resource->getTableName('eav_attribute') . ' AS t1
            JOIN icecat_imports_conversions_rules_attribute AS t2
            ON t1.attribute_code = t2.imports_conversions_rules_symbol
            JOIN ' . $resource->getTableName('eav_entity_attribute') . ' AS t3
            ON t1.attribute_id = t3.attribute_id
            WHERE t3.attribute_set_id ' . $comparison_operation . ' ' . $product_set_id . ';');

            try {
                //fetching all the iceshop attributes IDs
                $iceshop_attributes_id = [];
                while ($row = $select->fetch()) {
                    $iceshop_attributes_id[] = $row;
                }

                if (!empty($iceshop_attributes_id)) {
                    //formatting attributes in a string to use in IN stmt
                    $attributes_ids_str = '';
                    $divider = '';
                    foreach ($iceshop_attributes_id as $attribute_id) {
                        $attributes_ids_str .= $divider . $attribute_id['attribute_id'];
                        $divider = ',';
                    }

                    //deleting values from varchar table
                    $query = 'DELETE FROM ' . $resource->getTableName('catalog_product_entity_varchar') . '
                    WHERE entity_id = ' . $product_id . ' AND attribute_id IN (' . $attributes_ids_str . ');';
                    $this->connectDB->connection->query($query);
                    //deleting values from int table
                    $query = 'DELETE FROM ' . $resource->getTableName('catalog_product_entity_int') . '
                    WHERE entity_id = ' . $product_id . ' AND attribute_id IN (' . $attributes_ids_str . ');';
                    $this->connectDB->connection->query($query);
                }
            } catch (Exception $e) {
                $this->_fault('error_during_cleanup `' . $e->getMessage() . '`');
            }

            return true;
        }
        return false;
    }
}
