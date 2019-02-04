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
class Apptha_Airhotels_Model_Order extends Mage_Sales_Model_Order {
    /**
     * XML configuration paths
     */
    const XML_PATH_EMAIL_TEMPLATE = 'sales_email/order/template';
    const XML_PATH_EMAIL_GUEST_TEMPLATE = 'sales_email/order/guest_template';
    const XML_PATH_EMAIL_IDENTITY = 'sales_email/order/identity';
    const XML_PATH_EMAIL_COPY_TO = 'sales_email/order/copy_to';
    const XML_PATH_EMAIL_COPY_METHOD = 'sales_email/order/copy_method';
    const XML_PATH_EMAIL_ENABLED = 'sales_email/order/enabled';
    const XML_PATH_UPDATE_EMAIL_TEMPLATE = 'sales_email/order_comment/template';
    const XML_PATH_UPDATE_EMAIL_GUEST_TEMPLATE = 'sales_email/order_comment/guest_template';
    const XML_PATH_UPDATE_EMAIL_IDENTITY = 'sales_email/order_comment/identity';
    const XML_PATH_UPDATE_EMAIL_COPY_TO = 'sales_email/order_comment/copy_to';
    const XML_PATH_UPDATE_EMAIL_COPY_METHOD = 'sales_email/order_comment/copy_method';
    const XML_PATH_UPDATE_EMAIL_ENABLED = 'sales_email/order_comment/enabled';
    const XML_PATH_COUPON_TEMPLATE = 'dealcoupon/email/coupon_template';
    const XML_PATH_OWNER_TEMPLATE = 'dealcoupon/email/owner_template';
    const XML_PATH_NO_EMAIL_TEMPLATE = 'dealcoupon/email/email_template';
    const XML_PATH_EMAIL_RECIPIENT = 'contacts/email/recipient_email';
    const XML_PATH_EMAIL_SENDER = 'airhotels/order_reminder/sender_email_identity';    
    /**
     * Email Update template contus
     */
    const XML_PATH_ORDERSTUTS_TEMPLATE = 'airhotels/order_reminder/orderstatus_template';
    const XML_PATH_CREDITMEMO_TEMPLATE = 'airhotels/order_reminder/creditmemo_template';    
    /**
     * Order states
     */
    const STATE_NEW = 'new';
    const STATE_PENDING_PAYMENT = 'pending_payment';
    const STATE_PROCESSING = 'processing';
    const STATE_COMPLETE = 'complete';
    const STATE_CLOSED = 'closed';
    const STATE_CANCELED = 'canceled';
    const STATE_HOLDED = 'holded';
    const STATE_PAYMENT_REVIEW = 'payment_review';    
    /**
     * Order statuses
     */
    const STATUS_FRAUD = 'fraud';    
    /**
     * Order flags
     */
    const ACTION_FLAG_CANCEL = 'cancel';
    const ACTION_FLAG_HOLD = 'hold';
    const ACTION_FLAG_UNHOLD = 'unhold';
    const ACTION_FLAG_EDIT = 'edit';
    const ACTION_FLAG_CREDITMEMO = 'creditmemo';
    const ACTION_FLAG_INVOICE = 'invoice';
    const ACTION_FLAG_REORDER = 'reorder';
    const ACTION_FLAG_SHIP = 'ship';
    const ACTION_FLAG_COMMENT = 'comment';    
    /**
     * Report date types
     */
    const REPORT_DATE_TYPE_CREATED = 'created';
    const REPORT_DATE_TYPE_UPDATED = 'updated';
    protected $_eventPrefix = 'sales_order';
    protected $_eventObject = 'order';
    protected $_addresses = null;
    protected $_items = null;
    protected $_payments = null;
    protected $_statusHistory = null;
    protected $_invoices;
    protected $_tracks;
    protected $_shipments;
    protected $_creditmemos;
    protected $_relatedObjects = array ();
    protected $_orderCurrency = null;
    protected $_baseCurrency = null;    
    /**
     * Array of action flags for canUnhold, canEdit, etc.
     *
     * @var array
     */
    protected $_actionFlag = array ();    
    /**
     * Flag: if after order placing we can send new email to the customer.
     *
     * @var bool
     */
    protected $_canSendNewEmailFlag = true;    
    /**
     * Initialize resource model
     */
    protected function _construct() {
        $this->_init ( 'sales/order' );
    }    
    /**
     * Retrieve order reorder availability
     *
     * @return bool
     */
    public function canReorder() {
        $returnValue = true;
        if ($this->canUnhold () || $this->isPaymentReview () || ! $this->getCustomerId ()) {
            $returnValue = false;
        }
        $products = array ();
        /**
         * Iterating loop
         */
        foreach ( $this->getItemsCollection () as $item ) {
            $products [] = $item->getProductId ();
        }
        if (! empty ( $products )) {
            /**
             * Iteratng loop
             */
            foreach ( $products as $productId ) {
                $product = Mage::helper ( 'airhotels/product' )->getProductDetailsById ( $productId );
                if (! $product->getId () || ! $product->isSalable ()) {
                    $returnValue = false; 
                }
            }
        }
        if ($this->getActionFlag ( static::ACTION_FLAG_REORDER ) === false) {
            $returnValue = false; 
        }
        return $returnValue;
    }    
    /**
     * Declare order billing address
     *
     * @param Mage_Sales_Model_Order_Address $address            
     * @return Mage_Sales_Model_Order
     */
    public function setBillingAddress(Mage_Sales_Model_Order_Address $address) {
        $old = $this->getBillingAddress ();
        if (! empty ( $old )) {
            $address->setId ( $old->getId () );
        }
        $this->addAddress ( $address->setAddressType ( 'billing' ) );
        return $this;
    }
    /**
     * Order state setter.
     * If status is specified, will add order status history with specified comment
     * the setData() cannot be overriden because of compatibility issues with resource model
     *
     * @param string $state            
     * @param string|bool $status            
     * @param string $comment            
     * @param bool $isCustomerNotified            
     * @return Mage_Sales_Model_Order
     */
    public function setState($state, $status = false, $comment = '', $isCustomerNotified = null) {
        /**
         * Set state
         */
        return $this->_setState ( $state, $status, $comment, $isCustomerNotified, true );
    }    
    /**
     * Order state protected setter.
     * By default allows to set any state. Can also update status to default or specified value
     * Сomplete and closed states are encapsulated intentionally, see the _checkState()
     *
     * @param string $state            
     * @param string|bool $status            
     * @param string $comment            
     * @param bool $isCustomerNotified            
     * @param
     *            $shouldProtectState
     * @return Mage_Sales_Model_Order
     */
    protected function _setState($state, $status = false, $comment = '', $isCustomerNotified = null, $shouldProtectState = false) {
        /**
         * Attempt to set the specified state
         */
        if (($shouldProtectState) && ($this->isStateProtected ( $state ))) {
            Mage::throwException ( Mage::helper ( 'sales' )->__ ( 'The Order State "%s" must not be set manually.', $state ) );
        }        
        $this->setData ( 'state', $state );        
        /**
         * Add status history
         */
        if ($status) {
            if ($status === true) {
                $status = $this->getConfig ()->getStateDefaultStatus ( $state );
            }
            $this->setStatus ( $status );
            $history = $this->addStatusHistoryComment ( $comment, false );
            /**
             * No sense to set $status again
             */
            $history->setIsCustomerNotified ( $isCustomerNotified );
        /**
         * For backwards compatibility
         */
        }        
        /**
         * Returning status from paypal and if status equal to complete then we can send the coupon (voucher) for user
         */
        if ($status == 'complete' || $status == 'processing') {
            $this->_propertyUpdate ( $this->getRealOrderId () );
        }
        return $this;
    }    
    /**
     * Order Status update mail
     */
    public function propertyStatus($orderId, $stat) {
        $propertyStatusSentId = Mage::getSingleton ( 'core/session' )->getPropertyStatusSentOrderIdForChecking ();
        if ($propertyStatusSentId != $orderId) {
            $hostEmail = array ();
            /**
             * Load by order ID
             */
            $value = Mage::getModel ( 'sales/order' )->loadByIncrementId ( $orderId );
            
            /**
             * Buyer Email
             */
            $buyerEmail = $value->getCustomerEmail ();
            /**
             * Get customer first name
             */
            $buyerName = $value->getCustomerFirstname ();
            $collections = Mage::getModel ( 'airhotels/airhotels' )->load ($orderId,'order_id' );            
            /**
             * Get Entity id
             */
            $productId = $collections ['entity_id'];
            $model = Mage::getModel ( 'catalog/product' );
            /**
             * load by product ID
             */
            $product = $model->load ( $productId );
            $hostId = $product->getUserid ();
            /**
             * Load customer data
             */
            $customer = Mage::getModel ( 'customer/customer' )->load ( $hostId );
            /**
             * Get customer email
             */
            $hostEmail [0] = $customer->getEmail ();
            /**
             * Get customer name
             */
            $hostName = $customer->getName ();
            /**
             * Get status
             */
            $status = $this->getStatusLabel ();
            if ($stat) {
                $status = static::STATE_CLOSED;
            }
            $postObject = new Varien_Object ();
            $postObject->setData ( array (
                    'incrementid' => $orderId,
                    'status' => $status,
                    'customername' => $buyerName 
            ) );
            /**
             * Get model of email template
             */
            $mailTemplate = Mage::getModel ( 'core/email_template' );
            $mailTemplate->setTemplateSubject ( 'Order Status' );
            $mailTemplate->setDesignConfig ( array (
                    'area' => 'frontend' 
            ) )->sendTransactional ( Mage::getStoreConfig ( static::XML_PATH_ORDERSTUTS_TEMPLATE ), Mage::getStoreConfig ( static::XML_PATH_EMAIL_SENDER ), $buyerEmail, $buyerName, array (
                    'orderstatus' => $postObject 
            ) );
            $postObject = new Varien_Object ();
            $postObject->setData ( array (
                    'incrementid' => $orderId,
                    'status' => $status,
                    'customername' => $hostName 
            ) );
            $mailTemplate = Mage::getModel ( 'core/email_template' );
            $mailTemplate->setTemplateSubject ( 'Order Status' );
            $mailTemplate->setDesignConfig ( array (
                    'area' => 'frontend' 
            ) )->sendTransactional ( Mage::getStoreConfig ( static::XML_PATH_ORDERSTUTS_TEMPLATE ), Mage::getStoreConfig ( static::XML_PATH_EMAIL_SENDER ), $hostEmail, $hostName, array (
                    'orderstatus' => $postObject 
            ) );
            /**
             * Check status closed or not.
             */
            if ($status == static::STATE_CLOSED) {
                Mage::getSingleton ( 'core/session' )->setPropertyStatusSentOrderIdForChecking ( $orderId );
            }
        }
    }    
    /**
     * Whether specified state can be set from outside
     *
     * @param  $state
     * @return bool
     */
    public function isStateProtected($state) {
        if (empty ( $state )) {
            return false;
        }
        return static::STATE_COMPLETE == $state || static::STATE_CLOSED == $state;
    }    
    /**
     * Retrieve label of order status
     *
     * @return string
     */
    public function getStatusLabel() {
        return $this->getConfig ()->getStatusLabel ( $this->getStatus () );
    }    
    /**
     * Add status change information to history
     *
     * @deprecated after 1.4.0.0-alpha3
     *            
     * @param string $status            
     * @param string $comment            
     * @param bool $isCustomerNotified            
     * @return Mage_Sales_Model_Order
     */
    public function addStatusToHistory($status, $comment = '', $isCustomerNotified = false) {
        $this->addStatusHistoryComment ( $comment, $status )->setIsCustomerNotified ( $isCustomerNotified );
        return $this;
    }    
    /**
     * Add a comment to order Different or default status may be specified @param string $comment @param string $status @return Mage_Sales_Order_Status_History
     */
    public function addStatusHistoryComment($comment, $status = false) {
        if (false === $status) {
            $status = $this->getStatus ();
        } elseif (true === $status) {
            $status = $this->getConfig ()->getStateDefaultStatus ( $this->getState () );
        } else {
            $this->setStatus ( $status );
        }
        $history = Mage::getModel ( 'sales/order_status_history' )->setStatus ( $status )->setComment ( $comment );
        $this->addStatusHistory ( $history );
        return $history;
    }
    /**
     * Function sendNewOrderEmail
     * (non-PHPdoc)
     *
     * @see Mage_Sales_Model_Order::sendNewOrderEmail()
     */
    public function sendNewOrderEmail() {
        $this->queueNewOrderEmail ( true );
        return $this;
    }
    /**
     * Function queueNewOrderEmail
     *
     * {@inheritDoc}
     *
     * @see Mage_Sales_Model_Order::queueNewOrderEmail()
     */
    public function queueNewOrderEmail() {
        $storeId = $this->getStore ()->getId ();
        if (! Mage::helper ( 'sales' )->canSendNewOrderEmail ( $storeId )) {
            return $this;
        }
        /**
         * Get the destination email addresses to send copies to
         */
        $this->_getEmails ( static::XML_PATH_EMAIL_COPY_TO );
        Mage::getStoreConfig ( static::XML_PATH_EMAIL_COPY_METHOD, $storeId );
        /**
         * Start store emulation process
         */
        $appEmulation = Mage::getSingleton ( 'core/app_emulation' );
        $initialEnvironmentInfo = $appEmulation->startEnvironmentEmulation ( $storeId );
        try {
            /**
             * Retrieve specified view block from appropriate design package (depends on emulated store)
             */
            $paymentBlock = Mage::helper ( 'payment' )->getInfoBlock ( $this->getPayment () )->setIsSecureMode ( true );
            $paymentBlock->getMethod ()->setStore ( $storeId );
            $paymentBlockHtml = $paymentBlock->toHtml ();
        } catch ( Exception $exception ) {
            $appEmulation->stopEnvironmentEmulation ( $initialEnvironmentInfo );
            throw $exception;
        }
        $appEmulation->stopEnvironmentEmulation ( $initialEnvironmentInfo );
        /**
         * Retrieve corresponding email template id and customer name
         */
        if ($this->getCustomerIsGuest ()) {
            $templateId = Mage::getStoreConfig ( static::XML_PATH_EMAIL_GUEST_TEMPLATE, $storeId );
            $customerName = $this->getBillingAddress ()->getName ();
        } else {
            $templateId = Mage::getStoreConfig ( static::XML_PATH_EMAIL_TEMPLATE, $storeId );
            $customerName = $this->getCustomerName ();
        }
        $productId = Mage::getSingleton ( 'core/session' )->getProductID ();
        $_product = Mage::getModel ( 'catalog/product' )->load ( $productId );
        $spaceName = $_product->getName ();
        $hostId = $_product->getUserid ();
        $host = Mage::getModel ( 'customer/customer' )->load ( $hostId );
        $hostEmail = $host->getEmail ();
        $hostName = $host->getName ();
        $customer = Mage::getSingleton ( 'customer/session' )->getCustomer ();
        $customerEmail = $customer->getEmail ();
        $cusomerName = $customer->getName ();
        $AdminEmail = Mage::getStoreConfig ( 'trans_email/ident_general/email' );
        $AdminName = Mage::getStoreConfig ( 'trans_email/ident_general/name' );
        $storeName = Mage::app ()->getStore ()->getGroup ()->getName ();
        $template_id = ( int ) Mage::getStoreConfig ( 'airhotels/host_notification/neworderguest_template' );
        if ($template_id) {
            $emailTemplate = Mage::getModel ( 'core/email_template' )->load ( $template_id );
        } else {
            $emailTemplate = Mage::getModel ( 'core/email_template' )->loadDefault ( 'airhotels_host_notification_neworderguest_template' );
        }
        $emailTemplate->setSenderName ( $AdminName );
        $emailTemplate->setSenderEmail ( $AdminEmail );
        $emailTemplateVariables = (array (
                'order' => $this,
                'billing' => $this->getBillingAddress (),
                'payment_html' => $paymentBlockHtml,
                'cutomername' => $cusomerName,
                'spacename' => $spaceName,
                'storename' => $storeName 
        ));
        $emailTemplate->setDesignConfig ( array (
                'area' => 'frontend' 
        ) );
        $emailTemplate->getProcessedTemplate ( $emailTemplateVariables );
        $emailTemplate->send ( $customerEmail, $cusomerName, $emailTemplateVariables );
        $template_id_host = ( int ) Mage::getStoreConfig ( 'airhotels/host_notification/neworder_template' );
        if ($template_id_host) {
            $emailTemplateHost = Mage::getModel ( 'core/email_template' )->load ( $template_id_host );
        } else {
            $emailTemplateHost = Mage::getModel ( 'core/email_template' )->loadDefault ( 'airhotels_host_notification_neworder_template' );
        }
        $emailTemplateHost->setSenderName ( $AdminName );
        $emailTemplateHost->setSenderEmail ( $AdminEmail );
        $emailTemplateVariablesHost = (array (
                'order' => $this,
                'billing' => $this->getBillingAddress (),
                'payment_html' => $paymentBlockHtml,
                'hostname' => $hostName,
                'spacename' => $spaceName,
                'storename' => $storeName 
        ));
        $emailTemplateHost->setDesignConfig ( array (
                'area' => 'frontend' 
        ) );
        $emailTemplateHost->getProcessedTemplate ( $emailTemplateVariablesHost );
        $emailTemplateHost->send ( $hostEmail, $hostName, $emailTemplateVariablesHost );
        $this->setEmailSent ( true );
        $this->_getResource ()->saveAttribute ( $this, 'email_sent' );
        return $this;
    }
    /**
     * Property Update
     * 
     * @param ont $orderId            
     * @return boolean
     */
    public function _propertyUpdate($orderId) {
        /**
         * Get the creditmemo Value
         */
        $reqpost = Mage::app ()->getRequest ()->getPost ( 'creditmemo' );
        $stat = '';
        /**
         * Get Colletion for airhotels
         */
        $collections = Mage::getModel ( 'airhotels/airhotels' )->getCollection ()->addFieldToFilter ( 'order_id', $orderId );
        if (count ( $collections ) > 0) {
            foreach ( $collections as $collection ) {
                $propertyPropertyId = $collection ['id'];
                break;
            }
        }
        if (! empty ( $reqpost )) {
            if (! empty ( $propertyPropertyId )) {
                $data = array (
                        'order_status' => 0 
                );
                /**
                 * Get Model Value
                 */
                $model = Mage::getModel ( 'airhotels/airhotels' )->load ( $propertyPropertyId )->addData ( $data );
                try {
                    $model->setId ( $propertyPropertyId )->save ();
                } catch ( Exception $e ) {
                    Mage::getSingleton ( 'adminhtml/session' )->addError ( $e );
                    return false;
                }
            }
            $stat = 1;
        } else {
            if (! empty ( $propertyPropertyId )) {
                $data = array (
                        'order_status' => 1 
                );
                $model = Mage::getModel ( 'airhotels/airhotels' )->load ( $propertyPropertyId )->addData ( $data );
                try {
                    $model->setId ( $propertyPropertyId )->save ();
                } catch ( Exception $e ) {
                    Mage::getSingleton ( 'adminhtml/session' )->addError ( $e );
                    return false;
                }
            }
        }
        $this->propertyStatus ( $orderId, $stat );
    }
    /**
     * Getting request status for customer order item
     *
     * @param number $itemProductId            
     * @param number $orderId            
     * @param number $loggedInCustomerId            
     * @param number $value            
     * @return boolean $status
     */
    public function getItemRequestStatus($itemProductId, $orderId, $loggedInCustomerId, $value) {
        /**
         * Load commission model
         */
        $products = Mage::getModel ( 'airhotels/airhotels' )->getCollection ();
        $products->addFieldToSelect ( '*' );
        /**
         * Filter by order id and product id
         */
        $products->addFieldToFilter ( 'order_item_id', $orderId );
        $products->addFieldToFilter ( 'entity_id', $itemProductId );
        /**
         * Checking for value
         */
        $statusFlagValue = 0;
        if ($value == 4) {
            $status = $products->getFirstItem ()->getRefundRequestSeller ();
        } elseif ($value == 3) {
            $status = $products->getFirstItem ()->getCancelRequestCustomer ();
        } elseif ($value == 1 || $value == 2) {
            $status = $products->getFirstItem ()->getRefundRequestCustomer ();
        } else {
            $statusFlagValue = 1;
        }
        if ($statusFlagValue == 1) {
            $status = $products->getFirstItem ()->getCancelRequestCustomer ();
        }        
        /**
         * Return status
         */
        return $status;
    }
    public function updateSellerRequest($orderId, $value) {        
        /**
         * Checking for product id , order id and customer d
         */
        if (! empty ( $orderId )) {
            /**
             * Get product from commission model
             */
            $products = Mage::getModel ( 'airhotels/airhotels' )->getCollection ();
            $products->addFieldToSelect ( '*' );
            $products->addFieldToFilter ( 'order_item_id', $orderId );
            /**
             * Getting first data id
             */
            $collectionId = $products->getFirstItem ()->getId ();
            /**
             * Checking for first data exist or not
             */
            if (! empty ( $collectionId )) {
                /**
                 * Get cancel_request_status.
                 */
                $data = array ();
                $data = array (
                        'cancel_request_status' => $value 
                );                
                $model = Mage::getModel ( 'airhotels/airhotels' )->load ( $collectionId )->addData ( $data )->save ();
                
                if ($value == 2 || $value == 3) {
                    $model->setOrderStatus ( '2' )->save ();
                }
            }
        }
    }
    /**
     * Function name: orderUpdateEmail
     * @param unknown $incrementId
     * @param unknown $orderStatus
     */
    public function orderUpdateEmail($incrementId, $orderStatus) {
        $hostEmail = $result = array ();
        /**
         * Load by order ID
         */
        $value = Mage::getModel ( 'sales/order' )->load ( $incrementId );        
        $incrementId = $value->getIncrementId ();        
        $collections = Mage::getModel ( 'airhotels/airhotels' )->getCollection ()->addFieldToFilter ( 'order_id', $incrementId );
        /**
         * Buyer Email
         */
        $buyerEmail = $value->getCustomerEmail ();
        /**
         * Get customer first name
         */
        $buyerName = $value->getCustomerFirstname ();
        /**
         * getting values from array using foreach
         */
        foreach ( $collections as $collection ) {
            $result = $collection;
            break;
        }
        /**
         * Get Entity id
         */
        $model = Mage::getModel ( 'catalog/product' );
        /**
         * load by product ID
         */
        $productId = $result ['entity_id'];
        $product = $model->load ( $productId );
        $hostId = $product->getUserid ();
        $orderStatusTemplete = Mage::getStoreConfig ( static::XML_PATH_ORDERSTUTS_TEMPLATE );
        if ($orderStatus == 'cancel') {
            /**
             * Set order status as cancelled.
             */
            $orderStatus = static::STATE_CANCELED;
        } elseif ($orderStatus == 'creditmemo') {
            /**
             * Set order status as closed.
             */
            $orderStatus = static::STATE_CLOSED;
            $orderStatusTemplete = Mage::getModel ( 'core/email_template' )->loadDefault ( 'airhotels_order_reminder_creditmemo_template' );
        }
        /**
         * Load customer data
         */
        $customer = Mage::getModel ( 'customer/customer' )->load ( $hostId );
        /**
         * Get customer name
         */
        $hostName = $customer->getName ();
        /**
         * Get customer email
         */
        $hostEmail [0] = $customer->getEmail ();
        /**
         * Get status
         */
        $postObject = new Varien_Object ();
        $postObject->setData ( array (
                'incrementid' => $incrementId,
                'status' => $orderStatus,
                'customername' => $buyerName 
        ) );
        /**
         * Get model of email template
         */
        $mailTemplate = Mage::getModel ( 'core/email_template' );
        $mailTemplate->setTemplateSubject ( 'Order Status' );
        if ($orderStatus == 'closed') {
            /**
             * Get admin email id
             * Get from email id
             * Get from email name.
             */
            $adminEmailId = Mage::getStoreConfig ( 'airhotels/custom_email/admin_email_id' );
            $fromMailId = Mage::getStoreConfig ( "trans_email/ident_$adminEmailId/email" );
            $fromName = Mage::getStoreConfig ( "trans_email/ident_$adminEmailId/name" );
            $orderStatusTemplete->setSenderName ( $fromName );
            /**
             * mail sender email id
             */
            $orderStatusTemplete->setSenderEmail ( $fromMailId );
            $orderStatusTemplete->setDesignConfig ( array (
                    'area' => 'frontend' 
            ) );
            /**
             * Send email
             */
            $orderStatusTemplete->send ( $buyerEmail, $fromName, $postObject->getData () );
        } else {
            $mailTemplate->setDesignConfig ( array (
                    'area' => 'frontend' 
            ) )->sendTransactional ( $orderStatusTemplete, Mage::getStoreConfig ( static::XML_PATH_EMAIL_SENDER ), $buyerEmail, $buyerName, array (
                    'orderstatus' => $postObject 
            ) );
        }
        $postObject = new Varien_Object ();
        $postObject->setData ( array (
                'incrementid' => $incrementId,
                'status' => $orderStatus,
                'customername' => $hostName 
        ) );
        $mailTemplate = Mage::getModel ( 'core/email_template' );
        $mailTemplate->setTemplateSubject ( 'Order Status' );
        $mailTemplate->setDesignConfig ( array (
                'area' => 'frontend' 
        ) )->sendTransactional ( Mage::getStoreConfig ( static::XML_PATH_ORDERSTUTS_TEMPLATE ), Mage::getStoreConfig ( static::XML_PATH_EMAIL_SENDER ), $hostEmail, $hostName, array (
                'orderstatus' => $postObject 
        ) );
    }
    /**
     * Function Name: getCartInfo
     *
     * return info
     */
    public function getCartInfo($params) {
        $propertyServiceFrom = $params ['property_service_from'];
        $propertyServiceFromPeriod = $params ['property_service_from_period'];
        $propertyServiceTo = $params ['property_service_to'];
        $propertyServiceToPeriod = $params ['property_service_to_period'];
        $overallTotalHours = $params ['overall_total_hours'];
        $hourlyNightFee = $params ['hourly_night_fee'];
        Mage::getSingleton ( 'core/session' )->setHourlyFromTime ( $propertyServiceFrom );
        Mage::getSingleton ( 'core/session' )->setHourlyFormPeriod ( $propertyServiceFromPeriod );
        Mage::getSingleton ( 'core/session' )->setHourlyToTime ( $propertyServiceTo );
        Mage::getSingleton ( 'core/session' )->setHourlyToPeriod ( $propertyServiceToPeriod );
        Mage::getSingleton ( 'core/session' )->setOverallTotalHours ( $overallTotalHours );
        Mage::getSingleton ( 'core/session' )->setHourlyNightFee ( $hourlyNightFee );
        $propertyServiceFromTime = $propertyServiceFrom . ':' . $propertyServiceFromPeriod;
        $propertyServiceToTime = $propertyServiceTo . ':' . $propertyServiceToPeriod;
        Mage::getSingleton ( 'core/session' )->setPropertyServiceForm ( $propertyServiceFromTime );
        Mage::getSingleton ( 'core/session' )->setPropertyServiceTo ( $propertyServiceToTime );
    }
    /**
     * Function Name: getSubscriptionInfo
     *
     * Save values to session
     */
    public function getSubscriptionInfo($subId) {
        /**
         * Get the Period Values
         */
        $collections = Mage::getModel ( 'airhotels/subscriptiontype' )->load ( $subId, 'id' );
        /**
         * Billing Informations
         */
        $subscriptionValue = $collections ['title'];
        Mage::getSingleton ( 'core/session' )->setsubscriptionValue ( $subscriptionValue );
        /**
         * remove session data when we add product to cart existing product will be removed
         *
         * @var unknown
         */
        $cartItems = Mage::helper ( 'checkout/cart' )->getCart ()->getItemsCount ();
        if ($cartItems >= 1) {
            $cart = Mage::getModel ( 'checkout/cart' );
            $cart->truncate ();
            Mage::getSingleton ( 'checkout/session' )->clear ();
        }
    /**
     * Setting session value for cart.
     */
    }
}
?>