<?php
namespace ICEShop\ICECatConnect\Model\Source;

use ICEShop\ICECatConnect\Model\ICECatConnect;

class IndexerMode implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ICECatConnect::INDEXER_MODE_SCHEDULED => __('Scheduled'),
            ICECatConnect::INDEXER_MODE_UPDATE_ON_SAVE => __('Update on save'),
        ];
    }
}
