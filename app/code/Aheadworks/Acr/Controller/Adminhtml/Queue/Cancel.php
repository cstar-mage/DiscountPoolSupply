<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Acr\Controller\Adminhtml\Queue;

use Aheadworks\Acr\Api\QueueManagementInterface;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class Cancel
 * @package Aheadworks\Acr\Controller\Adminhtml\Queue
 */
class Cancel extends \Magento\Backend\App\Action
{
    /**
     * {@inheritdoc}
     */
    const ADMIN_RESOURCE = 'Aheadworks_Acr::mail_log';

    /**
     * @var QueueManagementInterface
     */
    private $queueManagement;

    /**
     * @param Context $context
     * @param QueueManagementInterface $queueManagement
     */
    public function __construct(
        Context $context,
        QueueManagementInterface $queueManagement
    ) {
        parent::__construct($context);
        $this->queueManagement = $queueManagement;
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $queueId = (int)$this->getRequest()->getParam('id');
        if ($queueId) {
            try {
                $result = $this->queueManagement->cancelById($queueId);
                if ($result) {
                    $this->messageManager->addSuccessMessage(__('Email was successfully cancelled.'));
                } else {
                    $this->messageManager->addErrorMessage(__('This email cannot be cancelled.'));
                }
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while cancelling the email.'));
            }
        }
        return $resultRedirect->setPath('*/*/');
    }
}
