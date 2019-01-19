<?php

namespace Wyomind\GoogleCustomerReviews\Controller\Devtools;

class Gtstestorder extends \Wyomind\GoogleCustomerReviews\Controller\Devtools
{
    
    public function execute()
    {
        if ($this->getRequest()->getParam('id') == "") {
            $resultRaw = $this->_resultRawFactory->create();
            return $resultRaw->setContents(__("No order to load"));
        }
        $order = $this->_orderModel->loadByIncrementId($this->getRequest()->getParam('id'));
        $this->_coreRegistry->register('order', $order);
        $this->_coreRegistry->register('gts_test_order', true);
        return $this->_resultPageFactory->create();
    }
}
