<?php

namespace Wyomind\GoogleCustomerReviews\Controller\Adminhtml\Devtools;

class Gtstestbadge extends \Wyomind\GoogleCustomerReviews\Controller\Adminhtml\Devtools
{

    public function execute()
    {
        $this->_view->loadLayout();
        $resultRaw = $this->_resultRawFactory->create();
        $content = $this->_view->getLayout()->createBlock('Wyomind\GoogleCustomerReviews\Block\Gts\Badge')->setArea('frontend')->setTemplate('Wyomind_GoogleCustomerReviews::gts/badge.phtml')->toHtml();
        return $resultRaw->setContents($content);
    }
}
