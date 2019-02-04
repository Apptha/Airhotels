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
 * Subscription Type block
 */
class Apptha_Airhotels_Block_Subscriptiontype extends Mage_Core_Block_Template {
    
    /**
     * Function to prepare layout
     */
    public function _prepareLayout() {
        if ($this->_blockGroup && $this->_controller && $this->_mode) {
            /**
             * Set child bloack
             */
            $this->setChild ( 'form', $this->getLayout ()->createBlock ( $this->_blockGroup . '/' . $this->_controller . '_' . $this->_mode . '_form' ) );
        }
        return parent::_prepareLayout ();
    }
    /**
     * Function to get registry data of Subscriptiontype
     *
     * @return string
     */
    public function getSubscriptiontype() {
        if (! $this->hasData ( 'subscriptiontype' )) {
            $this->setData ( 'subscriptiontype', Mage::registry ( 'subscriptiontype' ) );
        }
        /**
         * Get subscription type.
         */
        return $this->getData ( 'subscriptiontype' );
    }
}