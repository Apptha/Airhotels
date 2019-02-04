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
 * This class contains Invite Friends manipulation functionality
 */
class Apptha_Airhotels_PayoutController extends Mage_Core_Controller_Front_Action {
    /**
     * payoutAction - add Payout details based on country and currency
     *
     * @var $customerId
     * @var $smsEnabledOrNot
     */
    public function payoutAction() {
        Mage::getSingleton ( 'customer/session' )->setBeforeAuthUrl ( Mage::helper ( 'airhotels' )->getformUrl () );
        /**
         * Get customerId.
         */
        $customerId = Mage::getSingleton ( 'customer/session' )->getCustomer ()->getId ();
        /**
         * Check SMS Configuration from backend.
         */
        $smsEnabledOrNot = Mage::helper ( 'airhotels/smsconfig' )->getSmsEnabledOrNot ();
        if ($customerId) {
            if ($smsEnabledOrNot == 0) {
                /**
                 * Set success message.
                 */
                Mage::getSingleton ( 'core/session' )->addSuccess ( $this->__ ( 'Phone number have been verified' ) );
            }
            /**
             * Load and render layout
             */
            $this->loadLayout ();
            $this->_initLayoutMessages ( 'catalog/session' );
            /**
             * Set page title.
             */
            $this->getLayout ()->getBlock ( 'head' )->setTitle ( $this->__ ( 'Add bank transfer' ) );
            $this->renderLayout ();
        } else {
            /**
             * Set error meaasegs in session
             */
            Mage::getSingleton ( 'core/session' )->addError ( $this->__ ( 'You are not currently logged in' ) );
            $this->_redirectUrl ( Mage::helper ( 'customer' )->getLoginUrl () );
        }
    }
    /**
     * getfieldsAction - load Payout details fields
     *
     * @var $currencyCode
     *
     */
    public function getfieldsAction() {
        /**
         * Set bank currency code.
         */
        $currencyCode = $this->getRequest ()->getPost ( 'currency' );
        Mage::getSingleton ( 'customer/session' )->setBankCurrencyCode ( $currencyCode );
        /**
         * Redirect to customer payout section
         */
        $this->_redirect ( '*/payout/payout/' );
    }
    /**
     * payoutsaveAction - save the Payout details of host
     *
     * @var $customer
     * @var $serialize
     * @var $CustomerId
     * @var $collection
     *
     */
    public function payoutsaveAction() {
        /**
         * Get customer details.
         */
        $customer = Mage::getSingleton ( 'customer/session' )->getCustomer ();
        Mage::getSingleton ( 'customer/session' )->setBeforeAuthUrl ( Mage::helper ( 'airhotels' )->getformUrl () );
        /**
         * Get customer id.
         */
        $customerId = Mage::getSingleton ( 'customer/session' )->getCustomer ()->getId ();
        if ($customerId) {
            $post = $this->getRequest ()->getPost ();
            $serialize = serialize ( $post );
            $CustomerId = $customer->getId ();
            /**
             * Set customer id
             * Set branch name
             * Set bank details.
             */
            $collection = Mage::getModel ( 'airhotels/customerphoto' )->load ( $CustomerId, 'customer_id' );
            $collection->setCustomerId ( $CustomerId );
            $collection->setBranchName ( $serialize );
            $collection->setBankDetails ( $serialize );
            $collection->save ();
            /**
             * Get BankCurrencyCode from customer session
             *
             * Unset session data
             */
            if (Mage::getSingleton ( 'customer/session' )->getBankCurrencyCode ()) {
                Mage::getSingleton ( 'customer/session' )->unsBankCurrencyCode ();
            }
            /**
             * Unset bank country code from customer seesion.
             */
            if (Mage::getSingleton ( 'customer/session' )->getBankCountryCode ()) {
                Mage::getSingleton ( 'customer/session' )->unsBankCountryCode ();
            }
            /**
             * redirect to dashboard payment section
             *
             * Set success message.
             */
            Mage::getSingleton ( 'core/session' )->addSuccess ( $this->__ ( 'Payout details have been saved' ) );
            $this->_redirect ( '*/dashboard/payment/' );
        } else {
            /**
             * Redirect to customer login url
             *
             * Set error message.
             */
            Mage::getSingleton ( 'core/session' )->addError ( $this->__ ( 'You are not currently logged in' ) );
            $this->_redirectUrl ( Mage::helper ( 'customer' )->getLoginUrl () );
        }
    }
    /**
     * Function Name: payoutDeleteAction
     *
     * payoutDeleteAction - delete the Payout details of host.
     *
     * @var $customerId
     *
     */
    public function payoutDeleteAction() {
        /**
         * Set BeforeAuthUrl in customer session
         */
        Mage::getSingleton ( 'customer/session' )->setBeforeAuthUrl ( Mage::helper ( 'airhotels' )->getformUrl () );
        $customerId = Mage::getSingleton ( 'customer/session' )->getCustomer ()->getId ();
        if ($customerId) {
            $payoutDetails = '';
            /**
             * Getting customer collection from customerphoto
             */
            $collection = Mage::getModel ( 'airhotels/customerphoto' )->load ( $CustomerId, 'customer_id' );
            $collection->setCustomerId ( $customerId );
            $collection->setBankDetails ( $payoutDetails );
            /**
             * Save payout information
             */
            $collection->save ();
            /**
             * Set success message.
             *
             * Redirected to dashboard payment page.
             */
            Mage::getSingleton ( 'core/session' )->addSuccess ( $this->__ ( 'Payout details have been removed' ) );
            $this->_redirect ( '*/dashboard/payment/' );
        } else {
            /**
             * Set error message.
             *
             * Redirected to login url.
             */
            Mage::getSingleton ( 'core/session' )->addError ( $this->__ ( 'You are not currently logged in' ) );
            $this->_redirectUrl ( Mage::helper ( 'customer' )->getLoginUrl () );
        }
    }
    /**
     * Function Name: getcurrencyAction
     *
     * getcurrencyAction - get currency based on country
     *
     * @var $countryCode
     */
    public function getcurrencyAction() {
        /**
         * Set bank country code.
         */
        $countryCode = $this->getRequest ()->getPost ( 'country' );
        Mage::getSingleton ( 'customer/session' )->setBankCountryCode ( $countryCode );
        /**
         * Redirect to payout section
         */
        $this->_redirect ( '*/payout/payout/' );
    }
    /**
     * Function Name: paypalSaveAction
     *
     * paypalSaveAction - save the host payapal id
     *
     * @var $customerId
     * @var $customerCollection
     * @var $wantedIds
     * @var $paypalEmailId @array $data
     */
    public function paypalSaveAction() {
        Mage::getSingleton ( 'customer/session' )->setBeforeAuthUrl ( Mage::helper ( 'airhotels' )->getformUrl () );
        $customerId = Mage::getSingleton ( 'customer/session' )->getCustomer ()->getId ();
        if ($customerId) {
            /**
             * getting customer collection from airhotels
             */
            $customerCollection = Mage::getModel ( 'airhotels/customerphoto' )->getCollection ();
            $wantedIds = $customerCollection->getAllIds ();
            /**
             * Get paypal emailId.
             */
            $paypalEmailId = $this->getRequest ()->getPost ( 'paypal_email' );
            if (in_array ( $customerId, $wantedIds )) {
                $data = array (
                        'customer_id' => $customerId,
                        'paypal_email' => $paypalEmailId 
                );
                /**
                 * Store customer details.
                 */
                $collectionVal = Mage::getModel ( 'airhotels/customerphoto' )->load ( $customerId, 'customer_id' );
                $collectionVal->addData ( $data );
                $collectionVal->save ();
            } else {
                /**
                 * Save customer paypal email
                 * 
                 * @var unknown
                 */
                $collectionVal = Mage::getModel ( 'airhotels/customerphoto' )->load ( $customerId, 'customer_id' );
                $collectionVal->setCustomerId ( $customerId );
                $collectionVal->setPaypalEmail ( $paypalEmailId );
                $collectionVal->save ();
            }
            /**
             * Set success message.
             */
            Mage::getSingleton ( 'core/session' )->addSuccess ( $this->__ ( 'Payout details have been saved' ) );
            /**
             * Redirect to payment tab
             */
            $this->_redirect ( '*/dashboard/payment/' );
        } else {
            /**
             * Set error message.
             * Redirect to payment tab
             */
            Mage::getSingleton ( 'core/session' )->addError ( $this->__ ( 'You are not currently logged in' ) );
            $this->_redirectUrl ( Mage::helper ( 'customer' )->getLoginUrl () );
        }
    }
    /**
     * Function name: paypalDeleteAction
     *
     * paypalDeleteAction - delete the paypal id of host.
     *
     * @var $customerId
     * @var $paypalEmail
     * @var $collection
     */
    public function paypalDeleteAction() {
        Mage::getSingleton ( 'customer/session' )->setBeforeAuthUrl ( Mage::helper ( 'airhotels' )->getformUrl () );
        $customerId = Mage::getSingleton ( 'customer/session' )->getCustomer ()->getId ();
        if ($customerId) {
            $paypalEmail = '';
            /**
             * Getting Customer collection from Customerphoto
             * Store customerId.
             * Store paypalemail.
             */
            $collection = Mage::getModel ( 'airhotels/customerphoto' )->load ( $CustomerId, 'customer_id' );
            $collection->setCustomerId ( $customerId );
            $collection->setPaypalEmail ( $paypalEmail );
            $collection->save ();
            /**
             * Set success message.
             */
            Mage::getSingleton ( 'core/session' )->addSuccess ( $this->__ ( 'Payout details have been removed' ) );
            /**
             * redirect to payment section
             */
            $this->_redirect ( '*/dashboard/payment/' );
        } else {
            /**
             * Set error message.
             * redirected to customer login.
             */
            Mage::getSingleton ( 'core/session' )->addError ( $this->__ ( 'You are not currently logged in' ) );
            $this->_redirectUrl ( Mage::helper ( 'customer' )->getLoginUrl () );
        }
    }
    /**
     * Function name: updataMailIconAction
     *
     * Function to update the mail unread count
     *
     * @var $customer
     * @var $loggedinId
     * @var $inboxCollection
     * @var $replyMessageCollection
     * @var $TotalCount
     *
     * @return int
     */
    public function updataMailIconAction() {
        $customer = Mage::getSingleton ( 'customer/session' )->getCustomer ();
        $loggedinId = $customer->getId ();
        /**
         * Load customer collections
         * 
         * @var unknown
         */
        $inboxCollection = Mage::getModel ( 'airhotels/customerinbox' )->getCollection ()->addFieldTofilter ( 'receiver_id', $loggedinId )->addFieldToFilter ( 'receiver_read', 0 )->addFieldToFilter ( 'is_receiver_delete', 0 );
        /**
         * Get replay message count.
         * Get total inbox message count.
         */
        $replyMessageCollection = Mage::getModel ( 'airhotels/customerinbox' )->getCollection ()->addFieldTofilter ( 'sender_id', $loggedinId )->addFieldToFilter ( 'sender_read', 0 )->addFieldToFilter ( 'isdelete', 0 )->addFieldToFilter ( 'is_reply', 1 )->addFieldToFilter ( 'is_sender_delete', 0 );
        $TotalCount = count ( $inboxCollection ) + count ( $replyMessageCollection );
        if (count ( $TotalCount ) > 0) {
            /**
             * Get message count.
             */
            echo count ( $TotalCount );
        }
    }
    /**
     * Function Name: requestAction
     *
     * Function to update request payment for host
     *
     * @var $order_id
     *
     * @return int
     */
    public function requestAction() {
        if (! Mage::getSingleton ( 'customer/session' )->isLoggedIn ()) {
            /**
             * Set login redirect url.
             */
            $this->_redirectUrl ( Mage::helper ( 'customer' )->getLoginUrl () );
            return false;
        }
        /**
         * getting params
         */
        $order_id = $this->getRequest ()->getParam ( 'order_id' );
        Mage::getModel ( 'airhotels/property' )->paymentRequest ( $order_id );
        /**
         * Redirect to transaction history page.
         */
        $this->_redirect ( '*/dashboard/transactionhistory/' );
    }
}