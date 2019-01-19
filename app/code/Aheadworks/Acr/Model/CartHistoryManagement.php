<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Acr\Model;

use Aheadworks\Acr\Api\Data\CartHistoryInterface;
use Aheadworks\Acr\Api\Data\CartHistoryInterfaceFactory;
use Aheadworks\Acr\Api\CartHistoryManagementInterface;
use Aheadworks\Acr\Api\CartHistoryRepositoryInterface;
use Aheadworks\Acr\Api\Data\RuleInterface;
use Aheadworks\Acr\Api\QueueRepositoryInterface;
use Aheadworks\Acr\Api\QueueManagementInterface;
use Aheadworks\Acr\Api\RuleManagementInterface;
use Aheadworks\Acr\Api\Data\CartHistorySearchResultsInterface;
use Aheadworks\Acr\Api\Data\RuleSearchResultsInterface;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Psr\Log\LoggerInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\App\Emulation as AppEmulation;

/**
 * Class CartHistoryManagement
 * @package Aheadworks\Acr\Model
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class CartHistoryManagement implements CartHistoryManagementInterface
{
    /**
     * @var CartHistoryInterfaceFactory
     */
    private $cartHistoryFactory;

    /**
     * @var CartHistoryRepositoryInterface
     */
    private $cartHistoryRepository;

    /**
     * @var QueueRepositoryInterface
     */
    private $queueRepository;

    /**
     * @var QueueManagementInterface
     */
    private $queueManagement;

    /**
     * @var RuleManagementInterface
     */
    private $ruleManagement;

    /**
     * @var CartRepositoryInterface
     */
    private $cartRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var DateTime
     */
    private $dateTime;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var AppEmulation
     */
    private $appEmulation;

    /**
     * @param CartHistoryInterfaceFactory $cartHistoryFactory
     * @param CartHistoryRepositoryInterface $cartHistoryRepository
     * @param QueueRepositoryInterface $queueRepository
     * @param QueueManagementInterface $queueManagement
     * @param RuleManagementInterface $ruleManagement
     * @param CartRepositoryInterface $cartRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param DateTime $dateTime
     * @param LoggerInterface $logger
     * @param AppEmulation $appEmulation
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        CartHistoryInterfaceFactory $cartHistoryFactory,
        CartHistoryRepositoryInterface $cartHistoryRepository,
        QueueRepositoryInterface $queueRepository,
        QueueManagementInterface $queueManagement,
        RuleManagementInterface $ruleManagement,
        CartRepositoryInterface $cartRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        DateTime $dateTime,
        LoggerInterface $logger,
        AppEmulation $appEmulation
    ) {
        $this->cartHistoryFactory = $cartHistoryFactory;
        $this->cartHistoryRepository = $cartHistoryRepository;
        $this->queueRepository = $queueRepository;
        $this->queueManagement = $queueManagement;
        $this->ruleManagement = $ruleManagement;
        $this->cartRepository = $cartRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->dateTime = $dateTime;
        $this->logger = $logger;
        $this->appEmulation = $appEmulation;
    }

    /**
     * {@inheritdoc}
     */
    public function process(CartHistoryInterface $cartHistory)
    {
        /** @var CartInterface $cart */
        try {
            $cartData = unserialize($cartHistory->getCartData());
            $storeId = $cartData['store_id'];
            $this->appEmulation->startEnvironmentEmulation($storeId, 'frontend', true);
            $cart = $this->cartRepository->get($cartHistory->getReferenceId());
            $this->appEmulation->stopEnvironmentEmulation();
            if (!$cart->getIsActive()
                || $cart->getItemsCount() == 0
            ) {
                $this->cartHistoryRepository->delete($cartHistory);
            } else {
                /** @var RuleSearchResultsInterface $result */
                $result = $this->ruleManagement->validate($cartHistory);
                if ($result->getTotalCount() > 0) {
                    /** @var RuleInterface $rule */
                    foreach ($result->getItems() as $rule) {
                        $this->queueManagement->add($rule, $cartHistory);
                    }
                    $cartHistory->setProcessed(true);
                    $this->cartHistoryRepository->save($cartHistory);
                } else {
                    $this->cartHistoryRepository->delete($cartHistory);
                }
            }
        } catch (NoSuchEntityException $e) {
            $this->cartHistoryRepository->delete($cartHistory);
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function processUnprocessedItems($maxItemsCount)
    {
        $this->searchCriteriaBuilder
            ->addFilter(CartHistoryInterface::PROCESSED, false)
            ->setPageSize($maxItemsCount);

        /** @var CartHistorySearchResultsInterface $result */
        $result = $this->cartHistoryRepository->getList(
            $this->searchCriteriaBuilder->create()
        );
        $cartHistoryItems = $result->getItems();
        foreach ($cartHistoryItems as $cartHistoryItem) {
            try {
                $triggerAt = $this->dateTime->timestamp($cartHistoryItem->getTriggeredAt());
                $now = $this->dateTime->timestamp();
                if ($now - $triggerAt > self::CART_TRIGGER_TIMEOUT) {
                    $this->process($cartHistoryItem);
                }
            } catch (\Exception $e) {
                $this->logger->error($e);
            }
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function addCartToCartHistory($cartData)
    {
        if (!$this->validateCartData($cartData)) {
            return false;
        }

        $this->searchCriteriaBuilder
            ->addFilter(CartHistoryInterface::REFERENCE_ID, $cartData['entity_id']);

        /** @var CartHistorySearchResultsInterface $result */
        $result = $this->cartHistoryRepository->getList(
            $this->searchCriteriaBuilder->create()
        );
        $cartHistoryItems = $result->getItems();

        $cartHistory = reset($cartHistoryItems);
        if ($cartHistory) {
            if ($cartHistory->getProcessed()) {
                $this->queueRepository->deleteByCartHistoryId($cartHistory->getId());
                $this->cartHistoryRepository->delete($cartHistory);
                /** @var CartHistoryInterface $cartHistory */
                $cartHistory = $this->cartHistoryFactory->create();
            }
        } else {
            /** @var CartHistoryInterface $cartHistory */
            $cartHistory = $this->cartHistoryFactory->create();
        }

        $cartHistory
            ->setReferenceId($cartData['entity_id'])
            ->setCartData($this->getPreparedCartData($cartData))
            ->setTriggeredAt($this->dateTime->timestamp());

        $this->cartHistoryRepository->save($cartHistory);

        return true;
    }

    /**
     * Validation of cart data
     *
     * @param array $data
     * @return bool
     */
    private function validateCartData(array $data)
    {
        $dataKeysRequired = ['email', 'store_id', 'customer_group_id', 'customer_name'];
        foreach ($dataKeysRequired as $dataKey) {
            if (!array_key_exists($dataKey, $data)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Get prepared cart data
     *
     * @param array $data
     * @return string
     */
    private function getPreparedCartData(array $data)
    {
        foreach ($data as $key => $value) {
            if ((is_array($value) || is_object($value))) {
                unset($data[$key]);
            }

            if (isset($data[$key]) && preg_match("/\r\n|\r|\n/", $value)) {
                $data[$key] = preg_replace("/\r\n|\r|\n/", "", $value);
            }
        }
        return serialize($data);
    }
}
