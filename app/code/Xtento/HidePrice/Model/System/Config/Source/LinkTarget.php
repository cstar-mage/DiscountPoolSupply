<?php

/**
 * Product:       Xtento_HidePrice (1.0.2)
 * ID:            nwkgCoSUq+AYqPyK726YGWS2gaWLfPrdiRDDNmMBqtI=
 * Packaged:      2018-01-24T17:02:31+00:00
 * Last Modified: 2017-09-12T20:19:13+00:00
 * File:          app/code/Xtento/HidePrice/Model/System/Config/Source/LinkTarget.php
 * Copyright:     Copyright (c) 2017 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

namespace Xtento\HidePrice\Model\System\Config\Source;

/**
 * Class LinkTarget
 * @package Xtento\HidePrice\Model\System\Config\Source
 */
class LinkTarget implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        $options = [];
        $options[] = ['label' => __('Open link in same window'), 'value' => '_self'];
        $options[] = ['label' => __('Open link in a new window/tab'), 'value' => '_blank'];
        return $options;
    }
}
