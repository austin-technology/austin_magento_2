<?php 

namespace Sparx\StorePickup\Controller\Index;

//use Magento\Framework\App\RequestInterface;

class Select extends \Magento\Framework\App\Action\Action
{
  public function __construct(\Magento\Framework\App\Action\Context $context)
  {
    return parent::__construct($context);
  }

  public function execute()
  {
    $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
    $checkoutSession = $objectManager->get('\Magento\Checkout\Model\Session');
    $checkoutSession->setStoresel($_POST['option']);
echo $checkoutSession->getStoresel();
//    echo 'Hello World';
//    exit;
  }
}
