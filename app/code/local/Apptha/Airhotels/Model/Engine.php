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
 * Recurringpayments Model Engine Block
 */
class Apptha_Airhotels_Model_Engine extends Varien_Object {
 /**
  * Declaring the Constant variables
  */
 const PICK_ENGINE_PAYPAL = 0;
 const PICK_ENGINE_AUTHORIZE = 1;
 
 /**
  * get Engine type list
  * 
  * @return array
  */
 static public function getOptionArray() {
  /**
   * Returning the array of payment Gateways
   */
  return array (
    static::PICK_ENGINE_PAYPAL => Mage::helper ( 'airhotels' )->__ ( 'Paypal' ),
    static::PICK_ENGINE_AUTHORIZE => Mage::helper ( 'airhotels' )->__ ( 'Authorize.net' ) 
  );
 }
}