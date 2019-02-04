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
class Apptha_Airhotels_Block_Adminhtml_Airhotels_Edit_Tab_Details extends Mage_Adminhtml_Block_Widget_Form {
 /**
  * Initialize form
  *
  * @return Apptha_Airhotels_Block_Adminhtml_Airhotels_Grid
  */
 protected function _prepareForm() {
  $form = new Varien_Data_Form ();
  $this->setForm ( $form );
  $fieldset = $form->addFieldset ( 'airhotels_form', array ('legend' => Mage::helper ( 'airhotels' )->__ ( 'Property Details' ) ) );
  
  /**
   * Display the Property Name
   */
  $fieldset->addField ( 'product_name', 'text', array ('label' => Mage::helper ( 'airhotels' )->__ ( 'Property Name :' ),'readonly' => true) );
  /**
   * Adding Feild for customer_email
   */
  $fieldset->addField ( 'customer_email', 'text', array ('label' => Mage::helper ( 'airhotels' )->__ ( 'Booked Customer Email :' ),'readonly' => true) );
  /**
   * Adding Feild for Check In Date
   */
  $fieldset->addField ( 'fromdate', 'text', array ('label' => Mage::helper ( 'airhotels' )->__ ( 'Check In Date :' ),'readonly' => true) );
  /**
   * Adding Feild for Check Out Date
   */
  $fieldset->addField ( 'todate', 'text', array ('label' => Mage::helper ( 'airhotels' )->__ ( 'Check Out Date :' ),'readonly' => true ) );
  /**
   * Adding Feild for No of Guest
   */
  $fieldset->addField ( 'accomodates', 'text', array ('label' => Mage::helper ( 'airhotels' )->__ ( 'No of Guest :' ),'readonly' => true) );
  /**
   * Adding Feild for Grand Total
   */
  $fieldset->addField ( 'grand_total', 'text', array ('label' => Mage::helper ( 'airhotels' )->__ ( 'Grand Total :' ),'readonly' => true) );
  /**
   * Adding Feild for Host total Fee
   */
  $fieldset->addField ( 'host_total_fee', 'text', array ('label' => Mage::helper ( 'airhotels' )->__ ( 'Host Fee :' ),'readonly' => true ) );
  /**
   * Adding Feild for service Fee
   */
  $fieldset->addField ( 'service_fee', 'text', array ('label' => Mage::helper ( 'airhotels' )->__ ( 'Processing Fee:' ),'readonly' => true) );
  /**
   * Adding Feild for Host Fee
   */
  $fieldset->addField ( 'host_fee', 'text', array ('label' => Mage::helper ( 'airhotels' )->__ ( 'Commission Fee:' ),'readonly' => true) );
  
  if (Mage::getSingleton ( 'adminhtml/session' )->getAirhotelsData ()) {
   $form->setValues ( Mage::getSingleton ( 'adminhtml/session' )->getAirhotelsData () );
   Mage::getSingleton ( 'adminhtml/session' )->setAirhotelsData ( null );
  }
  if (Mage::registry ( 'airhotels_data' )) {
  /**
   * Set the airhotel data.
   */
   $form->setValues ( Mage::registry ( 'airhotels_data' )->getData () );
  }
  /**
   * Return the parent
   */
  return parent::_prepareForm ();
 }
}