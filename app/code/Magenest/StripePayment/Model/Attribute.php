<?php
/**
 * Created by Magenest.
 * Author: Pham Quang Hau
 * Date: 19/05/2016
 * Time: 21:26
 */

namespace Magenest\StripePayment\Model;

use Magenest\StripePayment\Model\ResourceModel\Attribute as Resource;
use Magenest\StripePayment\Model\ResourceModel\Attribute\Collection as Collection;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;

class Attribute extends AbstractModel
{
    protected $_eventPrefix = 'attribute_';

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
