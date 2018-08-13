<?php


namespace Meigee\Blacknwhite\Setup;
 
use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
 
/**
 * @codeCoverageIgnore
 */
class InstallData implements InstallDataInterface
{
    /**
     * EAV setup factory
     *
     * @var EavSetupFactory
     */
    private $eavSetupFactory;
 
    /**
     * Init
     *
     * @param EavSetupFactory $eavSetupFactory
     */
    public function __construct(EavSetupFactory $eavSetupFactory)
    {
        $this->eavSetupFactory = $eavSetupFactory;
    }
 
    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        /** @var EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
 
        /**
         * Add attributes to the eav/attribute
         */
 
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'hover_image',
            [
                'type' => 'varchar',
                'frontend' => 'Magento\Catalog\Model\Product\Attribute\Frontend\Image',
                'label' => 'Hover Image',
                'input' => 'media_image',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'required' => false,
                'used_in_product_listing' => true,
                'sort_order' => 10,
                'group' => 'Images',
            ]
        );
    }
}



























