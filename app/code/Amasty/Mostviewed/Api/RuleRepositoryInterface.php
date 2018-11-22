<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Mostviewed
 */


namespace Amasty\Mostviewed\Api;

/**
 * Interface RuleRepositoryInterface
 * @api
 */
interface RuleRepositoryInterface
{
    /**
     * @param \Amasty\Mostviewed\Api\Data\RuleInterface $rule
     * @return \Amasty\Mostviewed\Api\Data\RuleInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(\Amasty\Mostviewed\Api\Data\RuleInterface $rule);

    /**
     * @param int $ruleId
     * @return \Amasty\Mostviewed\Api\Data\RuleInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get($ruleId);

    /**
     * @param \Amasty\Mostviewed\Api\Data\RuleInterface $rule
     * @return bool
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function delete(\Amasty\Mostviewed\Api\Data\RuleInterface $rule);

    /**
     * @param int $ruleId
     * @return bool
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function deleteById($ruleId);
}
