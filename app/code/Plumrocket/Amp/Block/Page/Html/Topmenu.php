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

namespace Plumrocket\Amp\Block\Page\Html;

class Topmenu extends \Magento\Theme\Block\Html\Topmenu
{
    /**
     * Add sub menu HTML code for current menu item
     *
     * @param \Magento\Framework\Data\Tree\Node $child
     * @param string $childLevel
     * @param string $childrenWrapClass
     * @param int $limit
     * @return string HTML code
     */
    protected function _addSubMenu($child, $childLevel, $childrenWrapClass, $limit)
    {
        $html = '';
        if (!$child->hasChildren()) {
            return $html;
        }

        $colStops = null;
        if ($childLevel == 0 && $limit) {
            $colStops = $this->_columnBrake($child->getChildren(), $limit);
        }

        $html .= '<ul class="level' . $childLevel . ' submenu">';

        $outermostClassCode = '';
        $html .= '<li ' . $this->_getRenderedMenuItemAttributes($child) . '>';
        $html .= '<a href="' . $child->getUrl() . '" ' . $outermostClassCode . '><span>' . $this->escapeHtml(
            __ ('View All %1', $child->getName())
        ) . '</span></a>' . '</li>';

        $html .= $this->_getHtml($child, $childrenWrapClass, $limit, $colStops);
        $html .= '</ul>';

        return $html;
    }

    /**
     * Recursively generates top menu html from data that is specified in $menuTree
     *
     * @param \Magento\Framework\Data\Tree\Node $menuTree
     * @param string $childrenWrapClass
     * @param int $limit
     * @param array $colBrakes
     * @return string
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    protected function _getHtml(
        \Magento\Framework\Data\Tree\Node $menuTree,
        $childrenWrapClass,
        $limit,
        $colBrakes = []
    ) {
        $html = '';

        $children = $menuTree->getChildren();
        $parentLevel = $menuTree->getLevel();
        $childLevel = $parentLevel === null ? 0 : $parentLevel + 1;

        $counter = 1;
        $itemPosition = 1;
        $childrenCount = $children->count();

        $parentPositionClass = $menuTree->getPositionClass();
        $itemPositionClassPrefix = $parentPositionClass ? $parentPositionClass . '-' : 'nav-';

        foreach ($children as $child) {
            $child->setLevel($childLevel)
                ->setIsFirst($counter == 1)
                ->setIsLast($counter == $childrenCount)
                ->setPositionClass($itemPositionClassPrefix . $counter);

            $outermostClassCode = '';
            $outermostClass = $menuTree->getOutermostClass();

            if ($childLevel == 0 && $outermostClass) {
                $outermostClassCode = ' class="' . $outermostClass . '" ';
                $child->setClass($outermostClass);
            }

            if (count($colBrakes) && $colBrakes[$counter]['colbrake']) {
                $html .= '</ul></li><li class="column"><ul>';
            }

            $hasChildren = $child->hasChildren();
            if ($hasChildren) {
                $html .= '<li ' . $this->_getRenderedMenuItemAttributes($child) . '>';
                $html .= '<input type="checkbox" id="mitem-' . $child->getId() . '">';
                $html .= '<label for="mitem-' . $child->getId() . '" ' . $outermostClassCode . '><span>' . $this->escapeHtml(
                    $child->getName()
                ) . '</span></label>' . $this->_addSubMenu(
                    $child,
                    $childLevel,
                    $childrenWrapClass,
                    $limit
                ) . '</li>';
            } else {
                $html .= '<li ' . $this->_getRenderedMenuItemAttributes($child) . '>';
                $html .= '<a href="' . $child->getUrl() . '" ' . $outermostClassCode . '><span>' . $this->escapeHtml(
                    $child->getName()
                ) . '</span></a>' . '</li>';
            }
            $itemPosition++;
            $counter++;
        }

        if (count($colBrakes) && $limit) {
            $html = '<li class="column"><ul>' . $html . '</ul></li>';
        }

        return $html;
    }

    /**
     * Override parent method
     *
     * @return array
     */
    public function getCacheKeyInfo()
    {
        $keyInfo = \Magento\Framework\View\Element\Template::getCacheKeyInfo();
        $keyInfo[] = 'amp';
        return $keyInfo;
    }
}
