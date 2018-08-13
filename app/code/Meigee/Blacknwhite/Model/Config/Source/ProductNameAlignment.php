<?php
namespace Meigee\Blacknwhite\Model\Config\Source;

class ProductNameAlignment implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
		return [
			  ['value' => 'text-left', 'label' => __('Left')],
			  ['value' => 'text-center', 'label' => __('Center')],
			  ['value' => 'text-right', 'label' => __('Right')]
		];
    }
}