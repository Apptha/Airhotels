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
 * @package     Apptha_Sociallogin
 * @version     0.1.8
 * @author      Apptha Team <developers@contus.in>
 * @copyright   Copyright (c) 2014 Apptha. (http://www.apptha.com)
 * @license     http://www.apptha.com/LICENSE.txt
 *
 * */

/**
 * This Block for helps to added the social login js file
 */
class Apptha_Sociallogin_Block_Sociallogin extends Mage_Core_Block_Template {
    
    /**
     * preparing the social login pop-up layout
     *
     * @return void
     */
    public function _prepareLayout() {
        /**
         * checking whether social login enabled or not
         */
        /*if (Mage::getStoreConfig ( 'sociallogin/general/enable_sociallogin' ) == 1 && ! Mage::helper ( 'customer' )->isLoggedIn ()) {
            $this->getLayout ()->getBlock ( 'head' )->addJs ( 'sociallogin/sociallogin.js' );
        } */
        return parent::_prepareLayout ();
    }
}