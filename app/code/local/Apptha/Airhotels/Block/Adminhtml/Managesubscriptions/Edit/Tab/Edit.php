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
 * Managesubscriptions_Edit_Tab Subscriptiontype block
 */
class Apptha_Airhotels_Block_Adminhtml_Managesubscriptions_Edit_Tab_Edit extends Mage_Adminhtml_Block_Widget_Form {
    
    /**
     * Set template
     */
    public function __construct() {
        /**
         * Calling the parent Construct Method.
         */
        parent::__construct ();
        /**
         * set the templete file.
         */
        $this->setTemplate ( 'airhotels/managesubscriptions/edit.phtml' )->toHtml ();
    }
    /**
     * Get Collection values
     *
     * @method
     *
     */
    public function getSubscripitonCollection() {
        $subscriptionTypes = array ();
        /**
         * Get the Collection for subscription type
         */
        $subscriptionCollection = Mage::getModel ( 'airhotels/subscriptiontype' )->getCollection ();
        /**
         * get the SubscriptionCollection Data
         */
        $subscriptionTypes = $subscriptionCollection->getData ();
        /**
         * Get the subscription id.
         */
        foreach ( $subscriptionTypes as $subscriptions ) {
            $subscriptionId [] = $subscriptions ['id'];
        }
        /**
         * Returning the Subscription Id
         */
        return $subscriptionId;
    }
}

