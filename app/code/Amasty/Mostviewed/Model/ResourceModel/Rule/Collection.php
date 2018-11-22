<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Mostviewed
 */


namespace Amasty\Mostviewed\Model\ResourceModel\Rule;

use Magento\Framework\DB\Select;

/**
 * @method\ Amasty\Mostviewed\Model\ResourceModel\Rule getResource()
 */
class Collection extends \Magento\Rule\Model\ResourceModel\Rule\Collection\AbstractCollection
{
    protected $_idFieldName = 'rule_id';

    /**
     * Collection constructor.
     *
     * @param \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Framework\DB\Adapter\AdapterInterface $connection
     * @param \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource
     * @param \Magento\Framework\DataObject $associatedEntityMap
     */
    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Framework\DataObject $associatedEntityMap,
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null
    ) {
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
        $this->_associatedEntitiesMap = $associatedEntityMap->getData();
    }

    /**
     * Provide support for Associated id filter
     *
     * @param string $field
     * @param null|string|array $condition
     * @return $this
     */
    public function addFieldToFilter($field, $condition = null)
    {
        if ($field == 'store_id') {
            return $this->addStoreFilter($condition);
        }

        parent::addFieldToFilter($field, $condition);
        return $this;
    }

    /**
     * Limit rules collection by specific stores
     *
     * @param int|int[]|\Magento\Store\Api\Data\StoreInterface $storeId
     * @return $this
     */
    public function addStoreFilter($storeId = null)
    {
        if ($storeId instanceof \Magento\Store\Model\Store) {
            $storeId = $storeId->getId();
        }
        // "All Store Views" = 0
        $this->addAssociatedFilter([$storeId, 0], 'store');
        return $this;
    }

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Amasty\Mostviewed\Model\Rule', 'Amasty\Mostviewed\Model\ResourceModel\Rule');
    }

    protected function _afterLoad()
    {
        $this->mapAssociatedEntities('store', 'store_id');

        $this->setFlag('add_websites_to_result', false);
        return parent::_afterLoad();
    }

    /**
     * Map Associated Entities
     *
     * @param string $entityType
     * @param string $objectField
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return void
     */
    protected function mapAssociatedEntities($entityType, $objectField)
    {
        if (!$this->_items) {
            return;
        }

        $entityInfo = $this->_getAssociatedEntityInfo($entityType);
        $ruleIdField = $entityInfo['rule_id_field'];
        $entityIds = $this->getColumnValues($ruleIdField);

        $select = $this->getConnection()->select()->from(
            $this->getTable($entityInfo['associations_table'])
        )->where(
            $ruleIdField . ' IN (?)',
            $entityIds
        );

        $associatedEntities = $this->getConnection()->fetchAll($select);

        foreach ($associatedEntities as $associatedEntity) {
            $item = $this->getItemByColumnValue($ruleIdField, $associatedEntity[$ruleIdField]);
            $itemAssociatedValue = $item->getData($objectField) === null ? [] : $item->getData($objectField);
            $itemAssociatedValue[] = $associatedEntity[$entityInfo['entity_id_field']];
            $item->setData($objectField, $itemAssociatedValue);
        }
    }

    /**
     * Add filter to rule's associated entity Ids by entity type
     *
     * @param int|int[] $entityIds
     * @param string    $entityType
     *
     * @return $this
     */
    protected function addAssociatedFilter($entityIds, $entityType)
    {
        if (!$this->getFlag('is_' . $entityType . '_table_joined')) {
            $entityInfo = $this->_getAssociatedEntityInfo($entityType);
            $this->setFlag('is_' . $entityType . '_table_joined', true);

            $where =  'main_table.' . $entityInfo['rule_id_field'] . ' = ' .
                $entityType . '.' . $entityInfo['rule_id_field'];

            if ($entityIds) {
                $operator = ' = ?';
                if (is_array($entityIds)) {
                    $operator = ' IN (?)';
                }
                $where .= ' ' . Select::SQL_AND . ' ' .  $entityType . '.' . $entityInfo['entity_id_field'] . $operator;
            }

            $this->getSelect()->join(
                [$entityType => $this->getTable($entityInfo['associations_table'])],
                $this->getConnection()->quoteInto($where, $entityIds),
                []
            );
        }
        return $this;
    }
}
