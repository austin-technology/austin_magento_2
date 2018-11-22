<?php
/**
 * Created by PhpStorm.
 * User: hiennq
 * Date: 26/12/2017
 * Time: 18:44
 */

namespace Magenest\StripePayment\Controller\Checkout\Eps;

class Response extends \Magenest\StripePayment\Controller\Checkout\Response
{
    protected function setSourceAdditionalInformation($source, $payment)
    {
        parent::setSourceAdditionalInformation($source, $payment);
        $reference = $source->eps->reference;
        $sourceAdditionalInformation = [];
        $sourceAdditionalInformation[] = [
            'label' => "Payment Method",
            'value' => "EPS"
        ];
        if($reference){
            $sourceAdditionalInformation[] = [
                'label' => "Reference",
                'value' => $reference
            ];
        }
        $payment->setAdditionalInformation("stripe_source_additional_information", json_encode($sourceAdditionalInformation));
    }
}