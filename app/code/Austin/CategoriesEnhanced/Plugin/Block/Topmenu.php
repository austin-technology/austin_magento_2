<?php

namespace Austin\CategoriesEnhanced\Plugin\Block;

/**
 * Plugin for top menu block
 */
class Topmenu
{

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * Initialize dependencies.
     *
     * @param \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    )
    {
        $this->collectionFactory = $categoryCollectionFactory;
        $this->storeManager = $storeManager;
    }

    /**
     * Build category tree for menu block.
     *
     * @param \Magento\Theme\Block\Html\Topmenu $subject
     * @param string $outermostClass
     * @param string $childrenWrapClass
     * @param int $limit
     * @return void
     * @SuppressWarnings("PMD.UnusedFormalParameter")
     */
    public function beforeGetHtml(
        \Magento\Theme\Block\Html\Topmenu $subject,
        $outermostClass = '',
        $childrenWrapClass = '',
        $limit = 0
    )
    {
        $rootId = $this->storeManager->getStore()->getRootCategoryId();
        $storeId = $this->storeManager->getStore()->getId();
        $categoryIds = array_map(array($this, 'fetchCategoryId'), array_keys($subject->getMenu()->getAllChildNodes()));
        /** @var \Magento\Catalog\Model\ResourceModel\Category\Collection $collection */
        $collection = $this->getCategoryTree($storeId, $rootId, $categoryIds);
        foreach ($subject->getMenu()->getAllChildNodes() as $id => $node) {
            if ($categoryId = $this->fetchCategoryId($id)) {
                $category = $collection->getItemById($categoryId);
                if ($category) {
                    $node->addData($category->getData());
                }
            }
        }
    }

    /**
     * Get Category Tree
     *
     * @param int $storeId
     * @param int $rootId
     * @return \Magento\Catalog\Model\ResourceModel\Category\Collection
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function getCategoryTree($storeId, $rootId, $ids)
    {
        /** @var \Magento\Catalog\Model\ResourceModel\Category\Collection $collection */
        $collection = $this->collectionFactory->create();
        $collection->setStoreId($storeId);
        $collection->addIdFilter($ids);
        $collection->addAttributeToSelect(array('meigee_cat_bg', 'meigee_cat_bg_option', 'meigee_cat_block_bottom',
            'meigee_cat_block_right', 'meigee_cat_block_top', 'meigee_cat_bold_link', 'meigee_cat_customlabel',
            'meigee_cat_custom_link', 'meigee_cat_custom_link_target', 'meigee_cat_labeltext',
            'meigee_cat_max_quantity','meigee_cat_menutype', 'meigee_cat_menu_width', 'meigee_cat_ratio',
            'meigee_cat_subcontent', 'meigee_menu_catimg'));
        return $collection;
    }

    public function fetchCategoryId($nodeId)
    {
        $matches = [];
        return preg_match('/^category-node-(\d+)$/', $nodeId, $matches) ? $matches[1] : false;
    }
}
