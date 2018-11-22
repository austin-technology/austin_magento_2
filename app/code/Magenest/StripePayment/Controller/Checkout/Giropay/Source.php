<?php
/**
 * Created by PhpStorm.
 * User: joel
 * Date: 02/01/2017
 * Time: 18:18
 */

namespace Magenest\StripePayment\Controller\Checkout\Giropay;

class Source extends \Magenest\StripePayment\Controller\Checkout\Source
{
    protected function getReturnUrl()
    {
        $returnUrl = $this->storeManagerInterface->getStore()->getBaseUrl()."stripe/checkout_giropay/response";
        return $returnUrl;
    }

    protected function getSourceType()
    {
        return "giropay";
    }
}