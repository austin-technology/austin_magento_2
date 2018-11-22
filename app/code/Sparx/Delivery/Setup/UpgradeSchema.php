<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Sparx\Delivery\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;

/**
 * Upgrade schema DB for OfflineShipping module.
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    

    /**
     * @inheritdoc
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '1.1.0', '<')) {
            $this->updateFreeShippingColumns($setup);
        }

        $setup->endSetup();
    }

    /**
     * Modify 'free_shipping' and 'simple_free_shipping' columns added incorrectly in InstallSchema.
     *
     * @param SchemaSetupInterface $setup
     * @return void
     */
    private function updateFreeShippingColumns(SchemaSetupInterface $setup)
    {
        
        
        $installer = $setup;

        $installer->startSetup();

        /**
         * Create table 'shipping_matrixrate'
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('shipping_matrixrate')
        )->addColumn(
            'pk',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Primary key'
        )->addColumn(
            'website_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['nullable' => false, 'default' => '0'],
            'Website Id'
        )->addColumn(
            'dest_country_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            4,
            ['nullable' => false, 'default' => '0'],
            'Destination coutry ISO/2 or ISO/3 code'
        )->addColumn(
            'dest_region_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['nullable' => false, 'default' => '0'],
            'Destination Region Id'
        )->addColumn(
            'dest_city',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            30,
            ['nullable' => false, 'default' => ''],
            'Destination City'
        )->addColumn(
            'dest_zip',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            10,
            ['nullable' => false, 'default' => '*'],
            'Destination Post Code (Zip)'
        )->addColumn(
            'dest_zip_to',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            10,
            ['nullable' => false, 'default' => '*'],
            'Destination Post Code To (Zip)'
        )->addColumn(
            'condition_name',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            20,
            ['nullable' => false],
            'Rate Condition name'
        )->addColumn(
            'condition_from_value',
            \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
            '12,4',
            ['nullable' => false, 'default' => '0.0000'],
            'Rate condition from value'
        )->addColumn(
            'condition_to_value',
            \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
            '12,4',
            ['nullable' => false, 'default' => '0.0000'],
            'Rate condition to value'
        )->addColumn(
            'price',
            \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
            '12,4',
            ['nullable' => false, 'default' => '0.0000'],
            'Price'
        )->addColumn(
            'cost',
            \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
            '12,4',
            ['nullable' => false, 'default' => '0.0000'],
            'Cost'
        )->addColumn(
            'shipping_method',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Shipping Method'
        )->addIndex(
            $installer->getIdxName(
                'shipping_matrixrate',
                ['website_id', 'dest_country_id', 'dest_region_id', 'dest_city', 'dest_zip', 'condition_name',
                    'condition_from_value','condition_to_value','shipping_method'],
                \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
            ),
            ['website_id', 'dest_country_id', 'dest_region_id', 'dest_city', 'dest_zip', 'condition_name',
                'condition_from_value','condition_to_value','shipping_method'],
            ['type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE]
        )->setComment(
            'Sparx Shipping MatrixRate'
        );
        $installer->getConnection()->createTable($table);

        $installer->endSetup();
        
    }
}
