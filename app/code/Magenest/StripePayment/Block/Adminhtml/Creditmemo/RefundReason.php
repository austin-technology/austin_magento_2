<?php
/**
 * Created by Magenest.
 * Author: Pham Quang Hau
 * Date: 30/05/2016
 * Time: 21:45
 */

namespace Magenest\StripePayment\Block\Adminhtml\Creditmemo;

use Magenest\StripePayment\Model\StripePaymentIframe;
use Magenest\StripePayment\Model\StripePaymentMethod;

class RefundReason extends \Magento\Backend\Block\Template
{
    protected $orderFactory;

    public function __construct(
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Magento\Backend\Block\Template\Context $context,
        array $data = []
    ) {
    
        parent::__construct($context, $data);
        $this->orderFactory = $orderFactory;
    }

    public function canShowOption()
    {
        try {
            $orderId = $this->_request->getParam('order_id');
            $order = $this->orderFactory->create()->load($orderId);
            $payment = $order->getPayment();
            if ($payment) {
                $method = $payment->getMethod();
                if (strpos($method, "magenest_stripe") !== false){
                    return true;
                }
            }
        } catch (\Exception $e) {
            return false;
        }
        return false;
    }
}
