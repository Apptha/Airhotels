<?php
/**
 * Apptha
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.apptha.com/LICENSE.txt
 *
 * ==============================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * ==============================================================
 * This package designed for Magento COMMUNITY edition
 * Apptha does not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * Apptha does not provide extension support in case of
 * incorrect edition usage.
 * ==============================================================
 *
 * @category    Apptha
 * @package     Apptha_Airhotels
 * @version     0.2.9
 * @author      Apptha Team <developers@contus.in>
 * @copyright   Copyright (c) 2014 Apptha. (http://www.apptha.com)
 * @license     http://www.apptha.com/LICENSE.txt
 *
 */
class Apptha_Airhotels_Block_Adminhtml_Verifyhost_Download extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {
    /**
     * Define render function
     *
     * @see Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract::render()
     */
    public function render(Varien_Object $row) {
        $value = $row->getData ( $this->getColumn ()->getIndex () );
        /**
         * Get TagID
         */
        $hostTag = Mage::getModel ( 'airhotels/verifyhost' )->load ( $value );
        $tagId = $hostTag->getTagId ();
        $tagModel = Mage::getModel ( 'airhotels/tagsverification' )->load ( $tagId );
        if ($tagModel->getDirectUrl () != 1) {
            /**
             * Download link
             */
            return "<a title='Click to downlaod' href='" . $hostTag->getFilePath () . "' download='" . basename ( $hostTag->getFilePath () ) . "'>" . 'Download file ' . basename ( $hostTag->getFilePath () ) . "</a>";
        } else {
            /**
             * Return file path
             */
            return $hostTag->getFilePath ();
        }
    }
}