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
class Apptha_Airhotels_Block_Adminhtml_Verifyhost_Grid extends Mage_Adminhtml_Block_Widget_Grid {
 
 /**
  * Class constructor
  */
 public function __construct() {
  parent::__construct ();
  $this->setId ( 'verifyHostGrid' );
  $this->setDefaultSort ( 'id' );
  $this->setDefaultDir ( 'DESC' );
  $this->setSaveParametersInSession ( true );
 }
 
 /**
  * Prepare collection for host verification
  *
  * @return $verifyHostCollection
  */
 protected function _prepareCollection() {
  $verifyHostCollection = Mage::getModel ( 'airhotels/verifyhost' )->getCollection ()->addFieldToFilter ( 'file_path', array (
    neq => '' 
  ) );
  $this->setCollection ( $verifyHostCollection );
  return parent::_prepareCollection ();
 }
 
 /**
  * Prepare columns for host verify table
  *
  * @return collection
  */
 protected function _prepareColumns() {
  $this->addColumn ( 'id', array ('header' => Mage::helper ( 'airhotels' )->__ ( 'Id' ),'align' => 'left','index' => 'id' ) );
  
  $this->addColumn ( 'tag_id', array (
    'header' => Mage::helper ( 'airhotels' )->__ ( 'Tag Name' ),
    'align' => 'left',
    'index' => 'tag_id' 
  ) );
  
  $this->addColumn ( 'host_name', array ('header' => Mage::helper ( 'airhotels' )->__ ( 'Host Name' ),'align' => 'left','index' => 'host_name') );
  
  $this->addColumn ( 'host_email', array (
    'header' => Mage::helper ( 'airhotels' )->__ ( 'Host Email' ),
    'align' => 'left',
    'index' => 'host_email' 
  ) );
  
  $this->addColumn ( 'deleteaction', array ('header' => Mage::helper ( 'airhotels' )->__ ( 'Download File' ),'align' => 'left','index' => 'id','renderer' => 'Apptha_Airhotels_Block_Adminhtml_Verifyhost_Download' ) );
  
  $this->addColumn ( 'id_type', array (
    'header' => Mage::helper ( 'airhotels' )->__ ( 'Id Type' ),
    'align' => 'left',
    'index' => 'id_type',
    'renderer' => 'Apptha_Airhotels_Block_Adminhtml_Verifyhost_type' 
  ) );
  
  $this->addColumn ( 'country', array ('header' => Mage::helper ( 'airhotels' )->__ ( 'Country' ),'align' => 'left', 'index' => 'country_code','renderer' => 'Apptha_Airhotels_Block_Adminhtml_Verifyhost_country') );
  
  $this->addColumn ( 'Verify Document', array (
    'header' => Mage::helper ( 'airhotels' )->__ ( 'Verify Document' ),'index' => 'host_tags','type' => 'options',
  'options' => array (
      0 => 'Unverified',
      1 => 'Verified',
      2 => 'Rejected'
    ) 
  ) );
  return parent::_prepareColumns ();
 }
 
 /**
  * Getting row url
  */
 public function getRowUrl($row) {
  return false;
 }
 /**
  * Getting row url
  */
 protected function _prepareMassaction() {
  $this->setMassactionIdField ( 'id' );
  $this->getMassactionBlock ()->setFormFieldName ( 'verifyhost' );
  $this->getMassactionBlock ()->addItem ( 'delete', array (
    'label' => Mage::helper ( 'airhotels' )->__ ( 'Delete' ),
    'url' => $this->getUrl ( '*/*/massDelete' ),
    'confirm' => Mage::helper ( 'airhotels' )->__ ( 'Are you sure?' ) 
  ) );
  $this->getMassactionBlock ()->addItem ( 'Verified', array (
    'label' => Mage::helper ( 'airhotels' )->__ ( 'Verified' ),
    'url' => $this->getUrl ( '*/*/massVerify', array (
      '' => '' 
    ) ) 
  ) );
  $this->getMassactionBlock ()->addItem ( 'Rejected', array (
          'label' => Mage::helper ( 'airhotels' )->__ ( 'Rejected' ),
          'url' => $this->getUrl ( '*/*/massRejected', array (
                  '' => ''
          ) )
  ) );  
  return $this;
 }
}