<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Mostviewed
 */


namespace Amasty\Mostviewed\Plugin\Enterprise;

use Amasty\Mostviewed\Helper\Data;

class Product
{
    const UP_SELL_TYPE_NAME = 'upsell-rule';
    const RELATED_TYPE_NAME = 'related-rule';
    const CROSSSELL_TYPE_NAME = 'crosssell-rule';

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
     * Product constructor.
     * @param Data $helper
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Catalog\Model\Config $catalogConfig
     * @param \Magento\Checkout\Model\Session $session
     * @param \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry
     * @param \Magento\Framework\Data\CollectionFactory $emptyFactory
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
     * @param array|\Magento\Catalog\Model\ResourceModel\Product\Collection|\Magento\Framework\Data\Collection $items
     * @return array|\Magento\Catalog\Model\ResourceModel\Product\Collection|\Magento\Framework\Data\Collection
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function afterGetItemCollection($items, $findedItems)
    {
        $type = '';
        $excludedProducts = [];
        switch ($items->getData('type')) {
            case self::RELATED_TYPE_NAME:
                $type = Data::RELATED_PRODUCTS_CONFIG_NAMESPACE;
                break;
            case self::UP_SELL_TYPE_NAME:
                $type = Data::UP_SELLS_CONFIG_NAMESPACE;
                break;
            case self::CROSSSELL_TYPE_NAME:
                $type = Data::CROSS_SELLS_CONFIG_NAMESPACE;
                $quoteItems = $this->checkoutSession->getQuote()->getItemsCollection();
                if ($quoteItems->getSize()) {
                    $excludedProducts = $this->helper->getCartProductIds($quoteItems->getItems());
                    $this->currentProduct = $this->helper->getLastAddedProductInCart($quoteItems);
                }
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
                    $findedItems,
                    $excludedProducts
                );

                $mergedItems = [];

                if (is_array($modifiedCollection) && count($modifiedCollection)) {
                    $mergedItems = $modifiedCollection;
                } elseif (is_object($modifiedCollection) && $modifiedCollection->getItems()) {
                    $mergedItems = $modifiedCollection->getItems();
                }

                $this->registry->register('amcollection_is_modified_' . $type, $mergedItems);

                return $mergedItems;
            }
        } else {
            return $items;
        }
    }

    /**
     * @return bool
     */
    private function productStockStatus()
    {
        return (bool) $this->stockRegistry->getStockItem($this->currentProduct->getId())->getData('is_in_stock');
    }
}
