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
class Apptha_Airhotels_Model_Videostatus extends Varien_Object {
 const STATUS_ENABLED = 1;
 const STATUS_DISABLED = 2;
 /**
  * Getting enable disable option array
  * 
  * @return multitype:string Ambigous <string, string, multitype:>
  */
 static public function getOptionArray() {
  return array (
    static::STATUS_ENABLED => Mage::helper ( 'airhotels' )->__ ( 'Enabled' ),
    static::STATUS_DISABLED => Mage::helper ( 'airhotels' )->__ ( 'Disabled' ) 
  );
 }
}