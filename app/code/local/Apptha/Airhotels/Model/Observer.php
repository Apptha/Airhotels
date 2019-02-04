<?php
/**
 * Apptha
 * NOTICE OF LICENSE
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.apptha.com/LICENSE.txt
 * ==============================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * ==============================================================
 * This package designed for Magento COMMUNITY edition
 * Apptha does not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * Apptha does not provide extension support in case of
 * incorrect edition usage.
 * ==============================================================
 * @category    Apptha
 * @package     Apptha_Airhotels
 * @version     0.2.9
 * @author      Apptha Team <developers@contus.in>
 * @copyright   Copyright (c) 2014 Apptha. (http://www.apptha.com)
 * @license     http://www.apptha.com/LICENSE.txt
 * 
 */
class Apptha_Airhotels_Model_Observer {
    /**
     * Check airhotels Property
     */
    public function property() {
        /**
         * check airhotels enabled
         */
        $enableAirhotels = Mage::getStoreConfig ( 'airhotels/custom_group/enable_airhotels' );
        $isRecurring = Mage::getSingleton ( 'core/session' )->getSubId ();
        if ($isRecurring != 0) {
            $isRecurring = 1;
        }
        if (empty ( $enableAirhotels )) {
            $fromdate = Mage::getSingleton ( 'core/session' )->getFromdate ();
            $todate = Mage::getSingleton ( 'core/session' )->getTodate ();
            if (Mage::helper ( 'airhotels/product' )->getHourlyEnabledOrNot () != 0) {
                $todateVal = Mage::getSingleton ( 'core/session' )->getTodate ();
                $dateArr = explode ( "-", $todateVal );
                $todate = date ( 'Y-m-d', mktime ( 0, 0, 0, $dateArr [1], $dateArr [2] - 1, $dateArr [0] ) );
            }
            $propertyFormTime = Mage::getSingleton ( 'core/session' )->getPropertyServiceForm ();
            $propertyToTime = Mage::getSingleton ( 'core/session' )->getPropertyServiceTo ();
            if (! empty ( $propertyFormTime ) && ! empty ( $propertyToTime )) {
                $fromTimeArray = explode ( ':', $propertyFormTime );
                $propertyServiceFrom = $fromTimeArray [0];
                $propertyServiceFromPeriod = $fromTimeArray [1];
                $toTimeArray = explode ( ':', $propertyToTime );
                $propertyServiceTo = $toTimeArray [0];
                $propertyServiceToPeriod = $toTimeArray [1];
                $propertyServiceFromRail = $this->getPropertyServiceFromRail ( $propertyServiceFromPeriod, $propertyServiceFrom );
                $propertyServiceToRail = $this->getPropertyServiceToRail ( $propertyServiceToPeriod, $propertyServiceTo );
                $fromArray = explode ( '-', $fromdate );
                $checkinTime = mktime ( $propertyServiceFromRail, 0, 0, $fromArray [1], $fromArray [2], $fromArray [0] );
                $checkinTimeValue = date ( 'Y-m-d H:i:s', $checkinTime );
                $toArray = explode ( '-', $todate );
                $checkoutTime = mktime ( $propertyServiceToRail, 0, 0, $toArray [1], $toArray [2], $toArray [0] );
                $checkoutTimeValue = date ( 'Y-m-d H:i:s', $checkoutTime );
            }
            if ($fromdate && $todate && $fromdate != '' && $todate != '') {
                $accomodate = Mage::getSingleton ( 'core/session' )->getAccomodate ();
                $subtotal = Mage::getSingleton ( 'core/session' )->getSubtotal ();
                $session = Mage::getSingleton ( 'checkout/session' );
                $productId = "";
                $orders = Mage::getModel ( 'sales/order' )->getCollection ()->setOrder ( 'created_at', 'DESC' )->setPageSize ( 1 )->setCurPage ( 1 );
                foreach ( $orders as $order ) {
                    $orderId = $order->getIncrementId ();
                    $orderItemId = $order->getEntityId ();
                    $baseCurrency = $order->getBaseCurrencyCode ();
                    $orderCurrency = $order->getOrderCurrencyCode ();
                    $grandTotal = round ( $order->getGrandTotal (), 2 );
                    break;
                }
                foreach ( $session->getQuote ()->getAllItems () as $item ) {
                    $productId = $item->getProductId ();
                    break;
                }
                if (! empty ( $productId )) {
                    $productData = Mage::getModel ( 'catalog/product' )->load ( $productId );
                    $productName = $productData->getName ();
                    $hostId      = $productData->getUserid();
                }
                $customer = Mage::getSingleton ( 'customer/session' )->getCustomer ();
                $cusId = $customer->getId ();
                $buyerEmail = $customer->getEmail ();
                $config = Mage::getStoreConfig ( 'airhotels/custom_group' );
                $serviceFeeData = ($subtotal / 100) * ($config ["airhotels_servicetax"]);
                $serviceFee = number_format ( $serviceFeeData, 2, '.', '' );
                $hostFeeData = ($subtotal / 100) * ($config ["airhotels_hostfee"]);
                $hostFee = number_format ( $hostFeeData, 2, '.', '' );
                if (isset ( $checkinTimeValue ) && isset ( $checkoutTimeValue )) {
                    $data = array (
                            'entity_id' => $productId,
                            'product_name' => $productName,
                            'customer_id' => $cusId,
                            'host_id' => $hostId,
                            'customer_email' => $buyerEmail,
                            'fromdate' => $fromdate,
                            'todate' => $todate,
                            'checkin_time' => $checkinTimeValue,
                            'checkout_time' => $checkoutTimeValue,
                            'accomodates' => $accomodate,
                            'host_fee' => $hostFee,
                            'service_fee' => $serviceFee,
                            'order_id' => $orderId,
                            'base_currency_code' => $baseCurrency,
                            'order_currency_code' => $orderCurrency,
                            'order_item_id' => $orderItemId,
                            'cancel_order_status' => 0,
                            'subtotal' => $subtotal,
                            'grand_total' => $grandTotal                            
                    );
                    $model = Mage::getModel ( 'airhotels/airhotels' )->setData ( $data );
                    try {
                        $model->save ()->getId ();
                    } catch ( Exception $e ) {
                        Mage::getSingleton ( 'checkout/session' )->addError ( $ex );
                        $url = Mage::getBaseUrl ();
                        Mage::app ()->getResponse ()->setRedirect ( $url );
                        return false;
                    }
                } else {
                    $data = array (
                            'entity_id' => $productId,
                            'product_name' => $productName,
                            'customer_id' => $cusId,
                            'host_id' => $hostId,
                            'fromdate' => $fromdate,
                            'todate' => $todate,
                            'customer_email' => $buyerEmail,
                            'accomodates' => $accomodate,
                            'service_fee' => $serviceFee,
                            'order_id' => $orderId,
                            'base_currency_code' => $baseCurrency,
                            'host_fee' => $hostFee,
                            'subtotal' => $subtotal,
                            'grand_total' => $grandTotal,
                            'order_currency_code' => $orderCurrency,
                            'cancel_order_status' => 0,
                            'order_item_id' => $orderItemId,
                            'cancel_order_status' => 0 
                    );
                    $modelSaveCollection = Mage::getModel ( 'airhotels/airhotels' )->setData ( $data );
                    try {
                        /**
                         * Save model collection
                         */
                        $modelSaveCollection->save ()->getId ();
                    } catch ( Exception $e ) {
                        /**
                         * Redirect to base Url
                         * Set error message to session
                         */
                        Mage::getSingleton ( 'checkout/session' )->addError ( $ex );
                        $redirectUrl = Mage::getBaseUrl ();
                        Mage::app ()->getResponse ()->setRedirect ( $redirectUrl );
                        return false;
                    }
                }
                Mage::getSingleton ( 'core/session' )->setProductID ( $productId );
            }
        }
        $inviteeDiscountAmount = Mage::getSingleton ( "core/session" )->getCurrentCustomerDiscountedAmount ();
        if ($inviteeDiscountAmount > 0) {
            Mage::getModel ( 'airhotels/invitefriends' )->updateInviteeTransactionDiscountDetails ( $orderItemId );
        }
    }
    /**
     * Function Name: catalog_product_save_before
     * Catalog product save before evebt
     * @param object $observer            
     * @return boolean
     */
    public function catalog_product_save_before($observer) {
        /**
         * Get the product from the catalog_product_save_before Event
         */
        $product = $observer->getProduct ();
        return Mage::getModel('airhotels/search')->catalogProductSaveBefore(Mage::app ()->getRequest ()->getParam ( 'type' ),$product);
       }
    /**
     * Function Name: 'customerlogout'
     * customer logout
     * @return boolean
     */
    public function customerlogout() {
        return Mage::getModel('airhotels/search')->clearCart();
      }
    /**
     * Function Name: 'adminProductSave'
     * Email Notification to host if admin approve a property
     * @param array $observer            
     * @return bool
     */
    public function adminProductSave($observer) {
        /**
         * Getting the Property approval value
         */
        $propertyApproval = Mage::getStoreConfig ( 'airhotels/custom_email/property_approval' );
        /**
         * Check whether the propertyApproval is not empty
         */
        /**
         * Getting Product Details
         */
        $product = $observer->getProduct ();
        $propertyApproved = $product ['propertyapproved'];
        /**
         * Loading Product details
         */
        $propertyUnapproval = Mage::getModel ( 'catalog/product' )->load ( $product->getId () )->getPropertyapproved ();
        $adminEmailId = Mage::getStoreConfig ( 'airhotels/custom_email/admin_email_id' );
        $fromMailId = Mage::getStoreConfig ( "trans_email/ident_$adminEmailId/email" );
        $fromName = Mage::getStoreConfig ( "trans_email/ident_$adminEmailId/name" );
        $property = Mage::getModel ( 'catalog/product' )->load ( $product->getId () );
        $propertyName = $property->getName ();
        $productUrl = $property->getProductUrl ();
        $userId = $property->getUserid ();
        $customer = Mage::getModel ( 'customer/customer' )->load ( $userId );
        $recipient = $customer->getEmail ();
        $customerName = $customer->getName ();
        if ($propertyApproval && $propertyApproved && empty ( $propertyUnapproval )) {                
                /**
                 * Getting the Property templeId value
                 */
                $templateId = ( int ) Mage::getStoreConfig ( 'airhotels/custom_email/adminapproval_template' );
                
                if ($templateId) {
                    /**
                     * if it is user template then this process is continue
                     */
                    $emailTemplateApproved = Mage::getModel ( 'core/email_template' )->load ( $templateId );
                } else {                   
                    $emailTemplateApproved = Mage::getModel ( 'core/email_template' )->loadDefault ( 'airhotels_custom_email_adminapproval_template' );
                }    
                $emailTemplateApproved->setSenderName ( $fromName );
                $emailTemplateApproved->setSenderEmail ( $fromMailId );
                $emailTemplateVariables = (array (
                        'ownername' => $fromName,
                        'pname' => $propertyName,
                        'purl' => $productUrl,
                        'cname' => $customerName
                ));
                $emailTemplateApproved->setDesignConfig ( array (
                        'area' => 'frontend'
                ) );
                /**
                 * send mail to customer email ids
                 */
                $emailTemplateApproved->send ( $recipient, $fromName, $emailTemplateVariables );
            }           
       
        /**
         * email template for disapproved property
         */
        if (empty($propertyApproved) && $propertyUnapproval) {
            $templateId = ( int ) Mage::getStoreConfig ( 'airhotels/custom_email/admindisapproval_template' );
            if ($templateId) {
                $emailTemplate = Mage::getModel ( 'core/email_template' )->load ( $templateId );
            } else {
                $emailTemplate = Mage::getModel ( 'core/email_template' )->loadDefault ( 'airhotels_custom_email_admindisapproval_template' );
            }
            /**
             * email template for disapproved property
             */
            $emailTemplate->setSenderName ( $fromName );
            $emailTemplate->setSenderEmail ( $fromMailId );
            $emailTemplateVariables = (array (
                    'ownername' => $fromName,'pname' => $propertyName,'purl' => $productUrl,'cname' => $customerName
            ));
            /**
             * email template for disapproved property
             */
            $emailTemplate->setDesignConfig ( array ('area' => 'frontend') );
            /**
             * send mail to customer email ids
             */
            $emailTemplate->send ( $recipient, $fromName, $emailTemplateVariables );
        }
    }
    /**
     * Function Name: 'creditMemoEvent'
     * credit memo Event
     * @param Varien_Event_Observer $observer            
     * @return boolean
     */
    public function creditMemoEvent(Varien_Event_Observer $observer) {
        /**
         * Set the value from the observer
         */
        $creditmemo = $observer->getEvent ()->getCreditmemo ();
        $orderItemId = $creditmemo->getOrderId ();
        /**
         * Get the collection of airhotels
         */
        $collections = Mage::getModel ( 'airhotels/airhotels' )->getCollection ()->/**
         * Filter by order item id
         */
        addFieldToFilter ( 'order_item_id', $orderItemId );
        /**
         * Check whether the colletions is greater than zero
         */
        if (count ( $collections ) > 0) {
            /**
             * Iterating the loop
             */
            foreach ( $collections as $collection ) {
                $propertyPropertyId = $collection ['id'];
                break;
            }
        }
        /**
         * Check whether the PropertyId is not an empty Value
         */
        if (! empty ( $propertyPropertyId )) {
            $data = array (
                    'order_status' => 0 
            );
            $model = Mage::getModel ( 'airhotels/airhotels' )->load ( $propertyPropertyId )->addData ( $data );
            try {
                /**
                 * save data (Proeprty id)
                 */
                $model->setId ( $propertyPropertyId )->save ();
            } catch ( Exception $e ) {
                Mage::getSingleton ( 'adminhtml/session' )->addError ( $e );
                return false;
            }
        }
        Mage::getModel ( 'airhotels/invitefriends' )->cancelInviteeTransactionDiscountDetails ( $orderItemId );
    }
    /**
     * Function Name: 'addApprovedToProductGrid'
     * Adding approved product grid
     * @param Varien_Event_Observer $observer            
     */
    public function addApprovedToProductGrid(Varien_Event_Observer $observer) {
        $block = $observer->getEvent ()->getBlock ();
        if (($block instanceof Mage_Adminhtml_Block_Catalog_Product_Grid)) {
            /**
             * Add column for 'propertyapproved'
             */
            $block->addColumnAfter ( 'propertyapproved', array (
                    'header' => 'Approved',
                    'entity' => 'entity_id',
                    'index' => 'propertyapproved',
                    'sortable' => false,
                    'width' => '50px',
                    'type' => 'options',
                    'renderer' => 'Apptha_Airhotels_Block_Adminhtml_Host_Approved',
                    'options' => Mage::getModel ( 'airhotels/source_approved' )->toOptionArray () 
            ), 'status' );
        }
    }
    /**
     * Function Name: 'adminProductDelete'
     * admin product Delete
     * @param unknown $observer            
     */
    public function adminProductDelete($observer) {
        $property = $observer->getProduct ();
        /**
         * Set the values to admin_email_id,fromMailId,fromName,templeId
         */
        $adminEmailId = Mage::getStoreConfig ( 'airhotels/custom_email/admin_email_id' );
        $fromMailId = Mage::getStoreConfig ( "trans_email/ident_$adminEmailId/email" );
        $fromName = Mage::getStoreConfig ( "trans_email/ident_$adminEmailId/name" );
        $templateId = ( int ) Mage::getStoreConfig ( 'airhotels/custom_email/adminpropertydelete_template' );
        /**
         * if it is user template then this process is continue
         */
        if ($templateId) {
            $emailTemplate = Mage::getModel ( 'core/email_template' )->load ( $templateId );
        } else {
            /**
             * we are calling default template
             */
            $emailTemplate = Mage::getModel ( 'core/email_template' )->loadDefault ( 'airhotels_custom_email_adminpropertydelete_template' );
        }
        /**
         * Getting name
         */
        $propertyName = $property->getName ();
        $userId = $property->getUserid ();
        $customer = Mage::getModel ( 'customer/customer' )->load ( $userId );
        /**
         * Property Email Owner
         */
        $recipient = $customer->getEmail ();
        /**
         * Property Email Owner
         */
        $customerName = $customer->getName ();
        /**
         * mail sender name
         */
        $emailTemplate->setSenderName ( $fromName );
        /**
         * mail sender email id
         */
        $emailTemplate->setSenderEmail ( $fromMailId );
        $emailTemplateVariables = (array (
                'ownername' => $fromName,
                'pname' => $propertyName,
                'cname' => $customerName 
        ));
        $emailTemplate->setDesignConfig ( array (
                'area' => 'frontend' 
        ) );
        /**
         * it return the temp body
         */
        $emailTemplate->getProcessedTemplate ( $emailTemplateVariables );
        if (! empty ( $recipient )) {
            /**
             * send mail to customer email ids
             */
            $emailTemplate->send ( $recipient, $fromName, $emailTemplateVariables );
        }
    }
    /**
     * Function Name: syncWithGoogleCalendar
     * Synchronize with google calendar
     */
    public function syncWithGoogleCalendar() {
        /**
         * check weather the ics is enabled or not
         */
        $icsFileEnable = ( int ) Mage::getStoreConfig ( 'airhotels/ical/enable' );
        if ($icsFileEnable == 0) {
            /**
             * create the product collection
             */
            $products = Mage::getModel ( 'catalog/product' )->getCollection ()->addAttributeToSelect ( '*' );
            /**
             * Iterating the loop
             */
            foreach ( $products as $product ) {
                $icsUrl = $product->getGoogleCalendarIcsUrl ();
                $url = trim ( $icsUrl );
                $autoIcsSync = $product->getAttributeText ( 'auto_ics_sync' );
                $enable = trim ( $autoIcsSync );
                if (! empty ( $url ) && $enable == 'Yes') {
                    /**
                     * Getting Product Id
                     */
                    $productId = $product->getId ();
                    $icalString = Mage::getModel ( 'airhotels/calendarsync' )->readIcsUrl ( $url );
                    $icsDates = Mage::getModel ( 'airhotels/calendarsync' )->convertIcsStringToArray ( $icalString );
                    Mage::getModel ( 'airhotels/calendarsync' )->importFromGoogleIcsUrl ( $icalString, $icsDates, $productId );
                }
            }
        }
    }
    /**
     * Change status to disable for deleted host property.
     * @param unknown $observer            
     */
    public function customerdelete($observer) {
        $customer = $observer->getCustomer ();
        /**
         * create the product collection
         */
      Mage::getModel('airhotels/search')->productDisableStatus($customer->getId());
    }
    /**
     * Function Name: getProductName
     * Get Product Name
     * @param int $productId            
     * @return unknown
     */
    public function getProductName($productId) {
        /**
         * Product Id not empty
         */
        if (! empty ( $productId )) {
            /**
             * load the productId for productData
             */
            $productData = Mage::getModel ( 'catalog/product' )->load ( $productId );
            $productName = $productData->getName ();
        }
        /**
         * Returning the productName
         */
        return $productName;
    }
    /**
     * Store invite friends details
     */
    public function customerRegisterSuccess(Varien_Event_Observer $observer) {
        $customer = $observer->getCustomer ();
        $customerId = $customer->getId ();
        $customerEmail = $customer->getEmail ();
        $data = array (
                'customer_id' => $customerId,
                'email_id' => $customerEmail 
        );
        $model = Mage::getModel ( 'airhotels/customerphoto' )->setData ( $data );
        try {
            $model->save ()->getId ();
        } catch ( Exception $e ) {
            echo $e->getMessage ();
        }
        $customer = $observer->getCustomer ()->getData ();
        $ref = Mage::getModel ( 'core/cookie' )->get ( 'ref' );
        if (Mage::helper ( 'airhotels/invitefriends' )->getInviteFriendsEnabledOrNot () == 0) {
            if (! empty ( $ref )) {
                if (Zend_Validate::is ( $ref, 'EmailAddress' )) {
                    Mage::getModel ( 'airhotels/invitefriends' )->addNewCustomer ( $ref, $customer );
                    Mage::getModel ( 'core/cookie' )->delete ( 'ref' );
                }
            } else {
                /**
                 * Getting new customer details
                 */
                $customerId = $customerName = $customerEmail = '';
                if (isset ( $customer ['entity_id'] )) {
                    $customerId = $customer ['entity_id'];
                }
                if (isset ( $customer ['firstname'] )) {
                    $customerName = $customer ['firstname'];
                }
                if (isset ( $customer ['email'] )) {
                    $customerEmail = $customer ['email'];
                }
                if (! empty ( $customer ['lastname'] )) {
                    $customerName = $customerName . ' ' . $customer ['lastname'];
                }
                /**
                 * Getting current store and website id and save it via function
                 */
                $inviteFriendsArrayValue = array('cus_id'=>$customerId , 'cus_name' => $customerName, 'cus_email'=>$customerEmail);
                Mage::getModel('airhotels/search')->saveInviteFriends($inviteFriendsArrayValue);
            }
        }
    }
    /**
     * Setting ref param
     */
    public function setRefParam() {
        $ref = ( string ) Mage::app ()->getRequest ()->getParam ( 'ref' );
        if ($ref) {
            Mage::getModel ( 'core/cookie' )->set ( 'ref', $ref );
            if ((Zend_Validate::is ( $ref, 'EmailAddress' )) && (! Mage::getSingleton ( 'customer/session' )->isLoggedIn ())) {
                Mage::getSingleton ( 'customer/session' )->setBeforeAuthUrl ( Mage::getUrl ( '', array (
                        '_secure' => true 
                ) ) );
                Mage::getSingleton ( 'checkout/session' )->addSuccess ( "Your referral link accepted successfully!" );
                /**
                 * Redirect to login page
                 */
                $url = Mage::getUrl ( '', array (
                        '_secure' => true 
                ) ) . 'customer/account/login';
                $response = Mage::app ()->getFrontController ()->getResponse ();
                $response->setRedirect ( $url );
                $response->sendResponse ();
            }
        }
    }
    /**
     * Invited frieds credit function
     */
    public function orderInvoiceSaveAfter(Varien_Event_Observer $observer) {
        $event = $observer->getEvent ();
        $invoice = $event->getInvoice ();
        $order = $invoice->getOrder ();
        $orderId = $order->getId ();
        $customerId = $order->getCustomerId ();
        $storeId = $order->getStoreId ();
        $websiteId = '';
        if (! empty ( $storeId )) {
            $websiteId = Mage::getModel ( 'core/store' )->load ( $storeId )->getWebsiteId ();
        }
        if (Mage::helper ( 'airhotels/invitefriends' )->getInviteFriendsEnabledOrNot () == 0 && ! empty ( $websiteId ) && ! empty ( $storeId ) && ! empty ( $orderId )) {
            Mage::getModel ( 'airhotels/invitefriends' )->updateCustomerCreditAmount ( $customerId, $websiteId, $storeId, 'purchase', $orderId );
        }
    }
    /**
     * Set discount amout for invitee
     */
    public function setDiscount($observer) {
        $baseDiscountAmount = 0;
        $quote = $observer->getEvent ()->getQuote ();
        $quoteid = $quote->getId ();
        /**
         * First purchase discount
         */
        $firstPurchaseBaseDiscount = 0;
        $firstPurchaseDiscount = 0;
        $status = Mage::helper ( 'airhotels/invitefriends' )->getFirstPurchaseDiscountEnabledOrNot ();
        if (($status == 0) && (Mage::getSingleton ( 'customer/session' )->isLoggedIn ())) {
            $customerData = Mage::getSingleton ( 'customer/session' )->getCustomer ();
            $customerId = $customerData->getId ();
            $ordersCount = Mage::getResourceModel ( 'sales/order_collection' )->addFieldToSelect ( '*' )->addFieldToFilter ( 'customer_id', $customerId );
            if (count ( $ordersCount ) <= 0) {
                if (Mage::app ()->getStore ()->getCurrentCurrencyCode () != Mage::app ()->getStore ()->getBaseCurrencyCode ()) {
                    $discountForFirstPurchaseAmount = round ( Mage::helper ( 'directory' )->currencyConvert ( Mage::helper ( 'airhotels/invitefriends' )->getDiscountForFirstPurchase (), Mage::app ()->getStore ()->getBaseCurrencyCode (), Mage::app ()->getStore ()->getCurrentCurrencyCode () ), 2 );
                    $discountForFirstPurchaseLimitAmount = round ( Mage::helper ( 'directory' )->currencyConvert ( Mage::helper ( 'airhotels/invitefriends' )->getDiscountForFirstPurchaseLimitAmount (), Mage::app ()->getStore ()->getBaseCurrencyCode (), Mage::app ()->getStore ()->getCurrentCurrencyCode () ), 2 );
                } else {
                    $discountForFirstPurchaseAmount = Mage::helper ( 'airhotels/invitefriends' )->getDiscountForFirstPurchase ();
                    $discountForFirstPurchaseLimitAmount = Mage::helper ( 'airhotels/invitefriends' )->getDiscountForFirstPurchaseLimitAmount ();
                }
                $grandTotalValue = $quote->getGrandTotal ();
                if ($grandTotalValue >= $discountForFirstPurchaseLimitAmount && $grandTotalValue != 0) {
                    $firstPurchaseBaseDiscount = Mage::helper ( 'airhotels/invitefriends' )->getDiscountForFirstPurchase ();
                    $firstPurchaseDiscount = $discountForFirstPurchaseAmount;
                }
            }
        }
        /**
         * Getting discount amount for invitee
         */
        $discountAmount = Mage::getSingleton ( "core/session" )->getDiscountAmountForInvitee ();
        if ($quoteid && ($discountAmount > 0 || $firstPurchaseBaseDiscount > 0)) {
            $baseCurrencyCode = Mage::app ()->getStore ()->getBaseCurrencyCode ();
            $currentCurrencyCode = Mage::app ()->getStore ()->getCurrentCurrencyCode ();
            /**
             * For currency switcher
             */
            $baseCurrencyCode = Mage::app ()->getStore ()->getBaseCurrencyCode ();
            $currentCurrencyCode = Mage::app ()->getStore ()->getCurrentCurrencyCode ();
            $previousCurrencyCode = Mage::getSingleton ( "core/session" )->getDiscountAmountForInviteeCurrencyCode ();
            if (! empty ( $previousCurrencyCode )) {
                if ($currentCurrencyCode != $baseCurrencyCode) {
                    $previousAmount = Mage::getSingleton ( "core/session" )->getDiscountAmountForInvitee ();
                    $discountAmount = round ( Mage::helper ( 'directory' )->currencyConvert ( $previousAmount, $previousCurrencyCode, $currentCurrencyCode ), 2 );
                } else {
                    $previousAmount = Mage::getSingleton ( "core/session" )->getDiscountAmountForInvitee ();
                    /**
                     * Get all allowed currencies
                     * returns array of allowed currency codes
                     */
                    $allowedCurrencies = Mage::getModel ( 'directory/currency' )->getConfigAllowCurrencies ();
                    /**
                     * Get the currency rates
                     * returns array with key as currency code and value as currency rate
                     */
                    $currencyRates = Mage::getModel ( 'directory/currency' )->getCurrencyRates ( $baseCurrencyCode, array_values ( $allowedCurrencies ) );
                    $baseCurrencyRate = $currentCurrencyRate = '';
                    if (isset ( $currencyRates [$baseCurrencyCode] )) {
                        $baseCurrencyRate = $currencyRates [$baseCurrencyCode];
                    }
                    if (isset ( $currencyRates [$previousCurrencyCode] )) {
                        $currentCurrencyRate = $currencyRates [$previousCurrencyCode];
                    }
                    if (! empty ( $baseCurrencyRate ) && ! empty ( $currentCurrencyRate )) {
                        $baseRate = $baseCurrencyRate / $currentCurrencyRate;
                        $baseRatePrice = $baseRate * $previousAmount;
                        $discountAmount = round ( $baseRatePrice, 2 );
                    } else {
                        $discountAmount = $previousAmount;
                    }
                }
            }
            $baseDiscountAmount = Mage::getModel('airhotels/status')->getBaseDiscountAmountDetails($baseCurrencyCode,$currentCurrencyCode,$discountAmount);            
            Mage::getSingleton ( "core/session" )->setCurrentCustomerDiscountedAmount ( $baseDiscountAmount );            
            $discount = Mage::getModel('airhotels/status')->getDiscountDescription($discountAmount,$baseDiscountAmount,$firstPurchaseDiscount,$firstPurchaseBaseDiscount);
            $discountDescriptionSingle = $discount[0];
            $discountDescriptionDouble = $discount[1]; 
            $discountAmount            = $discount[2];
            $canAddItems = $quote->isVirtual () ? ('billing') : ('shipping');
            foreach ( $quote->getAllAddresses () as $address ) {
                if ($address->getAddressType () == $canAddItems) {
                    $address->setSubtotalWithDiscount ( ( float ) $address->getSubtotalWithDiscount () - $discountAmount );
                    $address->setGrandTotal ( ( float ) $address->getGrandTotal () - $discountAmount );
                    $address->setBaseSubtotalWithDiscount ( ( float ) $address->getBaseSubtotalWithDiscount () - $baseDiscountAmount );
                    $address->setBaseGrandTotal ( ( float ) $address->getBaseGrandTotal () - $baseDiscountAmount );
                    if ($address->getDiscountDescription ()) {
                        $address->setDiscountAmount ( $address->getDiscountAmount () - $discountAmount );
                        $address->setDiscountDescription ( $address->getDiscountDescription () . $discountDescriptionDouble );
                        $address->setBaseDiscountAmount ( $address->getBaseDiscountAmount () - $baseDiscountAmount );
                    } else {
                        $address->setDiscountAmount ( - ($discountAmount) );
                        $address->setDiscountDescription ( $discountDescriptionSingle );
                        $address->setBaseDiscountAmount ( - ($baseDiscountAmount) );
                    }
                    $address->save ();
                }
            }
        }
    }
    /**
     * Add discount amout for invitee
     */
    public function removeDiscount($observer) {
        $orderId = $observer->getEvent ()->getOrder ()->getId ();
        if (! empty ( $orderId )) {
            Mage::getModel ( 'airhotels/invitefriends' )->cancelInviteeTransactionDiscountDetails ( $orderId );
        }
    }
    /**
     * Get the property service from Rail Vlaue
     * @param String $propertyServiceFromPeriod            
     * @param String $propertyServiceFrom            
     * @return number
     */
    public function getPropertyServiceFromRail($propertyServiceFromPeriod, $propertyServiceFrom) {
        /**
         * Check the Vlaue
         */
        if ($propertyServiceFromPeriod == 'PM') {
            if ($propertyServiceFrom != 12) {
                $propertyServiceFromRail = $propertyServiceFrom + 12;
            } else {
                $propertyServiceFromRail = $propertyServiceFrom;
            }
        } else {
            if ($propertyServiceFrom != 12) {
                $propertyServiceFromRail = $propertyServiceFrom;
            } else {
                $propertyServiceFromRail = 0;
            }
        }
        return $propertyServiceFromRail;
    }
    /**
     * Get the Property Service to Rail Value
     * @param String $propertyServiceToPeriod            
     * @param String $propertyServiceTo            
     * @return number
     */
    public function getPropertyServiceToRail($propertyServiceToPeriod, $propertyServiceTo) {
        if ($propertyServiceToPeriod == 'PM') {
            if ($propertyServiceTo != 12) {
                $propertyServiceToRail = $propertyServiceTo + 12;
            } else {
                $propertyServiceToRail = $propertyServiceTo;
            }
        } else {
            if ($propertyServiceTo != 12) {
                $propertyServiceToRail = $propertyServiceTo;
            } else {
                $propertyServiceToRail = 0;
            }
        }
        return $propertyServiceToRail;
    }
    /**
     * Based on subscription type Payment method will change.
     * @param Varien_Event_Observer $observer            
     */
    public function paymentMethodIsActive(Varien_Event_Observer $observer) {
        $event = $observer->getEvent ();
        $method = $event->getMethodInstance ();
        $result = $event->getResult ();
        $subscriptionId = Mage::getSingleton ( 'core/session' )->getSubId ();
        if ($subscriptionId != 0) {
            if ($method->getCode () == 'paypaladaptive') {
                $result->isAvailable = true;
            } else {
                $result->isAvailable = false;
            }
        }
    }
    /**
     * Order saved successfully then commisssion information will be saved in database and email notification
     * will be sent to seller
      * Order information will be get from the $observer parameter
     * @param array $observer            
     * @return void
     */
    public function successAfter($observer) {
        $orderIds = $observer->getEvent ()->getOrderIds ();
        /**
         * Get order collection.
         */
        $orderId = $orderIds [0];
        $order = Mage::getModel ( 'sales/order' )->load ( $orderId );
        $items = $order->getAllItems ();
        foreach ( $items as $item ) {
            $getProductId = $item->getProductId ();
        }
        /**
         * Getting Email values
         */
        $adminEmailId = Mage::getStoreConfig ( 'airhotels/custom_email/admin_email_id' );
        $fromMailId = Mage::getStoreConfig ( "trans_email/ident_$adminEmailId/email" );
        $fromName = Mage::getStoreConfig ( "trans_email/ident_$adminEmailId/name" );
        $emailTemplate = Mage::getModel ( 'core/email_template' )->loadDefault ( 'airhotels_sales_notification_admin_email_template_selection' );
        /**
         * get proeprty details
         */
        $property = Mage::getModel ( 'catalog/product' )->load ( $getProductId );
        $propertyName = $property->getName ();
        /**
         * Product user ID
         */
        $userId = $property->getUserid ();
        $customer = Mage::getModel ( 'customer/customer' )->load ( $userId );
        /**
         * Property Email Owner
         */
        $recipient = $customer->getEmail ();
        $baseCurrencyCode = Mage::app ()->getStore ()->getBaseCurrencyCode ();
        $currentCurrencyCode = Mage::app ()->getStore ()->getCurrentCurrencyCode ();
        $serviceFee = $order->getFeeAmount ();
        $commissionPercent = Mage::getStoreConfig ( 'airhotels/custom_group/airhotels_hostfee' );
        $commissionFee = ($order->getSubtotal () * $commissionPercent) / 100;
        $initialCurrecyamount = Mage::helper ( 'directory' )->currencyConvert ( 1, $baseCurrencyCode, $currentCurrencyCode );
        $totalAmount = round ( (Mage::helper ( 'directory' )->currencyConvert ( $order->getGrandTotal (), $baseCurrencyCode, $currentCurrencyCode ) / $initialCurrecyamount), 2 );
        $adminFee = round ( (Mage::helper ( 'directory' )->currencyConvert ( ($commissionFee + $serviceFee), $baseCurrencyCode, $currentCurrencyCode ) / $initialCurrecyamount), 2 );
        $ownerAmount = round ( (Mage::helper ( 'directory' )->currencyConvert ( ($order->getGrandTotal () - ($commissionFee + $serviceFee)), $baseCurrencyCode, $currentCurrencyCode ) / $initialCurrecyamount), 2 );
        /**
         * mail sender name
         */
        $emailTemplate->setSenderName ( $fromName );
        $productDetails = '<table cellspacing="0" cellpadding="0" border="0" width="650" style="border:1px solid #eaeaea">';
        $productDetails .= '<thead><tr>';
        $productDetails .= '<th align="left" bgcolor="#EAEAEA" style="font-size:13px;padding:3px 9px;">' . Mage::helper ( 'airhotels' )->__ ( 'Product Name' ) . '</th><th align="center" bgcolor="#EAEAEA" style="font-size:13px;padding:3px 9px;">' . Mage::helper ( 'airhotels' )->__ ( 'Total Amount' ) . '</th>';
        $productDetails .= '<th align="center" bgcolor="#EAEAEA" style="font-size:13px;padding:3px 9px;">' . Mage::helper ( 'airhotels' )->__ ( 'Commission Fee' ) . '</th><th align="center" bgcolor="#EAEAEA" style="font-size:13px;padding:3px 9px;">' . Mage::helper ( 'airhotels' )->__ ( 'Property Owner Amount' ) . '<span style="display: block;
font-size: 11px;">(Incl.Tax)</span></th></tr></thead>';
        $productDetails .= '<tbody bgcolor="#F6F6F6">';
        $currencySymbol = Mage::app ()->getLocale ()->currency ( Mage::app ()->getStore ()->getCurrentCurrencyCode () )->getSymbol ();
        $productDetails .= '<tr>';
        $productDetails .= '<td align="left" valign="top" style="font-size:11px;padding:3px 9px;border-bottom:1px dotted #cccccc;">' . $propertyName . '</td>';
        $productDetails .= '<td align="center" valign="top" style="font-size:11px;padding:3px 9px;border-bottom:1px dotted #cccccc;">' . $currencySymbol . round ( ($totalAmount), 2 ) . '</td><td align="center" valign="top" style="font-size:11px;padding:3px 9px;border-bottom:1px dotted #cccccc;">' . $currencySymbol . round ( $adminFee, 2 ) . '</td>';
        $productDetails .= '<td align="center" valign="top" style="font-size:11px;padding:3px 9px;border-bottom:1px dotted #cccccc;">' . $currencySymbol . round ( $ownerAmount, 2 ) . '</td>';
        $productDetails .= '</tr></tbody></table>';
        /**
         * mail sender email id
         */
        $emailTemplate->setSenderEmail ( $fromMailId );
        $emailTemplateVariables = (array (
                'ownername' => $fromName,
                'customer_email' => $order->getCustomerEmail (),
                'customer_firstname' => $order->getCustomerFirstname (),
                'order_id' => $order->getIncrementId (),
                'productdetails' => $productDetails 
        ));
        
        $emailTemplate->setDesignConfig ( array (
                'area' => 'frontend' 
        ) );
        /**
         * send mail to customer email ids
         */
        $emailTemplate->send ( $recipient, $fromName, $emailTemplateVariables );
        /**
         * send mail to admin
         */
        $fromName = $order->getCustomerFirstname ();
        $emailTemplate->setSenderName ( $fromName );
        $recipient = $fromMailId;
        $emailTemplate->send ( $recipient, $fromName, $emailTemplateVariables );
        /**
         * Send message to customer
         */
        if (Mage::helper ( 'airhotels/smsconfig' )->getSmsEnabledOrNot () == 0) {
            Mage::helper ( 'airhotels/smsconfig' )->sendordermessage ( $order->getIncrementId (), 1 );
        }
    }
    /**
     * Send a review approval email for the host.
     */
    public function reviewSaveAfter($observer) {
        $data = $observer->getEvent ()->getObject ()->getData ();
        $property = Mage::getModel ( 'catalog/product' )->load ( $data ['entity_pk_value'] );
        $propertyUserId = $property->getUserid ();
        $host = Mage::getModel ( 'customer/customer' )->load ( $propertyUserId );
        $customer = Mage::getSingleton ( 'customer/session' )->getCustomer ();
        $customerName = $customer->getName ();
        $customerEmail = $customer->getEmail ();
        /**
         * Property Email Owner
         */
        $recipient = $host->getEmail ();
        /**
         * Property Email Owner
         */
        $hostName = $host->getName ();
        /**
         * Getting Email values
         */
        $adminEmailId = Mage::getStoreConfig ( 'airhotels/custom_email/admin_email_id' );
        $fromMailId = Mage::getStoreConfig ( "trans_email/ident_$adminEmailId/email" );
        $fromName = Mage::getStoreConfig ( "trans_email/ident_$adminEmailId/name" );
        if ($data ['status_id'] == 1) {
            $emailTemplate = Mage::getModel ( 'core/email_template' )->loadDefault ( 'airhotels_host_review_email_adminapproval_template' );
            /**
             * mail sender email id
             */
            $emailTemplate->setSenderName ( $fromName );
            $emailTemplate->setSenderEmail ( $fromMailId );
            $emailTemplateVariables = (array (
                    'cname' => $hostName,
                    'property_name' => $property->getName (),
                    'buyer_name' => $data ['nickname'],
                    'review_details' => $data ['detail'],
                    'review_status' => 'Approved' 
            ));
            $emailTemplate->setDesignConfig ( array (
                    'area' => 'frontend' 
            ) );
            /**
             * send mail to customer email ids
             */
            $emailTemplate->send ( $recipient, $hostName, $emailTemplateVariables );
            /**
             * Send Review approved mail to customer
             */
            $emailTemplateCustomer = Mage::getModel ( 'core/email_template' )->loadDefault ( 'airhotels_customer_review_email_adminapproval_template' );
            /**
             * mail sender email id
             */
            $emailTemplateCustomer->setSenderName ( $fromName );
            $emailTemplateCustomer->setSenderEmail ( $fromMailId );
            $emailTemplateCustomerVariables = (array (
                    'cname' => $customerName,
                    'property_name' => $property->getName (),
                    'buyer_name' => $data ['nickname'],
                    'review_details' => $data ['detail'],
                    'review_status' => 'Approved' 
            ));
            $emailTemplateCustomer->setDesignConfig ( array (
                    'area' => 'frontend' 
            ) );
            /**
             * send mail to customer email ids
             */
            $emailTemplateCustomer->send ( $customerEmail, $customerName, $emailTemplateCustomerVariables );
        } else {
            $emailTemplateModeration = Mage::getModel ( 'core/email_template' )->loadDefault ( 'airhotels_host_review_moderation_email_adminapproval_template' );
            /**
             * mail sender email id
             */
            $emailTemplateModeration->setSenderName ( $fromName );
            $emailTemplateModeration->setSenderEmail ( $fromMailId );
            $emailTemplateVariables = (array (
                    'cname' => $hostName,
                    'property_name' => $property->getName (),
                    'buyer_name' => $data ['nickname'],
                    'review_details' => $data ['detail'],
                    'review_status' => 'Pending' 
            ));
            $emailTemplateModeration->setDesignConfig ( array (
                    'area' => 'frontend' 
            ) );
            /**
             * send mail to customer email ids
             */
            $emailTemplateModeration->send ( $recipient, $hostName, $emailTemplateVariables );
            /**
             * Send Review approved mail to customer
             * @var unknown
             */
            $emailTemplateCustomerModeration = Mage::getModel ( 'core/email_template' )->loadDefault ( 'airhotels_customer_review_moderation_email_adminapproval_template' );
            /**
             * mail sender email id
             */
            $emailTemplateCustomerModeration->setSenderName ( $fromName );
            $emailTemplateCustomerModeration->setSenderEmail ( $fromMailId );
            $emailTemplateCustomerVariables = (array (
                    'cname' => $customerName,
                    'property_name' => $property->getName (),
                    'buyer_name' => $data ['nickname'],
                    'review_details' => $data ['detail'],
                    'review_status' => 'Pending' 
            ));
            $emailTemplateCustomerModeration->setDesignConfig ( array (
                    'area' => 'frontend' 
            ) );
            /**
             * send mail to customer email ids
             */
            $emailTemplateCustomerModeration->send ( $customerEmail, $customerName, $emailTemplateCustomerVariables );
        }
    }
}