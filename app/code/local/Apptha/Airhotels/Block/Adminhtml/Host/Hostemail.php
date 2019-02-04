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
 * Extend render block from grid
 * 
 * @author user
 *
 */
class Apptha_Airhotels_Block_Adminhtml_Host_Hostemail extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {
 /**
  * Setting the renderer
  * 
  * @see Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract::render()
  */
 public function render(Varien_Object $row) {
     /**
      * Getting column index
      * @var unknown
      */
  $value = $row->getData ( $this->getColumn ()->getIndex () );
  /**
   * Get Property User Id
   */
  $customerId = Mage::getModel ( 'catalog/product' )->load ( $value )->getUserid ();
  /**
   * Load Customer Details
   */
  $customer = Mage::getModel ( 'customer/customer' )->load ( $customerId );
  /**
   * Filter by customer Id
   */
  $customerDetail = Mage::helper ( "adminhtml" )->getUrl ( 'adminhtml/customer/edit', array (
    'id' => $customerId 
  ) );
  /**
   * Get customer email
   */
  return "<a title='Click to view detail' href='" . $customerDetail . "'>" . $customer->getEmail () . "</a>";
 }
}