<?php

/**
 * Product:       Xtento_HidePrice (1.0.2)
 * ID:            nwkgCoSUq+AYqPyK726YGWS2gaWLfPrdiRDDNmMBqtI=
 * Packaged:      2018-01-24T17:02:31+00:00
 * Last Modified: 2017-12-13T20:52:50+00:00
 * File:          app/code/Xtento/HidePrice/Plugin/Block/Product/ListProductPlugin.php
 * Copyright:     Copyright (c) 2017 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

namespace Xtento\HidePrice\Plugin\Block\Product;

use Magento\Framework\View\LayoutInterface;
use Magento\Store\Model\StoreManagerInterface;
use Xtento\HidePrice\Helper\Hide;
use Xtento\HidePrice\Helper\Module;

/**
 * Plugin to handle price/add to cart form removal for category actions
 *
 * Class ListProductPlugin
 * @package Xtento\HidePrice\Plugin\Block\Product
 */
class ListProductPlugin
{
    /**
     * @var Module
     */
    private $moduleHelper;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var Hide
     */
    private $hideHelper;

    /**
     * @var LayoutInterface
     */
    private $layout;

    /**
     * ListProductPlugin constructor.
     *
     * @param Module $moduleHelper
     * @param Hide $hideHelper
     * @param LayoutInterface $layout
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        Module $moduleHelper,
        Hide $hideHelper,
        LayoutInterface $layout,
        StoreManagerInterface $storeManager
    ) {
        $this->moduleHelper = $moduleHelper;
        $this->storeManager = $storeManager;
        $this->hideHelper = $hideHelper;
        $this->layout = $layout;
    }

    /**
     * Product listing (Category) - Add to Cart
     *
     * @param \Magento\Catalog\Block\Product\ListProduct $subject
     * @param $result
     *
     * @return string
     */
    public function afterToHtml(\Magento\Catalog\Block\Product\ListProduct $subject, $result)
    {
        if (!$this->moduleHelper->isModuleEnabled()) {
            return $result;
        }

        $storeId = $this->storeManager->getStore()->getId();
        $productCollection = $subject->getLoadedProductCollection();
        $category = $subject->getLayer()->getCurrentCategory();
        // Configuration
        $settingHideCompare = $this->moduleHelper->getConfigFlagHide('compare', $storeId);
        $settingHideWishlist = $this->moduleHelper->getConfigFlagHide('wishlist', $storeId);
        $settingReplaceAddToCartWithButton = $this->moduleHelper->getFrontendSettingFlag('replace_add_to_cart', $storeId);
        // Selectors
        $productListingAddToCartFormSelector = $this->moduleHelper->getSelectorFromConfig('listing_add_to_cart', $storeId);
        $addToLinksSelector = $this->moduleHelper->getSelectorFromConfig('listing_add_to_links', $storeId);
        $hideWishlistLinkSelector = $this->moduleHelper->getSelectorFromConfig('listing_wishlist', $storeId);
        $hideCompareLinkSelector = $this->moduleHelper->getSelectorFromConfig('listing_compare', $storeId);
        // Instantiate replacement button
        $buttonHtml = "";
        if ($settingReplaceAddToCartWithButton) {
            $buttonHtml = $this->layout->createBlock('Xtento\HidePrice\Block\Button\Contacts')->setData('type', 'add_to_cart')->setData('store_id', $storeId)->toHtml();
        }
        // Update product listing
        foreach ($productCollection as $product) {
            // Determine if add to cart should be hidden
            $hideAddToCart = $this->hideHelper->shouldHide('add_to_cart', $storeId, $product, $category);
            if ($hideAddToCart) {
                $formExploded = explode("<form", $result); // Split result by <forms> so correct products are affected.
                foreach ($formExploded as &$partialForm) {
                    $replaceCount = 0;
                    $addToCartFormReplacement = '></form>' . $buttonHtml;
                    $partialForm = preg_replace('/ ' . preg_quote($productListingAddToCartFormSelector) . '(.*?)name="product" value="' . $product->getId() . '"(.*?)<\/form>/ms', $addToCartFormReplacement, $partialForm, 1, $replaceCount);
                    if ($settingHideWishlist && $settingHideCompare) { // hide wishlist + compare
                        $partialForm = preg_replace('/<div '.preg_quote($addToLinksSelector).'(.*?)"product":"' . $product->getId() . '"(.*?)<\/div>/ms', '', $partialForm, 1);
                    } else {
                        if ($settingHideWishlist) { // just wishlist
                            $partialForm = preg_replace('/<a href="#"(.*?)class="' . preg_quote($hideWishlistLinkSelector) . '"(.*?)"product":"' . $product->getId() . '"(.*?)<\/a>/ms', '', $partialForm, 1);
                        }
                        if ($settingHideCompare) { // just compare
                            $partialForm = preg_replace('/<a href="#"(.*?)class="' . preg_quote($hideCompareLinkSelector) . '"(.*?)"product":"' . $product->getId() . '"(.*?)<\/a>/ms', '', $partialForm, 1);
                        }
                    }
                    if ($replaceCount > 0) {
                        break 1;
                    }
                }
                $result = implode("<form", $formExploded);
                continue;
            }
        }
        return $result;
    }
}