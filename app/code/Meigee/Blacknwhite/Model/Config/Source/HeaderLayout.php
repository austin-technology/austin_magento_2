<?php
namespace Meigee\Blacknwhite\Model\Config\Source;

class HeaderLayout implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
		return [
			  ['value' => 'header-1', 'label' => __('Header #1 (White)')],
			  ['value' => 'header-2', 'label' => __('Header #2 (Black)')]
		];
    }
}