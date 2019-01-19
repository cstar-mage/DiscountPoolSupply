<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Acr\Controller\Adminhtml\Rule;

use Aheadworks\Acr\Model\Config;
use Aheadworks\Acr\Api\Data\PreviewInterface;
use Aheadworks\Acr\Api\RuleManagementInterface;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Magento\Framework\Data\Form\FormKey;
use Magento\Store\Model\Store;

/**
 * Class Preview
 * @package Aheadworks\Acr\Controller\Adminhtml\Rule
 */
class Preview extends \Magento\Backend\App\Action
{
    /**
     * {@inheritdoc}
     */
    const ADMIN_RESOURCE = 'Aheadworks_Acr::rules';

    /**
     * @var Config
     */
    private $config;

    /**
     * @var Registry
     */
    private $coreRegistry;

    /**
     * @var RuleManagementInterface
     */
    private $ruleManagement;

    /**
     * @var FormKey
     */
    private $formKey;

    /**
     * @param Context $context
     * @param Config $config
     * @param Registry $coreRegistry
     * @param RuleManagementInterface $ruleManagement
     * @param FormKey $formKey
     */
    public function __construct(
        Context $context,
        Config $config,
        Registry $coreRegistry,
        RuleManagementInterface $ruleManagement,
        FormKey $formKey
    ) {
        parent::__construct($context);
        $this->config = $config;
        $this->coreRegistry = $coreRegistry;
        $this->ruleManagement = $ruleManagement;
        $this->formKey = $formKey;
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        if ($this->getRequest()->isAjax()) {
            $this->_getSession()->setAcrPreviewData($this->getRequest()->getPostValue());
        } else {
            $data = $this->_getSession()->getAcrPreviewData();
            if (empty($data) ||
                !isset($data['email_data']) ||
                !isset($data['form_key']) || $data['form_key'] !== $this->formKey->getFormKey()
            ) {
                $this->_forward('noroute');
            } else {
                /** @var PreviewInterface $preview */
                $preview = $this->getPreview($data['email_data']);
                $this->coreRegistry->register('aw_acr_preview', $preview);
                $this->_view->loadLayout(['aw_acr_preview'], true, true, false);
                $this->_view->renderLayout();
            }
        }
    }

    /**
     * Get preview data
     *
     * @param array $emailData
     * @return PreviewInterface
     */
    private function getPreview($emailData)
    {
        $subject = isset($emailData['subject']) ? $emailData['subject'] : '';
        $content = isset($emailData['content']) ? $emailData['content'] : '';
        if (isset($emailData['store_ids'])) {
            if (count($emailData['store_ids']) > 0) {
                $storeId = array_shift($emailData['store_ids']);
            } else {
                $storeId = Store::DEFAULT_STORE_ID;
            }
        }
        /** @var PreviewInterface $preview */
        $preview = $this->ruleManagement->getPreview($storeId, $subject, $content);

        return $preview;
    }
}
