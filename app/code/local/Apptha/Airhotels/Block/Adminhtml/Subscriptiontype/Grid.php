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
 * Subscription Type Grid block
 */
class Apptha_Airhotels_Block_Adminhtml_Subscriptiontype_Grid extends Mage_Adminhtml_Block_Widget_Grid {
 /**
  * Creating method for construct
  */
 public function __construct() {
  parent::__construct ();
  $this->setId ( 'subscriptiontypeGrid' );
  $this->setDefaultDir ( 'ASC' );
  $this->setDefaultSort ( 'subscriptiontype_id' );
  /**
   * Save admin grid parameters in session
   */
  $this->setSaveParametersInSession ( true );
 } 
 /**
  * Function Name: _prepareCollection()
  * Function to prepare columns to display grid
  *
  * @return array
  *
  */
 protected function _prepareCollection() {
  /**
   * Getting subscription type collection
   * @var unknown
   */
  $collection = Mage::getModel ( 'airhotels/subscriptiontype' )->getCollection ();
  $this->setCollection ( $collection );
  /**
   * return _prepareCollection
   */
  return parent::_prepareCollection ();
 } 
 /**
  * Function to prepare collection
  *
  * @return array
  *
  */
 protected function _prepareColumns() {
  /**
   * Add the field for Id
   */
  $this->addColumn ( 'id', array (
    'header' => Mage::helper ( 'airhotels' )->__ ( 'Id' ),
    'align' => 'right',
    'width' => '50px',
    'index' => 'id' 
  ) );
  /**
   * Add the field for engine_code
   */
  $this->addColumn ( 'engine_code', array (
    'header' => Mage::helper ( 'airhotels' )->__ ( 'Engine' ),
    'align' => 'left',
    'index' => 'engine_code',
    'type' => 'options',
    'options' => array (
      'Paypal' 
    ) 
  )
   );
  /**
   * Add the field for title
   */
  $this->addColumn ( 'title', array (
    'header' => Mage::helper ( 'airhotels' )->__ ( 'Title' ),
    'align' => 'left',
    'index' => 'title' 
  ) );
  /**
   * Add the field for status
   */
  $this->addColumn ( 'status', array (
    'header' => Mage::helper ( 'airhotels' )->__ ( 'Status' ),
    'align' => 'left',
    'index' => 'status',
    'type' => 'options',
    'options' => array (
      'Invisible',
      'Visible' 
    ) 
  ) );
  /**
   * Add the field for billing_period_unit
   */
  $periodunit = Mage::getModel ( 'airhotels/periodunit' )->getOptionArray ();
  $this->addColumn ( 'billing_period_unit', array (
    'header' => Mage::helper ( 'airhotels' )->__ ( 'Billing Period Unit' ),
    'align' => 'left',
    'index' => 'billing_period_unit',
    'type' => 'options',
    'options' => $periodunit 
  ) );
  /**
   * Add the field for billing_frequency
   */
  $this->addColumn ( 'billing_frequency', array (
    'header' => Mage::helper ( 'airhotels' )->__ ( 'Billing Frequency' ),
    'align' => 'left',
    'index' => 'billing_frequency' 
  ) );
  /**
   * Add the field for billing_cycle
   */
  $this->addColumn ( 'billing_cycle', array (
    'header' => Mage::helper ( 'airhotels' )->__ ( 'Billing Cycles' ),
    'align' => 'left',
    'index' => 'billing_cycle' 
  ) );  
  /**
   * Add the field for action
   */  
  $this->addColumn ( 'action', array (
    'header' => Mage::helper ( 'airhotels' )->__ ( 'Action' ),
    'width' => '100',
    'type' => 'action',
    'getter' => 'getId',
    'actions' => array (
      array (
        'caption' => Mage::helper ( 'airhotels' )->__ ( 'Edit' ),
        'url' => array (
          'base' => '*/*/edit' 
        ),
        'field' => 'id' 
      ) 
    ),
    'filter' => false,
    'sortable' => false,
    'index' => 'subscription',
    'is_system' => true 
  ) );  
  return parent::_prepareColumns ();
 } 
 /**
  * It gets the current row url
  *
  * @method getRowUrl()
  * @param $row @return
  *         array
  */
 public function getRowUrl($row) {
  return $this->getUrl ( '*/*/edit', array (
    'id' => $row->getId () 
  ) );
 }
}