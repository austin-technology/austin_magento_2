<?php
/**
 * Created by Magenest.
 * Author: Pham Quang Hau
 * Date: 15/05/2016
 * Time: 13:16
 */

namespace Magenest\StripePayment\Model\ResourceModel\Charge;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected $_idFieldName = 'id';

    protected function _construct()
    {
        $this->_init('Magenest\StripePayment\Model\Charge', 'Magenest\StripePayment\Model\ResourceModel\Charge');
    }
}
