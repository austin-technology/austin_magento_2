<?php
error_reporting(1);
ini_set('max_execution_time', 0);
ob_start();
use Magento\Framework\App\Bootstrap;
require __DIR__ . '/app/bootstrap.php';
$bootstrap = Bootstrap::create(BP, $_SERVER);
$obj = $bootstrap->getObjectManager();
$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
$productCollection = $objectManager->create('Magento\Catalog\Model\ResourceModel\Product\CollectionFactory');

$collection = $productCollection->create()
            ->addAttributeToSelect('*')
            ->load();

$file = fopen("product.csv","w");

$bmw = [];
$i = 1;
foreach ($collection as $product)
{
	if(!$product->getGtin() && !$product->getVpn() && !$product->getManufacturer())
	{   
		$bmw = $product->getId().','.$product->getSku().','.$product->getName().','.$i;
        fputcsv($file,explode(',',$bmw));
        $i++;
	 }
}

fclose($file);
?>
