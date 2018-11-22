<?php
/**
 * Created by Magenest.
 * Author: Pham Quang Hau
 * Date: 19/05/2016
 * Time: 21:26
 */

namespace Magenest\StripePayment\Model\ResourceModel;

class Attribute extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    protected function _construct()
    {
        $this->_init('magenest_stripe_product_attribute', 'id');
    }
}
