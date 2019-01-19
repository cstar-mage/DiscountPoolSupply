<?php
/**
 * Copyright © CyberSolutionsLLC, Inc. All rights reserved.
 */
namespace CyberSolutionsLLC\RotateStock\Model\Source;

class RotateStockStatus extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function getAllOptions()
    {
        if (!$this->_options) {
            $this->_options = [
                ['value' => 0, 'label' => __('Use Global Config')],
                ['value' => 1, 'label' => __('Enabled')],
                ['value' => 2, 'label' => __('Disabled')],
            ];
        }
        return $this->_options;
    }
}
