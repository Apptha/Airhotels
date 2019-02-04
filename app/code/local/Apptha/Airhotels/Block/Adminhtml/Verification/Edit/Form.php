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
 * Form for Verification
 * 
 * @abstract Mage_Adminhtml_Block_Widget_Form
 */
class Apptha_Airhotels_Block_Adminhtml_Verification_Edit_Form extends Mage_Adminhtml_Block_Widget_Form {
 /**
  * Prepare form before rendering HTML
  *
  * @return Mage_Adminhtml_Block_Widget_Form
  */
 protected function _prepareForm() {
  if (Mage::registry ( 'verification_data' )) {
   $data = Mage::registry ( 'verification_data' );
  } else {
   $data = array ();
  }
  /**
   *  Add new Varien_Data_Form
   */ 
  $form = new Varien_Data_Form ( array ('id' => 'edit_form','action' => $this->getUrl ( '*/*/save', array ('id' => $this->getRequest ()->getParam ( 'id' )) ),'method' => 'post','enctype' => 'multipart/form-data') );
  $form->setUseContainer ( true );
  $this->setForm ( $form );
  /**
   * Set form data
   * @var unknown
   */
  $fieldset = $form->addFieldset ( 'base_fieldset', array ('legend' => Mage::helper ( 'airhotels' )->__ ( 'Tag Section' ) ) );
  $fieldset->addField ( 'tag_name', 'text', array ('label' => Mage::helper ( 'airhotels' )->__ ( 'Tag Name' ),'class' => 'required-entry','required' => true,'name' => 'tag_name','note' => Mage::helper ( 'airhotels' )->__ ( 'Name of the Tag.' ) ) );
  $fieldset->addField ( 'tag_description', 'text', array ('label' => Mage::helper ( 'airhotels' )->__ ( 'Tag Description' ), 'class' => 'required-entry', 'required' => true, 'name' => 'tag_description' ) );
  $fieldset->addField ( 'direct_url', 'checkbox', array (
    'label' => Mage::helper ( 'airhotels' )->__ ( 'Allow Direct Url ?' ),
    'name' => 'direct_url',
    'value' => 1,
    'checked' => ($data->getDirectUrl () == 1) ? 'true' : '',
    'onclick' => 'this.value = this.checked ? 1 : 0;',
    'disabled' => false,
    'readonly' => false,
    'note' => Mage::helper ( 'airhotels' )->__ ( 'Checking this will allow host to input direct URl of verification document rather than uploading it.' ) 
  ) );
  /**
   * Set values.
   */
  $form->setValues ( $data );
  /**
   * Calling the parent Construct Method.
   */
  return parent::_prepareForm ();
 }
}