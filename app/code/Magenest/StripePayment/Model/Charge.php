<?php
/**
 * Created by Magenest.
 * Author: Pham Quang Hau
 * Date: 15/05/2016
 * Time: 13:06
 */

namespace Magenest\StripePayment\Model;

use Magenest\StripePayment\Model\ResourceModel\Charge as Resource;
use Magenest\StripePayment\Model\ResourceModel\Charge\Collection as Collection;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;

class Charge extends AbstractModel
{
    protected $_eventPrefix = 'charge_';

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
