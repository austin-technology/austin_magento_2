<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Mostviewed
 */


/**
 * Copyright © 2016 Amasty. All rights reserved.
 */

namespace Amasty\Mostviewed\Model\Config\Source\Condition;

class Price implements \Magento\Framework\Option\ArrayInterface
{
    const ANY       = 0;
    const SAME_AS   = 1;
    const MORE      = 2;
    const LESS      = 3;

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => self::ANY,
                'label' => __('Any')
            ],
            [
                'value' => self::SAME_AS,
                'label' => __('Same as')
            ],
            [
                'value' => self::MORE,
                'label' => __('More')
            ],
            [
                'value' => self::LESS,
                'label' => __('Less')
            ],
        ];
    }
}
