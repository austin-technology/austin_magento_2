<?php
/**
 * Created by Magenest.
 * Author: Pham Quang Hau
 * Date: 19/05/2016
 * Time: 21:27
 */

namespace Magenest\StripePayment\Model\ResourceModel\Attribute;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected $_idFieldName = 'id';

    protected function _construct()
    {
        $this->_init('Magenest\StripePayment\Model\Attribute', 'Magenest\StripePayment\Model\ResourceModel\Attribute');
    }
}
