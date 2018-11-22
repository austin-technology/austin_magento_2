<?php
/**
 * Created by Magenest.
 * Author: Pham Quang Hau
 * Date: 24/05/2016
 * Time: 03:06
 */

namespace Magenest\StripePayment\Block\Catalog\Product;

use Magento\Catalog\Block\Product\Context;
use Magenest\StripePayment\Model\AttributeFactory;
use Magenest\StripePayment\Helper\Config;

class View extends \Magento\Catalog\Block\Product\AbstractProduct
{
    protected $_attributeFactory;

    protected $_config;

    public function __construct(
        Context $context,
        AttributeFactory $attributeFactory,
        Config $config,
        $data = []
    ) {
        $this->_attributeFactory = $attributeFactory;
        $this->_config = $config;
        parent::__construct($context, $data);
    }

    public function getBillingOptions()
    {
        $product = $this->_coreRegistry->registry('current_product');
        $model = $this->_attributeFactory->create();

        $productId = $product->getId();

        /** @var \Magenest\StripePayment\Model\Attribute $model */
        $modelCollection = $model->getCollection();
        $modelCollection->addFieldToFilter('entity_id', $productId);
        $object = $modelCollection->getFirstItem();

        if ($object->getId()) {
            return unserialize($object->getValue());
        } else {
            return '';
        }
    }
}
