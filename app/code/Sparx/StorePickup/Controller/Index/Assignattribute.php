<?php 

namespace Sparx\StorePickup\Controller\Index;


class Assignattribute extends \Magento\Framework\App\Action\Action
{

    public function execute()
    {
      	$attributeSetId = 9; //Migrationnew_DEFAULT is 9, you can change as per your requirement.
        $product_id = 265477;
        $storeId = 0;
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $product = $objectManager->create('Magento\Catalog\Model\Product')->load($product_id);
        $product->setAttributeSetId($attributeSetId)->setStoreId($storeId)->save();
        echo "Done";
    }

}


