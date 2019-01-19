<?php

namespace Wyomind\GoogleCustomerReviews\Block\Adminhtml\System\Config\Form\Field\Gcr;

class TestOrderLink extends \Magento\Config\Block\System\Config\Form\Field
{

    protected $_backendHelper = null;
    protected $_storeManager = null;
    protected $_urlBuilder = null;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_backendHelper = $backendHelper;
        $this->_storeManager = $context->getStoreManager();
        $this->_urlBuilder = $context->getUrlBuilder();
    }

    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        unset($element);
        $code = $this->getRequest()->getParam('website');
        $website = null;
        if (!empty($code)) {
            $website = $code;
        } else {
            $websites = $this->_storeManager->getWebsites();
            $ws = [];
            foreach ($websites as $website) {
                if ($website->getStoresCount() > 0) {
                    $ws[] = $website;
                }
            }
            if (count($ws) >= 1) {
                $tmp = $ws[0];
                $website = $this->_storeManager->getWebsite($tmp->getId());
                $website = $website->getId();
            } else {
                $website = null;
            }
        }

        $urlTest = $this->_backendHelper->getUrl('googlecustomerreviews/devtools/gcrtestorder/', ['website' => $website != null ? $website : -1]);
        $urlValidator = $this->_urlBuilder->getDirectUrl('googlecustomerreviews/devtools/gcrtestorder'.($website != null ? '/website/'.$website.'/' : ''));

        $html = "<input type='text' class='input-text' style='width:200px;' placeholder='".__("Order number")."' id='gcr-order-number'/>"
                . "<button id='gcr-test-badbge-btn' onclick='javascript:GoogleCustomerReviews.testOrder(\"$website\",\"$urlTest\");return false;'>"
                . __('Go') . "</button>"
                . "<br/>"
                . "<textarea id='gcr-badge-test-order'></textarea>"
                . "<a target='_blank' id='GcrValidatorOrderUrl' base='" . $urlValidator . "' href=''></a>";

        return $html;
    }
}
