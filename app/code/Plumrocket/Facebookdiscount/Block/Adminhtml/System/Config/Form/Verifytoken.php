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

class Verifytoken extends Field
{
    /**
     * @var \Plumrocket\Facebookdiscount\Helper\Data
     */
    protected $helper;

    /**
     * @param \Magento\Backend\Block\Template\Context  $context
     * @param \Plumrocket\Facebookdiscount\Helper\Data $helper
     * @param array                                    $data
     */
    public function __construct(
        \Plumrocket\Facebookdiscount\Helper\Data $helper,
        \Magento\Backend\Block\Template\Context $context,
        array $data = []
    ) {
        $this->helper = $helper;
        parent::__construct($context, $data);
    }

    /**
     * {@inheritdoc}
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        return '<input id="'. $element->getHtmlId() .'" type="text" name=""
            value="'. $this->helper->getVerifyToken() .'" class="input-text"
            style="background-color: #EEE; color: #999;"
            readonly="readonly" />';
    }
}
