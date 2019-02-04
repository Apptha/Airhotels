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
class Apptha_Airhotels_Block_Checkout_Cart_Item_Renderer extends Mage_Checkout_Block_Cart_Item_Renderer {
 /**
  * Show in check in,check out and accomodates in cart table
  * update its options without deleting product from cart and adding it again.
  *
  * @return array
  */
 public function getOptionList() {
  /**
   * check sytlish theme
   */
  $enable_airhotels = Mage::getStoreConfig ( 'airhotels/custom_group/enable_airhotels' );
  /**
   * Make sure Airhotels has been enabled.
   */
  if (empty ( $enable_airhotels )) {
   /**
    * Getting from date
    */
   $fromdate = Mage::getSingleton ( 'core/session' )->getFromdate ();
   /**
    * Getting To date
    */
   $todate = Mage::getSingleton ( 'core/session' )->getTodate ();
   /**
    * Getting Number Of accomodates
    */
   $accomodate = ( int ) Mage::getSingleton ( 'core/session' )->getAccomodate ();
   /**
    * Gte the Hourly product Id
    */
   $productId = Mage::getSingleton ( 'core/session' )->getHourlyProductId ();
   /**
    * For hourly wise property
    */
   $propertyTime = Mage::getModel ( 'catalog/product' )->load ( $productId )->getPropertyTime ();
   $propertyTimeData = Mage::helper ( 'airhotels/airhotel' )->getPropertyTimeLabelByOptionId ();
   /**
    * make sure the Hourly Value Enabled
    */
   $hourlyEnabledOrNot = Mage::helper ( 'airhotels/product' )->getHourlyEnabledOrNot ();
   if ($propertyTime == $propertyTimeData && $hourlyEnabledOrNot == 0) {
    /**
     * Get hourly from and to time
     */
    $hourlyFromTime = Mage::getSingleton ( 'core/session' )->getHourlyFromTime ();
    $hourlyFromPeriod = Mage::getSingleton ( 'core/session' )->getHourlyFormPeriod ();
    $hourlyToTime = Mage::getSingleton ( 'core/session' )->getHourlyToTime ();
    $hourlyToPeriod = Mage::getSingleton ( 'core/session' )->getHourlyToPeriod ();
    $propertyFromTime = $hourlyFromTime . ' ' . $hourlyFromPeriod;
    $propertyToTime = $hourlyToTime . ' ' . $hourlyToPeriod;
   }
   /**
    * Check the accomodate value is grater than zero
    */
   if ($accomodate > 0) {
    /**
     * Make Sure the hourly Enabled
     */
    $hourlyEnabledOrNot = Mage::helper ( 'airhotels/product' )->getHourlyEnabledOrNot ();
    if ($propertyTime == $propertyTimeData && $hourlyEnabledOrNot == 0) {
     
    /**
     * Retun values.
     */
     return array (
       array ('label' => $this->__ ( '' ),
         'value' => $accomodate 
       ),
       array ('label' => $this->__ ( '' ),
         'value' => date ( "m/d/Y", strtotime ( $todate ) ) . '<br/>' . $propertyToTime 
       ),
       array ('label' => $this->__ ( '' ),
         'value' => date ( "m/d/Y", strtotime ( $fromdate ) ) . '<br/>' . $propertyFromTime 
       ) 
     );
    } else {
    /**
     * Return function values.
     */
     return array (
       array ('label' => $this->__ ( '' ),
         'value' => $accomodate 
       ),
       array ('label' => $this->__ ( '' ),
         'value' => date ( "m/d/Y", strtotime ( $todate ) ) 
       ),
       array ('label' => $this->__ ( '' ),
         'value' => date ( "m/d/Y", strtotime ( $fromdate ) ) 
       ) 
     );
    }
   } else {
    $this->accomodateLesser ( $propertyTime, $propertyTimeData, $propertyToTime, $propertyFromTime, $todate, $fromdate );
   }
  }
 }
 
 /**
  *
  * @param date $propertyTime         
  * @param time $propertyTimeData         
  * @param time $propertyToTime         
  * @param time $propertyFromTime         
  * @param date $todate         
  * @param date $fromdate         
  * @return multitype:multitype:string NULL |multitype:multitype:NULL
  */
 public function accomodateLesser($propertyTime, $propertyTimeData, $propertyToTime, $propertyFromTime, $todate, $fromdate) {
  /**
   * Checking whether hourly enabled or not
   */
  $hourlyEnabledOrNot = Mage::helper ( 'airhotels/product' )->getHourlyEnabledOrNot ();
  if ($propertyTime == $propertyTimeData && $hourlyEnabledOrNot == 0) {
  /**
   * Retun an array.
   */
   return array (
     array ( 'label' => $this->__ ( '' ),
       'value' => date ( "m/d/Y", strtotime ( $todate ) ) . '<br/>' . $propertyToTime 
     ),
     array ('label' => $this->__ ( '' ),
       'value' => date ( "m/d/Y", strtotime ( $fromdate ) ) . '<br/>' . $propertyFromTime 
     ) 
   );
  } else {
  /**
   * Return an array.
   */
   return array (
     array ('label' => $this->__ ( '' ),
       'value' => date ( "m/d/Y", strtotime ( $todate ) ) 
     ),
     array ('label' => $this->__ ( '' ),
       'value' => date ( "m/d/Y", strtotime ( $fromdate ) ) 
     ) 
   );
  }
 }
}