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
 * Managesubscriptions Edit block
 */
class Apptha_Airhotels_Block_Adminhtml_Managesubscriptions_Edit extends Mage_Adminhtml_Block_Widget_Form_Container {
    
    /**
     * Construct the inital display of grid information
     * Setting the Block files group for this grid
     * Setting the Setting the Object id
     * Setting the Controller file for this grid
     */
    public function __construct() {
        /**
         * creating the Construct Method
         */
        parent::__construct ();
        $this->_blockGroup = 'airhotels';
        $this->_objectId = 'id';
        /**
         * Defining controller
         */
        $this->_controller = 'adminhtml_managesubscriptions';
        /**
         * Add the update Button for "save"
         */
        $this->_updateButton ( 'save', 'label', Mage::helper ( 'airhotels' )->__ ( 'Save Item' ) );
        /**
         * Add the update Button for "Delete"
         */
        $this->_updateButton ( 'delete', 'label', Mage::helper ( 'airhotels' )->__ ( 'Delete Item' ) );
        
        
        
        /**
         * Add the "saveandcontinue" button
         */
        $this->_addButton ( 'saveandcontinue', array (
                'label' => Mage::helper ( 'adminhtml' )->__ ( 'Save And Continue Edit' ),
                'onclick' => 'saveAndContinueEdit()',
                'class' => 'save' 
        ), - 100 );
        
        $this->_formScripts [] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('managesubscriptions_content') == null) { tinyMCE.execCommand('mceAddControl', false, 'managesubscriptions_content'); } else { tinyMCE.execCommand('mceRemoveControl', false, 'managesubscriptions_content');  }
            }
            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }
    /**
     * It return header text
     *
     * @method getHeaderText()
     *        
     * @return string
     */
    public function getHeaderText() {
        /**
         * Check weather the Value of manage Subscriptions data is not empty
         */
        if (Mage::registry ( 'managesubscriptions_data' ) && Mage::registry ( 'managesubscriptions_data' )->getId ()) {
            $subscriptionTitle = Mage::helper ( 'airhotels' )->__ ( "Edit Item '%s'", $this->htmlEscape ( Mage::registry ( 'managesubscriptionData' )->getName () ) );
        } else {
            /**
             * Set subscription title.
             */
            $subscriptionTitle = Mage::helper ( 'airhotels' )->__ ( 'Add Item' );
        }
        
        return $subscriptionTitle;
    }
}