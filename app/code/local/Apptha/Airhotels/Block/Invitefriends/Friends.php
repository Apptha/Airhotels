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
 * This class contains Invite Friends manipulation
 */
class Apptha_Airhotels_Block_Invitefriends_Friends extends Mage_Catalog_Block_Product_Abstract {
 
 /**
  * Getting friends wishlist details
  *
  * @param string $customerId         
  * @return array $customerData
  */
 function getFirendsWishlist($customerIds) {
 /**
  * Initialize product data and customer data as null.
  * 
  * @var $productData
  * @var $customer
  */
  $productData = $customer = array ();
  foreach ( $customerIds as $customerId ) {
  /**
   * Get customer data
   */
   $customer = Mage::getModel ( 'customer/customer' )->load ( $customerId );
   /**
    * Get whishlist collection.
    * 
    * @var $wishList
    */
   $wishList = Mage::getModel ( 'wishlist/wishlist' )->loadByCustomer ( $customer );
   $wishListItemCollection = $wishList->getItemCollection ();
   foreach ( $wishListItemCollection as $item ) {
    $productData [] = $item->getProductId ();
   }
  }
  return $productData;
 }
 
 /**
  * Get product details
  *
  * @param array $productIds         
  * @return array $propertyCollection
  */
 public function getpropertycollection($productIds, $pageNo, $pageResultCount) {
 /**
  * Return property collction .
  * 
  * Filter by 'type_id','status','propertyapproved'
  */
  return Mage::getModel ( 'catalog/product' )->getCollection ()->addAttributeToFilter ( 'type_id', array (
    'eq' => 'property' 
  ) )->addAttributeToFilter ( 'status', array (
    'eq' => 1 
  ) )->addAttributeToFilter ( 'propertyapproved', array (
    'eq' => 1 
  ) )->addAttributeToSelect ( '*' )->addAttributeToFilter ( 'entity_id', array (
    'in' => $productIds 
  ) )->setPage ( $pageNo, $pageResultCount );  
 }
 
 /**
  * Get product count
  *
  * @param array $productIds         
  * @return array $propertyCollection
  */
 public function getpropertycollectionCount($productIds) {
 /**
  * Return property collction .
  * 
  * Filter by 'type_id','status','propertyapproved'
  */
  $propertyCollection = Mage::getModel ( 'catalog/product' )->getCollection ()->addAttributeToFilter ( 'type_id', array (
    'eq' => 'property' 
  ) )->addAttributeToFilter ( 'status', array (
    'eq' => 1 
  ) )->addAttributeToFilter ( 'propertyapproved', array (
    'eq' => 1 
  ) )->addAttributeToSelect ( '*' )->addAttributeToFilter ( 'entity_id', array (
    'in' => $productIds 
  ) );
  return count ( $propertyCollection );
 }
 
 /**
  * Get customer name and country
  * 
  * @param unknown $cutomerId         
  */
 function getCustomerDetails($productId, $friendIds) {
 /**
  * Initialize whishlist customer id
  * 
  * @var $wishlistCustomerId
  */
  $wishlistCustomerId = array ();
  foreach ( $friendIds as $friendId ) {
  /**
   * Get whislist collection
   * 
   * @var $wishlist
   */
   $wishlist = Mage::getModel ( 'wishlist/item' )->getCollection ();
   $wishlist->getSelect ()->join ( array (
     't2' => 'wishlist' 
   ), 'main_table.wishlist_id = t2.wishlist_id', array (
     'wishlist_id',
     'customer_id' 
   ) )->where ( 'main_table.product_id = ' . $productId . ' AND t2.customer_id=' . $friendId );
   /**
    * Get whislist count.
    * 
    * @var $count
    */
   $count = $wishlist->count ();
   if ($count >= 1) {
    $wishlistCustomerId [] = $friendId;
   }
  }
  /**
   * Return whishlist customerId.
   */
  return $wishlistCustomerId;
 }
}