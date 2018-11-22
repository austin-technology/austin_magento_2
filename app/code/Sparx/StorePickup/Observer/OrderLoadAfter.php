<?php
namespace Sparx\StorePickup\Observer;

use Magento\Framework\Event\ObserverInterface;

class OrderLoadAfter implements ObserverInterface
 
{
 
public function execute(\Magento\Framework\Event\Observer $observer)
 
{
 
    $order = $observer->getOrder();
 //print_r($order->getData());
    $extensionAttributes = $order->getExtensionAttributes();
 
 
 
    if ($extensionAttributes === null) {
 
        $extensionAttributes = $this->getOrderExtensionDependency();
 
    }
 
	$attr = $order->getData('ship_store');
	$attr = empty($attr)?'sushil':$attr;
 
    $extensionAttributes->setShipStore($attr);
 
    $order->setExtensionAttributes($extensionAttributes);
	return $order;
}
 
   
 
private function getOrderExtensionDependency() 
{
 
    $orderExtension = \Magento\Framework\App\ObjectManager::getInstance()->get(
        '\Magento\Sales\Api\Data\OrderExtension'
    );
 
    return $orderExtension;
 
}
 
}
