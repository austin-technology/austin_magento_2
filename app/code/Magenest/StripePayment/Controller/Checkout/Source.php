<?php
/**
 * Created by PhpStorm.
 * User: hiennq
 * Date: 27/12/2017
 * Time: 11:32
 */

namespace Magenest\StripePayment\Controller\Checkout;

use Magenest\StripePayment\Exception\StripePaymentException;
use Stripe;
use Magenest\StripePayment\Helper\Constant;
use Magento\Framework\App\Action\Context;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\Controller\ResultFactory;

abstract class Source extends \Magento\Framework\App\Action\Action
{
    protected $_checkoutSession;
    protected $stripeConfig;
    protected $storeManagerInterface;
    protected $stripeLogger;
    protected $_formKeyValidator;
    protected $stripeHelper;
    protected $customerSession;

    public function __construct(
        Context $context,
        CheckoutSession $session,
        \Magenest\StripePayment\Helper\Config $stripeConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManagerInterface,
        \Magenest\StripePayment\Helper\Logger $stripeLogger,
        \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator,
        \Magenest\StripePayment\Helper\Data $stripeHelper,
        \Magento\Customer\Model\Session $customerSession
    ) {
        parent::__construct($context);
        $this->_checkoutSession = $session;
        $this->stripeConfig = $stripeConfig;
        $this->storeManagerInterface = $storeManagerInterface;
        $this->stripeLogger = $stripeLogger;
        $this->_formKeyValidator = $formKeyValidator;
        $this->stripeHelper = $stripeHelper;
        $this->customerSession = $customerSession;
        Stripe\Stripe::setApiKey($this->stripeConfig->getSecretKey());
    }

    public function execute()
    {
        $this->_debug("Creating source");
        $result = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        try {
            if (!$this->_formKeyValidator->validate($this->getRequest())) {
                throw new StripePaymentException(
                    __("Invalid form key")
                );
            }
            $quote = $this->_checkoutSession->getQuote();
            $request = $this->getPostRequest();
            $source = Stripe\Source::create($request);
            $this->_debug($source->getLastResponse()->json);
            $redirectUrl = $source->redirect->url;
            $sourceId = $source->id;
            $clientSecret = $source->client_secret;
            $payment = $quote->getPayment();
            $payment->setAdditionalInformation("stripe_client_secret", $clientSecret);
            $payment->setAdditionalInformation("stripe_source_id", $sourceId);
            $quote->save();

            $data = [
                'success' => true,
                'error' => false,
                'redirect_url' => $redirectUrl
            ];
            $result->setData($data);
        }
        catch (Stripe\Error\Base $e) {
            $result->setData([
                'error' => true,
                'success' => false,
                'message' => $e->getMessage()
            ]);
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
    protected function getPostRequest(){
        $quote = $this->_checkoutSession->getQuote();
        $billingAddress = $quote->getBillingAddress();
        $quote = $this->_checkoutSession->getQuote();
        $grandTotal = $quote->getBaseGrandTotal();
        $baseCurrency = strtolower($quote->getBaseCurrencyCode());
        if (!$this->stripeHelper->isZeroDecimal($baseCurrency)) {
            $grandTotal = $grandTotal*100;
        }
        $request = [
            "type" => $this->getSourceType(),
            "amount" => round($grandTotal),
            "currency" => $baseCurrency,
            "redirect" => [
                "return_url" => $this->getReturnUrl()
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
        $request = array_merge($request, $this->getCustomRequest());
        $this->_debug($request);
        return $request;
    }

    abstract protected function getReturnUrl();
    abstract protected function getSourceType();

    /**
     * @return array
     */
    protected function getCustomRequest(){
        return [];
    }

    /**
     * @param array|string $debugData
     */
    protected function _debug($debugData)
    {
        $this->stripeLogger->debug(var_export($debugData, true));
    }
}