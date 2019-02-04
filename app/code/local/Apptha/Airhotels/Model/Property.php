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
class Apptha_Airhotels_Model_Property extends Mage_Core_Model_Abstract {
    /**
     * Function Name: "getpropertycollection"
     * Get Property Collection
     */
    public function getpropertycollection() {
        /**
         * get Collection for the 'catalog/product' with add attribute filer as 'type_id'
         * And add the select feild Values such as,
         * 'image'
         * 'userid'
         * 'bedtype'
         * 'name'
         * 'Price'
         * 'description'
         * 'short_description'
         * 'propertytype'
         * 'amenity'
         * 'totalrooms'
         * 'propertyadd'
         * 'privacy'
         * 'status'
         * 'city'
         * 'statee'
         * 'country'
         * 'cancelpolicy'
         * 'pets'
         * 'maplocation'
         * 'accomodates'
         * 'propertyapproved'
         */
        return Mage::getModel ( 'catalog/product' )->getCollection ()->setDisableFlat ( true )->addAttributeToFilter ( 'type_id', array (
                'eq' => 'property' 
        ) )->addAttributeToSelect ( 'image' )->addAttributeToSelect ( 'userid' )->addAttributeToSelect ( 'bedtype' )->addAttributeToSelect ( 'name' )->addAttributeToSelect ( 'price' )->addAttributeToSelect ( 'description' )->addAttributeToSelect ( 'short_description' )->addAttributeToSelect ( 'propertytype' )->addAttributeToSelect ( 'amenity' )->addAttributeToSelect ( 'totalrooms' )->addAttributeToSelect ( 'propertyadd' )->addAttributeToSelect ( 'privacy' )->addAttributeToSelect ( 'status' )->addAttributeToSelect ( 'city' )->addAttributeToSelect ( 'state' )->addAttributeToSelect ( 'country' )->addAttributeToSelect ( 'cancelpolicy' )->addAttributeToSelect ( 'pets' )->addAttributeToSelect ( 'maplocation' )->addAttributeToSelect ( 'accomodates' )->addAttributeToSelect ( 'propertyapproved' );
    }
    /**
     * Function Name: currentTrip
     * Current trip has been used to display the informations of CurrentTrip
     */
    public function currentTrip() {
        $productId = $fromdate = $todate = $dateTimeStatus = $cancelStatus = array ();
        $customer = Mage::getSingleton ( 'customer/session' )->getCustomer ();
        $cusId = $customer->getId ();
        $todayData = Mage::getModel ( 'core/date' )->timestamp ( time () );
        $todayDate = date ( 'Y-m-d', $todayData );
        $result = Mage::getModel ( 'airhotels/airhotels' )->getCollection ()->addFieldToFilter ( 'order_status', 1 )->addFieldToFilter ( 'fromdate', array (
                'lteq' => $todayDate 
        ) )->addFieldToFilter ( 'todate', array (
                'gteq' => $todayDate 
        ) )->addFieldToFilter ( 'customer_id', $cusId )->setOrder ( 'id', 'DESC' );
        foreach ( $result as $res ) {
            $dayflag = 0;
            if (! empty ( $res ['checkin_time'] )) {
                if (strtotime ( $res ['checkin_time'] ) < Mage::getModel ( 'core/date' )->timestamp ( time () ) && strtotime ( $res ['checkout_time'] ) > Mage::getModel ( 'core/date' )->timestamp ( time () )) {
                    $dayflag = 1;
                }
            } else {
                $dayflag = 1;
            }
            if ($dayflag == 1) {
                if (! empty ( $res ['checkin_time'] )) {
                    $fromdate [] = $res ['checkin_time'];
                    $dateTimeStatus [] = 1;
                } else {
                    $fromdate [] = $res ['fromdate'];
                    $dateTimeStatus [] = 0;
                }
                if (! empty ( $res ['checkout_time'] )) {
                    $todate [] = $res ['checkout_time'];
                } else {
                    $todate [] = $res ['todate'];
                }
                $productId [] = $res ['entity_id'];
                $cancelStatus [] = $res ['cancel_order_status'];
            }
        }
        /**
         * Return an array.
         */
        return array (
                $productId,
                $fromdate,
                $todate,
                $cancelStatus,
                $dateTimeStatus 
        );
    }
    /**
     * Function Name: previousTrip
     * Get Previous trip
     */
    public function previousTrip() {
        /**
         * Initialize the productId,fromDateVal,todateVal,dateTimeStatusVal to array.
         */
        $productId = $fromdateVal = $todateVal = $dateTimeStatusVal = array ();
        /**
         * Get Today's date
         */
        $todayData = Mage::getModel ( 'core/date' )->timestamp ( time () );
        /**
         * Change Format
         */
        $todayDate = date ( 'Y-m-d', $todayData );
        /**
         * setting the Result Value
         */
        $result = Mage::getModel ( 'airhotels/calendarsync' )->TripDetails ();       
        /**
         * Iterating Loop
         */
        foreach ( $result as $res ) {            
            $dayflag = 0;
            /**
             * check wether the $res array not an empty and get the
             * fromdate, dateTimeStatus
             */
            if (! empty ( $res ['checkout_time'] ) && strtotime ( $res ['todate'] ) == strtotime ( $todayDate )) {
                if (strtotime ( $res ['checkout_time'] ) < Mage::getModel ( 'core/date' )->timestamp ( time () )) {
                    $dayflag = 1;
                } else {
                    $dayflag = 0;
                }
            } else {
                $dayflag = 0;
            }
            /**
             * Checking Todate is less than today's date
             */
            if (strtotime ( $res ['todate'] ) < strtotime ( $todayDate )) {
                $dayflag = 1;
            }
            if ($dayflag == 1) {
                /**
                 * check wether the $res array not an empty and get the
                 * fromdate, dateTimeStatus
                 */
                if (! empty ( $res ['checkin_time'] )) {
                    /**
                     * check weather the $res array not an empty
                     * and get the todate value
                     */
                    $fromdateVal [] = $res ['checkin_time'];
                    $dateTimeStatusVal [] = 1;
                } else {
                    $fromdateVal [] = $res ['fromdate'];
                    $dateTimeStatusVal [] = 0;
                }
                /**
                 * check weather the $res array not an empty
                 * and get the todate value
                 */
                if (! empty ( $res ['checkout_time'] )) {
                    $todateVal [] = $res ['checkout_time'];
                } else {
                    $todateVal [] = $res ['todate'];
                }
                $productId [] = $res ['entity_id'];
                $orderId [] = $res['order_item_id'];
            }
        }
        /**
         * Return as an array.
         */
        return array (
                $productId,
                $fromdateVal,
                $todateVal,
                $dateTimeStatusVal, 
                $orderId
        );
    }
    
    
    /**
     * Function Name" updateProfileInformation
     * Get the response time and short description about client
     *
     * @param int $responseTime            
     * @param string $moreHost            
     * @return array $selectResult1
     */
    public function updateProfileInformation($responseTime, $moreHost) {
        /**
         * Set the value to customer Id
         */
        $customerId = Mage::getSingleton ( 'customer/session' )->getId ();
        /**
         * Get Collection of customerphoto
         */
        $result = Mage::getModel ( 'airhotels/customerphoto' )->getCollection ()->addFieldToFilter ( 'customer_id', $customerId );
        try {
            /**
             * if result count is zero
             */
            if (count ( $result ) == 0) {
                $coreResource = Mage::getSingleton ( 'core/resource' );
                $conn = $coreResource->getConnection ( 'core_read' );
                /**
                 * Insert the values to airhotels_customer_photo
                 */
                $conn->insert ( $coreResource->getTableName ( 'airhotels_customer_photo' ), array (
                        'customer_id' => $customerId,
                        'imagename' => '',
                        'response_time' => $responseTime,
                        'more_host' => $moreHost 
                ) );
            } else {
                /**
                 * Defining array
                 */
                $data = array (
                        'response_time' => $responseTime,
                        'more_host' => $moreHost 
                );
                /**
                 * Insert the values to customerphoto
                 */
                $model = Mage::getModel ( 'airhotels/customerphoto' )->load ( $customerId )->addData ( $data );
                $model->setId ( $customerId )->save ();
            }
            return 1;
        } catch ( Exception $ex ) {
            /**
             * Handle the error
             */
            Mage::getSingleton ( 'core/session' )->addError ( $ex );
            return 0;
        }
    }
    
    /**
     * Function Name" getPopularProperty
     * Get the first four popular propertys based on number of propertys
     *
     * @return array $results
     */
    public function getPopularProperty() {
        $popularArray = array ();
        /**
         * Create the Product Collection
         */
        /**
         * Filters applied are
         * status
         * property id
         * property approval
         */
        $_productCollection = Mage::getResourceModel ( 'reports/product_collection' )->addOrderedQty()        
        ->addAttributeToFilter ( 'status', array (
                'eq' => 1 
        ) )->addAttributeToFilter ( 'propertyapproved', array (
                'eq' => 1 
        ) )->addAttributeToFilter ( 'type_id', array (
                'eq' => 'property' 
        ) )->addAttributeToSelect ( "*" )->setVisibility ( array (
                2,
                3,
                4 
        ) )->setOrder('ordered_qty', 'desc');       
                
        foreach ( $_productCollection as $_product ) {
            $popularArray [] ['entity_id'] = $_product->getId ();
        }
        /**
         * return array
         */
        return $popularArray;
    }
    
    /**
     * Function Name" getPopularProperty
     * Get the first four popular propertys based on number of propertys
     *
     * @return array $results
     */
    public function getMostPopular() {
        /**
         * New algorithm for most popular products
         */
        $populararr = array ();
        $reviewsCount = array ();
        $averagedRating = array ();
        $productIds = array ();
        $count = 0;
        $overallaverageRating = 0;
        $reviews = Mage::getModel ( 'review/review_summary' )->getCollection ();
        foreach ( $reviews as $reviewSummary ) {
            $overallaverageRating += $reviewSummary->getRatingSummary ();
            $reviewsCount [] = $reviewSummary->getReviewsCount ();
        }
        /**
         * Count the reviews
         *
         * @var $count
         */
        $count = count ( $reviews );
        if ($count > 0) {
            $globalaveragerating = $overallaverageRating / $count;
        }
        $numberofvotes = count ( $reviews );
        $maxnumberofvotes = max ( $reviewsCount );
        $reviewsCollection = Mage::getModel ( 'review/review_summary' )->getCollection ();
        foreach ( $reviewsCollection as $reviewSummary ) {
            /**
             * averaged ('bayesian') rating
             */
            $averageRating = $reviewSummary->getRatingSummary ();
            $averagedRating [] = (($numberofvotes / $maxnumberofvotes) * $averageRating) + ((1 - ($numberofvotes / $maxnumberofvotes)) * $globalaveragerating);
            $productIds [] = $reviewSummary->getEntityPkValue ();
        }
        $arrayCombine = array_combine ( $productIds, $averagedRating );
        arsort ( $arrayCombine );
        foreach ( $arrayCombine as $key => $item ) {
            $populararr [] ['entity_id'] = $key;
        }
        return $populararr;
    }
    /**
     * Function Name: getRatedProperty
     * Get first four most rated propertys
     *
     * @return array $products
     */
    public function getRatedProperty() {
        $ratedProducts = array ();
        /**
         * Create the Product Collection
         */
        $products = Mage::getResourceModel ( 'reports/product_collection' )->addAttributeToFilter ( 'status', array (
                'eq' => 1 
        ) )->addAttributeToFilter ( 'propertyapproved', array (
                'eq' => 1 
        ) )->addAttributeToFilter ( 'type_id', array (
                'eq' => 'property' 
        ) )->addAttributeToSelect ( "*" )->setVisibility ( array (
                2,
                3,
                4 
        ) );
        /**
         * Using join query
         */
        $products->joinField ( 'reviews_count', 'review/review_aggregate', 'rating_summary', 'entity_pk_value=entity_id', array (
                'entity_type' => 1,
                'store_id' => Mage::app ()->getStore ()->getId () 
        ), 'left' );
        
        /**
         * Checking whether product rated or not
         */
        $products->addAttributeToFilter ( 'reviews_count', array (
                'gt' => 0 
        ) );
        $products->setOrder ( 'rating_summary', 'desc' );
        $products->setPageSize ( 4 );        
        /**
         * Iterating the loop
         */
        foreach ( $products as $product ) {
            $ratedProducts [] ['entity_id'] = $product->getId ();
        }
        
        return $ratedProducts;
    }
    
    /**
     * Function Name: getreview
     * Filter all the cusotmer review for particular host properties
     *
     * @return array $propertyReview
     */
    public function getreview() {
        $propertyId = array ();
        /**
         * Get the Customer Id value
         */
        $customerId = Mage::app ()->getRequest ()->getParam ( 'id' );
        $propertyReview = array ();
        /**
         * get the airhotels Property Collection
         */
        $PropertyCollection = Mage::getModel ( 'airhotels/property' )->getpropertycollection ()->addAttributeToFilter ( 'status', array (
                'eq' => 1 
        ) )->addAttributeToFilter ( 'propertyapproved', array (
                'eq' => 1 
        ) )->addAttributeToFilter ( 'userid', array (
                'eq' => $customerId 
        ) );
        /**
         * Iterating the loop
         */
        foreach ( $PropertyCollection as $property ) {
            $propertyId [] = $property->getId ();
        }
        /**
         * Retreive the review Collection
         */
        $reviewsTotal = Mage::getModel ( 'review/review' )->getResourceCollection ();
        $reviewsTotal->addStoreFilter ( 0 )->addStatusFilter ( Mage_Review_Model_Review::STATUS_APPROVED )->setDateOrder ()->addRateVotes ()->load ();
        /**
         * Add feild to filter
         */
        $reviewsTotal->addFieldToFilter ( 'entity_pk_value', array (
                'in' => $propertyId 
        ) );
        $reviewsTotal->setOrder ( 'created_at', 'desc' );
        /**
         * Iterating the loop
         */
        foreach ( $reviewsTotal as $review ) {
            $propertyReview [] = $review;
        }
        /**
         * Returning the reviewColletion
         */
        return $propertyReview;
    }
    
    /**
     * Function Name: deleteProperty
     * Delete the Property
     *
     * @param int $entity_id            
     */
    public function deleteProperty($entity_id) {
        /**
         * Get the admin email Id
         */
        $adminEmailId = Mage::getStoreConfig ( 'airhotels/custom_email/admin_email_id' );
        /**
         * Get the recepient email Id
         */
        $toMailId = Mage::getStoreConfig ( "trans_email/ident_$adminEmailId/email" );
        /**
         * Get the to name
         */
        $toName = Mage::getStoreConfig ( "trans_email/ident_$adminEmailId/name" );
        /**
         * Get the template Id
         */
        $templateId = ( int ) Mage::getStoreConfig ( 'airhotels/custom_email/propertydelete_template' );
        /**
         * if it is user template then this process is continue
         */
        if ($templateId) {
            $emailTemplate = Mage::getModel ( 'core/email_template' )->load ( $templateId );
        } else {
            /**
             * we are calling default template
             */
            $emailTemplate = Mage::getModel ( 'core/email_template' )->loadDefault ( 'airhotels_custom_email_propertydelete_template' );
        }
        /**
         * Get proeprty details
         */
        $property = Mage::getModel ( 'catalog/product' )->load ( $entity_id );
        /**
         * Property name and userid
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
         * Mail sender name
         */
        $emailTemplate->setSenderName ( $customerName );
        /**
         * Mail sender email id
         */
        $emailTemplate->setSenderEmail ( $toMailId );
        $senderName = $toName;
        $emailTemplateVariables = (array (
                'ownername' => $toName,
                'pname' => $propertyName,
                'cname' => $customerName 
        ));
        $emailTemplate->setDesignConfig ( array (
                'area' => 'frontend' 
        ) );
        /**
         * It return the temp body
         */
        $emailTemplate->getProcessedTemplate ( $emailTemplateVariables );
        /**
         * Send mail to customer email ids
         */
        $emailTemplate->send ( $recipient, $senderName, $emailTemplateVariables );
        /**
         * Delete collection
         * set secure admin area
         */
        Mage::register ( 'isSecureArea', true );
        Mage::getModel ( 'catalog/product' )->setId ( $entity_id )->delete ();
        
        /**
         * un set secure admin area
         */
        Mage::unregister ( 'isSecureArea' );
    }
    /**
     * function Name: adminApproval
     * Email notification to admin if host added a new property
     *
     * Passed property is as $entityId
     *
     * @param int $entityId            
     *
     * @return bool
     */
    public function adminApproval($entityId) {
        /**
         * update collection
         */
        $productDetails = Mage::getModel ( 'catalog/product' )->load ( $entityId );
        /**
         * Set status
         */
        $productDetails->setStatus ( 2 );
        /**
         * Set Property Approval
         */
        $productDetails->setPropertyapproved ( 0 );
        /**
         * Get the current store Id
         */
        $CurrentStoreId = Mage::app ()->getStore ()->getId ();
        Mage::app ()->setCurrentStore ( Mage_Core_Model_App::ADMIN_STORE_ID );
        /**
         * save Product Details
         */
        $productDetails->save ();
        
        Mage::app ()->setCurrentStore ( $CurrentStoreId );
        /**
         * Setting the email details
         */
        $adminEmailId = Mage::getStoreConfig ( 'airhotels/custom_email/admin_email_id' );
        /**
         * Getting to email Id
         */
        $toMailId = Mage::getStoreConfig ( "trans_email/ident_$adminEmailId/email" );
        /**
         * Getting to name
         */
        $toName = Mage::getStoreConfig ( "trans_email/ident_$adminEmailId/name" );
        $templateId = ( int ) Mage::getStoreConfig ( 'airhotels/custom_email/propertyapproval_template' );
        /**
         * if it is user template then this process is continue
         */
        if ($templateId) {
            $emailTemplate = Mage::getModel ( 'core/email_template' )->load ( $templateId );
        } else {
            /**
             * we are calling default template
             */
            $emailTemplate = Mage::getModel ( 'core/email_template' )->loadDefault ( 'airhotels_custom_email_propertyapproval_template' );
        }
        /**
         * get proeprty details
         */
        $property = Mage::getModel ( 'catalog/product' )->load ( $entityId );
        /**
         * Get Property Name
         */
        $propertyName = $property->getName ();
        /**
         * Get Property User Id
         */
        $userId = $property->getUserid ();
        /**
         * Load customer Details
         */
        $customer = Mage::getModel ( 'customer/customer' )->load ( $userId );
        /**
         * Property Email Owner
         */
        $senderId = $customer->getEmail ();
        /**
         * Property Email Owner
         */
        $customerName = $customer->getName ();
        /**
         * mail sender name
         */
        $emailTemplate->setSenderName ( $customerName );
        /**
         * mail sender email id
         */
        $emailTemplate->setSenderEmail ( $senderId );
        $adminurl = Mage::helper ( "adminhtml" )->getUrl ( "adminhtml/catalog_product/index" );
        $emailTemplateVariables = (array (
                'ownername' => $toName,
                'pname' => $propertyName,
                'cname' => $customerName,
                'adminurl' => $adminurl 
        ));
        $emailTemplate->setDesignConfig ( array (
                'area' => 'frontend' 
        ) );
        /**
         * it return the temp body
         */
        $emailTemplate->getProcessedTemplate ( $emailTemplateVariables );
        /**
         * send mail to customer email ids
         */
        $emailTemplate->send ( $toMailId, $toName, $emailTemplateVariables );
    }
    /**
     * Function Name: newProperty
     * Setting the Property
     *
     * @param int $entityId            
     */
    public function newProperty($entityId) {
        $productId = $entityId;
        $storeId = array ();
        $websiteId = Mage::app ()->getWebsite ()->getId ();
        $currentStoreId = Mage::app ()->getStore ()->getStoreId ();
        $productDetails = Mage::getModel ( 'catalog/product' )->load ( $entityId );
        $productDetails->setStatus ( 1 );
        $productDetails->setPropertyapproved ( 1 );
        Mage::app ()->setCurrentStore ( Mage_Core_Model_App::ADMIN_STORE_ID );
        $productDetails->save ();
        if (! empty ( $productId )) {
            $productUserId = Mage::getModel ( "catalog/product" )->load ( $productId )->getUserid ();
            $customerId = Mage::getSingleton ( 'customer/session' )->getCustomer ()->getId ();
            if ($productUserId != $customerId) {
                return false;
            }
        }
        $allStores = Mage::app ()->getStores ();
        foreach ( $allStores as $_eachStoreId => $val ) {
            $storeId [] = Mage::app ()->getStore ( $_eachStoreId )->getId ();
        }
        /**
         * Update invite friends products
         */
        if ((Mage::helper ( 'airhotels/invitefriends' )->getInviteFriendsEnabledOrNot () == 0) && (! empty ( $productId ))) {
            $inviteFriendsProductCollection = Mage::getModel ( 'airhotels/invitefriendsproduct' )->getCollection ()->addFieldToFilter ( 'product_id', $productId )->getFirstItem ();
            $inviteProductId = $inviteFriendsProductCollection->getProductId ();
            if (empty ( $inviteProductId )) {
                Mage::getModel ( 'airhotels/invitefriends' )->updateCustomerCreditAmount ( $customerId, $websiteId, $currentStoreId, 'listing', $productId );
            }
        }
        /**
         * Getting admin email ID
         */
        $adminEmailIdVal = Mage::getStoreConfig ( 'airhotels/custom_email/admin_email_id' );
        /**
         * Getting admin email ID
         */
        $toNameVal = Mage::getStoreConfig ( "trans_email/ident_$adminEmailIdVal/name" );
        $toMailIdVal = Mage::getStoreConfig ( "trans_email/ident_$adminEmailIdVal/email" );
        $templateIdVal = ( int ) Mage::getStoreConfig ( 'airhotels/custom_email/newproperty_template' );
        /**
         * if it is user template then this process is continue
         */
        if ($templateIdVal) {
            $emailTemplate = Mage::getModel ( 'core/email_template' )->load ( $templateIdVal );
        } else {
            /**
             * we are calling default template
             */
            $emailTemplate = Mage::getModel ( 'core/email_template' )->loadDefault ( 'airhotels_custom_email_newproperty_template' );
        }
        /**
         * get proeprty details
         */
        $property = Mage::getModel ( 'catalog/product' )->load ( $entityId );
        $propertyNameVal = $property->getName ();
        $productUrlVal = $property->getProductUrl ();
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
        $emailTemplate->setSenderName ( $customerName );
        /**
         * mail sender email id
         */
        $emailTemplate->setSenderEmail ( $recipient );
        $emailTemplateVariables = (array (
                'ownername' => $toNameVal,
                'pname' => $propertyNameVal,
                'purl' => $productUrlVal,
                'cname' => $customerName 
        ));
        $emailTemplate->setDesignConfig ( array (
                'area' => 'frontend' 
        ) );
        /**
         * it return the temp body
         */
        $emailTemplate->getProcessedTemplate ( $emailTemplateVariables );
        /**
         * send mail to customer email ids
         */
        $emailTemplate->send ( $toMailIdVal, $toNameVal, $emailTemplateVariables );
    }
    /**
     * Function Name: cancelOrder
     * Cancel order
     *
     * @param int $orderid            
     * @return boolean
     */
    public function cancelOrder($orderid) {
        $data = array (
                'cancel_order_status' => 1 
        );
        $model = Mage::getModel ( 'airhotels/airhotels' )->load ( $orderid, 'order_id' )->addData ( $data );
        $id = $model->getId ();
        try {
            $model->setId ( $id )->save ();
        } catch ( Exception $e ) {
            Mage::getSingleton ( 'core/session' )->addError ( $e->getMessage () );
            return false;
        }
        /**
         * Get the admin Email Id
         */
        $adminEmailId = Mage::getStoreConfig ( 'airhotels/custom_email/admin_email_id' );
        /**
         * Get the ToEmail Id
         */
        $toMailId = Mage::getStoreConfig ( "trans_email/ident_$adminEmailId/email" );
        /**
         * Get the Name
         */
        $toName = Mage::getStoreConfig ( "trans_email/ident_$adminEmailId/name" );
        /**
         * Get the Template ID
         */
        $templateId = ( int ) Mage::getStoreConfig ( 'airhotels/custom_email/cancelorder_template' );
        /**
         * if it is user template then this process is continue
         */
        if ($templateId) {
            $emailTemplate = Mage::getModel ( 'core/email_template' )->load ( $templateId );
        } else {
            /**
             * we are calling default template
             */
            $emailTemplate = Mage::getModel ( 'core/email_template' )->loadDefault ( 'airhotels_custom_email_cancelorder_template' );
        }
        $customer = Mage::getSingleton ( 'customer/session' )->getCustomer ();
        /**
         * Property Email Owner
         */
        $senderemail = $customer->getEmail ();
        /**
         * Property Email Owner
         */
        $customerName = $customer->getName ();
        /**
         * mail sender name
         */
        $emailTemplate->setSenderName ( $customerName );
        /**
         * mail sender email id
         */
        $emailTemplate->setSenderEmail ( $senderemail );
        $emailTemplateVariables = (array (
                'ownername' => $toName,
                'orderid' => $orderid 
        ));
        
        $emailTemplate->setDesignConfig ( array (
                'area' => 'frontend' 
        ) );
        /**
         * it return the temp body
         */
        $emailTemplate->getProcessedTemplate ( $emailTemplateVariables );
        /**
         * send mail to customer email ids
         */
        $emailTemplate->send ( $toMailId, $toName, $emailTemplateVariables );
    }
    /**
     * Payment request sent to admin from host
     *
     * @param unknown $order_id            
     */
    public function paymentRequest($order_id) {
        $data = array (
                'payment_request_status' => 1 
        );
        $model = Mage::getModel ( 'airhotels/airhotels' )->load ( $order_id, 'order_id' )->addData ( $data );
        $model->save ();
        /**
         * Get the admin Email Id
         */
        $adminEmailId = Mage::getStoreConfig ( 'airhotels/custom_email/admin_email_id' );
        /**
         * Get the ToEmail Id
         */
        $toMailId = Mage::getStoreConfig ( "trans_email/ident_$adminEmailId/email" );
        /**
         * Get the Name
         */
        $toName = Mage::getStoreConfig ( "trans_email/ident_$adminEmailId/name" );
        $emailTemplate = Mage::getModel ( 'core/email_template' )->loadDefault ( 'airhotels_custom_email_paymentrequest_template' );
        $customer = Mage::getSingleton ( 'customer/session' )->getCustomer ();
        /**
         * Property Email Owner
         */
        $customerName = $customer->getName ();
        /**
         * mail sender name
         */
        $emailTemplate->setSenderName ( $customerName );
        
        /**
         * Property Email Owner
         */
        $senderemail = $customer->getEmail ();
        /**
         * mail sender email id
         */
        $emailTemplate->setSenderEmail ( $senderemail );
        
        $emailTemplateVariables = (array (
                'ownername' => $toName,
                'orderid' => $order_id 
        ));
        $emailTemplate->setDesignConfig ( array (
                'area' => 'frontend' 
        ) );
        /**
         * it return the temp body
         */
        $emailTemplate->getProcessedTemplate ( $emailTemplateVariables );
        /**
         * send mail to customer email ids
         */
        $emailTemplate->send ( $toMailId, $toName, $emailTemplateVariables );
    }
    /**
     * Get subscription method
     *
     * @param unknown $productId            
     * @return unknown
     */
    public function getSubscriptionMethod($productId) {
        $_subscriptionType = array ();
        $productSubscriptionCollection = $this->getProductSubscriptionModel ()->addFieldToFilter ( 'product_id', $productId )->addFieldToSelect ( 'subscription_type' )->getData ();
        foreach ( $productSubscriptionCollection as $productSubscription ) {
            $_subscriptionType [] = $productSubscription ['subscription_type'];
        }
        if ($_subscriptionType) {
            return $this->getSubscriptionTypeModel ()->addFieldToFilter ( 'id', array (
                    $_subscriptionType 
            ) );
        }
    }
    /**
     * Get product subscription collection
     *
     * @return subscriptionCollection
     */
    public function getProductSubscriptionModel() {
        return Mage::getModel ( 'airhotels/subscriptiontype' )->productSubscriptionCollection ();
    }
    /**
     * Get subscription type model
     *
     * @param unknown $_subscriptionType            
     */
    public function getSubscriptionTypeModel($_subscriptionType) {
        return Mage::getModel ( 'airhotels/subscriptiontype' )->subscriptionTypeCollection ();
    }
}