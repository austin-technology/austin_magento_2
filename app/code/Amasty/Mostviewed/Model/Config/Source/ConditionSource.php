<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Mostviewed
 */


namespace Amasty\Mostviewed\Model\Config\Source;

class ConditionSource implements \Magento\Framework\Option\ArrayInterface
{
    private $ruleCollectionFactory;

    public function __construct(
        \Amasty\Mostviewed\Model\ResourceModel\Rule\CollectionFactory $ruleCollectionFactory
    ) {
        $this->ruleCollectionFactory = $ruleCollectionFactory;
    }

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options = [];

        /** @var \Amasty\Mostviewed\Model\ResourceModel\Rule\Collection $ruleCollection */
        $ruleCollection = $this->ruleCollectionFactory->create();
        /** @var \Amasty\Mostviewed\Model\Rule $rule */
        foreach ($ruleCollection as $rule) {
            $options[] = ['value' => $rule->getRuleId(), 'label' => $rule->getName()];
        }

        return $options;
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        $options = [];

        /** @var \Amasty\Mostviewed\Model\ResourceModel\Rule\Collection $ruleCollection */
        $ruleCollection = $this->ruleCollectionFactory->create();
        /** @var \Amasty\Mostviewed\Model\Rule $rule */
        foreach ($ruleCollection as $rule) {
            $options[$rule->getRuleId()] = $rule->getName();
        }

        return $options;
    }
}
