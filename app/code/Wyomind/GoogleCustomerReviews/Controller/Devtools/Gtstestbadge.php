<?php

namespace Wyomind\GoogleCustomerReviews\Controller\Devtools;

class Gtstestbadge extends \Wyomind\GoogleCustomerReviews\Controller\Devtools
{

    public function execute()
    {
        if ($this->getRequest()->getParam('id') == "") {
            $resultRaw = $this->_resultRawFactory->create();
            return $resultRaw->setContents(__("No product to load"));
        }
        $product = $this->_productModel->load($this->_productModel->getIdBySku($this->getRequest()->getParam('id')));
        $this->_coreRegistry->register('product', $product);
        $this->_coreRegistry->register('current_product', $product);
        $this->_coreRegistry->register('gts_test_badge', true);
        return $this->_resultPageFactory->create();
    }
}
