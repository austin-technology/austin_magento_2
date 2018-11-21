<?php
/**
 * Created by PhpStorm.
 * User: hiennq
 * Date: 26/12/2017
 * Time: 17:59
 */

namespace Magenest\StripePayment\Model;

use Magenest\StripePayment\Helper\Constant;
use Magento\Payment\Model\Method\AbstractMethod;
use Magento\Framework\Exception\LocalizedException;
use Stripe;

class Multibanco extends AbstractMethod
{
    const CODE = 'magenest_stripe_multibanco';
    protected $_code = self::CODE;

    protected $_isGateway = true;
    protected $_canAuthorize = false;
    protected $_canCapture = true;
    protected $_canCapturePartial = false;
    protected $_canCaptureOnce = true;
    protected $_canVoid = false;
    protected $_canUseInternal = false;
    protected $_canUseCheckout = true;
    protected $_canRefund = true;
    protected $_canRefundInvoicePartial = true;
    protected $_isInitializeNeeded = true;
    protected $_canOrder = false;
    protected $_infoBlockType = \Magenest\StripePayment\Block\Info\Multibanco::class;
    protected $_messageManager;
    protected $stripeHelper;
    protected $stripeLogger;
    protected $stripeConfig;
    protected $request;
    protected $storeManager;

    public function __construct(
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magenest\StripePayment\Helper\Config $stripeConfig,
        \Magenest\StripePayment\Helper\Data $stripeHelper,
        \Magenest\StripePayment\Helper\Logger $stripeLogger,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Store\Model\StoreManagerInterface $storeManagerInterface,
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory,
        \Magento\Framework\Api\AttributeValueFactory $customAttributeFactory,
        \Magento\Payment\Helper\Data $paymentData,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Payment\Model\Method\Logger $logger,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->_messageManager = $messageManager;
        $this->stripeHelper = $stripeHelper;
        $this->stripeLogger = $stripeLogger;
        $this->stripeConfig = $stripeConfig;
        $this->request = $request;
        $this->storeManager = $storeManagerInterface;
        parent::__construct(
            $context,
            $registry,
            $extensionFactory,
            $customAttributeFactory,
            $paymentData,
            $scopeConfig,
            $logger,
            $resource,
            $resourceCollection,
            $data
        );
    }

    public function getConfigPaymentAction()
    {
        return "authorize_capture";
    }

    public function initialize($paymentAction, $stateObject)
    {
        /**
         * @var \Magento\Sales\Model\Order $order
         */
        try{
            Stripe\Stripe::setApiKey($this->stripeConfig->getSecretKey());
            $payment = $this->getInfoInstance();
            $order = $payment->getOrder();
            $grandTotal = $order->getBaseGrandTotal();
            $baseCurrency = strtolower($order->getBaseCurrencyCode());
            if (!$this->stripeHelper->isZeroDecimal($baseCurrency)) {
                $grandTotal = $grandTotal*100;
            }
            $billingAddress = $order->getBillingAddress();
            $returnUrl = $this->storeManager->getStore()->getBaseUrl() . "stripe/checkout_multibanco/response";
            $request = [
                "type" => "multibanco",
                "amount" => round($grandTotal),
                "currency" => $baseCurrency,
                "redirect" => [
                    "return_url" => $returnUrl
                ],
                'owner'=>[
                    'name' => $billingAddress->getName(),
                    'email' => $billingAddress->getEmail(),
                    'phone' => $billingAddress->getTelephone(),
                    'address' => [
                        'city' => $billingAddress->getCity(),
                        'country' => $billingAddress->getCountryId(),
                        'line1' => $billingAddress->getStreetLine(1),
                        'line2' => $billingAddress->getStreetLine(2),
                        'postal_code' => $billingAddress->getPostcode(),
                        'state' => $billingAddress->getRegion()
                    ]
                ]
            ];
            $source = Stripe\Source::create($request);
            $this->_debug($source->getLastResponse()->json);
            $redirectUrl = $source->redirect->url;
            $sourceId = $source->id;
            $clientSecret = $source->client_secret;
            $reference = $source->multibanco->reference;
            $entity = $source->multibanco->entity;
            $sourceAdditionalInformation[] = [
                'label' => "Payment Method",
                'value' => "Multibanco"
            ];
            $order->setCanSendNewEmailFlag(false);
            $payment->setAdditionalInformation("stripe_source_additional_information", json_encode($sourceAdditionalInformation));
            $payment->setAdditionalInformation("stripe_multibanco_reference", $reference);
            $payment->setAdditionalInformation("stripe_multibanco_entity", $entity);
            $payment->setAdditionalInformation("stripe_client_secret", $clientSecret);
            $payment->setAdditionalInformation("stripe_source_id", $sourceId);
            $payment->setAdditionalInformation("stripe_redirect_url", $redirectUrl);
            return parent::initialize($paymentAction, $stateObject);
        }catch (\Stripe\Error\Base $e){
            throw new LocalizedException(__($e->getMessage()));
        }
    }

    /**
     * @param \Magento\Payment\Model\InfoInterface|\Magento\Sales\Model\Order\Payment $payment
     * @param float $amount
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function capture(\Magento\Payment\Model\InfoInterface $payment, $amount)
    {
        try{
            Stripe\Stripe::setApiKey($this->stripeConfig->getSecretKey());
            $order = $payment->getOrder();
            $sourceId = $payment->getAdditionalInformation("stripe_source_id");
            $chargeRequest = $this->stripeHelper->createChargeRequest($order, $amount, $sourceId);
            $charge = Stripe\Charge::create($chargeRequest);
            $this->_debug($charge->getLastResponse()->json);
            $chargeId = $charge->id;
            $payment->setAdditionalInformation("stripe_charge_id", $chargeId);
            $chargeStatus = $charge->status;
            if($chargeStatus == 'succeeded'){
                $transactionId = $charge->balance_transaction;
                $payment->setTransactionId($transactionId)
                    ->setLastTransId($transactionId);
                $payment->setIsTransactionClosed(1);
                $payment->setShouldCloseParentTransaction(1);
            }else{
                throw new LocalizedException(
                    __("Payment failed")
                );
            }
            return parent::capture($payment, $amount);
        }catch (\Stripe\Error\Base $e){
            throw new LocalizedException(__($e->getMessage()));
        }
    }

    /**
     * @param \Magento\Payment\Model\InfoInterface|\Magento\Sales\Model\Order\Payment $payment
     * @param float $amount
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function refund(\Magento\Payment\Model\InfoInterface $payment, $amount)
    {
        try {
            Stripe\Stripe::setApiKey($this->stripeConfig->getSecretKey());
            $chargeId = $payment->getAdditionalInformation("stripe_charge_id");
            $refundReason = $this->request->getParam('refund_reason');
            $request = $this->stripeHelper->createRefundRequest($payment, $chargeId, $amount);
            if ($refundReason) {
                $request['reason'] = $refundReason;
            }
            $refund = Stripe\Refund::create($request);
            $this->_debug($refund->getLastResponse()->json);
            $transactionId = $refund->balance_transaction;
            if ($transactionId) {
                $payment->setTransactionId($transactionId);
            }
            $payment->setShouldCloseParentTransaction(0);
            return parent::refund($payment, $amount);
        }catch (\Stripe\Error\Base $e){
            throw new LocalizedException(__($e->getMessage()));
        }
    }

    /**
     * @param array|string $debugData
     */
    protected function _debug($debugData)
    {
        $this->stripeLogger->debug(var_export($debugData, true));
    }

    public function canUseForCurrency($currencyCode)
    {
        if (!in_array(strtolower($currencyCode), $this->getAcceptedCurrencyCodes())) {
            return false;
        }
        return true;
    }

    private function getAcceptedCurrencyCodes()
    {
        return ['eur'];
    }
}
