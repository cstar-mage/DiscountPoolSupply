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
 * @package     Plumrocket_Facebook_Discount v2.x.x
 * @copyright   Copyright (c) 2017 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\Facebookdiscount\Setup;

class Uninstall extends \Plumrocket\Base\Setup\AbstractUninstall
{
    /**
     * Config section id
     *
     * @var string
     */
    protected $_configSectionId = 'facebookdiscount';

    /**
     * Pathes to files
     *
     * @var Array
     */
    protected $_pathes = ['/app/code/Plumrocket/Facebookdiscount'];

    /**
     * Attributes
     *
     * @var Array
     */
    protected $_attributes = [];

    /**
     * Tables
     *
     * @var Array
     */
    protected $_tables = [
        'facebookdiscount_log',
        'facebookdiscount_queue'
    ];
}