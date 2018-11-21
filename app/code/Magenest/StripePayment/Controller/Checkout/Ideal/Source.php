<?php
/**
 * Created by PhpStorm.
 * User: hiennq
 * Date: 27/12/2017
 * Time: 11:32
 */

namespace Magenest\StripePayment\Controller\Checkout\Ideal;

class Source extends \Magenest\StripePayment\Controller\Checkout\Source
{
    protected function getReturnUrl()
    {
        $returnUrl = $this->storeManagerInterface->getStore()->getBaseUrl()."stripe/checkout_ideal/response";
        return $returnUrl;
    }

    protected function getSourceType()
    {
        return "ideal";
    }

    protected function getCustomRequest()
    {
        $bank = $this->getRequest()->getParam('bankValue');
        $request = [];
        if($bank) {
            $request[$this->getSourceType()]['bank'] = $bank;
        }
        return $request;
    }
}
