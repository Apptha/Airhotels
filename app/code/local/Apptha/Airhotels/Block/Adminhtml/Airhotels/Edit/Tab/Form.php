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
class Apptha_Airhotels_Block_Adminhtml_Airhotels_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form {
 
 /**
  * Initialize form
  *
  * @return Apptha_Airhotels_Block_Adminhtml_Airhotels_Grid
  */
 protected function _prepareForm() {
  $form = new Varien_Data_Form ();
  $this->setForm ( $form );
  $fieldset = $form->addFieldset ( 'airhotels_form', array (
    'legend' => Mage::helper ( 'airhotels' )->__ ( 'Property Information' ) 
  ) );
  
  /**
   * Display the orderId
   * class:required
   * required:true
   * Name:order Id
   */
  $fieldset->addField ( 'order_id', 'text', array (
    'label' => Mage::helper ( 'airhotels' )->__ ( 'Order Id' ),
    'class' => 'required-entry',
    'required' => true,
    'readonly' => true,
    'name' => 'order_id' 
  ) );
  /**
   * Payment Status
   * class:required
   * required:true
   * Name:Status
   */
  $fieldset->addField ( 'status', 'select', array (
    'label' => Mage::helper ( 'airhotels' )->__ ( 'Status' ),
    'name' => 'status',
    'values' => array (
      array (
        'value' => 2,
        'label' => Mage::helper ( 'airhotels' )->__ ( 'Refund To Guest' ) 
      ),
      array (
        'value' => 1,
        'label' => Mage::helper ( 'airhotels' )->__ ( 'Paid To Hoster' ) 
      ),
      array (
        'value' => 0,
        'label' => Mage::helper ( 'airhotels' )->__ ( 'Not paid To Hoster' ) 
      ) 
    ) 
  ) );
  /**
   * Text editor for admin reply
   * class:required
   * required:true
   * Name:Message
   */
  $fieldset->addField ( 'message', 'editor', array (
    'name' => 'message',
    'label' => Mage::helper ( 'airhotels' )->__ ( 'Content' ),
    'title' => Mage::helper ( 'airhotels' )->__ ( 'Content' ),
    'style' => 'width:400px; height:200px;',
    'wysiwyg' => false,
    'required' => true 
  )
   );
  $this->FormBlock ( $form );
  return parent::_prepareForm ();
 }
 /**
  * Creating the form block
  * 
  * @param object $form         
  */
 public function FormBlock($form) {
  if (Mage::getSingleton ( 'adminhtml/session' )->getAirhotelsData ()) {
   /**
    * Setting values using session
    */
   $form->setValues ( Mage::getSingleton ( 'adminhtml/session' )->getAirhotelsData () );
   /**
    * Setting adminhtml session
    */
   Mage::getSingleton ( 'adminhtml/session' )->setAirhotelsData ( null );
  }
  if (Mage::registry ( 'airhotels_data' )) {
   /**
    * Getting registry details
    */
   $form->setValues ( Mage::registry ( 'airhotels_data' )->getData () );
  }
 }
}