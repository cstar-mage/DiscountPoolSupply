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
 * @package     Plumrocket RMA v2.x.x
 * @copyright   Copyright (c) 2017 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\RMA\Controller\Adminhtml\Returns;

use Magento\Framework\Exception\LocalizedException;
use Plumrocket\RMA\Controller\Adminhtml\Returns;
use Plumrocket\RMA\Helper\Data;

class AddressSave extends Returns
{
    /**
     * Form session key
     *
     * @var string
     */
    protected $_formSessionKey  = 'rma_returns_address_form_data';

    /**
     * Id param name
     *
     * @var string
     */
    protected $_idKey = 'parent_id';

    /**
     * Save returns address
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $request = $this->getRequest();
        $address = $this->addressFactory->create();

        if ($parentId = $request->getParam('parent_id')) {
            // From return edit page
            $address->load($parentId, 'parent_id');
            $model = $this->_getModel();

            if (! $address->getId() && $model->getId()) {
                $address->setParentId($model->getId())
                    ->setOrderId($model->getOrder()->getId());
            }

            $backToReturn['id'] = $parentId;
            $backToAddress['parent_id'] = $parentId;
        } elseif ($orderId = $request->getParam('order_id')) {
            // From return create page
            if ($unassignedAddress = $address->getUnassigned($orderId)) {
                $address = $unassignedAddress;
            } else {
                $order = $this->orderFactory->create()->load($orderId);
                if ($order && $order->getId()) {
                    $address->setOrderId($order->getId());
                }
            }

            $backToReturn['order_id'] = $orderId;
            $backToAddress['order_id'] = $orderId;
        }

        $data = $request->getPostValue();
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data && $address->getOrderId()) {
            $address->addData($data);
            try {
                $address->save();

                if (! empty($model) && $model->getId()) {
                    $model->setUpdatedAt($this->dateTime->gmtDate())->save();
                }

                $this->dataHelper->addFormData('returns_address', false);
                $this->messageManager->addSuccess(__('You updated the return address.'));
                return $resultRedirect->setPath(Data::SECTION_ID . '/*/edit', $backToReturn);
            } catch (LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('We can\'t update the return address right now.'));
            }

            $this->dataHelper->addFormData('returns_address', $data);
        }

        return $resultRedirect->setPath(Data::SECTION_ID . '/*/address', $backToAddress);
    }
}
