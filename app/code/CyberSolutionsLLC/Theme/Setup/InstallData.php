<?php
/**
 * Copyright © CyberSolutionsLLC. All rights reserved.
 */
namespace CyberSolutionsLLC\Theme\Setup;

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
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
      
        $sql = "DELETE FROM `catalog_category_entity_varchar` WHERE `attribute_id` IN (SELECT `attribute_id` FROM `eav_attribute` WHERE `attribute_code` = 'sw_menu_icon_img')";
        $setup->getConnection()->query($sql);

        $setup->endSetup();
    }
}
