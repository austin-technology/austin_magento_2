<?php
/**
 * Created by PhpStorm.
 * User: hiennq
 * Date: 27/12/2017
 * Time: 11:32
 */

namespace Magenest\StripePayment\Controller\Checkout\Bancontact;

class Source extends \Magenest\StripePayment\Controller\Checkout\Source
{
    protected function getReturnUrl()
    {
        $returnUrl = $this->storeManagerInterface->getStore()->getBaseUrl()."stripe/checkout_bancontact/response";
        return $returnUrl;
    }

    protected function getSourceType()
    {
        return "bancontact";
    }

    protected function getCustomRequest()
    {
        $language = $this->getRequest()->getParam('language');
        $request = [];
        if($language) {
            $request[$this->getSourceType()] = [
                "preferred_language" => $language
            ];
        }
        return $request;
    }
}
