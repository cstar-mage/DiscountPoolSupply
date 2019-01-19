<?php

namespace Wyomind\GoogleCustomerReviews\Model\Config\Source;

class BadgePosition implements \Magento\Framework\Option\ArrayInterface
{

    public function toOptionArray()
    {
        return [
            ['label' => 'BOTTOM_RIGHT', 'value' => 'BOTTOM_RIGHT'],
            ['label' => 'BOTTOM_LEFT', 'value' => 'BOTTOM_LEFT'],
            ['label' => 'USER_DEFINED', 'value' => 'USER_DEFINED']
        ];
    }
}
