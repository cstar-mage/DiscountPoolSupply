<?php
/**
 * Copyright © CyberSolutionsLLC. All rights reserved.
 */
namespace CyberSolutionsLLC\RotateStock\Setup;

use Magento\Eav\Setup\EavSetup;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\InstallDataInterface;

/**
 * Install Data script
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class InstallData implements InstallDataInterface
{
    /**
     * @var \Magento\Eav\Setup\EavSetupFactory
     */
    private $eavSetupFactory;

    /**
     * @param \Magento\Eav\Setup\EavSetupFactory $eavSetupFactory
     */
    public function __construct(
        \Magento\Eav\Setup\EavSetupFactory $eavSetupFactory
    ) {
        $this->eavSetupFactory = $eavSetupFactory;
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
      
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

        foreach ([
            \Magento\Catalog\Model\Category::ENTITY,
            \Magento\Catalog\Model\Product::ENTITY
        ] as $entityType) {
            $eavSetup->addAttribute(
                $entityType,
                'rs_enabled',
                [
                    'type' => 'int',
                    'label' => 'Rotate Stock Enabled',
                    'input' => 'select',
                    'source' => \CyberSolutionsLLC\RotateStock\Model\Source\RotateStockStatus::class,
                    'sort_order' => 1,
                    'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                    'group' => 'Rotate Stock',
                    'required' => false,
                    'used_in_product_listing' => true,
                ]
            )->addAttribute(
                $entityType,
                'rs_reset_qty',
                [
                    'type' => 'varchar',
                    'label' => 'New / Reset Qty',
                    'input' => 'text',
                    'source' => '',
                    'sort_order' => 2,
                    'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                    'group' => 'Rotate Stock',
                    'required' => false,
                    'note' => 'Leave empty to use global config.',
                    'used_in_product_listing' => true,
                ]
            )->addAttribute(
                $entityType,
                'rs_only_x_left',
                [
                    'type' => 'varchar',
                    'label' => 'Only X Left',
                    'input' => 'text',
                    'source' => '',
                    'sort_order' => 3,
                    'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                    'group' => 'Rotate Stock',
                    'required' => false,
                    'note' => 'Leave empty to use global config.',
                    'used_in_product_listing' => true,
                ]
            )->addAttribute(
                $entityType,
                'rs_min_qty_threshold',
                [
                    'type' => 'varchar',
                    'label' => 'Minimum Qty Threshold',
                    'input' => 'text',
                    'source' => '',
                    'sort_order' => 4,
                    'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                    'group' => 'Rotate Stock',
                    'required' => false,
                    'note' => 'Leave empty to use global config.',
                    'used_in_product_listing' => true,
                ]
            );
        }

        $setup->endSetup();
    }
}
