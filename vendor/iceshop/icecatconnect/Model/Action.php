<?php

// @codingStandardsIgnoreFile

namespace ICEShop\ICECatConnect\Model;

use Magento\Eav\Model\Entity\Attribute\AbstractAttribute;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ObjectManager;

class Action extends \Magento\Catalog\Model\ResourceModel\Product\Action
{

    protected $linkIdField;
    /**
     * Insert or Update attribute data
     *
     * @param \Magento\Catalog\Model\AbstractModel $object
     * @param AbstractAttribute $attribute
     * @param mixed $value
     * @return $this
     */
    public function _saveAttributeValue($object, $attribute, $value)
    {
        $connection = $this->getConnection();
        $storeId = (int)$this->_storeManager->getStore($object->getStoreId())->getId();
        $table = $attribute->getBackend()->getTable();

        $entityId = $this->resolveEntityId($object->getId(), $table);

        /**
         * If we work in single store mode all values should be saved just
         * for default store id
         * In this case we clear all not default values
         */

        if ($table == $this->_resource->getTableName("catalog_product_entity")) {
            $attribute_code = $attribute->getData()['attribute_code'];

            $data = new \Magento\Framework\DataObject(
                [
                    'attribute_id' => $attribute->getAttributeId(),
                    'store_id' => $storeId,
                    $this->getLinkField() => $entityId,
                    'value' => $this->_prepareValueForSave($value, $attribute),
                    $attribute_code => $this->_prepareValueForSave($value, $attribute)
                ]
            );
        } else {
            if ($this->_storeManager->hasSingleStore()) {
                $storeId = $this->getDefaultStoreId();
                $connection->delete(
                    $table,
                    [
                        'attribute_id = ?' => $attribute->getAttributeId(),
                        $this->getLinkField() . ' = ?' => $entityId,
                        'store_id <> ?' => $storeId
                    ]
                );
            }
            $data = new \Magento\Framework\DataObject(
                [
                    'attribute_id' => $attribute->getAttributeId(),
                    'store_id' => $storeId,
                    $this->getLinkField() => $entityId,
                    'value' => $this->_prepareValueForSave($value, $attribute),
                ]
            );
        }

        $bind = $this->_prepareDataForTable($data, $table);

        if ($attribute->isScopeStore()) {
            /**
             * Update attribute value for store
             */
            $this->_attributeValuesToSave[$table][] = $bind;
        } elseif ($attribute->isScopeWebsite() && $storeId != $this->getDefaultStoreId()) {
            /**
             * Update attribute value for website
             */
            $storeIds = $this->_storeManager->getStore($storeId)->getWebsite()->getStoreIds(true);
            foreach ($storeIds as $storeId) {
                $bind['store_id'] = (int)$storeId;
                $this->_attributeValuesToSave[$table][] = $bind;
            }
        } else {
            /**
             * Update global attribute value
             */
            $bind['store_id'] = $this->getDefaultStoreId();
            $this->_attributeValuesToSave[$table][] = $bind;
        }

        return $this;
    }

    /**
     * Save and delete collected attribute values
     *
     * @return $this
     */
    public function _processAttributeValues()
    {
        $connection = $this->getConnection();
        foreach ($this->_attributeValuesToSave as $table => $data) {
            if ($table == $this->_resource->getTableName("catalog_product_entity")) {
                foreach ($data as $key => $value) {
                    if (isset($value['store_id'])) {
                        unset($data[$key]['store_id']);
                    }
                }
                $connection->insertOnDuplicate($table, $data, []);
            } else {
                $connection->insertOnDuplicate($table, $data, ['value']);
            }
        }

        foreach ($this->_attributeValuesToDelete as $table => $valueIds) {
            $connection->delete($table, ['value_id IN (?)' => $valueIds]);
        }

        // reset data arrays
        $this->_attributeValuesToSave = [];
        $this->_attributeValuesToDelete = [];

        return $this;
    }

    /**
     * Prepare value for save
     *
     * @param mixed $value
     * @param AbstractAttribute $attribute
     * @return mixed
     */
    public function _prepareValueForSave($value, AbstractAttribute $attribute)
    {
        $type = $attribute->getBackendType();
        if (($type == 'int' || $type == 'decimal' || $type == 'datetime') && $value === '') {
            $value = null;
        } elseif ($type == 'decimal') {
            $value = $this->_localeFormat->getNumber($value);
        }
        $backendTable = $attribute->getBackendTable();
        if (!isset(self::$_attributeBackendTables[$backendTable])) {
            self::$_attributeBackendTables[$backendTable] = $this->getConnection()->describeTable($backendTable);
        }
        $describe = self::$_attributeBackendTables[$backendTable];
        if ($backendTable == $this->_resource->getTableName("catalog_product_entity")) {
            $attribute_code = $attribute->getData()['attribute_code'];
            return $this->getConnection()->prepareColumnValue($describe[$attribute_code], $value);
        } else {
            return $this->getConnection()->prepareColumnValue($describe['value'], $value);
        }
    }
    protected function resolveEntityId($entityId)
    {
        if ($this->getIdFieldName() == $this->getLinkField()) {
            return $entityId;
        }
        $select = $this->getConnection()->select();
        $tableName = $this->_resource->getTableName('catalog_product_entity');
        $select->from($tableName, [$this->getLinkField()])
            ->where('entity_id = ?', $entityId);
        return $this->getConnection()->fetchOne($select);
    }
    public function getLinkField()
    {
        if (!$this->linkIdField) {
            $indexList = $this->getConnection()->getIndexList($this->getEntityTable());
            $pkName = $this->getConnection()->getPrimaryKeyName($this->getEntityTable());
            $this->linkIdField = $indexList[$pkName]['COLUMNS_LIST'][0];
            if (!$this->linkIdField) {
                $this->linkIdField = $this->getEntityIdField();
            }
        }

        return $this->linkIdField;
    }
}
