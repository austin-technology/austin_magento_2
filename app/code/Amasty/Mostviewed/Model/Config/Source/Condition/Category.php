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

class Category implements \Magento\Framework\Option\ArrayInterface
{
    const ANY       = 0;
    const SAME_AS   = 1;

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
            ]
        ];
    }
}
