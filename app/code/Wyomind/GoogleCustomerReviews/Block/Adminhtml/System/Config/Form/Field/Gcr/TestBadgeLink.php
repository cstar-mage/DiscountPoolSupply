<?php

namespace Wyomind\GoogleCustomerReviews\Block\Adminhtml\System\Config\Form\Field\Gcr;

class TestBadgeLink extends \Magento\Config\Block\System\Config\Form\Field
{

    protected $_backendHelper = null;
    protected $_urlBuilder = null;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_backendHelper = $backendHelper;
        $this->_urlBuilder = $context->getUrlBuilder();
    }

    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        unset($element);

        $urlTest = $this->_backendHelper->getUrl('googlecustomerreviews/devtools/gcrtestbadge/');
        $urlValidator = $this->_urlBuilder->getDirectUrl('googlecustomerreviews/devtools/gcrtestbadge/');

        $html = "<button id='gts-test-badbge-btn' onclick='javascript:GoogleCustomerReviews.testBadge(\"$urlTest\");return false;'>"
                . __('Go') . "</button>"
                . "<br/>"
                . "<textarea id='gcr-badge-test-page'></textarea>"
                . "<a target='_blank' id='GcrValidatorBadgeUrl' base='" . $urlValidator . "' href=''></a>";

        return $html;
    }
}
