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
 * Class Apptha_Airhotels_Helper_Airhotel
 * 
 * extend Mage_Core_Helper_Abstract
 * @author user
 *
 */
class Apptha_Airhotels_Helper_Airhotel extends Mage_Core_Helper_Abstract {
 
 /**
  * Function Name: 'categoryIdForinitProduct'
  * Getting category Id for Init Product
  *
  * @param unknown $categoryId         
  * @return unknown
  */
 public function categoryIdForinitProduct($categoryId, $product) {
  if (! $categoryId && ($categoryId !== false)) {
   /**
    * last Id Value
    */
   $lastId = Mage::getSingleton ( 'catalog/session' )->getLastVisitedCategoryId ();
   if ($product->canBeShowInCategory ( $lastId )) {
    $categoryId = $lastId;
   }
  }
  return $categoryId;
 }
 
 /**
  * function Name : overallTotalHours
  * Getting the overall Hours.
  *
  * @param number $perHourNightFee         
  * @param number $overallHours         
  * @return string $overallTotalHours
  */
 public function overallTotalHours($perHourNightFee, $overallHours) {
  if ($perHourNightFee >= 1) {
   $message = 'Excluded night hour(s)';
   /**
    * get the overalol total hours
    */
   $overallTotalHours = $overallHours . ' ' . $message;
  }
  return $overallTotalHours;
 }
 
 /**
  * Function Name: getInboxMessageDetailsForCustomerDashboard
  * Getting new inbox message details for customer
  *
  * @return array $customerMessage customer collection
  */
 public function getInboxMessageDetailsForCustomerDashboard() {
  /**
   * Defining array
   */
  $resultData = array ();
  /**
   * Getting customer by session
   */
  $customer = Mage::getSingleton ( 'customer/session' )->getCustomer ();
  /**
   * Gtting cutomer ID
   */
  $CusId = $customer->getId ();
  /**
   * Get the Colletion for CustomerInbox
   */
  $resultOne = Mage::getModel ( 'airhotels/customerinbox' )->getCollection ()->addFieldToFilter ( 'receiver_id', $CusId )->addFieldToFilter ( 'receiver_read', array (
    'in' => array (
      0,
      '' 
    ) 
  ) )->addFieldToFilter ( 'is_receiver_delete', 0 )->setOrder ( 'created_date', 'DESC' );
  /**
   * get theCOlletion for customerinbox with filters
   */
  $resultTwo = Mage::getModel ( 'airhotels/customerinbox' )->getCollection ()->addFieldToFilter ( 'sender_id', $CusId )->addFieldToFilter ( 'sender_read', array (
    'in' => array (
      0,
      '' 
    ) 
  ) )->addFieldToFilter ( 'is_reply', 1 )->addFieldToFilter ( 'is_sender_delete', 0 )->setOrder ( 'created_date', 'DESC' );
  /**
   * Defining Foreach
   */
  foreach ( $resultOne as $res ) {
   $resultData [] = $res->getMessageId ();
  }
  /**
   * Iterating the loop
   */
  foreach ( $resultTwo as $res ) {
   $resultData [] = $res->getMessageId ();
  }
  /**
   * returning the Colletion of arrays
   */
  return Mage::getModel ( 'airhotels/customerinbox' )->getCollection ()->addFieldToFilter ( 'message_id', array (
    'in' => $resultData 
  ) );
 }
 
 /**
  * Function Name: 'getaccomodatesType'
  * Retrieve attribute id for accomodates
  *
  * @return (int)accomodates
  */
 public function getaccomodatesType() {
  /**
   * Getting Model from attribute table
   */
  return Mage::getResourceModel ( 'eav/entity_attribute' )->getIdByCode ( 'catalog_product', 'accomodates' );
 }
 
 /**
  * Function Name: 'getbedtype'
  * Retrieve attribute id for bedtype
  *
  * @return bedtype
  */
 public function getbedtype() {
     /**
      * Getting bedType attribute value
      */
  return Mage::getResourceModel ( 'eav/entity_attribute' )->getIdByCode ( 'catalog_product', 'bedtype' );
 } 
 /**
  * Function Name: 'getRooms'
  * Retrieve attribute id for totalrooms
  *
  * @return totalrooms
  */
 public function getrooms() {
     /**
      * Getting rooms attribute value
      */
  return Mage::getResourceModel ( 'eav/entity_attribute' )->getIdByCode ( 'catalog_product', 'totalrooms' );
 } 
 /**
  * Function Name": getPets"
  * Retrieve attribute id for pets
  *
  * @return pets
  */
 public function getpets() {
     /**
      * Getting pets attribute value
      */
  return Mage::getResourceModel ( 'eav/entity_attribute' )->getIdByCode ( 'catalog_product', 'pets' );
 } 
 /**
  * Function Nme : "getMyWishlistUrl"
  * Retrive My wishlist url
  *
  * @return string
  */
 public function getMyWishlistUrl() {
     /**
      * Getting MyWishlistUrl details
      */
  return $this->_getUrl ( 'wishlist' );
 } 
 /**
  * Function Name: getEditProfileUrl
  * Getting getEditProfileUrl
  * uploadphoto
  * Retrive Edit profile url
  *
  * @return string
  */
 public function getEditProfileUrl() {
     /**
      * Getting getEditProfileUrl
      */
  return $this->_getUrl ( 'property/property/uploadphoto' );
 } 
 /**
  * Function Name: getSentItemsUrl
  * Retrive My sent items url
  *
  * @return string
  */
 public function getSentItemsUrl() {
     /**
      * Getting getSentItemsUrl
      */
  return $this->_getUrl ( 'property/property/senditem' );
 } 
 /**
  * Function Name: getInboxUrl
  * Retrive My inbox url
  *
  * @return string
  */
 public function getInboxUrl() {
     /**
      * Getting getInboxUrl
      */
  return $this->_getUrl ( 'property/property/inbox' );
 }
 
 /**
  * Function Name: getPropertyMinimumByProductId
  * getInboxUrl: 'getPropertyMinimumByProductId'
  * Retrieve property minimum
  *
  * @return integer
  */
 public function getPropertyMinimumByProductId($productId) {
  /**
   * Loading Product Details
   */
  $productData = Mage::getModel ( 'catalog/product' )->load ( $productId );
  /**
   * return product information
   */
  return $productData->getPropertyMinimum ();
 }
 /**
  * Function Name: getPropertyMaximumByProductId
  * Retrieve property maximum
  *
  * @return integer
  */
 public function getPropertyMaximumByProductId($productId) {
  /**
   * Loading Product Details
   */
  $productData = Mage::getModel ( 'catalog/product' )->load ( $productId );
  /**
   * return product information
   */
  return $productData->getPropertyMaximum ();
 }
 /**
  * Function Name: getPropertyOverNightFeeByProductId
  * Retrieve property over night fee
  *
  * @return float
  */
 public function getPropertyOverNightFeeByProductId($productId) {
  /**
   * Loading Product Details
   */
  $productData = Mage::getModel ( 'catalog/product' )->load ( $productId );
  /**
   * property OverNight Fee
   */
  $propertyOvernightFee = $productData->getPropertyOvernightFee ();
  /**
   * return $propertyOvernightFee
   */
  if (empty ( $propertyOvernightFee )) {
   $propertyOvernightFee = 0;
  }
  return $propertyOvernightFee;
 } 
 /**
  * Function Name: 'getPropertyServiceToTimeByProductId'
  * Retrieve property service to time
  *
  * @return string
  */
 public function getPropertyServiceToTimeByProductId($productId) {
  /**
   * load the prodiuct Id into the ProductData
   */
  $productData = Mage::getModel ( 'catalog/product' )->load ( $productId );
  /**
   * returning the productData validateUploadFilelue
   */
  return $productData->getPropertyServiceToTime ();
 } 
 /**
  * Function Name: 'getPropertyServiceFromTimeByProductId'
  * Retrieve property service from time
  *
  * @return string
  */
 public function getPropertyServiceFromTimeByProductId($productId) {
  /**
   * Product Data
   */
  $productData = Mage::getModel ( 'catalog/product' )->load ( $productId );
  /**
   * return $productData
   */
  return $productData->getPropertyServiceFromTime ();
 }
 
 /**
  * Function Name: 'getPropertyTime'
  * Retrieve attribute id for property time
  *
  * @return integer
  */
 public function getPropertyTime() {
  return Mage::getResourceModel ( 'eav/entity_attribute' )->getIdByCode ( 'catalog_product', 'property_time' );
 }
 /**
  * Function Name: 'getPropertyTimeLabelByOptionId'
  * Retrieve attribute id by property time label
  *
  * @return integer
  */
 public function getPropertyTimeLabelByOptionId() {
  $type = 'Hourly';
  return $this->propertyTimeValueforDaily ( $type );
 }
 
 /**
  * Function Name: propertyTimeValueforDaily
  * Getting the Property Time value for daily
  *
  * @param string $type         
  * @return string
  */
 public function propertyTimeValueforDaily($type) {
  /**
   * get propertyTimeIdVal
   */
  $propertyTimeId = $this->getPropertyTime ();
  $propertyTimeValue = '';
  /**
   * Get Collection of catalog_product with 'propertyTimeValue'
   */
  $propertyAttribute = Mage::getModel ( 'eav/config' )->getAttribute ( 'catalog_product', $propertyTimeId );
  foreach ( $propertyAttribute->getSource ()->getAllOptions () as $propertyTimeOption ) {
   /**
    * PropertTime Value and
    * PropertyTime FOr
    */
   $propertyTimeLabel = $propertyTimeOption ['label'];
   $propertyTimeValue = $propertyTimeOption ['value'];
   if (! empty ( $propertyTimeLabel ) && $propertyTimeLabel == $type) {
    return $propertyTimeValue;
   }
  }
  /**
   * Returning the Colletion
   */
  return $propertyTimeValue;
 }
 
 /**
  * Function Name : 'hourly Flag'
  * Get the Hourly Flag Value
  *
  * @param array $_order         
  * @return number
  */
 public function hourlyFlag($_order) {
  $hourlyFlag = 0;
  /**
   * get the Increment Id
   */
  $incrementId = $_order->getIncrementId ();
  /**
   * get the order data Value
   */
  $orderData = Mage::getModel ( 'sales/order' )->loadByIncrementId ( $incrementId );
  /**
   * get all items value
   */
  $items = $orderData->getAllItems ();
  /**
   * Iterating loop
   */
  foreach ( $items as $item ) {
   $_options = $item->getProductOptions ();
   break;
  }
  /**
   * Iterating the Colletion
   */
  foreach ( $_options as $_option ) {
   if (! empty ( $_option ) && isset ( $_option ['property_service_from'] )) {
    $hourlyFlag = 1;
   }
   break;
  }
  /**
   * Return the Hourly Flag
   */
  return $hourlyFlag;
 }
}