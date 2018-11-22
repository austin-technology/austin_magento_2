<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Mostviewed
 */


namespace Amasty\Mostviewed\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * {@inheritdoc}
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        /** @since 1.3.0 Product Conditions functional release */
        if (version_compare($context->getVersion(), '1.3', '<')) {
            $this->createRuleTable($setup);
            $this->createRuleStoreTable($setup);
            $this->createProductIndexTable($setup);
        }

        $setup->endSetup();
    }

    /**
     * Create Rule Table
     *
     * @param SchemaSetupInterface $installer
     */
    private function createRuleTable($installer)
    {
        $tableName = $installer->getConnection()->newTable($installer->getTable('amasty_mostviewed_rule'))
            ->addColumn(
                'rule_id',
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Rule ID'
            )
            ->addColumn(
                'name',
                Table::TYPE_TEXT,
                255,
                [],
                'Rule Name'
            )
            ->addColumn(
                'conditions_serialized',
                Table::TYPE_TEXT,
                '2M',
                [],
                'Products Conditions serialized'
            );

        $installer->getConnection()->createTable($tableName);
    }

    /**
     * Create Rule Stores Table
     *
     * @param SchemaSetupInterface $installer
     */
    private function createRuleStoreTable(SchemaSetupInterface $installer)
    {
        $tableName = 'amasty_mostviewed_rule_store';
        $table     = $installer->getConnection()
            ->newTable($installer->getTable($tableName))
            ->addColumn(
                'rule_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'primary' => true],
                'Rule Id'
            )
            ->addColumn(
                'store_id',
                Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false, 'primary' => true],
                'Store View ID'
            )
            ->addForeignKey(
                $installer->getFkName(
                    $tableName,
                    'rule_id',
                    'amasty_mostviewed_rule',
                    'rule_id'
                ),
                'rule_id',
                $installer->getTable('amasty_mostviewed_rule'),
                'rule_id',
                Table::ACTION_CASCADE
            )
            ->addForeignKey(
                $installer->getFkName(
                    $tableName,
                    'store_id',
                    'store',
                    'store_id'
                ),
                'store_id',
                $installer->getTable('store'),
                'store_id',
                Table::ACTION_CASCADE
            )
            ->setComment('Rule Store relation');

        $installer->getConnection()->createTable($table);
    }

    /**
     * Create Index Table
     *
     * @param SchemaSetupInterface $installer
     */
    private function createProductIndexTable(SchemaSetupInterface $installer)
    {
        $tableName = 'amasty_mostviewed_product_index';
        $table     = $installer->getConnection()
            ->newTable($installer->getTable($tableName))
            ->addColumn(
                'index_id',
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Index ID'
            )
            ->addColumn(
                'rule_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'Rule Id'
            )
            ->addColumn(
                'product_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'Related Product Id'
            )
            ->addIndex(
                $installer->getIdxName(
                    $tableName,
                    [
                        'rule_id',
                        'product_id'
                    ],
                    true
                ),
                [
                    'rule_id',
                    'product_id'
                ]
            )
            ->addIndex(
                $installer->getIdxName($tableName, ['rule_id']),
                ['rule_id']
            )
            ->addIndex(
                $installer->getIdxName($tableName, ['product_id']),
                ['product_id']
            )
            ->setComment('Product Matches');

        $installer->getConnection()->createTable($table);
    }
}
