<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Acr\Model;

/**
 * Class Flag
 * @package Aheadworks\Acr\Model
 */
class Flag extends \Magento\Framework\Flag
{
    /**
     * Set flag code
     * @codeCoverageIgnore
     *
     * @param string $code
     * @return $this
     */
    public function setAcrFlagCode($code)
    {
        $this->_flagCode = $code;
        return $this;
    }
}
