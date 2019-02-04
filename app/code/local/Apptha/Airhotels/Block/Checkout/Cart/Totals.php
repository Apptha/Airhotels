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
class Mage_Checkout_Block_Cart_Totals extends Mage_Checkout_Block_Cart_Abstract {
 protected $_totalRenderers;
 protected $_defaultRenderer = 'checkout/total_default';
 protected $_totals = null;
 /**
  * Get Totals
  * 
  * @see Mage_Checkout_Block_Cart_Abstract::getTotals()
  */
 public function getTotals() {
  if (is_null ( $this->_totals )) {
  /**
   * Calling the parent Construct Method.
   */
   return parent::getTotals ();
  }
  /**
   * Return Total.
   */
  return $this->_totals;
 }
 /**
  * set Totals
  * 
  * @param string $value         
  * @return Mage_Checkout_Block_Cart_Totals
  */
 public function setTotals($value) {
  $this->_totals = $value;
  return $this;
 }
 /**
  * Get all available Renderers
  * 
  * @param string $code         
  * @return multitype
  */
 protected function _getTotalRenderer($code) {
  $blockName = $code . '_total_renderer';
  /**
   * Get Block
   */
  $block = $this->getLayout ()->getBlock ( $blockName );
  if (! $block) {
   $block = $this->_defaultRenderer;
   /**
    * Get configuration.
    */
   $config = Mage::getConfig ()->getNode ( "global/sales/quote/totals/{$code}/renderer" );
   if ($config) {
    $block = ( string ) $config;
   }
   
   $block = $this->getLayout ()->createBlock ( $block, $blockName );
  }
  /**
   * Transfer totals to renderer
   */
  $block->setTotals ( $this->getTotals () );
  return $block;
 }
 /**
  * Calculate the Render Total
  * 
  * @param object $total         
  * @param string $area         
  * @param number $colspan         
  */
 public function renderTotal($total, $area = null, $colspan = 1) {
  $code = $total->getCode ();
  if ($total->getAs ()) {
   $code = $total->getAs ();
  }
  return $this->_getTotalRenderer ( $code )->setTotal ( $total )->setColspan ( $colspan )->setRenderingArea ( is_null ( $area ) ? - 1 : $area )->toHtml ();
 }
 
 /**
  * Render totals html for specific totals area (footer, body)
  *
  * @param null|string $area         
  * @param int $colspan         
  * @return string
  */
 public function renderTotals($area = null, $colspan = 1) {
  $html = '';
  /**
   * Iterating the loop
   */
  foreach ( $this->getTotals () as $total ) {
   if ($total->getArea () != $area && $area != - 1) {
    continue;
   }
   /**
    * Render html layout.
    */
   $html .= $this->renderTotal ( $total, $area, $colspan );
  }
  return $html;
 }
 
 /**
  * Check if we have display grand total in base currency
  *
  * @return bool
  */
 public function needDisplayBaseGrandtotal() {
  $quote = $this->getQuote ();
  /**
   * Getting currency code
   */
  if ($quote->getBaseCurrencyCode () != $quote->getQuoteCurrencyCode ()) {
   return true;
  }
  return false;
 }
 
 /**
  * Get formated in base currency base grand total value
  *
  * @return string
  */
 public function displayBaseGrandtotal() {
  $firstTotal = reset ( $this->_totals );
  if ($firstTotal) {
   /**
    * Getting Grand Total
    */
   $total = $firstTotal->getAddress ()->getBaseGrandTotal ();
   return Mage::app ()->getStore ()->getBaseCurrency ()->format ( $total, array (), true );
  }
  return '-';
 }
 
 /**
  * Get active or custom quote
  *
  * @return Mage_Sales_Model_Quote
  */
 public function getQuote() {
  /**
   * Get custom Quote
   */
  if ($this->getCustomQuote ()) {
   return $this->getCustomQuote ();
  }
  
  if (null === $this->_quote) {
   $this->_quote = $this->getCheckout ()->getQuote ();
  }
  return $this->_quote;
 }
}
