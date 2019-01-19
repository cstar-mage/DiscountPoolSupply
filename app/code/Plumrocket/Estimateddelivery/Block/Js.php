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
 * @package     Plumrocket_Estimateddelivery
 * @copyright   Copyright (c) 2016 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\Estimateddelivery\Block;

/**
 * Estimated delivery load js
 */
class Js extends Product
{
    /**
     * Retrieve json config string
     * @param array $config
     *
     * @return string
     */
    public function getConfig(array $config = [])
    {
        $_config = $this->_productHelper->getSourceData();
        $_config['url'] = $this->getUrl('prestimateddelivery/ajax/'.$this->getShowPosition());

        $config = array_merge($_config, $config);

        return json_encode($config);
    }
}
