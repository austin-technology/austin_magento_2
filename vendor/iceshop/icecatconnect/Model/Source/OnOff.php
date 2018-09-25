<?php
namespace ICEShop\ICECatConnect\Model\Source;

class OnOff implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            '1' => __('On'),
            '0' => __('Off'),
        ];
    }
}
