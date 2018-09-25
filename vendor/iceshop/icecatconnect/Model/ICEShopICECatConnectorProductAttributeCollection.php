<?php

namespace ICEShop\ICECatConnect\Model;

use \Magento\Catalog\Model\ResourceModel\Product\Attribute\Collection;
use Magento\Framework\App\Action\Context;

class ICEShopICECatConnectorProductAttributeCollection extends
 \Magento\Catalog\Model\ResourceModel\Product\Attribute\Collection
{
    /**
     * Initialize select object
     *
     * @return $this
     */
    public function _initSelect()
    {
        $entityTypeId = (int)$this->_eavEntityFactory->create()->setType(
            \Magento\Catalog\Model\Product::ENTITY
        )->getTypeId();
        $columns = $this->getConnection()->describeTable($this->getResource()->getMainTable());
        unset($columns['attribute_id']);
        $retColumns = [];
        foreach ($columns as $labelColumn => $columnData) {
            $retColumns[$labelColumn] = $labelColumn;
            if ($columnData['DATA_TYPE'] == \Magento\Framework\DB\Ddl\Table::TYPE_TEXT) {
                $retColumns[$labelColumn] = 'main_table.' . $labelColumn;
            }
        }
        $this->getSelect()->from(
            ['main_table' => $this->getResource()->getMainTable()],
            $retColumns
        )->join(
            ['additional_table' => $this->getTable('catalog_eav_attribute')],
            'additional_table.attribute_id = main_table.attribute_id'
        )->joinLeft(
            ['additional_table_2' => 'icecatconnector_attribute_connection'],
            'additional_table_2.attribute_id_foreign = main_table.attribute_id'
        )->where(
            'main_table.entity_type_id = ?',
            $entityTypeId
        );
        return $this;
    }
}
