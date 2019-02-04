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
 * Edit grid for neighbourhoods cities
 */
class Apptha_Airhotels_Block_Adminhtml_City_Edit extends Mage_Adminhtml_Block_Widget_Form_Container {
    /**
     * Class constructor.
     */
    public function __construct() {
        /**
         * Calling the parent Construct Method.
         */
        parent::__construct ();
        /**
         * Defining block group.
         */
        $this->_blockGroup = 'airhotels';
        $this->_objectId = 'id';
        /**
         * Defining controller
         */
        $this->_controller = 'adminhtml_city';
        /**
         * Display Save button.
         * Display delete button.
         */
        $this->_updateButton ( 'save', 'label', Mage::helper ( 'airhotels' )->__ ( 'Save City' ) );
        $this->_updateButton ( 'delete', 'label', Mage::helper ( 'airhotels' )->__ ( 'Delete City' ) );
    }    
    /**
     * Function Name: getHeaderText()
     * 
     * Assign header text
     */
    public function getHeaderText() {
        /**
         * Set city data to registry
         */
        if (Mage::registry ( 'city_data' ) && Mage::registry ( 'city_data' )->getId ()) {
            /**
             * Display Edit city as header text.
             */
            $cityTittle = Mage::helper ( 'airhotels' )->__ ( "Edit City" );
        } else {
            /**
             * Display Add city as header text.
             */
            $cityTittle = Mage::helper ( 'airhotels' )->__ ( 'Add City' );
        }
        return $cityTittle;
    }
}