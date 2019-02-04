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
 * @version     0.1.0
 * @author      Apptha Team <developers@contus.in>
 * @copyright   Copyright (c) 2014 Apptha. (http://www.apptha.com)
 * @license     http://www.apptha.com/LICENSE.txt
 *
 */

/**
 * Recurringpayments_Model Periodunit Block
 */
class Apptha_Airhotels_Model_PeriodUnit extends Varien_Object {
 /**
  * Declaring Constant Values
  * @const PERIOD_DAY
  * @const PERIOD_WEEK
  * @const PERIOD_MONTH
  * @const PERIOD_UNIT_YEAR
  */
 const PERIOD_DAY = 1;
 const PERIOD_WEEK = 2;
 const PERIOD_MONTH = 4;
 const PERIOD_UNIT_YEAR = 5;
 /**
  *  Get option array function.
  */
 static public function getOptionArray() {
  return array (
    
    static::PERIOD_DAY => Mage::helper ( 'airhotels' )->__ ( 'Day' ),
    static::PERIOD_WEEK => Mage::helper ( 'airhotels' )->__ ( 'Week' ),
    static::PERIOD_MONTH => Mage::helper ( 'airhotels' )->__ ( 'Month' ),
    static::PERIOD_UNIT_YEAR => Mage::helper ( 'airhotels' )->__ ( 'Year' ) 
  );
 }
}