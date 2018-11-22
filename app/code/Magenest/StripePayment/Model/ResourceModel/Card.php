<?php
/**
 * Created by Magenest.
 * Author: Pham Quang Hau
 * Date: 17/05/2016
 * Time: 15:12
 */

namespace Magenest\StripePayment\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Card extends AbstractDb
{
    protected function _construct()
    {
        $this->_init('magenest_stripe_card', 'id');
    }
}
