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

namespace Plumrocket\Facebookdiscount\Model;

class Item extends \Magento\Framework\Model\AbstractModel
{

    const FACEBOOK_LIKE_ACTION = 0;
    const FACEBOOK_REMOVE_LIKE_ACTION = 1;

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Plumrocket\Facebookdiscount\Model\ResourceModel\Item');
    }
}
