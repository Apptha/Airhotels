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
 * Managesubscriptions_Edit_Tab General block
 * Form to set the values for product to change as "subscription product".
 */
class Apptha_Airhotels_Block_Adminhtml_Managesubscriptions_Edit_Tab_General extends Mage_Adminhtml_Block_Widget_Form {
 
 /**
  * Get the values for product to change as a subscription product.
  *
  * @return void
  */
 protected function _prepareForm() {
  $form = new Varien_Data_Form ();
  $this->setForm ( $form );
  /**
   * Add the general Field
   */
  $fieldset = $form->addFieldset ( 'managesubscriptions_form', array ('legend' => Mage::helper ( 'airhotels' )->__ ( 'General' )) );
  
  /**
   * Add Field to ProductId
   */
  $fieldset->addField ( 'product_id', 'hidden', array ('label' => Mage::helper ( 'airhotels' )->__ ( 'Product Id' ),
    'name' => 'product_id' ) );
  /**
   * Add the select Field
   */
  $fieldset->addField ( 'is_subscription_only', 'select', array (
    'label' => Mage::helper ( 'airhotels' )->__ ( 'Is Subscription Only' ),
    'name' => 'is_subscription_only',
    'values' => array (
      array ( 'value' => 1, 'label' => Mage::helper ( 'airhotels' )->__ ( 'Enabled' ) )) 
  ) );
  /**
   * Add the select Field
   */
  $isstartDateValue = $fieldset->addField ( 'start_date', 'select', array (
    'label' => Mage::helper ( 'airhotels' )->__ ( 'Start Date' ),
    'name' => 'start_date',
    'values' => array (
      array ( 'value' => 0,'label' => Mage::helper ( 'airhotels' )->__ ( 'Moment Of Purchase' ) ),      
      array ('value' => 1,'label' => Mage::helper ( 'airhotels' )->__ ( 'Defined by Customer' ) )) 
  ) );
  /**
   * Add the time Field
   */
  $dayOfMonth = $fieldset->addField ( 'day_of_month', 'time', array (
    'label' => Mage::helper ( 'airhotels' )->__ ( 'Day of Month' ), 'name' => 'day_of_month', 'required' => 'true' ) );
  $this->setChild ( 'form_after', $this->getLayout ()->createBlock ( 'adminhtml/widget_form_element_dependence' )->addFieldMap ( $isstartDateValue->getHtmlId (), $isstartDateValue->getName () )->addFieldMap ( $dayOfMonth->getHtmlId (), $dayOfMonth->getName () )->addFieldDependence ( $dayOfMonth->getName (), $isstartDateValue->getName (), 4 ) );
  /**
   * Check weather Manage subscription has data
   */
  if (Mage::getSingleton ( 'adminhtml/session' )->getManagesubscriptionsData ()) {
   $form->setValues ( Mage::getSingleton ( 'adminhtml/session' )->getManagesubscriptionsData () );
   /**
    * Set manage subscription data as null.
    */
   Mage::getSingleton ( 'adminhtml/session' )->setManagesubscriptionsData ( null );
  }
  if (Mage::registry ( 'managesubscriptions_data' )) {
  /**
   * Set manage subscription data.
   */
   $form->setValues ( Mage::registry ( 'managesubscriptions_data' )->getData () );
  }
  /**
   * Calling the parent Construct Method.
   */
  return parent::_prepareForm ();
 }
}