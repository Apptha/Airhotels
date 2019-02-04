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
class Mage_Sales_Model_Quote_Address_Total_Discount extends Mage_Sales_Model_Quote_Address_Total_Abstract {
 /**
  * Collect Method
  *
  * @see Mage_Sales_Model_Quote_Address_Total_Abstract::collect()
  */
 public function collect(Mage_Sales_Model_Quote_Address $address) {
  /**
   * Ge thte Quote object from the address object
   */
  $quote = $address->getQuote ();
  $eventArgs = array (
    'website_id' => Mage::app ()->getStore ( $quote->getStoreId () )->getWebsiteId (),
    'customer_group_id' => $quote->getCustomerGroupId (),
    'coupon_code' => $quote->getCouponCode () 
  );
  /**
   * Set free shipping as 0.
   */
  $address->setFreeShipping ( 0 );
  /**
   * setting the discount values to zero
   */
  $totalDiscountAmount = 0;
  $subtotalWithDiscount = 0;
  $baseTotalDiscountAmount = 0;
  $baseSubtotalWithDiscount = 0;
  /**
   * return the address
   */
  $items = $address->getAllItems ();
  if (! count ( $items )) {
   /**
    * Set discount amount
    */
   $address->setDiscountAmount ( $totalDiscountAmount );
   /**
    * Set subtital amount
    */
   $address->setSubtotalWithDiscount ( $subtotalWithDiscount );
   /**
    * Set base discount amount
    */
   $address->setBaseDiscountAmount ( $baseTotalDiscountAmount );
   /**
    * Set subtotal with discount
    */
   $address->setBaseSubtotalWithDiscount ( $baseSubtotalWithDiscount );
   return $this;
  }
  /**
   * Iterate the Item object to discount Values
   */
  $hasDiscount = false;
  /**
   * Iterating the loop
   */
  foreach ( $items as $item ) {
   if ($item->getNoDiscount ()) {
    $item->setDiscountAmount ( 0 );
    $item->setBaseDiscountAmount ( 0 );
    $item->setRowTotalWithDiscount ( $item->getRowTotal () );
    $item->setBaseRowTotalWithDiscount ( $item->getRowTotal () );
    /**
     * Getting Row Total
     */
    $subtotalWithDiscount += $item->getRowTotal ();
    $baseSubtotalWithDiscount += $item->getBaseRowTotal ();
   } else {
    /**
     * Child item discount we calculate for parent
     */
    if ($item->getParentItemId ()) {
     continue;
    }
    /**
     * Composite item discount calculation
     */
    if ($item->getHasChildren () && $item->isChildrenCalculated ()) {
     /**
      * Get the DIscount amount array
      */
     $discount = Mage::getModel ( 'airhotels/customerreply' )->hasDiscount ( $item, $eventArgs );
     $totalDiscountAmount = $discount ['totalDiscountAmount'];
     $baseTotalDiscountAmount = $discount ['baseTotalDiscountAmount'];
     $subtotalWithDiscount = $discount ['subtotalWithDiscount'];
     $baseSubtotalWithDiscount = $discount ['baseSubtotalWithDiscount'];
    } else {
     $eventArgs ['item'] = $item;
     /**
      * Call the sales_quote_address_discount_item event.
      */
     Mage::dispatchEvent ( 'sales_quote_address_discount_item', $eventArgs );
     $hasDiscount = Mage::getModel ( 'airhotels/search' )->itemHasDiscount ( $item );
     /**
      * Calculate the totaldiscount amount.
      * 
      * @var $totalDiscountAmount
      */
     $totalDiscountAmount += $item->getDiscountAmount ();
     $baseTotalDiscountAmount += $item->getBaseDiscountAmount ();
     $item->setRowTotalWithDiscount ( $item->getRowTotal () - $item->getDiscountAmount () );
     $item->setBaseRowTotalWithDiscount ( $item->getBaseRowTotal () - $item->getBaseDiscountAmount () );
     $subtotalWithDiscount += $item->getRowTotalWithDiscount ();
     $baseSubtotalWithDiscount += $item->getBaseRowTotalWithDiscount ();
    }
   }
  }
  $address->setDiscountAmount ( $totalDiscountAmount );
  $address->setSubtotalWithDiscount ( $subtotalWithDiscount );
  $address->setBaseDiscountAmount ( $baseTotalDiscountAmount );
  $address->setBaseSubtotalWithDiscount ( $baseSubtotalWithDiscount );
  $address->setGrandTotal ( $address->getGrandTotal () - $address->getDiscountAmount () );
  $address->setBaseGrandTotal ( $address->getBaseGrandTotal () - $address->getBaseDiscountAmount () );
  return $this;
 }
 /**
  * Fetch the Address Detils
  *
  * @see Mage_Sales_Model_Quote_Address_Total_Abstract::fetch()
  */
 public function fetch(Mage_Sales_Model_Quote_Address $address) {
  /**
   * Get the Amount Value
   */
  $amount = $address->getDiscountAmount ();
  if ($amount != 0) {
   $title = Mage::helper ( 'sales' )->__ ( 'Discount' );
   /**
    * Getting coupon code
    */
   $code = $address->getCouponCode ();
   /**
    * Set the title value
    */
   if (strlen ( $code )) {
    /**
     * Getting discount code
     */
    $title = Mage::helper ( 'sales' )->__ ( 'Discount (%s)', $code );
   }
   /**
    * Add total
    */
   $address->addTotal ( array (
     'code' => $this->getCode (),
     'title' => $title,
     'value' => - $amount 
   ) );
  }
  return $this;
 }
}