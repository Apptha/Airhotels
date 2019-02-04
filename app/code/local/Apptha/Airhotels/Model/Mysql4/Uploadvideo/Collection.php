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
 * Apptha_Airhotels_Model_Mysql4_Uploadvideo Collection Block
 *
 * @category Apptha
 * @package Apptha_Recurringpayments
 * @author Apptha Team <developers@contus.in>
 */
class Apptha_Airhotels_Model_Mysql4_Uploadvideo_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract {
    /**
     * Constructor class
     *
     * @see Mage_Core_Model_Resource_Db_Collection_Abstract::_construct()
     */
    public function _construct() {
        /**
         * Calling the parent Construct Method.
         */
        parent::_construct ();
        /**
         * Initializing upload video block.
         */
        $this->_init ( 'airhotels/uploadvideo' );
    }
}