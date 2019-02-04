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
class Apptha_Airhotels_Model_Sales_Quote_Address_Total_Fee extends Mage_Sales_Model_Quote_Address_Total_Abstract {
 
 /**
  * Collect fee grandtotal
  *
  * @param Mage_Sales_Model_Quote_Address $address         
  * @return Apptha_Airhotels_Model_Sales_Quote_Address_Total_Fee
  */
 public function collect(Mage_Sales_Model_Quote_Address $address) {
  /**
   * Call the PArent Value.
   */
  parent::collect ( $address );
  /**
   * SetAmount Value.
   */
  $this->_setAmount ( 0 );
  /**
   * Set baseAmount Vlaue.
   */
  $this->_setBaseAmount ( 0 );
  
  $items = $this->_getAddressItems ( $address );
  if (! count ( $items )) {
   return $this;
  }
  /**
   * Get security fee enable or not.
   */
  $getSecurityEnabledOrNot = Mage::helper ( 'airhotels/product' )->getSecurityEnabledOrNot ();
  /**
   * Get subcyle value
   * Get productId
   */
  $subCycle = Mage::getSingleton ( 'core/session' )->getSubCycle();
  $productId = Mage::getSingleton ( 'core/session' )->getHourlyProductId ();
  $product = Mage::getModel ( 'catalog/product' )->load ( $productId );
  /**
   * Initialize security fee as zero.
   */
  $securityFee = 0;
  if ($getSecurityEnabledOrNot == 0 && $subCycle == 'undefined') {
   $allOptions = $product->getOptions ();
   /**
    * Calculate security fee.
    */
   if ($allOptions) {
    foreach ( $allOptions as $option ) {
     foreach ( $option->getValues () as $value ) {
      $securityFee += $value->getPrice ();
     }
    }
   }
  }
  /**
   * Quote Vlaue.
   */
  $quote = $address->getQuote ();
  /**
   * Exist Amout Value.
   */
  $existAmount = $quote->getFeeAmount ();
  /**
   * Get the service fee amount
   */
  $processingFee = Apptha_Airhotels_Model_Fee::getFee ();
  Mage::getSingleton ( 'core/session' )->setAnySessionFinalFeeAmount ( $processingFee );
  /**
   * Get base currency code.
   * Get currnet currency code.
   */
  $baseCurrencyCode = Mage::app ()->getStore ()->getBaseCurrencyCode ();
  $currentCurrencyCode = Mage::app ()->getStore ()->getCurrentCurrencyCode ();
  /**
   * Check the 'base curreency Code' and 'currentCurrencyCode' are not same.
   */
 if ($baseCurrencyCode !== $currentCurrencyCode) {
   /**
    * Get the Fee Value.
    */
   $fee = round ( Mage::helper ( 'directory' )->currencyConvert ( $processingFee, Mage::app ()->getStore ()->getBaseCurrencyCode (), Mage::app ()->getStore ()->getCurrentCurrencyCode () ), 2 );
   $existAmountIni = round ( Mage::helper ( 'directory' )->currencyConvert ( $existAmount, Mage::app ()->getStore ()->getBaseCurrencyCode (), Mage::app ()->getStore ()->getCurrentCurrencyCode () ), 2 );
   /**
    * Set the Balance Vlaue.
    */
   $balance = $fee - $existAmountIni;
   /**
    * In cart page processing fees
    */
   $address->setFeeAmount ( $balance );
   $address->setBaseFeeAmount ( $processingFee );
   $quote->setFeeAmount ( $balance );
   /**
    * Add the service fee to the grandtotal
    */
   $address->setGrandTotal ( $address->getGrandTotal () + $address->getFeeAmount () + Mage::helper ( 'directory' )->currencyConvert ( $securityFee, Mage::app ()->getStore ()->getBaseCurrencyCode (), Mage::app ()->getStore ()->getCurrentCurrencyCode () ) );
   /**
    * Address Value for 'setBaseGrandTotal'
    */
   $address->setBaseGrandTotal ( $address->getBaseGrandTotal () + $address->getBaseFeeAmount () + $securityFee );
  }
  /**
   * Check the value of '$baseCurrencyCode' and $currentCurrencyCode are same.
   */
  if ($baseCurrencyCode === $currentCurrencyCode) {
   /**
    * Fee for process.
    */
   $fee = $processingFee;
   $balance = $fee - $existAmount;
   /**
    * Set the balance fee
    */
   $address->setFeeAmount ( $balance );
   $address->setBaseFeeAmount ( $balance );
   $quote->setFeeAmount ( $balance );
   /**
    * Add the service fee to the grandtotal
    */
   $address->setGrandTotal ( $address->getGrandTotal () + $address->getFeeAmount () + $securityFee );
   $address->setBaseGrandTotal ( $address->getBaseGrandTotal () + $address->getBaseFeeAmount () + $securityFee );
  }
 }
 /**
  * Function Name: fetch
  * Retrive the base fee amount
  *
  * @param Mage_Sales_Model_Quote_Address $address         
  * @return Apptha_Airhotels_Model_Sales_Quote_Address_Total_Fee
  */
 public function fetch(Mage_Sales_Model_Quote_Address $address) {
  /**
   * To retrive the property service fees from table Sales_Flat_Quote_Address
   *
   * @return int value
   */
  $amt = $address->getFeeAmount ();
  /**
   * Hourly Exclude Fee.
   */
  $hourlyExcludedFeeMsg = '';
  /**
   * For hourly wise property
   */
  $productId = Mage::getSingleton ( 'core/session' )->getHourlyProductId ();
  /**
   * Property Time Value.
   */
  $propertyTime = Mage::getModel ( 'catalog/product' )->load ( $productId )->getPropertyTime ();
  /**
   * Property Time Dat Value.
   */
  $propertyTimeData = Mage::helper ( 'airhotels/airhotel' )->getPropertyTimeLabelByOptionId ();
  /**
   * HOurly Enabled or Not Value.
   */
  $hourlyEnabledOrNot = Mage::helper ( 'airhotels/product' )->getHourlyEnabledOrNot ();
  /**
   * Check weatehr the property Time Value and hourlyEnabled or set.
   */
  if ($propertyTime == $propertyTimeData && $hourlyEnabledOrNot == 0) {
   $hourlyNightFee = Mage::getSingleton ( 'core/session' )->getHourlyNightFee ();
   if (! (empty ( $hourlyNightFee )) && $hourlyNightFee >= 1) {
    $hourlyExcludedFeeMsg = "<div class='hourly_excluded_fee' style='font-size: 10px;font-weight: 200;text-align:right;'>(" . Mage::helper ( 'airhotels' )->__ ( 'Included Overnight Fee' ) . ")</div>";
   }
  } else {
   $hourlyExcludedFeeMsg = '';
  }
  $airhotelsEnabled = ( int ) Mage::helper ( 'airhotels/productconfiguration' )->getModuleEnabledOrNot ();
  /**
   * Check the airhotels Enabled Value.
   */
  if ($airhotelsEnabled == 0) {
   /**
    * Applied only daily based special price
    * To resolve overnight text issue in cart page
    */
   if (Mage::getSingleton ( 'core/session' )->getRemoveIncludedOvernightFeeInCart () == 1) {
    $hourlyExcludedFeeMsg = '';
   }
   /**
    * Get security fee enable/disable configuration.
    */
   $getSecurityEnabledOrNot = Mage::helper ( 'airhotels/product' )->getSecurityEnabledOrNot ();   
   $subCycle = Mage::getSingleton ( 'core/session' )->getSubCycle();
   $product = Mage::getModel ( 'catalog/product' )->load ( $productId );
   /**
    * Check security fee enable or not.
    */
   if ($getSecurityEnabledOrNot == 0 && $subCycle == 'undefined') {
    $allOptions = $product->getOptions ();
    $this->addAddressTotal($allOptions,$address);
    }
   /**
    * Set the address Value to
    * 'code',
    * 'title',
    * 'value'
    */
   $address->addTotal ( array (
     'code' => $this->getCode (),
     'title' => $hourlyExcludedFeeMsg . "<span>" . Mage::helper ( 'airhotels' )->__ ( 'Processing Fee' ) . "</span>",
     'value' => $amt 
   ) );
  }
  return $this;
 }
 /**
  * Function Name: updatePaypalTotal
  * To update paypal total
  *
  * @param array $evt         
  *
  */
 public function updatePaypalTotal($evt) {
  /**
   * Return te Paypal Amount
   */
  $paypalFeeAmount = Mage::getSingleton ( 'core/session' )->getAnySessionFinalFeeAmount ();
  /**
   * cart Value.
   */
  $cart = $evt->getPaypalCart ();
  /**
   * Add Itemto Cart.
   */
  $cart->addItem ( Mage::helper ( 'airhotels' )->__ ( 'Processing Fee' ), 1, round ( $paypalFeeAmount, 2 ) );
  /**
   * Get security fee enable or not.
   */
  $getSecurityEnabledOrNot = Mage::helper ( 'airhotels/product' )->getSecurityEnabledOrNot ();
  /**
   * Get subcyle value
   * Get productId
   */
  $subCyclePayment = Mage::getSingleton ( 'core/session' )->getSubCycle();
  /**
   * Get hourly product id
   * @var unknown
   */
  $productIdPayment = Mage::getSingleton ( 'core/session' )->getHourlyProductId ();
  /**
   * Get Product information
   * @var unknown
   */
  $product = Mage::getModel ( 'catalog/product' )->load ( $productIdPayment );
  /**
   * Initialize security fee as zero.
   */
  $securityFee = 0;
  if ($getSecurityEnabledOrNot == 0 && $subCyclePayment == 'undefined') {
      
      $allOptions = $product->getOptions ();
      /**
       * Calculate security fee for magento default payment. with price values.
       */
      if ($allOptions) {
          foreach ( $allOptions as $option ) {
              foreach ( $option->getValues () as $value ) {
                  $securityFee += $value->getPrice ();
              }
          }
      }
  }
  $cart->addItem ( Mage::helper ( 'airhotels' )->__ ( 'Security Fee' ), 1, round ( $securityFee, 2 ) );
 }
 /**
  * Function to add total to sales address
  * @param unknown $allOptions
  * @param unknown $address
  */
 public function addAddressTotal($allOptions,$address){
     if ($allOptions) {
         foreach ( $allOptions as $option ) {
             foreach ( $option->getValues () as $key => $value ) {
                 /**
                  * Display different fee values.
                  */
                 $address->addTotal ( array (
                         'code' => $key,
                         'title' => "<span>" . Mage::helper ( 'airhotels' )->__ ( $value->getDefaultTitle () . ' Fee' ) . "</span>",
                         'value' => Mage::helper ( 'directory' )->currencyConvert ( $value->getPrice (), Mage::app ()->getStore ()->getBaseCurrencyCode (), Mage::app ()->getStore ()->getCurrentCurrencyCode () )
                 ) );
             }
         }
     }
 }
}