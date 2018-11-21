<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Mostviewed
 */


namespace Amasty\Mostviewed\Model\Indexer;

class Rule implements \Magento\Framework\Indexer\ActionInterface, \Magento\Framework\Mview\ActionInterface
{
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var \Amasty\Mostviewed\Api\RuleRepositoryInterface
     */
    private $ruleRepository;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    private $productCollectionFactory;

    /**
     * @var \Magento\Framework\DB\Adapter\AdapterInterface
     */
    private $connection;

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    private $resource;

    /**
     * @var \Amasty\Mostviewed\Model\ResourceModel\Rule
     */
    private $ruleResource;

    /**
     * @var \Amasty\Mostviewed\Model\ResourceModel\Rule\Collection
     */
    private $ruleResourceCollection;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    private $batchCount = 1000;

    /**
     * Rule constructor.
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Amasty\Mostviewed\Api\RuleRepositoryInterface $ruleRepository
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     * @param \Magento\Framework\App\ResourceConnection $resource
     * @param \Amasty\Mostviewed\Model\ResourceModel\Rule $ruleResource
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Amasty\Mostviewed\Api\RuleRepositoryInterface $ruleRepository,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Framework\App\ResourceConnection $resource,
        \Amasty\Mostviewed\Model\ResourceModel\Rule $ruleResource,
        \Amasty\Mostviewed\Model\ResourceModel\Rule\Collection $ruleResourceCollection,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->ruleRepository = $ruleRepository;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->connection = $resource->getConnection();
        $this->resource = $resource;
        $this->ruleResource = $ruleResource;
        $this->ruleResourceCollection = $ruleResourceCollection;
        $this->logger = $logger;
    }

    public function executeFull()
    {
        return $this->doReindex();
    }

    public function executeList(array $ids)
    {
        return $this->doReindex($ids);
    }

    public function executeRow($id)
    {
        return $this->doReindex([$id]);
    }

    public function execute($ids)
    {
        return $this->doReindex($ids);
    }

    private function getTable($tableName)
    {
        return $this->resource->getTableName($tableName);
    }

    private function doReindex(array $ids = [])
    {
        $where = '';
        if (!empty($ids)) {
            $where = $this->connection->quoteInto('rule_id IN (?)', $ids);
        } else {
            $ids = $this->ruleResourceCollection->getAllIds();
        }

        $this->connection->delete(
            $this->getTable('amasty_mostviewed_product_index'),
            $where
        );

        $rows = [];
        $count = 0;

        foreach ($ids as $ruleId) {
            try {
                /** @var \Amasty\Mostviewed\Model\Rule $rule */
                $rule = $this->ruleRepository->get($ruleId);

                /** @var \Magento\Catalog\Model\ResourceModel\Product\Collection $productCollection */
                $productCollection = $this->productCollectionFactory
                    ->create()
                    ->addAttributeToSelect($rule->getAttributeCodes());
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->logger->critical($e);
                continue;
            }
            /** @var \Magento\Catalog\Model\Product $product */
            foreach ($productCollection as $product) {
                if ($rule->validate($product)) {
                    $rows[] = [
                        'rule_id' => $ruleId,
                        'product_id' => $product->getId()
                    ];
                    if (++$count == $this->batchCount) {
                        $this->connection->insertMultiple($this->getTable('amasty_mostviewed_product_index'), $rows);
                        $rows = [];
                        $count = 0;
                    }
                }
            }
        }

        if (!empty($rows)) {
            $this->connection->insertMultiple($this->getTable('amasty_mostviewed_product_index'), $rows);
        }

        return $this;
    }
}
