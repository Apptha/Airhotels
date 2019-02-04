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
class Apptha_Airhotels_Block_Adminhtml_Sales_Items_Column_Name extends Mage_Adminhtml_Block_Sales_Items_Column_Name {
 /**
  * Function Name: perHourNightFees
  * Get the Per hour night fee
  *
  * @param array $det         
  * @return array
  */
 public function perHourNightFees($det) {
  $perHourNightFee = "";
  /**
   * Check weather the "per_hour_night_fee" is set and
   * if it's value is setted assign to "$perHourNightFee" Variable
   */
  if (isset ( $det ['per_hour_night_fee'] )) {
   /**
    * Declaring the value to "$perHourNightFee" variable
    */
   $perHourNightFee = $det ['per_hour_night_fee'];
  }
  /**
   * Returning the "$perHourNightFee" Value
   */
  return $perHourNightFee;
 }
}