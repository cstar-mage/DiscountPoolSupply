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

use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\UrlInterface;
use Magento\Framework\App\Response\RedirectInterface;

class Index extends \Plumrocket\Facebookdiscount\Controller\AstractLike
{
    /**
     * Execute view action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        if (!$this->formKeyValidator->validate($this->getRequest())) {
            return $this->jsonResponse(
                [
                    'status' => 'error',
                    'message' => 'Form key not valid!',
                ]
            );
        }

        if ($this->dataHelper->moduleEnabled()
            && (!$this->dataHelper->hasLike() || $this->dataHelper->hasDislike())
        ) {
            $model = $this->itemFactory->create();
            $model->setData(
                [
                    'customer_id'   => $this->dataHelper->getCustomerId(),
                    'visitor_id'    => $this->dataHelper->getVisitorId(),
                    'date_created'  => strftime('%F %T', time()),
                    'discount'      => $this->dataHelper->getDiscountAmount(),
                ]
            );

            ($this->dataHelper->hasDislike()) ? $model->setActive(0) : $model->setActive(1);
            $model->save();

            $this->_eventManager->dispatch(
                'facebookdiscount_like_after',
                ['model' => $model, 'controller' => $this]
            );

            $this->messageManager->addSuccess($this->dataHelper->getConfig($this->dataHelper->getConfigSectionId() . '/general/message'));
        }

        return $this->jsonResponse(
            ['status' => 'success']
        );
    }
}
