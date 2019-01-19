<?php
/**
 * Plumrocket Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End-user License Agreement
 * that is available through the world-wide-web at this URL:
 * http://wiki.plumrocket.net/wiki/EULA
 * If you are unable to obtain it through the world-wide-web, please
 * send an email to support@plumrocket.com so we can send you a copy immediately.
 *
 * @package     Plumrocket_Estimateddelivery
 * @copyright   Copyright (c) 2015 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\Estimateddelivery\Block;

class Product extends \Magento\Framework\View\Element\Template
{
    protected $_helper;
    protected $_productHelper;

    public function __construct(
        \Plumrocket\Estimateddelivery\Helper\Data $helper,
        \Plumrocket\Estimateddelivery\Helper\Product $productHelper,
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->_helper = $helper;
        $this->_productHelper = $productHelper;
    }

    public function isEnabled()
    {
        return $this->_productHelper->isEnabled();
    }

    protected function _toHtml()
    {
        if (!$this->isEnabled()) {
            $this->setTemplate('empty.phtml');
        }
        return parent::_toHtml();
    }

    public function canShow()
    {
        if (!$this->_helper->showPosition($this->getShowPosition())) {
            return false;
        }

        return $this->hasDeliveryDate() || $this->hasShippingDate();
    }


    public function setCategory($category)
    {
        $this->_productHelper->setCategory($category);
        return $this;
    }

    public function setProduct($product, $orderItem = null)
    {
        $this->_productHelper->setProduct($product, $orderItem);
        return $this;
    }

    public function reset()
    {
        $this->_productHelper->reset();
        return $this;
    }

    public function getProduct()
    {
        return $this->_productHelper->getProduct();
    }
    public function getCategory()
    {
        return $this->_productHelper->getCategory();
    }

    public function hasDeliveryDate()
    {
        return $this->_productHelper->hasDeliveryDate();
    }
    public function hasShippingDate()
    {
        return $this->_productHelper->hasShippingDate();
    }

    public function formatDeliveryDate()
    {
        return $this->_productHelper->formatDeliveryDate();
    }
    public function formatShippingDate()
    {
        return $this->_productHelper->formatShippingDate();
    }

    public function getDeliveryFromTime()
    {
        return $this->_productHelper->getDeliveryFromTime();
    }
    public function getShippingFromTime()
    {
        return $this->_productHelper->getShippingFromTime();
    }

    public function getDeliveryToTime()
    {
        return $this->_productHelper->getDeliveryToTime();
    }
    public function getShippingToTime()
    {
        return $this->_productHelper->getShippingToTime();
    }

    public function getDeliveryTime()
    {
        return $this->_productHelper->getDeliveryTime();
    }
    public function getShippingTime()
    {
        return $this->_productHelper->getShippingTime();
    }

    public function getDeliveryFromDate()
    {
        return $this->_productHelper->getDeliveryFromDate();
    }
    public function getShippingFromDate()
    {
        return $this->_productHelper->getShippingFromDate();
    }

    public function getDeliveryToDate()
    {
        return $this->_productHelper->getDeliveryToDate();
    }
    public function getShippingToDate()
    {
        return $this->_productHelper->getShippingToDate();
    }

    public function getDeliveryDate()
    {
        return $this->_productHelper->getDeliveryDate();
    }
    public function getShippingDate()
    {
        return $this->_productHelper->getShippingDate();
    }

    public function getDeliveryText()
    {
        return $this->_productHelper->getDeliveryText();
    }
    public function getShippingText()
    {
        return $this->_productHelper->getShippingText();
    }

    public function specialFormatDate($time)
    {
        return $this->_productHelper->specialFormatDate($time);
    }

    public function getEstimatedDate()
    {
        return $this->_productHelper->getEstimatedDate();
    }
    public function getEstimatedText()
    {
        return $this->_productHelper->getEstimatedText();
    }

    public function getShppingFromTime()
    {
        return $this->_productHelper->getShppingFromTime();
    }
    public function getShppingToTime()
    {
        return $this->_productHelper->getShppingToTime();
    }

    public function getDelivery()
    {
        return $this->_productHelper->getDelivery();
    }
    public function getShipping()
    {
        return $this->_productHelper->getShipping();
    }
}
