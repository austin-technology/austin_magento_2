<?php
/**
 * Solwin Infotech
 * Solwin Instagram Extension
 * 
 * @category   Solwin
 * @package    Solwin_Instagram
 * @copyright  Copyright © 2006-2016 Solwin (https://www.solwininfotech.com)
 * @license    https://www.solwininfotech.com/magento-extension-license/
 */
namespace Sparx\StorePickup\Controller\Index;

use Magento\Framework\App\RequestInterface;

class Index extends \Magento\Framework\App\Action\Action
{

    public function execute() {
		ob_start();
		$html = '';
		$html .= "<div class ='address-prod'>";
		$html .= "<div class = 'store_add'>";
		$html .= $this->_view->getLayout()->createBlock('Magento\Cms\Block\Block')->setBlockId($_POST['option'])->toHtml();
		$html .= "</div>";
		$html .= "<div class = 'right_prod_block'>";
	        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$cart = $objectManager->get('\Magento\Checkout\Model\Cart'); 
		 
		$totalItems = $cart->getQuote()->getItemsCollection();
		foreach($totalItems as $prod){
			$product = $objectManager->create('Magento\Catalog\Model\Product')->load($prod->getProductId());
			$html .= "<div class ='poroducts'>"; 
			$html .= "<div class = 'prod_name'>";
			$html .= $product->getData("name");
			$html .= "</div>";
			if($product->getData($_POST['option'])){
				$html .= "<span class = 'stock in-stoc'>In Stock </span>";
			}else{
				$html .= "<span class = 'stock out-stoc'><strong>Currently Unavailable –</strong> Don’t Worry! Simply place an order and your local store will contact you as soon as your product is available for collection or delivery</span>";
			}
			$html .= "</div>";
		}
		$html .= "</div>";
		$html .= "</div>";
	//	ob_end_flush();
		echo $html;
    }

}
