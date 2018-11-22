<?php
namespace Sparx\StorePickup\Setup;


use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * @codeCoverageIgnore
 */
class InstallSchema implements InstallSchemaInterface
{

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        $installer->getConnection()->addColumn(
            $installer->getTable('quote'),
            'ship_store',
            [
                'type' => 'text',
                'nullable' => false,
                'comment' => 'Delivery Store',
            ]
        );


        $installer->getConnection()->addColumn(
            $installer->getTable('sales_order'),
            'ship_store',
            [
                'type' => 'text',
                'nullable' => false,
                'comment' => 'Delivery Store',
            ]
        );

        $installer->getConnection()->addColumn(
            $installer->getTable('sales_order_grid'),
            'ship_store',
            [
                'type' => 'text',
                'nullable' => false,
                'comment' => 'Delivery Store',
            ]
        );

        $setup->endSetup();
    }
}
