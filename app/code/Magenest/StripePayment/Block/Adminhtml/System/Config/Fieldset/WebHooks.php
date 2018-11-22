<?php
/**
 * Created by PhpStorm.
 * User: hiennq
 * Date: 18/01/2018
 * Time: 13:05
 */

namespace Magenest\StripePayment\Block\Adminhtml\System\Config\Fieldset;

class WebHooks extends \Magento\Config\Block\System\Config\Form\Field
{

    protected $helper;

    /**
     * @var int
     */
    protected $storeId;

    /**
     * @var \Magento\Store\Model\StoreFactory
     */
    protected $storeFactory;

    /**
     * @var \Magento\Store\Model\WebsiteFactory
     */
    protected $websiteFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Store\Model\StoreFactory $storeFactory
     * @param \Magento\Store\Model\WebsiteFactory $websiteFactory
     * @param array $data
     */

    protected $configData;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Store\Model\StoreFactory $storeFactory,
        \Magento\Store\Model\WebsiteFactory $websiteFactory,
        array $data = []
    ) {
        $this->storeFactory = $storeFactory;
        $this->websiteFactory = $websiteFactory;
        $this->storeManager = $context->getStoreManager();
        parent::__construct($context, $data);
    }

    /**
     * Test the API connection and report common errors.
     *
     * @return \Magento\Framework\Phrase|string
     */
    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $html = (string)$this->webConfig();
        if (strpos($html, 'success') !== false) {
        } else {
            $html = '<strong>' . $html . '</strong>';
        }

        return $html;
    }

    protected function webConfig()
    {
        $webhookUrl = $this->storeManager->getStore()->getBaseUrl()."stripe/checkout/webhooks";
        $html = "<h2><a href='https://dashboard.stripe.com/account/webhooks' target='_blank'>Use webhooks to receive events from your account</a></h2>";
        $html .= "<div class='input-url'>";
        $html .= "<div><label for='endpoint_url'>Endpoint Url <input size='100' id='endpoint_url' type='text' readonly value='$webhookUrl'></label></div>";
        $html .= "<p>SOFORT and Multibanco need to use webhooks to notify payment statuses</p>";
        $html .= "</div>";
        return $html;
    }
}