<?php
/**
 * Created by PhpStorm.
 * User: hiennq
 * Date: 27/12/2017
 * Time: 11:32
 */

namespace Magenest\StripePayment\Controller\Checkout\Alipay;

class Source extends \Magenest\StripePayment\Controller\Checkout\Source
{
    protected function getReturnUrl()
    {
        $returnUrl = $this->storeManagerInterface->getStore()->getBaseUrl()."stripe/checkout_alipay/response";
        return $returnUrl;
    }

    protected function getSourceType()
    {
        return "alipay";
    }
}
