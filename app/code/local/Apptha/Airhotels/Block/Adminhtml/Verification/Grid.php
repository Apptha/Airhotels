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
 * Grid for verification tags
 * 
 * @abstract Mage_Adminhtml_Block_Widget_Grid
 */
class Apptha_Airhotels_Block_Adminhtml_Verification_Grid extends Mage_Adminhtml_Block_Widget_Grid { 
 /**
  * Class constructor
  * 
  * @abstract Mage_Adminhtml_Block_Widget_Grid
  */
 public function __construct() {
  /**
   * Initalising the grid
   */
  parent::__construct ();
  /**
   * Settig the 'hostGrid'
   */
  $this->setId ( 'hostGrid' );
  /**
   * Setting the default sort to "customer_id"
   */
  $this->setDefaultSort ( 'customer_id' );
  $this->setDefaultDir ( 'DESC' );
  $this->setSaveParametersInSession ( true );
 } 
 /**
  * Prepare collection for verification tags
  *
  * @return collection
  */
 protected function _prepareCollection() {
 /**
  * Get tagverification collection.
  */
  $collectionInfo = Mage::getModel ( 'airhotels/tagsverification' )->getCollection ();
  $this->setCollection ( $collectionInfo );
  /**
   * Calling the parent Construct Method.
   */
  return parent::_prepareCollection ();
 } 
 /**
  * Prepare columns for verification tags
  *
  * @return collection
  */
 protected function _prepareColumns() {
 /**
  * Display columns
  * 
  * 'tag_id','tag_name'
  * 'tag_description','direct_url'
  */
  $this->addColumn ( 'tag_id', array ('header' => Mage::helper ( 'airhotels' )->__ ( 'Id' ),'align' => 'left','index' => 'tag_id') );
  $this->addColumn ( 'tag_name', array ('header' => Mage::helper ( 'airhotels' )->__ ( 'Tag Name' ),'align' => 'left','index' => 'tag_name') );
  /**
   * Add new column for tag_description and direct_url
   */
  $this->addColumn ( 'tag_description', array ('header' => Mage::helper ( 'airhotels' )->__ ( 'Tag Description' ),'align' => 'left','index' => 'tag_description') );
  $this->addColumn ( 'direct_url', array ('header_css_class' => 'a-center','header' => Mage::helper ( 'airhotels' )->__ ( 'Allowed direct Url' ),'index' => 'direct_url','type' => 'checkbox','align' => 'center','values' => array ('1', '2')) );
  return parent::_prepareColumns ();
 }
 
 /**
  * Getting row url
  */
 public function getRowUrl($rowId) {
  return $this->getUrl ( '*/*/edit', array (
    'id' => $rowId->getId () 
  ) );
 } 
 /**
  * Mass action for document and video verification
  */
 protected function _prepareMassaction(){
  /**
   * Mass Action for 'tag_id'
   */
  $this->setMassactionIdField ('tag_id');
  /**
   * Mass Action for "tagData"
   */
  $this->getMassactionBlock()->setFormFieldName ( 'tagData' );
  $this->getMassactionBlock()->addItem ( 'delete', array ('label' => Mage::helper ( 'airhotels' )->__ ( 'Delete' ),'url' => $this->getUrl ( '*/*/massDelete' ),'confirm' => Mage::helper ( 'airhotels' )->__ ( 'Are you sure?' )));
  return $this;
 }
}