<?php

namespace Wyomind\GoogleCustomerReviews\Controller\Adminhtml;

abstract class Devtools extends \Magento\Backend\App\Action
{

    protected $_resultRawFactory = null;
    
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Controller\Result\RawFactory $resultRawFactory
    ) {
        $this->_resultRawFactory = $resultRawFactory;
        parent::__construct($context);
    }
    
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Wyomind_GoogleCustomerReviews::devtools');
    }
    
    abstract public function execute();
}
