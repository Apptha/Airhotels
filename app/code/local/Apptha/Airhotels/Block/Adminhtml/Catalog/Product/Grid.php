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
/**
 * @abstract Mage_Adminhtml_Block_Catalog_Product_Grid
 * 
 * @author user
 *
 */
class Apptha_Airhotels_Block_Adminhtml_Catalog_Product_Grid extends Mage_Adminhtml_Block_Catalog_Product_Grid {
    /**
     * Construct the inital display of grid information
     * Adding the Header text to the grid
     * Set as controller as "adminhtml_products"
     * set blockGroup as "Airhotels"
     * add massaction for property approval
     *
     * Return parent construct
     *
     * @return array
     */
    protected function _prepareMassaction() {
        /**
         * Calling the parent Construct Method.
         */
        parent::_prepareMassaction ();        
        /**
         * Append new mass action option
         * 
         * mass action for propertyapproved
         */
        $this->getMassactionBlock ()->addItem ( 'propertyapproved', array (
                'label' => $this->__ ( 'Property Status' ),
                'url' => $this->getUrl ( 'airhotels/adminhtml_airhotels/approve' ),
                'additional' => array (
                        'visibility' => array (
                                'name' => 'approved',
                                'type' => 'select',
                                'class' => 'required-entry',
                                'label' => Mage::helper ( 'catalog' )->__ ( 'Approved' ),
                                'values' => array (
                                        '' => ' ',
                                        '1' => 'Yes',
                                        '2' => 'No' 
                                ) 
                        ) 
                ) 
        ) );
    }
}