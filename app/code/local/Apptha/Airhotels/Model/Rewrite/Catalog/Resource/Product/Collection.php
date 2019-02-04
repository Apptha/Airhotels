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
 * Custom product collection to resolve flat issue
 */
class Apptha_Airhotels_Model_Rewrite_Catalog_Resource_Product_Collection extends Mage_Catalog_Model_Resource_Product_Collection {
 protected $_disableFlat = false;
 
 /**
  * Enable flat
  */
 public function isEnabledFlat() {
  /**
   * checks the disableflat is active
   */
  if ($this->_disableFlat) {
   return false;
  }
  return parent::isEnabledFlat ();
 }
 
 /**
  * Set disable flat
  */
 public function setDisableFlat($value) {
  $this->_disableFlat = ( boolean ) $value;
  /**
   * check the type value
   */
  $type = $value ? 'catalog/product' : 'catalog/product_flat';
  $this->setEntity ( Mage::getResourceSingleton ( $type ) );
  return $this;
 }
}