<?php
/**
 * Created by PhpStorm.
 * User: magenest
 * Date: 26/05/2017
 * Time: 15:55
 */

namespace Magenest\StripePayment\Model\Source;

use Magento\Framework\Option\ArrayInterface;

class ThreedSecureAction implements ArrayInterface
{
    public function toOptionArray()
    {
        return [
            [
                'value' => 'recommended',
                'label' => __('3D Secure is recommended')
            ],
            [
                'value' => 'optional',
                'label' => __('3D Secure is optional')
            ],
        ];
    }
}
