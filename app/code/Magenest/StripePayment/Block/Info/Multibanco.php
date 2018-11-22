<?php
namespace Magenest\StripePayment\Block\Info;

class Multibanco extends \Magento\Payment\Block\Info
{
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $this->addChild("stripe_multibanco_block", \Magenest\StripePayment\Block\Info\Multibanco\Info::class);
        return $this;
    }
}