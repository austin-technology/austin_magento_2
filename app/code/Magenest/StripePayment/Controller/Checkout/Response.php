<?php
/**
 * Created by PhpStorm.
 * User: hiennq
 * Date: 26/12/2017
 * Time: 18:44
 */

namespace Magenest\StripePayment\Controller\Checkout;

use Magenest\StripePayment\Exception\StripePaymentException;
use Magenest\StripePayment\Helper\Constant;
use Magento\Framework\App\Action\Context;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\Exception\LocalizedException;
use Magento\Quote\Model\GuestCart\GuestCartManagement;
use Magento\Sales\Model\Order\Email\Sender\OrderSender;
use Stripe;

class Response extends \Magento\Framework\App\Action\Action
{
    protected $_checkoutSession;
    protected $_chargeFactory;
    protected $invoiceSender;
    protected $transactionFactory;
    protected $jsonFactory;
    protected $stripeConfig;
    protected $storeManagerInterface;
    protected $stripeLogger;
    protected $orderSender;
    protected $stripeHelper;

    /**
     * @var \Magento\Quote\Model\QuoteManagement
     */
    protected $quoteManagement;

    public function __construct(
        Context $context,
        CheckoutSession $session,
        \Magenest\StripePayment\Model\ChargeFactory $chargeFactory,
        \Magento\Sales\Model\Order\Email\Sender\InvoiceSender $invoiceSender,
        \Magento\Framework\DB\TransactionFactory $transactionFactory,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magenest\StripePayment\Helper\Config $stripeConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManagerInterface,
        \Magenest\StripePayment\Helper\Logger $stripeLogger,
        OrderSender $orderSender,
        \Magento\Quote\Model\QuoteManagement $quoteManagement,
        \Magenest\StripePayment\Helper\Data $stripeHelper
    ) {
        parent::__construct($context);
        $this->_checkoutSession = $session;
        $this->_chargeFactory = $chargeFactory;
        $this->invoiceSender = $invoiceSender;
        $this->transactionFactory = $transactionFactory;
        $this->jsonFactory = $resultJsonFactory;
        $this->stripeConfig = $stripeConfig;
        $this->storeManagerInterface = $storeManagerInterface;
        $this->stripeLogger = $stripeLogger;
        $this->orderSender = $orderSender;
        $this->quoteManagement = $quoteManagement;
        $this->stripeHelper = $stripeHelper;
        Stripe\Stripe::setApiKey($this->stripeConfig->getSecretKey());
    }

    public function execute()
    {
        try {
            $this->_debug("Processing payment response");
            $quote = $this->_checkoutSession->getQuote();
            $response = $this->getRequest()->getParams();
            $this->_debug($response);
            $sourceId = $this->getRequest()->getParam('source');
            $clientSecretResponse = $this->getRequest()->getParam('client_secret');
            $payment = $quote->getPayment();
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
            if ($source->status == 'chargeable') {
                /** @var \Magento\Customer\Model\Session $customerSession */
                $customerSession = $this->_objectManager->create('Magento\Customer\Model\Session');
                if(!$customerSession->isLoggedIn()){
                    $quote->setCheckoutMethod(\Magento\Quote\Model\QuoteManagement::METHOD_GUEST);
                }
                $this->setSourceAdditionalInformation($source, $payment);
                $order = $this->quoteManagement->placeOrder($quote->getId(), $payment);
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
        catch (LocalizedException $e) {
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

    /**
     * @param Stripe\StripeObject $source
     * @param \Magento\Quote\Model\Quote\Payment $payment
     */
    protected function setSourceAdditionalInformation($source, $payment){}

    /**
     * @param array|string $debugData
     */
    protected function _debug($debugData)
    {
        $this->stripeLogger->debug(var_export($debugData, true));
    }
}