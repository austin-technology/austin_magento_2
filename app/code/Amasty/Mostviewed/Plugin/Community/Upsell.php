<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Mostviewed
 */


namespace Amasty\Mostviewed\Plugin\Community;

class Upsell extends \Amasty\Mostviewed\Plugin\Community\AbstractProduct
{
    
    /**
     * @param $items
     * @return array|\Magento\Catalog\Model\ResourceModel\Product\Collection|\Magento\Framework\Data\Collection
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetItemCollection($object, $items)
    {
        return $this->prepareCollection(\Amasty\Mostviewed\Helper\Data::UP_SELLS_CONFIG_NAMESPACE, $items);
    }
}
