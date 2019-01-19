<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Acr\Controller\Adminhtml\Queue;

use Aheadworks\Acr\Api\Data\QueueInterface;
use Aheadworks\Acr\Api\Data\RuleInterface;
use Aheadworks\Acr\Api\QueueRepositoryInterface;
use Aheadworks\Acr\Api\RuleRepositoryInterface;
use Aheadworks\Acr\Model\PreviewInterface;
use Aheadworks\Acr\Api\QueueManagementInterface;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;

/**
 * Class Preview
 * @package Aheadworks\Acr\Controller\Adminhtml\Queue
 */
class Preview extends \Magento\Backend\App\Action
{
    /**
     * {@inheritdoc}
     */
    const ADMIN_RESOURCE = 'Aheadworks_Acr::mail_log';

    /**
     * @var Registry
     */
    private $coreRegistry;

    /**
     * @var QueueRepositoryInterface
     */
    private $queueRepository;

    /**
     * @var RuleRepositoryInterface
     */
    private $ruleRepository;

    /**
     * @var QueueManagementInterface
     */
    private $queueManagement;

    /**
     * @param Context $context
     * @param Registry $coreRegistry
     * @param QueueRepositoryInterface $queueRepository
     * @param RuleRepositoryInterface $ruleRepository
     * @param QueueManagementInterface $queueManagement
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        QueueRepositoryInterface $queueRepository,
        RuleRepositoryInterface $ruleRepository,
        QueueManagementInterface $queueManagement
    ) {
        parent::__construct($context);
        $this->coreRegistry = $coreRegistry;
        $this->queueRepository = $queueRepository;
        $this->ruleRepository = $ruleRepository;
        $this->queueManagement = $queueManagement;
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $queueId = (int)$this->getRequest()->getParam('id');
        if ($queueId) {
            try {
                /** @var QueueInterface $queue */
                $queue = $this->queueRepository->get($queueId);

                /** @var PreviewInterface $preview */
                $preview = $this->queueManagement->getPreview($queue);
                $this->coreRegistry->register('aw_acr_preview', $preview);

                $this->_view->loadLayout(['aw_acr_preview'], true, true, false);
                $this->_view->renderLayout();
                return;
            } catch (\Exception $e) {
                // do nothing
            }
        }
        $this->_forward('noroute');
    }
}
