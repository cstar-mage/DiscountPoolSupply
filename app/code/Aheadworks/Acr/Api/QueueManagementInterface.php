<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Acr\Api;

use Aheadworks\Acr\Api\Data\QueueInterface;
use Aheadworks\Acr\Api\Data\RuleInterface;
use Aheadworks\Acr\Api\Data\CartHistoryInterface;
use Aheadworks\Acr\Model\PreviewInterface;

interface QueueManagementInterface
{
    /**
     * Add new email to queue
     *
     * @param RuleInterface $rule
     * @param CartHistoryInterface $cartHistory
     * @return bool
     */
    public function add(RuleInterface $rule, CartHistoryInterface $cartHistory);

    /**
     * Cancel email
     *
     * @param QueueInterface $queue
     * @return bool
     * @throws LocalizedException If validation fails
     */
    public function cancel(QueueInterface $queue);

    /**
     * Cancel email by id
     *
     * @param int $queueId
     * @return bool
     * @throws LocalizedException If validation fails
     */
    public function cancelById($queueId);

    /**
     * Send email
     *
     * @param QueueInterface $queue
     * @return bool
     * @throws LocalizedException
     */
    public function send(QueueInterface $queue);

    /**
     * Send test email
     *
     * @param RuleInterface $rule
     * @return mixed
     */
    public function sendTest(RuleInterface $rule);

    /**
     * Send email by id
     *
     * @param int $queueId
     * @return bool
     * @throws LocalizedException
     */
    public function sendById($queueId);

    /**
     * Get email preview
     *
     * @param QueueInterface $queue
     * @return PreviewInterface
     */
    public function getPreview(QueueInterface $queue);

    /**
     * Clear queue
     *
     * @param int $keepForDays
     * @return bool
     */
    public function clearQueue($keepForDays);

    /**
     * Send scheduled emails
     *
     * @param int|null $timestamp
     * @return bool
     */
    public function sendScheduledEmails($timestamp = null);
}
