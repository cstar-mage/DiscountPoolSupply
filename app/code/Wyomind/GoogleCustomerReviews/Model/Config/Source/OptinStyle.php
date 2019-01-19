<?php

namespace Wyomind\GoogleCustomerReviews\Model\Config\Source;

class OptinStyle implements \Magento\Framework\Option\ArrayInterface
{

    public function toOptionArray()
    {
        return [
            ['label' => 'CENTER_DIALOG', 'value' => 'CENTER_DIALOG'],
            ['label' => 'BOTOM_RIGHT_DIALOG', 'value' => 'BOTOM_RIGHT_DIALOG'],
            ['label' => 'BOTTOM_LEFT_DIALOG', 'value' => 'BOTTOM_LEFT_DIALOG'],
            ['label' => 'TOP_RIGHT_DIALOG', 'value' => 'TOP_RIGHT_DIALOG'],
            ['label' => 'TOP_LEFT_DIALOG', 'value' => 'TOP_LEFT_DIALOG'],
            ['label' => 'BOTTOM_TRAY', 'value' => 'BOTTOM_TRAY'],
        ];
     
    }

}
