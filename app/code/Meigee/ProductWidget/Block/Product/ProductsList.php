<?php
namespace Meigee\ProductWidget\Block\Product;
 
class ProductsList extends \Magento\CatalogWidget\Block\Product\ProductsList
{
    const DEFAULT_COLLECTION_SORT_BY = 'name';
    const DEFAULT_COLLECTION_ORDER = 'asc';
    const DEFAULT_PRODUCTS_COUNT = '10';
 

	protected function _construct()
    {
        parent::_construct();
        $this->addColumnCountLayoutDepend('empty', 6)
            ->addColumnCountLayoutDepend('1column', 5)
            ->addColumnCountLayoutDepend('2columns-left', 4)
            ->addColumnCountLayoutDepend('2columns-right', 4)
            ->addColumnCountLayoutDepend('3columns', 3);

        $this->addData([
            'cache_lifetime' => 0,
            'cache_tags' => [\Magento\Catalog\Model\Product::CACHE_TAG,
        ], ]);
    }
	
    public function getSortBy()
    {
        if (!$this->hasData('collection_sort_by')) {
            $this->setData('collection_sort_by', self::DEFAULT_COLLECTION_SORT_BY);
        }
        return $this->getData('collection_sort_by');
    }
 
    public function getSortOrder()
    {
        if (!$this->hasData('collection_sort_order')) {
            $this->setData('collection_sort_order', self::DEFAULT_COLLECTION_ORDER);
        }
        return $this->getData('collection_sort_order');
    }
	
	public function getNewProductsCollection() 
	{
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();   
		$collection = $objectManager->create('\Magento\Catalog\Model\ResourceModel\Product\Collection'); 
		$resource = $objectManager->create('\Magento\Framework\App\ResourceConnection');
		$categoryRepository = $objectManager->create('Magento\Catalog\Api\CategoryRepositoryInterface');
		$this->_collection = $collection;
		$this->_resource = $resource;
		$this->categoryRepository = $categoryRepository;
        $collection = clone $this->_collection;
        $collection->clear()->getSelect()->reset(\Magento\Framework\DB\Select::WHERE)->reset(\Magento\Framework\DB\Select::ORDER)->reset(\Magento\Framework\DB\Select::LIMIT_COUNT)->reset(\Magento\Framework\DB\Select::LIMIT_OFFSET)->reset(\Magento\Framework\DB\Select::GROUP);
        
		$todayDate = date('Y-m-d');
        $category_id = $this->_storeManager->getStore()->getRootCategoryId();
        $category = $this->categoryRepository->get($category_id);
        if(isset($category) && $category) {
            $collection->addMinimalPrice()
                ->addFinalPrice()
                ->addTaxPercents()
                ->addAttributeToSelect('name')
                ->addAttributeToSelect('image')
                ->addAttributeToSelect('small_image')
                ->addAttributeToSelect('thumbnail')
                ->addAttributeToSelect($this->_catalogConfig->getProductAttributes())
                ->addUrlRewrite()
                ->addAttributeToFilter(
					'news_from_date',
					[
						'or' => [
							0 => ['date' => true, 'to' => $todayDate],
							1 => ['is' => new \Zend_Db_Expr('null')],
						]
					])->addAttributeToFilter(
					[
						['attribute' => 'news_from_date', 'is' => new \Zend_Db_Expr('not null')],
					]
				)
                ->addAttributeToFilter('is_saleable', 1, 'left')
                ->addCategoryFilter($category)
                ->addAttributeToSort('created_at','desc');
        } else {
            $collection->addMinimalPrice()
                ->addFinalPrice()
                ->addTaxPercents()
                ->addAttributeToSelect('name')
                ->addAttributeToSelect('image')
                ->addAttributeToSelect('small_image')
                ->addAttributeToSelect('thumbnail')
                ->addAttributeToSelect($this->_catalogConfig->getProductAttributes())
                ->addUrlRewrite()
				->addAttributeToFilter(
					'news_from_date',
					[
						'or' => [
							0 => ['date' => true, 'to' => $todayDate],
							1 => ['is' => new \Zend_Db_Expr('null')],
						]
					])->addAttributeToFilter(
					[
						['attribute' => 'news_from_date', 'is' => new \Zend_Db_Expr('not null')],
					]
				)
                ->addAttributeToFilter('is_saleable', 1, 'left')
                ->addAttributeToSort('created_at','desc');
        }
        
        $collection->setPageSize($this->getPageSize())
            ->setCurPage($this->getRequest()->getParam($this->getData('page_var_name'), 1))
            ->setOrder($this->getSortBy(), $this->getSortOrder());
 
        $conditions = $this->getConditions();
        $conditions->collectValidatedAttributes($collection);
        $this->sqlBuilder->attachConditionToCollection($collection, $conditions);
                
        return $collection;
	}

	public function getBestSellersCollection()
    {
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();   
		$collection = $objectManager->create('\Magento\Catalog\Model\ResourceModel\Product\Collection'); 
		$resource = $objectManager->create('\Magento\Framework\App\ResourceConnection');
		$categoryRepository = $objectManager->create('Magento\Catalog\Api\CategoryRepositoryInterface');
		$this->_collection = $collection;
		$this->_resource = $resource;
		$this->categoryRepository = $categoryRepository;
		
        $collection = clone $this->_collection;
        $collection->clear()->getSelect()->reset(\Magento\Framework\DB\Select::WHERE)->reset(\Magento\Framework\DB\Select::ORDER)->reset(\Magento\Framework\DB\Select::LIMIT_COUNT)->reset(\Magento\Framework\DB\Select::LIMIT_OFFSET)->reset(\Magento\Framework\DB\Select::GROUP)->reset(\Magento\Framework\DB\Select::COLUMNS)->reset('from');
        $connection  = $this->_resource->getConnection();
        $collection->getSelect()->join(['e' => $connection->getTableName('catalog_product_entity')],'');

		$category_id = $this->_storeManager->getStore()->getRootCategoryId();
        $category = $this->categoryRepository->get($category_id);
        if(isset($category) && $category) {
            $collection->addMinimalPrice()
                ->addFinalPrice()
                ->addTaxPercents()
                ->addAttributeToSelect('name')
                ->addAttributeToSelect('image')
                ->addAttributeToSelect('small_image')
                ->addAttributeToSelect('thumbnail')
                ->addAttributeToSelect($this->_catalogConfig->getProductAttributes())
                ->addUrlRewrite()
                ->addAttributeToFilter('is_saleable', 1, 'left')
                ->addCategoryFilter($category);
        } else {
            $collection->addMinimalPrice()
                ->addFinalPrice()
                ->addTaxPercents()
                ->addAttributeToSelect('name')
                ->addAttributeToSelect('image')
                ->addAttributeToSelect('small_image')
                ->addAttributeToSelect('thumbnail')
                ->addAttributeToSelect($this->_catalogConfig->getProductAttributes())
                ->addUrlRewrite()
                ->addAttributeToFilter('is_saleable', 1, 'left');
        }
        
        $collection->getSelect()
            ->joinLeft(['soi' => $connection->getTableName('sales_order_item')], 'soi.product_id = e.entity_id', ['SUM(soi.qty_ordered) AS ordered_qty'])
            ->join(['order' => $connection->getTableName('sales_order')], "order.entity_id = soi.order_id",['order.state'])
            ->where("order.state <> 'canceled' and soi.parent_item_id IS NULL AND soi.product_id IS NOT NULL")
            ->group('soi.product_id');
		$collection->setPageSize($this->getPageSize())
            ->setCurPage($this->getRequest()->getParam($this->getData('page_var_name'), 1))
            ->setOrder($this->getSortBy(), $this->getSortOrder());
 
        $conditions = $this->getConditions();
        $conditions->collectValidatedAttributes($collection);
        $this->sqlBuilder->attachConditionToCollection($collection, $conditions);
        
        return $collection;
    }
	
	public function getFeaturedCategoryProductsCollection() 
	{
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$categoryFactory = $objectManager->create('\Magento\Catalog\Model\CategoryFactory'); 
		$this->_categoryFactory = $categoryFactory;
		$categoryId = $this->getData('featured_category');
		$collection = $this->_categoryFactory->create()->load($categoryId)->getProductCollection()
			->addAttributeToSelect('name')
			->addAttributeToSelect('image')
			->addAttributeToSelect('small_image')
			->addAttributeToSelect('thumbnail')
			->addAttributeToSelect($this->_catalogConfig->getProductAttributes())
			->addUrlRewrite()
			->addAttributeToFilter('is_saleable', 1, 'left');
			
		$collection->setPageSize($this->getPageSize())
            ->setCurPage($this->getRequest()->getParam($this->getData('page_var_name'), 1))
            ->setOrder($this->getSortBy(), $this->getSortOrder());
 
        $conditions = $this->getConditions();
        $conditions->collectValidatedAttributes($collection);
        $this->sqlBuilder->attachConditionToCollection($collection, $conditions);
		
		return $collection;
	}
	
	public function getSaleProductsCollection() 
	{
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();   
		$collection = $objectManager->create('\Magento\Catalog\Model\ResourceModel\Product\Collection'); 
		$categoryRepository = $objectManager->create('Magento\Catalog\Api\CategoryRepositoryInterface');
		$this->_collection = $collection;
		$this->categoryRepository = $categoryRepository;
		
		
		$collection = clone $this->_collection;
        $collection->clear()->getSelect()->reset(\Magento\Framework\DB\Select::WHERE)->reset(\Magento\Framework\DB\Select::ORDER)->reset(\Magento\Framework\DB\Select::LIMIT_COUNT)->reset(\Magento\Framework\DB\Select::LIMIT_OFFSET)->reset(\Magento\Framework\DB\Select::GROUP);

        $category_id = $this->_storeManager->getStore()->getRootCategoryId();
        $category = $this->categoryRepository->get($category_id);
        $now = date('Y-m-d');
        if(isset($category) && $category) {
            $collection->addMinimalPrice()
                ->addFinalPrice()
                ->addTaxPercents()
                ->addAttributeToSelect('name')
                ->addAttributeToSelect('image')
                ->addAttributeToSelect('small_image')
                ->addAttributeToSelect('thumbnail')
                // ->addAttributeToSelect('special_from_date')
                // ->addAttributeToSelect('special_to_date')
                ->addAttributeToFilter('special_price', ['neq' => ''])
                // ->addAttributeToFilter([
                    // [
                        // 'attribute' => 'special_from_date',
                        // 'lteq' => date('Y-m-d G:i:s', strtotime($now)),
                        // 'date' => true,
                    // ],
                    // [
                        // 'attribute' => 'special_to_date',
                        // 'gteq' => date('Y-m-d G:i:s', strtotime($now)),
                        // 'date' => true,
                    // ]
                // ])
                ->addAttributeToFilter('is_saleable', 1, 'left');
        } else {
            $collection->addMinimalPrice()
                ->addFinalPrice()
                ->addTaxPercents()
                ->addAttributeToSelect('name')
                ->addAttributeToSelect('image')
                ->addAttributeToSelect('small_image')
                ->addAttributeToSelect('thumbnail')
                ->addAttributeToFilter('special_price', ['neq' => ''])
                // ->addAttributeToSelect('special_from_date')
                // ->addAttributeToSelect('special_to_date')
                // ->addAttributeToFilter([
                    // [
                        // 'attribute' => 'special_from_date',
                        // 'lteq' => date('Y-m-d G:i:s', strtotime($now)),
                        // 'date' => true,
                    // ],
                    // [
                        // 'attribute' => 'special_to_date',
                        // 'gteq' => date('Y-m-d G:i:s', strtotime($now)),
                        // 'date' => true,
                    // ]
                // ])
                ->addAttributeToFilter('is_saleable', 1, 'left');
        }
        
        $collection->setPageSize($this->getPageSize())
            ->setCurPage($this->getRequest()->getParam($this->getData('page_var_name'), 1))
            ->setOrder($this->getSortBy(), $this->getSortOrder());
 
        $conditions = $this->getConditions();
        $conditions->collectValidatedAttributes($collection);
        $this->sqlBuilder->attachConditionToCollection($collection, $conditions);
        
        return $collection;
	}
	
	// public function getProductsCount()
	// {
		// if($this->getData('products_per_page')) {
			// $
		// }
	// }
	
	public function getWidgetId()
    {
        if ($this->hasData('widget_id')) {
            return $this->getData('widget_id');
        }

        return $this->getData('widget_id');
    }
	
}