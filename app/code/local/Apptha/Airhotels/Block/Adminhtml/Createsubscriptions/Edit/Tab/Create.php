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
 * Createsubscriptions_Edit Subscription Form Block
 * 
 * @abstract Mage_Adminhtml_Block_Widget_Form
 */
class Apptha_Airhotels_Block_Adminhtml_Createsubscriptions_Edit_Tab_Create extends Mage_Adminhtml_Block_Widget_Form { 
 /**
  * Set template
  * 
  * @abstract Mage_Adminhtml_Block_Widget_Form
  */
 public function __construct() {
 /**
  * Calling the parent Construct Method.
  */
  parent::__construct ();
  /**
   * Initializing create subscription phtml file.
   */
  $this->setTemplate ( 'airhotels/createsubscriptions/create.phtml' );
 }
}