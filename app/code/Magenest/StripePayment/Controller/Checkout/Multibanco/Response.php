<?php
/**
 * Created by PhpStorm.
 * User: hiennq
 * Date: 26/12/2017
 * Time: 18:44
 */

namespace Magenest\StripePayment\Controller\Checkout\Multibanco;

use Stripe;
use Magenest\StripePayment\Exception\StripePaymentException;

class Response extends \Magenest\StripePayment\Controller\Checkout\Response
{
    public function execute()
    {
        try {
            $this->_debug("Processing payment response");
            $order = $this->_checkoutSession->getLastRealOrder();
            $response = $this->getRequest()->getParams();
            $this->_debug($response);
            $sourceId = $this->getRequest()->getParam('source');
            $clientSecretResponse = $this->getRequest()->getParam('client_secret');
            $payment = $order->getPayment();
            $quoteSourceId = $payment->getAdditionalInformation('stripe_source_id');
            $quoteClientSecret = $payment->getAdditionalInformation('stripe_client_secret');
            if(($quoteSourceId != $sourceId)||($quoteClientSecret != $clientSecretResponse)){
                throw new StripePaymentException(
                    __("Payment source validation fail")
                );
            }
            $source = Stripe\Source::retrieve($sourceId);
            $this->_debug($source->getLastResponse()->json);
            $clientSecret = $source->client_secret;
            if ($clientSecret != $clientSecretResponse) {
                throw new StripePaymentException(
                    __("Payment source validation fail")
                );
            }
            //dont process capture ~~> handle by webhook
            if ($source->status == 'chargeable') {
                /** @var \Magento\Customer\Model\Session $customerSession */
//                if ($order->canInvoice()) {
//                    $invoice = $this->_objectManager->create('Magento\Sales\Model\Service\InvoiceService')->prepareInvoice($order);
//                    if (!$invoice->getTotalQty()) {
//                        throw new \Magento\Framework\Exception\LocalizedException(
//                            __('You can\'t create an invoice without products.')
//                        );
//                    }
//                    $invoice->setRequestedCaptureCase(\Magento\Sales\Model\Order\Invoice::CAPTURE_ONLINE);
//                    $invoice->register();
//                    $invoice->getOrder()->setCustomerNoteNotify(!empty($data['send_email']));
//                    $invoice->getOrder()->setIsInProcess(true);
//                    $transaction = $this->_objectManager->create('Magento\Framework\DB\Transaction')
//                        ->addObject($invoice)
//                        ->addObject($invoice->getOrder());
//                    $transaction->save();
//                    $this->invoiceSender->send($invoice);
//                }
                return $this->_redirect('checkout/onepage/success');
            }

            if ($source->status == 'pending') {
                //Payment pending
                $referenceNumber = $payment->getAdditionalInformation("stripe_multibanco_reference");
                $entityNumber = $payment->getAdditionalInformation("stripe_multibanco_entity");
                $this->messageManager->addWarningMessage("Payment Pending");
                $this->messageManager->addNoticeMessage("To complete payment, you will need to transfer of funds from your bank account using these reference and entity numbers");
                $this->messageManager->addNoticeMessage("Reference Number: ".(string)$referenceNumber." Entity Number: ".(string)$entityNumber);
                return $this->_redirect('checkout/onepage/success');
            }

            if ($source->status == 'consumed') {
                return $this->_redirect('checkout/onepage/success');
            }

            if ($source->status == 'failed') {
                throw new StripePaymentException(
                    __("Payment failed")
                );
            }
        }
        catch (Stripe\Error\Base $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            $this->_debug($e->getMessage());
            return $this->_redirect('checkout/cart');
        }
        catch (StripePaymentException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            $this->_debug($e->getMessage());
            return $this->_redirect('checkout/cart');
        }
        catch (\Exception $e) {
            $this->messageManager->addErrorMessage("Payment Exception");
            $this->_debug($e->getMessage());
            return $this->_redirect('checkout/cart');
        }
        return $this->_redirect('checkout/cart');
    }
}