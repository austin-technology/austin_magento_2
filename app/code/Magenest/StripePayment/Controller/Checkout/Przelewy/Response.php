<?php
/**
 * Created by PhpStorm.
 * User: hiennq
 * Date: 26/12/2017
 * Time: 18:44
 */

namespace Magenest\StripePayment\Controller\Checkout\Przelewy;

class Response extends \Magenest\StripePayment\Controller\Checkout\Response
{
    protected function setSourceAdditionalInformation($source, $payment)
    {
        parent::setSourceAdditionalInformation($source, $payment);
        $reference = $source->p24->reference;
        $sourceAdditionalInformation = [];
        $sourceAdditionalInformation[] = [
            'label' => "Payment Method",
            'value' => "P24 (Przelewy24)"
        ];
        $payment->setAdditionalInformation("stripe_source_additional_information", json_encode($sourceAdditionalInformation));
        $payment->setAdditionalInformation("stripe_przelewy_reference", $reference);
    }
}