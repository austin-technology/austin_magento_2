<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Mostviewed
 */


namespace Amasty\Mostviewed\Model;

use Amasty\Mostviewed\Api\Data\RuleInterface;
use Magento\Framework\Api\AttributeValueFactory;
use Magento\Framework\Api\ExtensionAttributesFactory;

class Rule extends \Magento\Rule\Model\AbstractModel implements RuleInterface
{
    /**
     * @var \Magento\SalesRule\Model\Rule\Condition\CombineFactory
     */
    private $combineFactory;

    /**
     * @var \Magento\SalesRule\Model\Rule\Action\CollectionFactory
     */
    private $actionFactory;

    protected function _construct()
    {
        parent::_construct();
        $this->_init('Amasty\Mostviewed\Model\ResourceModel\Rule');
        $this->setIdFieldName('rule_id');
        $this->combineFactory = $this->getData('combineFactory');
        $this->actionFactory = $this->getData('actionFactory');
    }

    /**
     * @param string $formName
     *
     * @return string
     */
    public function getConditionsFieldSetId($formName = '')
    {
        return $formName . 'rule_conditions_fieldset_' . $this->getId();
    }

    /**
     * Validate rule data before save
     *
     * @param \Magento\Framework\DataObject|Rule $dataObject
     * @return bool|string[] - return true if validation passed successfully. Array with errors description otherwise
     */
    public function validateData(\Magento\Framework\DataObject $dataObject)
    {
        $result = [];

        if ($dataObject->hasStoreIds()) {
            $storeIds = $dataObject->getStoreIds();
            if (empty($storeIds)) {
                $result[] = __('Please specify a store.');
            }
        }

        if (!empty($result)) {
            return $result;
        }

        return true;
    }

    /**
     * Validate product by rule
     *
     * @param \Magento\Framework\DataObject $product
     * @return bool
     */
    public function validate(\Magento\Framework\DataObject $product)
    {
        if (!parent::validate($product)) {
            return false;
        }

        $result = true;

        /** exclude case with "All Store Views" */
        if ($this->getStoreIds() != [0]) {
            $stores = array_intersect($product->getStoreIds(), $this->getStoreIds());
            if (!count($stores)) {
                $result = false;
            }
        }

        return $result;
    }

    /**
     * Get rule associated store Ids
     * Note: Rule can be for All Store View (sore_ids = array(0 => '0'))
     *
     * @return array
     */
    public function getStoreIds()
    {
        if (!$this->hasStoreIds()) {
            $storeIds = $this->_getResource()->getStoreIds($this->getId());
            $this->setData('store_ids', (array)$storeIds);
        }
        return $this->_getData('store_ids');
    }

    /**
     * Get attribute codes for rule
     *
     * @return array
     */
    public function getAttributeCodes()
    {
        $ruleAttributes = [];
        foreach ($this->getConditions()->getConditions() as $condition) {
            $ruleAttributes[] = $condition->getAttribute();
        }

        return array_unique($ruleAttributes);
    }

    /**
     * Get product IDs by Rule
     *
     * @return array
     */
    public function getProductIds()
    {
        return $this->_resource->getProductIds($this->getRuleId());
    }

    /**
     * Getter for rule conditions collection. Product Conditions
     *
     * @return \Magento\CatalogRule\Model\Rule\Condition\Combine
     */
    public function getConditionsInstance()
    {
        return $this->combineFactory->create();
    }

    /**
     * Getter for rule actions collection. Customer Condition
     *
     * @return \Magento\SalesRule\Model\Rule\Action\Collection
     */
    public function getActionsInstance()
    {
        return $this->actionFactory->create();
    }

    /**
     * {@inheritdoc}
     */
    public function getRuleId()
    {
        return $this->_getData(RuleInterface::RULE_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setRuleId($ruleId)
    {
        $this->setData(RuleInterface::RULE_ID, $ruleId);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->_getData(RuleInterface::NAME);
    }

    /**
     * {@inheritdoc}
     */
    public function setName($name)
    {
        $this->setData(RuleInterface::NAME, $name);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getConditionsSerialized()
    {
        return $this->_getData(RuleInterface::CONDITIONS_SERIALIZED);
    }

    /**
     * {@inheritdoc}
     */
    public function setConditionsSerialized($conditionsSerialized)
    {
        $this->setData(RuleInterface::CONDITIONS_SERIALIZED, $conditionsSerialized);

        return $this;
    }

}
