<?php

/**
 * Product:       Xtento_CustomTrackers (2.1.4)
 * ID:            nwkgCoSUq+AYqPyK726YGWS2gaWLfPrdiRDDNmMBqtI=
 * Packaged:      2018-01-24T17:02:19+00:00
 * Last Modified: 2015-07-12T09:56:59+00:00
 * File:          app/code/Xtento/CustomTrackers/Model/System/Config/Source/DefaultCarriers.php
 * Copyright:     Copyright (c) 2017 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

namespace Xtento\CustomTrackers\Model\System\Config\Source;

class DefaultCarriers extends \Xtento\XtCore\Model\System\Config\Source\Shipping\Carriers
{
    /**
     * Get all default carriers - and not our custom ones
     *
     * @return array
     */
    public function toOptionArray()
    {
        $carriers = parent::toOptionArray();
        foreach ($carriers as $key => $carrier) {
            if (preg_match('/^tracker/', $carrier['value'])) {
                unset($carriers[$key]);
            }
        }
        return $carriers;
    }
}
