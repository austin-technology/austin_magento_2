<?php
namespace Meigee\Blacknwhite\Model\Config\Source;

class CurrencySwitcher implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
		return [
			  ['value' => 'currency_select', 'label' => __('Select Box'), 'img' => 'Meigee_Blacknwhite::images/currency_select.png'],
			  ['value' => 'currency_images', 'label' => __('Flags'), 'img' => 'Meigee_Blacknwhite::images/currency_images.png']
		];
    }
}