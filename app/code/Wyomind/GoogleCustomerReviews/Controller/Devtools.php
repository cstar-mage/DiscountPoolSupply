<?php

namespace Wyomind\GoogleCustomerReviews\Controller;

abstract class Devtools extends \Magento\Framework\App\Action\Action
{

    protected $_resultPageFactory = null;
    protected $_coreRegistry = null;
    protected $_productModel = null;
    protected $_orderModel = null;
    protected $_resultRawFactory = null;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Catalog\Block\Product\Context $pcontext,
        \Magento\Catalog\Model\Product $productModel,
        \Magento\Sales\Model\Order $orderModel,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Controller\Result\RawFactory $resultRawFactory
    ) {
        $this->_resultPageFactory = $resultPageFactory;
        $this->_coreRegistry = $pcontext->getRegistry();
        $this->_productModel = $productModel;
        $this->_orderModel = $orderModel;
        $this->_resultRawFactory = $resultRawFactory;
        parent::__construct($context);
    }
    
    abstract public function execute();
}
