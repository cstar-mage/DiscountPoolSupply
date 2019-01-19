<?php

/**
 * Product:       Xtento_HidePrice (1.0.2)
 * ID:            nwkgCoSUq+AYqPyK726YGWS2gaWLfPrdiRDDNmMBqtI=
 * Packaged:      2018-01-24T17:02:31+00:00
 * Last Modified: 2017-09-02T16:10:26+00:00
 * File:          app/code/Xtento/HidePrice/Plugin/Block/Cart/SidebarPlugin.php
 * Copyright:     Copyright (c) 2017 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

namespace Xtento\HidePrice\Plugin\Block\Cart;

use Magento\Store\Model\StoreManagerInterface;
use Xtento\HidePrice\Helper\Module;

/**
 * Plugin to remove shopping cart references: Mini cart
 *
 * Class SidebarPlugin
 * @package Xtento\HidePrice\Plugin\Block\Cart
 */
class SidebarPlugin
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
     * ListProductPlugin constructor.
     *
     * @param Module $moduleHelper
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        Module $moduleHelper,
        StoreManagerInterface $storeManager
    ) {
        $this->moduleHelper = $moduleHelper;
        $this->storeManager = $storeManager;
    }

    /**
     * @param \Magento\Checkout\Block\Cart\Sidebar $subject
     * @param $result
     *
     * @return string
     */
    public function afterToHtml(\Magento\Checkout\Block\Cart\Sidebar $subject, $result)
    {
        if (!$this->moduleHelper->isModuleEnabled()) {
            return $result;
        }

        $storeId = $this->storeManager->getStore()->getId();

        $customCss = "";
        if ($this->moduleHelper->getDisableCustomerFunctionality($storeId)) {
            $customCss .= ".panel.wrapper { display: none !important; }"; // Top bar
        }
        if ($this->moduleHelper->getDisableCheckout($storeId)) {
            $customCss .= '
#top-cart-btn-checkout { display: none !important; }
.cart-summary { display: none !important; }
.form-cart { width: 100% !important; }
';
            // @todo: custom styles
        }
        $customStyle = '<style type="text/css">' . $customCss . '</style>';
        if ($this->moduleHelper->getDisableShoppingCart($storeId)) {
            return $customStyle;
        }
        if (!empty($customCss)) {
            return $result . $customStyle;
        }
        return $result;
    }
}