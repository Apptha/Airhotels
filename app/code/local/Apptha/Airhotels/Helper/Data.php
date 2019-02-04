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
 * Class Apptha_Airhotels_Helper_Data
 *
 * extends Mage_Core_Helper_Abstract
 * 
 * @author user
 *        
 */
class Apptha_Airhotels_Helper_Data extends Mage_Core_Helper_Abstract {
    /**
     * Function Name: getformurl
     * Retrieve property form url
     *
     * @return string
     */
    public function getformurl() {
        /**
         * Returning the FormUrl
         */
        return $this->_getUrl ( 'property/general/form' );
    }
    /**
     * Function Name: checkInArray
     * Check Array is available
     *
     * @return boolean
     */
    public function checkInArray($product) {
        /**
         * Check weather the value exist in in array
         */
        if (! in_array ( Mage::app ()->getStore ()->getWebsiteId (), $product->getWebsiteIds () )) {
            return false;
        }
    }
    /**
     * Function Name: 'getSecurityFee'
     * Retrieve attribute id for security fee
     *
     * @return integer
     */
    public function getSecurityFee() {
        /**
         * Getting entity attribute source model for securityFee
         */
        return Mage::getResourceModel ( 'eav/entity_attribute' )->getIdByCode ( 'catalog_product', 'security' );
    }
    /**
     * Function Name: getcalendarurl
     * Retrieve property calender
     *
     * @return string
     */
    public function getcalendarurl() {
        /**
         * Returning the Calendar Url
         */
        return $this->_getUrl ( 'property/property/calender' );
    }
    /**
     * Function to remove file from directory
     *
     * @var $file
     * @var $_helper
     *
     * @param unknown $file            
     * @return boolean
     */
    public function removeFile($file) {
        /**
         * remove image file from directory
         * 
         * @var unknown
         */
        $_helper = Mage::helper ( 'airhotels' );
        $file = $_helper->updateDirSepereator ( $file );
        $directory = Mage::getBaseDir ( 'media' ) . DS . $file;
        /**
         * Use Varien_Io_File() function
         * 
         * @var unknown
         */
        $io = new Varien_Io_File ();
        return $io->rmdir ( $directory, true );
    }
    /**
     * Funtion to seperate directory
     *
     * @param unknown $path            
     */
    public function updateDirSepereator($path) {
        return str_replace ( '\\', DS, $path );
    }
    /**
     * Function Name: getaccomodates
     * Retrieve attribute id for accomodates
     *
     * @return integer
     */
    public function getaccomodates() {
        /**
         * Returning the ResourceModel Value.
         */
        return Mage::getResourceModel ( 'eav/entity_attribute' )->getIdByCode ( 'catalog_product', 'accomodates' );
    }
    /**
     * Function Name: getdescription
     * Retrieve attribute id for description
     *
     * @return integer
     */
    public function getdescription() {
        /**
         * Returning the resource Model for 'description'
         */
        return Mage::getResourceModel ( 'eav/entity_attribute' )->getIdByCode ( 'catalog_product', 'description' );
    }
    /**
     * Function Name: getshortdescription
     * Retrieve attribute id for short_description
     *
     * @return integer
     */
    public function getshortdescription() {
        /**
         * Returning the resource Model for 'short_description'
         */
        return Mage::getResourceModel ( 'eav/entity_attribute' )->getIdByCode ( 'catalog_product', 'short_description' );
    }
    /**
     * Function Name:
     * Retrieve attribute id for hostemail
     *
     * @return integer
     */
    public function gethostemail() {
        /**
         * Returning the resource Model for 'hostemail'
         */
        return Mage::getResourceModel ( 'eav/entity_attribute' )->getIdByCode ( 'catalog_product', 'hostemail' );
    }
    /**
     * Function Name: getname
     * Retrieve attribute id for name
     *
     * @return integer
     */
    public function getname() {
        /**
         * Returning the resource Model for 'name'
         */
        return Mage::getResourceModel ( 'eav/entity_attribute' )->getIdByCode ( 'catalog_product', 'name' );
    }
    /**
     * Function Name: getprice
     * Retrieve attribute id for price
     *
     * @return integer
     */
    public function getprice() {
        /**
         * Returning the resource Model for 'price'
         */
        return Mage::getResourceModel ( 'eav/entity_attribute' )->getIdByCode ( 'catalog_product', 'price' );
    }
    /**
     * Function Name: getaddress
     * Retrieve attribute id for propertyadd
     *
     * @return integer
     */
    public function getaddress() {
        /**
         * Returning the resource Model for 'propertyadd'
         */
        return Mage::getResourceModel ( 'eav/entity_attribute' )->getIdByCode ( 'catalog_product', 'propertyadd' );
    }
    /**
     * Function Name: getroom
     * Retrieve attribute id for totalrooms
     *
     * @return integer
     */
    public function getroom() {
        /**
         * Returning the resource Model for 'totalrooms'
         */
        return Mage::getResourceModel ( 'eav/entity_attribute' )->getIdByCode ( 'catalog_product', 'totalrooms' );
    }
    /**
     * Function Name: getsmallimage
     * Retrieve attribute id for small image
     *
     * @return integer
     */
    public function getsmallimage() {
        /**
         * Returning the resource Model for 'small_image'
         */
        return Mage::getResourceModel ( 'eav/entity_attribute' )->getIdByCode ( 'catalog_product', 'small_image' );
    }
    /**
     * Function Name: getstate
     * Retrieve attribute id for state
     *
     * @return integer
     */
    public function getstate() {
        /**
         * Returning the resource Model for 'state'
         */
        return Mage::getResourceModel ( 'eav/entity_attribute' )->getIdByCode ( 'catalog_product', 'state' );
    }
    /**
     * Function Name: getcity
     * Retrieve attribute id for city
     *
     * @return integer
     */
    public function getcity() {
        /**
         * Returning the resource Model for 'city'
         */
        return Mage::getResourceModel ( 'eav/entity_attribute' )->getIdByCode ( 'catalog_product', 'city' );
    }
    /**
     * Function Name: getcountry
     * Retrieve attribute id for country
     *
     * @return integer
     */
    public function getcountry() {
        /**
         * Returning the resource Model for 'country'
         */
        return Mage::getResourceModel ( 'eav/entity_attribute' )->getIdByCode ( 'catalog_product', 'country' );
    }
    /**
     * This function has been used to validate the lenght of the string.
     *
     * @var $productName
     * @var $lenProductName
     * @var $subProductName
     * @var $prNameFix
     *
     * @param unknown $actualName            
     * @return Ambigous <string, unknown>
     */
    public function wordCounter($actualName) {
        $productName = $actualName;
        $lenProductName = strlen ( $actualName );
        $subProductName = substr ( $productName, 0, 40 );
        if ($lenProductName >= 40) {
            $prNameFix = $subProductName . "...";
        } else {
            $prNameFix = $productName;
        }
        return $prNameFix;
    }
    /**
     * Function Name: creditmemo
     *
     * @var $orderId
     * @param unknown $orderId            
     */
    public function creditmemo($orderId) {
        /**
         * Check that customer login or not.
         */
        if (Mage::getSingleton ( 'customer/session' )->isLoggedIn () && isset ( $orderId )) {
            $order = Mage::getModel ( "sales/order" )->load ( $orderId );
        } else {
            /**
             * Error message for the when unwanted person access these request.
             */
            Mage::getSingleton ( 'core/session' )->addError ( $this->__ ( "You do not have permission to access this page." ) );
            return;
        }
        try {
            $invoices = array ();
            foreach ( $order->getInvoiceCollection () as $invoice ) {
                $invoices [] = $invoice;
            }
            $service = Mage::getModel ( 'sales/service_order', $order );
            if (! $order->getId ()) {
                /**
                 * Display message.
                 */
                $this->_fault ( 'order_not_exists' );
            }
            if (! $order->canCreditmemo ()) {
                /**
                 * Set error
                 */
                $this->_fault ( 'cannot_create_creditmemo' );
            }
            $data = array ();
            $service = Mage::getModel ( 'sales/service_order', $order );
            $creditmemo = $service->prepareCreditmemo ( $data );
            /**
             * refund to Store Credit
             */
            if ($refundToStoreCreditAmount) {
                /**
                 * check if refund to Store Credit is available
                 */
                if ($order->getCustomerIsGuest ()) {
                    $this->_fault ( 'cannot_refund_to_storecredit' );
                }
                $refundToStoreCreditAmount = max ( 0, min ( $creditmemo->getBaseCustomerBalanceReturnMax (), $refundToStoreCreditAmount ) );
                if ($refundToStoreCreditAmount) {
                    $refundToStoreCreditAmount = $creditmemo->getStore ()->roundPrice ( $refundToStoreCreditAmount );
                    $creditmemo->setBaseCustomerBalanceTotalRefunded ( $refundToStoreCreditAmount );
                    $refundToStoreCreditAmount = $creditmemo->getStore ()->roundPrice ( $refundToStoreCreditAmount * $order->getStoreToOrderRate () );
                    /**
                     * this field can be used by customer balance observer
                     */
                    $creditmemo->setBsCustomerBalTotalRefunded ( $refundToStoreCreditAmount );
                    /**
                     * setting flag to make actual refund to customer balance after credit memo save
                     */
                    $creditmemo->setCustomerBalanceRefundFlag ( true );
                }
            }
            $creditmemo->setPaymentRefundDisallowed ( true )->register ();
            /**
             * add comment to creditmemo
             */
            if (! empty ( $comment )) {
                $creditmemo->addComment ( $comment, $notifyCustomer );
            }
            try {
                Mage::getModel ( 'core/resource_transaction' )->addObject ( $creditmemo )->addObject ( $order )->save ();
                /**
                 * send email notification
                 */
                $creditmemo->sendEmail ( $notifyCustomer, $comment );
            } catch ( Mage_Core_Exception $e ) {
                /**
                 * Set invalid message
                 */
                $this->_fault ( 'data_invalid', $e->getMessage () );
            }
            return;
        } catch ( Mage_Core_Exception $e ) {
            /**
             * Set error message.
             */
            Mage::getSingleton ( 'core/session' )->addError ( $this->__ ( "You do not have permission to access this page." ) );
            return;
        }
    }
}