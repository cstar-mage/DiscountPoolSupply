<?php

/**
 * Product:       Xtento_HidePrice (1.0.2)
 * ID:            nwkgCoSUq+AYqPyK726YGWS2gaWLfPrdiRDDNmMBqtI=
 * Packaged:      2018-01-24T17:02:31+00:00
 * Last Modified: 2017-08-31T15:14:57+00:00
 * File:          app/code/Xtento/HidePrice/Observer/DisableShoppingObserver.php
 * Copyright:     Copyright (c) 2017 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

namespace Xtento\HidePrice\Observer;

use Magento\Backend\Helper\Data;
use Magento\Framework\App\Response\RedirectInterface;
use Magento\Framework\Event\ObserverInterface;
use Magento\Store\Model\StoreManagerInterface;
use Xtento\HidePrice\Helper\Module;

class DisableShoppingObserver implements ObserverInterface
{
    /**
     * @var RedirectInterface
     */
    protected $redirect;

    /**
     * @var Data
     */
    private $backendHelper;

    /**
     * @var Module
     */
    private $moduleHelper;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * DisableShoppingCartObserver constructor.
     *
     * @param RedirectInterface $redirect
     * @param Data $backendHelper
     * @param StoreManagerInterface $storeManager
     * @param Module $moduleHelper
     */
    public function __construct(
        RedirectInterface $redirect,
        Data $backendHelper,
        StoreManagerInterface $storeManager,
        Module $moduleHelper
    ) {
        $this->redirect = $redirect;
        $this->backendHelper = $backendHelper;
        $this->moduleHelper = $moduleHelper;
        $this->storeManager = $storeManager;
    }

    /**
     *
     * @param \Magento\Framework\Event\Observer $observer
     *
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var \Magento\Framework\App\Action\Action $controller */
        $controller = $observer->getControllerAction();
        if (!$controller) {
            return;
        }
        $request = $controller->getRequest();
        if (!$request) {
            return;
        }
        // Check if is shopping cart controller, else return
        if (!in_array($request->getModuleName(), ['checkout', 'multishipping', 'customer'])) {
            return;
        }
        // It is shopping cart, check if should be disabled...
        if (!$this->moduleHelper->isModuleEnabled()) {
            return;
        }
        $store = $this->storeManager->getStore();
        if (!$store) {
            return;
        }
        $storeId = $store->getId();
        // Disable cart
        if ($request->getModuleName() == 'checkout' && $request->getControllerName() == 'cart' && $this->moduleHelper->getDisableShoppingCart($storeId)) {
            $this->handleRequest($controller);
        }
        // Disable checkout
        if ($request->getModuleName() == 'checkout' && $request->getControllerName() == 'onepage' && $this->moduleHelper->getDisableCheckout($storeId)) {
            $this->handleRequest($controller);
        }
        // Disable multi-shipping checkout
        if ($request->getModuleName() == 'multishipping' && $request->getControllerName() == 'checkout' && $this->moduleHelper->getDisableCheckout($storeId)) {
            $this->handleRequest($controller);
        }
        // Disable customer functionality
        if ($request->getModuleName() == 'customer' && $this->moduleHelper->getDisableCustomerFunctionality($storeId)) {
            $this->handleRequest($controller);
        }
    }

    /**
     * Do not dispatch request, redirect to homepage
     *
     * @param $controller
     */
    protected function handleRequest($controller)
    {
        $controller->getActionFlag()->set('', \Magento\Framework\App\Action\Action::FLAG_NO_DISPATCH, true);
        $this->redirect->redirect($controller->getResponse(), '/');
        // @todo: configurable route where to redirect to
    }
}