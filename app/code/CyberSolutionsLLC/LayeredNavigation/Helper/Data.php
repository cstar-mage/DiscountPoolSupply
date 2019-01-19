<?php
/**
 * Copyright © CyberSolutionsLLC. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace CyberSolutionsLLC\LayeredNavigation\Helper;

class Data extends \Mageplaza\LayeredNavigation\Helper\Data
{
	/**
     * @param $filters
     * @return mixed
     */
    public function getLayerConfiguration($filters)
    {
        $filterParams = $this->_getRequest()->getParams();
        
        $escaper = $this->objectManager->get(\Magento\Framework\Escaper::class);
        foreach ($filterParams as $key => $value) {
            $filterParams[$key] = $escaper->escapeHtml($value);
        }

        $config       = new \Magento\Framework\DataObject([
            'active' => array_keys($filterParams),
            'params' => $filterParams
        ]);

        $this->getFilterModel()->getLayerConfiguration($filters, $config);

        return $this->objectManager->get('Magento\Framework\Json\EncoderInterface')->encode($config->getData());
    }
}