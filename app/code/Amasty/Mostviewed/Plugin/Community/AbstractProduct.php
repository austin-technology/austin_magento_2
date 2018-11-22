<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Mostviewed
 */


namespace Amasty\Mostviewed\Plugin\Community;

use Amasty\Mostviewed\Helper\Data;

abstract class AbstractProduct
{
    /**
     * @var Data
     */
    private $helper;

    /**
     * @var \Magento\Framework\Registry
     */
    private $registry;

    /**
     * Catalog config
     *
     * @var \Magento\Catalog\Model\Config
     */
    private $catalogConfig;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    private $checkoutSession;

    /**
     * @var mixed
     */
    private $currentProduct;

    /**
     * @var \Magento\CatalogInventory\Api\StockRegistryInterface
     */
    private $stockRegistry;

    /**
     * @var \Magento\Framework\Data\CollectionFactory
     */
    private $emptyFactory;

    /**
     * AbstractProduct constructor.
     *
     * @param Data                                                 $helper
     * @param \Magento\Framework\Registry                          $registry
     * @param \Magento\Catalog\Model\Config                        $catalogConfig
     * @param \Magento\Checkout\Model\Session                      $session
     * @param \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry
     * @param \Magento\Framework\Data\CollectionFactory            $emptyFactory
     */
    public function __construct(
        Data $helper,
        \Magento\Framework\Registry $registry,
        \Magento\Catalog\Model\Config $catalogConfig,
        \Magento\Checkout\Model\Session $session,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry,
        \Magento\Framework\Data\CollectionFactory $emptyFactory
    ) {
        $this->registry = $registry;
        $this->helper = $helper;
        $this->catalogConfig = $catalogConfig;
        $this->currentProduct = $this->registry->registry('current_product');
        $this->checkoutSession = $session;
        $this->stockRegistry = $stockRegistry;
        $this->emptyFactory = $emptyFactory;
    }

    /**
     * @param string $type
     * @param $collection
     * @return array|\Magento\Catalog\Model\ResourceModel\Product\Collection|\Magento\Framework\Data\Collection|mixed
     */
    protected function prepareCollection($type, $collection)
    {
        $excludedProducts = [];
        if ($type === Data::CROSS_SELLS_CONFIG_NAMESPACE) {
            $quoteItems = $this->checkoutSession->getQuote()->getItemsCollection();
            $excludedProducts = $this->getExcludedProducts($quoteItems);
            $this->setCurrentProductForCart($quoteItems);
        }

        if ($this->currentProduct && $type) {
            $outOfStockOnly = (bool) $this->helper->getBlockConfig($type, 'out_of_stock_only');
            if ($outOfStockOnly && $this->productStockStatus()) {
                return $this->emptyFactory->create();
            }

            $registry = $this->registry->registry('amcollection_is_modified_' . $type);
            if ($registry !== null) {
                return $this->registry->registry('amcollection_is_modified_' . $type);
            } else {
                $modifiedCollection = $this->helper->itemsCollectionModifiedByType(
                    $type,
                    $this->currentProduct,
                    $this->catalogConfig,
                    $collection,
                    $excludedProducts
                );
                $this->registry->register('amcollection_is_modified_' . $type, $modifiedCollection);

                return $modifiedCollection;
            }
        } else {
            return $collection;
        }
    }

    /**
     * @param $quoteItems
     */
    private function setCurrentProductForCart($quoteItems)
    {
        if ($quoteItems->getSize()) {
            $this->currentProduct = $this->helper->getLastAddedProductInCart($quoteItems);
        } else {
            $this->currentProduct = null;
        }
    }

    /**
     * @param \Magento\Quote\Model\ResourceModel\Quote\Item\Collection $quoteItems
     * @return array
     */
    private function getExcludedProducts($quoteItems)
    {
        $excludedProducts = [];
        if ($quoteItems->getSize()) {
            $excludedProducts = $this->helper->getCartProductIds($quoteItems->getItems());
        }
        return $excludedProducts;
    }

    /**
     * @return bool
     */
    private function productStockStatus()
    {
        return (bool) $this->stockRegistry->getStockItem($this->currentProduct->getId())->getData('is_in_stock');
    }
}
