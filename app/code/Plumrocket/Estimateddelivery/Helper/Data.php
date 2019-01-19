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
 * @package     Plumrocket_Estimateddelivery
 * @copyright   Copyright (c) 2015 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\Estimateddelivery\Helper;

class Data extends Main
{
    const SECTION_ID = 'estimateddelivery';
    protected $_configSectionId = 'estimateddelivery';

    public function moduleEnabled($store = null)
    {
        return (bool)$this->getConfig($this->_configSectionId.'/general/enable', $store);
    }

    public function makeDeliveryGroup($group)
    {
        $deliveryGroup = new \Magento\Framework\DataObject(['data' => $group->getData()]);
        // $this->_objectManager->create('Magento\Framework\DataObject', ['data' => $group->getData()]);
        $deliveryGroup->setAttributeGroupName(__('Estimated Delivery Date'));
        return $deliveryGroup;
    }

    public function makeShippingGroup($group)
    {
        $shippingGroup = new \Magento\Framework\DataObject(['data' => $group->getData()]);
        // $this->_objectManager->create('Magento\Framework\DataObject', ['data' => $group->getData()]);
        $shippingGroup->setAttributeGroupName(__('Estimated Shipping Date'));
        return $shippingGroup;
    }

    public function getGroup($setId)
    {
        $groupName = $this->getGroupName();

        return $this->_objectManager->get('Magento\Eav\Model\ResourceModel\Entity\Attribute\Group\Collection')
            ->setAttributeSetFilter($setId)
            ->addFilter('attribute_group_name', $groupName)
            ->setSortOrder()
            ->load()
            ->getFirstItem();
    }

    public function getGroupName()
    {
        return 'Estimated Delivery/Shipping';
    }

    public function showPosition($position)
    {
        $positions = explode(',', $this->getConfig($this->_configSectionId.'/general/position'));
        if (in_array($position, $positions)) {
            return true;
        }
    }

    public function disableExtension()
    {
        $resource = $this->_objectManager->get('Magento\Framework\App\ResourceConnection');
        $connection = $resource->getConnection('core_write');
        $connection->delete(
            $resource->getTableName('core_config_data'),
            [$connection->quoteInto('path = ?', $this->_configSectionId.'/general/enable')]
        );

        $config = $this->_objectManager->get('\Magento\Config\Model\Config');
        $config->setDataByPath($this->_configSectionId.'/general/enable', 0);
        $config->save();
    }

    public function moduleCheckoutspageEnabled()
    {
        return (bool)$this->moduleExists('Checkoutspage');
    }

    public function getDateTimeFormat()
    {
        // return 'M/d/yyyy H:mm';
        return 'MM-dd-yyyy';
    }

    public function getOptions($item, $forOrder)
    {
        $options = [];

        if (!$product = $item->getProduct()) {
            $product = $this->_objectManager->create('Magento\Catalog\Model\Product')->load($item->getProductId());
        }

        $estimateddelivery = $this->_objectManager->get('Plumrocket\Estimateddelivery\Helper\Product')
            ->setProduct($product, $forOrder? $item : null);

        if ($shipping = $estimateddelivery->getShipping()) {
            $options['shipping'] = $shipping;
        }

        if ($delivery = $estimateddelivery->getDelivery()) {
            $options['delivery'] = $delivery;
        }

        return $options;
    }

    public function saveOptions($item, $options)
    {
        $shipping = !empty($options['shipping']['value']) ? $options['shipping']['value'] : null;
        $delivery = !empty($options['delivery']['value']) ? $options['delivery']['value'] : null;

        if ($item->getId()) {
            $this->_objectManager->create('Plumrocket\Estimateddelivery\Model\OrderItem')
                ->setItemId($item->getId())
                ->setShipping($shipping)
                ->setDelivery($delivery)
                ->save();
        }

        return $this;
    }
}
