<?php
/**
 * Copyright © CyberSolutionsLLC. All rights reserved.
 */
namespace CyberSolutionsLLC\Sales\Plugin;

class OrderCustomerExtractor
{
    /**
     * {@inheritdoc}
     */
    public function afterExtract(
        \Magento\Sales\Model\Order\OrderCustomerExtractor $subject,
        $result
    ) {
        if (!$result->getDob()) {
            $result->setDob('1900-01-01');
        }
        
        return $result;
    }
}
