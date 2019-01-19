<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_ProductAttachment
 */

namespace Amasty\ProductAttachment\Observer\Backend;

use Magento\Framework\Event\ObserverInterface;

class SaveProductAttachment implements ObserverInterface
{

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * @var \Amasty\ProductAttachment\Model\ResourceModel\File\Collection
     */
    protected $fileCollection;

    /**
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Message\Manager $messageManager
     * @param \Amasty\ProductAttachment\Model\ResourceModel\File\Collection $fileCollection
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Message\Manager $messageManager,
        \Amasty\ProductAttachment\Model\ResourceModel\File\Collection $fileCollection
    ) {
        $this->objectManager = $objectManager;
        $this->logger = $logger;
        $this->messageManager = $messageManager;
        $this->fileCollection = $fileCollection;
    }

    /**
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $controller = $observer->getController();

        if ($this->_isProductController($controller) !== true) {
            return;
        }

        $productId = $observer->getProduct()->getId();
        $this->_saveFilesData($controller, $productId);
    }

    protected function _isProductController($controller)
    {
        return $controller instanceof \Magento\Catalog\Controller\Adminhtml\Product\Save;
    }

    /**
     * @param \Magento\Catalog\Controller\Adminhtml\Product\Save $controller
     * @param string $productId
     */
    protected function _saveFilesData($controller, $productId)
    {
        $attachDataFromForm = $controller->getRequest()->getParam(
            'amasty_product_attachments', []
        );
        $storeId = $controller->getRequest()->getParam('store', 0);
        $productFiles = $this->fileCollection->getFilesByProductAndStore($productId, $storeId);
        $filesFromForm = [];
        if (isset($attachDataFromForm['attachments'])) {
            foreach ($attachDataFromForm['attachments'] as $fileData) {
                try {
                    $fileModel = $this->createFileModel();
                    $fileData['product_id'] = $productId;
                    $file = $fileModel->saveProductAttachment($fileData, $storeId);
                    $filesFromForm[] = $file->getId();
                } catch (\Exception $e) {
                    $this->_addErrors($e);
                    $this->_rollbackCreateFile($fileModel);
                }
            }
        }
        /** @var \Amasty\ProductAttachment\Model\File $file */
        foreach ($productFiles as $file) {
            if (!in_array($file->getId(), $filesFromForm)) {
                try {
                    $file->delete();
                } catch (\Exception $e) {
                    $this->_addErrors($e);
                }
            }
        }
    }

    /**
     * @param \Amasty\ProductAttachment\Model\File $fileModel
     */
    protected function _rollbackCreateFile($fileModel)
    {
        if ($fileModel->isObjectNew()) {
            $fileModel->delete();
        }
    }

    protected function _addErrors($errors)
    {
        foreach ($errors as $error) {
            $this->messageManager->addError($error);
        }
    }

    /**
     * @return \Amasty\ProductAttachment\Model\File
     */
    public function createFileModel()
    {
        return $this->objectManager->create('Amasty\ProductAttachment\Model\File');
    }
}
