<?php
/**
 * Created by Magenest.
 * Author: Pham Quang Hau
 * Date: 15/05/2016
 * Time: 13:15
 */

namespace Magenest\StripePayment\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Charge extends AbstractDb
{
    protected function _construct()
    {
        $this->_init('magenest_stripe_charge', 'id');
    }
}
