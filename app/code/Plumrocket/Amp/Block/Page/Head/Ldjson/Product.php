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
 * @copyright   Copyright (c) 2016 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\Amp\Block\Page\Head\Ldjson;

use Magento\Store\Model\ScopeInterface;

class Product extends \Magento\Catalog\Block\Product\AbstractProduct
{
    /**
     * Default values
     */
    const DEFAULT_PRODUCT_NAME = 'Product name';
    const DEFAULT_PRODUCT_SHORT_DESCRIPTION = 'Product short description';
    const DEFAULT_PRODUCT_STATUS = 'OutStock';
    const DEFAULT_PRODUCT_PRICE_CURRENCY = 'USD';

    const PRODUCT_NAME_MAX_LEN = 32;
    const PRODUCT_SHORT_DESCRIPTION_MAX_LEN = 255;

    const PRODUCT_IMAGE_WIDTH = 720;
    const PRODUCT_IMAGE_HEIGHT = 720;


    /**
     * @var Plumrocket\Amp\Helper\Data
     */
    protected $_helper;

    /**
     * Construct
     *
     * @param \Magento\Framework\View\Element\Context $context
     * @param \Magento\Framework\Registry $coreRegistry,
     * @param \Plumrocket\Amp\Helper\Data $helper,
     * @param array $data
     */
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Plumrocket\Amp\Helper\Data $helper,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_helper = $helper;
    }

    /**
     * Retrieve string by JSON format according to http://schema.org requirements
     * @return string
     */
    public function getJson()
    {
        /**
         * Get helper, product and store objects
         */
        $_product = $this->getProduct();
        $_store = $_product->getStore();

        /**
         * Set product default values
         */
        $productName = self::DEFAULT_PRODUCT_NAME;
        $productShortDescription = self::DEFAULT_PRODUCT_SHORT_DESCRIPTION;
        $productStatus = self::DEFAULT_PRODUCT_STATUS;
        $productPrice = 0;
        $productPriceCurrency = self::DEFAULT_PRODUCT_PRICE_CURRENCY;

        /**
         * Set product data from product object
         */
        if ($_product) {
            /**
             * Get product name
             */
            if (strlen($_product->getName())) {
                $productName = $this->escapeHtml(mb_substr($_product->getName(), 0, self::PRODUCT_NAME_MAX_LEN, 'UTF-8'));
            }

            /**
             * Get product image
             */
            $productImage = $this->getImage($_product, 'product_page_image_small', [])->getData('image_url');

            /**
             * Get product description
             */
            if (strlen($_product->getShortDescription())) {
                $productShortDescription = $this->escapeHtml(mb_substr($_product->getShortDescription(), 0, self::PRODUCT_SHORT_DESCRIPTION_MAX_LEN, 'UTF-8'));
            }
        }

        $siteName = $this->_scopeConfig->getValue('general/store_information/name', ScopeInterface::SCOPE_STORE);
        if (!$siteName) {
            $siteName = 'Magento Store';
        }

        $logoBlock = $this->getLayout()->getBlock('amp.logo');
        $logo = $logoBlock ? $logoBlock->getLogoSrc() : '';

        if ($this->pageConfig->getTitle()->get()) {
            $pageContentHeading = $this->pageConfig->getTitle()->get();
        } else {
            $pageContentHeading = $productName;
        }

        $json = array(
            "@context" => "http://schema.org",
            "@type" => "Article",
            "author" => $siteName,
            "image" => array(
                '@type' => 'ImageObject',
                'url' => $productImage,
                'width' => self::PRODUCT_IMAGE_WIDTH,
                'height' => self::PRODUCT_IMAGE_HEIGHT,
            ),
            "name" => $productName,
            "description" => $productShortDescription,
            "datePublished" => $_product->getCreatedAt(),
            "dateModified" => $_product->getUpdatedAt(),
            "headline" => mb_substr($pageContentHeading, 0, 110, 'UTF-8'),
            "publisher" => array(
                '@type' => 'Organization',
                'name' => $siteName,
                'logo' => array(
                    '@type' => 'ImageObject',
                    'url' => $logo,
                ),
            ),
            "mainEntityOfPage" => array(
                "@type" => "WebPage",
                "@id" => $this->getUrl(),
            ),
        );

        return str_replace('\/', '/', json_encode($json));
    }
}