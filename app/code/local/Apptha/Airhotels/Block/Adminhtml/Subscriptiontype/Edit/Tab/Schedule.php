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
 * Subscription Type Schedule Form block
 */
class Apptha_Airhotels_Block_Adminhtml_Subscriptiontype_Edit_Tab_Schedule extends Mage_Adminhtml_Block_Widget_Form {
 
 /**
  * Prepare form to schedule the subscription type.
  * 
  * @return array
  */
 protected function _prepareForm() {
  $form = new Varien_Data_Form ();
  /**
   * Set form data.
   */
  $this->setForm ( $form );
  /**
   * Adding the fieldset for subscriptiontype_form
   */
  $fieldset = $form->addFieldset ( 'subscriptiontype_form', array (
    'legend' => Mage::helper ( 'airhotels' )->__ ( 'Schedule' ) 
  ) );
  $periodunit = Mage::getModel ( 'airhotels/periodunit' )->getOptionArray ();
  /**
   * Add the Column for billing period Unit
   */
  $fieldset->addField ( 'billing_period_unit', 'select', array (
    'label' => Mage::helper ( 'airhotels' )->__ ( 'Billing Period' ),
    'required' => true,
    'name' => 'billing_period_unit',
    'note' => Mage::helper ( 'airhotels' )->__ ( 'Unit for billing during the subscription period..' ),
    'values' => $periodunit 
  ) );
  /**
   * Ad the Column for Billing frequency
   */
  $fieldset->addField ( 'billing_frequency', 'text', array (
    'label' => Mage::helper ( 'airhotels' )->__ ( 'Billing Frequency' ),
    'required' => true,
    'name' => 'billing_frequency',
    'class' => 'required-entry validate-digits' 
  ) );
  
  /**
   * Add the Custom column for Biling Cycle
   */
  $fieldset->addField ( 'billing_cycle', 'text', array (
    'label' => Mage::helper ( 'airhotels' )->__ ( 'Billing Cycles' ),
    'required' => true,
    'name' => 'billing_cycle',
    'class' => 'required-entry validate-digits',
    'note' => Mage::helper ( 'airhotels' )->__ ( 'The number of billing cycles for payment period.' ) 
  ) );
  
  /**
   * check weather the subscriptiontype Data
   */
  if (Mage::getSingleton ( 'adminhtml/session' )->getSubscriptiontypeData ()) {
   $form->setValues ( Mage::getSingleton ( 'adminhtml/session' )->getSubscriptiontypeData () );
   /**
    * Set subscription type data null.
    */
   Mage::getSingleton ( 'adminhtml/session' )->setSubscriptiontypeData ( null );
  }
  if (Mage::registry ( 'subscriptiontype_data' )) {
  /**
   * Set subscription type data.
   */
   $form->setValues ( Mage::registry ( 'subscriptiontype_data' )->getData () );
  }
  /**
   * Calling the parent Construct Method.
   */
  return parent::_prepareForm ();
 }
}