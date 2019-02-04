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
 * This class contains the functionality of home page and ratings
 */
class Apptha_Airhotels_Block_Airhotels extends Mage_Core_Block_Template {
 
 /**
  * Set page title
  *
  * @return object layout
  */
 public function _prepareLayout() {
  /**
   * Set the airhotel Title
   */
  $title = Mage::getStoreConfig ( 'airhotels/custom_group/airhotels_title' );
  /**
   * Getting Layout
   */
  $this->getLayout ()->getBlock ( 'head' )->setTitle ( $this->__ ( $title ) );
  /**
   * Calling the parent Construct Method.
   */
  return parent::_prepareLayout ();
 }
 
 /**
  * Get airhotels registry data
  *
  * @return object airhotels
  */
 public function getAirhotels() {
  /**
   * check airhotel hasdata
   */
  if (! $this->hasData ( 'airhotels' )) {
   /**
    * Setting Registry
    */
   $this->setData ( 'airhotels', Mage::registry ( 'airhotels' ) );
  }
  return $this->getData ( 'airhotels' );
 }
 
 /**
  * Prepare rating stars
  *
  * @param int $count
  *         rating count
  * @return string $htmlElements rating html content
  */
 public function showratingCode($count = 0) {
  $htmlElements = '';
  /**
   * Adding the images
   */
  for($x = 1; $x <= $count; $x ++) {
   $htmlElements = $htmlElements . "<img style='float:left'  src='" . $this->getSkinUrl ( 'images/red.png' ) . "' width='16' height='16' alt='' />";
  }
  for($i = $x; $i <= 5; $i ++) {
   $htmlElements = $htmlElements . "<img style='float:left'  src='" . $this->getSkinUrl ( 'images/grey.png' ) . "' width='16' height='16' alt=''/>";
  }
  /**
   * Return html elements.
   */
  return $htmlElements;
 }
 
 /**
  * Prepare recent product collection for home page banner slider
  *
  * @return object $_productCollection product collection
  */
 public function recentPro($bannerCount) {
  /**
   * returning the airhotels property Collection for recent Properties
   */
  return Mage::getModel ( 'airhotels/property' )->getpropertycollection ()->
  /**
   * Selecting All Attributes
   */
  addAttributeToSelect ( '*' )->
  /**
   * Filter by status
   */
  addAttributeToFilter ( 'status', array (
    'eq' => 1 
  ) )->
  /**
   * Filter by property Approved
   */
  addAttributeToFilter ( 'propertyapproved', array (
    'eq' => 1 
  ) )->
  /**
   * Set Order
   */
  setOrder ( 'created_at', 'desc' )->setPageSize ( $bannerCount );
 }
 
 /**
  * Prepare Host selected product collection for home page banner slider
  *
  * @return object $_productCollection product collection
  */
 public function hostSelectedPro($bannerCount) {
  /**
   * returning the airhotels property Collection host selected Properties
   */
  return Mage::getModel ( 'airhotels/property' )->getpropertycollection ()->addAttributeToSelect ( '*' )->addAttributeToFilter ( 'status', array (
    'eq' => 1 
  ) )->/**
   * Filter by Banner
   */
  addAttributeToFilter ( 'banner', array (
    'eq' => 1 
  ) )->addAttributeToFilter ( 'propertyapproved', array (
    'eq' => 1 
  ) )->setOrder ( 'created_at', 'desc' )->setPageSize ( $bannerCount );
 }
 
 /**
  * Prepare popular product collection for home page banner slider
  *
  * @return object $_productCollection product collection
  */
 public function popularPro($bannerCount) {
  /**
   * Return the resource model
   */
  return Mage::getResourceModel ( 'reports/product_collection' )->addAttributeToSelect ( '*' )->addOrderedQty ()->/**
   * Filter by status
   */
  addAttributeToFilter ( 'status', array (
    'eq' => 1 
  ) )->/**
   * Filter by type ID
   */
  addAttributeToFilter ( 'type_id', array (
    'eq' => 'property' 
  ) )->/**
   * Filter by name
   */
  addAttributeToFilter ( 'name', array (
    'neq' => '' 
  ) )->/**
   * Filter by proeprty approved
   */
  addAttributeToFilter ( 'propertyapproved', array (
    'eq' => 1 
  ) )->setOrder ( 'ordered_qty', 'DESC' )->setPageSize ( $bannerCount );
 }
}