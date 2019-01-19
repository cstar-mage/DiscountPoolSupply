<?php

/**
 * Product:       Xtento_HidePrice (1.0.2)
 * ID:            nwkgCoSUq+AYqPyK726YGWS2gaWLfPrdiRDDNmMBqtI=
 * Packaged:      2018-01-24T17:02:31+00:00
 * Last Modified: 2017-12-13T20:52:50+00:00
 * File:          app/code/Xtento/HidePrice/Plugin/Block/Product/AbstractProductPlugin.php
 * Copyright:     Copyright (c) 2017 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

namespace Xtento\HidePrice\Plugin\Block\Product;

use Magento\Framework\View\LayoutInterface;
use Magento\Store\Model\StoreManagerInterface;
use Xtento\HidePrice\Helper\Hide;
use Xtento\HidePrice\Helper\Module;

class AbstractProductPlugin
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
     * Product listing - Price
     *
     * @param \Magento\Catalog\Block\Product\AbstractProduct $subject
     * @param callable $proceed
     * @param \Magento\Catalog\Model\Product $product
     *
     * @return string
     */
    public function aroundGetProductPrice(
        \Magento\Catalog\Block\Product\AbstractProduct $subject,
        callable $proceed,
        \Magento\Catalog\Model\Product $product
    ) {
        $returnValue = $proceed($product);
        if (!$this->moduleHelper->isModuleEnabled()) {
            return $returnValue;
        }

        $storeId = $this->storeManager->getStore()->getId();
        $category = ($subject->getLayer()) ? $subject->getLayer()->getCurrentCategory() : false;
        // Determine if price should be hidden
        if ($this->hideHelper->shouldHide('price', $storeId, $product, $category)) {
            $priceHtml = "";
            if ($this->moduleHelper->getFrontendSettingFlag('replace_price', $storeId)) {
                $priceHtml = $this->layout->createBlock('Xtento\HidePrice\Block\Button\Contacts')->setData('type', 'price')->setData('store_id', $storeId)->toHtml();
            }
            return $priceHtml;
        }
        return $returnValue;
    }
}