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
 * Class Apptha_Airhotels_DashboardController
 *
 * Extend from Mage_Core_Controller_Front_Action
 * 
 * @author user
 *        
 */
class Apptha_Airhotels_DashboardController extends Mage_Core_Controller_Front_Action {
    /**
     * Function Name: reviewpageAction
     * Rendering load layout
     */
    public function reviewpageAction() {
        $this->loadLayout ();
        $this->_initLayoutMessages ( 'catalog/session' );
        /**
         * if not logged in
         */
        if (! Mage::getSingleton ( 'customer/session' )->isLoggedIn ()) {
            /**
             * Redirect to customer login page.
             */
            $this->_redirectUrl ( Mage::helper ( 'customer' )->getLoginUrl () );
        } else {
            /**
             * Set page title.
             */
            $this->getLayout ()->getBlock ( 'head' )->setTitle ( $this->__ ( 'Reviews' ) );
        }
        $this->renderLayout ();
    }
    /**
     * Function propertyrequestAction()
     * It will show the list of request property for host.
     */
    public function propertyrequestAction() {
        /**
         * Rendering load layout
         */
        $this->loadLayout ();
        $this->_initLayoutMessages ( 'catalog/session' );
        /**
         * If not logged in
         */
        if (! Mage::getSingleton ( 'customer/session' )->isLoggedIn ()) {
            /**
             * Set login redirect url.
             */
            $this->_redirectUrl ( Mage::helper ( 'customer' )->getLoginUrl () );
        } else {
            /**
             * Set page title.
             */
            $this->getLayout ()->getBlock ( 'head' )->setTitle ( $this->__ ( 'Property Request' ) );
        }
        /**
         * Render layout.
         */
        $this->renderLayout ();
    }
    /**
     * Function Name: notificationsAction()
     *
     * Render the layout which is host notification section.
     *
     * @var $getCollection
     * @var $customerId
     * @var $notificationsModel
     * @var $data
     */
    public function notificationsAction() {
        Mage::getSingleton ( 'customer/session' )->setBeforeAuthUrl ( Mage::helper ( 'airhotels' )->getformUrl () );
        $customerId = Mage::getSingleton ( 'customer/session' )->getCustomer ()->getId ();
        if ($customerId) {
            /**
             * Get Notification collection
             * 
             * @var unknown $getCollection
             */
            $getCollection = Mage::getModel ( 'airhotels/notifications' )->getCollection ()->addFieldToFilter ( "user_id", $customerId );
            if (! $getCollection->getData ()) {
                $data = array (
                        "user_id" => $customerId,
                        "inbox" => "on",
                        "recieve_request" => "on",
                        "response_request" => "on",
                        "account_listing" => "on" 
                );
                /**
                 * Save notication details.
                 */
                $notificationsModel = Mage::getModel ( 'airhotels/notifications' )->addData ( $data );
                $notificationsModel->save ();
            }
            $this->loadLayout ();
            $this->_initLayoutMessages ( 'catalog/session' );
            /**
             * Set page title.
             */
            $this->getLayout ()->getBlock ( 'head' )->setTitle ( $this->__ ( 'Notifications' ) );
            $this->renderLayout ();
        } else {
            /**
             * Set error message in session.
             *
             * Redirect to customer login page.
             */
            Mage::getSingleton ( 'core/session' )->addError ( $this->__ ( 'You are not currently logged in' ) );
            $this->_redirectUrl ( Mage::helper ( 'customer' )->getLoginUrl () );
        }
    }
    /**
     * Function Name: notificationsaveAction()
     * Save the notifications
     *
     * @var $customerId
     * @var $accept_reject
     * @var $request
     * @var $account_listing
     * @var $data
     *
     */
    public function notificationsaveAction() {
        /**
         * Set form authendication url.
         */
        Mage::getSingleton ( 'customer/session' )->setBeforeAuthUrl ( Mage::helper ( 'airhotels' )->getformUrl () );
        $customerId = Mage::getSingleton ( 'customer/session' )->getCustomer ()->getId ();
        /**
         * Check if customerId is exist or not
         */
        if ($customerId) {
            $inbox = $this->getRequest ()->getParam ( "inbox" );
            $accept_reject = $this->getRequest ()->getParam ( "accept_reject" );
            $request = $this->getRequest ()->getParam ( "request" );
            $account_listing = $this->getRequest ()->getParam ( "account_listing" );
            if (! isset ( $inbox )) {
                $inbox = "off";
            }
            if (! isset ( $accept_reject )) {
                $accept_reject = "off";
            }
            if (! isset ( $request )) {
                $request = "off";
            }
            if (! isset ( $account_listing )) {
                $account_listing = "off";
            }
            $data = array (
                    "user_id" => $customerId,
                    "inbox" => $inbox,
                    "recieve_request" => $request,
                    "response_request" => $accept_reject,
                    "account_listing" => $account_listing 
            );
            /**
             * Load customer notifications
             */
            $model = Mage::getModel ( 'airhotels/notifications' )->load ( $customerId, 'user_id' )->addData ( $data );
            try {
                /**
                 * Save customer notification.
                 * and set notification success message.
                 */
                $model->save ();
                Mage::getSingleton ( 'core/session' )->addSuccess ( $this->__ ( "Updated successfully." ) );
            } catch ( Exception $e ) {
                /**
                 * Set error message
                 */
                Mage::getSingleton ( 'core/session' )->addError ( $this->__ ( $e->getMessage () ) );
            }
            /**
             * Redirect to notification action.
             */
            $this->_redirect ( '*/dashboard/notifications' );
        } else {
            /**
             * Set error message to session
             *
             * Redirect to login page.
             */
            Mage::getSingleton ( 'core/session' )->addError ( $this->__ ( 'You are not currently logged in' ) );
            $this->_redirectUrl ( Mage::helper ( 'customer' )->getLoginUrl () );
        }
    }
    /**
     * Function name: transactionhistoryAction()
     *
     * Display property history
     * Dashboard section
     */
    public function transactionhistoryAction() {
        /**
         * Renderer loadlayout()
         */
        $this->loadLayout ();
        $this->_initLayoutMessages ( 'catalog/session' );
        /**
         * If not logged in
         */
        if (! Mage::getSingleton ( 'customer/session' )->isLoggedIn ()) {
            /**
             * Set login redirect url.
             */
            $this->_redirectUrl ( Mage::helper ( 'customer' )->getLoginUrl () );
        } else {
            /**
             * Set page title.
             */
            $this->getLayout ()->getBlock ( 'head' )->setTitle ( $this->__ ( 'Transaction History' ) );
        }
        $this->renderLayout ();
    }
    /**
     * Function name: invitetransactionhistoryAction()
     *
     * Display invite friends transaction details
     * Dashboard section
     */
    public function invitetransactionhistoryAction() {
        /**
         * Renderer loadlayout()
         */
        $this->loadLayout ();
        $this->_initLayoutMessages ( 'catalog/session' );
        /**
         * If not logged in
         */
        if (! Mage::getSingleton ( 'customer/session' )->isLoggedIn ()) {
            /**
             * Set login redirect url.
             */
            $this->_redirectUrl ( Mage::helper ( 'customer' )->getLoginUrl () );
        } else {
            /**
             * Set page title.
             */
            $this->getLayout ()->getBlock ( 'head' )->setTitle ( $this->__ ( 'Invite friends Transaction History' ) );
        }
        $this->renderLayout ();
    }
    /**
     * Function Name: paymentAction()
     * Action for payment link which is host can add Payout details
     *
     * Rendering laod and render layout
     */
    public function paymentAction() {
        /**
         * if not logged in
         */
        if (! Mage::getSingleton ( 'customer/session' )->isLoggedIn ()) {
            /**
             * Redirect to login page.
             */
            $this->_redirectUrl ( Mage::helper ( 'customer' )->getLoginUrl () );
        }
        /**
         * unset BankCountryCode to session
         */
        if (Mage::getSingleton ( 'customer/session' )->getBankCountryCode ()) {
            Mage::getSingleton ( 'customer/session' )->unsBankCountryCode ();
        }
        /**
         * Unset bank currency code to session.
         */
        if (Mage::getSingleton ( 'customer/session' )->getBankCurrencyCode ()) {
            Mage::getSingleton ( 'customer/session' )->unsBankCurrencyCode ();
        }
        /**
         * Rendering laod and render layout
         */
        $this->loadLayout ();
        $this->_initLayoutMessages ( 'catalog/session' );
        /**
         * if not logged in
         */
        if (! Mage::getSingleton ( 'customer/session' )->isLoggedIn ()) {
            /**
             * Set login redirect url.
             */
            $this->_redirectUrl ( Mage::helper ( 'customer' )->getLoginUrl () );
        } else {
            /**
             * Set page title.
             */
            $this->getLayout ()->getBlock ( 'head' )->setTitle ( $this->__ ( 'Payout Preference' ) );
        }
        $this->renderLayout ();
    }
    
    /**
     * Function name: securityAction()
     * Display security details
     *
     * Load and render layout
     */
    public function securityAction() {
        $this->loadLayout ();
        /**
         * if not logged in
         */
        if (! Mage::getSingleton ( 'customer/session' )->isLoggedIn ()) {
            /**
             * Set login redirect url.
             */
            $this->_redirectUrl ( Mage::helper ( 'customer' )->getLoginUrl () );
        } else {
            /**
             * Set page title as security.
             */
            $this->getLayout ()->getBlock ( 'head' )->setTitle ( $this->__ ( 'Security' ) );
            $this->renderLayout ();
        }
    }
    /**
     * Function Name: settingsAction
     * settingsAction - Render the layout which is host settings section.
     *
     * @var $customerId Load and render layout
     */
    public function settingsAction() {
        Mage::getSingleton ( 'customer/session' )->setBeforeAuthUrl ( Mage::helper ( 'airhotels' )->getformUrl () );
        /**
         * Load and render layout
         */
        if (Mage::getSingleton ( 'customer/session' )->getCustomer ()->getId ()) {
            $this->loadLayout ();
            $this->_initLayoutMessages ( 'catalog/session' );
            /**
             * Set page title as settings.
             */
            $this->getLayout ()->getBlock ( 'head' )->setTitle ( $this->__ ( 'Settings' ) );
            $this->renderLayout ();
        } else {
            /**
             * Set error message to session
             *
             * Redirect to login page.
             */
            Mage::getSingleton ( 'core/session' )->addError ( $this->__ ( 'You are not currently logged in' ) );
            $this->_redirectUrl ( Mage::helper ( 'customer' )->getLoginUrl () );
        }
    }
    /**
     * Function Name: residencesaveAction()
     *
     * @var $customerId
     * @var $country save the residence of host
     */
    public function residencesaveAction() {
        Mage::getSingleton ( 'customer/session' )->setBeforeAuthUrl ( Mage::helper ( 'airhotels' )->getformUrl () );
        $customerId = Mage::getSingleton ( 'customer/session' )->getCustomer ()->getId ();
        /**
         * Load and render layout
         */
        if ($customerId) {
            /**
             * Save customer details.
             */
            $country = $this->getRequest ()->getParam ( "host_country" );
            $collection = Mage::getModel ( 'airhotels/customerphoto' )->load ( $CustomerId, 'customer_id' );
            $collection->setCustomerId ( $customerId );
            $collection->setCountry ( $country );
            $collection->save ();
            /**
             * Set success message.
             * Redirect to settings action.
             */
            Mage::getSingleton ( 'core/session' )->addSuccess ( $this->__ ( 'Information have been updated' ) );
            $this->_redirect ( '*/dashboard/settings' );
        } else {
            /**
             * Set error message to session.
             * Redirect to customer login page.
             */
            Mage::getSingleton ( 'core/session' )->addError ( $this->__ ( 'You are not currently logged in' ) );
            $this->_redirectUrl ( Mage::helper ( 'customer' )->getLoginUrl () );
        }
    }
    
    /**
     * Function Name: CancelaccountAction()
     *
     * Host send the request via email to admin for cancel account
     *
     * @var $hostId
     * @var $message
     * @var $customerData
     * @var $hostEmail
     * @var $hostName
     * @var $adminEmailId
     * @var $fromMailId
     * @var $fromName
     */
    public function cancelaccountAction() {
        Mage::getSingleton ( 'customer/session' )->setBeforeAuthUrl ( Mage::helper ( 'airhotels' )->getformUrl () );
        /**
         * Get customer details.
         */
        $customerId = Mage::getSingleton ( 'customer/session' )->getCustomer ()->getId ();
        if ($customerId) {
            $hostId = $this->getRequest ()->getParam ( "hostId" );
            $message = $this->getRequest ()->getParam ( "message" );
            /**
             * Get host email and host name.
             *
             * @var $hostEmail,$hostName
             */
            $customerData = Mage::getModel ( 'customer/customer' )->load ( $hostId );
            $hostEmail = $customerData->getEmail ();
            $hostName = $customerData->getName ();
            /**
             * Get admin emailId.
             * Get from email id
             * Get from name.
             */
            $adminEmailId = Mage::getStoreConfig ( 'airhotels/custom_email/admin_email_id' );
            $fromMailId = Mage::getStoreConfig ( "trans_email/ident_$adminEmailId/email" );
            $fromName = Mage::getStoreConfig ( "trans_email/ident_$adminEmailId/name" );
            /**
             * Get email templete Id for cancel account
             * 
             * @var unknown
             */
            $templateId = ( int ) Mage::getStoreConfig ( 'airhotels/custom_email/host_cancel_account' );
            if ($templateId) {
                $emailTemplate = Mage::getModel ( 'core/email_template' )->load ( $templateId );
            } else {
                /**
                 * we are calling default template
                 */
                $emailTemplate = Mage::getModel ( 'core/email_template' )->loadDefault ( 'airhotels_custom_email_host_cancel_account' );
            }
            /**
             * mail sender name
             */
            $emailTemplate->setSenderName ( $fromName );
            /**
             * mail sender email id
             */
            $emailTemplate->setSenderEmail ( $fromMailId );
            $emailTemplateVariables = (array (
                    'adminname' => $fromName,
                    'hostname' => $hostName,
                    'hostemail' => $hostEmail,
                    'message' => $message 
            ));
            $emailTemplate->setDesignConfig ( array (
                    'area' => 'frontend' 
            ) );
            /**
             * it return the temp body
             */
            $emailSent = $emailTemplate->send ( $fromMailId, $hostName, $emailTemplateVariables );
            /**
             * Check if $emailSent or not
             */
            if ($emailSent) {
                /**
                 * Set success message.
                 * Redirect to dashboard settings action page.
                 */
                Mage::getSingleton ( 'core/session' )->addSuccess ( $this->__ ( 'Your request sent successfully.' ) );
                $this->_redirect ( '*/dashboard/settings' );
            } else {
                /**
                 * Set error message.
                 * Redirect to dashboard settings action.
                 */
                Mage::getSingleton ( 'core/session' )->addError ( $this->__ ( "Your request can not sent please try again." ) );
                $this->_redirect ( '*/dashboard/settings' );
            }
        } else {
            /**
             * Set error and success message in session
             * Redirect to login page.
             */
            Mage::getSingleton ( 'core/session' )->addError ( $this->__ ( 'You are not currently logged in' ) );
            $this->_redirectUrl ( Mage::helper ( 'customer' )->getLoginUrl () );
        }
    }
    /**
     * Function Name: ReviewpageloadAction
     * Review page action
     *
     * @var $productId
     * @var $reviews
     * @var $reviewCount
     * @var $customer_data
     */
    public function ReviewpageloadAction() {
        $productId = $this->getRequest ()->getParam ( 'product' );
        /**
         * Get review collection.
         */
        $reviews = Mage::getModel ( 'review/review' )->getResourceCollection ();
        $reviews->addStoreFilter ( Mage::app ()->getStore ()->getId () )->addStatusFilter ( Mage_Review_Model_Review::STATUS_APPROVED )->addEntityFilter ( 'product', $productId )->setDateOrder ()->addRateVotes ()->load ();
        $reviews = $reviews->getData ();
        /**
         * Count the reviews
         */
        if (count ( $reviews )) {
            $reviewCount = count ( $reviews );
            for($i = 0; $i < $reviewCount; $i ++) {
                $customer_data = Mage::getModel ( 'airhotels/product' )->getCustomerPictureById ( $reviews [$i] ["customer_id"] );
                $htmlVal .= '<div id="reviewrapper">';
                $htmlVal .= '<div class="review-product">';
                $htmlVal .= '<ul>';
                $htmlVal .= ' <li>';
                $htmlVal .= ' <a class="customer_img" href="' . Mage::helper ( 'airhotels/product' )->getprofilepage () . 'id/' . $reviews [$i] ["customer_id"] . '" >';
                /**
                 * Day wise $customerData blocked date details
                 */
                if ($customer_data [0] ["imagename"]) {
                    $htmlVal .= '<img src="' . Mage::getBaseUrl ( 'media' ) . "catalog/customer/thumbs/" . $customer_data [0] ["imagename"] . '"; alt="">';
                } else {
                    $htmlVal .= ' <img src="' . Mage::getBaseUrl ( 'skin' ) . 'frontend/default/stylish/images/no_user.jpg' . '" alt="">';
                }
                $htmlVal .= '</a>';
                $htmlVal .= '<div class="review_content_wrapper"><p class="nick_name">' . $reviews [$i] ["nickname"] . '</p>';
                $htmlVal .= '<p class="reviews_title">' . nl2br ( $reviews [$i] ["title"] ) . '</p>';
                $htmlVal .= '<p class="reviews_detail">' . nl2br ( $reviews [$i] ["detail"] ) . '</p>';
                $htmlVal .= '<p class="reviews_created">' . $reviews [$i] ["nickname"] . ", " . date ( "jS, F Y", strtotime ( $reviews [$i] ["created_at"] ) ) . '</p>';
                $htmlVal .= '</div></li></ul></div></div>';
            }
        } else {
            /**
             * Set no review message
             */
            $message = $this->__ ( 'There are no reviews yet for this product. Be the first to write a review' );
            $htmlVal = $htmlVal . $message;
        }
        /**
         * Set message to body response
         */
        $this->getResponse ()->setBody ( $htmlVal );
    }
    
    /**
     * Function Name: lessReviewPageAction
     *
     * In lessreviewspage action define less reviews.
     * Getting last 3 reviews from the collection.
     *
     * @var $productId
     * @var $reviewsCollection
     * @var $reviewCountCondition
     */
    public function lessReviewPageAction() {
        $productId = $this->getRequest ()->getParam ( 'product' );
        /**
         * Get review collection.
         * Filter by status.
         */
        $reviewsCollection = Mage::getModel ( 'review/review' )->getResourceCollection ();
        $reviewsCollection->addStoreFilter ( Mage::app ()->getStore ()->getId () )->addStatusFilter ( Mage_Review_Model_Review::STATUS_APPROVED )->addEntityFilter ( 'product', $productId )->setDateOrder ()->addRateVotes ()->setPageSize ( 3 )->load ();
        $reviewsCollection = $reviewsCollection->getData ();
        /**
         * Check the review count.
         */
        if (count ( $reviewsCollection )) {
            $reviewCountCondition = count ( $reviewsCollection );
            for($i = 0; $i < $reviewCountCondition; $i ++) {
                $customerData = Mage::getModel ( 'airhotels/product' )->getCustomerPictureById ( $reviewsCollection [$i] ["customer_id"] );
                $html .= '<div id="reviewrapper">';
                $html .= '<div class="review-product">';
                $html .= '<ul>';
                $html .= ' <li>';
                $html .= ' <a class="customer_img" href="' . Mage::helper ( 'airhotels/product' )->getprofilepage () . 'id/' . $reviewsCollection [$i] ["customer_id"] . '" >';
                /**
                 * Day wise $customerData blocked date details
                 */
                if ($customerData [0] ["imagename"]) {
                    $html .= '<img src="' . Mage::getBaseUrl ( 'media' ) . "catalog/customer/thumbs/" . $customerData [0] ["imagename"] . '"; alt="">';
                } else {
                    $html .= ' <img src="' . Mage::getBaseUrl ( 'skin' ) . 'frontend/default/stylish/images/no_user.jpg' . '" alt="">';
                }
                $html .= '</a>';
                $html .= '<div class="review_content_wrapper"><p class="nick_name">' . $reviewsCollection [$i] ["nickname"] . '</p>';
                $html .= '<p class="reviews_title">' . nl2br ( $reviewsCollection [$i] ["title"] ) . '</p>';
                $html .= '<p class="reviews_detail">' . nl2br ( $reviewsCollection [$i] ["detail"] ) . '</p>';
                $html .= '<p class="reviews_created">' . $reviewsCollection [$i] ["nickname"] . ", " . date ( "jS, F Y", strtotime ( $reviewsCollection [$i] ["created_at"] ) ) . '</p>';
                $html .= '</div></li></ul></div></div>';
            }
        } else {
            /**
             * Set message as there is no review.
             *
             * @var $message
             */
            $message = $this->__ ( 'There are no reviewsCollection yet for this product. Be the first to write a review' );
            $html = $html . $message;
        }
        /**
         * Set message to body
         */
        $this->getResponse ()->setBody ( $html );
    }
}