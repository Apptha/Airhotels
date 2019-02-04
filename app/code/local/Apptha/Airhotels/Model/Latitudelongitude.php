<?php

/**
 * Contus
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
 * Contus does not guarantee correct work of this extension
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
 **/
class Apptha_Airhotels_Model_Latitudelongitude extends Mage_Core_Model_Abstract {
    /**
     * Constructor class
     * (non-PHPdoc)
     *
     * @see Varien_Object::_construct()
     */
    public function _construct() {
        /**
         * Calling the parent Construct Method.
         */
        parent::_construct ();
        /**
         * Initializing latitudelongitude Block.
         */
        $this->_init ( 'airhotels/latitudelongitude' );
    }
}