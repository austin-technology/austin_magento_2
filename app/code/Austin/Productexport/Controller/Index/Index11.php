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
        


        $ATTRIBUTE_CODE = 'quantity_and_stock_status';
        $ATTRIBUTE_GROUP = 'General';

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();

        
        //$state = $objectManager->get(Magento\Framework\App\State::class);
        //$state->setAreaCode('adminhtml');

        /* Attribute assign logic */
        $eavSetup = $objectManager->create(\Magento\Eav\Setup\EavSetup::class);
        $config = $objectManager->get(\Magento\Catalog\Model\Config::class);
        $attributeManagement = $objectManager->get(\Magento\Eav\Api\AttributeManagementInterface::class);

        $entityTypeId = $eavSetup->getEntityTypeId(\Magento\Catalog\Model\Product::ENTITY);
        $attributeSetIds = $eavSetup->getAllAttributeSetIds($entityTypeId);
        





        echo "<pre>";
        print_r($attributeSetIds);
        //exit();
    
        foreach ($attributeSetIds as $attributeSetId) {

        if ($attributeSetId) {
        

        

        $groupIds = [];
        
        // \Magento\Eav\Model\ResourceModel\Entity\Attribute\Group\CollectionFactory
        $groupCollection = $objectManager->get('\Magento\Eav\Model\ResourceModel\Entity\Attribute\Group\CollectionFactory')
            ->setAttributeSetFilter($attributeSetId)
            ->load(); // product attribute group collection
        foreach ($groupCollection as $group) {
            array_push($groupIds, $group->getAttributeGroupId());
            // \Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory
            $groupAttributesCollection = $objectManager->get('\Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory')
                ->setAttributeGroupFilter($group->getId())
                ->addVisibleFilter()
                ->load(); // product attribute collection
            foreach ($groupAttributesCollection->getItems() as $attribute) {
                array_push($attributeids, $attribute->getAttributeId());
            }
        }
        print_r($groupIds); // It will print all attribute group ids  



          }

           exit();


            if ($attributeSetId) {
                $group_id = $config->getAttributeGroupId($attributeSetId, $ATTRIBUTE_GROUP);
                $attributeManagement->assign(
                    'catalog_product',
                    $attributeSetId,
                    $group_id,
                    $ATTRIBUTE_CODE,
                    999
               );
            }
        }
      echo "Done";  

        //$this->resultPage = $this->resultPageFactory->create();  
		//return $this->resultPage;
        
    }
}
