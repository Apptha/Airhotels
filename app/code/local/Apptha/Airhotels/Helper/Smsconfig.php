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
class Apptha_Airhotels_Helper_Smsconfig extends Mage_Core_Helper_Url {
    /**
     * Function Name: 'getHourlyEnabledOrNot'
     * Retrieve Hourly enabled or not
     *
     * @return string
     */
    public function getSmsEnabledOrNot() {
        return Mage::getStoreConfig ( 'airhotels/nexmo/enable' );
    }
    /**
     * Get custom attribute option enable or not for seller
     */
    public function getCustomAttributeEnableOrNot() {
        return Mage::getStoreConfig ( 'airhotels/custom_group/custom_attribute' );
    }
    /**
     * Save custom attributes
     *
     * @param unknown $product            
     * @param unknown $productData            
     * @return unknown
     */
    public function customAttributeSave($product, $productData) {
        /**
         * Update custom attributes values for seller inputs
         */
        foreach ( $productData as $dataKey => $dataValue ) {
            if (array_key_exists ( $dataKey, $product->getData () ) && $productData [$dataKey] != $product->getData ( $dataKey )) {
                $specificationFunction = 'set' . str_replace ( '_', "", uc_words ( $dataKey ) );
                $product->$specificationFunction ( $productData [$dataKey] );
            }
        }
        /**
         * Return product collection.
         */
        return $product;
    }
    
    /**
     * send order message to customer
     *
     * @param
     *            placed order id $orderId
     * @param
     *            message id to send text message $messageId
     */
    public function sendordermessage($orderId, $messageId) {
        $text = "";
        /**
         * Get customer id.
         */
        $customerData = Mage::getSingleton ( 'customer/session' )->getCustomer ();
        $customerId = $customerData->getId ();
        if ($customerId) {
            /**
             * Get customer details
             */
            $customerDetails = Mage::getModel ( 'airhotels/customerphoto' )->getCollection ()->addFieldtofilter ( 'customer_id', $customerId )->addFieldtofilter ( 'mobile_verified_profile', 'verified' );
            
            foreach ( $customerDetails as $details ) {
                $contactNumber = unserialize ( $details ['contact_number'] );
                if ($messageId == 1) {
                    /**
                     * Set content in variables.
                     */
                    $text = "Order placed successfully. Your orderId is #" . $orderId;
                }
                /**
                 * Send order message.
                 */
                $this->sendMessage ( $contactNumber ['isd_code'] . $contactNumber ['contact_number'], $text );
            }
        }
    }
    
    /**
     * Send message to phone
     *
     * @param
     *            Phonenumber to send message $phoneNumber
     * @param
     *            send text message $text
     */
    public function sendMessage($isdCode,$phoneNumber, $text ,$code) {
        $nexmoKey = Mage::getStoreConfig ( 'airhotels/nexmo/nexmo_key' );
        $nexmoSecret = Mage::getStoreConfig ( 'airhotels/nexmo/nexmo_secret' );
        $nexmoFrom = Mage::getStoreConfig ( 'airhotels/nexmo/nexmo_from' );
        /**
         * Set message to customer mobile api url
         * @parameters api_key
         * @parameters api_secret
         * @parameters api_from
         * @parameters api_to
         * @parameters api_text
         */
        if($isdCode == "+1"){
        $url = 'https://rest.nexmo.com/sc/us/2fa/json?' . http_build_query([
        'api_key' => $nexmoKey,
        'api_secret' => $nexmoSecret,
        'to' => $phoneNumber,
        'pin' => $code
        ]);
        }else{
        $url = 'https://rest.nexmo.com/sms/json?' . http_build_query ( array (
                'api_key' => $nexmoKey,
                'api_secret' => $nexmoSecret,
                'from' => $nexmoFrom,
                'to' => $phoneNumber,
                'text' => $text 
        ) );
        }
        /**
         * Initalize curl.
         */
        $curlRequest = curl_init ( $url );
        curl_setopt ( $curlRequest, CURLOPT_SSL_VERIFYPEER, false );
        curl_setopt ( $curlRequest, CURLOPT_RETURNTRANSFER, 1 );
        /**
         * Execute curl.
         */
        $result = curl_exec ( $curlRequest );
        
        if (empty ( $result )) {
            trigger_error ( curl_error ( $curlRequest ) );
        }
        /**
         * Close curl request
         */
        curl_close ( $curlRequest );
        
        return $result;
    }
    /**
     * Function Name: redirectVerification
     *
     * return Url
     */
    public function redirectVerification() {
        Mage::app()->getResponse()->setRedirect( 'accountverification' );
    }
}