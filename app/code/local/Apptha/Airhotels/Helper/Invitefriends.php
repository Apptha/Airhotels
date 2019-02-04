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
 * Class Apptha_Airhotels_Helper_Invitefriends
 * 
 * extend Mage_Core_Helper_Abstract
 * @author user
 *
 */
class Apptha_Airhotels_Helper_Invitefriends extends Mage_Core_Helper_Abstract {
 
 /**
  * Function Name: getInviteMailUrl
  * Retrieve invite mail Url
  *
  * @return String $url
  */
 public function getInviteMailUrl() {
     /**
      * Getting invite mail url
      */
  return Mage::getUrl ( 'airhotels/invitefriends/invitemail', array (
    '_secure' => true 
  ) );
 } 
 /**
  * Function getReferralUrlForCustomer
  * 
  * Retrieve referral url
  * @var $customerData
  * @var $customerEmail
  * 
  * @return string $url
  */
 public function getReferralUrlForCustomer() {
  $customerData = Mage::getSingleton ( 'customer/session' )->getCustomer ();
  $customerEmail = $customerData->getEmail ();
  /**
   * return customer email url
   */
  return Mage::getBaseUrl () . '?ref=' . $customerEmail;
 }
 
 
 /**
  * Retrieve fb share title
  *
  * @return string
  */
 public function getFbShareTitle() {
     /**
      * Getting FBsharetitle
      */
  return Mage::helper ( 'airhotels' )->__ ( 'Take a trip!' );
 }
 
 /**
  * Retrieve fb share summary
  *
  * @return string
  */
 public function getFbShareSummary() {
     /**
      * Getting FbShareSummary
      */
  return Mage::helper ( 'airhotels' )->__ ( 'Discover and book unique experience around the world with' ) . ' ' . $this->getSiteTitle ();  
 }
 
 /**
  * Retrieve fb share summary
  *
  * @return string
  */
 public function getFbShareCaption() {
     /**
      * Getting FbShareCaption
      */
  return Mage::helper ( 'airhotels' )->__ ( "We \'ll help you pay for it" );
 } 
 /**
  * Retrieve fb share imgae src
  *
  * @return string
  */
 public function getFbShareImageSrc() {
     /**
      * Getting FbShareImageSrc
      */
  return Mage::getBaseUrl ( 'skin' ) . 'frontend/default/stylish/images/logo.gif';
 } 
 /**
  * Retrieve fb share enabled or not
  *
  * @return boolean
  */
 public function getFbShateEnabledorNot() {
     /**
      * Getting FbShateEnabledorNot url from admin configuration
      */
  return Mage::getStoreConfig ( 'property/invitefriends/purchase_credit' );
 }
 
 /**
  * Retrieve site title
  *
  * @return string
  */
 public function getSiteTitle() {
     /**
      * Setting SiteTitle from admin configuration
      */
  return Mage::getStoreConfig ( 'property/custom_group/airhotels_title' );
 } 
 /**
  * Retrieve first purchase discount enabled or not
  *
  * @return string
  */
 public function getFirstPurchaseDiscountEnabledOrNot() {
     /**
      * Getting pruchase discount amount from admin configuration
      */
  return Mage::getStoreConfig ( 'property/invitefriends/first_purchase_discount_enable' );
 } 
 /**
  * Retrieve Profile Url
  * 
  * @return string
  */
 public function getProfileUrl() {
     /**
      * Getting customer details from session
      * @var unknown $customer
      */
  $customer = Mage::getSingleton ( 'customer/session' )->getCustomer ();
  return $this->_getUrl ( 'airhotels/index/profile/id/' . $customer->getId () );
 } 
 /**
  * Getting all friends url
  *
  * @return String $url
  */
 public function getAllFriendsUrl() {
     /**
      * Set invitefriends Url
      */
  return Mage::getUrl ( 'airhotels/invitefriends/index', array (
    '_secure' => true 
  ) );
 } 
 /**
  * Retrieve invite friends enabled or not
  *
  * @return integer
  */
 public function getInviteFriendsEnabledOrNot() {
     /**
      * configuartion for checking invite friends module is ensble or not
      */
  return Mage::getStoreConfig ( 'airhotels/invitefriends/enable' );
 } 
 /**
  * Retrieve credit amount for each invitee purchase
  *
  * @return integer
  */
 public function getCreditAmountForPurchase() {
     /**
      * purchase credit amount from configuration
      */
  return Mage::getStoreConfig ( 'airhotels/invitefriends/purchase_credit' );
 } 
 /**
  * Retrieve credit amount for each invitee listing
  *
  * @return integer
  */
 public function getCreditAmountForListing() {
     /**
      * Getting credit amount based on listings
      */
  return Mage::getStoreConfig ( 'airhotels/invitefriends/listing_credit' );
 } 
 /**
  * Retrieve first purchase discount amount
  *
  * @return string $amount
  */
 public function getDiscountForFirstPurchase() {
     /**
      * Setting first purchase discount amount
      * @var Ambiguous $amount
      */
  $amount = Mage::getStoreConfig ( 'airhotels/invitefriends/first_purchase' );
  if (empty ( $amount )) {
   $amount = 25;
  }
  return $amount;
 } 
 /**
  * Retrieve first purchase discount amount
  *
  * @return string $amount
  */
 public function getDiscountForFirstPurchaseLimitAmount() {
     /**
      * Setting first purchase limit amount
      * @var Ambiguous $amount
      */
  $amount = Mage::getStoreConfig ( 'airhotels/invitefriends/first_purchase_limit' );
  if (empty ( $amount )) {
   $amount = 75;
  }
  return $amount;
 } 
 /**
  * Function to upload profile video
  * 
  * @param string $name         
  * @param array $filesDataArray         
  * @return string
  */
 public function uploadProfileVideo($name, $filesDataArray) {  
  /**
   * checking file extension
   */
  $path = Mage::getBaseDir ( 'media' ) . DS . 'catalog' . DS . 'customer' . DS;
  $uploader = new Varien_File_Uploader ( $name );
  $uploader->setAllowedExtensions ( array (
    'mp4', 'avi', '3gp', 'mov','webm','flv', 'mpeg4', 'mpegps', 'wmv' 
  ) );
  $uploader->setAllowRenameFiles ( false );
  $uploader->setFilesDispersion ( false );
  /**
   * Save profile video
   */
  $uploader->save ( $path, $filesDataArray [$name] ['name'] );
  return Mage::getBaseUrl ( 'media' ) . DS . 'catalog' . DS . 'customer' . DS . $uploader->getUploadedFileName ();  
 } 
 /**
  * Retrieve customer credit amount
  * 
  * @param int $customerId         
  * @return int $creditAmount
  */
 public function getCustomerCreditAmount($customerId) {  
  $websiteId = Mage::app ()->getStore ()->getWebsiteId ();
  /**
   * Get invite friends collection.
   */
  $inviteFriendsCollection = Mage::getModel ( 'airhotels/invitefriends' )->getCollection ()->addFieldToFilter ( 'customer_id', $customerId )->addFieldToFilter ( 'website_id', $websiteId )->getFirstItem ();
  return $inviteFriendsCollection->getBalanceCreditAmount ();  
 } 
 /**
  * Retrieve invite mail Url
  *
  * @return String $url
  */
 public function getAddCreditDiscountForInviteeUrl() {
  return Mage::getUrl ( 'property/invitefriends/addDiscount', array (
    '_secure' => true 
  ) );  
 } 
 /**
  * Upload image for city
  *
  * @param File $filesDataArray         
  * @param String $name         
  * @param String $path         
  * @return String $imagesPath
  */
 public function uploadImageForVideoImage($filesDataArray, $name, $path, $id) {
  $imagesPath = '';
  if (isset ( $filesDataArray [$name] ['name'] ) && $filesDataArray [$name] ['name'] != '') {
   /**
    * File path to store the city image
    */
   $cityImageName = $path . $filesDataArray [$name] ['name'];
   $splitExtension = explode ( ".", $cityImageName );
   $arrayCount = count ( $splitExtension );
   if (isset ( $splitExtension [$arrayCount - 1] )) {
    $imageNameForSave = $id . '.' . $filesDataArray [$name] ['name'];
   } else {
    $imageNameForSave = $cityImageName;
   }
   /**
    * checking file extension
    */
   $uploader = new Varien_File_Uploader ( $name );
   $uploader->setAllowedExtensions ( array (
     'jpg', 'jpeg', 'gif', 'png' 
   ) );
   $uploader->addValidateCallback ( 'catalog_product_image', Mage::helper ( 'catalog/image' ), 'validateUploadFile' );
   $uploader->setAllowRenameFiles ( false );
   $uploader->setFilesDispersion ( false );
   /**
    * Save city image to DB
    */
   $uploader->save ( $path, $imageNameForSave );
   $imagesPath = $path . $uploader->getUploadedFileName ();
  }
  /**
   * Return image path.
   */
  return $imagesPath;
 }
}