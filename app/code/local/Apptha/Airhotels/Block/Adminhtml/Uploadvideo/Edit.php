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
 * 
 * @abstract Mage_Adminhtml_Block_Widget_Form_Container
 */
class Apptha_Airhotels_Block_Adminhtml_Uploadvideo_Edit extends Mage_Adminhtml_Block_Widget_Form_Container {
    /**
     * Class constructor
     * 
     * @abstract Mage_Adminhtml_Block_Widget_Form_Container
     */
    public function __construct() {
        /**
         * Calling the parent Construct Method.
         */
        parent::__construct ();
        /**
         * Defining block group
         */
        $this->_objectId = 'id';
        $this->_blockGroup = 'airhotels';
        /**
         * Defining controller
         */
        $this->_controller = 'adminhtml_uploadvideo';
        /**
         * Added Buttons
         *
         * 'Save video' , 'Save' , 'Delete Video'
         */
        $this->_updateButton ( 'save', 'label', Mage::helper ( 'airhotels' )->__ ( 'Save Video' ) );
        $this->_updateButton ( 'save', 'onclick', 'editformsubmit(this)' );
        $this->_updateButton ( 'delete', 'label', Mage::helper ( 'airhotels' )->__ ( 'Delete Video' ) );
    }    
    /**
     * Assign header text
     */
    public function getHeaderText() {
        if (Mage::registry ( 'video_data' ) && Mage::registry ( 'video_data' )->getId ()) {
            /**
             * Set header text as Edit Video.
             */
            return Mage::helper ( 'airhotels' )->__ ( "Edit Video" );
        } else {
            /**
             * Set header text as Add Video.
             */
            return Mage::helper ( 'airhotels' )->__ ( 'Add Video' );
        }
    }
}