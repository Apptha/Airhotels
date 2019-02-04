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
 * Invite friends model class
 */
class Apptha_Airhotels_Model_Invitefriends extends Mage_Core_Model_Abstract {
    /**
     * Constructor class
     */
    public function _construct() {
        /**
         * Calling the parent Construct Method.
         */
        parent::_construct ();
        /**
         * Initialize the invite friends blcok.
         */
        $this->_init ( 'airhotels/invitefriends' );
    }
    /**
     * Adding new customer
     */
    public function addNewCustomer($ref, $customer) {
        /**
         * Getting invitee details
         */
        $inviteeData = Mage::getModel ( 'customer/customer' );
        $inviteeData->setWebsiteId ( Mage::app ()->getWebsite ()->getId () );
        $inviteeData->loadByEmail ( trim ( $ref ) );
        $inviteeId = $inviteeData->getId ();
        if (! empty ( $inviteeId )) {
            /**
             * Get Name
             * Get email.
             */
            $inviteeName = $inviteeData->getName ();
            $inviteeEmail = $inviteeData->getEmail ();
            /**
             * Getting new customer details
             */
            $customerEmail = $customerName = $customerEmail = '';
            if (isset ( $customer ['entity_id'] )) {
                /**
                 * Get customer Id.
                 */
                $customerId = $customer ['entity_id'];
            }
            if (! empty ( $customer ['lastname'] )) {
                /**
                 * Get customer Name.
                 */
                $customerName = $customerName . ' ' . $customer ['lastname'];
            }
            if (isset ( $customer ['email'] )) {
                /**
                 * Get customer email.
                 */
                $customerEmail = $customer ['email'];
            }
            if (isset ( $customer ['firstname'] )) {
                $customerName = $customer ['firstname'];
            }
            /**
             * Getting current store and website id
             */
            $websiteId = Mage::app ()->getStore ()->getWebsiteId ();
            $storeId = Mage::app ()->getStore ()->getStoreId ();
            /**
             * Store customer invite friends details,
             * 'customer_id','customer_name'
             * 'customer_email','invitee_id','invitee_name'
             * 'invitee_email','store_id','website_id'
             * 'current_credit_amount','friend_purchase_count'
             * 'friend_listing_count','overall_credit_amount'
             */
            $model = Mage::getModel ( 'airhotels/invitefriends' );
            $model->setCustomerId ( $customerId );
            $model->setCustomerName ( $customerName );
            $model->setCustomerEmail ( $customerEmail );
            $model->setInviteeId ( $inviteeId );
            $model->setInviteeName ( $inviteeName );
            $model->setInviteeEmail ( $inviteeEmail );
            $model->setStoreId ( $storeId );
            $model->setWebsiteId ( $websiteId );
            $model->setCurrentCreditAmount ( 0 );
            $model->setFriendsPurchaseCount ( 0 );
            $model->setFriendsListingCount ( 0 );
            $model->setOverallCreditAmount ( 0 );
            $model->save ();
        }
        return;
    }
    /**
     * Update customer credit amount
     *
     * @param int $customerId            
     * @param int $websiteId            
     * @param int $storeId            
     * @param string $option            
     * @param int $optionValue            
     * @return boolean
     */
    public function updateCustomerCreditAmount($customerId, $websiteId, $storeId, $option, $optionValue) {
        /**
         * Getting invitee details
         */
        $inviteFriendsCollection = Mage::getModel ( 'airhotels/invitefriends' )->getCollection ()->addFieldToFilter ( 'customer_id', $customerId )->addFieldToFilter ( 'website_id', $websiteId )->addFieldToFilter ( 'invitee_id', array (
                'neq' => '' 
        ) )->getFirstItem ();
        /**
         * Get invitee id.
         * 
         * @var $inviteeId
         */
        $inviteeId = $inviteFriendsCollection->getInviteeId ();
        $inviteeCollection = Mage::getModel ( 'airhotels/invitefriends' )->getCollection ()->addFieldToFilter ( 'customer_id', $inviteeId )->addFieldToFilter ( 'website_id', $websiteId )->getFirstItem ();
        /**
         * Update invitee credit amount
         */
        $creditAmount = 0;
        if ($inviteeCollection->getCustomerId ()) {
            if ($option == 'purchase') {
                $invitefriendsCount = Mage::getModel ( 'airhotels/invitefriendsorder' )->getCollection ()->addFieldToFilter ( 'customer_id', $customerId )->addFieldToFilter ( 'invitee_id', $inviteFriendsCollection->getInviteeId() )->addFieldToFilter ( 'website_id', $websiteId )->addFieldToFilter ( 'store_id', $storeId )->count();
                /**
                 * Get credit amount.
                 */
                $creditAmount = ( int ) Mage::helper ( 'airhotels/invitefriends' )->getCreditAmountForPurchase ();
            }
            
            if ($option == 'listing') {
                $invitefriendsCount = Mage::getModel ( 'airhotels/invitefriendsorder' )->getCollection ()->addFieldToFilter ( 'customer_id', $customerId )->addFieldToFilter ( 'invitee_id', $inviteFriendsCollection->getInviteeId() )->addFieldToFilter ( 'product_id', array('gt' => 0))->addFieldToFilter ( 'website_id', $websiteId )->addFieldToFilter ( 'store_id', $storeId )->count();
                /**
                 * Get credit amount.
                 */
                $creditAmount = ( int ) Mage::helper ( 'airhotels/invitefriends' )->getCreditAmountForListing ();
            }
            if($invitefriendsCount == 0){
            $id = $inviteeCollection->getId ();
            $balanceCreditAmount = $inviteeCollection->getBalanceCreditAmount ();
            $overallCreditAmount = $inviteeCollection->getOverallCreditAmount ();
            $friendsPurchaseCount = $inviteeCollection->getFriendsPurchaseCount ();
            $friendsListingCount = $inviteeCollection->getFriendsListingCount ();
            
            Mage::getSingleton('core/session')->setCreditDiscountAmount($creditAmount);
            /**
             * Calculate overall credit_amount
             * calculate balance_credit_amount.
             */
            $data ['balance_credit_amount'] = $balanceCreditAmount + $creditAmount;
            $data ['overall_credit_amount'] = $overallCreditAmount + $creditAmount;

            if ($option == 'purchase') {
                $data ['friends_purchase_count'] = $friendsPurchaseCount + 1;
                /**
                 * Update invite friends products
                 */
                $this->updateInviteFriendsProduct ( $customerId, $inviteeId, $websiteId, $storeId, 'order', $optionValue);
            }
            if ($option == 'listing') {
                /**
                 * Get friend listing count
                 */
                $data ['friends_listing_count'] = $friendsListingCount + 1;
                /**
                 * Update invite friends products.
                 */
                $this->updateInviteFriendsProduct ( $customerId, $inviteeId, $websiteId, $storeId, 'product', $optionValue);
            }
            /**
             * Save date in invitefriends table.
             */
            $model = Mage::getModel ( 'airhotels/invitefriends' );
            $model->setData ( $data )->setId ( $id );
            $model->save ();
            }
        }
        return true;
    }
    
    /**
     * Update invite friends product/order
     *
     * @param int $customerId            
     * @param int $inviteeId            
     * @param int $websiteId            
     * @param int $storeId            
     * @param int $optionValue            
     * @return boolean
     */
    function updateInviteFriendsProduct($customerId, $inviteeId, $websiteId, $storeId, $option, $optionValue) {
        /**
         * Check option value.
         */
        $model = Mage::getModel ( 'airhotels/invitefriendsorder' );
        if ($option == 'product') {
            $model->setProductId ( $optionValue );
        }
        if ($option == 'order') {
            $model->setOrderId ( $optionValue );
        }
        /**
         * Store invite friends order
         * 'customer_id','invitee_id'
         * 'website_id','store_id'
         */
        $model->setInviteeId ( $inviteeId );
        $model->setCustomerId ( $customerId );
        $model->setWebsiteId ( $websiteId );
        $model->setDiscountAmount ( Mage::getSingleton('core/session')->getCreditDiscountAmount() );
        $model->setStoreId ( $storeId );
        $model->save ();
        Mage::getSingleton('core/session')->unsCreditDiscountAmount();
        return true;
    }
    
    /**
     * Update invitee transaction discount details
     */
    function updateInviteeTransactionDiscountDetails($orderid) {
        if (Mage::getSingleton ( 'customer/session' )->isLoggedIn ()) {
            $customerData = Mage::getSingleton ( 'customer/session' )->getCustomer ();
            $customerId = $customerData->getId ();
            $websiteId = Mage::app ()->getWebsite ()->getId ();
            $currentStoreId = Mage::app ()->getStore ()->getId ();
            /**
             * Getting invitee details
             */
            $inviteFriendsCollection = Mage::getModel ( 'airhotels/invitefriends' )->getCollection ()->addFieldToFilter ( 'customer_id', $customerId )->addFieldToFilter ( 'website_id', $websiteId )->getFirstItem ();
            $id = $inviteFriendsCollection->getId ();
            $balanceCreditAmount = $inviteFriendsCollection->getBalanceCreditAmount ();
            if (! empty ( $id ) && $balanceCreditAmount > 0) {
                $discountAmount = Mage::getSingleton ( "core/session" )->getCurrentCustomerDiscountedAmount ();
                if ($balanceCreditAmount >= $discountAmount && $discountAmount != 0) {
                    /**
                     * calculate and store balance credit amount
                     */
                    $data ['balance_credit_amount'] = $balanceCreditAmount - $discountAmount;
                    $model = Mage::getModel ( 'airhotels/invitefriends' );
                    $model->setData ( $data )->setId ( $id );
                    $model->save ();
                    /**
                     * Store invitee order details.
                     */
                    $transactions = Mage::getModel ( 'airhotels/invitefriendsorder' );
                    $transactions->setOrderId ( $orderid );
                    $transactions->setInviteeId ( $customerId );
                    $transactions->setStoreId ( $currentStoreId );
                    $transactions->setWebsiteId ( $websiteId );
                    $transactions->setDiscountAmount ( $discountAmount );
                    $transactions->setOrderStatus ( 1 );
                    $transactions->save ();
                }
            }
        }
        /**
         * Customer discount amount
         */
        Mage::getSingleton ( "core/session" )->setCurrentCustomerDiscountedAmount ( 0 );
        Mage::getSingleton ( "core/session" )->setDiscountAmountForInviteeCurrencyCode ( '' );
        Mage::getSingleton ( "core/session" )->setDiscountAmountForInvitee ( 0 );
        Mage::getSingleton ( "core/session" )->setDiscountAmountForInviteeStatus ( 0 );
        return true;
    }
    
    /**
     * Cancel invitee transaction discount details
     */
    function cancelInviteeTransactionDiscountDetails($orderId) {        
        /**
         * Getting invitee is count collection.
         */
        $discountCollection = Mage::getModel ( 'airhotels/invitefriendsorder' )->getCollection ()->addFieldToFilter ( 'order_id', $orderId )->getFirstItem ();
        $customerId = $discountCollection->getInviteeId ();
        $discountAmount = $discountCollection->getDiscountAmount ();
        if (! empty ( $customerId ) && ! empty ( $discountAmount )) {
            /**
             * Get invite friends collection.
             */
            $inviteFriendsCollection = Mage::getModel ( 'airhotels/invitefriends' )->getCollection ()->addFieldToFilter ( 'customer_id', $customerId )->getFirstItem ();
            $balanceCreditAmount = $inviteFriendsCollection->getBalanceCreditAmount ();
            $id = $inviteFriendsCollection->getId ();
            if (! empty ( $id )) {
                /**
                 * Calculate ans store balance credit amount.
                 */
                $data ['balance_credit_amount'] = $balanceCreditAmount + $discountAmount;
                $model = Mage::getModel ( 'airhotels/invitefriends' );
                $model->setData ( $data )->setId ( $id );
                $model->save ();
                
                /**
                 * Set discount amount status as zero(0)
                 * @var $discountCollection
                 */
                $discountAmountCollection = Mage::getModel ( 'airhotels/invitefriendsorder' );
                $discountStatus['status'] = 2;
                $discountAmountCollection->setData ( $discountStatus )->setId ( $discountCollection->getId() );
                $discountAmountCollection->save ();
            }
        }
        return true;
    }
}