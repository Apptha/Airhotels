<?php
/**
 * Apptha
 * NOTICE OF LICENSE
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.apptha.com/LICENSE.txt
 * ==============================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * ==============================================================
 * This package designed for Magento COMMUNITY edition
 * Apptha does not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * Apptha does not provide extension support in case of
 * incorrect edition usage.
 * ==============================================================
 * @category    Apptha
 * @package     Apptha_Airhotels
 * @version     0.2.9
 * @author      Apptha Team <developers@contus.in>
 * @copyright   Copyright (c) 2014 Apptha. (http://www.apptha.com)
 * @license     http://www.apptha.com/LICENSE.txt
 * 
 */
class Apptha_Airhotels_Block_Adminhtml_Airhotels_Edit extends Mage_Adminhtml_Block_Widget_Form_Container {
    /**
     * Construct Method
     */
    public function __construct() {
        /**
         * Calling the parent Construct Method.
         */
        parent::__construct ();
        /**
         * Defining block
         */
        $this->_objectId = 'id';
        $this->_blockGroup = 'airhotels';
        /**
         * Defining controller
         */
        $this->_controller = 'adminhtml_airhotels';
        /**
         * Adding the update button for for Save
         */
        $this->_updateButton ( 'save', 'label', Mage::helper ( 'airhotels' )->__ ( 'Save Item' ) );
        /**
         * Adding the update button for for Delete
         */
        $this->_updateButton ( 'delete', 'label', Mage::helper ( 'airhotels' )->__ ( 'Delete Item' ) );
        /**
         * Adding the remove button for for Delete
         */
        $this->_removeButton ( 'delete' );
        /**
         * Adding the remove button for for Reset
         */
        $this->_removeButton ( 'reset' );
        /**
         * Adding the button for saveandcontinue
         */
        $this->_addButton ( 'saveandcontinue', array (
                'label' => Mage::helper ( 'adminhtml' )->__ ( 'Save And Continue Edit' ),
                'onclick' => 'saveAndContinueEdit()',
                'class' => 'save' 
        ), - 100 );
        
        $this->_formScripts [] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('airhotels_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'airhotels_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'airhotels_content');
                }
            }
            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }";
    }
    /**
     * Get header text Value
     * @see Mage_Adminhtml_Block_Widget_Container::getHeaderText()
     */
    public function getHeaderText() {
        if (Mage::registry ( 'airhotels_data' ) && Mage::registry ( 'airhotels_data' )->getId ()) {
            /**
             * Retuen header text.
             */
            return Mage::helper ( 'airhotels' )->__ ( "Hoster Payment Status" );
        } else {
            /**
             * Retuen header text.
             */
            return Mage::helper ( 'airhotels' )->__ ( 'Add Item' );
        }
    }
}