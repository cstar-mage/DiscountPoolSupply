<?php
/**
 * Copyright © CyberSolutionsLLC. All rights reserved.
 */
namespace CyberSolutionsLLC\RotateStock\Helper;

use Magento\Store\Model\ScopeInterface;

class Config extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * Configs
     */
    const XML_PATH_ENABLED = 'cataloginventory/rotate_stock/enabled';

    const XML_PATH_RESET_QTY = 'cataloginventory/rotate_stock/reset_qty';

    const XML_PATH_MIN_QTY_THRESHOLD = 'cataloginventory/rotate_stock/min_qty_threshold';

    const XML_PATH_ONLY_X_LEFT = 'cataloginventory/rotate_stock/only_x_left';

    const XML_PATH_ONLY_X_LEFT_MESSAGE = 'cataloginventory/rotate_stock/only_x_left_message';

    /**
     * Placeholder
     */
    const PLACEHOLDER_ONLY_X_LEFT = '{X}';

    /**
     * Check if module enabled
     *
     * @param mixed $scopeCode
     * @return bool
     */
    public function isEnabled($scopeCode = null)
    {
        return $this->scopeConfig->isSetFlag(
            static::XML_PATH_ENABLED,
            ScopeInterface::SCOPE_STORE,
            $scopeCode
        );
    }

    /**
     * Retrieve reset qty
     *
     * @param mixed $scopeCode
     * @return string
     */
    public function getResetQty($scopeCode = null)
    {
        return $this->scopeConfig->getValue(
            static::XML_PATH_RESET_QTY,
            ScopeInterface::SCOPE_STORE,
            $scopeCode
        );
    }

    /**
     * Retrieve min qty threshold
     *
     * @param mixed $scopeCode
     * @return string
     */
    public function getMinTqyThreshold($scopeCode = null)
    {
        return $this->scopeConfig->getValue(
            static::XML_PATH_MIN_QTY_THRESHOLD,
            ScopeInterface::SCOPE_STORE,
            $scopeCode
        );
    }

    /**
     * Retrieve only x left
     *
     * @param mixed $scopeCode
     * @return string
     */
    public function getOnlyXLeft($scopeCode = null)
    {
        return $this->scopeConfig->getValue(
            static::XML_PATH_ONLY_X_LEFT,
            ScopeInterface::SCOPE_STORE,
            $scopeCode
        );
    }

    /**
     * Retrieve only x left message
     *
     * @param mixed $scopeCode
     * @return string
     */
    public function getOnlyXLeftMessage($scopeCode = null)
    {
        return $this->scopeConfig->getValue(
            static::XML_PATH_ONLY_X_LEFT_MESSAGE,
            ScopeInterface::SCOPE_STORE,
            $scopeCode
        );
    }

    /**
     * Get config for product
     *
     * @param \Magento\Catalog\Model\Product $product
     * @param mixed $scopeCode
     * @return array
     */
    public function getRotateConfigForProduct(\Magento\Catalog\Model\Product $product, $scopeCode = null)
    {
        $productConfig = $this->getRotateConfigForEntity($product);
        if ($category = $product->getCategory()) {
            $categoryConfig = $this->getRotateConfigForEntity($category);
            $productConfig = $this->mergeRotateConfigs($productConfig, $categoryConfig);
        }

        $globalConfig = $this->getRotateConfigForGlobal($scopeCode);
        return $this->mergeRotateConfigs($productConfig, $globalConfig);
    }

    /**
     * Get config for category
     *
     * @param \Magento\Catalog\Model\Category $product
     * @param mixed $scopeCode
     * @return array
     */
    public function getRotateConfigForCategory(\Magento\Catalog\Model\Category $category, $scopeCode = null)
    {
        $categoryConfig = $this->getRotateConfigForEntity($category);
        $globalConfig = $this->getRotateConfigForGlobal($scopeCode);
        return $this->mergeRotateConfigs($categoryConfig, $globalConfig);
    }

    /**
     * Get config for category
     *
     * @param mixed $scopeCode
     * @return array
     */
    public function getRotateConfigForGlobal($scopeCode = null)
    {
        return [
            'enabled' => (int)$this->isEnabled($scopeCode),
            'reset_qty' => $this->getResetQty($scopeCode),
            'min_qty_threshold' => $this->getMinTqyThreshold($scopeCode),
            'only_x_left' => $this->getOnlyXLeft($scopeCode),
            'only_x_left_message' => $this->getOnlyXLeftMessage($scopeCode),
        ];
    }

    /**
     * Get entity config
     *
     * @param $object $entity
     * @return array
     */
    protected function getRotateConfigForEntity($entity)
    {
        $enabled = (int)$entity->getData('rs_enabled');
        return [
            'enabled' => $enabled == 1 ? true : ($enabled == 2 ? false : null),
            'reset_qty' => $entity->getData('rs_reset_qty') ?: null,
            'min_qty_threshold' => $entity->getData('rs_min_qty_threshold') ?: null,
            'only_x_left' => $entity->getData('rs_only_x_left') ?: null,
            'only_x_left_message' => null,
        ];
    }

    /**
     * Merge configs
     *
     * @param array $configTarget
     * @param array $configSource
     * @return array
     */
    protected function mergeRotateConfigs(array $configTarget, array $configSource)
    {
        foreach ($configTarget as $key => $val) {
            if ($val === null) {
                $configTarget[$key] = $configSource[$key];
            }
        }

        return $configTarget;
    }
}
