<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Mostviewed
 */

namespace Amasty\Mostviewed\Model\ResourceModel;

use Amasty\Mostviewed\Model\ResourceModel\Rule;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\EntityManager\Operation\AttributeInterface;

/**
 * Class SaveHandler
 */
class SaveHandler implements AttributeInterface
{
    /**
     * @var Rule
     */
    protected $ruleResource;

    /**
     * @var MetadataPool
     */
    protected $metadataPool;

    /**
     * @param Rule $ruleResource
     * @param MetadataPool $metadataPool
     */
    public function __construct(
        Rule $ruleResource,
        MetadataPool $metadataPool
    ) {
        $this->ruleResource = $ruleResource;
        $this->metadataPool = $metadataPool;
    }

    /**
     * @param string $entityType
     * @param array $entityData
     * @param array $arguments
     * @return array
     * @throws \Exception
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute($entityType, $entityData, $arguments = [])
    {
        $linkField = $this->metadataPool->getMetadata($entityType)->getLinkField();
        if (isset($entityData['store_id'])) {
            $storeIds = $entityData['store_id'];
            if (!is_array($storeIds)) {
                $storeIds = explode(',', (string)$storeIds);
            }
            $this->ruleResource->bindRuleToEntity($entityData[$linkField], $storeIds, 'store');
        }

        return $entityData;
    }
}
