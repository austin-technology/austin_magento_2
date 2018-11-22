<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Mostviewed
 */


namespace Amasty\Mostviewed\Model;

use Amasty\Mostviewed\Api\Data;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;

class RuleRepository implements \Amasty\Mostviewed\Api\RuleRepositoryInterface
{
    /**
     * @var ResourceModel\Rule
     */
    private $ruleResource;

    /**
     * @var \Amasty\Mostviewed\Model\RuleFactory
     */
    private $ruleFactory;

    /**
     * @var Indexer\RuleProcessor
     */
    private $ruleIndexProcessor;

    /**
     * @var array
     */
    private $rules = [];

    public function __construct(
        ResourceModel\Rule $ruleResource,
        \Amasty\Mostviewed\Model\RuleFactory $ruleFactory,
        \Amasty\Mostviewed\Model\Indexer\RuleProcessor $ruleIndexProcessor
    ) {
        $this->ruleFactory = $ruleFactory;
        $this->ruleResource = $ruleResource;
        $this->ruleIndexProcessor = $ruleIndexProcessor;
    }

    /**
     * {@inheritdoc}
     */
    public function save(Data\RuleInterface $rule)
    {
        if ($rule->getRuleId()) {
            $rule = $this->get($rule->getRuleId())->addData($rule->getData());
        }

        try {
            $this->ruleResource->save($rule);
            unset($this->rules[$rule->getId()]);
        } catch (\Exception $e) {
            if ($rule->getRuleId()) {
                throw new CouldNotSaveException(
                    __('Unable to save rule with ID %1. Error: %2', [$rule->getRuleId(), $e->getMessage()])
                );
            }
            throw new CouldNotSaveException(__('Unable to save new rule. Error: %1', $e->getMessage()));
        }
        if ($this->ruleIndexProcessor->isIndexerScheduled()) {
            $this->ruleIndexProcessor->markIndexerAsInvalid();
        } else {
            $this->ruleIndexProcessor->reindexRow($rule->getRuleId());
        }
        return $rule;
    }

    /**
     * {@inheritdoc}
     */
    public function get($ruleId)
    {
        if (!isset($this->rules[$ruleId])) {
            /** @var \Amasty\Mostviewed\Model\Rule $rule */
            $rule = $this->ruleResource->load($this->ruleFactory->create(), $ruleId);
            if (!$rule->getRuleId()) {
                throw new NoSuchEntityException(__('Rule with specified ID "%1" not found.', $ruleId));
            }
            $this->rules[$ruleId] = $rule;
        }
        return $this->rules[$ruleId];
    }

    /**
     * {@inheritdoc}
     */
    public function delete(Data\RuleInterface $rule)
    {
        try {
            $this->ruleResource->delete($rule);
            unset($this->rules[$rule->getRuleId()]);
        } catch (\Exception $e) {
            if ($rule->getRuleId()) {
                throw new CouldNotDeleteException(
                    __('Unable to remove rule with ID %1. Error: %2', [$rule->getRuleId(), $e->getMessage()])
                );
            }
            throw new CouldNotDeleteException(__('Unable to remove rule rule. Error: %1', $e->getMessage()));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($ruleId)
    {
        $model = $this->get($ruleId);
        $this->delete($model);
        return true;
    }
}
