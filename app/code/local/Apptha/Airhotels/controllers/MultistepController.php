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
 * Class : Apptha_Airhotels_MultistepController
 * extends from Mage_Core_Controller_Front_Action
 * 
 * @author user
 *        
 */
class Apptha_Airhotels_MultistepController extends Mage_Core_Controller_Front_Action {
    
    /**
     * Function Name: PhotosaveAction
     * PhotosSaveAction - save the experience images
     */
    public function PhotosaveAction() {
        /**
         * Check sutomer login details.
         */
        if (! Mage::getSingleton ( 'customer/session' )->isLoggedIn ()) {
            /**
             * Redirect to login url.
             */
            $this->_redirectUrl ( Mage::helper ( 'customer' )->getLoginUrl () );
            return false;
        }
        /**
         * Check condition If customer is logged in or not
         *
         * @var $post
         * @var $entityId
         * @var $imageCollection
         */
        $post = $this->getRequest ()->getPost ();
        $entityId = $this->getRequest ()->getParam ( 'entity_id' );
        $imageCollection = $this->getRequest ()->getParam ( 'imageCollection' );
        /**
         * Remove function for Image
         */
        if ($this->getRequest ()->getParam ( 'remove' ) != "0") {
            $imageForCondition = count ( $imageCollection );
            for($i = 0; $i < $imageForCondition; $i ++) {
                if ($imageCollection [$i]) {
                    /**
                     * Remove Image.
                     */
                    Mage::getModel ( 'airhotels/calendar' )->removeImage ( $imageCollection [$i], $entityId );
                }
            }
        }
        /**
         * Album update for city images
         */
        Mage::getModel ( 'airhotels/city' )->albumupdate ( $post );
        /**
         * based on the ID redirect to "Profile"
         */
        if (count ( $imageCollection )) {
            return $this->_redirect ( '*/property/form/step/profile' );
        }
        $selectedTab = $post ['selected_tab'];
        /**
         * Check condition if $selectedTab is empty or not
         */
        if (empty ( $selectedTab )) {
            $selectedTab = "profile";
        }
        /**
         * Redirect to basic action page.
         */
        return $this->_redirect ( '*/property/form/step/' . $selectedTab );
    }
    
    /**
     * Function Name: photoDeleteAction
     * photoDeleteAction - delete the experience image
     *
     * @var $imageId
     * @var $entityId
     * @var $albumCover
     */
    public function photoDeleteAction() {
        /**
         * Get customer login details.
         */
        if (! Mage::getSingleton ( 'customer/session' )->isLoggedIn ()) {
            /**
             * Redirect to customer login.
             */
            $this->_redirectUrl ( Mage::helper ( 'customer' )->getLoginUrl () );
            return false;
        }
        /**
         * Get ImageId.
         * Get Entity Id.
         * Get album cover.
         */
        $imageId = $this->getRequest ()->getParam ( "image_id" );
        $entityId = $this->getRequest ()->getParam ( "entity_id" );
        $albumCover = $this->getRequest ()->getParam ( "albumCover" );
        /**
         * Check condition if $imageId exist or not
         */
        if ($imageId) {
            /**
             * Remove image.
             */
            Mage::getModel ( 'airhotels/calendar' )->removeImage ( $imageId, $entityId, $albumCover );
        } else {
            /**
             * Set error message.
             */
            Mage::getSingleton ( 'core/session' )->addError ( $this->__ ( 'Please try again!' ) );
        }
        /**
         * Set redirect url.
         */
        $this->_redirect ( '*/property/form/step/photos' );
    }
    /**
     * Function Name: ProfilesaveAction
     * profileSaveAction - save the host profile information
     *
     * @var $getImage
     * @var $data
     * @var $post
     */
    public function ProfilesaveAction() {
        ini_set ( 'post_max_size', '60M' );
        if (! Mage::getSingleton ( 'customer/session' )->isLoggedIn ()) {
            /**
             * Set redirect url.
             */
            $this->_redirectUrl ( Mage::helper ( 'customer' )->getLoginUrl () );
            return false;
        }
        /**
         * get the Param Value of "crop"
         */
        $getImage = $this->getRequest ()->getParam ( 'crop' );
        $data = $getImage ['image'];
        $post = $this->getRequest ()->getPost ();
        /**
         * Get file transfer
         * Function Zend_File_Transfer_Adapter_Http(0
         *
         * @var unknown
         */
        $uploadsData = new Zend_File_Transfer_Adapter_Http ();
        $filesDataArray = $uploadsData->getFileInfo ();
        /**
         * Get customer details.
         */
        $customer = Mage::getSingleton ( 'customer/session' )->getCustomer ();
        /**
         * Get customerId
         * Get current ExperianceId.
         */
        $customerId = $customer->getId ();
        $currentExperienceId = Mage::getSingleton ( 'customer/session' )->getCurrentExperienceId ();       
        $product = Mage::getModel ( 'catalog/product' )->load ( $currentExperienceId );
        Mage::app ()->setCurrentStore ( Mage_Core_Model_App::ADMIN_STORE_ID );
        $product->save ();
        /**
         * Set default store value as Zero
         */
        Mage::app ()->setCurrentStore ( 0 );
        /**
         * Get customer photo collection.
         */
        $collection = Mage::getModel ( 'airhotels/customerphoto' )->load ( $customerId, 'customer_id' );
        /**
         * Get customerId.
         * Get video link.
         */
        $getId = $collection->getId ();
        $videoUrl = $post ['video_link'];
        /**
         * Check weather the profile video is set
         */
        if ($filesDataArray ['profilevideo'] ['name'] != "") {
            try {
                /**
                 * Get video url.
                 */
                $videoUrl = Mage::helper ( 'airhotels/url' )->uploadProfileVideo ( 'profilevideo', $filesDataArray );
            } catch ( Exception $e ) {
                /**
                 * Display error message for video upload
                 */
                Mage::getSingleton ( 'core/session' )->addError ( $this->__ ( $e->getMessage () ) );
                return $this->_redirect ( '*/property/form/step/profile/' );
            }
        }
        /**
         * Make sure the image has been set.
         */
        if (! empty ( $data )) {
            Mage::getModel ( 'airhotels/customerphoto' )->updateProfilePicture ( $data );
        }
        if ($getId) {
            /**
             * Get customer collection.
             * Set customerId
             * Set hostname.
             * Set host city.
             * Set host country
             * set host email.
             */
            $collection = Mage::getModel ( 'airhotels/customerphoto' )->load ( $customerId, 'customer_id' );
            $collection->setCustomerId ( $customerId );
            $collection->setName ( $post ['host_name'] );
            $collection->setCity ( $post ['host_city'] );
            $collection->setCountry ( $post ['host_country'] );
            $collection->setEmailId ( $post ['email'] );
            /**
             * Set customer contact,timezone.
             */
            if (isset ( $post ['contact'] )) {
                $collection->setContactNumber ( $post ['contact'] );
            }
            $collection->setTimeZone ( $post ['time_zone'] );
            $collection->setVideoUrl ( $videoUrl );
            /**
             * Set notification flag.
             */
            if (isset ( $post ['notification'] )) {
                if ($post ['notification'] == 'on') {
                    $collection->setNotification ( 1 );
                } else {
                    /**
                     * Load newsletter email.
                     */
                    $collection->setNotification ( '' );
                    Mage::getModel ( 'newsletter/subscriber' )->loadByEmail ( $customer->getEmail () )->unsubscribe ();
                }
            }
            $collection->setMoreHost ( $post ['moreabouthost'] );
            $collection->save ();
            if (! empty ( $filesDataArray ['profilePhoto'] ['name'] )) {
                /**
                 * Update profile picture.
                 */
                Mage::getModel ( 'airhotels/customerphoto' )->updateProfilePicture ();
            }
        } else {
            /**
             * Get customer photo collection.
             */
            $collection = Mage::getModel ( 'airhotels/customerphoto' );
            $collection->setCustomerId ( $customerId );
            $collection->setName ( $post ['host_name'] );
            $collection->setCountry ( $post ['host_country'] );
            $collection->setEmailId ( $post ['email'] );
            $collection->setContactNumber ( $post ['contact'] );
            $collection->setTimeZone ( $post ['time_zone'] );
            $collection->setVideoUrl ( $videoUrl );
            $collection->setCity ( $post ['host_city'] );
            if ($post ['notification'] == 'on') {
                $collection->setNotification ( 1 );
            }
            $collection->setMoreHost ( $post ['moreabouthost'] );
            $collection->setVideoVerified ( 0 );
            $collection->setDocumentVerified ( 0 );
            $collection->save ();
            if (! empty ( $filesDataArray ['profilePhoto'] ['name'] )) {
                Mage::getModel ( 'airhotels/customerphoto' )->updateProfilePicture ();
            }
        }
        $selectedTab = $post ['selected_tab'];
        if (empty ( $selectedTab )) {
            $selectedTab = "publish";
        }
        return $this->_redirect ( '*/property/form/step/' . $selectedTab );
    }
    /**
     * Send code to registered users.
     *
     * @throws Exception
     * @return boolean
     */
    public function sendcodeAction() {
        $returnValue = true;
        /**
         * Get Nexmo text verification content
         * 
         * @var $verificationContent
         */
        $verificationContent = Mage::getStoreConfig ( 'airhotels/nexmo/nexmo_text' );
        /**
         * Get parameters.
         */
        $arrayParams = $this->getRequest ()->getParams ();
        if (isset ( $arrayParams ['phonenumber'] )) {
            if (! isset ( $_SESSION )) {
                /**
                 * Session start.
                 */
                session_start ();
            }
            /**
             * Get country code.
             * Get phone number
             * Get phone country code.
             */
            $isdCode = $arrayParams ['countrycode'];
            $number = $arrayParams ['phonenumber'];
            $isdNumber = $isdCode . $arrayParams ['phonenumber'];
            $phoneCountryCode = $arrayParams ['phoneCountryCode'];
            /**
             * Random 4 digit code
             */
            $code = rand ( 1000, 9999 );
            /**
             * Store code for later
             */
            $_SESSION ['code'] = $code;
            $result = Mage::helper('airhotels/smsconfig')->sendMessage($isdCode,$isdNumber,$verificationContent . ': ' . $code,$code);
            /**
             * Some error checking
             */
            $data = json_decode ( $result, true );
            if (! isset ( $data ['messages'] )) {
                /**
                 * Set API response error.
                 */
                $this->getResponse ()->setBody ( 'Unknown API Response' );
                return $returnValue;
            }
            if ($data ['messages'] [0] ['status'] == 2) {
                /**
                 * Set configuration error message.
                 * Set response message in html body.
                 */
                $message = 'API Configuration Error';
                $this->getResponse ()->setBody ( $message );
                return $returnValue;
            }
            if (! isset ( $arrayParams ['payout'] )) {
                /**
                 * Get customer details.
                 * Get customerId.
                 */
                $customerData = Mage::getSingleton ( 'customer/session' )->getCustomer ();
                $customerId = $customerData->getId ();
                $dataCustomerArray = array (
                        'contact_number' => $number,
                        "isd_code" => $isdCode,
                        "country_code" => $phoneCountryCode 
                );
                /**
                 * Serialize the customer phone number.
                 */
                $dataCustomerArray = serialize ( $dataCustomerArray );
                $customerPhoneNumber = array (
                        "telephone" => $isdNumber 
                );
                /**
                 * Get collection of customer photo.
                 * Save customer phone number.
                 */
                $customerDetails = Mage::getModel ( 'airhotels/customerphoto' )->load ( $customerId )->setContactNumber ( $dataCustomerArray )->setmobileVerifiedProfile ( '' );
                $customerDetails->setId ( $customerId )->save ();
                /**
                 * Set customer address.
                 */
                $customAddress = Mage::getModel ( 'customer/address' );
                $customAddress->setData ( $customerPhoneNumber )->setCustomerId ( $customerId )->setSaveInAddressBook ( '1' );
                try {
                    /**
                     * Customer address save.
                     */
                    $customAddress->save ();
                } catch ( Exception $ex ) {
                    $returnValue = false;
                }                
            }
            foreach ( $data ['messages'] as $message ) {
                if ($message ['status'] != 0) {
                    /**
                     * Set error response.
                     */
                    $this->getResponse ()->setBody ( 'API Error: ' . $message ['error-text'] );
                    return $returnValue;
                } else {
                    /**
                     * Set success message.
                     */
                    echo "success";
                }
            }
        }
    }
    /**
     * Function Name: codeverificationAction
     * Code Verification
     *
     * @var $customerData
     * @var $customerId
     * @var $customerDetails
     */
    public function codeverificationAction() {
        $arrayParams = $this->getRequest ()->getParams ();
        /**
         * Get customer details.
         * Get customer id.
         */
        $customerData = Mage::getSingleton ( 'customer/session' )->getCustomer ();
        $customerId = $customerData->getId ();
        if (isset ( $arrayParams ['code'] )) {
            if (! isset ( $_SESSION )) {
                /**
                 * Session start.
                 */
                session_start ();
            }
            /**
             * Check condition if session code and
             * nexmo code is same or not
             */
            if ($_SESSION ['code'] == $arrayParams ['code']) {
                $text = 1;
                if (! isset ( $arrayParams ['payout'] )) {
                    /**
                     * Set mobile verification flag.
                     */
                    $data = array (
                            'mobile_verified_profile' => 'verified' 
                    );
                } else {
                    /**
                     * Set payment verification flag.
                     */
                    $data = array (
                            'mobile_verified_payment' => 'verified' 
                    );
                }
                /**
                 * Get customer details from customerphoto collection
                 *
                 * @var $customerId
                 * @var unknown
                 */
                $customerDetails = Mage::getModel ( 'airhotels/customerphoto' )->load ( $customerId )->addData ( $data );
                $customerDetails->setId ( $customerId )->save ();
            } else {
                /**
                 * Set text as 0.
                 * 
                 * @var $text
                 */
                $text = 0;
            }
        }
        echo $text;
    }
    /**
     * Function Name: ViewprofileAction
     * ViewprofileAction - Display the host profile for every users.
     *
     * rendering load and render layout
     */
    public function ViewprofileAction() {
        /**
         * Load layout.
         */
        $this->loadLayout ();
        /**
         * Set page title.
         */
        $this->getLayout ()->getBlock ( 'head' )->setTitle ( $this->__ ( 'View Profile' ) );
        $this->renderLayout ();
    }
    
    /**
     * Function Name: verificationAction
     * Trust and verification - Verify host details
     *
     * rendering load and render layout
     */
    public function verificationAction() {
        $this->loadLayout ();
        $this->renderLayout ();
    }
    /**
     * Function Name: reviewPageAction
     * Display review details
     *
     * @var $productid
     * @var $review
     * @var $customerData
     */
    public function reviewPageAction() {
        $productid = $this->getRequest ()->getParam ( 'product' );
        /**
         * Get All review collection
         * 
         * @var unknown
         */
        $review = Mage::getModel ( 'review/review' )->getResourceCollection ();
        $review->addStoreFilter ( Mage::app ()->getStore ()->getId () )->addStatusFilter ( Mage_Review_Model_Review::STATUS_APPROVED )->addEntityFilter ( 'product', $productid )->setDateOrder ()->addRateVotes ()->load ();
        $review = $review->getData ();
        $htmlValue = "";
        if (count ( $review )) {
            /**
             * Get review count.
             */
            $reviewCountCondition = count ( $review );
            for($i = 0; $i < $reviewCountCondition; $i ++) {
                $customerData = Mage::getModel ( 'airhotels/product' )->getCustomerPictureById ( $review [$i] ["customer_id"] );
                $htmlValue .= '<div id="reviewrapper">';
                $htmlValue .= '<div class="review-product">';
                $htmlValue .= '<ul>';
                $htmlValue .= ' <li>';
                $htmlValue .= ' <a class="customer_img" href="' . Mage::helper ( 'airhotels/product' )->getprofilepage () . 'id/' . $review [$i] ["customer_id"] . '" >';
                /**
                 * Day wise $customerData blocked date details
                 */
                if ($customerData [0] ["imagename"]) {
                    $htmlValue .= '<img src="' . Mage::getBaseUrl ( 'media' ) . "catalog/customer/thumbs/" . $customerData [0] ["imagename"] . '"; alt="">';
                } else {
                    $htmlValue .= ' <img src="' . Mage::getBaseUrl ( 'skin' ) . 'frontend/default/stylish/images/no_user.jpg' . '" alt="">';
                }
                $htmlValue .= '</a>';
                $htmlValue .= '<div class="review_content_wrapper"><p class="nick_name">' . $review [$i] ["nickname"] . '</p>';
                $htmlValue .= '<p class="reviews_title">' . nl2br ( $review [$i] ["title"] ) . '</p>';
                $htmlValue .= '<p class="reviews_detail">' . nl2br ( $review [$i] ["detail"] ) . '</p>';
                $htmlValue .= '<p class="reviews_created">' . $review [$i] ["nickname"] . ", " . date ( "jS, F Y", strtotime ( $review [$i] ["created_at"] ) ) . '</p>';
                $htmlValue .= '</div></li></ul></div></div>';
            }
        } else {
            /**
             * Display message in produt detail page
             * 
             * @var unknown
             *
             * @return $htmlValue
             */
            $message = $this->__ ( 'There are no reviews yet for this product. Be the first to write a review' );
            $htmlValue = $htmlValue . $message;
        }
        /**
         * Set html response.
         */
        $this->getResponse ()->setBody ( $htmlValue );
    }
    
    /**
     * Function Name: publishSaveAction
     * publishSaveAction - This action for publish the experience
     *
     * @var $customerId
     * @var $propertyApproval
     */
    public function publishSaveAction() {
        /**
         * Get the entity_id
         */
        Mage::getSingleton ( 'customer/session' )->setBeforeAuthUrl ( Mage::helper ( 'airhotels' )->getformUrl () );
        /**
         * Get customer id from session
         * 
         * @var unknown
         */
        $customerId = Mage::getSingleton ( 'customer/session' )->getCustomer ()->getId ();
        if ($customerId) {
            if (Mage::getSingleton ( 'customer/session' )->getCurrentExperienceId ()) {
                /**
                 * Get experience id
                 * 
                 * @var $currentExperienceId
                 */
                $currentExperienceId = Mage::getSingleton ( 'customer/session' )->getCurrentExperienceId ();                
                /**
                 * Get the property Approval Value
                 */
                $product = Mage::getModel ( 'catalog/product' )->load ( $currentExperienceId );
                $propertyApproval = Mage::getStoreConfig ( 'airhotels/custom_email/property_approval' );                  
                if ($propertyApproval && $product['propertyapproved'] != 1) {
                    /**
                     * Set notice message for admin approval.
                     */
                    Mage::getModel ( 'airhotels/property' )->adminApproval ( $currentExperienceId );
                    Mage::getSingleton ( 'core/session' )->addNotice ( $this->__ ( "Property details hosted is awaiting admin's approval" ) );
                    /**
                     * Redirect to list url action.
                     */
                    return $this->_redirectUrl ( Mage::helper ( 'airhotels/product' )->getshowlisturl () );
                } else {
                    /**
                     * Add new proerty.
                     */
                    Mage::getModel ( 'airhotels/property' )->newProperty ( $currentExperienceId );
                }                
                /**
                 * setPropertyapproved(0) - Experience approve status is no,setBookingapproved(1) - Experience approve status is yes.
                 */
                $product->setPropertyapproved ( 1 );
                Mage::app ()->setCurrentStore ( Mage_Core_Model_App::ADMIN_STORE_ID );
                $product->save ();
                /**
                 * setPropertyapproved(0) - Experience approve status is no,setBookingapproved(1) - Experience approve status is yes.
                 */
                Mage::app ()->setCurrentStore ( 0 );
                Mage::getSingleton ( 'customer/session' )->unsCurrentExperienceId ();
                /**
                 * Set property added success message.
                 * Redirect to list url action.
                 */
                Mage::getSingleton ( 'core/session' )->addSuccess ( $this->__ ( 'Experience has been published' ) );
                return $this->_redirectUrl ( Mage::helper ( 'airhotels/product' )->getshowlisturl () );
            }
        } else {
            /**
             * Set login error messages.
             * Redirect to login url.
             */
            Mage::getSingleton ( 'core/session' )->addError ( $this->__ ( 'You are not currently logged in' ) );
            $this->_redirectUrl ( Mage::helper ( 'customer' )->getLoginUrl () );
        }
    }
    /**
     * Fuction _getWishlist
     *
     * @return Ambigous <mixed, NULL, multitype:>|boolean|unknown
     */
    protected function _getWishlist() {
        /**
         * Get whislist.
         */
        $wishlist = Mage::registry ( 'wishlist' );
        if ($wishlist) {
            /**
             * Return whishlist.
             */
            return $wishlist;
        }
        try {
            /**
             * Load wishlist based on customer
             * 
             * @var $wishlist
             */
            $wishlist = Mage::getModel ( 'wishlist/wishlist' )->loadByCustomer ( Mage::getSingleton ( 'customer/session' )->getCustomer (), true );
            Mage::register ( 'wishlist', $wishlist );
        } catch ( Mage_Core_Exception $e ) {
            /**
             * Set error message.
             */
            Mage::getSingleton ( 'wishlist/session' )->addError ( $e->getMessage () );
            Mage::getSingleton ( 'wishlist/session' )->addException ( $e, Mage::helper ( 'wishlist' )->__ ( 'Cannot create wishlist.' ) );
            return false;
        }
        return $wishlist;
    }
    /**
     * Function addAction()
     * Add wishlist using ajax
     *
     * @var $response
     * @var v
     */
    public function addAction() {
        $response = array ();
        /**
         * Check admin configuration for whislist.
         */
        if (! Mage::getStoreConfigFlag ( 'wishlist/general/active' )) {
            /**
             * Set response error.
             */
            $response ['status'] = 'ERROR';
            $response ['message'] = $this->__ ( 'Wishlist Has Been Disabled By Admin' );
        }
        if (! Mage::getSingleton ( 'customer/session' )->isLoggedIn ()) {
            /**
             * Set login error message.
             */
            $response ['status'] = 'NOTLOGGED';
            $response ['message'] = $this->__ ( 'Please Login First' );
        }
        
        if (empty ( $response )) {
            /**
             * Load wishlist based on customer
             * 
             * @var $wishlist
             */
            $wishlist = $this->_getWishlist ();
            if (! $wishlist) {
                /**
                 * Set error meesgae.
                 */
                $response ['status'] = 'ERROR';
                $response ['message'] = $this->__ ( 'Unable to Create Wishlist' );
            } else {
                /**
                 * Get product id.
                 * 
                 * @var $productId
                 */
                $productId = ( int ) $this->getRequest ()->getParam ( 'product' );
                $requestParams = $this->getRequest ()->getParams ();
                $response = Mage::getModel ( 'airhotels/search' )->wishlistAdd ( $requestParams, $wishlist, $productId );
                $this->loadLayout ();
                $toplink = $this->getLayout ()->getBlock ( 'top.links' )->toHtml ();
                /**
                 * Load whislist_sidebar block.
                 */
                $sidebar_block = $this->getLayout ()->getBlock ( 'wishlist_sidebar' );
                $sidebar = $sidebar_block->toHtml ();
                $response ['toplink'] = $toplink;
                $response ['sidebar'] = $sidebar;
            }
        }
        /**
         * Set response in body.
         */
        $this->getResponse ()->setBody ( Mage::helper ( 'core' )->jsonEncode ( $response ) );
        return;
    }
    /**
     * Function removewishlistAction()
     * Remove wishlist using ajax
     *
     * @var $wishlistId
     * @var $response
     * @var $message
     */
    public function removewishlistAction() {
        $response = array ();
        try {
            /**
             * Get whislistId.
             */
            $wishlistId = $this->getRequest ()->getParam ( 'id' );
            $product = Mage::getModel ( 'catalog/product' )->load ( $wishlistId );
            /**
             * Delte property from whislist.
             */
            Mage::getModel ( 'wishlist/item' )->load ( $wishlistId, 'product_id' )->delete ();
            /**
             * Set success meesage
             */
            $response ['status'] = 'SUCCESS';
            $message = $this->__ ( '%1$s has been removed from wishlist.', $product->getName (), $referer );
            $response ['message'] = $message;
        } catch ( Mage_Core_Exception $e ) {
            /**
             * Set error message.
             */
            $response ['status'] = 'ERROR';
        }
        /**
         * Encode message response
         */
        $this->getResponse ()->setBody ( Mage::helper ( 'core' )->jsonEncode ( $response ) );
        return;
    }
}