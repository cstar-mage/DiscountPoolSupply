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

namespace Plumrocket\Facebookdiscount\Controller\Callbackurl;

use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\UrlInterface;
use Magento\Framework\App\Response\RedirectInterface;

class Queue extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $resource;

    /**
     * @var \Plumrocket\Facebookdiscount\Helper\Data
     */
    protected $helper;

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;

    protected $formKeyValidator;

    /**
     * @var \Plumrocket\Facebookdiscount\Model\ItemFactory
     */
    protected $itemFactory;

    /**
     * @param \Magento\Framework\App\ResourceConnection        $resource
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Magento\Framework\App\Action\Context            $context
     */
    public function __construct(
        \Plumrocket\Facebookdiscount\Model\ItemFactory $itemFactory,
        \Magento\Framework\App\ResourceConnection $resource,
        \Plumrocket\Facebookdiscount\Helper\Data $helper,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Backend\App\Action\Context $context
    ) {
        $this->itemFactory = $itemFactory;
        $this->formKeyValidator = $context->getFormKeyValidator();
        $this->resource = $resource;
        $this->helper = $helper;
        $this->resultJsonFactory = $resultJsonFactory;
        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        if (!$this->helper->moduleEnabled()) {
            return;
        }

        $tableName = $this->resource->getTableName('facebookdiscount_queue');
        $connection = $this->resource->getConnection();

        $query = 'SELECT `entity_id` FROM ' . $tableName . ' WHERE time > UNIX_TIMESTAMP() - 5 * 60 LIMIT 1';

        $results = $connection->fetchOne($query);
        $data = false;

        if ($results) {
            $deleteQuery = 'DELETE FROM ' . $tableName . ' WHERE entity_id=' . $results;
            $connection->query($deleteQuery);
            $data = $this->addDiscount($this->getRequest());
        }

        $result = $this->resultJsonFactory->create();
        return $result->setData(['success' => $data]);
    }

    private function addDiscount($request)
    {

        if (!$this->helper->hasLike() || $this->helper->hasDislike()) {
            $model = $this->itemFactory->create();
            $model->setData(
                [
                    'customer_id'   => $this->helper->getCustomerId(),
                    'visitor_id'    => $this->helper->getVisitorId(),
                    'date_created'  => strftime('%F %T', time()),
                    'discount'      => $this->helper->getDiscountAmount(),
                ]
            );

            ($this->helper->hasDislike()) ? $model->setActive(0) : $model->setActive(1);
            $model->save();

            $this->_eventManager->dispatch(
                'facebookdiscount_like_after',
                ['model' => $model, 'controller' => $this]
            );

            $this->messageManager->addSuccess($this->helper->getConfig($this->helper->getConfigSectionId() . '/general/message'));
        }

        return true;
    }
}
