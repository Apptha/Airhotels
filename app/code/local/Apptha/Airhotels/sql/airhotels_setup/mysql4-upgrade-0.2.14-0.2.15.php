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
$installer = $this;
/**
 *  @var $installer Mage_Core_Model_Resource_Setup 
 */

/**
 * Load Initial setup
 */
$installer->startSetup();

/**
 * Alter table airhotels_customer_inbox
 * Added two fields
 * mobile number
 *
 */
$installer->run("
ALTER TABLE  {$this->getTable('apptha_managesubscriptions')} CHANGE  `start_date` `start_date` int(11) DEFAULT NULL;
");

/**
 * unset installer setup.
 */
$installer->endSetup();