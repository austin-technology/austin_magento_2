<?php
/**
 * Created by PhpStorm.
 * User: hiennq
 * Date: 27/12/2017
 * Time: 11:32
 */

namespace Magenest\StripePayment\Controller\Checkout\Ideal;

use Magenest\StripePayment\Exception\StripePaymentException;
use Stripe;
use Magento\Framework\Controller\ResultFactory;

class Update extends \Magenest\StripePayment\Controller\Checkout\Source
{
    /**
     * @return array
     */
    public function execute()
    {
        $this->_debug("iDEAL Element updating source");
        $result = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        try {
            if (!$this->_formKeyValidator->validate($this->getRequest())) {
                throw new StripePaymentException(
                    __("Invalid form key")
                );
            }
            $quote = $this->_checkoutSession->getQuote();
            $source = $this->getRequest()->getParam('source');
            $this->_debug($source);
            $sourceId = @$source['id'];
            $clientSecret = @$source['client_secret'];
            if(!$sourceId){
                throw new StripePaymentException(
                    __("Source error")
                );
            }
            $payment = $quote->getPayment();
            $payment->setAdditionalInformation("stripe_source_id", $sourceId);
            $payment->setAdditionalInformation("stripe_client_secret", $clientSecret);
            $quote->save();

            $data = [
                'success' => true,
                'error' => false,
            ];
            $result->setData($data);
        }
        catch (\Magenest\StripePayment\Exception\StripePaymentException $e) {
            $result->setData([
                'error' => true,
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
        catch (\Exception $e) {
            $result->setData([
                'error' => true,
                'success' => false,
                'message' => "Cannot process payment"
            ]);
        } finally {
            return $result;
        }
    }

    /**
     * @return array
     */
    protected function getPostRequest()
    {
        return [];
    }

    protected function getReturnUrl()
    {
        // TODO: Implement getReturnUrl() method.
    }

    protected function getSourceType()
    {
        // TODO: Implement getSourceType() method.
    }
}
