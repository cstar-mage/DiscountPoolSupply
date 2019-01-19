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
 * @package Plumrocket_Facebook_Discount
 * @copyright Copyright (c) 2017 Plumrocket Inc. (http://www.plumrocket.com)
 * @license http://wiki.plumrocket.net/wiki/EULA End-user License Agreement
 */

namespace Plumrocket\Facebookdiscount\Helper;

use Magento\Config\Model\Config;
use Magento\Framework\App\ResourceConnection;
use Magento\Customer\Model\Session;
use Magento\Customer\Model\Visitor;
use Magento\SalesRule\Model\Rule;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use Plumrocket\Facebookdiscount\Model\Item as FacebookdiscountItem;
use Plumrocket\Facebookdiscount\Model\ItemFactory as FacebookdiscountItemFactory;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\App\Helper\Context;

class Data extends Main
{
    protected $_configSectionId = 'facebookdiscount';
    protected $logItem = null;
    protected $config;
    protected $resourceConnection;
    protected $storeManager;
    protected $customerSession;
    protected $customerVisitor;
    protected $itemFactory;

    public function __construct(
        Config $config,
        ResourceConnection $resourceConnection,
        StoreManagerInterface $storeManager,
        Session $customerSession,
        Visitor $customerVisitor,
        FacebookdiscountItemFactory $itemFactory,
        Store $store,
        ObjectManagerInterface $objectManager,
        Context $context
    ) {
        $this->config = $config;
        $this->resourceConnection = $resourceConnection;
        $this->storeManager = $storeManager;
        $this->customerSession = $customerSession;
        $this->customerVisitor = $customerVisitor;
        $this->itemFactory = $itemFactory;
        parent::__construct($objectManager, $context);
    }

    public function moduleEnabled($store = null)
    {
        return (bool)$this->getConfig($this->_configSectionId . '/general/enabled', $store);
    }

    public function getCustomerId()
    {
        return $this->customerSession->isLoggedIn() ?
            (int)$this->customerSession->getCustomer()->getId() : 0;
    }

    public function getVisitorId()
    {
        if ($visitorId = $this->customerVisitor->getId()) {
            if (!$this->customerSession->getVisitorId()) {
                $this->customerSession->setVisitorId($visitorId);
            }
            return $visitorId;
        }
        return (int)$this->customerSession->getVisitorId();
    }

    protected function loadLogItem()
    {
        if (is_null($this->logItem)) {
            $customerId = $this->getCustomerId();
            if ($customerId) {
                $this->logItem = $this->itemFactory->create()->load($customerId, 'customer_id');
            }

            if (!$this->logItem || !$this->logItem->getId()) {
                $visitorId = $this->getVisitorId();
                if ($visitorId) {
                    $this->logItem = $this->itemFactory->create()->load($visitorId, 'visitor_id');
                }
            }
        }
        return $this->logItem;
    }

    public function hasDislike()
    {
        $item = $this->loadLogItem();
        if (is_null($item)) {
            return false;
        }
        return $item->getAction() == FacebookdiscountItem::FACEBOOK_REMOVE_LIKE_ACTION;
    }

    public function disableExtension()
    {
        $resource = $this->resourceConnection;
        $connection = $resource->getConnection('core_write');
        $connection->delete(
            $resource->getTableName('core_config_data'),
            [
                $connection->quoteInto('path = ?', $this->_configSectionId  . '/general/enabled')
            ]
        );
        $this->config->setDataByPath($this->_configSectionId  . '/general/enabled', 0);
        $this->config->save();
    }

    public function hasLike()
    {
        return $this->loadLogItem() && ($this->logItem->getId() > 0);
    }

    public function hasActiveLike()
    {
        $this->_logger->debug($this->getCustomerId() . '_' . $this->getVisitorId());
        return $this->hasLike() && ($this->logItem->getActive() == 1) && ($this->logItem->getAction() != FacebookdiscountItem::FACEBOOK_REMOVE_LIKE_ACTION);
    }

    public function getCurrentLike()
    {
        return $this->logItem;
    }

    public function getDiscountAmount()
    {
        switch ($this->getDiscountType()) {
            case 0 : return (float)$this->getConfig($this->_configSectionId . '/general/fixed_discount'); break;
            case 1 : return (float)$this->getConfig($this->_configSectionId . '/general/percent_discount'); break;
        }
    }

    public function getSimpleAction()
    {
        switch ($this->getDiscountType()) {
            case 0 : return Rule::CART_FIXED_ACTION; break;
            case 1 : return Rule::BY_PERCENT_ACTION; break;
        }
    }

    public function getDiscountType()
    {
        return (int)$this->getConfig($this->_configSectionId . '/general/discount_type');
    }

    public function getApiKey()
    {
        return $this->getConfig($this->_configSectionId . '/general/app_id');
    }

    public function getPageUrl()
    {
        return $this->getConfig($this->_configSectionId . '/general/page_url');
    }

    public function getVerifyToken()
    {
        return crc32($this->storeManager->getStore()->getBaseUrl());
    }
}
