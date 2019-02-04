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
 * @abstract Mage_Adminhtml_Block_Widget_Form_Container
 * 
 * @author user
 *
 */
class Apptha_Airhotels_Block_Adminhtml_Verification_Edit extends Mage_Adminhtml_Block_Widget_Form_Container {
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
         * Defining blocks
         */
        $this->_objectId = 'id';
        $this->_blockGroup = 'airhotels';
        /**
         * Defining controller
         */
        $this->_controller = 'adminhtml_verification';
        $this->_mode = 'edit';
        $this->_addButton ( 'save_and_continue', array (
                'label' => Mage::helper ( 'adminhtml' )->__ ( 'Save And Continue Edit' ),
                'onclick' => 'saveAndContinueEdit()',
                'class' => 'save' 
        ), - 100 );
        $this->_formScripts [] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('form_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'edit_form');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'edit_form');
                }
            }
        
            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
        $this->_updateButton ( 'save', 'label', Mage::helper ( 'airhotels' )->__ ( 'Save Tag' ) );
     }    
    /**
     * Assign header text
     */
    public function getHeaderText() {
        if (Mage::registry ( 'verification_data' ) && Mage::registry ( 'verification_data' )->getId ()) {
            /**
             * Return header text as Edit Tag
             */
            return Mage::helper ( 'airhotels' )->__ ( "Edit Tag" );
        } else {
            /**
             * Return header text as Add Tag.
             */
            return Mage::helper ( 'airhotels' )->__ ( 'Add Tag' );
        }
    }
}