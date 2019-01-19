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

namespace Plumrocket\Facebookdiscount\Block;

use Magento\Framework\Data\Form\FormKey;
use Magento\Framework\View\Element\Template as ViewTemplate;
use Magento\Framework\View\Element\Template\Context;
use Plumrocket\Facebookdiscount\Helper\Data;
use Magento\Framework\Pricing\Helper\Data as PriceHelper;
use Magento\Checkout\Helper\Cart as CheckoutCartHelper;
use Magento\Framework\Json\Helper\Data as JsonHelper;
use Magento\Store\Model\Store;

class Like extends ViewTemplate
{
    protected $formKey;
    protected $dataHelper;
    protected $priceHelper;
    protected $checkoutCartHelper;
    protected $jsonHelper;
    protected $store;

    public function __construct(
        FormKey $formKey,
        Data $dataHelper,
        PriceHelper $priceHelper,
        CheckoutCartHelper $checkoutCartHelper,
        JsonHelper $jsonHelper,
        Store $store,
        Context $context,
        array $data = []
    ) {
        $this->formKey = $formKey;
        $this->dataHelper = $dataHelper;
        $this->priceHelper = $priceHelper;
        $this->checkoutCartHelper = $checkoutCartHelper;
        $this->jsonHelper = $jsonHelper;
        $this->store = $store;
        parent::__construct($context, $data);
    }

    protected function _toHtml()
    {
        if (($this->dataHelper->hasLike() || $this->dataHelper->hasDislike())
            || !$this->dataHelper->moduleEnabled()
            || !$this->getPageUrl()
            || !$this->getApiKey()
            || ($this->checkoutCartHelper->getItemsCount() == 0)
        ) {
            $this->setTemplate('empty.phtml');
        }
        return parent::_toHtml();
    }

    public function getConfig(array $config = [])
    {
        $config['facebookLikeUrl'] = $this->getUrl('facebookdiscount/like', ['form_key' => $this->getFormKey()]);
        $config['removeLikeUrl'] = $this->getRemoveLikeUrl();

        return $this->jsonHelper->jsonEncode($config);
    }

    public function getApiKey()
    {
        return $this->dataHelper->getApiKey();
    }

    public function getPageUrl()
    {
        return $this->dataHelper->getPageUrl();
    }

    public function getDiscountAmount($format = true, $includeContainer = false)
    {
        $discountAmount = $this->dataHelper->getDiscountAmount();
        switch ($this->dataHelper->getDiscountType()) {
            case 0 : return $this->priceHelper->currency($discountAmount, $format, $includeContainer); break;
            case 1 : return $discountAmount . '%'; break;
        }
    }

    public function getRemoveLikeUrl()
    {
        $fUrl = $this->getUrl('facebookdiscount/like/removeLike/');
        if ($this->store->isCurrentlySecure()) {
            $fUrl = str_replace('http://', 'https://', $fUrl);
        }
        return $fUrl;
    }

    public function getDisplayDiscount()
    {
        return (bool)$this->dataHelper->hasActiveLike();
    }

    public function getFormKey()
    {
        return $this->formKey->getFormKey();
    }

    public function getQueueUrl()
    {
        return $this->getUrl('facebookdiscount/callbackurl/queue');
    }
}
