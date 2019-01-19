<?php

/**
 * Product:       Xtento_HidePrice (1.0.2)
 * ID:            nwkgCoSUq+AYqPyK726YGWS2gaWLfPrdiRDDNmMBqtI=
 * Packaged:      2018-01-24T17:02:31+00:00
 * Last Modified: 2017-12-13T20:39:31+00:00
 * File:          app/code/Xtento/HidePrice/Plugin/Pricing/RenderPlugin.php
 * Copyright:     Copyright (c) 2017 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

namespace Xtento\HidePrice\Plugin\Pricing;

use Magento\Catalog\Model\Product;
use Magento\Framework\Registry;
use Magento\Framework\View\LayoutInterface;
use Xtento\HidePrice\Helper\Hide;
use Xtento\HidePrice\Helper\Module;

/**
 * Plugin to remove price from product page
 *
 * Class RenderPlugin
 * @package Xtento\HidePrice\Plugin\Pricing
 */
class RenderPlugin
{
    /**
     * @var LayoutInterface
     */
    private $layout;

    /**
     * @var Registry
     */
    private $registry;

    /**
     * @var Module
     */
    private $moduleHelper;

    /**
     * @var Hide
     */
    private $hideHelper;

    /**
     * RenderPlugin constructor.
     *
     * @param LayoutInterface $layout
     * @param Registry $registry
     * @param Module $moduleHelper
     * @param Hide $hideHelper
     */
    public function __construct(
        LayoutInterface $layout,
        Registry $registry,
        Module $moduleHelper,
        Hide $hideHelper
    ) {
        $this->layout = $layout;
        $this->registry = $registry;
        $this->moduleHelper = $moduleHelper;
        $this->hideHelper = $hideHelper;
    }

    /**
     * @param \Magento\Catalog\Pricing\Render $subject
     * @param $result
     *
     * @return string
     */
    public function afterToHtml(\Magento\Catalog\Pricing\Render $subject, $result)
    {
        $affectedBlocks = explode(",", $this->moduleHelper->getBlockNameFromConfig('product_price'));
        $blockName = $subject->getNameInLayout();
        if (!in_array($blockName, $affectedBlocks)) {
            return $result;
        }
        if (!$this->moduleHelper->isModuleEnabled()) {
            return $result;
        }

        $product = $this->getProduct($subject);
        if ($this->hideHelper->shouldHide('price', $product->getStoreId(), $product)) {
            $priceHtml = "";
            if ($affectedBlocks[0] == $blockName /*first block listed*/ && $this->moduleHelper->getFrontendSettingFlag('replace_price', $product->getStoreId())) {
                $priceHtml = $this->layout->createBlock('Xtento\HidePrice\Block\Button\Contacts')->setData('type', 'price')->setData('store_id', $product->getStoreId())->toHtml();
            }
            return $priceHtml;
        }
        return $result;
    }

    /**
     * Returns saleable item instance
     *
     * @param \Magento\Catalog\Pricing\Render $subject
     *
     * @return Product
     */
    protected function getProduct(\Magento\Catalog\Pricing\Render $subject)
    {
        $parentBlock = $subject->getParentBlock();

        $product = $parentBlock && $parentBlock->getProductItem()
            ? $parentBlock->getProductItem()
            : $this->registry->registry('product');
        return $product;
    }
}