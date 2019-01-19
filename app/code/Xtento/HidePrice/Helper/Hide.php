<?php

/**
 * Product:       Xtento_HidePrice (1.0.2)
 * ID:            nwkgCoSUq+AYqPyK726YGWS2gaWLfPrdiRDDNmMBqtI=
 * Packaged:      2018-01-24T17:02:31+00:00
 * Last Modified: 2017-09-13T13:42:48+00:00
 * File:          app/code/Xtento/HidePrice/Helper/Hide.php
 * Copyright:     Copyright (c) 2017 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

namespace Xtento\HidePrice\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\Http\Context as HttpContext;
use Xtento\HidePrice\Model\Attribute\Source\Display;

class Hide extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var HttpContext
     */
    protected $httpContext;

    /**
     * @var Module
     */
    protected $moduleHelper;

    /**
     * Hide constructor.
     *
     * @param Context $context
     * @param HttpContext $httpContext
     * @param Module $moduleHelper
     */
    public function __construct(
        Context $context,
        HttpContext $httpContext,
        Module $moduleHelper
    ) {
        parent::__construct($context);
        $this->httpContext = $httpContext;
        $this->moduleHelper = $moduleHelper;
    }

    public function getCustomerGroupId()
    {
        return $this->httpContext->getValue(\Magento\Customer\Model\Context::CONTEXT_GROUP);
    }

    /**
     * @param null $storeId
     *
     * @return bool
     */
    public function hideForCategory($categoryId, $storeId = null)
    {
        return in_array($categoryId, explode(",", $this->moduleHelper->getApplyToConfig('category', $storeId)));
    }

    /**
     * @param null $storeId
     *
     * @return bool
     */
    public function hideForCustomerGroup($customerGroupId, $storeId = null)
    {
        $selectedCustomerGroups = explode(",", $this->moduleHelper->getApplyToConfig('customer_group', $storeId));
        foreach ($selectedCustomerGroups as $selectedCustomerGroup) {
            if ($selectedCustomerGroup === "") {
                continue;
            }
            if ($selectedCustomerGroup == $customerGroupId) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param $field
     * @param \Magento\Catalog\Model\Product $product
     * @param \Magento\Catalog\Model\Category $category
     *
     * @return bool
     */
    public function shouldHide($field, $storeId = null, $product = false, $category = false)
    {
        // $field values: price or add_to_cart
        // Product "Hide"
        if ($product && $product->getData('hideprice_display_' . $field) === Display::HIDE) {
            return true;
        }
        // Product "Show"
        if ($product && $product->getData('hideprice_display_' . $field) === Display::SHOW) {
            return false;
        }
        // Category "Hide"
        if ($category && $category->getData('hideprice_display_' . $field) === Display::HIDE) {
            return true;
        }
        // Category "Show"
        if ($category && $category->getData('hideprice_display_' . $field) === Display::SHOW) {
            return false;
        }
        // Should "price" or "add_to_cart" be hidden for products/categories where this hasn't been set explicitly in the product?
        $hideSetting = $this->moduleHelper->getConfigFlagHide($field, $storeId);
        if ($hideSetting) {
            // Categories that should not show $field
            if ($category && $this->hideForCategory($category->getId(), $storeId)) {
                return true;
            }
            // Customer groups that should not see $field
            if ($this->hideForCustomerGroup($this->getCustomerGroupId(), $storeId)) {
                return true;
            }
            if ($product && !$category && $categoryIds = $product->getCategoryIds()) {
                foreach ($categoryIds as $categoryId) {
                    if ($this->hideForCategory($categoryId, $storeId)) {
                        return true;
                    }
                }
            }
        }
        return false;
    }
}
