<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Acr\Model;

use Aheadworks\Acr\Api\Data\RuleInterface;
use Aheadworks\Acr\Api\Data\QueueInterface;
use Aheadworks\Acr\Api\Data\CartHistoryInterface;
use Aheadworks\Acr\Api\RuleRepositoryInterface;
use Aheadworks\Acr\Api\CartHistoryRepositoryInterface;
use Aheadworks\Acr\Model\Template\TransportBuilder;
use Aheadworks\Acr\Model\Exception\TestRecipientNotSpecified;
use Aheadworks\Acr\Api\Data\CouponVariableInterface;
use Aheadworks\Acr\Api\CouponVariableManagementInterface;
use Magento\Framework\Exception\MailException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\ResourceModel\Quote\CollectionFactory as QuoteCollectionFactory;
use Magento\Customer\Model\Customer;
use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory as CustomerCollectionFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\ObjectManagerInterface;

/**
 * Class Sender
 * @package Aheadworks\Acr\Model
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Sender
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var TransportBuilder
     */
    private $transportBuilder;

    /**
     * @var RuleRepositoryInterface
     */
    private $ruleRepository;

    /**
     * @var CartHistoryRepositoryInterface
     */
    private $cartHistoryRepository;

    /**
     * @var CartRepositoryInterface
     */
    private $cartRepository;

    /**
     * @var QuoteCollectionFactory
     */
    private $quoteCollectionFactory;

    /**
     * @var CustomerCollectionFactory
     */
    private $customerCollectionFactory;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var CouponVariableManagementInterface
     */
    private $couponVariableManager;

    /**
     * @param Config $config
     * @param TransportBuilder $transportBuilder
     * @param RuleRepositoryInterface $ruleRepository
     * @param CartHistoryRepositoryInterface $cartHistoryRepository
     * @param CartRepositoryInterface $cartRepository
     * @param QuoteCollectionFactory $quoteCollectionFactory
     * @param CustomerCollectionFactory $customerCollectionFactory
     * @param StoreManagerInterface $storeManager
     * @param ObjectManagerInterface $objectManager
     * @param CouponVariableManagementInterface $couponVariableManager
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        Config $config,
        TransportBuilder $transportBuilder,
        RuleRepositoryInterface $ruleRepository,
        CartHistoryRepositoryInterface $cartHistoryRepository,
        CartRepositoryInterface $cartRepository,
        QuoteCollectionFactory $quoteCollectionFactory,
        CustomerCollectionFactory $customerCollectionFactory,
        StoreManagerInterface $storeManager,
        ObjectManagerInterface $objectManager,
        CouponVariableManagementInterface $couponVariableManager
    ) {
        $this->config = $config;
        $this->transportBuilder = $transportBuilder;
        $this->ruleRepository = $ruleRepository;
        $this->cartHistoryRepository = $cartHistoryRepository;
        $this->cartRepository = $cartRepository;
        $this->quoteCollectionFactory = $quoteCollectionFactory;
        $this->customerCollectionFactory = $customerCollectionFactory;
        $this->storeManager = $storeManager;
        $this->objectManager = $objectManager;
        $this->couponVariableManager = $couponVariableManager;
    }

    /**
     * Send test email
     *
     * @param QueueInterface $queueItem
     * @throws TestRecipientNotSpecified
     * @return $queueItem
     */
    public function sendTestEmail(QueueInterface $queueItem)
    {
        $recipientEmail = $this->config->getTestEmailRecipient($queueItem->getStoreId());
        if (!$recipientEmail) {
            throw new TestRecipientNotSpecified(
                __('Unable to send test email. Test Email Recipient is not specified.')
            );
        }
        $emailData = $this->getTestTemplateVarsData($queueItem->getStoreId());
        $recipientName = isset($emailData['customer_name']) ? $emailData['customer_name'] : '';
        /** @var RuleInterface $rule */
        $rule = $this->ruleRepository->get($queueItem->getRuleId());

        $result = $this->sendEmail(
            $recipientEmail,
            $recipientName,
            '[TEST EMAIL] '. $rule->getSubject(),
            $rule->getContent(),
            $queueItem->getStoreId(),
            $emailData
        );
        $queueItem
            ->setRecipientEmail($recipientEmail)
            ->setRecipientName($recipientName)
            ->setSavedSubject($result['subject'])
            ->setSavedContent($result['content']);

        return $queueItem;
    }

    /**
     * Send queue item
     *
     * @param QueueInterface $queueItem
     * @return QueueInterface
     * @throws MailException
     */
    public function sendQueueItem(QueueInterface $queueItem)
    {
        $storeId = $queueItem->getStoreId();
        if ($this->config->isTestModeEnabled($storeId)) {
            $recipientEmail = $this->config->getTestEmailRecipient();
        } else {
            $recipientEmail = $queueItem->getRecipientEmail();
        }

        if ($queueItem->getSavedContent()) {
            $this->sendEmail(
                $recipientEmail,
                $queueItem->getRecipientName(),
                $queueItem->getSavedSubject(),
                $queueItem->getSavedContent(),
                $queueItem->getStoreId()
            );
        } else {
            $emailData = $this->getTemplateVarsData($queueItem);
            /** @var CouponVariableInterface $couponVariable */
            $couponVariable = $this->getCouponVariable($queueItem->getRuleId(), $queueItem->getStoreId(), false);
            $emailData['coupon'] = $couponVariable;

            $rule = $this->ruleRepository->get($queueItem->getRuleId());
            $this->sendEmail(
                $recipientEmail,
                $queueItem->getRecipientName(),
                $rule->getSubject(),
                $rule->getContent(),
                $queueItem->getStoreId(),
                $emailData
            );
            $queueItem
                ->setSavedSubject($this->transportBuilder->getMessageSubject())
                ->setSavedContent($this->transportBuilder->getMessageContent());
            if ($this->config->isTestModeEnabled($storeId)) {
                $queueItem->setRecipientEmail($recipientEmail);
            }
        }
        return $queueItem;
    }

    /**
     * Get prepared content for preview (test data)
     *
     * @param int $storeId
     * @param string $subject
     * @param string $content
     * @return array ('subject' => ..., 'content' => ...)
     */
    public function getTestPreview($storeId, $subject, $content)
    {
        $recipientEmail = $this->config->getTestEmailRecipient($storeId) ?
            $this->config->getTestEmailRecipient($storeId) :
            '';
        $emailData = $this->getTestTemplateVarsData($storeId);
        $recipientName = isset($emailData['customer_name']) ? $emailData['customer_name'] : '';
        $this->transportBuilder
            ->setTemplateOptions([
                'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                'store' => $storeId
            ])
            ->setTemplateVars($emailData)
            ->setTemplateData([
                'template_subject' => $subject,
                'template_text' => $content
            ])
            ->addTo($recipientEmail, $recipientName)
        ;

        $this->transportBuilder->prepareForPreview();

        $result = [];
        $result['recipient_name'] = $recipientName;
        $result['recipient_email'] = $recipientEmail;
        $result['subject'] = $this->transportBuilder->getMessageSubject();
        $result['content'] = $this->transportBuilder->getMessageContent();
        return $result;
    }

    /**
     * Get prepared content for preview
     *
     * @param QueueInterface $queueItem
     * @param string $subject
     * @param string $content
     * @return array ('subject' => ..., 'content' => ...)
     */
    public function getPreview(QueueInterface $queueItem, $subject, $content)
    {
        $emailData = $this->getTemplateVarsData($queueItem);
        /** @var CouponVariableInterface $couponVariable */
        $couponVariable = $this->getCouponVariable($queueItem->getRuleId(), $queueItem->getStoreId(), true);
        $emailData['coupon'] = $couponVariable;

        $this->transportBuilder
            ->setTemplateOptions([
                'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                'store' => $queueItem->getStoreId()
            ])
            ->setTemplateVars($emailData)
            ->setTemplateData([
                'template_subject' => $subject,
                'template_text' => $content
            ])
            ->addTo($queueItem->getRecipientEmail(), $queueItem->getRecipientName())
        ;

        $this->transportBuilder->prepareForPreview();

        $result = [];
        $result['subject'] = $this->transportBuilder->getMessageSubject();
        $result['content'] = $this->transportBuilder->getMessageContent();
        return $result;
    }

    /**
     * Send email
     *
     * @param string $recipientEmail
     * @param string $recipientName
     * @param string $subject
     * @param string $content
     * @param int $storeId
     * @param array $emailData
     * @return array (['subject' => ..., 'content' => ...])
     */
    public function sendEmail($recipientEmail, $recipientName, $subject, $content, $storeId, $emailData = [])
    {
        $this->transportBuilder
            ->setTemplateOptions([
                'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                'store' => $storeId
            ])
            ->setTemplateVars($emailData)
            ->setTemplateData([
                'template_subject' => $subject,
                'template_text' => $content
            ])
            ->setFrom($this->config->getSender($storeId))
            ->addTo($recipientEmail, $recipientName)
        ;

        $transport = $this->transportBuilder->getTransport();
        $transport->sendMessage();

        $result = [];
        $result['subject'] = $this->transportBuilder->getMessageSubject();
        $result['content'] = $this->transportBuilder->getMessageContent();
        return $result;
    }

    /**
     * Get template variables data
     *
     * @param QueueInterface $queueItem
     * @return array
     * @throws LocalizedException
     */
    private function getTemplateVarsData(QueueInterface $queueItem)
    {
        try {
            /** @var CartHistoryInterface $historyItem */
            $historyItem = $this->cartHistoryRepository->get($queueItem->getCartHistoryId());
            $emailData = unserialize($historyItem->getCartData());

            try {
                /** @var CartInterface|Quote $cart */
                $cart = $this->cartRepository->get($emailData['entity_id']);
                $emailData['quote'] = $cart;
            } catch (NoSuchEntityException $e) {
                throw new LocalizedException(__("Event object is missing"));
            }

            if (isset($emailData['customer_id'])) {
                try {
                    /** @var Customer $customer */
                    $customer = $this->objectManager
                        ->create(Customer::class)
                        ->load($emailData['customer_id']);
                    $emailData['customer'] = $customer;
                } catch (NoSuchEntityException $e) {
                    // do nothing
                }
            }

            if (isset($emailData['customer'])) {
                $emailData['store'] = $this->storeManager->getStore($emailData['customer']->getStoreId());
            }
        } catch (NoSuchEntityException $e) {
            $emailData = [];
        }

        return $emailData;
    }

    /**
     * Get test template variables data
     *
     * @param int $storeId
     * @return array
     * @throws LocalizedException
     */
    private function getTestTemplateVarsData($storeId)
    {
        $emailData = [];
        // Create quote instance
        /** @var \Magento\Quote\Model\ResourceModel\Quote\Collection $collection */
        $collection = $this->quoteCollectionFactory->create();
        $collection
            ->addFilter('is_active', 1)
            ->getSelect()
            ->order(new \Zend_Db_Expr('RAND()'))
            ->limit(1)
        ;
        $quote = $collection->getFirstItem();
        $emailData['quote'] = $quote;

        // Create customer instance
        if ($quote->getCustomerId()) {
            /** @var Customer $customer */
            $customer = $this->objectManager
                ->create(Customer::class)
                ->load($quote->getCustomerId());
        } else {
            $customerCollection = $this->customerCollectionFactory->create();
            $customerCollection->getSelect()
                ->order(new \Zend_Db_Expr('RAND()'))
                ->limit(1)
            ;
            $customer = $customerCollection->getFirstItem();
        }
        $emailData['customer'] = $customer;

        // Create store instance
        $emailData['store'] = $this->storeManager->getStore($storeId);
        // Add required event data
        $customerData = [
            'email'  => $emailData['customer']->getEmail(),
            'store_id'  => $emailData['customer']->getStoreId(),
            'customer_group_id'  => $emailData['customer']->getGroupId(),
            'customer_firstname' => $emailData['customer']->getFirstname(),
            'customer_name' => $emailData['customer']->getName()
        ];

        /** @var CouponVariableInterface $couponVariable */
        $couponVariable = $this->couponVariableManager->getTestCouponVariable();
        $emailData['coupon'] = $couponVariable;

        return array_merge($emailData, $quote->getData(), $customerData);
    }

    /**
     * Get coupon variable
     *
     * @param int $ruleId
     * @param int $storeId
     * @param bool $isTest
     * @return CouponVariableInterface
     */
    private function getCouponVariable($ruleId, $storeId, $isTest = false)
    {
        if ($isTest) {
            /** @var CouponVariableInterface $couponVariable */
            $couponVariable = $this->couponVariableManager->getTestCouponVariable();
        } else {
            /** @var CouponVariableInterface $couponVariable */
            $couponVariable = $this->couponVariableManager->getCouponVariable($ruleId, $storeId);
        }
        return $couponVariable;
    }
}
