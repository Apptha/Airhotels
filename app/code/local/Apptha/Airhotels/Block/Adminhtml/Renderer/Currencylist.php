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
 * Renderer to display the document status
 * 
 * @see Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract::render()
 */
class Apptha_Airhotels_Block_Adminhtml_Renderer_Currencylist extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {
 /**
  * Function to display document status in grid
  * (non-PHPdoc)
  *
  * @see Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract::render()
  */
 public function render(Varien_Object $row) {
  if (! $row ['currency_code']) {
  /**
   * Set message.
   */
   return "No Country Available";
  } else {
   $rows = explode ( ",", $row ['currency_code'] );
   $count = count ( $rows );
   /**
    * Set increment value as zero
    * @var unknown
    */
   $increment = 0;
   $currency = "";
   foreach ( $rows as $code ) {
    $increment ++;
    /**
     * Get all currency collection.
     * 
     * Load by country code
     */
    $currency .= Mage::getModel ( 'airhotels/allcurrency' )->loadByValue ( $code );
    /**
     * Check $count is not equal to $increment
     */
    if ($count != $increment) {
     $currency .= ",";
    }
   }
   return $currency;
  }
 }
}