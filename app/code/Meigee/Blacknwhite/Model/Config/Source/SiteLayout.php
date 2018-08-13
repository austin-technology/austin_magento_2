<?php
namespace Meigee\Blacknwhite\Model\Config\Source;

class SiteLayout implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
		return [
			  ['value' => 'wide-layout', 'label' => __('Wide')],
			  ['value' => 'boxed-layout', 'label' => __('Boxed')]
		];
    }
}