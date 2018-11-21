<?php
/**
 * Created by PhpStorm.
 * User: magenest
 * Date: 30/05/2017
 * Time: 20:43
 */

namespace Magenest\StripePayment\Controller\Checkout;

use Magenest\StripePayment\Helper\Constant;
use Magento\Framework\App\Action\Context;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Sales\Model\Order\Email\Sender\OrderSender;

class ThreedSecureResponse extends \Magento\Framework\App\Action\Action
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
        $this->stripeHelper = $stripeHelper;
    }

    public function execute()
    {
        try {
            $response = $this->getRequest()->getParams();
            $this->_debug($response);
            $cardSource = $response['source'];
            $order = $this->_checkoutSession->getLastRealOrder();
            /** @var \Magento\Sales\Model\Order\Payment $payment */
            $payment = $order->getPayment();
            $clientSecret = $payment->getAdditionalInformation("client_secret");
            $clientSecretConfirm = $response['client_secret'];
            if ($clientSecret != $clientSecretConfirm) {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __('3d secure validate fail')
                );
            }
            $url = Constant::SOURCE_ENDPOINT ."/". $cardSource;
            $sourceResponse = $this->stripeHelper->sendRequest(null, $url, null);
            $this->_debug($sourceResponse);
            if ($sourceResponse['status'] == 'failed') {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __('3d secure authenticate fail')
                );
            }
            $payment->setAdditionalInformation('payment_token', $cardSource);

            $payAction = $payment->getAdditionalInformation(Constant::ADDITIONAL_PAYMENT_ACTION);
            $totalDue = $order->getTotalDue();
            $baseTotalDue = $order->getBaseTotalDue();
            if ($payAction == 'authorize') {
                $payment->authorize(true, $baseTotalDue);
                // base amount will be set inside
                $payment->setAmountAuthorized($totalDue);
            }
            if ($payAction == 'authorize_capture') {
                $payment->setAmountAuthorized($totalDue);
                $payment->setBaseAmountAuthorized($baseTotalDue);
                $payment->capture(null);
            }
            //place order success
            if ($order->getCanSendNewEmailFlag()) {
                try {
                    $this->orderSender->send($order);
                } catch (\Exception $e) {
                    $this->stripeLogger->critical($e->getMessage());
                }
            }
            $order->setState(\Magento\Sales\Model\Order::STATE_PROCESSING);
            $order->save();
            $this->_redirect('checkout/onepage/success');
        } catch (\Exception $e) {
            $order = $this->_checkoutSession->getLastRealOrder();
            $order->cancel();
            $order->setState(\Magento\Sales\Model\Order::STATE_CANCELED);
            $order->setStatus(\Magento\Sales\Model\Order::STATE_CANCELED);
            $order->addStatusHistoryComment("Exception payment");
            $order->save();
            $this->_checkoutSession->restoreQuote();
            $payment = $order->getPayment();
            $payment->setStatus('Payment EXCEPTION');
            $payment
                ->setShouldCloseParentTransaction(1)
                ->setIsTransactionClosed(1);
            $this->stripeLogger->critical($e->getMessage());
            $this->messageManager->addErrorMessage($e->getMessage());
            $this->_redirect('checkout/cart');
        }
    }

    /**
     * @param array|string $debugData
     */
    private function _debug($debugData)
    {
        $this->stripeLogger->debug(var_export($debugData, true));
    }
}
