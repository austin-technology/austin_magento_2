<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Mostviewed
 */


namespace Amasty\Mostviewed\Model\Config\Source;

class DataSource implements \Magento\Framework\Option\ArrayInterface
{
    const SOURCE_VIEWED = 0;
    const SOURCE_BOUGHT = 1;
    const PRODUCT_CONDITIONS = 2;
    const SOURCE_CURRENT = 3;

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::SOURCE_VIEWED, 'label' => __('Viewed together')],
            ['value' => self::SOURCE_BOUGHT, 'label' => __('Bought together')],
            ['value' => self::PRODUCT_CONDITIONS, 'label' => __('Related Product Rules')],
            ['value' => self::SOURCE_CURRENT, 'label' => __('Product currently viewed')]
        ];
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return [
            self::SOURCE_VIEWED => __('Viewed together'),
            self::SOURCE_BOUGHT => __('Bought together'),
            self::PRODUCT_CONDITIONS => __('Related Product Rules'),
            self::SOURCE_CURRENT => __('Product currently viewed')
        ];
    }
}
