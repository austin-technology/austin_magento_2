<?php
/**
 * Created by PhpStorm.
 * User: joel
 * Date: 13/10/2016
 * Time: 23:54
 */

namespace Magenest\StripePayment\Block\Checkout\View;

use Magento\Catalog\Block\Product\Context;
use Magenest\StripePayment\Helper\Config as ConfigHelper;

class Config extends \Magento\Framework\View\Element\Template
{
    protected $_config;

    protected $_helper;

    protected $_cardFactory;

    protected $_customerSession;

    protected $_checkoutSession;

    public $checkFlag = 0;

    public function __construct(
        Context $context,
        ConfigHelper $config,
        \Magenest\StripePayment\Model\CardFactory $cardFactory,
        \Magenest\StripePayment\Helper\Data $dataHelper,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Checkout\Model\Session $checkoutSession,
        array $data
    ) {
        $this->_customerSession = $customerSession;
        $this->_checkoutSession = $checkoutSession;
        $this->_config = $config;
        $this->_helper = $dataHelper;
        $this->_cardFactory = $cardFactory;
        parent::__construct($context, $data);
    }

    public function getPublishableKey()
    {
        $isTest = $this->_config->getIsSandboxMode();
        if ($isTest) {
            return $this->_config->getConfigValue('test_publishable');
        } else {
            return $this->_config->getConfigValue('live_publishable');
        }
    }

    public function getConfigData()
    {
        return [
            'is_iframe_active' => $this->_config->isIframeActive()
        ];
    }

    public function getIsDebugMode()
    {
        return $this->_config->isDebugMode();
    }

    public function isSave()
    {
        return $this->_config->isSave();
    }

    public function getDataCard()
    {
        $customer_id = $this->_customerSession->getCustomerId();
        $model = $this->_cardFactory->create()
            ->getCollection()->addFieldToFilter('magento_customer_id', $customer_id)
            ->getData();
        $this->checkFlag = count($model);

        return $model;
    }

    public function getConfig()
    {
        return $this->_config;
    }

    public function checkIsZeroDecimal()
    {
        $currency = $this->_checkoutSession->getQuote()->getBaseCurrencyCode();
        return $this->_helper->isZeroDecimal($currency) ? '1' : '0';
    }

    public function getCustomerSession()
    {
        return $this->_customerSession;
    }
}
