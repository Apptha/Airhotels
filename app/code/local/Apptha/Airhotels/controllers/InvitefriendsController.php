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
 * This class contains Invite Friends manipulation functionality
 */
class Apptha_Airhotels_InvitefriendsController extends Mage_Core_Controller_Front_Action {
    /**
     * Index action
     */
    public function indexAction() {
        /**
         * Load and render layout
         */
        $this->loadLayout ();
        /**
         * Set page title.
         */
        $this->getLayout ()->getBlock ( 'head' )->setTitle ( Mage::helper ( "airhotels" )->__ ( 'Invite Friends' ) );
        $this->renderLayout ();
    }
    /**
     * Invite action
     */
    public function inviteAction() {
        /**
         * Check customer is logged in or not.
         */
        if (! Mage::getSingleton ( 'customer/session' )->isLoggedIn ()) {
            /**
             * Get all invite friends url
             * Redirect to invite friends action.
             */
            $this->_redirectUrl ( Mage::helper ( 'customer' )->getLoginUrl () );
            return false;
        }
        if(( int ) Mage::helper ( 'airhotels/invitefriends' )->getInviteFriendsEnabledOrNot () == 0){
            $this->loadLayout ();
            /**
             * Set page title.
             */
            $this->getLayout ()->getBlock ( 'head' )->setTitle ( Mage::helper ( "airhotels" )->__ ( 'Invite Friends' ) );
            $this->renderLayout ();
        }else{
            $this->_forward('defaultNoRoute');
        }
    }
    
    /**
     * Invite mail action
     */
    public function invitemailAction() {
        $friendEmails = array ();
        /**
         * Get Post parameters.
         */
        $postData = Mage::app ()->getRequest ()->getPost ();
        $message = $postData ['message'];
        if (isset ( $postData ['friends'] )) {
            $friendEmails = $postData ['friends'];
        }
        /**
         * Check customer loggedin or not.
         */
        if (! Mage::getSingleton ( 'customer/session' )->isLoggedIn ()) {
            /**
             * Get the invite friends url.
             */
            $this->_redirectUrl ( Mage::helper ( 'customer' )->getLoginUrl () );
            return false;
        }
        /**
         * Get customer data.
         * Get customer Id.
         * Get Email is
         */
        $customerData = Mage::getSingleton ( 'customer/session' )->getCustomer ();
        $customerId = $customerData->getId ();
        $customerEmail = $customerData->getEmail ();
        /**
         * Get customer profile picture.
         */
        $profilePhoto = Mage::getModel ( 'airhotels/product' )->getCustomerPictureById ( $customerId );
        /**
         * To Display the customer profile image in myaccount
         * Display customer profile information.
         */
        if (! empty ( $profilePhoto [0] ["imagename"] )) {
            $senderprofileimage = Mage::getBaseUrl ( 'media' ) . "catalog/customer/" . $profilePhoto [0] ["imagename"];
        } else {
            $senderprofileimage = Mage::getBaseUrl ( Mage_Core_Model_Store::URL_TYPE_SKIN ) . "frontend/default/stylish/images/no_user.jpg";
        }
        /**
         * Get referal url for the customer.
         */
        $aceeptinviteurl = Mage::helper ( 'airhotels/invitefriends' )->getReferralUrlForCustomer ();
        /**
         * Check invite friends enable or not.
         */
        if (! empty ( $customerId ) && ( int ) Mage::helper ( 'airhotels/invitefriends' )->getInviteFriendsEnabledOrNot () == 0) {
            /**
             * Get airhotels title.
             */
            $title = Mage::getStoreConfig ( 'airhotels/custom_group/airhotels_title' );
            $customerName = $customerData->getName ();
            /**
             * Get friends emails.
             */
            foreach ( $friendEmails as $friendEmail ) {
                $friendsNameArray = explode ( "@", $friendEmail );
                $friendName = $friendsNameArray [0];
                if (! empty ( $friendEmail )) {
                    /**
                     * Get Email templete Id.
                     */
                    $templateId = ( int ) Mage::getStoreConfig ( 'airhotels/google/invite' );
                    /**
                     * Check email templeteId is exist or nor.
                     * Load the email templete
                     */
                    if ($templateId) {
                        $emailTemplate = Mage::getModel ( 'core/email_template' )->load ( $template_id );
                    } else {
                        $emailTemplate = Mage::getModel ( 'core/email_template' )->loadDefault ( 'airhotels_custom_email_invitefriends' );
                    }
                    /**
                     * Set customer name.
                     * Set Customer Email.
                     */
                    $emailTemplate->setSenderName ( $customerName );
                    $emailTemplate->setSenderEmail ( $customerEmail );
                    $emailTemplateVariables = (array (
                            'url' => $aceeptinviteurl,
                            'sendername' => $customerName,
                            'senderemail' => $customerEmail,
                            'title' => $title,
                            'image' => $senderprofileimage,
                            'message' => $message 
                    ));
                    $emailTemplate->setDesignConfig ( array (
                            'area' => 'frontend' 
                    ) );
                    $emailTemplate->getProcessedTemplate ( $emailTemplateVariables );
                    /**
                     * Sending mail to host
                     */
                    $emailSent = $emailTemplate->send ( $friendEmail, $friendName, $emailTemplateVariables );
                }
            }
            /**
             * Check email sent or not.
             * If email sent, return success message.
             */
            if ($emailSent) {
                Mage::getSingleton ( "core/session" )->addSuccess ( Mage::helper ( "airhotels" )->__ ( 'Mail sent successfully.' ) );
            }
            /**
             * Redirect to invite action.
             */
            return $this->_redirect ( 'airhotels/invitefriends/invite' );
        }
    }
    
    /**
     * Add discount amount
     */
    public function addDiscountAction() {
        /**
         * Set session values
         * @values Discount amount
         * @value Current customer first purchase discount
         */
        Mage::getSingleton ( "core/session" )->setDiscountAmountForInviteeToDisplay ( 0 );
        Mage::getSingleton ( "core/session" )->setCurrentCustomerFirstPurchaseDiscount ( 0 );
        /**
         * Get base currency code.
         */
        $baseCurrencyCode = Mage::app ()->getStore ()->getBaseCurrencyCode ();
        $currentCurrencyCode = Mage::app ()->getStore ()->getCurrentCurrencyCode ();
        /**
         * Set base currency code in session.
         */
        Mage::getSingleton ( "core/session" )->setDiscountAmountForInviteeCurrencyCode ( $currentCurrencyCode );
        $sessionCustomer = Mage::getSingleton ( "customer/session" );
        /**
         * Check customer is logged in or not.
         */
        if ($sessionCustomer->isLoggedIn ()) {
            $customerId = $sessionCustomer->getId ();
            /**
             * Get customer earned amount.
             */
            $customerCreditAmount = Mage::helper ( "airhotels/invitefriends" )->getCustomerCreditAmount ( $customerId );
            /**
             * Check base currncy code with current currency code.
             */
            if ($baseCurrencyCode != $currentCurrencyCode) {
                $customerCreditAmount = round ( Mage::helper ( 'directory' )->currencyConvert ( $customerCreditAmount, $baseCurrencyCode, $currentCurrencyCode ), 2 );
            }
            /**
             * Get discount amount.
             */
            $discountAmount = Mage::app ()->getRequest ()->getParam ( 'discount_amount' );
            if ($customerCreditAmount < $discountAmount) {
                /**
                 * Display error message.
                 */
                Mage::getSingleton ( "core/session" )->addError ( Mage::helper ( "airhotels" )->__ ( 'Kindly check your discount amount' ) );
                /**
                 * Set discount amount for set invite friends status as zero.
                 * Redirect to onestepcheckout.
                 */
                Mage::getSingleton ( "core/session" )->setDiscountAmountForInviteeStatus ( 0 );
                $this->_redirect ( "onestepcheckout" );
                return;
            }
            /**
             * Get maximum discount amount.
             */
            $maximumDiscountAmount = Mage::helper ( "airhotels/calendar" )->getMaximumDiscountAmount ();
            /**
             * Check base currency code with current currency code.
             */
            if ($baseCurrencyCode != $currentCurrencyCode) {
                /**
                 * Get maximum currency amount for discount in diffenet currency.
                 */
                $maximumDiscountAmount = round ( Mage::helper ( 'directory' )->currencyConvert ( $maximumDiscountAmount, $baseCurrencyCode, $currentCurrencyCode ), 2 );
            }
            /**
             * Check discount amount with maximum discount amount.
             * If discount amount ecced the maximum disscount amount through the error message.
             * and set discount invite friends status as zero.
             */
            if ($discountAmount > $maximumDiscountAmount) {
                /**
                 * Set error message.
                 * Redirect to onestepcheckout page.
                 */
                Mage::getSingleton ( "core/session" )->addError ( Mage::helper ( "airhotels" )->__ ( 'The maximum discount amount is' . $maximumDiscountAmount ) );
                Mage::getSingleton ( "core/session" )->setDiscountAmountForInviteeStatus ( 0 );
                $this->_redirect ( "onestepcheckout" );
                return;
            }
            /**
             * Get the quote data.=
             */
            $quote = Mage::getModel ( 'checkout/session' )->getQuote ();
            $quoteData = $quote->getData ();
            /**
             * Get grand total.
             */
            $grandTotal = $quoteData ['grand_total'];
            if ($discountAmount >= $grandTotal) {
                $discountAmount = $grandTotal;
            }
            /**
             * Check discount amount.
             * Add succes message.
             * Set Discount amount status flag.
             */
            if ($discountAmount <= 0) {
                Mage::getSingleton ( "core/session" )->addSuccess ( Mage::helper ( "airhotels" )->__ ( 'Discount was removed.' ) );
                Mage::getSingleton ( "core/session" )->setDiscountAmountForInvitee ( 0 );
                Mage::getSingleton ( "core/session" )->setDiscountAmountForInviteeStatus ( 0 );
            } else {
                Mage::getSingleton ( "core/session" )->addSuccess ( Mage::helper ( "airhotels" )->__ ( 'Discount was applied.' ) );
                Mage::getSingleton ( "core/session" )->setDiscountAmountForInvitee ( $discountAmount );
                Mage::getSingleton ( "core/session" )->setDiscountAmountForInviteeStatus ( 1 );
            }
        }
        /**
         * Redirect to onestepcheckout page.
         */
        $this->_redirect ( "onestepcheckout" );
        return;
    }
    
    /**
     * load more about host layout file
     */
    public function abouthostAction() {
        /**
         * Load layout file.
         */
        $this->loadLayout ();
        $this->_initLayoutMessages ( 'catalog/session' );
        /**
         * If not logged in
         * Redirect to customer login page.
         */
        if (! Mage::getSingleton ( 'customer/session' )->isLoggedIn ()) {
            $this->_redirectUrl ( Mage::helper ( 'customer' )->getLoginUrl () );
        } else {
            /**
             * Set page title.
             */
            $this->getLayout ()->getBlock ( 'head' )->setTitle ( $this->__ ( 'About Host' ) );
        }
        $this->renderLayout ();
    }
    
    /**
     * Function for gmail invite friends
     *
     * @return string
     */
    public function inviteFriendsAction() {
        /**
         * Load layout file.
         */
        $this->loadLayout ();
        $this->renderLayout ();
    }
    /**
     * Function for send email to invite friends
     *
     * @return string
     */
    public function invitefriendsemailAction() {
        /**
         * Get parameters from invite friend form.
         * 
         * @param
         *            get email.
         * @param
         *            get name.
         * @param
         *            get message.
         */
        $friendsEmail = $this->getRequest ()->getParam ( 'email' );
        $friendsName = $this->getRequest ()->getParam ( 'name' );
        $message = $this->getRequest ()->getParam ( 'message' );
        /**
         * Load email templete id for the invite friends.
         */
        $templateId = ( int ) Mage::getStoreConfig ( 'airhotels/custom_email/invitefriends' );
        /**
         * if it is user template then this process is continue
         */
        if ($templateId) {
            $emailTemplate = Mage::getModel ( 'core/email_template' )->load ( $templateId );
        } else {
            /**
             * we are calling default template
             */
            $emailTemplate = Mage::getModel ( 'core/email_template' )->loadDefault ( 'airhotels_custom_email_invitefriends' );
        }
        /**
         * Get sender details.
         */
        $senderDetails = Mage::getSingleton ( 'customer/session' )->getCustomer ();
        /**
         * Full Name
         */
        $senderName = $senderDetails->getName ();
        /**
         * Get sender email id.
         */
        $senderEmail = $senderDetails->getEmail ();
        $profilePhoto = Mage::getModel ( 'airhotels/product' )->getCustomerPictureById ( $senderDetails->getId () );
        /**
         * To Display the customer profile image in myaccount
         * Display customer profile information.
         */
        if (! empty ( $profilePhoto [0] ["imagename"] )) {
            $senderprofileimage = Mage::getBaseUrl ( 'media' ) . "catalog/customer/" . $profilePhoto [0] ["imagename"];
        } else {
            $senderprofileimage = $this->getSkinUrl ( 'images/no_user.jpg' );
        }
        /**
         * Get logged in url.
         */
        $aceeptinviteurl = Mage::helper ( 'customer' )->getLoginUrl ();
        $recipients = array_combine ( $friendsEmail, $friendsName );
        /**
         * mail sender name
         */
        $emailTemplate->setSenderName ( $senderName );
        /**
         * mail sender email id
         */
        $emailTemplate->setSenderEmail ( $senderEmail );
        $emailTemplateVariables = (array (
                'sendername' => ucwords ( $senderName ),
                'senderemail' => $senderEmail,
                'friendsname' => array_values ( $recipients ),
                'friendsemail' => array_keys ( $recipients ),
                'message' => $message,
                'image' => $senderprofileimage,
                'url' => $aceeptinviteurl 
        ));
        $emailTemplate->setDesignConfig ( array (
                'area' => 'frontend' 
        ) );
        /**
         * it return the temp body
         */
        $processedTemplate = $emailTemplate->getProcessedTemplate ( $emailTemplateVariables );
        /**
         * send mail to multiple friend's email ids
         */
        $emailSent = $emailTemplate->send ( array_keys ( $recipients ), array_values ( $recipients ), $processedTemplate );
        if ($emailSent) {
            /**
             * Set success message.
             */
            Mage::getSingleton ( 'core/session' )->addSuccess ( $this->__ ( 'Email sent successfully.' ) );
        }
        /**
         * Return email sent status.
         */
        return $emailSent;
    }
    
    /**
     * profilevideosaveAction - Save the profile video for host
     */
    public function profilevideosaveAction() {
        /**
         * Get customer id.
         */
        $customerId = Mage::getSingleton ( 'customer/session' )->getId ();
        /**
         * Get customer photo collection.
         */
        $customerPhotoCollection = Mage::getModel ( 'airhotels/customerphoto' )->load ( $customerId, 'customer_id' );
        $uploadsData = new Zend_File_Transfer_Adapter_Http ();
        $filesDataArray = $uploadsData->getFileInfo ();
        /**
         * Check profile video exist or not.
         */
        if ($filesDataArray ['profileVideo'] ['name'] != "") {
            try {
                /**
                 * Get video url.
                 */
                $videoUrl = Mage::helper ( 'airhotels/invitefriends' )->uploadProfileVideo ( 'profileVideo', $filesDataArray );
                $customerPhotoCollection->setVideoUrl ( $videoUrl );
                $customerPhotoCollection->save ();
                /**
                 * Set video updated success message
                 */
                Mage::getSingleton ( 'core/session' )->addSuccess ( $this->__ ( "Profile video updated successfully." ) );
            } catch ( Exception $e ) {
                /**
                 * Set error message.
                 */
                Mage::getSingleton ( 'core/session' )->addError ( $this->__ ( $e->getMessage () ) );
            }
        }
        /**
         * Redirect to set upload photo action.
         */
        return $this->_redirect ( 'property/property/uploadphoto' );
    }
    
    /**
     * Documnent Save action
     *
     * @return Ambigous <Mage_Core_Controller_Varien_Action, Apptha_Airhotels_InvitefriendsController>
     */
    public function documentsaveAction() {
        /**
         * Get post parameters.
         * Get customer Id.
         */
        $data = $this->getRequest ()->getPost ();
        $customerId = Mage::getSingleton ( 'customer/session' )->getId ();
        /**
         * create a folder to save the verified documents
         */
        $verificationTags = Mage::getModel ( 'airhotels/tagsverification' )->getCollection ();
        /**
         * Initialize file transfer adapter object.
         */
        $uploadsData = new Zend_File_Transfer_Adapter_Http ();
        $filesDataArray = $uploadsData->getFileInfo ();
        foreach ( $verificationTags as $verificationTag ) {
            /**
             * Check verification tag direct url.
             */
            if ($verificationTag->getDirectUrl () == 0 && $filesDataArray [$verificationTag->getTagName ()] ['name']) {
                try {
                    $uploader = new Varien_File_Uploader ( $filesDataArray [$verificationTag->getTagName ()] );
                    /**
                     * Set image upload extensions.
                     */
                    $uploader->setAllowedExtensions ( array (
                            'jpg',
                            'jpeg',
                            'gif',
                            'png',
                            'pdf' 
                    ) );
                    $uploader->setAllowRenameFiles ( true );
                    $uploader->setAllowCreateFolders ( true );
                    $uploader->setFilesDispersion ( false );
                    $localMediaPath = Mage::getBaseDir ( 'media' ) . DS;
                    /**
                     * Get document path.
                     */
                    $documentPath = 'airhotels' . DS . 'verified documents' . DS . $customerId . DS . $verificationTag->getTagName ();
                    /**
                     * Get tag id.
                     */
                    $tagId = Mage::getModel ( 'airhotels/verifyhost' )->filterTag ( $customerId, $verificationTag->getTagId () );
                    /**
                     * Get the TagSave
                     */
                    $tagSave = $this->TagSave ( $tagId );
                    $tagSave->setTagId ( $verificationTag->getTagId () )->setHostId ( $customerId )->setHostEmail ( $emailId )->setHostName ( $name );
                    /**
                     * Save document.
                     */
                    $uploader->save ( $localMediaPath . $documentPath, $filesDataArray [$verificationTag->getTagName ()] ['name'] );
                    $filepath = str_replace ( "\\", '/', Mage::getBaseUrl ( Mage_Core_Model_Store::URL_TYPE_MEDIA ) . $documentPath . DS . $filesDataArray [$verificationTag->getTagName ()] ['name'] );
                    $tagSave->setFilePath ( $filepath )->save ();
                } catch ( Exception $e ) {
                    /**
                     * Display error message for images upload
                     */
                    Mage::getSingleton ( 'core/session' )->addError ( $this->__ ( $e->getMessage () ) );
                }
            }
            /**
             * Check verification tag url.
             */
            if ($verificationTag->getDirectUrl () == 1 && $data [$verificationTag->getTagName ()]) {
                $filePath = $data [$verificationTag->getTagName ()];
                /**
                 * Get tag id.
                 */
                $tagId = Mage::getModel ( 'airhotels/verifyhost' )->filterTag ( $customerId, $verificationTag->getTagId () );
                $tagSave = $this->TagSave ( $tagId );
                $tagSave->setTagId ( $verificationTag->getTagId () )->setHostId ( $customerId )->setHostName ( $name )->setHostEmail ( $emailId )->setFilePath ( $filePath )->save ();
                /**
                 * Display success message.
                 */
                Mage::getSingleton ( 'core/session' )->addSuccess ( $this->__ ( 'Updated successfully.' ) );
            }
        }
        /**
         * Display success meesage.
         * Redirect to multistep verification action.
         */
        Mage::getSingleton ( 'core/session' )->addSuccess ( $this->__ ( 'Updated successfully.' ) );
        return $this->_redirect ( 'airhotels/multistep/verification' );
    }
    
    /**
     * Trust and verification - Verify host details
     */
    public function accountverificationAction() {
        $this->loadLayout ();
        /**
         * If not logged in
         * Redirect to login page.
         */
        if (! Mage::getSingleton ( 'customer/session' )->isLoggedIn ()) {
            $this->_redirectUrl ( Mage::helper ( 'customer' )->getLoginUrl () );
        } else {
            /**
             * Set page title.
             */
            $this->getLayout ()->getBlock ( 'head' )->setTitle ( $this->__ ( 'ID verification' ) );
            $this->renderLayout ();
        }
    }
    
    /**
     * Customer current trip page
     */
    public function CurrenttripAction() {
        /**
         * Load layout file.
         */
        $this->loadLayout ();
        $this->_initLayoutMessages ( 'catalog/session' );
        /**
         * if not logged in
         * Redirect to login page.
         */
        if (! Mage::getSingleton ( 'customer/session' )->isLoggedIn ()) {
            $this->_redirectUrl ( Mage::helper ( 'customer' )->getLoginUrl () );
        } else {
            /**
             * Set page title.
             */
            $this->getLayout ()->getBlock ( 'head' )->setTitle ( $this->__ ( 'Current Trips' ) );
        }
        $this->renderLayout ();
    }
    /**
     * Customer previous trip page
     */
    public function PrevioustripAction() {
        /**
         * Load layout.
         */
        $this->loadLayout ();
        $this->_initLayoutMessages ( 'catalog/session' );
        /**
         * if not logged in
         */
        if (! Mage::getSingleton ( 'customer/session' )->isLoggedIn ()) {
            /**
             * Redirect to login page.
             */
            $this->_redirectUrl ( Mage::helper ( 'customer' )->getLoginUrl () );
        } else {
            /**
             * Set page title.
             */
            $this->getLayout ()->getBlock ( 'head' )->setTitle ( $this->__ ( 'Previous Trips' ) );
        }
        $this->renderLayout ();
    }
    
    /**
     * Tag Save Action
     *
     * @param Int $tagId            
     * @return Ambigous <Mage_Core_Model_Abstract, Mage_Core_Model_Abstract, false, boolean, unknown>
     */
    public function TagSave($tagId) {
        /**
         * Check tag flag is exist or not.
         * Save tag flag.
         */
        if ($tagId) {
            $tagSave = Mage::getModel ( 'airhotels/verifyhost' )->load ( $tagId );
        } else {
            $tagSave = Mage::getModel ( 'airhotels/verifyhost' );
        }
        /**
         * Return tagsave.
         */
        return $tagSave;
    }
}