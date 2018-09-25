<?php
namespace ICEShop\ICECatConnect\Model\Source;

use Magento\Framework\App\ObjectManager;

class Test implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $urlInterface = ObjectManager::getInstance()->get('\Magento\Backend\Model\UrlInterface');
        $urlPhpInfo = $urlInterface->getUrl('iceshop_icecatconnect/data/test');
        return [
            '1' => $urlPhpInfo,
        ];
    }
}
