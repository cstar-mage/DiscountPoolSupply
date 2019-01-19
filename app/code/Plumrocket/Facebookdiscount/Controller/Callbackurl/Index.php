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

class Index extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Plumrocket\Facebookdiscount\Helper\Data
     */
    protected $helper;

    /**
     * @var \Plumrocket\Facebookdiscount\Model\Queue
     */
    protected $queueModel;

    /**
     * @param \Magento\Framework\App\Action\Context    $context
     * @param \Plumrocket\Facebookdiscount\Helper\Data $helper
     * @param \Plumrocket\Facebookdiscount\Model\Queue $queueModel
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Plumrocket\Facebookdiscount\Helper\Data $helper,
        \Plumrocket\Facebookdiscount\Model\Queue $queueModel
    ) {
        $this->helper = $helper;
        $this->queueModel = $queueModel;
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

        $hubChallenge = $this->getRequest()->getParam('hub_challenge');
        $hubVerifyToken = $this->getRequest()->getParam('hub_verify_token');

        if ($hubChallenge && $hubVerifyToken && $hubVerifyToken == $this->helper->getVerifyToken()) {
			       echo $hubChallenge;
			       return;
		    }

        $json = file_get_contents('php://input');
		    $action = json_decode($json, true);

		    $like = $action['entry']['0']['changes']['0']['value']['item'];
		    $time = $action['entry']['0']['time'];

        if ($like == 'like') {
            $this->queueModel->setTime($time)->save();
        }
    }
}
