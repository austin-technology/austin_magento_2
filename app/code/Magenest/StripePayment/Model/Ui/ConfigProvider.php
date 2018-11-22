<?php
/**
 * Created by Magenest.
 * Author: Pham Quang Hau
 * Date: 26/05/2016
 * Time: 14:57
 */

namespace Magenest\StripePayment\Model\Ui;

use Magenest\StripePayment\Model\Alipay;
use Magenest\StripePayment\Model\Bancontact;
use Magenest\StripePayment\Model\Eps;
use Magenest\StripePayment\Model\GiroPay;
use Magenest\StripePayment\Model\Ideal;
use Magenest\StripePayment\Model\Multibanco;
use Magenest\StripePayment\Model\Przelewy;
use Magenest\StripePayment\Model\Sofort;
use Magenest\StripePayment\Model\StripePaymentMethod;
use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Payment\Helper\Data as PaymentHelper;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Asset\Repository;
use Psr\Log\LoggerInterface;
use Magento\Payment\Model\Config as PaymentConfig;

class ConfigProvider implements ConfigProviderInterface
{
    protected $_helper;

    protected $_cardFactory;

    protected $_customerSession;

    protected $_checkoutSession;

    protected $stripeConfigHelper;

    protected $_urlBuilder;

    protected $idealBank;

    protected $bancontactLanguage;

    protected $sofortLanguage;
    protected $sofortBank;

    protected $assetRepo;
    protected $request;
    protected $urlBuilder;
    protected $logger;

    const CODE = 'magenest_stripe';

    public function __construct(
        PaymentConfig $paymentConfig,
        Repository $assetRepo,
        RequestInterface $request,
        LoggerInterface $logger,
        PaymentHelper $paymentHelper,
        \Magenest\StripePayment\Model\CardFactory $cardFactory,
        \Magenest\StripePayment\Helper\Data $dataHelper,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magenest\StripePayment\Helper\Config $stripeConfigHelper,
        \Magento\Framework\UrlInterface $urlBuilder,
        \Magenest\StripePayment\Model\Source\IdealBank $idealBank,
        \Magenest\StripePayment\Model\Source\BancontactLanguage $bancontactLanguage,
        \Magenest\StripePayment\Model\Source\SofortLanguage $sofortLanguage,
        \Magenest\StripePayment\Model\Source\SofortCountry $sofortBank
    ) {
        $this->_customerSession = $customerSession;
        $this->_checkoutSession = $checkoutSession;
        $this->_helper = $dataHelper;
        $this->_cardFactory = $cardFactory;
        $this->stripeConfigHelper = $stripeConfigHelper;
        $this->_urlBuilder = $urlBuilder;
        $this->idealBank = $idealBank;
        $this->bancontactLanguage = $bancontactLanguage;
        $this->sofortBank = $sofortBank;
        $this->sofortLanguage = $sofortLanguage;
        $this->config = $paymentConfig;
        $this->assetRepo = $assetRepo;
        $this->request = $request;
        $this->logger = $logger;
    }

    public function getConfig()
    {
        $cardData = $this->getDataCard();
        return [
            'payment' => [
                "magenest_stripe_config" => [
                    'publishableKey' => $this->stripeConfigHelper->getPublishableKey(),
                    'isLogin' => $this->_customerSession->isLoggedIn(),
                    'isZeroDecimal' => $this->checkIsZeroDecimal(),
                    'icon' => $this->getIconMethod()
                ],
                "magenest_stripe" => $this->getStripeConfig(),
                "magenest_stripe_iframe" => $this->getStripeCheckoutConfigOption(),
                "magenest_stripe_applepay" => $this->getStripeApplePayConfig(),
                "magenest_stripe_sofort" => $this->getSofortConfig(),
                "magenest_stripe_ideal" => $this->getIdealConfig(),
                "magenest_stripe_bancontact" => $this->getBancontactConfig(),
            ]
        ];
    }

    public function getIconMethod(){
        return [
            StripePaymentMethod::CODE => $this->getViewFileUrl("Magenest_StripePayment::images/card.png"),
            GiroPay::CODE => $this->getViewFileUrl("Magenest_StripePayment::images/giropay.png"),
            Alipay::CODE => $this->getViewFileUrl("Magenest_StripePayment::images/alipay.png"),
            Eps::CODE => $this->getViewFileUrl("Magenest_StripePayment::images/eps.png"),
            Bancontact::CODE => $this->getViewFileUrl("Magenest_StripePayment::images/bancontact.png"),
            Ideal::CODE => $this->getViewFileUrl("Magenest_StripePayment::images/ideal.png"),
            Multibanco::CODE => $this->getViewFileUrl("Magenest_StripePayment::images/multibanco.png"),
            Przelewy::CODE => $this->getViewFileUrl("Magenest_StripePayment::images/p24.png"),
            Sofort::CODE => $this->getViewFileUrl("Magenest_StripePayment::images/sofort.png"),
        ];
    }

    public function getDataCard()
    {
        $objectManager = ObjectManager::getInstance();
        /** @var \Magento\Customer\Model\Session $customerSession */
        $customerSession = $objectManager->create('Magento\Customer\Model\Session');
        if ($customerSession->isLoggedIn()) {
            $customer_id = $customerSession->getCustomerId();
            $model = $this->_cardFactory->create()
                ->getCollection()
                ->addFieldToFilter('magento_customer_id', $customer_id)
                ->addFieldToFilter('status', "active");
            return $model->getData();
        } else {
            return [];
        }
    }

    public function checkIsZeroDecimal()
    {
        $currency = $this->_checkoutSession->getQuote()->getBaseCurrencyCode();
        return $this->_helper->isZeroDecimal($currency) ? '1' : '0';
    }

    public function getStripeConfig(){
        $cardData = $this->getDataCard();
        return [
            'isSave' => $this->stripeConfigHelper->isSave(),
            'saveCards' => json_encode($cardData),
            'hasCard' => count($cardData)>0 ? true:false,
            'instructions' => $this->stripeConfigHelper->getInstructions(),
            'api' => $this->stripeConfigHelper->getApiVersion()
        ];
    }

    public function getStripeCheckoutConfigOption()
    {
        $canCollectShipping = $this->stripeConfigHelper->getCheckoutCanCollectShipping();
        $canCollectBilling = $this->stripeConfigHelper->getCheckoutCanCollectBilling();
        $canCollectZipCode = $this->stripeConfigHelper->getCheckoutCanCollectZip();
        $displayName = $this->stripeConfigHelper->getDisplayName();
        $imageUrl = $this->stripeConfigHelper->getCheckoutImageUrl();
        return [
            'can_collect_billing' => $canCollectBilling,
            'can_collect_shipping' => $canCollectShipping,
            'can_collect_zip' => $canCollectZipCode,
            'display_name' => $displayName,
            'button_label' => $this->stripeConfigHelper->getButtonLabel(),
            'allow_remember' => $this->stripeConfigHelper->getAllowRemember(),
            'accept_bitcoin' => $this->stripeConfigHelper->getCanAcceptBitcoin(),
            'accept_alipay' => $this->stripeConfigHelper->getCanAcceptAlipay(),
            'image_url' => $imageUrl,
            'locale' => $this->stripeConfigHelper->getLocale()
        ];
    }

    public function getStripeApplePayConfig()
    {
        return [
            'replace_placeorder' => $this->stripeConfigHelper->getReplacePlaceOrder(),
            'button_type' => $this->stripeConfigHelper->getButtonType(),
            'button_theme' => $this->stripeConfigHelper->getButtonTheme(),
        ];
    }

    public function getSofortConfig(){
        return [
            'allow_select_bank_country' => ($this->stripeConfigHelper->isSofortAllowSelectBankCountry()=="1")?true:false,
            'allow_select_language' => ($this->stripeConfigHelper->isSofortAllowSelectLanguage()=="1")?true:false,
            'default_language' => $this->stripeConfigHelper->sofortDefaultLanguage(),
            'default_bank_country' => $this->stripeConfigHelper->sofortDefaultBankCountry(),
            'language_list' => json_encode($this->sofortLanguage->toOptionArray()),
            'bank_list' => json_encode($this->sofortBank->toOptionArray())
        ];
    }

    public function getIdealConfig(){
        return [
            'is_use_element_interface' => ($this->stripeConfigHelper->isUseElementInterface()=="1")?true:false,
            'is_allow_select_bank' => ($this->stripeConfigHelper->isIdealAllowSelectBank()=="1")?true:false,
            'default_bank' => $this->stripeConfigHelper->getIdealDefaultBank(),
            'bank_list' => json_encode($this->idealBank->toOptionArray())
        ];
    }

    public function getBancontactConfig(){
        return [
            'allow_select_language' => ($this->stripeConfigHelper->isBancontactAllowSelectLanguage()=="1")?true:false,
            'default_language' => $this->stripeConfigHelper->bancontactDefaultLanguage(),
            'language_list' => json_encode($this->bancontactLanguage->toOptionArray())
        ];
    }

    public function getViewFileUrl($fileId, array $params = [])
    {
        try {
            $params = array_merge(['_secure' => $this->request->isSecure()], $params);
            return $this->assetRepo->getUrlWithParams($fileId, $params);
        } catch (LocalizedException $e) {
            $this->logger->critical($e);
            return $this->urlBuilder->getUrl('', ['_direct' => 'core/index/notFound']);
        }
    }
}
