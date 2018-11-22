<?php
/**
 * Created by Magenest.
 * Author: Pham Quang Hau
 * Date: 17/05/2016
 * Time: 15:13
 */

namespace Magenest\StripePayment\Model\ResourceModel\Card;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected $_idFieldName = 'id';

    protected function _construct()
    {
        $this->_init('Magenest\StripePayment\Model\Card', 'Magenest\StripePayment\Model\ResourceModel\Card');
    }
}
