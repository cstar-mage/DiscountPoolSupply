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

namespace Plumrocket\Facebookdiscount\Controller\Like;

use Plumrocket\Facebookdiscount\Model\Item as FacebookdiscountItem;

class RemoveLike extends \Plumrocket\Facebookdiscount\Controller\AstractLike
{
    public function execute()
    {
        try {
            $model = $this->itemFactory->create();
            $model->setCustomerId($this->dataHelper->getCustomerId())
                ->setVisitorId($this->dataHelper->getVisitorId())
                ->setDateCreated(strftime('%F %T', time()))
                ->setDiscount($this->dataHelper->getDiscountAmount())
                ->setAction(FacebookdiscountItem::FACEBOOK_REMOVE_LIKE_ACTION)
                ->save();
            return $this->jsonResponse(
                ['status' => 'success']
            );
        } catch (\Exception $e) {
            $this->logger->debug($e->getMessage());
            return $this->jsonResponse(
                [
                    'status' => 'error',
                    'message' => $e->getMessage()
                ]
            );
        }
    }
}
