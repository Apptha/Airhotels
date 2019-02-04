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
 * Class : Apptha_Airhotels_CurrencyController
 * extends from Mage_Directory_CurrencyController
 * 
 * @author user
 *        
 */
require_once Mage::getModuleDir ( 'controllers', "Mage_Directory" ) . DS . "CurrencyController.php";
class Apptha_Airhotels_CurrencyController extends Mage_Directory_CurrencyController {
    public function switchAction() {
        /**
         * Get currency
         */
        if ($curency = ( string ) $this->getRequest ()->getParam ( 'currency' )) {
            
            /**
             * Set currency code.
             */
            Mage::app ()->getStore ()->setCurrentCurrencyCode ( $curency );
            
            /**
             * Get cache type.
             *
             * @var $cacheTypes
             */
            $cacheTypes = Mage::app ()->useCache ();
            foreach ( $cacheTypes as $type => $option ) {
                Mage::app ()->getCacheInstance ()->cleanType ( $type );
            }
            /**
             * Clean and flush cache.
             */
            Mage::app ()->cleanCache ();
            Mage::app ()->getCacheInstance ()->flush ();
        }
        /**
         * set redirect url.
         */
        $this->_redirectReferer ( Mage::getBaseUrl () );
    }
}
