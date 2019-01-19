<?php

namespace Wyomind\GoogleCustomerReviews\Block\Gcr;

class Order extends \Magento\Framework\View\Element\Template
{

    private $_order = null;
    private $_merchantId = null;
    private $_orderId = null;
    private $_email = null;
    private $_country = null;
    private $_deliveryDate = null;
    private $_optinStyle = null;
    private $_lang = null;
    public $_coreRegistry = null;
    private $_pcontext = null;
    private $_checkoutSession = null;
    private $_orderModel = null;
    public $coreHelper = null;
    public $error = false;
    private $_listProducts = false;
    private $_gtins = [];
    private $_gtinAttribute = "sku";
    protected $_productRepository = null;
    
    
    public function __construct(
    \Magento\Framework\View\Element\Template\Context $context,
            \Magento\Catalog\Block\Product\Context $pcontext,
            \Magento\Checkout\Model\Session $checkoutSession,
            \Magento\Sales\Model\Order $orderModel,
            \Wyomind\Core\Helper\Data $coreHelper,
            \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
            array $data = []
    )
    {

        $coreHelper->constructor($this, func_get_args());
        parent::__construct($context, $data);

        $this->coreHelper = $coreHelper;
        $this->_checkoutSession = $checkoutSession;
        $this->_orderModel = $orderModel;
        $this->_pcontext = $pcontext;
        // @exclude on
        $this->_coreRegistry = $this->_pcontext->getRegistry();
        // @exclude off
        $this->_productRepository = $productRepository;


        $program = $this->getRequest()->getParam('googlecustomerreviews_program_program');
        if ($program == null) {
            $program = $this->coreHelper->getStoreConfig('googlecustomerreviews/program/program');
        }

        if (($program == "gcr") || $this->getRequest()->getControllerName() == "devtools") {
            $this->setTemplate('Wyomind_GoogleCustomerReviews::gcr/badge.phtml');
            $this->_proceed();
        } else {
            $this->setTemplate('empty.phtml');
        }
    }

    private function _proceed()
    {

        $website = $this->_storeManager->getStore()->getWebsite();
        $orderId = $this->_checkoutSession->getLastOrderId();

        // dev tool will not work when developer mode is enabled
        if (getenv("MAGE_MODE") == "developer") {
            putenv("MAGE_MODE=default");
        }

        $order = null;

        if ($orderId) {

            $order = $this->_orderModel->load($orderId);
        } elseif ($this->getRequest()->getParam("order-number") != null) {
            $order = $this->_orderModel->loadByIncrementId($this->getRequest()->getParam("order-number"));
        } elseif ($this->getRequest()->getParam("id") != null && $this->getRequest()->getParam("id") != "") {
            $order = $this->_orderModel->loadByIncrementId($this->getRequest()->getParam("id"));
        }
        if ($this->getRequest()->getParam("order-number") != null) {
            $eta = $this->getRequest()->getParam("googlecustomerreviews_orders_eta");
            $useEdd = $this->getRequest()->getParam('googlecustomerreviews_orders_use_edd_module');
            $merchantId = $this->getRequest()->getParam('googlecustomerreviews_badge_gcr_merchant_id');
            $lang = $this->getRequest()->getParam('googlecustomerreviews_badge_gcr_lang');
            $optinStyle = $this->getRequest()->getParam('googlecustomerreviews_orders_gcr_optin_style');
            $listProducts = $this->getRequest()->getParam('googlecustomerreviews_orders_gcr_optin_list_products');
            $gtinAttribute = $this->getRequest()->getParam('googlecustomerreviews_orders_gcr_optin_gtin');
            
        } else {
            $eta = $website->getConfig("googlecustomerreviews/orders/eta");
            $useEdd = $website->getConfig('googlecustomerreviews/orders/use_edd_module');
            $merchantId = $website->getConfig('googlecustomerreviews/badge/gcr_merchant_id');
            $lang = $website->getConfig('googlecustomerreviews/badge/gcr_lang');
            $optinStyle = $website->getConfig('googlecustomerreviews/orders/gcr_optin_style');
            $listProducts = $website->getConfig('googlecustomerreviews/orders/gcr_optin_list_products');
            $gtinAttribute = $website->getConfig('googlecustomerreviews/orders/gcr_optin_gtin');
        }


        if ($order == null || $order->getEntityId() == null) {
            $this->error = __("Unable to load the order");
            return;
        }

        $orderId = $order->getIncrementId();


        $email = $order->getCustomerEmail();

        $address = "";

        if ($address = $order->getShippingAddress()) {
            $country = $address->getCountryId();
        } else {
            $country = $order->getBillingAddress()->getCountryId();
        }

        $dateTime = $order->getEstimatedDeliveryDateDatetime();
        if ($this->coreHelper->moduleIsEnabled("Wyomind_EstimatedDeliveryDate") && $useEdd && $dateTime != "") {
            $date = new \Datetime();
            $date->setTimestamp(strtotime($dateTime));
            $deliveryDate = $date->format('Y-m-d');
        } else {
            $date = new \Datetime();
            $date->setTimestamp(strtotime($order->getCreatedAt()));
            $add_days = $eta;
            if ($add_days != '' && $add_days > 0) {
                $date->add(new \DateInterval('P' . $add_days . 'D'));
            } // add x day to the order date
            $deliveryDate = $date->format('Y-m-d');
        }
        
        $orderItems = $order->getAllVisibleItems();
        $gtins = [];
        foreach ($orderItems as $item) {

            if ($item->getData("product_type") == "configurable") {
                if (is_array($item->getData("product_options"))) {
                    $simpleInfo = ($item->getData("product_options"));
                } else {
                    $simpleInfo = unserialize($item->getData("product_options"));
                }
                $sku = $simpleInfo["simple_sku"];
                $product = $this->_productRepository->get($sku);
            } else {
                $product = $this->_productRepository->getById($item->getProductId());
            }
            if ($product->getData($gtinAttribute) != null) {
                $gtins[] = array("gtin" => $product->getData($gtinAttribute));
            }
            
        }


        $this->_order = $order;
        $this->_merchantId = $merchantId;
        $this->_orderId = $orderId;
        $this->_email = $email;
        $this->_country = $country;
        $this->_deliveryDate = $deliveryDate;
        $this->_optinStyle = $optinStyle;
        $this->_lang = $lang;
        $this->_listProducts = $listProducts;
        $this->_gtinAttribute = $gtinAttribute;
        $this->_gtins = $gtins;
    }
    
    
    public function getListProducts() {
        return $this->_listProducts;
    }
    
    public function getGtins() {
        return $this->_gtins;
    }

    public function isFrontendTest()
    {
        return $this->_coreRegistry->registry("gcr_test_order");
    }

    public function getItems()
    {
        return $this->_items;
    }

    public function getOrder()
    {
        return $this->_order;
    }

    public function getOrderId()
    {
        return $this->_orderId;
    }

    public function getDomain()
    {
        return $this->_domain;
    }

    public function getLang()
    {
        return $this->_lang;
    }

    public function getEmail()
    {
        return $this->_email;
    }

    public function getCountry()
    {
        return $this->_country;
    }

    public function getDeliveryDate()
    {
        return $this->_deliveryDate;
    }

    public function getMerchantId()
    {
        return $this->_merchantId;
    }

    public function getOptinStyle()
    {
        return $this->_optinStyle;
    }

}
