<?php
namespace Meigee\Blacknwhite\Model\Config\Source;

class LangSwitcher implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
		return [
			  ['value' => 'language_select', 'label' => __('Select Box'), 'img' => 'Meigee_Blacknwhite::images/language_select.png'],
			  ['value' => 'language_flags', 'label' => __('Flags'), 'img' => 'Meigee_Blacknwhite::images/language_flags.png']
		];
    }
}