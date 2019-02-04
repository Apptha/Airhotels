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
 * This class contains property and customer manipulation functionality
 */
class Apptha_Airhotels_OrderController extends Mage_Core_Controller_Front_Action {
    
    /**
     * basicSaveAction - Save the new experience for the host
     */
    public function cancelrequestAction() {
        /**
         * Admin configuration for order cancel request active status.
         */
        $orderCancelStatusFlag = Mage::getStoreConfig ( 'airhotels/custom_group/order_cancel_request' );
        $orderId = $this->getRequest ()->getParam ( 'order_id' );
        $loggedInCustomerId = '';
        /**
         * Check that customer login or not.
         */
        if (Mage::getSingleton ( 'customer/session' )->isLoggedIn () && isset ( $orderId )) {
            /**
             * Get logged in customer data.
             */
            $customerData = Mage::getSingleton ( 'customer/session' )->getCustomer ();
            $loggedInCustomerId = $customerData->getId ();
            /**
             * Get ordered customer data.
             */
            $customerid = Mage::getModel ( 'sales/order' )->load ( $orderId )->getCustomerId ();
        } else {
            /**
             * Error message for the when unwanted person access these request.
             */
            Mage::getSingleton ( 'core/session' )->addError ( $this->__ ( "You do not have permission to access this page" ) );
            $this->_redirect ( 'sales/order/history' );
            return;
        }
        /**
         * Check order cancel status.
         */
        if ($orderCancelStatusFlag == 1 && ! empty ( $loggedInCustomerId ) && $customerid == $loggedInCustomerId) {
            try {
                /**
                 * Get templete id for the order cancel request notification.
                 */
                $templateId = ( int ) Mage::getStoreConfig ( 'airhotels/custom_group/order_cancel_request_notification_template_selection' );
                if ($templateId) {
                    /**
                     * Load email templete.
                     */
                    $emailTemplate = Mage::helper ( 'marketplace/marketplace' )->loadEmailTemplate ( $templateId );
                } else {
                    /**
                     * Load cancel order email templete.
                     */
                    $emailTemplate = Mage::getModel ( 'core/email_template' )->loadDefault ( 'airhotels_cancel_order_admin_email_template_selection' );
                }
                /**
                 * Load order product details based on the orde id.
                 */
                $_order = Mage::getModel ( 'sales/order' )->load ( $orderId );
                /**
                 * Get increment id.
                 */
                $incrementId = $_order->getIncrementId ();
                $sellerProductDetails = array ();
                $selectedItemproductId = '';
                /**
                 * Get the order item from the order.
                 */
                foreach ( $_order->getAllItems () as $item ) {
                    /**
                     * Get item product is.
                     * Get seller Id.
                     */
                    $itemProductId = $item->getProductId ();
                    $sellerId = Mage::getModel ( 'catalog/product' )->load ( $itemProductId )->getUserid ();
                    $selectedItemproductId = $itemProductId;
                    $sellerProductDetails [$sellerId] [] = $item->getName ();
                }
                /**
                 * Load customer data.
                 * Load seller data.
                 */
                $customer = Mage::getModel ( 'customer/customer' )->load ( $loggedInCustomerId );
                $seller = Mage::getModel ( 'customer/customer' )->load ( $sellerId );
                /**
                 * Get customer name and customer email id.
                 */
                $buyerName = $customer->getName ();
                $buyerEmail = $customer->getEmail ();
                /**
                 * Get host name and host email id.
                 */
                $sellerEmail = $seller->getEmail ();
                $sellerName = $seller->getName ();
                $recipient = $sellerEmail;
                /**
                 * Set sender name,Sender email.
                 */
                $emailTemplate->setSenderName ( $buyerName );
                $emailTemplate->setSenderEmail ( $buyerEmail );
                /**
                 * To set cancel/refund request sent
                 */
                $requestedType = $this->__ ( 'cancellation' );
                Mage::getModel ( 'airhotels/order' )->updateSellerRequest ( $orderId, '1' );
                $emailTemplateVariables = array (
                        'ownername' => $sellerName,
                        'order_id' => $incrementId,
                        'customer_email' => $buyerEmail,
                        'customer_firstname' => $buyerName,
                        'requesttype' => $requestedType,
                        'requestperson' => $this->__ ( 'Customer' ) 
                );
                $emailTemplate->setDesignConfig ( array (
                        'area' => 'frontend' 
                ) );
                /**
                 * Sending email to admin
                 */
                $emailTemplate->getProcessedTemplate ( $emailTemplateVariables );
                $emailTemplate->send ( $recipient, $sellerName, $emailTemplateVariables );
                /**
                 * Set cancell request success message.
                 * Redirect to view order page.
                 */
                Mage::getSingleton ( 'core/session' )->addSuccess ( $this->__ ( "Booking cancellation request has been sent successfully." ) );
                $this->_redirect ( 'sales/order/view/order_id/' . $orderId );
            } catch ( Exception $e ) {
                /**
                 * Set error message.
                 * Redirect to view order page.
                 */
                Mage::getSingleton ( 'core/session' )->addError ( $this->__ ( $e->getMessage () ) );
                $this->_redirect ( 'sales/order/view/order_id/' . $orderId );
            }
        } else {
            /**
             * Set eror message.
             * and set redirect url.
             */
            Mage::getSingleton ( 'core/session' )->addError ( $this->__ ( "You do not have permission to access this page" ) );
            $this->_redirect ( 'sales/order/view/order_id/' . $orderId );
        }
    }
    /**
     * Function for cancel request to order
     */
    public function cancelAction() {
        /**
         * Get order status flag.
         */
        $orderCancelStatusFlag = Mage::getStoreConfig ( 'airhotels/custom_group/order_cancel_request' );
        $orderId = $this->getRequest ()->getParam ( 'id' );
        /**
         * Check customer logged in or not.
         */
        if (Mage::getSingleton ( 'customer/session' )->isLoggedIn () && isset ( $orderId )) {
            $customerData = Mage::getSingleton ( 'customer/session' )->getCustomer ();
            /**
             * Get logged in customer Id.
             */
            $loggedInCustomerId = $customerData->getId ();
        } else {
            /**
             * Error message for the when unwanted person access these request.
             *
             * Redirect to product history page.
             */
            Mage::getSingleton ( 'core/session' )->addError ( $this->__ ( "You do not have permission to access this page." ) );
            $this->_redirect ( 'property/product/history' );
            return;
        }
        /**
         * Check order status flag and loggin customer Id.
         */
        if ($orderCancelStatusFlag == 1 && ! empty ( $loggedInCustomerId )) {
            
            $orderDetails = Mage::getModel ( 'sales/order' )->load ( $orderId );
            /**
             * Initialicze invoice increment is as empty.
             */
            $invIncrementIDs = array ();
            foreach ( $orderDetails->getInvoiceCollection () as $inv ) {
                $invIncrementIDs [] = $inv->getIncrementId ();
            }
            /**
             * Check invoice increment id.
             */
            if (empty ( $invIncrementIDs )) {
                /**
                 * Set order cancel status.
                 */
                $orderDetails->setState ( Mage_Sales_Model_Order::STATE_CANCELED, true )->save ();
                Mage::getModel ( 'airhotels/order' )->updateSellerRequest ( $orderId, '2' );
                /**
                 * Send order update email.
                 */
                $orderDetails->orderUpdateEmail ( $orderId, 'cancel' );
            } else {
                /**
                 * Update seller request.
                 * and create creditmemo.
                 */
                Mage::getModel ( 'airhotels/order' )->updateSellerRequest ( $orderId, '3' );
                Mage::helper ( 'airhotels' )->creditmemo ( $orderId );
                /**
                 * Send order update email.
                 */
                Mage::getModel ( 'airhotels/order' )->orderUpdateEmail ( $orderId, 'creditmemo' );
            }
            /**
             * Set property cancelled success message.
             * And redirect to view order page.
             */
            Mage::getSingleton ( 'core/session' )->addSuccess ( $this->__ ( "Property has been cancelled." ) );
            $this->_redirect ( 'property/product/vieworder/order_id/' . $orderId );
        } else {
            /**
             * Set error message.
             * And redirect to product history.
             */
            Mage::getSingleton ( 'core/session' )->addError ( $this->__ ( "You do not have permission to access this page." ) );
            $this->_redirect ( 'property/product/history/order_id/' . $orderId );
        }
    }
    /**
     * Function to invoice action
     */
    public function invoiceAction() {
        /**
         * Get order id.
         */
        $orderId = $this->getRequest ()->getParam ( 'id' );
        /**
         * Check that customer login or not.
         */
        if (Mage::getSingleton ( 'customer/session' )->isLoggedIn () && isset ( $orderId )) {
            /**
             * Load order details.
             */
            $order = Mage::getModel ( "sales/order" )->load ( $orderId );
        } else {
            /**
             * Error message for the when unwanted person access these request.
             *
             * Redirect to view order page.
             */
            Mage::getSingleton ( 'core/session' )->addError ( $this->__ ( "You do not have permission to access this page" ) );
            $this->_redirect ( 'property/product/vieworder/order_id/' . $orderId );
            return;
        }
        
        try {
            /**
             * Check invoice create status.
             */
            if (! $order->canInvoice ()) {
                /**
                 * Set error meesage
                 *
                 * And redirect to view order page.
                 */
                Mage::getSingleton ( 'core/session' )->addError ( $this->__ ( "Cannot create an invoice." ) );
                $this->_redirect ( 'property/product/vieworder/order_id/' . $orderId );
                return;
            }
            /**
             * Create Invoice.
             */
            $invoice = Mage::getModel ( 'sales/service_order', $order )->prepareInvoice ();
            $invoice->setRequestedCaptureCase ( Mage_Sales_Model_Order_Invoice::CAPTURE_ONLINE );
            /**
             * Register invoice.
             */
            $invoice->register ();
            $transactionSave = Mage::getModel ( 'core/resource_transaction' )->addObject ( $invoice )->addObject ( $invoice->getOrder () );
            $transactionSave->save ();
            /**
             * Send invoice email.
             */
            $invoice->getOrder ()->setCustomerNoteNotify ( true );
            $invoice->getOrder ()->setIsInProcess ( true );
            /**
             * Send invoice email.
             */
            $invoice->sendEmail ();
            /**
             * Set Invoice created success message
             * And product view order page.
             */
            Mage::getSingleton ( 'core/session' )->addSuccess ( $this->__ ( "Invoice created sucessfully." ) );
            $this->_redirect ( 'property/product/vieworder/order_id/' . $orderId );
        } catch ( Mage_Core_Exception $e ) {
            /**
             * Set error message.
             * And redirect to view order page.
             */
            Mage::getSingleton ( 'core/session' )->addError ( $this->__ ( "You do not have permission to access this page." ) );
            $this->_redirect ( 'property/product/vieworder/order_id/' . $orderId );
            return;
        }
    }
}