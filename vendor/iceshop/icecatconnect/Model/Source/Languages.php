<?php

// @codingStandardsIgnoreFile

namespace ICEShop\ICECatConnect\Model\Source;

use Magento\Framework\App\ObjectManager;

class Languages implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $scopeConfig = ObjectManager::getInstance()->get('\Magento\Framework\App\Config\ScopeConfigInterface');
        $qvalues = $scopeConfig->getValue('iceshop_default_icecat_languages');

        //parsing and returning
        if (!empty($qvalues)) {
            //checking is serialized
            $data = unserialize($qvalues);
            if ($data !== false) {
                return $data;
            }
        }
        return false;
    }
}
