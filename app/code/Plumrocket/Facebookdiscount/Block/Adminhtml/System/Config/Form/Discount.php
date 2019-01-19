<?php
/**
 * Plumrocket Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End-user License Agreement
 * that is available through the world-wide-web at this URL:
 * http://wiki.plumrocket.net/wiki/EULA
 * If you are unable to obtain it through the world-wide-web, please
 * send an email to support@plumrocket.com so we can send you a copy immediately.
 *
 * @package Plumrocket_Facebook_Discount
 * @copyright Copyright (c) 2017 Plumrocket Inc. (http://www.plumrocket.com)
 * @license http://wiki.plumrocket.net/wiki/EULA End-user License Agreement
 */

namespace Plumrocket\Facebookdiscount\Block\Adminhtml\System\Config\Form;

use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;

class Discount extends Field
{

    public function __construct(
        \Magento\Store\Model\StoreFactory $storeFactory,
        \Magento\Backend\Block\Template\Context $context,
        array $data = []
    ) {
        $this->storeFactory = $storeFactory;
        parent::__construct($context, $data);
    }

    protected function _getElementHtml(AbstractElement $element)
    {
        $storeCode = $this->_request->getParam('store');
        if (!$storeCode) {
            $currency = $this->_storeManager->getStore()->getBaseCurrencyCode();
        } else {
            $store = $this->storeFactory->create()->load($storeCode);
            $currency = $store->getBaseCurrencyCode();
        }
        $comment = str_replace('%currency%', $currency, $element->getComment());
        $element->setComment($comment);
        return parent::_getElementHtml($element);
    }
}
