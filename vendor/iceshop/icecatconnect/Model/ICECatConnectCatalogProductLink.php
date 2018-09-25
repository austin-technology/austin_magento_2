<?php

namespace ICEShop\ICECatConnect\Model;

use \Magento\Catalog\Model\Product\Link;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ObjectManager;

class ICECatConnectCatalogProductLink extends \Magento\Catalog\Model\Product\Link
{

    public $typeMap = [
        'related' => \Magento\Catalog\Model\Product\Link::LINK_TYPE_RELATED,
        'up_sell' => \Magento\Catalog\Model\Product\Link::LINK_TYPE_UPSELL,
        'cross_sell' => \Magento\Catalog\Model\Product\Link::LINK_TYPE_CROSSSELL,
    ];

    /**
     * Get related products
     *
     * @param $type
     * @param $productId
     * @param null $identifierType
     * @param bool $only_id
     * @return array
     */
    public function items($type, $productId, $identifierType = null, $only_id = false)
    {
        $typeId = $this->_getTypeId($type);
        $product = $this->_initProduct($productId, $identifierType);
        $link = $product->getLinkInstance()
            ->setLinkTypeId($typeId);
        $collection = $this->_initCollection($link, $product);
        $result = [];

        foreach ($collection as $linkedProduct) {
            if (true === $only_id) {
                $row = [
                    $linkedProduct->getId()
                ];
            } else {
                $row = [
                    'product_id' => $linkedProduct->getId(),
                    'type' => $linkedProduct->getTypeId(),
                    'set' => $linkedProduct->getAttributeSetId(),
                    'sku' => $linkedProduct->getSku()
                ];
            }

            foreach ($link->getAttributes() as $attribute) {
                $row[$attribute['code']] = $linkedProduct->getData($attribute['code']);
            }
            $result[] = $row;
        }
        return $result;
    }
    
    /**
     * Remove product link association
     *
     * @param $type
     * @param $productId
     * @param $linkedProductId
     * @param null $identifierType
     * @return bool
     * @throws \Exception
     */

    public function remove($type, $productId, $linkedProductId, $identifierType = null)
    {
        $typeId = $this->_getTypeId($type);

        $product = $this->_initProduct($productId, $identifierType);

        $link = $product->getLinkInstance()
            ->setLinkTypeId($typeId);

        try {
            $getProductLinkId = $link->getResource()->getProductLinkId($productId, $linkedProductId, $typeId);
            $link->getResource()->deleteProductLink($getProductLinkId);
        } catch (\Exception $e) {
            $this->_fault('data_invalid', __('Link product does not exist.'));
        }

        return true;
    }

    public function _initProduct($productId, $identifierType = null)
    {
        $productModel = ObjectManager::getInstance()->create('Magento\Catalog\Model\Product');
        $product = $productModel->load($productId);
        if (!$product->getId()) {
            $this->_fault('product_not_exists');
        }

        return $product;
    }

    /**
     * Initialize and return linked products collection
     */
    public function _initCollection($link, $product)
    {
        $collection = $link
            ->getProductCollection()
            ->setIsStrongMode()
            ->setProduct($product);

        return $collection;
    }

    /**
     * Export collection to editable array
     */
    public function _collectionToEditableArray($collection)
    {
        $result = [];

        foreach ($collection as $linkedProduct) {
            $result[$linkedProduct->getId()] = [];

            foreach ($collection->getLinkModel()->getAttributes() as $attribute) {
                $result[$linkedProduct->getId()][$attribute['code']] = $linkedProduct->getData($attribute['code']);
            }
        }

        return $result;
    }

    private function _fault($phrase, $msg = null)
    {
        if (isset($msg)) {
            $phrase = $phrase . '(' . $msg . ')';
        }
        throw new \Exception($phrase);
    }

    public function _getTypeId($type)
    {
        if (!isset($this->typeMap[$type])) {
            $this->_fault('type_not_exists');
        }

        return $this->typeMap[$type];
    }

    /**
     * Add product link association
     *
     * @param $type
     * @param $productId
     * @param $linkedProductId
     * @param array $data
     * @param null $identifierType
     * @return bool
     * @throws \Exception
     */

    public function assign($type, $productId, $linkedProductId, $data = [], $identifierType = null)
    {
        $typeId = $this->_getTypeId($type);

        $product = $this->_initProduct($productId, $identifierType);

        $link = $product->getLinkInstance()
            ->setLinkTypeId($typeId);

        $collection = $this->_initCollection($link, $product);

        $links = $this->_collectionToEditableArray($collection);

        $links[(int)$linkedProductId] = [];

        foreach ($collection->getLinkModel()->getAttributes() as $attribute) {
            if (isset($data[$attribute['code']])) {
                $links[(int)$linkedProductId][$attribute['code']] = $data[$attribute['code']];
            }
        }

        try {
            if ($type == 'grouped') {
                $link->getResource()->saveGroupedLinks($productId, $links, $typeId);
            } else {
                $link->getResource()->saveProductLinks($productId, $links, $typeId);
            }

            $_linkInstance = ObjectManager::getInstance()->create('Magento\Catalog\Model\Product\Link\Interceptor');
            $_linkInstance->saveProductRelations($product);
        } catch (\Exception $e) {
            $this->_fault('data_invalid', __('Link product does not exist.'));
        }

        return true;
    }
}
