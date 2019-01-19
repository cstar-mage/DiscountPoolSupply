<?php

/**
 * Product:       Xtento_HidePrice (1.0.2)
 * ID:            nwkgCoSUq+AYqPyK726YGWS2gaWLfPrdiRDDNmMBqtI=
 * Packaged:      2018-01-24T17:02:31+00:00
 * Last Modified: 2017-09-20T13:37:45+00:00
 * File:          app/code/Xtento/HidePrice/Setup/InstallData.php
 * Copyright:     Copyright (c) 2017 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

namespace Xtento\HidePrice\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;

class InstallData implements InstallDataInterface
{
    private $eavSetupFactory;

    public function __construct(EavSetupFactory $eavSetupFactory)
    {
        $this->eavSetupFactory = $eavSetupFactory;
    }

    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        /** @var EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
        foreach ([\Magento\Catalog\Model\Category::ENTITY, \Magento\Catalog\Model\Product::ENTITY] as $entity) {
            $eavSetup->addAttribute(
                $entity, 'hideprice_display_price',
                [
                    'type' => 'int',
                    'label' => 'Display "Price"',
                    'input' => 'select',
                    'source' => \Xtento\HidePrice\Model\Attribute\Source\Display::class,
                    'visible' => true,
                    'default' => null,
                    'sort_order' => 100,
                    'required' => false,
                    'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                    'group' => 'Hide Price Configuration',
                    'used_in_product_listing' => true,
                ]
            );
            $eavSetup->addAttribute(
                $entity, 'hideprice_display_add_to_cart',
                [
                    'type' => 'int',
                    'label' => 'Display "Add to Cart"',
                    'input' => 'select',
                    'source' => \Xtento\HidePrice\Model\Attribute\Source\Display::class,
                    'visible' => true,
                    'default' => null,
                    'sort_order' => 110,
                    'required' => false,
                    'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                    'group' => 'Hide Price Configuration',
                    'used_in_product_listing' => true,
                ]
            );
        }
    }
}