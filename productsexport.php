<?php
use Magento\Framework\App\Bootstrap;
include('app/bootstrap.php');
$bootstrap = Bootstrap::create(BP, $_SERVER);

$objectManager = $bootstrap->getObjectManager();

$state = $objectManager->get('Magento\Framework\App\State');
$state->setAreaCode('frontend');
$_product = $objectManager->create('Magento\Catalog\Model\ResourceModel\Product\Collection');

/*echo "<pre>";
print_r($_product->getData());*/

foreach ($_product as $product) {
	echo $product_id['id']=$product->getData('entity_id');
	$product['sku']=$product->getdata('sku');
	/*$product['name']=$product->getData('');
	
	$product['type']=$product->getData('');*/
	print_r($product->getData());
	echo "++++++++++=";
	$productm = $objectManager->get('Magento\Catalog\Model\Product')->load($product['sku']);
	echo "<pre>";
	print_r($productm->getData());
	exit();
}