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
class Apptha_Airhotels_GeneralController extends Mage_Core_Controller_Front_Action {
    const XML_PATH_EMAIL_RECIPIENT = 'trans_email/ident_general/email';
    const XML_PATH_EMAIL_SENDER = 'contacts/email/sender_email_identity';
    /**
     * basicSaveAction - Save the new experience for the host
     * 
     * Check weather the property Time is Set
     */
    public function basicsaveAction() {
        if (! Mage::getSingleton ( 'customer/session' )->isLoggedIn ()) {
            $this->_redirectUrl ( Mage::helper ( 'customer' )->getLoginUrl () );
            return false;
        }                     
        $post = $this->getRequest ()->getPost ();        
        $productData = $this->getRequest ()->getPost ( 'product' );
        $propertyData = $this->getRequest ()->getPost ( 'property' );
        $currentExperienceId = Mage::getSingleton ( 'customer/session' )->getCurrentExperienceId ();              
        if ($post) {
            $product = Mage::getModel( 'airhotels/status' )->saveFormData($post);            
            $product->addData ( $propertyData );
            /**
             * Check weather the Security deposit has been enabled
             */
            $getSecurityEnabledOrNot = Mage::helper ( 'airhotels/product' )->getSecurityEnabledOrNot ();
            if ($getSecurityEnabledOrNot == 0) {
                $productData = $post ['product'];
                if ($product->getOptions ()) {
                    foreach ( $product->getOptions () as $opt ) {
                        $opt->delete ();
                    }
                    $product->setCanSaveCustomOptions ( 1 );
                    $product->save ();
                }
                /**
                 * Initialize product options
                 */
                if (! empty ( $productData ['options'] ['1'] ['values'] )) {
                    $product->setProductOptions ( $productData ['options'] );
                    $product->setCanSaveCustomOptions ( 1 );
                    $product->save ();
                }
            }
            $product->save ();           
            /**
             * logic for saving subscription type from frontend.
             */
            $product_Id = $product->getId ();
            $subscriptionType = $post ['subscription_type'];
            $pricePerIteration = $post ['price_per_iteration'];
            $maximumValue = sizeof ( $subscriptionType );
            $pricePerIterationValue = $pricePerIterationValue = '';
            $productModel = array ();
            $isSubscriptionOnly = $productStatus = "1";
            $productModel = Mage::getModel ( 'airhotels/productsubscriptions' )->getCollection ()->addFieldToFilter ( 'product_id', array (
                    'eq' => $product_Id 
            ) )->addFieldToFilter ( 'is_delete', '0' )->getData ();
            if ($productModel) {
                for($i = 0; $i < count ( $productModel ); $i ++) {
                    $id = $productModel [$i] ['id'];
                    $productcollections = Mage::getModel ( 'airhotels/productsubscriptions' )->load ( $id );
                    $productcollections->delete ();
                }
            }
            for($i = 0; $i < $maximumValue; $i ++) {
                $subscriptionTypeList = $subscriptionType [$i];
                $pricePerIterationValue = $pricePerIteration [$i];
                $productModel = Mage::getModel ( 'airhotels/productsubscriptions' );
                $productModel->setData ( 'product_id', $product_Id );
                $productModel->setData ( 'subscription_type', $subscriptionTypeList );
                $productModel->setData ( 'is_subscription_only', $isSubscriptionOnly );
                $productModel->setData ( 'price_per_iteration', $pricePerIterationValue );
                $productModel->save ();
            }
            $manageModel = $manageSubscriptionsModel = array ();
            $manageProductId = $manageSubscriptionsModel = '';
            $manageSubscriptionsModel = Mage::getModel ( 'airhotels/managesubscriptions' )->getCollection ();
            $manageModel = $manageSubscriptionsModel->getData ();
            if (! empty ( $manageModel )) {
                $manageModel = Mage::getModel ( 'airhotels/managesubscriptions' )->getCollection ()->addFieldToFilter ( 'product_id', array (
                        'eq' => $product_Id 
                ) )->getData ();
                foreach ( $manageModel as $manageModelData ) {
                    $manageProductId = $manageModelData ['product_id'];
                }
                if ($manageProductId == $product_Id) {
                    $model = Mage::getModel ( 'airhotels/managesubscriptions' )->load ( $product_Id, 'product_id' );                    
                    $model->setData ( 'is_subscription_only', $isSubscriptionOnly );
                    $model->setData ( 'start_date', $startDate );
                    $model->setData ( 'product_status', $productStatus );
                    $model->save ();
                } else {
                    $model = Mage::getModel ( 'airhotels/managesubscriptions' );
                    $model->setData ( 'product_id', $product_Id );
                    $model->setData ( 'is_subscription_only', $isSubscriptionOnly );
                    $model->setData ( 'start_date', $startDate );
                    $model->setData ( 'product_status', $productStatus );
                    $model->save ();
                }
            } else {
                $model = Mage::getModel ( 'airhotels/managesubscriptions' );
                $model->setData ( 'product_id', $product_Id );
                $model->setData ( 'is_subscription_only', $isSubscriptionOnly );
                $model->setData ( 'start_date', $startDate );
                $model->setData ( 'product_status', $productStatus );
                $model->save ();
            }
            /**
             * Save latitude and longitude values
             */
            $entityId = $product->getId ();
            $dataLatitues = array (
                    'latitude' => $post ['latitude'],
                    "longitude" => $post ['longitude'],
                    "entity_id" => $entityId 
            );
            if (empty ( $currentExperienceId )) {
                $collection = Mage::getModel ( 'airhotels/latitudelongitude' )->setData ( $dataLatitues );
                $collection->save ()->getId ();                
                Mage::getSingleton ( 'customer/session' )->setCurrentExperienceId ( $product->getId () );
            } else {
                $collection = Mage::getModel ( 'airhotels/latitudelongitude' )->load ( $currentExperienceId, 'entity_id' )->addData ( $dataLatitues );
                $collection->save ();
            }           
            $selectedTab = $post ['selected_tab'];
            if (empty ( $selectedTab )) {
                $selectedTab = "photos";
            }
            return $this->_redirect ( '*/property/form/step/' . $selectedTab );
        } else {
            Mage::getSingleton ( 'core/session' )->addError ( $this->__ ( 'Error' ) );
            $this->_redirect ( '*/*/' );
        }
    }
    /**
     * Function Name: 'showAction'
     * Show all avaiable lists
     */
    public function showAction() {
        /**
         * Load the layout and rendering the layout.
         */
        $this->loadLayout ();
        $this->_initLayoutMessages ( 'catalog/session' );
        /**
         * If not logged in
         */
        if (! Mage::getSingleton ( 'customer/session' )->isLoggedIn ()) {
            $this->_redirectUrl ( Mage::helper ( 'customer' )->getLoginUrl () );
        } else {
            $this->getLayout ()->getBlock ( 'head' )->setTitle ( $this->__ ( 'My Listings' ) );
        }
        $this->renderLayout ();
    }
    /**
     * FUnction Name: deleteAction
     * List all the property which are realted to customer
     */
    public function deleteAction() {
        /**
         * Load the Layout
         */
        $this->loadLayout ();
        /**
         * and rendering the Layout value
         */
        $this->renderLayout ();
        /**
         * owner permission
         */
        $entityId = ( int ) $this->getRequest ()->getParam ( 'id' );
        /**
         * Get the Customer ID
         */
        $customerId = Mage::getSingleton ( 'customer/session' )->getCustomer ()->getId ();
        /**
         * load the Enity Id to get the Colletion
         */
        $collection = Mage::getModel ( 'catalog/product' )->load ( $entityId );
        /**
         * Get the Customer Info
         */
        $userId = $collection->getUserid ();
        /**
         * Check weather the customer Id is same.
         */
        if ($customerId != $userId) {
            /**
             * Add the error Notification
             */
            Mage::getSingleton ( 'core/session' )->addError ( $this->__ ( "Access denied" ) );
            $this->_redirect ( '*/general/show/' );
            return;
        }
        Mage::getModel ( 'airhotels/property' )->deleteProperty ( $entityId );
        /**
         * Property Deleted successfully, redirect to /general/show/.
         */
        Mage::getSingleton ( 'core/session' )->addSuccess ( $this->__ ( "Property Deleted Successfully" ) );
        $this->_redirect ( '*/general/show/' );
        return true;
    }
    /**
     * Function Name: editAction
     * Property Edit action used for editing the uploaded user informations
     */
    public function editAction() {
        /**
         * Load the Layout
         */
        $this->loadLayout ();
        /**
         * Init Layout Messeages.
         */
        $this->_initLayoutMessages ( 'catalog/session' );
        /**
         * get the customerId from the session
         */
        $customerId = Mage::getSingleton ( 'customer/session' )->getCustomer ()->getId ();
        /**
         * check customerId is enable
         */
        if ($customerId) {
            /**
             * Get the layout value
             */
            $this->getLayout ()->getBlock ( 'head' )->setTitle ( $this->__ ( 'Edit your Property' ) );
            /**
             * Rendering the layout
             */
            $this->renderLayout ();
        } else {
            Mage::getSingleton ( 'core/session' )->addError ( $this->__ ( 'You are not currently logged in' ) );
            $this->_redirectUrl ( Mage::helper ( 'customer' )->getLoginUrl () );
        }
        /**
         * get the entity Id from the session
         */
        $entityId = ( int ) $this->getRequest ()->getParam ( 'id' );
        /**
         * Load the entity ID Value
         */
        $collectionVal = Mage::getModel ( 'catalog/product' )->load ( $entityId );
        /**
         * Customer ID Value.
         */
        $Customerid = $collectionVal->getUserid ();
        /**
         * Make sure the '$customerId' and '$Customer_id' are not same.
         */
        if ($customerId != $Customerid) {
            Mage::getSingleton ( 'core/session' )->addError ( $this->__ ( "Access denied" ) );
            $this->_redirect ( '*/property/show/' );
            return;
        }
        if (isset ( $entityId )) {            
            Mage::getSingleton ( 'customer/session' )->setCurrentExperienceId ( $entityId );
            $this->_redirect ( '*/property/form/step/basics' );
            return;
        }
    }
    /**
     * Function Name: formAction
     * Redirect to the customer login page when you click on the List the new space
     */
    public function formAction() {
        Mage::getSingleton ( 'customer/session' )->setBeforeAuthUrl ( Mage::helper ( 'airhotels' )->getformUrl () );
        /**
         * Get the Customer Id for form Saving
         * @var type int
         */
        $customerId = Mage::getSingleton ( 'customer/session' )->getCustomer ()->getId ();
        /**
         * Check weather the Customer ID is correct formAction will be loaded or
         * otherwise show an error Message.
         */
        if ($customerId) {
            /**
             * Get the layout value
             */
            $this->loadLayout ();
            /**
             * Rendering the layout
             */
            $this->_initLayoutMessages ( 'catalog/session' );
            /**
             * Get the block values.
             */
            $this->getLayout ()->getBlock ( 'head' )->setTitle ( $this->__ ( 'Add your Property' ) );
            $this->renderLayout ();
        } else {
            /**
             * Add the Error Notification Values.
             */
            Mage::getSingleton ( 'core/session' )->addError ( $this->__ ( 'You are not currently logged in' ) );
            $this->_redirectUrl ( Mage::helper ( 'customer' )->getLoginUrl () );
        }
    }
    /**
     * Function Name: imageAction
     * set action url for property images
     * @ update /delete image
     */
    public function imageAction() {
        /**
         * Load the layoutVlaue.
         */
        $this->loadLayout ();
        $this->_initLayoutMessages ( 'catalog/session' );
        /**
         * If not logged in
         */
        if (! Mage::getSingleton ( 'customer/session' )->isLoggedIn ()) {
            /**
             * Redirect Url value
             */
            $this->_redirectUrl ( Mage::helper ( 'customer' )->getLoginUrl () );
        } else {
            /**
             * get Layout Value for block
             */
            $this->getLayout ()->getBlock ( 'head' )->setTitle ( $this->__ ( 'My List' ) );
        }
        $customerId = Mage::getSingleton ( 'customer/session' )->getCustomer ()->getId ();
        $entityId = ( int ) $this->getRequest ()->getParam ( 'id' );
        $collection = Mage::getModel ( 'catalog/product' )->load ( $entityId );
        $userId = $collection->getUserid ();
        if ($customerId != $userId) {
            Mage::getSingleton ( 'core/session' )->addError ( $this->__ ( "Access denied" ) );
            $this->_redirect ( '*/property/show/' );
            return;
        }
        if (isset ( $entityId )) {            
            Mage::getSingleton ( 'customer/session' )->setCurrentExperienceId ( $entityId );
            $this->_redirect ( '*/property/form/step/photos' );
            return;
        }
    }
    /**
     * Function Name: imageuploadAction
     * set action for uploading the new property image for existing property
     * @return boolean
     */
    public function imageuploadAction() {
        $this->loadLayout ();
        /**
         * property id
         */
        $entityId = $this->getRequest ()->getParam ( 'id' );
        /**
         * @param int $entity_id            
         * @param array $_FILES            
         */
        $uploadsData = new Zend_File_Transfer_Adapter_Http ();
        $filesDataArray = $uploadsData->getFileInfo ();       
        /**
         * imageupload method will be triggered.
         */
        Mage::getModel ( 'airhotels/calendar' )->imageupload ( $filesDataArray, $entityId );
        $this->renderLayout ();
        $result = array (
                'Success' => "image uploaded successfully" 
        );
        $this->getResponse ()->clearHeaders ()->setHeader ( 'Content-type', 'application/json', true );
        $this->getResponse ()->setBody ( json_encode ( $result ) );
    }
    /**
     * Function Name: yourtripAction
     * Customer trip history page in Dashboard
     */
    public function yourtripAction() {
        /**
         * Load the layout messagaes
         */
        $this->loadLayout ();
        /**
         * Initlayout Messge Value.
         */
        $this->_initLayoutMessages ( 'catalog/session' );
        if (! Mage::getSingleton ( 'customer/session' )->isLoggedIn ()) {
            /**
             * Called the Redirect Url
             */
            $this->_redirectUrl ( Mage::helper ( 'customer' )->getLoginUrl () );
        } else {
            /**
             * Get the head block for "my Trip"
             */
            $this->getLayout ()->getBlock ( 'head' )->setTitle ( $this->__ ( 'My Trips' ) );
        }
        /**
         * Render the Layout Vlaue.
         */
        $this->renderLayout ();
    }
    /**
     * Function Name: albumupdateAction
     * Update album for existing products
     * @return bool
     */
    public function albumupdateAction() {
        /**
         * Get the array of variables
         * @var post
         */
        $post = $this->getRequest ()->getPost ();
        /**
         * Entity id
         */
        $entityId = $this->getRequest ()->getParam ( 'entity_id' );
        /**
         * Image Collection
         */
        $imageCollection = $this->getRequest ()->getParam ( 'imageCollection' );
        /**
         * check the vlaue of param 'remove' not null;
         */
        if ($this->getRequest ()->getParam ( 'remove' ) != "0") {
            $imgForCondition = count ( $imageCollection );
            for($i = 0; $i < $imgForCondition; $i ++) {
                /**
                 * get the ImageColletion Value.
                 */
                if ($imageCollection [$i]) {
                    /**
                     * Remove Image form the Calendar
                     */
                    Mage::getModel ( 'airhotels/calendar' )->removeImage ( $imageCollection [$i], $entityId );
                }
            }
        }
        /**
         * Load the layout and rendering the Layout.
         */
        $this->loadLayout ();
        $this->renderLayout ();
        /**
         * Album Update
         */
        Mage::getModel ( 'airhotels/city' )->albumupdate ( $post );
        /**
         * count the 'imageCollection' Value
         */
        if (count ( $imageCollection )) {
            /**
             * Add the success notification
             */
            Mage::getSingleton ( 'core/session' )->addSuccess ( $this->__ ( 'Image Removed successfully' ) );
            return $this->_redirectUrl ( Mage::getBaseUrl () . 'property/general/image/id/' . $entityId );
        }
        /**
         * Add the success notification
         */
        Mage::getSingleton ( 'core/session' )->addSuccess ( $this->__ ( 'Gallery Updated successfully' ) );
        return $this->_redirectUrl ( Mage::helper ( 'airhotels/product' )->getshowlisturl () );
    }
    /**
     * Function Name: 'newalbumupdateAction'
     * Newly added products image upload functionality
     * @return bool
     */
    public function newalbumupdateAction() {
        /**
         * Get the array of variables
         * @var $postVal
         */
        $postVal = $this->getRequest ()->getPost ();
        /**
         * Get the entity_id
         */
        $entityIdVal = $this->getRequest ()->getParam ( 'entity_id' );
        /**
         * Check weather the Value is set
         */
        $imageCollections = $this->getRequest ()->getParam ( 'imageCollection' );
        if ($this->getRequest ()->getParam ( 'remove' ) != "0") {
            /**
             * Count the 'imageColetions' Vlaue.
             */
            $imgForConditionVal = count ( $imageCollections );
            for($i = 0; $i < $imgForConditionVal; $i ++) {
                if ($imageCollections [$i]) {
                    /**
                     * Remove Image
                     */
                    Mage::getModel ( 'airhotels/calendar' )->removeImage ( $imageCollections [$i], $entityIdVal );
                }
            }
        }
        /**
         * Load the layout and render the layout Vlaue.
         */
        $this->loadLayout ();
        $this->renderLayout ();
        Mage::getModel ( 'airhotels/city' )->albumupdate ( $postVal );
        /**
         * Get the property Approval Value
         */
        $propertyApproval = Mage::getStoreConfig ( 'airhotels/custom_email/property_approval' );
        if ($propertyApproval) {
            Mage::getModel ( 'airhotels/property' )->adminApproval ( $entityIdVal );
            Mage::getSingleton ( 'core/session' )->addNotice ( $this->__ ( "Property details hosted is awaiting admin's approval" ) );
            return $this->_redirectUrl ( Mage::helper ( 'airhotels/product' )->getshowlisturl () );
        } else {
            Mage::getModel ( 'airhotels/property' )->newProperty ( $entityIdVal );
        }
        /**
         * Add the notification
         */
        Mage::getSingleton ( 'core/session' )->addNotice ( $this->__ ( 'Property is Published' ) );
        return $this->_redirectUrl ( Mage::helper ( 'airhotels/product' )->getshowlisturl () );
    }
    /**
     * Function Name: reviewPageAction
     * Review Page Action for Reviewing the Property Reviews
     */
    public function reviewPageAction() {
        /**
         * Get the param Page
         */
        $page = $this->getRequest ()->getParam ( 'page' );
        /**
         * Set the Product Id Vlaue.
         */
        $productId = $this->getRequest ()->getParam ( 'product' );
        /**
         * Day wise addStoreFilter blocked date details
         */
        $reviews = Mage::getModel ( 'review/review' )->getResourceCollection ();
        /**
         * Day wise $htmlValue blocked date details
         * with adding the filter
         * 'product'
         * setDateOrder
         * setPageSize Value
         */
        $reviews->addStoreFilter ( Mage::app ()->getStore ()->getId () )->addStatusFilter ( Mage_Review_Model_Review::STATUS_APPROVED )->addEntityFilter ( 'product', $productId )->setDateOrder ()->addRateVotes ()->setPageSize ( 4 )->setCurPage ( $page )->load ();
        $reviews = $reviews->getData ();
        /**
         * Day wise $htmlValue blocked date details
         */
        if (count ( $reviews )) {
            /**
             * Count the Values of Reviews
             * @var int
             */
            $reviewCountCondition = count ( $reviews );
            for($i = 0; $i < $reviewCountCondition; $i ++) {
                $customerData = Mage::getModel ( 'airhotels/product' )->getCustomerPictureById ( $reviews [$i] ["customer_id"] );
                $htmlValue .= '<div class="review-product">';
                $htmlValue .= '<ul>';
                $htmlValue .= ' <li class="yourlist_img floatleft" >';
                $htmlValue .= ' <a href="' . Mage::helper ( 'airhotels/product' )->getprofilepage () . 'id/' . $reviews [$i] ["customer_id"] . '" >';
                if ($customerData [0] ["imagename"]) {
                    $htmlValue .= '<img src="' . Mage::getBaseUrl ( 'media' ) . "catalog/customer/thumbs/" . $customerData [0] ["imagename"] . '"; style="width: 63px !important; height: 53px !important" alt="">';
                } else {
                    $htmlValue .= ' <img src="' . Mage::getBaseUrl ( 'skin' ) . 'frontend/default/stylish/images/no_user.jpg' . '" style="width: 63px !important; height: 53px !important" alt="">';
                }
                $htmlValue .= '</a>';
                $htmlValue .= '<ol class="nick_name">' . $reviews [$i] ["nickname"] . '</ol>';
                $htmlValue .= '</li>';
                $htmlValue .= '<li class="review_comment_grid">';
                $htmlValue .= '<div class="review-content bubble"><span style="font-weight:bold;font-size:14px;">"</span>' . nl2br ( $reviews [$i] ["detail"] ) . '<span style="font-weight:bold;font-size:14px;">"</span>';
                $htmlValue .= '<div> - ' . $reviews [$i] ["nickname"] . ", " . date ( "jS, F Y", strtotime ( $reviews [$i] ["created_at"] ) ) . ' </div>';
                $htmlValue .= '</div></li></ul></div>';
            }
            /**
             * Getting total count
             */
            $reviewsTotal = Mage::getModel ( 'review/review' )->getResourceCollection ();
            /**
             * Add filter to colletion
             * setDateOrder
             * addRateVotes
             * and load the values.
             */
            $reviewsTotal->addStoreFilter ( Mage::app ()->getStore ()->getId () )->addStatusFilter ( Mage_Review_Model_Review::STATUS_APPROVED )->addEntityFilter ( 'product', $productId )->setDateOrder ()->addRateVotes ()->load ();
            $totalRecords = count ( $reviewsTotal );
            /**
             * Day wise $htmlValue blocked date details
             */
            if ($page > 1) {
                $htmlValue .= "<a class='paginationClass' href='javascript:void(0);' onclick='getPagination(\"1\",\"$productId\")' >" . $this->__ ( 'First' ) . "</a>";
            }
            /**
             * Day wise $ceilForCondition blocked date details
             */
            $ceilForCondition = ceil ( $totalRecords / 4 );
            for($i = 1; $i <= $ceilForCondition; $i ++) {
                if ($i == $page) {
                    $htmlValue .= "<a class='paginationClass currentpaginationClass'  href='javascript:void(0);'>" . $i . "</a>";
                } else {
                    $htmlValue .= "<a class='paginationClass' href='javascript:void(0);' onclick='getPagination(\"$i\",\"$productId\")' >" . $i . "</a>";
                }
            }
            /**
             * Day wise $totalRecords blocked date details
             */
            if (ceil ( $totalRecords / 4 ) > $page) :
                $lastMsgValue = $this->__ ( 'Last' );
                $htmlValue .= "<a class='paginationClass' href='javascript:void(0);' onclick='getPagination(\"1\",\"$productId\")' >" . $lastMsgValue . "</a>";
            endif;
           } else {
            /**
             * Html Vlaue.
             */
            $htmlValue = $this->__ ( 'There are no reviews yet for this product. Be the first to write a review' );
        }
        $this->getResponse ()->setBody ( $htmlValue );
    }
    /**
     * Function Name":wishlistAction"
     * To load layout for showing Wish list page
     */
    public function wishlistAction() {
        /**
         * Check wether the customer is logged in or not
         */
        if (! Mage::getSingleton ( 'customer/session' )->isLoggedIn ()) {
            $this->_redirectUrl ( Mage::helper ( 'customer' )->getLoginUrl () );
        }
        /**
         * Load the Layout
         */
        $this->loadLayout ();
        /**
         * Get the customer
         */
        $customer = Mage::getSingleton ( 'customer/session' )->getCustomer ();
        /**
         * Get the Customer Name
         */
        $customerName = $customer->getName ();
        /**
         * Get the layout and and get the Block of values
         */
        $this->getLayout ()->getBlock ( 'head' )->setTitle ( $this->__ ( 'My Wish List' ) . '-' . $customerName );
        $this->renderLayout ();
    }
    /**
     * FUnction Name: popularAction
     * To load layout for showing the popular property details
     */
    public function popularAction() {
        $this->loadLayout ();
        $this->renderLayout ();
    }
    /**
     * Function Name: cancelorderAction
     * Cancel order
     */
    public function cancelorderAction() {
        $this->loadLayout ();
        $this->renderLayout ();
        /**
         * Get param of order id
         */
        $orderId = ( int ) $this->getRequest ()->getParam ( 'orderid' );
        Mage::getModel ( 'airhotels/property' )->cancelOrder ( $orderId );
        Mage::getSingleton ( 'core/session' )->addSuccess ( $this->__ ( "Your Request for Cancellation has been submitted successfully!" ) );
        /**
         * Redirect
         */
        $this->_redirect ( 'property/general/yourtrip/' );
        return true;
    }
    /**
     * Function Name:'statusAction'
     * Status of the property
     */
    public function statusAction() {
        /**
         * Getting the status
         */
        $status = $this->getRequest ()->getParam ( 'status' );
        /**
         * product ID
         */
        $productId = Mage::app ()->getRequest ()->getParam ( 'productid' );
        $status = Mage::getModel ( 'airhotels/airhotels' )->status ( $status, $productId );
        /**
         * Based on the status value setBody
         */
        if ($status) {
            /**
             * Get the responce and sent to body as 'Available'
             */
            $this->getResponse ()->setBody ( "Available" );
        } else {
            /**
             * Get the responce and sent to body as 'NotAvailable'
             */
            $this->getResponse ()->setBody ( "NotAvailable" );
        }
    }
    /**
     * Function Name: 'reviewAction'
     * The Review Action for adding reviews to properties
     */
    public function reviewAction() {
        /**
         * Owner permission
         */
        $this->redirectForOwnerPermission ();
        /**
         * load the Layout Vlaue.
         */
        $this->loadLayout ();
        /**
         * Setting block title
         */
        $this->getLayout ()->getBlock ( 'head' )->setTitle ( $this->__ ( 'Review' ) );
        /**
         * Render the Layout Vlaue.
         */
        $this->renderLayout ();
    }
    /**
     * Function Name: 'redirectForOwnerPermission'
     * Getting the redirect Permission
     */
    public function redirectForOwnerPermission() {
        /**
         * Set the customer ID
         */
        $customerIdVal = Mage::getSingleton ( 'customer/session' )->getCustomer ()->getId ();
        /**
         * Entity Id Value.
         */
        $entityIdVal = ( int ) Mage::app ()->getRequest ()->getParam ( 'id' );
        /**
         * Colletion Val
         */
        $collectionVal = Mage::getModel ( 'catalog/product' )->load ( $entityIdVal );
        /**
         * Customer ID Vlaue.
         */
        $customerIdValues = $collectionVal->getUserid ();
        /**
         * Redirect to show
         */
        if ($customerIdVal != $customerIdValues) {
            /**
             * Adding error session message
             */
            Mage::getSingleton ( 'core/session' )->addError ( $this->__ ( "Access denied" ) );
            /**
             * Redirect to show controller
             */
            $this->_redirect ( '*/general/show/' );
            return;
        }
    }
}