<?php
/**
 * Created by Magenest.
 * Author: Pham Quang Hau
 * Date: 11/05/2016
 * Time: 13:33
 */
namespace Magenest\StripePayment\Model;

use Magenest\StripePayment\Helper\Constant;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Magento\Framework\Api\ExtensionAttributesFactory;
use Magento\Framework\Api\AttributeValueFactory;
use Magento\Payment\Helper\Data;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Payment\Model\Method\Logger;
use Magento\Framework\Module\ModuleListInterface;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magenest\StripePayment\Helper\Data as DataHelper;

class StripeDirectApi extends \Magento\Payment\Model\Method\Cc
{
    const CODE = 'magenest_stripe';
    protected $_code = self::CODE;
    protected $_isGateway = true;
    protected $_canCapture = true;
    protected $_canCapturePartial = true;
    protected $_canRefund = true;
    protected $_canRefundInvoicePartial = true;
    protected $_canVoid = true;
    protected $_canAuthorize = true;

    protected $_helper;
    public $stripeLogger;
    protected $stripeCard;
    protected $_messageManager;

    public function __construct(
        Context $context,
        Registry $registry,
        ExtensionAttributesFactory $extensionFactory,
        AttributeValueFactory $customAttributeFactory,
        Data $paymentData,
        ScopeConfigInterface $scopeConfig,
        Logger $logger,
        ModuleListInterface $moduleList,
        TimezoneInterface $localeDate,
        DataHelper $dataHelper,
        \Magenest\StripePayment\Helper\Logger $stripeLogger,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magenest\StripePayment\Model\StripePaymentMethod $stripePaymentMethod,
        $data = []
    ) {
        $this->_helper = $dataHelper;
        $this->stripeLogger = $stripeLogger;
        $this->_messageManager = $messageManager;
        $this->stripeCard = $stripePaymentMethod;

        parent::__construct(
            $context,
            $registry,
            $extensionFactory,
            $customAttributeFactory,
            $paymentData,
            $scopeConfig,
            $logger,
            $moduleList,
            $localeDate,
            null,
            null,
            $data
        );
    }

    public function assignData(\Magento\Framework\DataObject $data)
    {
        $infoInstance = $this->getInfoInstance();
        $additionalData = $data->getData('additional_data');
        parent::assignData($data);
        if ($this->_appState->getAreaCode() == 'adminhtml') {
            $sourceId = isset($additionalData['source_id'])?$additionalData['source_id']:"";
            $customerId = isset($additionalData['customer_id'])?$additionalData['customer_id']:"";
            $infoInstance->setAdditionalInformation('source_id', $sourceId);
            $infoInstance->setAdditionalInformation('customer_id', $customerId);
            if ($sourceId) {
                $infoInstance->setAdditionalInformation('db_source', true);
            }
            return $this;
        }

        return $this;
    }

    public function authorize(\Magento\Payment\Model\InfoInterface $payment, $amount)
    {
        $this->_debug("authorize action");
        /** @var \Magento\Sales\Model\Order $order */
        $order = $payment->getOrder();
        $paymentToken = $this->_helper->getDirectSource($order);
        $dbSource = $payment->getAdditionalInformation('db_source');
        $customerId = $payment->getAdditionalInformation('customer_id');
        $stripeCustomerId = $this->_helper->getStripeCustomerId($customerId);
        $request = $this->_helper->createChargeRequest($order, $amount, $paymentToken, false, $dbSource, $stripeCustomerId);
        $url = 'https://api.stripe.com/v1/charges';
        $response = $this->_helper->sendRequest($request, $url, null);
        if (isset($response['error'])) {
            $this->_messageManager->addErrorMessage("Message: ".$response['error']['message']);
            throw new \Magento\Framework\Exception\LocalizedException(
                __('Payment error')
            );
        }
        if (isset($response['status']) && ($response['status'] == 'succeeded')) {
            $payment->setAmount($amount);
            $payment->setTransactionId($response['id'])
                ->setIsTransactionClosed(false)
                ->setShouldCloseParentTransaction(false)
                ->setCcTransId($response['id']);

        } else {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('Something went wrong. Please try again later.')
            );
        }
    }

    public function capture(\Magento\Payment\Model\InfoInterface $payment, $amount)
    {
        $this->_debug("capture action");
        /** @var \Magento\Sales\Model\Order $order */
        $order = $payment->getOrder();
        $transId = $payment->getCcTransId();
        if ($transId) {
            $url = Constant::CHARGE_ENDPOINT.'/' . $transId . '/capture';
            $request = $this->_helper->createCaptureRequest($order, $amount);
            $response = $this->_helper->sendRequest($request, $url, null);
            $this->_debug($response);
            if (isset($response['error'])) {
                $this->_messageManager->addErrorMessage("Message: ".$response['error']['message']);
                throw new \Magento\Framework\Exception\LocalizedException(
                    __('Payment error')
                );
            }
            if (isset($response['status'])&&($response['status'] == 'succeeded')) {
                $transactionId = isset($response['balance_transaction'])?$response['balance_transaction']:"";
                $payment->setStatus(\Magento\Payment\Model\Method\AbstractMethod::STATUS_SUCCESS)
                    ->setShouldCloseParentTransaction(1)
                    ->setIsTransactionClosed(1);
                $payment->setTransactionId($transactionId);
            } else {
                throw new \Magenest\StripePayment\Exception\StripePaymentException(
                    __('Capture fail')
                );
            }
        } else {
            $paymentToken = $this->_helper->getDirectSource($order);
            $dbSource = $payment->getAdditionalInformation('db_source');
            $customerId = $payment->getAdditionalInformation('customer_id');
            $stripeCustomerId = $this->_helper->getStripeCustomerId($customerId);
            $request = $this->_helper->createChargeRequest($order, $amount, $paymentToken, true, $dbSource, $stripeCustomerId);
            $url = 'https://api.stripe.com/v1/charges';
            $response = $this->_helper->sendRequest($request, $url, null);
            if (isset($response['error'])) {
                $this->_messageManager->addErrorMessage("Message: ".$response['error']['message']);
                throw new \Magento\Framework\Exception\LocalizedException(
                    __('Payment error')
                );
            }
            if (isset($response['status']) && ($response['status'] == 'succeeded')) {
                $payment->setAmount($amount);
                $payment->setTransactionId($response['id'])
                    ->setIsTransactionClosed(false)
                    ->setShouldCloseParentTransaction(false)
                    ->setCcTransId($response['id']);

            } else {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __('Something went wrong. Please try again later.')
                );
            }
        }
    }

    public function refund(\Magento\Payment\Model\InfoInterface $payment, $amount)
    {
        return $this->stripeCard->refund($payment, $amount);
    }

    public function void(\Magento\Payment\Model\InfoInterface $payment)
    {
        return $this->stripeCard->void($payment);
    }

    public function cancel(\Magento\Payment\Model\InfoInterface $payment)
    {
        return $this->stripeCard->cancel($payment);
    }

    /**
     * @param array|string $debugData
     */
    protected function _debug($debugData)
    {
        $this->stripeLogger->debug(var_export($debugData, true));
    }

    public function validate()
    {
        return \Magento\Payment\Model\Method\AbstractMethod::validate();
    }

    public function canUseInternal()
    {
        return $this->getConfigData('active_moto');
    }

    public function isAvailable(\Magento\Quote\Api\Data\CartInterface $quote = null)
    {
        return \Magento\Payment\Model\Method\AbstractMethod::isAvailable($quote);
    }

    public function hasVerification()
    {
        return true;
    }
}
