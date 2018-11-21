<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Mostviewed
 */


namespace Amasty\Mostviewed\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Ui\Component\MassAction\Filter;
use Amasty\Mostviewed\Model\ResourceModel\Rule\CollectionFactory;
use Amasty\Mostviewed\Model\RuleFactory;

abstract class Rule extends \Magento\Backend\App\Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Amasty_Mostviewed::rule';

    /**
     * @var Filter
     */
    protected $filter;

    /**
     * @var CollectionFactory
     */
    protected $ruleCollectionFactory;

    /**
     * @var \Amasty\Mostviewed\Api\RuleRepositoryInterface
     */
    protected $ruleRepository;

    /**
     * @var RuleFactory
     */
    protected $ruleFactory;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;

    /**
     * @var \Magento\Framework\App\Request\DataPersistorInterface
     */
    protected $dataPersistor;

    /**
     * @var \Magento\Framework\DataObject
     */
    protected $dataObject;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * Rule constructor.
     * @param Action\Context $context
     * @param Filter $filter
     * @param CollectionFactory $ruleCollectionFactory
     */
    public function __construct(
        Action\Context $context,
        Filter $filter,
        CollectionFactory $ruleCollectionFactory,
        \Amasty\Mostviewed\Api\RuleRepositoryInterface $ruleRepository,
        RuleFactory $ruleFactory,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor,
        \Magento\Framework\DataObject $dataObject,
        \Psr\Log\LoggerInterface $logger
    ) {
        parent::__construct($context);
        $this->filter = $filter;
        $this->ruleCollectionFactory = $ruleCollectionFactory;
        $this->ruleRepository = $ruleRepository;
        $this->ruleFactory = $ruleFactory;
        $this->coreRegistry = $coreRegistry;
        $this->dataPersistor = $dataPersistor;
        $this->dataObject = $dataObject;
        $this->logger = $logger;
    }

    /**
     * Initiate action
     *
     * @return $this
     */
    protected function _initAction()
    {
        $this->_view->loadLayout();
        $this->_setActiveMenu(self::ADMIN_RESOURCE)
            ->_addBreadcrumb(__('Related Product Rules'), __('Related Product Rules'));

        return $this;
    }
}
