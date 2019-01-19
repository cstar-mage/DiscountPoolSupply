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
 * @package     Plumrocket_Amp 2.x.x
 * @copyright   Copyright (c) 2017 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\Amp\Block\Page;

use \Plumrocket\Amp\Helper\Data as DataHelper;
use \Magento\Framework\View\Element\Template\Context;
use \Magento\Store\Model\ScopeInterface;
use \Magento\Framework\UrlInterface;

class Zopim extends \Magento\Framework\View\Element\Template
{
    const LIVECHAT_BASE_URL = "https://v2.zopim.com/widget/livechat.html";
    const DEFAUL_LANG = 'en';

    /**
     * Store manager
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Scope Config
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * Zopim Key
     * @var string
     */
    public $zopimKey;

    /**
     * @param Context $context
	 * @param DataHelper $dataHelper
     */
    public function __construct(Context $context, DataHelper $dataHelper) {
        $this->_dataHelper = $dataHelper;
        $this->_storeManager = $context->getStoreManager();
        $this->_scopeConfig = $context->getScopeConfig();

        if ($this->_dataHelper->getZopimEnabled()) {
            $this->zopimKey = $this->_dataHelper->getZopimKey();
        }

        parent::__construct($context);
    }

    /**
     * Retrieve language code by store current locale options
     * @param void
     * @return string
     */
    public function getLang()
    {
        $store = $this->_storeManager->getStore();
        $locale = $this->_scopeConfig->getValue(
            'general/locale/code',
            ScopeInterface::SCOPE_STORE,
            $store->getStoreId()
        );

        $localeInfo = explode('_', $locale);
        if (!empty($localeInfo[0])) {
            return $localeInfo[0];
        }

        return self::DEFAULT_LANG;
    }

    /**
     * Retrieve current host name
     * @param void
     * @return string
     */
    public function getHostname()
    {
        $store = $this->_storeManager->getStore();
        $baseUrl = filter_var($store->getBaseUrl(UrlInterface::URL_TYPE_LINK), FILTER_VALIDATE_URL);

        if ($baseUrl && ($hostname = parse_url($baseUrl, PHP_URL_HOST))) {
            return $hostname;
        }

        return null;
    }

    public function getWidgetUrl()
    {
        if ($this->zopimKey) {
            $queryData = [
                'key' => $this->zopimKey,
                'hostname' => $this->getHostname(),
                'lang' => $this->getLang(),
            ];

            return self::LIVECHAT_BASE_URL . '?' . urldecode(http_build_query($queryData));
        }

        return null;
    }

    public function getButtonLabel()
    {
        return $this->_dataHelper->getZopimButtonLabel();
    }
}
