<?php

/**
 * Product:       Xtento_HidePrice (1.0.2)
 * ID:            nwkgCoSUq+AYqPyK726YGWS2gaWLfPrdiRDDNmMBqtI=
 * Packaged:      2018-01-24T17:02:31+00:00
 * Last Modified: 2017-09-22T14:34:08+00:00
 * File:          app/code/Xtento/HidePrice/Block/Button/Contacts.php
 * Copyright:     Copyright (c) 2017 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

namespace Xtento\HidePrice\Block\Button;

use Magento\Framework\View\Element\Template;
use Magento\Store\Model\StoreManagerInterface;
use Xtento\HidePrice\Helper\Module;

class Contacts extends \Magento\Framework\View\Element\Template
{
    protected $_template = 'Xtento_HidePrice::button/contacts.phtml';

    /**
     * @var Module
     */
    protected $moduleHelper;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * Contacts constructor.
     *
     * @param Template\Context $context
     * @param Module $moduleHelper
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        Module $moduleHelper,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->moduleHelper = $moduleHelper;
        $this->storeManager = $context->getStoreManager();
    }


    /**
     * Get button link
     *
     * @return string
     */
    public function getButtonUrl()
    {
        return $this->moduleHelper->getFrontendSetting('button_' . $this->getData('type') . '_link', $this->getData('store_id'));
    }

    /**
     * Get button image
     *
     * @return string
     */
    public function getButtonImage()
    {
        $image = $this->moduleHelper->getFrontendSetting('button_' . $this->getData('type') . '_image', $this->getData('store_id'));
        if (!empty($image)) {
            $imagePath = '/sales/store/' . $image;
            return rtrim($this->storeManager->getStore($this->getData('store_id'))->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA), '/') . $imagePath;
        } else {
            return false;
        }
    }

    /**
     * Get button link target
     *
     * @return string
     */
    public function getButtonTarget()
    {
        return $this->moduleHelper->getFrontendSetting('button_' . $this->getData('type') . '_target', $this->getData('store_id'));
    }

    /**
     * Get button label
     *
     * @return string
     */
    public function getButtonLabel()
    {
        return $this->moduleHelper->getFrontendSetting('button_' . $this->getData('type') . '_text', $this->getData('store_id'));
    }

    /**
     * Get button CSS
     *
     * @return string
     */
    public function getButtonCss()
    {
        return preg_replace('/[\r\n]/', '', $this->moduleHelper->getFrontendSetting('button_' . $this->getData('type') . '_css', $this->getData('store_id')));
    }
}