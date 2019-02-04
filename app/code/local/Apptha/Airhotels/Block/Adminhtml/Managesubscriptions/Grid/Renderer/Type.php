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
 * It is Renderer class for 'subscriptiontype' column in subscriptions grid.
 */
class Apptha_Airhotels_Block_Adminhtml_Managesubscriptions_Grid_Renderer_Type extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {
 
 /**
  * Function to call render for a subscription grid column.
  * (non-PHPdoc)
  *
  * @see Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract::render()
  *
  * @param $row as
  *         varien object.
  * @return string
  */
 public function render(Varien_Object $row) {
  /**
   * Get Data From the colletion
   */
  $collection = $row->getData ();
  $commaseperated = $result = null;
  /**
   * Get the Product ID from the Colletion
   */
  $productId = $collection ['product_id'];
  /**
   * Get the productSubscriptions Collection
   */
  $productModel = Mage::getModel ( 'airhotels/productsubscriptions' )->getCollection ()->addFieldToSelect ( 'subscription_type' )->addFieldToSelect ( 'product_id' )->addFieldToFilter ( 'is_delete', 0 )->addFieldToFilter ( 'product_id', $productId );
  $value = array ();
  foreach ( $productModel as $productModelValue ) {
   $value [] = $productModelValue ['subscription_type'];
   $commaseperated = implode ( " ", $value );
  }
  /**
   * Initialze the Subscription Data
   */
  $subscriptionData = array ();
  /**
   * Manage the subscription Array
   */
  $subscriptionData = $this->subscriptionArray ( $commaseperated );
  /**
   * Check weather the subscription Data
   */
  if (! empty ( $subscriptionData )) {
   $result = implode ( " ", $subscriptionData );
  }   
  return $result;
 }
 
 /**
  * Function to call toOptionArray ,get all product subscriptiontypes.
  *
  * @param $subscriptionlistValue as
  *         String
  * @return array
  */
 public function subscriptionArray($subscriptionlistValue) {
  /**
   * Declaring the subscriptionType Result, result Values
   */
  $subscriptionTypeResult = null;
  $result = array ();
  $result [] = explode ( " ", $subscriptionlistValue );
  $final = null;
  /**
   * Iterating the Result Array
   */
  foreach ( $result as $resultData ) {
   /**
    * Get theColletion of subscription type
    */
   $subscriptionTitle = Mage::getModel ( 'airhotels/subscriptiontype' )->getCollection ()->addFieldToFilter ( 'id', $resultData );
   $final = $subscriptionTitle->getData ();
   
   foreach ( $final as $countData ) {
    $subscriptionTypeResult [] = $countData ['title'];
   }
  }
  /**
   * Returning the subscriptionTypeResult
   */
  return $subscriptionTypeResult;
 }
}