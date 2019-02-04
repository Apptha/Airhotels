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
class Apptha_Airhotels_ProductController extends Mage_Core_Controller_Front_Action {
    
    /**
     * Function Name: historyAction()
     *
     * Display property history
     * Dashboard section
     *
     * rendering load and render layout
     */
    public function historyAction() {
        /**
         * rendering load and render layout
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
            $this->getLayout ()->getBlock ( 'head' )->setTitle ( $this->__ ( 'Property History' ) );
        }
        $this->renderLayout ();
    }
    /**
     * Function Name: vieworderAction()
     *
     * Display property history
     * Dashboard section
     */
    public function vieworderAction() {
        /**
         * rendering load and render layout
         */
        $this->loadLayout ();
        $this->_initLayoutMessages ( 'catalog/session' );
        
        $customerIdVal = Mage::getSingleton ( 'customer/session' )->getCustomer ()->getId ();
        $orderId = ( int ) $this->getRequest ()->getParam ( 'order_id' );
        $orderObject=Mage::getModel('sales/order')->load($orderId);
        $items = $orderObject->getAllVisibleItems();
        foreach($items as $i):
        $entityId= $i->getProductId();
        endforeach;
        $collectionInfo = Mage::getModel ( 'catalog/product' )->load ( $entityId );
        $CustomerId = $collectionInfo->getUserid ();
        if ($customerIdVal != $CustomerId) {
            Mage::getSingleton ( 'core/session' )->addError ( $this->__ ( "Access denied" ) );
            $this->_redirect ( '*/product/history/' );
            return;
        }
        /**
         * if not logged in
         */
        if (! Mage::getSingleton ( 'customer/session' )->isLoggedIn ()) {
            /**
             * Set redirect login url.
             */
            $this->_redirectUrl ( Mage::helper ( 'customer' )->getLoginUrl () );
        } else {
            /**
             * Set page title.
             */
            $this->getLayout ()->getBlock ( 'head' )->setTitle ( $this->__ ( 'Product Ordered' ) );
        }
        $this->renderLayout ();
    }
    /**
     * Function Name: dashboardinboxAction()
     */
    public function dashboardinboxAction() {
        /**
         * If not logged in
         */
        if (! Mage::getSingleton ( 'customer/session' )->isLoggedIn ()) {
            $this->_redirectUrl ( Mage::helper ( 'customer' )->getLoginUrl () );
        } else {
            /**
             * Get message Id.
             */
            $messageid = $this->getRequest ()->getParam ( 'messageid' );
            if ($messageid) {
                if (Mage::getModel ( 'airhotels/calendar' )->deleteMessage ( $messageid, "in" )) {
                    /**
                     * Set delete success message.
                     */
                    Mage::getSingleton ( 'core/session' )->addSuccess ( $this->__ ( 'Deleted successfully' ) );
                } else {
                    /**
                     * Set delete error message.
                     */
                    Mage::getSingleton ( 'core/session' )->addSuccess ( $this->__ ( 'Deletion failed. Try again' ) );
                }
            }
            $this->loadLayout ();
            /**
             * Set page title.
             */
            $this->getLayout ()->getBlock ( 'head' )->setTitle ( $this->__ ( 'Inbox' ) );
            $this->renderLayout ();
        }
    }
    
    /**
     * Function Name: updataMailIconAction()
     * Function to update the mail unread count
     *
     * @var $loggedinId
     * @var $Customer
     * @var $inboxCollection
     *
     * @return int
     */
    public function updataMailIconAction() {
        /**
         * Get customer details.
         */
        $Customer = Mage::getSingleton ( 'customer/session' )->getCustomer ();
        $loggedinId = $Customer->getId ();
        /**
         * Get customer collection from inbox
         * 
         * @var unknown
         */
        $inboxCollection = Mage::getModel ( 'airhotels/customerinbox' )->getCollection ()->addFieldTofilter ( 'receiver_id', $loggedinId )->addFieldToFilter ( 'receiver_read', 0 )->addFieldToFilter ( 'is_receiver_delete', 0 );
        if (count ( $inboxCollection ) > 0) {
            /**
             * Get message count.
             */
            echo count ( $inboxCollection );
        }
    }
    /**
     * Function viewrequestAction()
     *
     * It will show particular request of experience.
     */
    public function viewrequestAction() {
        $this->loadLayout ();
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
            $this->getLayout ()->getBlock ( 'head' )->setTitle ( $this->__ ( 'View Request' ) );
        }
        /**
         * Rendering load and render layout
         */
        $this->renderLayout ();
    }
    /**
     * Function Name: requesttripsAction()
     *
     * Customer Request trip page
     */
    public function requesttripsAction() {
        /**
         * Load layout.
         */
        $this->loadLayout ();
        $this->_initLayoutMessages ( 'catalog/session' );
        /**
         * If not logged in
         */
        if (! Mage::getSingleton ( 'customer/session' )->isLoggedIn ()) {
            /**
             * Set redirect url.
             */
            $this->_redirectUrl ( Mage::helper ( 'customer' )->getLoginUrl () );
        } else {
            /**
             * Set page title.
             */
            $this->getLayout ()->getBlock ( 'head' )->setTitle ( $this->__ ( 'Request Trips' ) );
        }
        /**
         * Rendering load and render layout
         */
        $this->renderLayout ();
    }
    
    /**
     * Function Name: accountverificationAction()
     *
     * Trust and verification - Verify host details
     */
    public function accountverificationAction() {
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
             * Rendering load and render layout
             * Set page title.
             */
            $this->getLayout ()->getBlock ( 'head' )->setTitle ( $this->__ ( 'ID verification' ) );
            $this->renderLayout ();
        }
    }
    /**
     * Function Name: idsaveAction()
     * The Id verifcation image has been stored in database
     *
     * By verifying the tag and also the type
     *
     * @var $data
     * @var $customerId
     * @var $customerData
     * @var $name
     * @var $emailId
     * @var $countryCode
     * @var $tagId
     * @var $idType
     *
     */
    public function idsaveAction() {
        $data = $this->getRequest ()->getPost ();
        /**
         * Get customer session id
         * 
         * @var unknown
         */
        $customerId = Mage::getSingleton ( 'customer/session' )->getId ();
        $customerData = Mage::getModel ( 'customer/customer' )->load ( $customerId );
        /**
         * Get customer name
         * Get customer email
         * Get tag id
         * Get id type.
         */
        $name = $customerData ['firstname'];
        $emailId = $customerData ['email'];
        $countryCode = $this->getRequest ()->getPost ( 'country' );
        $tagId = $this->getRequest ()->getPost ( 'tag_id' );
        $idType = $this->getRequest ()->getPost ( 'id_type' );
        /**
         * create a folder to save the verified documents
         */
        $uploadsIdData = new Zend_File_Transfer_Adapter_Http ();
        $filesDataArray = $uploadsIdData->getFileInfo ();
        if ($filesDataArray [$tagId] ['name'] == '') {
            /**
             * Set error message.
             */
            Mage::getSingleton ( 'core/session' )->addError ( $this->__ ( 'Please select and upload the documents.' ) );
            Mage::helper ( 'airhotels/smsconfig' )->redirectVerification ();
        }
        /**
         * Set tags name $filesDataArray
         */
        if ($filesDataArray [$tagId] ['name']) {
            try {
                $uploader = new Varien_File_Uploader ( $filesDataArray [$tagId] );
                /**
                 * Set allowed file type.
                 */
                $uploader->setAllowedExtensions ( array (
                        'jpg',
                        'jpeg',
                        'png' 
                ) );
                /**
                 * Using Varien_File_Uploader function
                 */
                $uploader->setAllowRenameFiles ( true );
                $uploader->setAllowCreateFolders ( true );
                $uploader->setFilesDispersion ( false );
                $localMediaPath = Mage::getBaseDir ( 'media' ) . DS;
                $idPath = 'airhotels' . DS . 'verified_documents' . DS . $customerId . DS . $tagId;
                $tagIdFilter = Mage::getModel ( 'airhotels/verifyhost' )->filterTag ( $customerId, $tagId );
                /**
                 * Check already document exist or not.
                 */
                if ($tagIdFilter) {
                    /**
                     * Remove old document.
                     */
                    $tagSave = Mage::getModel ( 'airhotels/verifyhost' )->load ( $tagIdFilter );
                    Mage::getModel ( 'airhotels/verifyhost' )->removeOldDocument ( $localMediaPath . $idPath, $tagSave->getFilePath () );
                } else {
                    /**
                     * Create object.
                     *
                     * @var $tagSave.
                     */
                    $tagSave = Mage::getModel ( 'airhotels/verifyhost' );
                }
                /**
                 * Saved host tags
                 */
                $tagSave->setTagId ( $tagId )->setHostId ( $customerId )->setHostName ( $name )->setHostEmail ( $emailId )->setCountryCode ( $countryCode )->setHostTags ( 0 )->setIdType ( $idType );
                $result = $uploader->save ( $localMediaPath . $idPath, $filesDataArray [$tagId] ['name'] );
                /**
                 * Define file path.
                 *
                 * @var $filepath.
                 */
                $filepath = str_replace ( "\\", '/', Mage::getBaseUrl ( Mage_Core_Model_Store::URL_TYPE_MEDIA ) . $idPath . DS . $result ['file'] );
                $tagSave->setFilePath ( $filepath )->save ();
                /**
                 * Set document upload success message
                 *
                 * And redirect to account verification page.
                 */
                Mage::getSingleton ( 'core/session' )->addSuccess ( $this->__ ( 'Updated successfully.' ) );
                Mage::helper ( 'airhotels/smsconfig' )->redirectVerification ();
            } catch ( Exception $e ) {
                /**
                 * Display error message for images upload
                 *
                 * Redirect to account verification.
                 */
                Mage::getSingleton ( 'core/session' )->addError ( $this->__ ( $e->getMessage () ) );
                Mage::helper ( 'airhotels/smsconfig' )->redirectVerification ();
            }
        } elseif ($data [$tagId]) {
            /**
             * Get tagID
             */
            $tagIdFilter = Mage::getModel ( 'airhotels/verifyhost' )->filterTag ( $customerId, $tagId );
            if ($tagIdFilter) {
                $tagSave = Mage::getModel ( 'airhotels/verifyhost' )->load ( $tagIdFilter );
            } else {
                $tagSave = Mage::getModel ( 'airhotels/verifyhost' );
            }
            /**
             * Set host document details.
             *
             * Set success message.
             */
            $tagSave->setTagId ( $tagId )->setHostId ( $customerId )->setHostName ( $name )->setHostEmail ( $emailId )->setFilePath ( $data [$tagId] )->save ();
            Mage::getSingleton ( 'core/session' )->addSuccess ( $this->__ ( 'Updated successfully.' ) );
        }
        /**
         * Redirect to account verification.
         */
        Mage::helper ( 'airhotels/smsconfig' )->redirectVerification ();
    }
    /**
     * The Id verifcation video has been stored in database
     *
     * By verifying the tag and also the type
     */
    public function videosaveAction() {
        $data = $this->getRequest ()->getPost ();
        /**
         * Get customer details.
         * Get customer name.
         * Get customer email.
         */
        $customerId = Mage::getSingleton ( 'customer/session' )->getId ();
        $customerData = Mage::getModel ( 'customer/customer' )->load ( $customerId );
        $name = $customerData ['firstname'];
        $emailId = $customerData ['email'];
        $tagVideo = $this->getRequest ()->getPost ( 'tag_video' );
        /**
         * create a folder to save the verified documents
         */
        $uploadsVideoData = new Zend_File_Transfer_Adapter_Http ();
        $filesDataArray = $uploadsVideoData->getFileInfo ();
        if ($filesDataArray [$tagVideo] ['name'] == '') {
            /**
             * Set customer error message.
             */
            Mage::getSingleton ( 'core/session' )->addError ( $this->__ ( 'Please select and upload the documents.' ) );
            Mage::helper ( 'airhotels/smsconfig' )->redirectVerification ();
        }
        if ($filesDataArray [$tagVideo] ['name']) {
            try {
                /**
                 * Set allowed extention type.
                 */
                $uploaderVideo = new Varien_File_Uploader ( $filesDataArray [$tagVideo] );
                $uploaderVideo->setAllowedExtensions ( array (
                        'mp4',
                        'avi',
                        '3gp',
                        'mov',
                        'webm',
                        'flv',
                        'mpeg4',
                        'mpegps',
                        'wmv' 
                ) );
                $uploaderVideo->setAllowRenameFiles ( true );
                $uploaderVideo->setAllowCreateFolders ( true );
                $uploaderVideo->setFilesDispersion ( false );
                $localMediaPath = Mage::getBaseDir ( 'media' ) . DS;
                /**
                 * Define video file path.
                 *
                 * @var $videoPath
                 */
                $videoPath = 'airhotels' . DS . 'verified_documents' . DS . $customerId . DS . $tagVideo;
                /**
                 * Get existing document details.
                 */
                $tagVideoFilter = Mage::getModel ( 'airhotels/verifyhost' )->filterTag ( $customerId, $tagVideo );
                if ($tagVideoFilter) {
                    /**
                     * Remove old document details.
                     */
                    $tagSave = Mage::getModel ( 'airhotels/verifyhost' )->load ( $tagVideoFilter );
                    Mage::getModel ( 'airhotels/verifyhost' )->removeOldDocument ( $localMediaPath . $videoPath, $tagSave->getFilePath () );
                } else {
                    /**
                     * Define an object.
                     *
                     * @var $tagSave.
                     */
                    $tagSave = Mage::getModel ( 'airhotels/verifyhost' );
                }
                /**
                 * Save host details and document details
                 */
                $tagSave->setTagId ( $tagVideo )->setHostId ( $customerId )->setHostName ( $name )->setHostEmail ( $emailId )->setHostTags ( 0 );
                $getResult = $uploaderVideo->save ( $localMediaPath . $videoPath, $filesDataArray [$tagVideo] ['name'] );
                /**
                 * Define file path.
                 *
                 * @var $filepath
                 */
                $filepath = str_replace ( "\\", '/', Mage::getBaseUrl ( Mage_Core_Model_Store::URL_TYPE_MEDIA ) . $videoPath . DS . $getResult ['file'] );
                $tagSave->setFilePath ( $filepath )->save ();
                /**
                 * Set document updated success message.
                 *
                 * Redirect to account verification page.
                 */
                Mage::getSingleton ( 'core/session' )->addSuccess ( $this->__ ( 'Updated successfully.' ) );
                Mage::helper ( 'airhotels/smsconfig' )->redirectVerification ();
            } catch ( Exception $e ) {
                /**
                 * Display error message for images upload
                 *
                 * Redirect to account verification page.
                 */
                Mage::getSingleton ( 'core/session' )->addError ( $this->__ ( $e->getMessage () ) );
                Mage::helper ( 'airhotels/smsconfig' )->redirectVerification ();
            }
        } elseif ($data [$tagVideo]) {
            /**
             * Get collection from verifyhost.
             *
             * @var $tagVideoFilter
             */
            $tagVideoFilter = Mage::getModel ( 'airhotels/verifyhost' )->filterTag ( $customerId, $tagVideo );
            /**
             * Define object
             *
             * @var $tagSave.
             */
            if ($tagVideoFilter) {
                $tagSave = Mage::getModel ( 'airhotels/verifyhost' )->load ( $tagVideoFilter );
            } else {
                $tagSave = Mage::getModel ( 'airhotels/verifyhost' );
            }
            /**
             * Set document upload success message.
             *
             * Redirect account verification.
             */
            $tagSave->setTagId ( $tagVideo )->setHostId ( $customerId )->setHostName ( $name )->setHostEmail ( $emailId )->setFilePath ( $data [$tagVideo] )->save ();
            Mage::getSingleton ( 'core/session' )->addSuccess ( $this->__ ( 'Updated successfully.' ) );
            Mage::helper ( 'airhotels/smsconfig' )->redirectVerification ();
        }
        /**
         * Redirect to account verification page.
         */
        Mage::helper ( 'airhotels/smsconfig' )->redirectVerification ();
    }
    /**
     * Function name: documentsaveAction()
     * The Id verifcation document has been stored in database
     *
     * By verifying the tag and also the type
     *
     * @var $data
     * @var $customerId
     * @var $customerData
     * @var $name
     * @var $emailId
     * @var $tagDocument
     */
    public function documentsaveAction() {
        ini_set ( 'post_max_size', '60M' );
        $data = $this->getRequest ()->getPost ();
        /**
         * Get customer id
         *
         * @var $customerId
         */
        $customerId = Mage::getSingleton ( 'customer/session' )->getId ();
        /**
         * Get customer details.
         */
        $customerData = Mage::getModel ( 'customer/customer' )->load ( $customerId );
        $name = $customerData ['firstname'];
        $emailId = $customerData ['email'];
        $tagDocument = $this->getRequest ()->getPost ( 'tag_document' );
        /**
         * create a folder to save the verified documents
         */
        $uploadsDocumentData = new Zend_File_Transfer_Adapter_Http ();
        $filesDataArray = $uploadsDocumentData->getFileInfo ();
        if ($filesDataArray [$tagDocument] ['name'] == '') {
            /**
             * Set document upload error message.
             *
             * Redirect to account verification page.
             */
            Mage::getSingleton ( 'core/session' )->addError ( $this->__ ( 'Please select and upload the documents.' ) );
            Mage::helper ( 'airhotels/smsconfig' )->redirectVerification ();
        }
        if ($filesDataArray [$tagDocument] ['name']) {
            try {
                $uploaderDocument = new Varien_File_Uploader ( $filesDataArray [$tagDocument] );
                /**
                 * Image upload using Varien_File_Uploader function
                 *
                 * Set allowed extention.
                 */
                $uploaderDocument->setAllowedExtensions ( array (
                        'pdf',
                        'txt' 
                ) );
                $uploaderDocument->setAllowRenameFiles ( true );
                $uploaderDocument->setAllowCreateFolders ( true );
                $uploaderDocument->setFilesDispersion ( false );
                /**
                 * Define document path.
                 *
                 * @var $localMediaPath,$documentPath
                 */
                $localMediaPath = Mage::getBaseDir ( 'media' ) . DS;
                $documentPath = 'airhotels' . DS . 'verified_documents' . DS . $customerId . DS . $tagDocument;
                $tagIdFilter = Mage::getModel ( 'airhotels/verifyhost' )->filterTag ( $customerId, $tagDocument );
                /**
                 * Check host tag id is set or not
                 */
                if ($tagIdFilter) {
                    $tagSave = Mage::getModel ( 'airhotels/verifyhost' )->load ( $tagIdFilter );
                    /**
                     * Remove existing document from list.
                     */
                    Mage::getModel ( 'airhotels/verifyhost' )->removeOldDocument ( $localMediaPath . $documentPath, $tagSave->getFilePath () );
                } else {
                    $tagSave = Mage::getModel ( 'airhotels/verifyhost' );
                }
                /**
                 * Save document details.
                 */
                $tagSave->setTagId ( $tagDocument )->setHostId ( $customerId )->setHostName ( $name )->setHostEmail ( $emailId )->setHostTags ( 0 );
                $result = $uploaderDocument->save ( $localMediaPath . $documentPath, $filesDataArray [$tagDocument] ['name'] );
                $filepath = str_replace ( "\\", '/', Mage::getBaseUrl ( Mage_Core_Model_Store::URL_TYPE_MEDIA ) . $documentPath . DS . $result ['file'] );
                $tagSave->setFilePath ( $filepath )->save ();
                /**
                 * Set success message.
                 *
                 * Redirect account verification page.
                 */
                Mage::getSingleton ( 'core/session' )->addSuccess ( $this->__ ( 'Updated successfully.' ) );
                Mage::helper ( 'airhotels/smsconfig' )->redirectVerification ();
            } catch ( Exception $e ) {
                /**
                 * Display error message for images upload
                 *
                 * Redirect account verification page.
                 */
                Mage::getSingleton ( 'core/session' )->addError ( $this->__ ( $e->getMessage () ) );
                Mage::helper ( 'airhotels/smsconfig' )->redirectVerification ();
            }
        } elseif ($data [$tagDocument]) {
            /**
             * Check existing documentId.
             */
            $tagIdFilter = Mage::getModel ( 'airhotels/verifyhost' )->filterTag ( $customerId, $tagDocument );
            /**
             * Create object.
             *
             * @var $tagSave.
             */
            if ($tagIdFilter) {
                $tagSave = Mage::getModel ( 'airhotels/verifyhost' )->load ( $tagIdFilter );
            } else {
                $tagSave = Mage::getModel ( 'airhotels/verifyhost' );
            }
            /**
             * Getting verified host information
             */
            $tagSave->setTagId ( $tagDocument )->setHostId ( $customerId )->setHostName ( $name )->setHostEmail ( $emailId )->setFilePath ( $data [$tagDocument] )->save ();
            /**
             * Set document upload success message.
             *
             * Redirect to account verification page.
             */
            Mage::getSingleton ( 'core/session' )->addSuccess ( $this->__ ( 'Updated successfully.' ) );
            Mage::helper ( 'airhotels/smsconfig' )->redirectVerification ();
        }
        /**
         * Redirect to account verification page.
         */
        Mage::helper ( 'airhotels/smsconfig' )->redirectVerification ();
    }
    /**
     * Function for subscription action
     */
    public function subscriptionAction() {
        /**
         * Get subscription.
         */
        return Mage::getModel ( 'airhotels/subscriptiontype' )->subscriptionfrequency ();
    }
}