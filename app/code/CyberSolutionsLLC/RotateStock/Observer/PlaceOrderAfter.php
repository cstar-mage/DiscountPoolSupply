<?php
/**
 * Copyright © CyberSolutionsLLC. All rights reserved.
 */
namespace CyberSolutionsLLC\RotateStock\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;

use CyberSolutionsLLC\RotateStock\Helper\Config as ConfigHelper;

class PlaceOrderAfter implements ObserverInterface
{
    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var ConfigHelper
     */
    private $configHelper;

    /**
     * @param ProductRepositoryInterface $productRepository
     * @param ConfigHelper $configHelper
     */
    public function __construct(
        ProductRepositoryInterface $productRepository,
        ConfigHelper $configHelper
    ) {
        $this->productRepository = $productRepository;
        $this->configHelper = $configHelper;
    }

    /**
     * @return void
     */
    public function execute(Observer $observer)
    {
        $event = $observer->getEvent();
        $quote = $event->getQuote();
        $items = $quote->getAllVisibleItems();
        $storeId = 0;

        foreach ($items as $item) {
            if ($productId = (int)$item->getProductId()) {
                try {
                    $product = $this->productRepository->getById($productId, false, $storeId);
                    $_rotateConfig = $this->configHelper->getRotateConfigForProduct($product);
                    if ($_rotateConfig['enabled']) {
                        $newQty = $_rotateConfig['only_x_left'] - $item->getQty();
                        if ($_rotateConfig['min_qty_threshold'] >= $newQty) {
                            $newQty = $_rotateConfig['reset_qty'];
                        }
                        $product->setData('rs_only_x_left', $newQty);
                        $product->setStoreId($storeId);
                        $product->setForceReindexEavRequired(true);
                        $product->save();
                    }
                } catch (\Exception $e) {
                    //nothing
                }
            }
        }
    }
}
