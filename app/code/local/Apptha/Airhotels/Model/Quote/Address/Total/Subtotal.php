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
class Apptha_Airhotels_Model_Quote_Address_Total_Subtotal extends Mage_Sales_Model_Quote_Address_Total_Subtotal {
 /**
  * Collect Method
  *
  * @see Mage_Sales_Model_Quote_Address_Total_Subtotal::collect()
  */
 public function collect(Mage_Sales_Model_Quote_Address $address) {
  /**
   * Calling the parent Construct Method.
   */
  parent::collect ( $address );
  $address->setTotalQty ( 0 );
  
  $baseVirtualAmount = $virtualAmount = 0;
  /**
   * Process address items
   */
  $items = $this->_getAddressItems ( $address );
  foreach ( $items as $item ) {
   if ($this->_initItem ( $address, $item ) && $item->getQty () > 0) {
    /**
     * Separatly calculate subtotal only for virtual products
     */
    if ($item->getProduct ()->isVirtual ()) {
     /**
      * setting the baseCurrencyCode,currentCurrencyCode
      */
     $baseCurrencyCode = Mage::app ()->getStore ()->getBaseCurrencyCode ();
     $currentCurrencyCode = Mage::app ()->getStore ()->getCurrentCurrencyCode ();
     $this->getSubTotal ( $baseCurrencyCode, $currentCurrencyCode, $item );
     /**
      * Check the current currency Code
      */
     $this->checkCurrencyCode ( $baseCurrencyCode, $currentCurrencyCode, $item );
     /**
      * setting the Virutal Amount Value
      */
     $virtualAmount += $item->getRowTotal ();
     /**
      * setting the base Virutal Amount Value
      */
     $baseVirtualAmount += $item->getBaseRowTotal ();
    }
   } else {
    $this->_removeItem ( $address, $item );
   }
  }
  /**
   * Set the subtotal
   */
  $address->setSubtotal ( Mage::getSingleton ( 'core/session' )->getSubtotal () + $address->getFeeAmount () );
  /**
   * Set the base sub total
   */
  $address->setBaseSubtotal ( Mage::getSingleton ( 'core/session' )->getSubtotal () + $address->getFeeAmount () );
  /**
   * Initialize grand totals
   */
  Mage::helper ( 'sales' )->checkQuoteAmount ( $address->getQuote (), $address->getSubtotal () );
  Mage::helper ( 'sales' )->checkQuoteAmount ( $address->getQuote (), $address->getBaseSubtotal () );
  return $this;
 }
 
 /**
  * Get the Sub total Value
  *
  * @param string $baseCurrencyCode         
  * @param string $currentCurrencyCode         
  * @param object $item         
  */
 public function getSubTotal($baseCurrencyCode, $currentCurrencyCode, $item) {
  /**
   * check the baseCurrencyCode and currentCurrencyCode are not same
   */
  if ($baseCurrencyCode !== $currentCurrencyCode) {
   /**
    * getting the base currency value
    */
   $currentCurrencyPrice = Mage::helper ( 'directory' )->currencyConvert ( Mage::getSingleton ( 'core/session' )->getAnyBaseSubtotal (), $baseCurrencyCode, $currentCurrencyCode );
   $baseCurrencyPrice = Mage::getSingleton ( 'core/session' )->getAnyBaseSubtotal ();
   $item->setRowTotal ( $currentCurrencyPrice );
   /**
    * Set the base currency price value to Baserow total
    */
   $item->setBaseRowTotal ( $baseCurrencyPrice );
  }
 }
 /**
  * Check the Currency Code
  *
  * @param string $baseCurrencyCode         
  * @param string $currentCurrencyCode         
  * @param object $item         
  */
 public function checkCurrencyCode($baseCurrencyCode, $currentCurrencyCode, $item) {
  /**
   * check the baseCurrencyCode and currentCurrencyCode are same
   */
  if ($baseCurrencyCode === $currentCurrencyCode) {
   /**
    * getting the base currency value
    */
   $baseCurrencyPrice = Mage::helper ( 'directory' )->currencyConvert ( Mage::getSingleton ( 'core/session' )->getAnyBaseSubtotal (), $baseCurrencyCode, $currentCurrencyCode );
   /**
    * Set the basetotal price value to RowTotal.
    */
   $item->setRowTotal ( Mage::getSingleton ( 'core/session' )->getAnyBaseSubtotal () );
   /**
    * Set the base currency price value to Baserow total
    */
   $item->setBaseRowTotal ( $baseCurrencyPrice );
  }
 }
}