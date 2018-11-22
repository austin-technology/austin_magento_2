<?php
/**
 *
 * Copyright © 2015 Austincommerce. All rights reserved.
 */
namespace Austin\Productexport\Controller\Index;

class Index extends \Magento\Framework\App\Action\Action
{

	/**
     * @var \Magento\Framework\App\Cache\TypeListInterface
     */
    protected $_cacheTypeList;

    /**
     * @var \Magento\Framework\App\Cache\StateInterface
     */
    protected $_cacheState;

    /**
     * @var \Magento\Framework\App\Cache\Frontend\Pool
     */
    protected $_cacheFrontendPool;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @param Action\Context $context
     * @param \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList
     * @param \Magento\Framework\App\Cache\StateInterface $cacheState
     * @param \Magento\Framework\App\Cache\Frontend\Pool $cacheFrontendPool
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
       \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        \Magento\Framework\App\Cache\StateInterface $cacheState,
        \Magento\Framework\App\Cache\Frontend\Pool $cacheFrontendPool,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->_cacheTypeList = $cacheTypeList;
        $this->_cacheState = $cacheState;
        $this->_cacheFrontendPool = $cacheFrontendPool;
        $this->resultPageFactory = $resultPageFactory;
    }
	
    /**
     * Flush cache storage
     *
     */
    public function execute()
    {   

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $attributeCode="quantity_and_stock_status";
        $this->eavTypeFactory = $objectManager->get('Magento\Eav\Model\Entity\TypeFactory');
        $this->attributeFactory=$objectManager->get('Magento\Eav\Model\Entity\AttributeFactory');
        $this->attributeSetFactory=$objectManager->get('\Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\Collection');
        $this->attributeGroupFactory=$objectManager->get('Magento\Eav\Model\Entity\Attribute\GroupFactory');
        $this->attributeManagement=$objectManager->get('Magento\Eav\Model\AttributeManagement');

        $entityType = $this->eavTypeFactory->create()->loadByCode('catalog_product');
        $attribute = $this->attributeFactory->create()->loadByCode($entityType->getId(), $attributeCode);
    
        if (!$attribute->getId()) {
            return false;
        }

          echo $entityType->getId();

         /** @var \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\Collection $setCollection */
        $setCollection = $this->attributeSetFactory;
        $setCollection->addFieldToFilter('entity_type_id', $entityType->getId());
          
        
        

          /** @var Set $attributeSet */
    foreach ($setCollection as $attributeSet) {
        /** @var Group $group */
        //$group = $this->attributeGroupFactory->create()->getCollection()
          //  ->addFieldToFilter('attribute_group_code', ['eq' => $attributeGroupCode])
            //->addFieldToFilter('attribute_set_id', ['eq' => $attributeSet->getId()])
            //->getFirstItem();
 
       // $groupId = $group->getId() ?: $attributeSet->getDefaultGroupId();

          echo "<pre>";
          print_r($attribute->getData());
          print_r($setCollection->getData());
         
          exit();
 
        // Assign:
        $this->attributeManagement->assign(
            'catalog_product',
            $attributeSet->getId(),
            //$groupId,
            $attributeCode,
            $attributeSet->getCollection()->count() * 10
        );
    }
    
    echo "Done";










        //$this->resultPage = $this->resultPageFactory->create();  
		//return $this->resultPage;
        
    }
}
