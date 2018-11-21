<?php
/**
 * Created by PhpStorm.
 * User: hiennq
 * Date: 26/12/2017
 * Time: 18:44
 */

namespace Magenest\StripePayment\Controller\Checkout\Alipay;

class Response extends \Magenest\StripePayment\Controller\Checkout\Response
{
    protected function setSourceAdditionalInformation($source, $payment)
    {
        parent::setSourceAdditionalInformation($source, $payment);
        $nativeUrl = $source->alipay->native_url;
        $sourceAdditionalInformation = [];
        $sourceAdditionalInformation[] = [
            'label' => "Payment Method",
            'value' => "Alipay"
        ];
        if($nativeUrl){
            $sourceAdditionalInformation[] = [
                'label' => "Native Url",
                'value' => $nativeUrl
            ];
        }
        $payment->setAdditionalInformation("stripe_source_additional_information", json_encode($sourceAdditionalInformation));
    }
}