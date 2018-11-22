<?php

namespace Magenest\StripePayment\Model\ResourceModel\Source;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected $_idFieldName = 'source_id';

    protected function _construct()
    {
        $this->_init('Magenest\StripePayment\Model\Source', 'Magenest\StripePayment\Model\ResourceModel\Source');
    }
}