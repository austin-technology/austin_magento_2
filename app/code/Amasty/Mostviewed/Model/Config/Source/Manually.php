<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Mostviewed
 */

/**
 * Copyright © 2016 Amasty. All rights reserved.
 */

namespace Amasty\Mostviewed\Model\Config\Source;


class Manually implements \Magento\Framework\Option\ArrayInterface
{
    const NOTHING = 0;
    const REPLACE = 1;
    const APPEND  = 2;

    /**
     * Return array of options as value-label pairs
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => self::NOTHING,
                'label' => __('Display Manually Added Products Only')
            ],
            [
                'value' => self::REPLACE,
                'label' => __('Replace Manually Added Products')
            ],
            [
                'value' => self::APPEND,
                'label' => __('Append to Manually Added Products')
            ],
        ];
    }
}