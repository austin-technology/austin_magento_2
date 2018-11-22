<?php
namespace Sparx\StorePickup\Observer;
use Magento\Framework\Event\ObserverInterface;

class Savebef implements ObserverInterface {

   // protected $connector;
     //   public function __construct() {
       //         $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        // }

    private $responseFactory;
    private $url;
    public function __construct(
	\Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\App\ResponseFactory $responseFactory,
        \Magento\Framework\UrlInterface $url
    ) {
	$this->_objectManager = $objectManager;
        $this->responseFactory = $responseFactory;
        $this->url = $url;
    }

    public function execute(\Magento\Framework\Event\Observer $observer) {
	$quote = $observer->getEvent()->getQuote();
	echo "aaaa";
	if($quote->getShippingAddress()->getShippingMethod() == "delivery_delivery"){
	    $items = $quote->getAllItems();
	    $flag = 1;
	    $prod_arr = '';
	    foreach($items as $item){
	        echo $item->getProductId(); echo "<br>";
	        $product = $this->_objectManager->create('Magento\Catalog\Model\Product')->load($item->getProductId());
	        if($product->getData("c2c_branch_osb_qty") == 0 || $product->getData("c2c_branch_osb_qty") == null){
	            $flag = 0;
		    $prod_arr .= '"'.$product->getName().'",';
	        } 
	    }
	    $messageManager = $this->_objectManager->get("\Magento\Framework\Message\ManagerInterface");
	    if($flag == 0){
		$messageManager->addWarning(__("Unfortunately your Cart contains any item which is not available for delivery - please remove these items - ".$prod_arr));
		$urli = $this->url->getUrl('checkout/cart/index');
		$messageManager = $objectManager->get("\Magento\Framework\Message\ManagerInterface");
		$observer->getControllerAction()->getResponse()->setRedirect($urli);
		return $this;
	    }else{
		return $this;
	    }
	}
	return $this;
    }
}

