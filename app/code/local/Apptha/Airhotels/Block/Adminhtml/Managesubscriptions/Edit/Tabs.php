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
 * Managesubscriptions_Edit Tabs block
 */
class Apptha_Airhotels_Block_Adminhtml_Managesubscriptions_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs {
 
 /**
  * Creating the MethodFor Construct
  */
 public function __construct() {
 /**
  * Calling the parent Construct Method.
  */ 
  parent::__construct ();
  $this->setId ( 'managesubscriptions_tabs' );
  
  /**
   * Set the Element ID
   */
  $this->setDestElementId ( 'edit_form' );
  /**
   * Set the Title
   */
  $this->setTitle ( Mage::helper ( 'airhotels' )->__ ( 'Subscriptions Information' ) );
 }
 
 /**
  * Function to display the header labels
  *
  * @see Mage_Adminhtml_Block_Widget_Tabs::_beforeToHtml()
  */
 protected function _beforeToHtml() {
  
  /**
   * assigning a general tab
   */
  $this->addTab( 'general_section', array ('label' => Mage::helper ( 'airhotels' )->__ ( 'General Tab' ),'title' => Mage::helper ( 'airhotels' )->__ ( 'General' ),'content' => $this->getLayout ()->createBlock ( 'airhotels/adminhtml_managesubscriptions_edit_tab_general' )->toHtml ()) );
  
  /**
   * assigning a subscription type tab
   */
  $this->addTab('subscriptiontype_section', array ('label' => Mage::helper ( 'airhotels' )->__ ( 'Product Subscription Types' ),'title' => Mage::helper ( 'airhotels' )->__ ( 'Subscription Types' ),'content' => $this->getLayout ()->createBlock ( 'airhotels/adminhtml_managesubscriptions_edit_tab_edit' )->toHtml ()) );
  
  return parent::_beforeToHtml ();
 }
}