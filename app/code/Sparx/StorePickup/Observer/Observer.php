<?php
namespace Sparx\StorePickup\Observer; 
use Magento\Framework\Event\ObserverInterface; 
 
class Observer implements ObserverInterface { 
 
    protected $connector; 
	public function __construct() { 
        	$objectManager = \Magento\Framework\App\ObjectManager::getInstance(); 
   	 }
 
    public function execute(\Magento\Framework\Event\Observer $observer) { 
        $order = $observer->getEvent()->getOrder();
	$order_id = $observer->getEvent()->getOrderIds();
	$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
	$checkoutSession = $objectManager->get('\Magento\Checkout\Model\Session');

	$order_obj = $objectManager->create('\Magento\Sales\Model\Order')
                           ->load($order_id[0]);
//echo $order_obj->getShippingMethod();
//echo "<br>". $checkoutSession->getStoresel(); 
	if($order_obj->getShippingMethod() == "storepickup_storepickup"){
		$order_obj->setShipStore($checkoutSession->getStoresel())->save();
	}
    }
	public function log($data,$file=''){
		if(!$file){
        		$file = 'custom.log';
     		 }
     		 $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/'.$file);
      		$logger = new \Zend\Log\Logger();
      		$logger->addWriter($writer);
      		$logger->info($data);
   	 }
}
