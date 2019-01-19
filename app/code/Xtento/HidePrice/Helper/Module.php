<?php

/**
 * Product:       Xtento_HidePrice (1.0.2)
 * ID:            nwkgCoSUq+AYqPyK726YGWS2gaWLfPrdiRDDNmMBqtI=
 * Packaged:      2018-01-24T17:02:31+00:00
 * Last Modified: 2017-10-01T15:27:17+00:00
 * File:          app/code/Xtento/HidePrice/Helper/Module.php
 * Copyright:     Copyright (c) 2017 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

namespace Xtento\HidePrice\Helper;

use Magento\Store\Model\ScopeInterface;

class Module extends \Xtento\XtCore\Helper\AbstractModule
{
    protected $edition = 'CE';
    protected $module = 'Xtento_HidePrice';
    protected $extId = 'MTWOXtento_HidePrice121310';
    protected $configPath = 'hideprice/general/';

    // Module specific functionality below

    /**
     * @return bool
     */
    public function isModuleEnabled()
    {
        return parent::isModuleEnabled();
    }

    /**
     * @param null $storeId
     *
     * @return bool
     */
    public function getDisableCustomerFunctionality($storeId = null)
    {
        return $this->scopeConfig->isSetFlag('hideprice/website_config/disable_customer', ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param null $storeId
     *
     * @return bool
     */
    public function getDisableShoppingCart($storeId = null)
    {
        return $this->scopeConfig->isSetFlag('hideprice/website_config/disable_shopping_cart', ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param null $storeId
     *
     * @return bool
     */
    public function getDisableCheckout($storeId = null)
    {
        return $this->scopeConfig->isSetFlag('hideprice/website_config/disable_checkout', ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param $field
     * @param null $storeId
     *
     * @return bool
     */
    public function getConfigFlagHide($field, $storeId = null)
    {
        if ($field == 'add_to_cart' && $this->getDisableShoppingCart($storeId)) {
            return true;
        }
        return $this->scopeConfig->isSetFlag('hideprice/display_config/hide_' . $field, ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param $field
     * @param null $storeId
     *
     * @return bool
     */
    public function getApplyToConfig($field, $storeId = null)
    {
        return $this->scopeConfig->getValue('hideprice/apply_to_config/' . $field, ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param $field
     * @param null $storeId
     *
     * @return bool
     */
    public function getSelectorFromConfig($field, $storeId = null)
    {
        return $this->scopeConfig->getValue('hideprice/dev_config/selector_' . $field, ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param $field
     * @param null $storeId
     *
     * @return bool
     */
    public function getBlockNameFromConfig($field, $storeId = null)
    {
        return $this->scopeConfig->getValue('hideprice/dev_config/block_' . $field, ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param $field
     * @param null $storeId
     *
     * @return bool
     */
    public function getFrontendSettingFlag($field, $storeId = null)
    {
        return $this->scopeConfig->isSetFlag('hideprice/display_config/' . $field, ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param $field
     * @param null $storeId
     *
     * @return bool
     */
    public function getFrontendSetting($field, $storeId = null)
    {
        return $this->scopeConfig->getValue('hideprice/display_config/' . $field, ScopeInterface::SCOPE_STORE, $storeId);
    }
}
