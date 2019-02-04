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
 * Createsubscriptions edit block
 * 
 * @abstract Mage_Adminhtml_Block_Widget_Form_Container
 */
class Apptha_Airhotels_Block_Adminhtml_Createsubscriptions_Edit extends Mage_Adminhtml_Block_Widget_Form_Container {
    /**
     * Creating the Method for construct
     * 
     * @abstract Mage_Adminhtml_Block_Widget_Form_Container
     */
    public function __construct() {
        /**
         * Calling the parent Construct Method.
         */
        parent::__construct ();
        /**
         * Defining block
         */
        $this->_objectId = 'entity_id';
        $this->_blockGroup = 'airhotels';
        /**
         * Defining controller
         */
        $this->_controller = 'adminhtml_createsubscriptions';
        /**
         * Adding update Button
         */
        $this->_updateButton ( 'delete', 'label', Mage::helper ( 'airhotels' )->__ ( 'Delete Item' ) );
        $this->_updateButton ( 'save', 'label', Mage::helper ( 'airhotels' )->__ ( 'Save Item' ) );
        /**
         * Add Button
         */
        $this->_formScripts [] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('createsubscriptions_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'createsubscriptions_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'createsubscriptions_content');
                }
            }
            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
        $this->_addButton ( 'saveandcontinue', array (
                'label' => Mage::helper ( 'adminhtml' )->__ ( 'Save And Continue Edit' ),
                'onclick' => 'saveAndContinueEdit()',
                'class' => 'save' 
        ), - 100 );
   }
    /**
     * Function for Header Text
     *
     * @see Mage_Adminhtml_Block_Widget_Container::getHeaderText()
     *
     */
    public function getHeaderText() {
        if (Mage::registry ( 'createsubscriptions_data' ) && Mage::registry ( 'createsubscriptions_data' )->getId ()) {
            
            return Mage::helper ( 'catalog' )->__ ( "Edit Item '%s'", $this->htmlEscape ( Mage::registry ( 'createsubscriptions_data' )->getName () ) );
        } else {
            /**
             * Return header text.
             */
            return Mage::helper ( 'airhotels' )->__ ( 'Add Item' );
        }
    }
}