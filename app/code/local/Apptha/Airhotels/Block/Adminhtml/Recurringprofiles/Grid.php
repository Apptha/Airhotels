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
 * RecurringProfiles Grid block
 */
class Apptha_Airhotels_Block_Adminhtml_Recurringprofiles_Grid extends Mage_Adminhtml_Block_Widget_Grid {
 /**
  * Create Method for Construct
  */
 public function __construct() {
 /**
  * Calling the parent Construct Method.
  */ 
  parent::__construct ();
 /**
  * Set revurringprofileGrid Id
  * Set sort by preapprovaldetails_id
  */
  $this->setId ( 'recurringprofilesGrid' );
  $this->setDefaultSort ( 'preapprovaldetails_id' );
  $this->setDefaultDir ( 'ASC' );
  $this->setSaveParametersInSession ( true );
 }
 
 /**
  * Prepare Collection.
  * Set the recurring profile collection based on the status received from Controller.
  *
  * @return array
  */
 protected function _prepareCollection() {
  $status = Mage::registry ( 'ProfileStatus' );
  $state = Mage::registry ( 'Status' );
  if ($status == 3) {
   /**
    * Get the Collection of preapprovaldetails
    */
   $collection = Mage::getModel ( 'paypaladaptive/preapprovaldetails' )->getCollection ();   
   $this->setCollection ( $collection );
  } else {
   /**
    * Get the Collection of preapprovaldetails
    */
   $collection = Mage::getModel ( 'paypaladaptive/preapprovaldetails' )->getCollection ()->addFieldToFilter ( 'status', $state );
   
   $this->setCollection ( $collection );
  }
  /**
   * Returning the prepareColection
   */
  return parent::_prepareCollection ();
 }
 
 /**
  * Prepare Columns.
  * Add block for adding columns in the grid.
  *
  * @return array
  */
 protected function _prepareColumns() {
  /**
   * Add Column for preapprovaldetails_id
   */
  $this->addColumn ( 'preapprovaldetails_id', array ('header' => Mage::helper ( 'airhotels' )->__ ( 'Id' ), 'align' => 'right', 'width' => '50px','index' => 'preapprovaldetails_id' ) );
  /**
   * Add Column for preapproval
   */
  $this->addColumn ( 'preapproval', array (
    'header' => Mage::helper ( 'airhotels' )->__ ( 'Preapproval Id' ), 'align' => 'right', 'width' => '50px', 'index' => 'preapproval' ) );
  
  /**
   * Add Column for status
   */
  $this->addColumn ( 'status', array (
    'header' => Mage::helper ( 'airhotels' )->__ ( 'Profile State' ), 'align' => 'left',  'index' => 'status', 'filter' => false ) );
  /**
   * Add Column for maximum total amount of all payments
   */
  $this->addColumn ( 'max_total_amount_of_all_payments', array (
    'header' => Mage::helper ( 'airhotels' )->__ ( 'Billing Amount' ), 'align' => 'left', 'index' => 'max_total_amount_of_all_payments', 'type' => 'price', 'currency_code' => Mage::app ()->getStore ( 0 )->getBaseCurrency ()->getCode () ) );
  
  /**
   * Add Column for starting date
   */
  $this->addColumn ( 'starting_date', array (
    'header' => Mage::helper ( 'airhotels' )->__ ( 'Start Date' ),'align' => 'left', 'width' => '50px', 'index' => 'starting_date' ) );
  /**
   * Add Column for ending date
   */
  $this->addColumn ( 'ending_date', array (
    'header' => Mage::helper ( 'airhotels' )->__ ( 'End Date' ),'align' => 'left', 'width' => '50px', 'index' => 'ending_date' ) );
  /**
   * Add Column for payment period
   */
  $this->addColumn ( 'payment_period', array ( 'header' => Mage::helper ( 'airhotels' )->__ ( 'Billing Period Unit' ),    'align' => 'left', 'index' => 'payment_period' ) );
  /**
   * Add Column for current payments
   */
  $this->addColumn ( 'cur_payments', array (
    'header' => Mage::helper ( 'airhotels' )->__ ( 'Current No of Payments' ),'align' => 'left','index' => 'cur_payments' ) );
  /**
   * Add Column for maximum number of payments
   */
  $this->addColumn ( 'max_number_of_payments', array (
    'header' => Mage::helper ( 'airhotels' )->__ ( 'Maximum No of Payments' ), 'align' => 'left', 'index' => 'max_number_of_payments' ) );
  /**
   * Add Column for current payments amount
   */
  $this->addColumn ( 'cur_payments_amount', array (
    'header' => Mage::helper ( 'airhotels' )->__ ( 'Current Payment Amount' ),'align' => 'left', 'index' => 'cur_payments_amount' ) );
  /**
   * Add Column for Current Period Ending Date
   */
  $this->addColumn ( 'cur_period_ending_date', array (
    'header' => Mage::helper ( 'airhotels' )->__ ( 'Cur. Payment Ending Date' ),'align' => 'left','index' => 'cur_period_ending_date' ) );
  /**
   * Calling the parent Construct Method.
   */
  return parent::_prepareColumns ();
 }
}