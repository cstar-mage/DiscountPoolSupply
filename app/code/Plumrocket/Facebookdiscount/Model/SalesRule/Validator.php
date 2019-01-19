<?php
/**
 * Plumrocket Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End-user License Agreement
 * that is available through the world-wide-web at this URL:
 * http://wiki.plumrocket.net/wiki/EULA
 * If you are unable to obtain it through the world-wide-web, please
 * send an email to support@plumrocket.com so we can send you a copy immediately.
 *
 * @package Plumrocket_Facebook_Discount
 * @copyright Copyright (c) 2017 Plumrocket Inc. (http://www.plumrocket.com)
 * @license http://wiki.plumrocket.net/wiki/EULA End-user License Agreement
 */

namespace Plumrocket\Facebookdiscount\Model\SalesRule;

use Magento\Quote\Model\Quote\Address;
use Magento\SalesRule\Model\Rule;
use Magento\SalesRule\Model\Validator as RuleValidator;
use Plumrocket\Facebookdiscount\Helper\Data;

class Validator extends RuleValidator
{
    protected $myRule = false;
    protected $dataHelper;

    public function __construct(
        Data $dataHelper,
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\SalesRule\Model\ResourceModel\Rule\CollectionFactory $collectionFactory,
        \Magento\Catalog\Helper\Data $catalogData,
        \Magento\SalesRule\Model\Utility $utility,
        \Magento\SalesRule\Model\RulesApplier $rulesApplier,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \Magento\SalesRule\Model\Validator\Pool $validators,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->dataHelper = $dataHelper;
        parent::__construct(
            $context,
            $registry,
            $collectionFactory,
            $catalogData,
            $utility,
            $rulesApplier,
            $priceCurrency,
            $validators,
            $messageManager,
            $resource,
            $resourceCollection,
            $data
        );
    }

    protected function _getRules(Address $address = null)
    {
        $currentRules = parent::_getRules($address);

        $discount_amount = $this->dataHelper->hasActiveLike() ? $this->dataHelper->getDiscountAmount() : 0;

        if (!$this->dataHelper->moduleEnabled() || !$discount_amount) {
             return $currentRules;
        }

        if (!$this->myRule) {
            $this->myRule = $currentRules->getNewEmptyItem()->setData(
                [
                    'name'              => 'Facebook Discount Rule',
                    'description'       => 'Facebook Discount Rule',
                    'is_active'         => '1',
                    'simple_action'     => $this->dataHelper->getSimpleAction(),
                    'discount_amount'   => $discount_amount,
                    'store_labels'      => [
                        0 => __('Facebook Discount'),
                    ],
                    'coupon_type'       => Rule::COUPON_TYPE_NO_COUPON,
                    'from_date'         => '2017-05-09',
                    'to_date'           => '2018-05-09',
                    'uses_per_customer'     => '0',
                    'stop_rules_processing' => '0',
                    'is_advanced' => '1',
                    'product_ids' => null,
                    'discount_qty' => null,
                    'discount_step' => '0',
                    'apply_to_shipping' => '0',
                    'times_used' => '0',
                    'is_rss' => '1',
                    'use_auto_generation' => '0',
                    'uses_per_coupon' => '0',
                    'simple_free_shipping' => null,
                ]
            )->setId(439998);

            $removeRule = false;

            foreach ($currentRules as $rule) {
                if ($removeRule) {
                    $currentRules->removeItemByKey($rule->getId());
                    continue;
                }

                if ($rule->getStopRulesProcessing()) {
                    $removeRule = true;
                    $rule->setStopRulesProcessing(false);
                }
            }
        }

        $currentRules->removeItemByKey(439998)->addItem($this->myRule);

        return $currentRules;
    }
}
