<?php
/**
 * Copyright 2018 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\OneStepCheckout\Model\Config\Backend;

use Magento\Framework\App\Config\Value;

/**
 * Class ConfigValue
 * @package Aheadworks\OneStepCheckout\Model\Config\Backend
 */
class ConfigValue extends Value
{
    /** Get serialized value
     *
     * @return mixed
     */
    public function resolveSerializedValue()
    {
        return @unserialize($this->getValue()) !== false ? @unserialize($this->getValue()) : $this->getValue();
    }
}
