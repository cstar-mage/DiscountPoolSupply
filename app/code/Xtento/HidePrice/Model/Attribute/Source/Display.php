<?php

/**
 * Product:       Xtento_HidePrice (1.0.2)
 * ID:            nwkgCoSUq+AYqPyK726YGWS2gaWLfPrdiRDDNmMBqtI=
 * Packaged:      2018-01-24T17:02:31+00:00
 * Last Modified: 2017-09-04T14:41:39+00:00
 * File:          app/code/Xtento/HidePrice/Model/Attribute/Source/Display.php
 * Copyright:     Copyright (c) 2017 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

namespace Xtento\HidePrice\Model\Attribute\Source;

class Display extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
    const USE_CONFIG = '';
    const HIDE = '0';
    const SHOW = '1';

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function getAllOptions()
    {
        if (!$this->_options) {
            $this->_options = [
                ['value' => self::USE_CONFIG, 'label' => __('-- Default Configuration --')],
                ['value' => self::HIDE, 'label' => __('Hide')],
                ['value' => self::SHOW, 'label' => __('Show')],
            ];
        }
        return $this->_options;
    }
}
