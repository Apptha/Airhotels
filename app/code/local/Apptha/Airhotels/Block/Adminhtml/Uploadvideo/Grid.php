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
 * Grid for neighbourhoods cities
 */
class Apptha_Airhotels_Block_Adminhtml_Uploadvideo_Grid extends Mage_Adminhtml_Block_Widget_Grid {
 /**
  * Class constructor
  */
 public function __construct() {
 /**
  * Calling the parent Construct Method.
  */
  parent::__construct();
  $this->setId( 'videoGrid' );
  $this->setDefaultSort( 'id' );
  $this->setDefaultDir( 'DESC' );
  $this->setSaveParametersInSession( true );
 }
 /**
  * 
  * Prepare collection for uploaded video
  *
  * @return collection
  */
 protected function _prepareCollection() {
  $collection = Mage::getModel ( 'airhotels/uploadvideo' )->getCollection();
  $this->setCollection( $collection );
  /**
   * Calling the parent Construct Method.
   */
  return parent::_prepareCollection();
 } 
 /**
  * Prepare columns for uploaded video
  *
  * @return collection
  * 
  * @column id
  * @column video_name
  * @column video_url_mp4
  * @column video_url_webm
  * @column status
  * @column created_at
  * @column editaction
  * @column deleteaction
  * 
  */
 protected function _prepareColumns() {
  $this->addColumn ( 'id', array ('header' => Mage::helper ( 'airhotels' )->__ ( 'Id' ),'align' => 'left', 'index' => 'id') );
  $this->addColumn ( 'video_name', array ('header' => Mage::helper ( 'airhotels' )->__ ( 'Video Name' ),'align' => 'left','index' => 'video_name') );
  $this->addColumn ( 'video_url_mp4', array ('header' => Mage::helper ( 'airhotels' )->__ ( 'Video Url(MP4)' ),'align' => 'left','index' => 'video_url_mp4') );
  $this->addColumn ( 'video_url_webm', array ('header' => Mage::helper ( 'airhotels' )->__ ( 'Video Url(WEBM)' ),'align' => 'left','index' => 'video_url_webm' ) );
  /**
   * Serialize the images and saved in database so we can't able to display multiple images in grid.
   * removed the images section in grid.
   */
  $this->addColumn ( 'status', array ('header' => Mage::helper ( 'airhotels' )->__ ( 'Status' ),'align' => 'left', 'index' => 'status', 'type' => 'options', 'options' => array (
      1 => 'Enable',  2 => 'Disable' 
    ) 
  ) );
  /**
   * Add new column for created_at
   */
  $this->addColumn ( 'created_at', array (
    'header' => Mage::helper ( 'airhotels' )->__ ( 'Created Date' ),
    'align' => 'left', 'index' => 'created_at', 'type' => 'datetime' 
  ) );
  /**
   * Add new column for editaction
   */
  $this->addColumn ( 'editaction', array (
    'header' => Mage::helper ( 'airhotels' )->__ ( 'Edit Action' ),
    'width' => '50px', 'getter' => 'getId','type' => 'action',
    'actions' => array (
      array (
        'caption' => Mage::helper ( 'airhotels' )->__ ( 'Edit' ),
        
        'url' => array (
          'base' => 'airhotels/adminhtml_uploadvideo/edit/',
          'params' => array ( 'store' => $this->getRequest ()->getParam ( 'store' )) 
        )
        ,
        'field' => 'id' 
      ) 
    ),
    
    'sortable' => false,'filter' => false,'index' => 'stores' 
  ) );
  /**
   * Add new column for deleteaction
   */
  $this->addColumn ( 'deleteaction', array (
    'header' => Mage::helper ( 'airhotels' )->__ ( 'Delete Action' ),
    'width' => '50px', 'type' => 'action','getter' => 'getId',
    'actions' => array (
      array (
        'caption' => Mage::helper ( 'airhotels' )->__ ( 'Delete' ),'url' => array (
          'base' => 'airhotels/adminhtml_uploadvideo/delete/','params' => array ('store' => $this->getRequest ()->getParam ( 'store' )) 
        ),
        'field' => 'id','confirm' => Mage::helper ( 'airhotels' )->__ ( 'Are you sure?' ) 
      ) 
    ),
    'filter' => false,'sortable' => false,'index' => 'stores') );
  /**
   * Calling the parent Construct Method.
   */
  return parent::_prepareColumns ();
 } 
 /**
  * Getting row url
  * 
  * Redirect to edit url
  */
 public function getRowUrl($row) {
  /**
   * Return the url based on ID
   */
  return $this->getUrl ( '*/*/edit', array (
    'id' => $row->getId() 
  ) );
 } 
 /**
  * Function Name: _prepareMassaction()
  * 
  * Mass action for neighbourhoods city
  */
 protected function _prepareMassaction() {
  /**
   * set the Mass Action feild
   */
  $this->setMassactionIdField( 'id' );
  /**
   * Set the form feild name
   */
  $this->getMassactionBlock ()->setFormFieldName ( 'videos' );
  /**
   * Add option for mass delete action
   */ 
  $this->getMassactionBlock ()->addItem ( 'delete', array (
    'label' => Mage::helper ( 'airhotels' )->__ ( 'Delete' ),'url' => $this->getUrl ( '*/*/massDelete' ),
    'confirm' => Mage::helper ( 'airhotels' )->__ ( 'Are you sure?' ) ) );
  return $this;
 }
}