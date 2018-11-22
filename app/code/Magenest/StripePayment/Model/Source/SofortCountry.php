<?php
/**
 * Created by PhpStorm.
 * User: magenest
 * Date: 26/05/2017
 * Time: 15:55
 */

namespace Magenest\StripePayment\Model\Source;

use Magento\Framework\Option\ArrayInterface;

class SofortCountry implements ArrayInterface
{
    public function toOptionArray()
    {
        return [
            [
                'value' => 'AT',
                'label' => __('Austria'),
            ],
            [
                'value' => 'BE',
                'label' => __('Belgium')
            ],
            [
                'value' => 'DE',
                'label' => __('Germany')
            ],
            [
                'value' => 'IT',
                'label' => __('Italy')
            ],
            [
                'value' => 'NL',
                'label' => __('Netherlands')
            ],
            [
                'value' => 'ES',
                'label' => __('Spain')
            ]
        ];
    }
}
