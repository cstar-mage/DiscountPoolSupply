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

namespace Plumrocket\RMA\Model\Returns;

use Magento\Framework\Model\AbstractModel;

class Message extends AbstractModel
{
    /**
     * Message from customer
     */
    const FROM_CUSTOMER = 'customer';

    /**
     * Message from manager (admin)
     */
    const FROM_MANAGER = 'manager';

    /**
     * Message from system (cron)
     */
    const FROM_SYSTEM = 'system';

    /**
     * @var \Plumrocket\RMA\Helper\Config
     */
    protected $configHelper;

    /**
     * @var \Plumrocket\RMA\Helper\Returns
     */
    protected $returnsHelper;

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Plumrocket\RMA\Helper\Config $configHelper
     * @param \Plumrocket\RMA\Helper\Returns $returnsHelper,
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Plumrocket\RMA\Helper\Config $configHelper,
        \Plumrocket\RMA\Helper\Returns $returnsHelper,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->configHelper = $configHelper;
        $this->returnsHelper = $returnsHelper;
        parent::__construct(
            $context,
            $registry,
            $resource,
            $resourceCollection,
            $data
        );
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Plumrocket\RMA\Model\ResourceModel\Returns\Message');
    }

    /**
     * Retrieve files with info
     *
     * @return array
     */
    public function getPreparedFiles()
    {
        $result = [];
        $files = $this->getFiles();
        if (! is_array($files)) {
            $files = (array)json_decode($files, true);
        }

        foreach ($files as $filename) {
            $result[] = [
                'filename' => $filename,
                'name' => basename($filename)
            ];
        }

        return $result;
    }
}
