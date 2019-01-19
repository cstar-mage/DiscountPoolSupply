<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Acr\Api;

use Aheadworks\Acr\Api\Data\RuleInterface;
use Aheadworks\Acr\Api\Data\CartHistoryInterface;
use Aheadworks\Acr\Api\Data\RuleSearchResultsInterface;

/**
 * Interface RuleManagementInterface
 * @package Aheadworks\Acr\Api
 */
interface RuleManagementInterface
{
    /**
     * Validate and return valid rules
     *
     * @param CartHistoryInterface $cartHistory
     * @return RuleSearchResultsInterface
     */
    public function validate(CartHistoryInterface $cartHistory);

    /**
     * Get email send time
     *
     * @param RuleInterface $rule
     * @param string $triggerTime
     * @return string
     */
    public function getEmailSendTime(RuleInterface $rule, $triggerTime);

    /**
     * Get email preview
     *
     * @param int $storeId
     * @param string $subject
     * @param string $content
     * @return PreviewInterface
     */
    public function getPreview($storeId, $subject, $content);
}
