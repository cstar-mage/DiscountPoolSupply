<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Acr\Api;

use Aheadworks\Acr\Api\Data\RuleInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Rule CRUD interface
 * @api
 */
interface RuleRepositoryInterface
{
    /**
     * Save rule
     *
     * @param RuleInterface $rule
     * @return RuleInterface
     * @throws LocalizedException If validation fails
     */
    public function save(RuleInterface $rule);

    /**
     * Retrieve rule
     *
     * @param int $ruleId
     * @return RuleInterface
     * @throws NoSuchEntityException If rule does not exist
     */
    public function get($ruleId);

    /**
     * Retrieve rules matching the specified criteria
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return \Aheadworks\Acr\Api\Data\RuleSearchResultsInterface
     * @throws LocalizedException
     */
    public function getList(SearchCriteriaInterface $searchCriteria);

    /**
     * Delete rule
     *
     * @param RuleInterface $rule
     * @return bool true on success
     * @throws NoSuchEntityException If rule does not exist
     */
    public function delete(RuleInterface $rule);

    /**
     * Delete rule by id
     *
     * @param int $ruleId
     * @return bool true on success
     * @throws NoSuchEntityException If rule does not exist
     */
    public function deleteById($ruleId);
}
