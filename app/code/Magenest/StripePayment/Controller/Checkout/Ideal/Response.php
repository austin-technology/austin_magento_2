<?php
/**
 * Created by PhpStorm.
 * User: hiennq
 * Date: 27/12/2017
 * Time: 11:32
 */

namespace Magenest\StripePayment\Controller\Checkout\Ideal;

class Response extends \Magenest\StripePayment\Controller\Checkout\Response
{
    protected function setSourceAdditionalInformation($source, $payment)
    {
        parent::setSourceAdditionalInformation($source, $payment);
        $bank = $source->ideal->bank;
        $bic = $source->ideal->bic;
        $ibanLast4 = $source->ideal->iban_last4;
        $sourceAdditionalInformation = [];
        $sourceAdditionalInformation[] = [
            'label' => "Payment Method",
            'value' => "iDEAL"
        ];
        if($bank){
            $sourceAdditionalInformation[] = [
                'label' => "Bank",
                'value' => $bank
            ];
        }
        if($bic){
            $sourceAdditionalInformation[] = [
                'label' => "BIC",
                'value' => $bic
            ];
        }
        if($ibanLast4){
            $sourceAdditionalInformation[] = [
                'label' => "IBAN last4",
                'value' => $ibanLast4
            ];
        }
//        if($statementDescriptor){
//            $sourceAdditionalInformation[] = [
//                'label' => "statement_descriptor",
//                'value' => $statementDescriptor
//            ];
//        }
        $payment->setAdditionalInformation("stripe_source_additional_information", json_encode($sourceAdditionalInformation));
    }
}
