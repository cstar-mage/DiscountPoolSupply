<?php

namespace Wyomind\GoogleCustomerReviews\Model\Config\Source;

class UseEDD implements \Magento\Framework\Option\ArrayInterface
{

    private $_coreHelper = null;

    public function __construct(
    \Wyomind\Core\Helper\Data $coreHelper)
    {
        $this->_coreHelper = $coreHelper;
    }

    public function toOptionArray()
    {
        if ($this->_coreHelper->moduleIsEnabled("Wyomind_EstimatedDeliveryDate")) {
            return array(
                array('label' => __('Yes'), 'value' => '1'),
                array('label' => __('No'), 'value' => '0')
            );
        } else {
            return [
                ['label' => __('No'), 'value' => '0']
            ];
        }
    }

}
