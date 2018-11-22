<?php
/**
 * Created by Magenest.
 * Author: Pham Quang Hau
 * Date: 17/05/2016
 * Time: 15:12
 */

namespace Magenest\StripePayment\Model;

use Magenest\StripePayment\Model\ResourceModel\Customer as Resource;
use Magenest\StripePayment\Model\ResourceModel\Customer\Collection as Collection;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;

class Customer extends AbstractModel
{
    protected $_eventPrefix = 'customer_';

    public function __construct(
        Context $context,
        Registry $registry,
        Resource $resource,
        Collection $resourceCollection,
        $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }
}
