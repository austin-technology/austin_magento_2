<?php
namespace Meigee\Blacknwhite\Model\Config\Source;

class ContactmapBlockPos implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
		return [
			  ['value' => 'left-top', 'label' => __('Left Top')],
			  ['value' => 'right-top', 'label' => __('Right Top')],
			  ['value' => 'left-bottom', 'label' => __('Left Bottom')],
			  ['value' => 'right-bottom', 'label' => __('Right Bottom')]
		];
    }
}