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
 * Subscriptiontype Model Class
 */
class Apptha_Airhotels_Model_Subscriptiontype extends Mage_Core_Model_Abstract {
    /**
     * Construct Method
     *
     * @see Mage_Core_Model_Resource_Abstract::_construct()
     */
    public function _construct() {
        /**
         * Note that the airhotels_id refers to the key field in your database table.
         * Initializing subscriptiontype Block.
         */
        $this->_init ( 'airhotels/subscriptiontype' );
    }
    
    /**
     * Subscription Method for Property
     */
    public function subscription() {
        /**
         * Get the subscription ID
         */
        $subscriptionid = Mage::app ()->getRequest ()->getParam ( 'subscriptionid' );
        $productId = Mage::app ()->getRequest ()->getParam ( 'productid' );
        /**
         * Adding filters to product Subscription Colletion
         */
        $productSubscriptionCollection = $this->productSubscriptionCollection ()->addFieldToFilter ( 'subscription_type', $subscriptionid )->addFieldToFilter ( 'product_id', $productId );
        
        /**
         * Get subscription type collection
         *
         * @var $subscriptionTypeCollection
         */
        $subscriptionTypeCollection = $this->subscriptionTypeCollection ()->addFieldToFilter ( 'id', $subscriptionid );
        
        foreach ( $subscriptionTypeCollection as $subscriptionType ) {
            
            $subscriptionBillingFrequency = $subscriptionType ['billing_frequency'];
            $subscriptionBillingPeriod = $subscriptionType ['billing_period_unit'];
            $subscriptionBillingCycle = $subscriptionType ['billing_cycle'];
        }
        
        $subscriptionBillingPeriodCalc = $this->getBillingPeriod ( $subscriptionBillingPeriod );
        
        foreach ( $productSubscriptionCollection as $subscription ) {
            /**
             * Get subscription type
             *
             * @var $subscriptionPrice
             */
            $subscriptionPrice = trim ( $subscription ['price_per_iteration'] );
        }        
        /**
         * Get currency symbol.
         *
         * @var $currencySymbol
         */
        $currencySymbol = Mage::app ()->getLocale ()->currency ( Mage::app ()->getStore ()->getCurrentCurrencyCode () )->getSymbol ();        
        $billingFrequency = Mage::helper('airhotels')->__('Billing Frequency');
        $billingPeriod = Mage::helper('airhotels')->__('Billing Period');
        $billingCycle = Mage::helper('airhotels')->__('Billing Cycle');
        $priceIteration = Mage::helper('airhotels')->__('Price (Per Iteration)');
        $totalNights = Mage::helper('airhotels')->__('Total Number of Nights');
        $symbol = "<span><b>".$billingFrequency."</b><b class='bold'>" . $subscriptionBillingFrequency . "</b></span>" . "<span><b>".$billingPeriod."</b><b class='bold'>" . $subscriptionBillingPeriodCalc ['subscriptionBillingPeriod'] . "</b></span>" . "<span><b>".$billingCycle."</b><b class='bold'>" . $subscriptionBillingCycle . "</b></span>";
        $symbol .= "<input type='hidden' id='subscription_price' value='" . $subscriptionPrice . "' >";
        $symbol .= "<input type='hidden' id='subscription_cycle' value='" . $subscriptionBillingCycle . "' >";
        $symbol .= "<span><b>".$priceIteration."</b><b class='bold'>" . $currencySymbol . round ( Mage::helper ( 'directory' )->currencyConvert ( $subscriptionPrice, Mage::app ()->getStore ()->getBaseCurrencyCode (), Mage::app ()->getStore ()->getCurrentCurrencyCode () ), 0 ) . "</b></span>";
        $symbol .= "<input type='hidden' id='dayscount' value='" . $subscriptionBillingFrequency * $subscriptionBillingPeriodCalc ['subscriptionBillingPeriodCalc'] * $subscriptionBillingCycle . "' >";
        $symbol .= "<span><b>".$totalNights."</b><b class='bold' id='frequency'>" . $subscriptionBillingFrequency * $subscriptionBillingPeriodCalc ['subscriptionBillingPeriodCalc'] * $subscriptionBillingCycle . "</b></p>";
        /**
         * Set response
         */
        Mage::app ()->getResponse ()->setBody ( $symbol );
    }
    
    /**
     * Get Product subscription type collection
     */
    public function productSubscriptionCollection() {
        return Mage::getModel ( 'airhotels/productsubscriptions' )->getCollection ()->addFieldToFilter ( 'is_delete', '0' );
    }
    /**
     * Function for getting subscription type collection
     */
    public function subscriptionTypeCollection() {
        return Mage::getModel ( 'airhotels/subscriptiontype' )->getCollection ()->addFieldToFilter ( 'status', '1' );
    }
    /**
     * Function for getting billing period no.
     * of days
     *
     * @param unknown $subscriptionBillingPeriod            
     * @return multitype:string
     */
    public function getBillingPeriod($subscriptionBillingPeriod) {
        switch ($subscriptionBillingPeriod) {
            /**
             * Check case 1 for Day.
             */
            case 1 :
                $subscriptionBillingPeriod = "Day";
                $subscriptionBillingPeriodCalc = "1";
                break;
            /**
             * Checking case 2 for weekly.
             */
            case 2 :
                $subscriptionBillingPeriod = "Weekly";
                $subscriptionBillingPeriodCalc = "7";
                break;
            /**
             * Checking case 4 for monthly.
             */
            case 4 :
                $subscriptionBillingPeriod = "Monthly";
                $subscriptionBillingPeriodCalc = "30";
                break;
            case 5 :
                /**
                 * Checking case 5 for yearly.
                 */
                $subscriptionBillingPeriod = "Yearly";
                $subscriptionBillingPeriodCalc = "365";
                break;
            /**
             * Default case.
             */
            default :
                $subscriptionBillingPeriod = "";
                $subscriptionBillingPeriodCalc = "";
                break;
        }
        /**
         * Returning array contains the Values
         */
        return array (
                "subscriptionBillingPeriod" => $subscriptionBillingPeriod,
                "subscriptionBillingPeriodCalc" => $subscriptionBillingPeriodCalc 
        );
    }
    
    /**
     * Get the Available Types
     *
     * @return Array
     */
    public function getAvailableTypes() {
        /**
         * get the Colletion of subscriptiontype
         */
        return Mage::getModel ( 'airhotels/subscriptiontype' )->getCollection ()->addFieldToFilter ( 'status', '1' );
    }
    
    /**
     * Subscription Method for Property
     */
    public function subscriptionfrequency() {
        /**
         * Get the subscription ID
         */
        $subscriptionid = Mage::app ()->getRequest ()->getParam ( 'subscriptionid' );
        /**
         * Adding filters to product Subscription Colletion
         */
        $productSubscriptionCollection = $this->subscriptionTypeCollection ()->addFieldToFilter ( 'id', $subscriptionid );
        
        /**
         * Get subscription type collection
         *
         * @var $subscriptionTypeCollection
         */
        $subscriptionTypeCollection = $this->subscriptionTypeCollection ()->addFieldToFilter ( 'id', $subscriptionid );
        
        foreach ( $subscriptionTypeCollection as $subscriptionType ) {
            
            $subscriptionBillingFrequency = $subscriptionType ['billing_frequency'];
            $subscriptionBillingPeriod = $subscriptionType ['billing_period_unit'];
            $subscriptionBillingCycle = $subscriptionType ['billing_cycle'];
        }
        $subscriptionBillingPeriodCalc = $this->getBillingPeriod ( $subscriptionBillingPeriod );
        foreach ( $productSubscriptionCollection as $subscription ) {
            /**
             * Get subscription type
             * 
             * @var $subscriptionPrice
             */
            $subscriptionPrice = trim ( $subscription ['price_per_iteration'] );
        }
        /**
         * Get currency symbol.
         * 
         * @var $currencySymbol
         */
        $symbol = "<span><b>Billing Frequency</b><b class='bold'>" . $subscriptionBillingFrequency . "</b></span>" . "<span><b>Billing Period</b><b class='bold'>" . $subscriptionBillingPeriodCalc ['subscriptionBillingPeriod'] . "</b></span>" . "<span><b>Billing Cycle</b><b class='bold'>" . $subscriptionBillingCycle . "</b></span>";
        $symbol .= "<input type='hidden' id='subscription_price' value='" . $subscriptionPrice . "' >";
        $symbol .= "<input type='hidden' id='subscription_cycle' value='" . $subscriptionBillingCycle . "' >";
        /**
         * Set response
         */
        Mage::app ()->getResponse ()->setBody ( $symbol );
    }
}