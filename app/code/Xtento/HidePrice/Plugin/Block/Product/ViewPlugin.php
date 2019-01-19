<?php

/**
 * Product:       Xtento_HidePrice (1.0.2)
 * ID:            nwkgCoSUq+AYqPyK726YGWS2gaWLfPrdiRDDNmMBqtI=
 * Packaged:      2018-01-24T17:02:31+00:00
 * Last Modified: 2017-09-12T20:31:18+00:00
 * File:          app/code/Xtento/HidePrice/Plugin/Block/Product/ViewPlugin.php
 * Copyright:     Copyright (c) 2017 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

namespace Xtento\HidePrice\Plugin\Block\Product;

use Magento\Framework\View\LayoutInterface;
use Xtento\HidePrice\Helper\Hide;
use Xtento\HidePrice\Helper\Module;

/**
 * Plugin to remove add to cart/compare/wishlist from product_view
 *
 * Class ViewPlugin
 * @package Xtento\HidePrice\Plugin\Block\Product
 */
class ViewPlugin
{
    /**
     * @var LayoutInterface
     */
    protected $layout;

    /**
     * @var Module
     */
    protected $moduleHelper;

    /**
     * @var Hide
     */
    protected $hideHelper;

    /**
     * ViewPlugin constructor.
     *
     * @param LayoutInterface $layout
     * @param Module $moduleHelper
     * @param Hide $hideHelper
     */
    public function __construct(
        LayoutInterface $layout,
        Module $moduleHelper,
        Hide $hideHelper
    ) {
        $this->layout = $layout;
        $this->moduleHelper = $moduleHelper;
        $this->hideHelper = $hideHelper;
    }

    public function afterToHtml(\Magento\Catalog\Block\Product\View $subject, $result)
    {
        if (!$this->moduleHelper->isModuleEnabled()) {
            return $result;
        }

        $blockName = $subject->getNameInLayout();
        $product = $subject->getProduct();
        $storeId = $product->getStoreId();
        // Should this be hidden?
        $hideThis = $this->hideHelper->shouldHide('add_to_cart', $storeId, $product);
        if ($hideThis) {
            // Configuration
            $settingHideCompare = $this->moduleHelper->getConfigFlagHide('compare', $storeId);
            $settingHideWishlist = $this->moduleHelper->getConfigFlagHide('wishlist', $storeId);
            $settingReplaceAddToCartWithButton = $this->moduleHelper->getFrontendSettingFlag('replace_add_to_cart', $storeId);
            if (stristr($blockName, $this->moduleHelper->getBlockNameFromConfig('product_add_to_cart', $storeId)) !== false) {
                $formHtml = "";
                if ($settingReplaceAddToCartWithButton) {
                    // Replace add to cart with button
                    $formHtml = $this->layout->createBlock('Xtento\HidePrice\Block\Button\Contacts')->setData('type', 'add_to_cart')->setData('store_id', $storeId)->toHtml();
                }
                return $formHtml;
            }
            if ($settingHideWishlist && $blockName == $this->moduleHelper->getBlockNameFromConfig('product_add_to_wishlist', $storeId)) {
                return "";
            }
            if ($settingHideCompare && $blockName == $this->moduleHelper->getBlockNameFromConfig('product_add_to_compare', $storeId)) {
                return "";
            }
        }
        return $result;
    }
}