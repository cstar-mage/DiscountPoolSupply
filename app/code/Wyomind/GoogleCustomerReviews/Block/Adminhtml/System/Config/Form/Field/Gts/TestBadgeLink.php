<?php

namespace Wyomind\GoogleCustomerReviews\Block\Adminhtml\System\Config\Form\Field\Gts;

class TestBadgeLink extends \Magento\Config\Block\System\Config\Form\Field
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

        $urlTest = $this->_backendHelper->getUrl('googlecustomerreviews/devtools/gtstestbadge/', ['website' => $website != null ? $website : -1]);
        $urlValidator = $this->_urlBuilder->getDirectUrl('googlecustomerreviews/devtools/gtstestbadge/', ['website' => $website != null ? $website : -1]);

        $html = "<input type='text' class='input-text' style='width:200px;' placeholder='".__("Product sku")."' id='product-sku'/>"
                . "<button id='gts-test-badbge-btn' onclick='javascript:GoogleTrustedStores.testBadge(\"$website\",\"$urlTest\");return false;'>"
                . __('Go') . "</button>"
                . "<br/>"
                . "<textarea id='gts-badge-test-page'></textarea>"
                . "<a target='_blank' id='GtsValidatorBadgeUrl' base='" . $urlValidator . "' href=''></a>";

        return $html;
    }
}
