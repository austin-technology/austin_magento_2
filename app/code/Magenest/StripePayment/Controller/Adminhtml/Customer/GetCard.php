<?php

namespace Magenest\StripePayment\Controller\Adminhtml\Customer;

use Magento\Backend\App\Action;
use Magento\Framework\Controller\Result\JsonFactory;

class GetCard extends \Magento\Backend\App\Action
{
    protected $jsonFactory;
    protected $stripeHelper;

    public function __construct(
        Action\Context $context,
        JsonFactory $jsonFactory,
        \Magenest\StripePayment\Helper\Data $stripeHelper
    ) {

        parent::__construct($context);
        $this->jsonFactory = $jsonFactory;
        $this->stripeHelper = $stripeHelper;
    }

    public function execute()
    {
        $customerId = $this->getRequest()->getParam('customer_id');
        $listCard = $this->stripeHelper->getSaveCard($customerId);
        return $this->jsonFactory->create()->setData([
            'success' => true,
            'error' =>false,
            'listCard' => $listCard->getData()
        ]);
    }

    public function _isAllowed()
    {
        return true;
    }
}
