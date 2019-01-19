<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Acr\Api;

use Aheadworks\Acr\Api\Data\CartHistoryInterface;

/**
 * Interface CartHistoryManagementInterface
 * @package Aheadworks\Acr\Api
 */
interface CartHistoryManagementInterface
{
    /**
     * timeout after which abandoned cart event may trigger
     */
    const CART_TRIGGER_TIMEOUT = 3600;

    /**
     * Process cart history
     *
     * @param CartHistoryInterface $cartHistory
     * @return bool
     */
    public function process(CartHistoryInterface $cartHistory);

    /**
     * Process unprocessed cart history items
     *
     * @param int $maxItemsCount
     * @return bool
     */
    public function processUnprocessedItems($maxItemsCount);

    /**
     * Add cart data to cart history
     *
     * @param array $cartData
     * @return bool
     */
    public function addCartToCartHistory($cartData);
}
