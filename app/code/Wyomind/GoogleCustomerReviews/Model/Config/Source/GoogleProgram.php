<?php

namespace Wyomind\GoogleCustomerReviews\Model\Config\Source;

class GoogleProgram implements \Magento\Framework\Option\ArrayInterface
{

    public function toOptionArray()
    {
        return [
            ['label' => 'Google Trusted Stores', 'value' => 'gts'],
            ['label' => 'Google Customer Reviews', 'value' => 'gcr']
        ];
    }
}
