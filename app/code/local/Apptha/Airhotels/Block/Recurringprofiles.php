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
 * Recurring Profiles block
 */
class Apptha_Airhotels_Block_Recurringprofiles extends Mage_Core_Block_Template {
 
 /**
  * Function to Prepare Layout
  */
 public function _prepareLayout() {
  /**
   * Create Block
   */
  $button = $this->getLayout ()->createBlock ( 'adminhtml/widget_button' )->setData ( array (
    'label' => Mage::helper ( 'sales' )->__ ( 'Update Changes' ),
    'onclick' => 'order.sidebarApplyChanges()',
    'before_html' => '<div class="sub-btn-set">',
    'after_html' => '</div>' 
  ) );
  $this->setChild ( 'top_button', $button );
  /**
   * Calling the parent Construct Method.
   */
  return parent::_prepareLayout ();
 }
 
 /**
  * Function to get registry data of recurring profiles
  *
  * @return string
  */
 public function getRecurringprofiles() {
  if (! $this->hasData ( 'recurringprofiles' )) {
   $this->setData ( 'recurringprofiles', Mage::registry ( 'recurringprofiles' ) );
  }
  return $this->getData ( 'recurringprofiles' );
 }
}