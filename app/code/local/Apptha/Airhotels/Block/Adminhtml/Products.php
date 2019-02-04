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
class Apptha_Airhotels_Block_Adminhtml_Products extends Mage_Adminhtml_Block_Widget_Grid_Container {
    /**
     * Construct the inital display of grid information
     * Adding the Header text to the grid
     * Set as controller as "adminhtml_products"
     * set blockGroup as "Airhotels"
     * add Button label as "Add Item"
     *
     * Return parent construct
     *
     * @return array
     */
    public function __construct() {
        /**
         * Defining Header Text
         */
        $this->_headerText = Mage::helper ( 'airhotels' )->__ ( 'Manage Property' );
        /**
         * Defining controller
         */
        $this->_controller = 'adminhtml_products';
        $this->_blockGroup = 'airhotels';
        /**
         * Add button label.
         */
        $this->_addButtonLabel = Mage::helper ( 'airhotels' )->__ ( 'Add Item' );
        /**
         * Calling the parent Construct Method.
         */
        parent::__construct ();
        /**
         * Add the Remove button
         */
        $this->_removeButton ( 'add' );
    }
}