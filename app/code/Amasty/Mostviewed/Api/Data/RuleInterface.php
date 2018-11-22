<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Mostviewed
 */


namespace Amasty\Mostviewed\Api\Data;

interface RuleInterface
{
    /**#@+
     * Constants defined for keys of data array
     */
    const RULE_ID = 'rule_id';
    const NAME = 'name';
    const CONDITIONS_SERIALIZED = 'conditions_serialized';
    /**#@-*/

    /**
     * @return int
     */
    public function getRuleId();

    /**
     * @param int $ruleId
     *
     * @return \Amasty\Mostviewed\Api\Data\RuleInterface
     */
    public function setRuleId($ruleId);

    /**
     * @return string|null
     */
    public function getName();

    /**
     * @param string|null $name
     *
     * @return \Amasty\Mostviewed\Api\Data\RuleInterface
     */
    public function setName($name);

    /**
     * @return string|null
     */
    public function getConditionsSerialized();

    /**
     * @param string|null $conditionsSerialized
     *
     * @return \Amasty\Mostviewed\Api\Data\RuleInterface
     */
    public function setConditionsSerialized($conditionsSerialized);
}
