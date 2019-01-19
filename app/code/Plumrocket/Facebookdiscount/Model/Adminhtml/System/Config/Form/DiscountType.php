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

namespace Plumrocket\Facebookdiscount\Model\Adminhtml\System\Config\Form;

class DiscountType implements \Magento\Framework\Option\ArrayInterface
{

    const FIXED_AMOUNT = 0;
    const PERCENT_AMOUNT = 1;

    public function toOptionArray()
    {
        $values = $this->toOptionHash();
        $result = [];

        foreach ($values as $key => $value) {
            $result[] = [
                'value'    => $key,
                'label'    => $value,
            ];
        }
        return $result;
    }

    public function toOptionHash()
    {
        return [
            self::FIXED_AMOUNT => __('Fixed amount discount for whole cart'),
            self::PERCENT_AMOUNT => __('Percent of product price discount'),
        ];
    }
}