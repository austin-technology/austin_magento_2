<?php
namespace Inchoo\Helloworld\Block;
class Helloworld extends \Magento\Framework\View\Element\Template
{    
    protected $_productCollectionFactory;
        
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,        
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,        
        array $data = []
    )
    {    
        $this->_productCollectionFactory = $productCollectionFactory;    
        parent::__construct($context, $data);
    }
    
    public function getProductCollection()
    {
        $collection = $this->_productCollectionFactory->create();
        $collection->addAttributeToSelect('*');
        //$collection->setPageSize(5000); // fetching only 3 products
        return $collection;
    }
}
?>