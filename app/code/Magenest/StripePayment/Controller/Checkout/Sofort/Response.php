<?php
/**
 * Created by PhpStorm.
 * User: hiennq
 * Date: 27/12/2017
 * Time: 11:32
 */

namespace Magenest\StripePayment\Controller\Checkout\Sofort;

class Response extends \Magenest\StripePayment\Controller\Checkout\Response
{
    protected function setSourceAdditionalInformation($source, $payment)
    {
        parent::setSourceAdditionalInformation($source, $payment);
        $bankName = $source->sofort->bank_name;
        $bankCode = $source->sofort->bank_code;
        $bic = $source->sofort->bic;
        $ibanLast4 = $source->sofort->iban_last4;
        $sourceAdditionalInformation = [];
        $sourceAdditionalInformation[] = [
            'label' => "Payment Method",
            'value' => "SOFORT"
        ];
        if($bankName){
            $sourceAdditionalInformation[] = [
                'label' => "Bank name",
                'value' => $bankName
            ];
        }
        if($bankCode){
            $sourceAdditionalInformation[] = [
                'label' => "Bank code",
                'value' => $bankCode
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
        $payment->setAdditionalInformation("stripe_source_additional_information", json_encode($sourceAdditionalInformation));
    }
}
