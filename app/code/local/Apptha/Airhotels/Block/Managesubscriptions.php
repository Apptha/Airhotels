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
 * Managesubscription Type block
 */
class Apptha_Airhotels_Block_Managesubscriptions extends Mage_Core_Block_Template {
 /**
  * The _prepareLayout() method is called immediately after a block has been added to
  * the layout object for the first time.
  *
  * If this seems vague don't worry, we'll get to some specifics in a moment.
  * Create the method for PrepareLayout
  *
  * @see Mage_Core_Block_Abstract::_prepareLayout()
  */
 
 /**
  * Get a registry data of 'Managesubscriptions'
  *
  * @return string
  */
 public function getManagesubscriptions() {
  if (! $this->hasData ( 'managesubscriptions' )) {
  /**
   * set data from manage subscription.
   */
   $this->setData ( 'managesubscriptions', Mage::registry ( 'managesubscriptions' ) );
  }
  /**
   * Get data from manage subscription.
   */
  return $this->getData ( 'managesubscriptions' );
 }
}