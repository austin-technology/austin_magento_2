<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Mostviewed
 */


namespace Amasty\Mostviewed\Model\ResourceModel;

use Magento\Framework\Model\AbstractModel;
use Amasty\Mostviewed\Helper\Data;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Store\Model\ScopeInterface;
use Amasty\Mostviewed\Model\Config\Source\DataSource;

class Rule extends \Magento\Rule\Model\ResourceModel\AbstractResource
{
    /**
     * @var \Magento\Framework\EntityManager\EntityManager
     */
    private $entityManager;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    private $messageManager;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * Rule constructor.
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @param \Magento\Framework\DataObject $associatedEntityMap
     * @param \Magento\Framework\EntityManager\EntityManager $entityManager
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param null $connectionName
     */
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        \Magento\Framework\DataObject $associatedEntityMap,
        \Magento\Framework\EntityManager\EntityManager $entityManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        $connectionName = null
    ) {
        $this->_associatedEntitiesMap = $associatedEntityMap->getData();
        parent::__construct($context, $connectionName);
        $this->entityManager = $entityManager;
        $this->scopeConfig = $scopeConfig;
        $this->messageManager = $messageManager;
        $this->storeManager = $storeManager;
    }

    /**
     * Model Initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('amasty_mostviewed_rule', 'rule_id');
    }

    /**
     * Retrieve store ids of specified rule
     *
     * @param int $ruleId
     * @return array
     */
    public function getStoreIds($ruleId)
    {
        return $this->getAssociatedEntityIds($ruleId, 'store');
    }

    /**
     * @param AbstractModel $object
     * @param mixed $value
     * @param string $field
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function load(AbstractModel $object, $value, $field = null)
    {
        return $this->entityManager->load($object, $value);
    }

    /**
     * @param AbstractModel $object
     * @return $this
     * @throws \Exception
     */
    public function save(AbstractModel $object)
    {
        $this->entityManager->save($object);
        return $this;
    }

    /**
     * @param AbstractModel $object
     * @return $this
     * @throws CouldNotDeleteException
     */
    public function delete(AbstractModel $object)
    {
        if (in_array($object->getRuleId(), $this->getActiveConfigRuleIds())) {
            throw new CouldNotDeleteException(
                __('This rule is using in extension. 
                Please remove rule from Stores >  Configuration >  Amasty extension > Automated Related Products 
                before deleting.')
            );
        }

        $this->entityManager->delete($object);

        return $this;
    }

    /**
     * Get all active rule IDs from configurations
     *
     * @return array
     */
    public function getActiveConfigRuleIds()
    {
        $ruleIds = [];

        foreach ($this->storeManager->getStores() as $store) {
            $storeId = $store->getId();
            if ($this->isActiveProductSource(Data::CROSS_SELLS_CONFIG_NAMESPACE, $storeId)) {
                $ruleIds[] = $this->getConfigRuleId(Data::CROSS_SELLS_CONFIG_NAMESPACE, $storeId);
            }
            if ($this->isActiveProductSource(Data::RELATED_PRODUCTS_CONFIG_NAMESPACE, $storeId)) {
                $ruleIds[] = $this->getConfigRuleId(Data::RELATED_PRODUCTS_CONFIG_NAMESPACE, $storeId);
            }
            if ($this->isActiveProductSource(Data::UP_SELLS_CONFIG_NAMESPACE, $storeId)) {
                $ruleIds[] = $this->getConfigRuleId(Data::UP_SELLS_CONFIG_NAMESPACE, $storeId);
            }
        }
        $ruleIds = array_unique($ruleIds);

        return $ruleIds;
    }

    /**
     * Check if data source is product conditions for type and store and this type is active
     *
     * @param $type - cross sell/related/upsell
     * @param $storeId
     * @return bool
     */
    public function isActiveProductSource($type, $storeId)
    {
        $isActiveProductSource = false;

        if ($this->isTypeEnabled($type, $storeId) && $this->isProductConditionSource($type, $storeId)) {
            $isActiveProductSource = true;
        }

        return $isActiveProductSource;
    }

    /**
     * Check if data source is product conditions for type and store
     *
     * @param $type - cross sell/related/upsell
     * @param $storeId
     * @return bool
     */
    public function isProductConditionSource($type, $storeId)
    {
        $isProductConditionSource = false;

        if ($this->getConfigDataSource($type, $storeId) == DataSource::PRODUCT_CONDITIONS) {
            $isProductConditionSource = true;
        }

        return $isProductConditionSource;
    }

    /**
     * Get Rule Data Source from config by current type and scope
     *
     * @param $type - cross sell/related/upsell
     * @param $storeId
     * @return mixed
     */
    public function getConfigDataSource($type, $storeId)
    {
        return $this->scopeConfig->getValue(
            'ammostviewed/'. $type. '/data_source',
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Get Rule ID from config by current type and scope
     *
     * @param $type - cross sell/related/upsell
     * @param $storeId
     * @return mixed
     */
    public function getConfigRuleId($type, $storeId)
    {
        return $this->scopeConfig->getValue(
            'ammostviewed/'. $type. '/condition_id',
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Check if type setting is enabled
     *
     * @param $type - cross sell/related/upsell
     * @param $storeId
     * @return bool
     */
    public function isTypeEnabled($type, $storeId)
    {
        return (bool)$this->scopeConfig->getValue(
            'ammostviewed/'. $type. '/enabled',
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Get product IDs by Rule
     *
     * @param $ruleId
     * @return array
     */
    public function getProductIds($ruleId)
    {
        $connection = $this->getConnection();
        $idsSql = $connection->select()
            ->from($this->getTable('amasty_mostviewed_product_index'), ['product_id'])
            ->where('rule_id = ?', $ruleId);
        $ids = array_unique($connection->fetchCol($idsSql));

        return $ids;
    }

    /**
     * @param AbstractModel $rule
     *
     * @return $this
     */
    protected function _afterDelete(AbstractModel $rule)
    {
        if ($rule->getRuleId()) {
            $connection = $this->getConnection();
            $connection->delete(
                $this->getTable('amasty_mostviewed_rule_store'),
                ['rule_id=?' => $rule->getId()]
            );
        }

        return parent::_afterDelete($rule);
    }
}
