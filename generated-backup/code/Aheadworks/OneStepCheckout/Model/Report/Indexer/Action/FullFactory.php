<?php
namespace Aheadworks\OneStepCheckout\Model\Report\Indexer\Action;

/**
 * Factory class for @see \Aheadworks\OneStepCheckout\Model\Report\Indexer\Action\Full
 */
class FullFactory
{
    /**
     * Object Manager instance
     *
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager = null;

    /**
     * Instance name to create
     *
     * @var string
     */
    protected $_instanceName = null;

    /**
     * Factory constructor
     *
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param string $instanceName
     */
    public function __construct(\Magento\Framework\ObjectManagerInterface $objectManager, $instanceName = '\\Aheadworks\\OneStepCheckout\\Model\\Report\\Indexer\\Action\\Full')
    {
        $this->_objectManager = $objectManager;
        $this->_instanceName = $instanceName;
    }

    /**
     * Create class instance with specified parameters
     *
     * @param array $data
     * @return \Aheadworks\OneStepCheckout\Model\Report\Indexer\Action\Full
     */
    public function create(array $data = array())
    {
        return $this->_objectManager->create($this->_instanceName, $data);
    }
}
