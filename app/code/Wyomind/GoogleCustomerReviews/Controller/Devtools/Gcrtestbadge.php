<?php

namespace Wyomind\GoogleCustomerReviews\Controller\Devtools;

class Gcrtestbadge extends \Wyomind\GoogleCustomerReviews\Controller\Devtools
{

    public function execute()
    {
        $this->_coreRegistry->register('gcr_test_badge', true);
        return $this->_resultPageFactory->create();
    }

}
