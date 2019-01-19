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

namespace Plumrocket\RMA\Controller\Returns;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\File\Mime;
use Plumrocket\RMA\App\Response\Http;
use Plumrocket\RMA\Controller\AbstractReturns;
use Plumrocket\RMA\Helper\Returns as ReturnsHelper;

class File extends AbstractReturns
{
    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $model = $this->getModel();

        $storage = $this->getRequest()->getParam('storage');
        if (! $model->getId() || ! is_string($storage) || ! trim($storage)) {
            $this->_forward('noroute');
            return;
        }

        $ds = DIRECTORY_SEPARATOR;
        // Need to use basename, because path can contain ".." to navigate to any site files
        $name = $this->fileHelper->basename($storage);
        $fileMediaPath = $this->configHelper->getBaseMediaPath(false) . $ds
            . $model->getId() . $ds
            . $name;

        $fileFullPath = $this->configHelper->getBaseMediaPath() . $ds
            . $model->getId() . $ds
            . $name;

        $content = [
            'type' => 'filename',
            'value' => $fileMediaPath,
            'rm' => false // can not delete file after use
        ];

        try {
           $response = $this->httpFactory->create()
                ->setFinalHeader('Content-Disposition', 'inline; filename="' . $name . '"', true);

            $contentType = (new Mime())->getMimeType($fileFullPath);

            $t = $this->fileFactoryFactory->create(['response' => $response])
                ->create($name, $content, DirectoryList::MEDIA, $contentType);
        } catch (\Exception $e) {
            $this->_forward('noroute');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function canViewReturn()
    {
        if ($this->specialAccess()) {
            return true;
        }

        return parent::canViewReturn();
    }

    /**
     * {@inheritdoc}
     */
    public function canViewOrder()
    {
        // Client cannot have separate order on this page
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function specialAccess()
    {
        // Access by code for admin.
        $model = $this->getModel();
        $request = $this->getRequest();
        $code = $this->returnsHelper->getCode($model, ReturnsHelper::CODE_SALT_FILE);
        if ($request->getParam('code')
            && $request->getParam('code') === $code
        ) {
            return true;
        }

        return false;
    }
}
