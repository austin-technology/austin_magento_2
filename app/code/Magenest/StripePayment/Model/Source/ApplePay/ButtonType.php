<?php

namespace Magenest\StripePayment\Model\Source\ApplePay;

class ButtonType implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return [
            ['value' => "default", 'label' => __('Default')],
            ['value' => "donate", 'label' => __('Donate')],
            ['value' => "buy", 'label' => __('Buy')]
        ];
    }
}
