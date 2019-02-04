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
 * Grid for Manage Bank details with fields
 * 
 * Mage_Adminhtml_Block_Widget_Grid
 */
class Apptha_Airhotels_Block_Adminhtml_Managebankdetails_Grid extends Mage_Adminhtml_Block_Widget_Grid {
 /**
  * Class constructor
  * 
  * Mage_Adminhtml_Block_Widget_Grid
  */
 public function __construct() {
 /**
  * Calling the parent Construct Method.
  */
  parent::__construct ();
  $this->setId ( 'managebankdetailsGrid' );
  $this->setDefaultSort ( 'id' );
  $this->setDefaultDir ( 'DESC' );
  $this->setSaveParametersInSession ( true );
 }
 /**
  * Prepare collection for Manage Bank details
  *
  * @return collection
  */
 protected function _prepareCollection() {
 /**
  * 
  * @var unknown
  */
 /**
  * Calling the parent Construct Method.
  * 
  * Getting collection for bank details
  */
  $managebankdetailsCollection = Mage::getModel ( 'airhotels/managebankdetails' )->getCollection ();
  $this->setCollection ( $managebankdetailsCollection );  
  return parent::_prepareCollection ();
  /**
   * return _prepareCollection
   */
 }
 /**
  * Prepare columns for Manage Bank details
  *
  * @return collection
  */
 protected function _prepareColumns() {
 /**
  * Add columns
  * 
  * @column id
  * @column country_code
  * @column currency_code
  * @column editaction
  * @column deleteaction
  */
  $this->addColumn ( 'id', array (
    'header' => Mage::helper ( 'airhotels' )->__ ( 'Id' ),'align' => 'left','index' => 'id' 
  ) );
  /**
   * Add new column for country code
   */
  $this->addColumn ( 'country_code', array (
    'header' => Mage::helper ( 'airhotels' )->__ ( 'Country' ),    'align' => 'left',
    'index' => 'country_code',    'renderer' => 'Apptha_Airhotels_Block_Adminhtml_Renderer_Countrylist') );
  /**
   * Add new column for currency_code
   */
  $this->addColumn ( 'currency_code', array ( 'header' => Mage::helper ( 'airhotels' )->__ ( 'Currency' ), 'align' => 'left', 'index' => 'country_code','renderer' => 'Apptha_Airhotels_Block_Adminhtml_Renderer_Currencylist') );
  /**
   * Add new column for created_at
   */
  $this->addColumn ( 'created_at', array (
    'header' => Mage::helper ( 'airhotels' )->__ ( 'Created' ),
    'align' => 'left','index' => 'created_at', 'type' => 'datetime' ) );
  /**
   * Add new column for field_name
   */
  $this->addColumn ( 'field_name', array ( 'header' => Mage::helper ( 'airhotels' )->__ ( 'Field Name' ), 'align' => 'left',  'index' => 'field_name'  ) );
  /**
   * Add new column for field_title
   */
  $this->addColumn ( 'field_title', array (
    'header' => Mage::helper ( 'airhotels' )->__ ( 'Field Title' ),
    'align' => 'left','index' => 'field_title' 
  ) );
  /**
   * Add new column for editaction
   */
  $this->addColumn ( 'editaction', array (
    'header' => Mage::helper ( 'airhotels' )->__ ( 'Edit Action' ), 'width' => '50px','type' => 'action', 'getter' => 'getId', 'actions' => array (
      array (
        'caption' => Mage::helper ( 'airhotels' )->__ ( 'Edit' ),   'url' => array ( 'base' => 'airhotels/adminhtml_managebankdetails/edit/', 'params' => array (
            'store' => $this->getRequest ()->getParam ( 'store' ) 
          ) 
        ),
        'field' => 'id' 
      ) 
    ),
    'filter' => false, 'sortable' => false,'index' => 'stores' 
  ) );
  /**
   * Add new column form delete action
   */
  $this->addColumn ( 'deleteaction', array ( 'header' => Mage::helper ( 'airhotels' )->__ ( 'Delete Action' ), 'width' => '50px',
    'type' => 'action','getter' => 'getId', 'actions' => array ( array ( 'caption' => Mage::helper ( 'airhotels' )->__ ( 'Delete' ), 'url' => array ( 'base' => 'airhotels/adminhtml_managebankdetails/delete/', 'params' => array ('store' => $this->getRequest ()->getParam ( 'store' ) ) ),'field' => 'id','confirm' => Mage::helper ( 'airhotels' )->__ ( 'Are you sure?' )  ) ), 'filter' => false, 'sortable' => false, 'index' => 'stores' ) );
  /**
   * Calling the parent Construct Method.
   */
  return parent::_prepareColumns ();
 }
 /**
  * Getting magange bank details row url
  */
 public function getRowUrl($row) {
  return $this->getUrl ( '*/*/edit', array (
    
    'id' => $row->getId () 
  ) );
 }
 /**
  * Function Name: _prepareMassaction()
  * 
  * Mass action for Manage Bank details
  */
 protected function _prepareMassaction() {
 /**
  * Set massaction id
  * Set massaction name
  */
  $this->setMassactionIdField ( 'id' );
  $this->getMassactionBlock ()->setFormFieldName ( 'country_code' );
  /**
   * Add delete mass action 
   */ 
  $this->getMassactionBlock ()->addItem ( 'delete', array (
    'label' => Mage::helper ( 'airhotels' )->__ ( 'Delete' ),
    'url' => $this->getUrl ( '*/*/massDelete' ),
    'confirm' => Mage::helper ( 'airhotels' )->__ ( 'Are you sure?' ) 
  ) );
  return $this;
 }
}