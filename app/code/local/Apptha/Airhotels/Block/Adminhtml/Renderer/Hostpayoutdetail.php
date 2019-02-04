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
 * Renderer to Host payout details
 * 
 * @see Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract::render()
 */
class Apptha_Airhotels_Block_Adminhtml_Renderer_Hostpayoutdetail extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {
 /**
  *
  * @see Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract::render()
  */
 public function render(Varien_Object $row) {
  if (! $row->getBankDetails ()) {
  /**
   * Return empty when bank details is null.
   */
   return;
  } else {
  /**
   * Unserialize the bank details.
   */
   $rows = unserialize ( $row->getBankDetails () );
   $rowKeys = array ();
   $rowKeys = array_keys ( $rows );
   /**
    * Get count from $rowKeys
    */
   $count = count ( $rowKeys );
   for($i = 0; $i <= $count - 1; $i ++) {
    $fieldKey = $rowKeys [$i];
    /**
     * Check key as 'country_code'
     */
    if ($fieldKey == "country_code") {
     echo "<span style='font-weight:bold;'>".$this->__('Country')." :</span> " . $rows [$fieldKey] . "</br>";
    }
    /**
     * Check key as 'currency_id'
     */
    if ($fieldKey == "currency_id") {
     echo "<span style='font-weight:bold;'>".$this->__('Currency')." :</span> ";
     /**
      * Load currency collection by values
      * @var unknown
      */
     $currency = Mage::getModel ( 'airhotels/allcurrency' )->loadByValue ( $rows [$fieldKey] );
     echo $currency . "</br>";
    }
    /**
     * Check key as 'country_id'
     */
    if ($fieldKey != "submit" && $fieldKey != "country_id") {
     echo "<span style='font-weight:bold;'>$fieldKey :</span> ";
     echo $rows [$fieldKey] . "</br>";
    }
   }
   return;
  }
 }
}