<?php

namespace Wyomind\GoogleCustomerReviews\Block\Gts;

class Order extends \Magento\Framework\View\Element\Template
{

    private $_order = null;
    private $_orderId = null;
    private $_domain = null;
    private $_email = null;
    private $_country = null;
    private $_currencyCode = null;
    private $_orderTotal = null;
    private $_orderDiscount = null;
    private $_orderShipping = null;
    private $_orderTax = null;
    private $_orderDeliveryDate = null;
    private $_orderShipDate = null;
    private $_hasPreorder = null;
    private $_hasDigital = null;
    private $_items = array();
    protected $_coreRegistry = null;
    protected $_productRepository = null;
    protected $_productCollectionFactory = null;
    public $pcontext = null;
    public $checkoutSession = null;
    public $orderModel = null;
    public $coreHelper = null;
    public $error = false;

    public function __construct(
    \Magento\Framework\View\Element\Template\Context $context,
            \Magento\Catalog\Block\Product\Context $pcontext,
            \Magento\Checkout\Model\Session $checkoutSession,
            \Magento\Sales\Model\Order $orderModel,
            \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
            \Wyomind\Core\Helper\Data $coreHelper,
            \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
            array $data = []
    )
    {
        $coreHelper->constructor($this, func_get_args());
        parent::__construct($context, $data);
        $this->pcontext = $pcontext;
        $this->checkoutSession = $checkoutSession;
        $this->orderModel = $orderModel;
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->coreHelper = $coreHelper;
        $this->_productRepository = $productRepository;

        
        // @exclude on
        $this->_coreRegistry = $this->pcontext->getRegistry();
        // @exclude off

        $program = $this->getRequest()->getParam('googlecustomerreviews_program_program');
        if ($program == null) {
            $program = $this->coreHelper->getStoreConfig('googlecustomerreviews/program/program');
        }

        if ($program == "gts" || $this->getRequest()->getControllerName() == "devtools") {
            $this->setTemplate('Wyomind_GoogleCustomerReviews::gts/order.phtml');
            $this->_proceed();
        } else {
            $this->setTemplate('empty.phtml');
        }
    }

    private function _proceed()
    {
        $website = $this->_storeManager->getStore()->getWebsite();
        $orderId = $this->checkoutSession->getLastOrderId();

        // dev tool will not work when developer mode is enabled
        if (getenv("MAGE_MODE") == "developer") {
            putenv("MAGE_MODE=default");
        }

        $order = null;
        if ($orderId) {
            $order = $this->orderModel->load($orderId);
        } elseif ($this->getRequest()->getParam("order-number") != null) {
            $order = $this->orderModel->loadByIncrementId($this->getRequest()->getParam("order-number"));
        } elseif ($this->getRequest()->getParam("id") != null && $this->getRequest()->getParam("id") != "") {
            $order = $this->orderModel->loadByIncrementId($this->getRequest()->getParam("id"));
        }

        if ($this->getRequest()->getParam("order-number") != null) {
            $etaShip = $this->getRequest()->getParam("googlecustomerreviews_orders_eta_ship");
            $eta = $this->getRequest()->getParam("googlecustomerreviews_orders_eta");
            $useEdd = $this->getRequest()->getParam("googlecustomerreviews_orders_use_edd_module");
            $idTemplate = $this->getRequest()->getParam("googlecustomerreviews_badge_gts_gs_product_id");
            $country = $this->getRequest()->getParam("googlecustomerreviews_badge_gts_country");
            $gtsId = $this->getRequest()->getParam("googlecustomerreviews_badge_gts_gts_id");
            $position = $this->getRequest()->getParam("googlecustomerreviews_badge_position");
            $language = $this->getRequest()->getParam("googlecustomerreviews_badge_gts_language");
            $gbId = $this->getRequest()->getParam("googlecustomerreviews_badge_gts_gb_id");
            $css = $this->getRequest()->getParam("googlecustomerreviews_badge_css");
        } else {
            $etaShip = $website->getConfig("googlecustomerreviews/orders/gts_eta_ship");
            $eta = $website->getConfig("googlecustomerreviews/orders/eta");
            $useEdd = $website->getConfig("googlecustomerreviews/orders/use_edd_module");
            $idTemplate = $website->getConfig("googlecustomerreviews/badge/gts_gs_product_id");
            $country = $website->getConfig("googlecustomerreviews/badge/gts_country");
            $gtsId = $website->getConfig("googlecustomerreviews/badge/gts_orders_gts_id");
            $position = $website->getConfig("googlecustomerreviews/badge/position");
            $language = $website->getConfig("googlecustomerreviews/badge/gts_language");
            $gbId = $website->getConfig("googlecustomerreviews/badge/gts_gb_id");
            $css = $website->getConfig("googlecustomerreviews/badge/css");
        }

        if ($order == null || $order->getEntityId() == null) {
            $this->error = __("Unable to load the order");
            return;
        }

        $domain = parse_url($this->_storeManager->getStore()->getBaseUrl());
        $domain = $domain["host"];
        $email = $order->getCustomerEmail();
        $address = "";

        if ($address = $order->getShippingAddress()) {
            $country = $address->getCountryId();
        } else {
            $country = $order->getBillingAddress()->getCountryId();
        }
        $currencyCode = $order->getOrderCurrencyCode();

        // amounts
        $orderTotal = sprintf("%01.2F", $order->getGrandTotal());
        $orderDiscount = sprintf("%01.2F", $order->getDiscountAmount());
        $orderShipping = sprintf("%01.2F", $order->getShippingAmount());
        $orderTax = sprintf("%01.2F", $order->getTaxAmount());

        // shipment date
        $date = new \Datetime();
        $date->setTimestamp(time($order->getCreatedAt()));
        $addDays = $etaShip;
        if ($addDays != "" && $addDays > 0) {
            $date->add(new \DateInterval("P" . $addDays . "D")); // add x day to the order date
        }
        $shipDate = $date->format("Y-m-d");

        // delivery date

        /* if ($this->coreHelper->moduleIsEnabled("Wyomind_EstimatedDeliveryDate") && $useEdd && $json != "") {
          $date = new Datetime();
          $date->setTimestamp($json->time);
          $deliveryDate = $date->format('Y-m-d');
          } else { */
        $date = new \Datetime();
        $date->setTimestamp(time($order->getCreatedAt()));
        $addDays = $eta;
        if ($addDays != "" && $addDays > 0) {
            $date->add(new \DateInterval("P" . $addDays . "D")); // add x day to the order date
        }
        $deliveryDate = $date->format("Y-m-d");
        //}
        $orderItems = $order->getAllVisibleItems();

        // has preorder ?
        $hasPreorder = "N";
        foreach ($orderItems as $item) {
            if ($item->getQtyBackordered() > 0) {
                $thasPreorder = "Y";
                break;
            }
        }

        // has digital ?
        $hasDigital = "N";
        foreach ($orderItems as $item) {
            if ($item->getIsVirtual() > 0) {
                $hasDigital = "Y";
                break;
            }
        }

        // ordered items
        $items = [];

        foreach ($orderItems as $item) {
            $itemInfo = [
                "name" => $this->escapeHtml($item->getName()),
                "price" => sprintf("%01.2F", $item->getPriceInclTax()),
                "qty_ordered" => number_format(sprintf($item->getIsQtyDecimal() ? "%F" : "%d", $item->getQtyOrdered()), 0, ".", ""),
            ];

            if ($item->getData("product_type") == "configurable") {
                if (is_array($item->getData("product_options"))) {
                    $simpleInfo = ($item->getData("product_options"));
                } else {
                    $simpleInfo = unserialize($item->getData("product_options"));
                }

                $itemInfo["simple_sku"] = $simpleInfo["simple_sku"];
                $itemInfo["name"] = $simpleInfo["simple_name"];
                $itemInfo["product_id"] = $this->_productRepository->get($itemInfo["simple_sku"])->getId();
            } else {
                $itemInfo["product_id"] = $item->getProductId();
            }

            $itemInfo["google_shopping"] = [
                "gbase_account_id" => $gbId,
                "gbase_country" => $country,
                "gbase_language" => $language,
                "gbase_id" => -1
            ];

            $items[$itemInfo["product_id"]] = $itemInfo;
        }

        $collection = $this->_productCollectionFactory->create()
                ->addAttributeToSelect([$idTemplate], true)
                ->addAttributeToFilter("entity_id", ["in" => array_keys($items)]);


        foreach ($collection as $product) {
            $this->_items[$product->getId()]["google_shopping"]['gbase_id'] = $product->getData($idTemplate);
        }

        $this->_order = $order;
        $this->_orderId = $orderId;
        $this->_domain = $domain;
        $this->_email = $email;
        $this->_country = $country;
        $this->_currencyCode = $currencyCode;
        $this->_orderTotal = $orderTotal;
        $this->_orderDiscount = $orderDiscount;
        $this->_orderShipping = $orderShipping;
        $this->_orderTax = $orderTax;
        $this->_orderDeliveryDate = $deliveryDate;
        $this->_orderShipDate = $shipDate;
        $this->_hasPreorder = $hasPreorder;
        $this->_hasDigital = $hasDigital;
        $this->_items = $items;
    }

    public function isFrontendTest()
    {
        return $this->_coreRegistry->registry("gts_test_order");
    }

    function getOrder()
    {
        return $this->_order;
    }

    function getOrderId()
    {
        return $this->_orderId;
    }

    function getDomain()
    {
        return $this->_domain;
    }

    function getEmail()
    {
        return $this->_email;
    }

    function getCountry()
    {
        return $this->_country;
    }

    function getCurrencyCode()
    {
        return $this->_currencyCode;
    }

    function getOrderTotal()
    {
        return $this->_orderTotal;
    }

    function getOrderDiscount()
    {
        return $this->_orderDiscount;
    }

    function getOrderShipping()
    {
        return $this->_orderShipping;
    }

    function getOrderTax()
    {
        return $this->_orderTax;
    }

    function getOrderDeliveryDate()
    {
        return $this->_orderDeliveryDate;
    }

    function getOrderShipDate()
    {
        return $this->_orderShipDate;
    }

    function getHasPreorder()
    {
        return $this->_hasPreorder;
    }

    function getHasDigital()
    {
        return $this->_hasDigital;
    }

    function getItems()
    {
        return $this->_items;
    }

}
