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
 * Edit grid for Manage Bank details
 * 
 * @abstract Mage_Adminhtml_Block_Widget_Form_Container
 */
class Apptha_Airhotels_Block_Adminhtml_Managebankdetails_Edit extends Mage_Adminhtml_Block_Widget_Form_Container {
    /**
     * Class constructor for manage bank details
     * 
     * @abstract Mage_Adminhtml_Block_Widget_Form_Container
     */
    public function __construct() {
        /**
         * Calling the parent Construct Method.
         */
        parent::__construct ();
        /**
         * Defining block froup
         */
        $this->_objectId = 'id';
        $this->_blockGroup = 'airhotels';
        /**
         * Defining controller
         */
        $this->_controller = 'adminhtml_managebankdetails';
        /**
         * Add save and delete button.
         */
        $this->_updateButton ( 'save', 'label', Mage::helper ( 'airhotels' )->__ ( 'Save ' ) );
        $this->_updateButton ( 'delete', 'label', Mage::helper ( 'airhotels' )->__ ( 'Delete ' ) );
    }    
    /**
     * Function Name: getHeaderText()
     * Assign header text to manage bank details
     */
    public function getHeaderText() {
        if (Mage::registry ( 'managebankdetails_data' ) && Mage::registry ( 'managebankdetails_data' )->getId ()) {
            /**
             * return header text
             */
            return Mage::helper ( 'airhotels' )->__ ( "Edit Details " );
        } else {
            /**
             * return header text
             */
            return Mage::helper ( 'airhotels' )->__ ( 'Add Details' );
        }
    }
}