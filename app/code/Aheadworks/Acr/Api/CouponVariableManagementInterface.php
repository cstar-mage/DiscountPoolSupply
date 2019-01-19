<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Acr\Api;

use Aheadworks\Acr\Api\Data\CouponVariableInterface;

/**
 * Interface CouponVariableManagementInterface
 * @package Aheadworks\Acr\Api
 */
interface CouponVariableManagementInterface
{
    /**
     * Get coupon variable
     *
     * @param int $ruleId
     * @param int $storeId
     * @return CouponVariableInterface
     */
    public function getCouponVariable($ruleId, $storeId);

    /**
     * Get test coupon variable
     *
     * @return CouponVariableInterface
     */
    public function getTestCouponVariable();
}
