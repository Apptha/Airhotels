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
 * Managesubscriptions block for managing the Admin added subscription types
 * with frequency Period,Frequency Unit,Frequency mode along with property details.
 */
class Apptha_Airhotels_Block_Adminhtml_Managesubscriptions extends Mage_Adminhtml_Block_Widget_Grid_Container {
 
 /**
  * Construct the inital display of grid information
  * Setting the Block files group for this grid
  * Setting the Header text to display
  * Setting the Controller file for this grid
  *
  * Return managesubscriptions as array
  *
  * @return array
  */
 public function __construct() {
  /**
   * Get the Controller Name
   */
  $this->_controller = 'adminhtml_managesubscriptions';
  $this->_blockGroup = 'airhotels';
  /**
   * Getting the Helper text for airhotel
   */
  $this->_headerText = Mage::helper ( 'airhotels' )->__ ( 'Subscriptions' );
  $this->_addButtonLabel = Mage::helper ( 'airhotels' )->__ ( 'Add Item' );
  /**
   * Returning the Parent Controller from the Varien Class
   */
  parent::__construct ();
 }
}