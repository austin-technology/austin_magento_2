<?php
/**
 * Created by Magenest.
 * Author: Pham Quang Hau
 * Date: 20/05/2016
 * Time: 12:01
 */

namespace Magenest\StripePayment\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Framework\HTTP\ZendClientFactory;
use Magento\Framework\Encryption\EncryptorInterface;
use Magenest\StripePayment\Model\CustomerFactory;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    protected $_encryptor;

    protected $_httpClientFactory;

    protected $_customerFactory;

    protected $_config;

    protected $_cardFactory;

    protected $_chargeFactory;

    protected $stripeLogger;

    protected $customerSession;

    public function __construct(
        Context $context,
        EncryptorInterface $encryptorInterface,
        ZendClientFactory $clientFactory,
        CustomerFactory $customerFactory,
        Config $config,
        \Magenest\StripePayment\Model\CardFactory $cardFactory,
        \Magenest\StripePayment\Model\ChargeFactory $chargeFactory,
        \Magenest\StripePayment\Helper\Logger $stripeLogger,
        \Magento\Customer\Model\Session $customerSession
    ) {
        $this->_encryptor = $encryptorInterface;
        $this->_httpClientFactory = $clientFactory;
        $this->_customerFactory = $customerFactory;
        $this->_config = $config;
        parent::__construct($context);
        $this->_cardFactory = $cardFactory;
        $this->_chargeFactory = $chargeFactory;
        $this->stripeLogger = $stripeLogger;
        $this->customerSession = $customerSession;
    }

    /**
     * @param string $url
     * @param array $requestPost
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function sendRequestDelete($url, $requestPost = null)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $key = $this->_config->getSecretKey();
        $httpHeaders = new \Zend\Http\Headers();
        $httpHeaders->addHeaders([
            'Authorization' => 'Bearer ' . $key,
        ]);
        $request = new \Zend\Http\Request();
        $request->setHeaders($httpHeaders);
        $request->setUri($url);
        $request->setMethod(\Zend\Http\Request::METHOD_DELETE);

        if (!!$requestPost) {
            $request->getPost()->fromArray($requestPost);
        }

        $client = new \Zend\Http\Client();
        $options = [
            'adapter' => 'Zend\Http\Client\Adapter\Curl',
            'curloptions' => [CURLOPT_FOLLOWLOCATION => true],
            'maxredirects' => 0,
            'timeout' => 30
        ];
        $client->setOptions($options);
        try {
            $response = $client->send($request);
            $responseBody = $response->getBody();
            $responseBody = (array)json_decode($responseBody);

            return $responseBody;
        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('Cannot send request to Stripe servers.')
            );
        }
    }

    public function sendRequest($requestPost, $url, $requestMethod = null)
    {
        if (!$requestMethod) {
            $requestMethod="post";
        }
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $key = $this->_config->getSecretKey();
        $httpHeaders = new \Zend\Http\Headers();
        $httpHeaders->addHeaders([
            'Authorization' => 'Bearer ' . $key,
        ]);
        $request = new \Zend\Http\Request();
        $request->setHeaders($httpHeaders);
        $request->setUri($url);
        $request->setMethod(strtoupper($requestMethod));

        if (!!$requestPost) {
            $request->getPost()->fromArray($requestPost);
        }

        $client = new \Zend\Http\Client();
        $options = [
            'adapter' => 'Zend\Http\Client\Adapter\Curl',
            'curloptions' => [CURLOPT_FOLLOWLOCATION => true],
            'maxredirects' => 0,
            'timeout' => 30
        ];
        $client->setOptions($options);
        try {
            $response = $client->send($request);
            $responseBody = $response->getBody();
            $responseBody = json_decode($responseBody, true);

            return $responseBody;
        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('Cannot send request to Stripe servers.')
            );
        }
    }

    public function checkStripeCustomerId($cusId)
    {
        $url = 'https://api.stripe.com/v1/customers/' . $cusId;
        $request = $this->sendRequest([], $url, null);
        if (isset($request['error'])) {
            return false;
        }
        return true;
    }

    public function isZeroDecimal($currency)
    {
        return in_array(strtolower($currency), [
            'bif',
            'djf',
            'jpy',
            'krw',
            'pyg',
            'vnd',
            'xaf',
            'xpf',
            'clp',
            'gnf',
            'kmf',
            'mga',
            'rwf',
            'vuv',
            'xof'
        ]);
    }

    /**
     * @param string $customerId
     * @param $stripeResponse
     */
    public function saveCard($customerId, $stripeResponse)
    {
        try {
            $cardData = isset($stripeResponse['card'])?$stripeResponse['card']:[];
            $expMonth = isset($cardData['exp_month'])?$cardData['exp_month']:"";
            $expYear = isset($cardData['exp_year'])?$cardData['exp_year']:"";
            $brand = isset($cardData['brand'])?$cardData['brand']:"";
            $cardLast4 = isset($cardData['last4'])?$cardData['last4']:"";
            $sourceId = isset($stripeResponse['id'])?$stripeResponse['id']:"";
            $threeDSecureStatus = isset($cardData['three_d_secure'])?$cardData['three_d_secure']:"";
            $cardModel = $this->_cardFactory->create();
            $data = [
                'magento_customer_id' => $customerId,
                'card_id' => $sourceId,
                'brand' => $brand,
                'last4' => (string)$cardLast4,
                'exp_month' => (string)$expMonth,
                'exp_year' => (string)$expYear,
                'status' => "active",
                'three_d_secure' => $threeDSecureStatus
            ];

            $stripeCustomerId = $this->getStripeCustomerId();
            if ($stripeCustomerId) {
                if (!$this->checkStripeCustomerId($stripeCustomerId)) {
                    $this->deleteStripeCustomerId($stripeCustomerId);
                    $stripeCustomerId = $this->createCustomer($sourceId);
                } else {
                    $res = $this->addSourceToCustomer($stripeCustomerId, $sourceId);
                }
            } else {
                $stripeCustomerId = $this->createCustomer($sourceId);
            }

            if ($stripeCustomerId) {
                $cardModel->addData($data)->save();
            }
            return $stripeCustomerId;
        } catch (\Exception $e) {
            $this->stripeLogger->critical("save card exception". $e->getMessage());
            return false;
        }
    }

    public function saveCardBeforePayment($customerId, $payment)
    {
        try {
//            $cardData = isset($stripeResponse['card'])?year$stripeResponse['card']:[];
            $expMonth = $payment->getData('cc_exp_month');
            $expYear = $payment->getData('cc_exp_month');
            $brand = $payment->getData('cc_type');
            $cardLast4 = $payment->getData('cc_last_4');
            $sourceId = $payment->getAdditionalInformation()['source_id'];
            $threeDSecureStatus = $payment->getAdditionalInformation()['three_d_secure'];
            $cardModel = $this->_cardFactory->create();
            $data = [
                'magento_customer_id' => $customerId,
                'card_id' => $sourceId,
                'brand' => $brand,
                'last4' => (string)$cardLast4,
                'exp_month' => (string)$expMonth,
                'exp_year' => (string)$expYear,
                'status' => "active",
                'three_d_secure' => $threeDSecureStatus
            ];

            $stripeCustomerId = $this->getStripeCustomerId();
            if ($stripeCustomerId) {
                if (!$this->checkStripeCustomerId($stripeCustomerId)) {
                    $this->deleteStripeCustomerId($stripeCustomerId);
                    $stripeCustomerId = $this->createCustomer($sourceId);
                } else {
                    $res = $this->addSourceToCustomer($stripeCustomerId, $sourceId);
                }
            } else {
                $stripeCustomerId = $this->createCustomer($sourceId);
            }

            if ($stripeCustomerId) {
                $cardModel->addData($data)->save();
            }
            return $stripeCustomerId;
        } catch (\Exception $e) {
            $this->stripeLogger->critical("save card exception". $e->getMessage());
            return false;
        }

    }

    public function deleteCard($customerId, $cardId)
    {

        $url = "https://api.stripe.com/v1/customers/".$customerId."/sources/" . $cardId;
        return $this->sendRequest([], $url, "delete");
    }

    /**
     * @param \Magento\Sales\Model\Order $order
     * @param $response
     */
    public function saveCharge($order, $response, $status)
    {
        $chargeModel  = $this->_chargeFactory->create();
        $data = [
            'charge_id' => @$response['id'],
            'order_id' => $order->getIncrementId(),
            'customer_id' => $order->getCustomerId(),
            'status' => $status
        ];

        $chargeModel->addData($data)->save();
    }

    /**
     * Create a stripe customer object
     *
     * @param \Magento\Payment\Model\InfoInterface $payment
     * @param $token
     * @return mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function createCustomer($source = null)
    {
        try {
            $customerModel = $this->_customerFactory->create();

            $url = 'https://api.stripe.com/v1/customers';

            $request = [
                "description" => $this->customerSession->getCustomer()->getEmail(),
                "email" => $this->customerSession->getCustomer()->getEmail()
            ];
            if ($source) {
                $request['source'] = $source;
            }

            $customer = $this->sendRequest($request, $url, null);
            $customerModel->addData([
                'magento_customer_id' => $this->customerSession->getCustomerId(),
                'stripe_customer_id' => $customer['id']
            ]);
            $customerModel->save();
            return $customer['id'];
        } catch (\Exception $e) {
            $this->stripeLogger->critical("create customer fail");
            return false;
        }
    }

    public function addSourceToCustomer($stripeCustomerId, $source)
    {
        $request = [
            'source' => $source
        ];
        $url = 'https://api.stripe.com/v1/customers/' . $stripeCustomerId . '/sources';
        $response = $this->sendRequest($request, $url, 'post');
    }

    public function getStripeCustomerId($magentoCustomerId = false)
    {
        if ($magentoCustomerId) {
            $customerId = $magentoCustomerId;
        } else {
            $customerId = $this->customerSession->getCustomerId();
        }
        $customer = $this->_customerFactory->create()->getCollection()
            ->addFieldToFilter('magento_customer_id', $customerId)
            ->getFirstItem();
        return $customer->getData('stripe_customer_id');
    }

    public function deleteStripeCustomerId($stripeCustomerId, $isOnline = false)
    {
        $customer = $this->_customerFactory->create()->getCollection()
            ->addFieldToFilter('stripe_customer_id', $stripeCustomerId)
            ->getFirstItem();
        return $customer->delete();
    }


    /**
     * @param \Magento\Sales\Model\Order $order
     * @param string $paymentToken
     * @param bool $isCapture
     */
    public function createChargeRequest($order, $amount, $paymentToken, $isCapture = true, $dbSource = false, $_stripeCustomerId = false)
    {
        $multiply = 100;
        if ($this->isZeroDecimal($order->getBaseCurrencyCode())) {
            $multiply = 1;
        }
        $amount = $amount * $multiply;
        $request = [
            "amount" => round($amount),
            "currency" => $order->getBaseCurrencyCode(),
            'capture' => $isCapture?'true':'false',
            "source" => $paymentToken,
            "metadata" => [
                'Order Id' => $order->getIncrementId(),
                'Customer Email' => $order->getCustomerEmail()
            ]
        ];
        if($_stripeCustomerId){
            $request['customer'] = $_stripeCustomerId;
        }
        if ($dbSource) {
            if ($_stripeCustomerId) {
                $stripeCustomer = $_stripeCustomerId;
            } else {
                $stripeCustomer = $this->getStripeCustomerId();
            }
            if ($stripeCustomer) {
                $request['customer'] = $stripeCustomer;
            }
        }
        if ($this->_config->sendMailCustomer()) {
            $request['receipt_email'] = $order->getCustomerEmail();
        }
        return $request;
    }

    /**
     * @param \Magento\Sales\Model\Order $order
     */
    public function createCaptureRequest($order, $amount)
    {
        $multiply = 100;
        if ($this->isZeroDecimal($order->getBaseCurrencyCode())) {
            $multiply = 1;
        }
        $amount = $amount * $multiply;
        $request = [
            "amount" => round($amount),
        ];
        if ($this->_config->sendMailCustomer()) {
            $request['receipt_email'] = $order->getCustomerEmail();
        }
        return $request;
    }

    /**
     * @var \Magento\Sales\Model\Order $order
     */
    public function getDirectSource($order)
    {
        /** @var \Magento\Sales\Model\Order\Address $billing */
        $payment = $order->getPayment();
        $sourceId = $payment->getAdditionalInformation("source_id");
        if ($sourceId) {
            return $sourceId;
        }
        $billing = $order->getBillingAddress();
        $source = [
            'exp_month' => $order->getPayment()->getCcExpMonth(),
            'exp_year' => $order->getPayment()->getCcExpYear(),
            'number' => $order->getPayment()->getCcNumber(),
            'cvc' => $order->getPayment()->getCcCid(),
            'object' => 'card',
            'name' => $billing->getName(),
            'address_line1' => $billing->getStreetLine(1),
            'address_line2' => $billing->getStreetLine(2),
            'address_city' => $billing->getCity(),
            'address_zip' => $billing->getCity(),
            'address_state' => $billing->getRegion(),
            'address_country' => $billing->getCountryId()
        ];
        return $source;
    }

    public function getSaveCard($customerId)
    {
        $col = $this->_cardFactory->create()->getCollection();
        $col->addFieldToFilter("magento_customer_id", $customerId);
        return $col;
    }

    public function createRefundRequest($payment, $chargeId, $amount = null)
    {
        $multiply = 100;
        if ($this->isZeroDecimal($payment->getOrder()->getBaseCurrencyCode())) {
            $multiply = 1;
        }
        if($amount) {
            $amount = $amount * $multiply;
            $request = [
                "charge" => $chargeId,
                "amount" => round($amount),
            ];
        }else{
            $request = [
                "charge" => $chargeId
            ];
        }

        return $request;
    }
}
