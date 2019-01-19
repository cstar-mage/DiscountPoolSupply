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

namespace Plumrocket\Facebookdiscount\Controller;

use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\Action;
use Magento\Framework\Json\Helper\Data as JsonHelper;
use Magento\Framework\Url\DecoderInterface;
use Magento\Store\Model\StoreManagerInterface;
use Plumrocket\Facebookdiscount\Helper\Data as DataHelper;
use Plumrocket\Facebookdiscount\Model\ItemFactory;

abstract class AstractLike extends Action
{
    protected $dataHelper;
    protected $jsonHelper;
    protected $storeManager;
    protected $urlDecoder;
    protected $itemFactory;
    protected $formKeyValidator;

    public function __construct(
        Context $context,
        DecoderInterface $urlDecoder,
        DataHelper $dataHelper,
        JsonHelper $jsonHelper,
        StoreManagerInterface $storeManager,
        ItemFactory $itemFactory
    ) {
        $this->formKeyValidator = $context->getFormKeyValidator();
        $this->urlDecoder = $urlDecoder;
        $this->storeManager = $storeManager;
        $this->dataHelper = $dataHelper;
        $this->jsonHelper = $jsonHelper;
        $this->itemFactory = $itemFactory;

        parent::__construct($context);
    }

    /**
     * Create json response
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function jsonResponse($response = '')
    {
        return $this->getResponse()->representJson(
            $this->jsonHelper->jsonEncode($response)
        );
    }
}
