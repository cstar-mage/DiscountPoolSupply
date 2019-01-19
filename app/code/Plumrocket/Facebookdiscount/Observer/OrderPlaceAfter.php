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

namespace Plumrocket\Facebookdiscount\Observer;

use Plumrocket\Facebookdiscount\Helper\Data;

class OrderPlaceAfter implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * Execute observer
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    protected $dataHelper;

    public function __construct(
        Data $dataHelper
    ) {
        $this->dataHelper = $dataHelper;
    }

    public function execute(
        \Magento\Framework\Event\Observer $observer
    ) {
        if ($this->dataHelper->moduleEnabled()
            && $this->dataHelper->hasActiveLike()
        ) {
            $this->dataHelper->getCurrentLike()
                ->setData('active', 0)
                ->save();
        }

        return $observer;
    }
}
