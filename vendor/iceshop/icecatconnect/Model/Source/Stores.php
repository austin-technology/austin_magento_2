<?php
namespace ICEShop\ICECatConnect\Model\Source;

use Magento\Framework\App\ObjectManager;

class Stores implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $storeManager = ObjectManager::getInstance()->get('\Magento\Store\Model\StoreManagerInterface');
        $websites = $storeManager->getWebsites();

        foreach ($websites as $website) {
            foreach ($website->getGroups() as $group) {
                $stores = $group->getStores();
                foreach ($stores as $store) {
                    $values[] = [
                        'value' => $store->getId(),
                        'label' => $store->getName()
                    ];
                }
            }
        }
        return $values;
    }
}
