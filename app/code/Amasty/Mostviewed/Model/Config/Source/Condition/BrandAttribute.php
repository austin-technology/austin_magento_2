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


class BrandAttribute implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var array
     */
    protected $optionArray = [['value'=>'', 'label'=>'']];
    //
    public function __construct(\Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory $collectionFactory)
    {
        $collection = $collectionFactory->create()->addVisibleFilter();
        foreach($collection as $attribute) {
            $this->optionArray[] = [
                'value' => $attribute->getAttributeCode(),
                'label' => $attribute->getData('frontend_label'),
            ];
        }
    }


    public function toOptionArray()
    {
        return $this->optionArray;
    }
}
