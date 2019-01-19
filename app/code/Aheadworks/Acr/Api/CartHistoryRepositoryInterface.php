<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Acr\Api;

use Aheadworks\Acr\Api\Data\CartHistoryInterface;
use Aheadworks\Acr\Api\Data\CartHistorySearchResultsInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * CartHistory CRUD interface
 * @api
 */
interface CartHistoryRepositoryInterface
{
    /**
     * Save cart history
     *
     * @param CartHistoryInterface $cartHistory
     * @return CartHistoryInterface
     * @throws LocalizedException If validation fails
     */
    public function save(CartHistoryInterface $cartHistory);

    /**
     * Retrieve cart history
     *
     * @param int $cartHistoryId
     * @return CartHistoryInterface
     * @throws NoSuchEntityException If cart history does not exist
     */
    public function get($cartHistoryId);

    /**
     * Retrieve cart histories matching the specified criteria
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return CartHistorySearchResultsInterface
     * @throws LocalizedException
     */
    public function getList(SearchCriteriaInterface $searchCriteria);

    /**
     * Delete cart history
     *
     * @param CartHistoryInterface $cartHistory
     * @return bool true on success
     * @throws NoSuchEntityException If cart history does not exist
     */
    public function delete(CartHistoryInterface $cartHistory);

    /**
     * Delete cart history by id
     *
     * @param int $cartHistoryId
     * @return bool true on success
     * @throws NoSuchEntityException If cart history does not exist
     */
    public function deleteById($cartHistoryId);
}
