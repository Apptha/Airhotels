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
class Apptha_Airhotels_Model_Calendar extends Mage_Core_Model_Abstract {
    /**
     * Function Name: 'Construct'
     * Method for Construct
     *
     * @see Varien_Object::_construct()
     */
    public function _construct() {
        /**
         * Calling the Parent Construct Method
         */
        parent::_construct ();
        /**
         * Initilizing the 'airhotels/calendar' Model
         */
        $this->_init ( 'airhotels/calendar' );
    }
    /**
     * Function Name: imageupload
     *
     * @param array $_FILES            
     * @param int $entity_id            
     * @return boolean
     */
    public function imageupload($FILES, $entity_id) {
        $returnValue = true;
        /**
         * Get width of image.
         */
        list ( $width ) = getimagesize ( $_FILES ["uploadfile"] ['tmp_name'] );
        /**
         * Check image width.
         * If exist more than 1000px return an error message.
         */
        if ($width < 1000) {
            $returnValue = false;
            Mage::getSingleton ( 'core/session' )->addError ( 'Please upload image width as more than 1000px.' );
            return $returnValue;
        }
        /**
         * Get the Media Gallery Imgae FILES
         */
        $mediagalleryId = Mage::helper ( 'airhotels/product' )->getmediagallery ();
        /**
         * Declaring the Values to '$propertyImage'
         */
        $propertyImage = str_replace ( ' ', '_', strtolower ( $FILES ['uploadfile'] ['name'] ) );
        /**
         * property Image with
         */
        $propertyImage = str_replace ( '(', '_', $propertyImage );
        /**
         * property Image with
         */
        $propertyImage = str_replace ( ')', '_', $propertyImage );
        /**
         * Explode the 'splitExtension ' Value.
         */
        $splitextension = explode ( ".", $propertyImage );
        $tempImageName = "";
        /**
         * Count the Colletion Array
         */
        $splitextensionCondition = count ( $splitextension );
        /**
         * Looping the $splitextensionCondition Value.
         */
        for($i = 0; $i < $splitextensionCondition - 1; $i ++) {
            $tempImageName .= $splitextension [$i];
        }
        /**
         * Property Image Value.
         */
        $propertyImage = $tempImageName . $entity_id . "_" . rand ( 0, 100000 ) . "." . $splitextension [count ( $splitextension ) - 1];
        /**
         * String spliting the Valueds
         */
        $magePath = str_split ( $propertyImage );
        if ($magePath [1] == '') {
            $magePath [1] = '_';
        }
        /**
         * ImagePath Value
         */
        $imagepath = $magePath [0] . '/' . $magePath [1] . '/' . $propertyImage;
        /**
         * Check weather the $FILES array set.
         */
        if (isset ( $FILES ['uploadfile'] ['name'] ) && $FILES ['uploadfile'] ['name'] != '') {
            try {
                $uploader = new Varien_File_Uploader ( 'uploadfile' );
                /**
                 * Set the Allow extensions
                 * 'jpg','jpeg','gif','png'
                 */
                $uploader->setAllowedExtensions ( array (
                        'jpg',
                        'jpeg',
                        'gif',
                        'png' 
                ) );
                $uploader->setAllowRenameFiles ( false );
                $uploader->setFilesDispersion ( false );
                /**
                 * Initialize the image path.
                 *
                 * @var $path
                 */
                $path = Mage::getBaseDir ( 'media' ) . DS . 'catalog' . DS . 'product' . DS . $magePath [0] . DS . $magePath [1] . DS;
                $pathThumbRoot = Mage::getBaseDir ( 'media' ) . DS . 'catalog' . DS . 'product' . DS . 'thumbs';
                $pathThumbRootOne = Mage::getBaseDir ( 'media' ) . DS . 'catalog' . DS . 'product' . DS . 'thumbs' . DS . $magePath [0];
                $pathThumbRootTwo = Mage::getBaseDir ( 'media' ) . DS . 'catalog' . DS . 'product' . DS . 'thumbs' . DS . $magePath [0] . DS . $magePath [1] . DS;
                $uploader->save ( $path, $propertyImage );
                /**
                 * Create thumnail images.
                 */
                $this->createThumbnail ( $path, $propertyImage, $pathThumbRoot, $pathThumbRootOne, $pathThumbRootTwo );
            } catch ( Exception $e ) {
                /**
                 * Set error message.
                 */
                Mage::getSingleton ( 'core/session' )->addError ( $e->getMessage () );
                return;
            }
            $coreResource = Mage::getSingleton ( 'core/resource' );
            $connection = $coreResource->getConnection ( 'core_read' );
            try {
                /**
                 * Insert image details in core table.
                 */
                $connection->insert ( $coreResource->getTableName ( 'catalog_product_entity_media_gallery' ), array (
                        'attribute_id' => $mediagalleryId,
                        'entity_id' => $entity_id,
                        'value' => $imagepath 
                ) );
            } catch ( Exception $ex ) {
                /**
                 * Core Error Value.
                 */
                Mage::getSingleton ( 'core/session' )->addError ( $ex->getMessage () );     
                $returnValue = false;
            }            
            return $returnValue;
        }
    }
    /**
     * Function Name: createThumbnail
     * creting thumbs
     *
     * @param string $pathToImages            
     * @param string $fname            
     * @param string $pathThumbRoot            
     * @param string $pathThumbRootOne            
     * @param string $pathThumbRootTwo            
     * @return boolean
     */
    public function createThumbnail($pathToImages, $fname, $pathThumbRoot, $pathThumbRootOne, $pathThumbRootTwo) {
        /**
         * Create instances for 'Varien_Io_File'
         */
        $dirClass = new Varien_Io_File ();
        /**
         * Check the Value of '$pathThumbRoot'
         */
        if (! empty ( $pathThumbRoot )) {
            /**
             * DirClass call the checkAndCreateFolder value.
             */
            $dirClass->checkAndCreateFolder ( $pathThumbRoot );
        }
        /**
         * Check the Value of '$pathThumbRootOne'
         */
        if (! empty ( $pathThumbRootOne )) {
            /**
             * DirClass call the checkAndCreateFolder value.
             */
            $dirClass->checkAndCreateFolder ( $pathThumbRootOne );
        }
        /**
         * Check the Value of '$pathThumbRootTwo'
         */
        if (! empty ( $pathThumbRootTwo )) {
            /**
             * DirClass call the checkAndCreateFolder value.
             */
            $dirClass->checkAndCreateFolder ( $pathThumbRootTwo );
        }
        try {
            /**
             * create the object for 'Varien_Image'
             */
            $imageObj = new Varien_Image ( $pathToImages . $fname );
            /**
             * set the $thumbWidth
             */
            $width = $imageObj->getOriginalWidth ();
            /**
             * Set the Heiht
             */
            $height = $imageObj->getOriginalHeight ();
            /**
             * calculate thumbnail size
             */
            $thumbWidth = 100;
            /**
             * Claculating the new Height
             */
            $newHeight = floor ( $height * ($thumbWidth / $width) );
            /**
             * Call the constrainOnly
             */
            $imageObj->constrainOnly ( TRUE );
            /**
             * Call the keepAspectRatio
             */
            $imageObj->keepFrame ( FALSE );
            /**
             * Call the resize
             */
            $imageObj->keepAspectRatio ( FALSE );
            /**
             * Call the keepFrame
             */
            $imageObj->resize ( $thumbWidth, $newHeight );
            /**
             * Save the image.
             */
            $imageObj->save ( $pathThumbRootTwo . $fname );
        } catch ( Exception $ex ) {
            /**
             * Add the error notification
             */
            Mage::getSingleton ( 'core/session' )->addError ( $ex->getMessage () );
            return false;
        }
    }
    /**
     * Function Name: getDate
     * Get Date
     *
     * @param int $productid            
     * @return string
     */
    public function getdate($productid) {
        /**
         * Calculate the 'Range' Value.
         */
        $range = Mage::helper ( 'airhotels/general' )->getRangeCount ( $productid );
        /**
         * Calculate the CountValue.
         */
        $count = count ( $range );
        /**
         * looping the Colletion
         */
        for($i = 0; $i <= $count; $i ++) {
            /**
             * Set the FromDate Value.
             */
            $fromdate = $range [$i] [fromdate];
            /**
             * Set the Todate Value.
             */
            $todate = $range [$i] [todate];
            /**
             * Check value with the 'entity_id' and 'entity_id'
             */
            if ($fromdate < $todate) {
                /**
                 * Get the '$datesRange' Value.
                 */
                $datesRange [] = date ( 'Y-n-j', strtotime ( $fromdate ) );
                /**
                 * Date1 Value.
                 */
                $date = strtotime ( $fromdate );
                /**
                 * Date2 Value.
                 */
                $dateTo = strtotime ( $todate );
                while ( $date != $dateTo ) {
                    $date = mktime ( 0, 0, 0, date ( "m", $date ), date ( "d", $date ) + 1, date ( "Y", $date ) );
                    $datesRange [] = date ( 'Y-n-j', $date );
                }
            }
        }
        /**
         * Return the '$datesRange' array
         */
        return $datesRange;
    }
    /**
     * Function Name: removeImage
     * Removing property image functionality
     */
    public function removeImage($imageId, $entityId, $albumCover="") {        
        $currentExperienceId = Mage::getSingleton('customer/session')->getCurrentExperienceId();
        if($albumCover != "" && $albumCover == "yes") {            
            $_gallery = Mage::getModel('catalog/product')->load($currentExperienceId)->getMediaGalleryImages();            
            if(count($_gallery) == 1) {                
                Mage::getSingleton ( 'core/session' )->addError("We need atleast one photo for album cover");                
                return false;
            }
        }        
        $resource = Mage::getSingleton ( 'core/resource' );
        /**
         * Get image table name.
         *
         * @var $imageTableName
         */
        $imageTableName = $resource->getTableName ( 'catalog/product_attribute_media_gallery' );
        $coreResource = Mage::getSingleton ( 'core/resource' );
        $connection = $coreResource->getConnection ( 'core_read' );
        $select = $connection->select ()->from ( array (
                'ac' => $coreResource->getTableName ( 'catalog/product_attribute_media_gallery' ) 
        ), array (
                'value' 
        ) )->where ( 'value_id = ?', $imageId );
        $result = $connection->fetchRow ( $select );
        foreach ( $result as $res ) {
            $imageLocation = $res;
            break;
        }
        $deleteResult = 0;
        try {
            /**
             * Craete the databse connetion
             */
            $connection = Mage::getSingleton ( 'core/resource' )->getConnection ( 'core_read' );
            /**
             * Assign fields to
             * 'value_id'
             * 'entity_id'
             */
            $condition [] = $connection->quoteInto ( 'value_id =?', $imageId );
            $condition [] = $connection->quoteInto ( 'entity_id =?', $entityId );
            /**
             * Delete the Vlaue.
             */
            $connection->delete ( $imageTableName, $condition );
            $deleteResult = 1;
        } catch ( Exception $ex ) {
            /**
             * Add error Notification
             */
            Mage::getSingleton ( 'core/session' )->addError ( $ex->getMessage () );            
        }
        /**
         * Check weather the '$deleteResult' isset
         */
        if ($deleteResult) {
            try {
                $dirClass = new Varien_Io_File ();
                $fileFlag = $dirClass->fileExists ( Mage::getBaseDir () . DS . "media" . DS . "catalog" . DS . "product" . DS . $imageLocation );
                /**
                 * Check weather the $fileFlag
                 */
                if ($fileFlag) {
                    $dirClass->rm ( Mage::getBaseDir () . DS . "media" . DS . "catalog" . DS . "product" . DS . $imageLocation );
                    $dirClass->rm ( Mage::getBaseDir () . DS . "media" . DS . "catalog" . DS . "product" . DS . "thumbs" . DS . $imageLocation );
                }
            } catch ( Exception $ex ) {
                /**
                 * Set error messge.
                 */
                Mage::getSingleton ( 'core/session' )->addError ( $ex->getMessage () );
                return false;
            }
        }
        if($albumCover != "" && $albumCover == "yes") {                        
            $storeId = Mage::app ()->getStore ()->getId ();
            $websiteId = Mage::app ()->getWebsite ()->getId ();
            $product = Mage::getModel ( 'catalog/product' )->load ( $currentExperienceId );
            $product->setStoreID ( $storeId );
            $product->setThumbnail ( '' )->setImage ( '' )->setSmallImage ( '' )->setWebsiteIDs ( array (
                    $websiteId
            ) );                                
            $product->save ();                    
        }
        return true;
    }
    /**
     * Insert new message in customerinbox table and send inbox notification
     *
     * @param array $data            
     * @return boolean
     */
    public function saveInbox($data) {
        /**
         * Get customer details.
         *
         * @var $customer
         */
        $customer = Mage::getSingleton ( 'customer/session' )->getCustomer ();
        $customerId = $customer->getId ();
        /**
         * Get customer inbox collection.
         *
         * @var $inboxModel.
         */
        $inboxModel = Mage::getModel ( 'airhotels/customerinbox' );
        $inboxModel->setSenderId ( $customerId );
        $inboxModel->setReceiverId ( $data ["hostId"] );
        $inboxModel->setProductId ( $data ["experienceId"] );
        $inboxModel->setCheckin ( $data ["startOn"] );
        $inboxModel->setCheckout ( $data ["endOn"] );
        $inboxModel->setGuest ( $data ["peoples"] );
        $inboxModel->setMessage ( $data ["mailContent"] );
        $inboxModel->setCanCall ( $data ["hostCall"] );
        $inboxModel->setTimezone ( $data ["guest_preference"] );
        $inboxModel->setMobileNo ( $data ["mobileNo"] );
        $inboxModel->setStartAt ( $data ["startAt"] );
        $inboxModel->setEndAt ( $data ["endAt"] );
        $inboxModel->setPropertyServiceFrom ( $data ["property_service_from"] );
        $inboxModel->setPropertyServiceFromPeriod ( $data ["property_service_from_period"] );
        $inboxModel->setPropertyServiceTo ( $data ["property_service_to"] );
        $inboxModel->setPropertyServiceToPeriod ( $data ["property_service_to_period"] );
        try {
            $inboxResult = $inboxModel->save ();
            /**
             * Check inbox notification enable or not.
             */
            if (( int ) Mage::getStoreConfig ( 'airhotels/inbox_notification/enable' ) == 0) {
                /**
                 * Initilizing inbox message data
                 */
                $message = $data ["mailContent"];
                $customerName = $customer->getName ();
                $hostId = $data ["hostId"];
                /**
                 * Get inbox url.
                 *
                 * @var $inboxUrl
                 */
                $inboxUrl = Mage::getUrl ( 'property/property/inbox' );
                /**
                 * Get domain name.
                 *
                 * @var $domainName
                 */
                $domainName = Mage::app ()->getFrontController ()->getRequest ()->getHttpHost ();
                /**
                 * Get host details.
                 *
                 * @var $host
                 */
                $host = Mage::getModel ( 'customer/customer' )->load ( $hostId );
                $hostName = $host->getName ();
                $hostEmail = $host->getEmail ();
                /**
                 * Getting store name
                 */
                $storeName = Mage::app ()->getStore ()->getGroup ()->getName ();
                /**
                 * Get email templete id.
                 *
                 * @var $templateId
                 */
                $templateId = ( int ) Mage::getStoreConfig ( 'airhotels/inbox_notification/newinbox_template' );
                if ($templateId) {
                    $emailTemplate = Mage::getModel ( 'core/email_template' )->load ( $templateId );
                } else {
                    $emailTemplate = Mage::getModel ( 'core/email_template' )->loadDefault ( 'airhotels_inbox_notification_newinbox_template' );
                }
                /**
                 * Set sender name.
                 *
                 * Set sender email.
                 */
                $emailTemplate->setSenderName ( $customerName );
                $emailTemplate->setSenderEmail ( 'noreply@' . Mage::app ()->getRequest ()->getServer ( 'HTTP_HOST' ) );
                $emailTemplateVariables = (array (
                        'hostname' => $hostName,
                        'domainname' => $domainName,
                        'customername' => $customerName,
                        'message' => $message,
                        'storename' => $storeName,
                        'inboxurl' => $inboxUrl 
                ));
                $emailTemplate->setDesignConfig ( array (
                        'area' => 'frontend' 
                ) );
                $emailTemplate->getProcessedTemplate ( $emailTemplateVariables );
                /**
                 * Sending mail to host
                 */
                $emailTemplate->send ( $hostEmail, $hostName, $emailTemplateVariables );
            }
        } catch ( Exception $ex ) {
            /**
             * Set error message.
             */
            Mage::getSingleton ( 'core/session' )->addError ( $ex->getMessage () );
            return false;
        }
        /**
         * Return inbox result.
         */
        return $inboxResult;
    }
    /**
     * Function Name: getOutboxDetails
     * Get out box message collection
     *
     * @return array message details
     */
    public function getOutboxDetails() {
        /**
         * Table Prefix Value.
         */
        $tPrefix = ( string ) Mage::getConfig ()->getTablePrefix ();
        /**
         * append the table Value.
         */
        $airhotelsCustomerReply = $tPrefix . 'airhotels_customer_reply';
        /**
         * Intialise the array as $resultArray
         */
        $resultArray = array ();
        /**
         * Save the customer details in session Value.
         */
        $customer = Mage::getSingleton ( 'customer/session' )->getCustomer ();
        /**
         * Customer ID Value.
         */
        $customerId = $customer->getId ();
        $result = Mage::getModel ( 'airhotels/customerinbox' )->getCollection ()->addFieldToFilter ( 'sender_id', $customerId )->addFieldToFilter ( 'isdelete', 0 )->addFieldToSelect ( 'message_id' )->setOrder ( 'created_date', 'DESC' );
        /**
         * Get Colletion of 'nextResult '
         */
        $nextResult = Mage::getModel ( 'airhotels/customerinbox' )->getCollection ()->addFieldToSelect ( 'message_id' );
        /**
         * NextResult Value with joining the arrays
         */
        $nextResult->getSelect ()->joinInner ( array (
                'airhotels_customer_reply' => $airhotelsCustomerReply 
        ), "(airhotels_customer_reply.message_id = main_table.message_id  AND main_table.is_reply = '1' AND airhotels_customer_reply.is_delete = '0' AND airhotels_customer_reply.customer_id='$customerId')", array () );
        $nextResult->getSelect ()->group ( 'airhotels_customer_reply.message_id' );
        $nextResult->setOrder ( 'airhotels_customer_reply.created_date', 'DESC' );
        /**
         * Iterating the foreach Value.
         */
        foreach ( $result as $res ) {
            $resultArray [] = $res->getMessageId ();
        }
        /**
         * Iterating the '$nextResult' Colletion
         */
        foreach ( $nextResult as $value ) {
            $resultArray [] = $value->getMessageId ();
        }
        /**
         * $uniqueResultArray array with array_unique
         */
        $uniqueResultArray = array_unique ( $resultArray );
        /**
         * Return customer inbox collection.
         * Filter by message id
         * Sort order by created_date.
         */
        return Mage::getModel ( 'airhotels/customerinbox' )->getCollection ()->addFieldToFilter ( 'message_id', array (
                'in' => $uniqueResultArray 
        ) )->setOrder ( 'created_date', 'DESC' );
    }
    /**
     * Function Name: markAsRead
     * Mark the readed mails
     *
     * @param int $messageid            
     * @return array
     */
    public function markAsRead($messageid) {
        /**
         * Save the customer info into session
         */
        $customer = Mage::getSingleton ( 'customer/session' )->getCustomer ();
        /**
         * get the value of customerID
         */
        $customerId = $customer->getId ();
        /**
         * Get the table Name
         */
        $tableName = Mage::getSingleton ( 'core/resource' )->getTableName ( 'airhotels_customer_inbox' );
        /**
         * FieldReciever Value.
         */
        $fieldReceiver = 'receiver_read';
        /**
         * /* Select Result Value.
         */
        $selectResult = Mage::getModel ( 'airhotels/calendarsync' )->updateCustInbox ( $messageid, $customerId, $tableName, $fieldReceiver );
        $fieldReceiver = 'sender_read';
        Mage::getModel ( 'airhotels/calendarsync' )->updateCustInbox ( $messageid, $customerId, $tableName, $fieldReceiver );
        /**
         * Return the value of '$selectResult'
         */
        return $selectResult;
    }
    /**
     * Functio nName: deleteMessage
     * Delete the Messages
     *
     * @param int $messageid            
     * @param int $action            
     * @return string
     */
    public function deleteMessage($messageid, $action) {
        $selectResult = '';
        /**
         * Set core write permission.
         *
         * @var $connection
         */
        $connection = Mage::getSingleton ( 'core/resource' )->getConnection ( 'core_write' );
        /**
         * Get table prifix.
         *
         * @var $tPrefix
         */
        $tPrefix = ( string ) Mage::getConfig ()->getTablePrefix ();
        $customerCustomerInbox = $tPrefix . 'airhotels_customer_inbox';
        $airhotelsCustomerReply = $tPrefix . 'airhotels_customer_reply';
        /**
         * save the customer info into session
         */
        $customer = Mage::getSingleton ( 'customer/session' )->getCustomer ();
        /**
         * Get the customer ID Vlaue.
         */
        $customerId = $customer->getId ();
        /**
         * Check the value of $action
         */
        if ($action == "in") {
            try {
                /**
                 * Begin the Transaction
                 */
                $connection->beginTransaction ();
                $fields = array ();
                $where = array ();
                $fields ['is_receiver_delete'] = 1;
                $where [] = $connection->quoteInto ( 'message_id IN (?)', $messageid );
                $where [] = $connection->quoteInto ( 'receiver_id = ?', $customerId );
                $connection->update ( $customerCustomerInbox, $fields, $where );
                $selectResult = $connection->commit ();
                /**
                 * Begin the Transaction
                 */
                $connection->beginTransaction ();
                $fields = array ();
                $where = array ();
                $fields ['is_sender_delete'] = 1;
                /**
                 * set the where clause to
                 * 'message_id'
                 * 'sender_id'
                 * 'is_reply'
                 */
                $where [] = $connection->quoteInto ( 'message_id IN (?)', $messageid );
                $where [] = $connection->quoteInto ( 'sender_id = ?', $customerId );
                $where [] = $connection->quoteInto ( 'is_reply = ?', 1 );
                $connection->update ( $customerCustomerInbox, $fields, $where );
                /**
                 * Commit the Transaction Value.
                 */
                $selectResult = $connection->commit ();
            } catch ( Exception $ex ) {
                /**
                 * Add the error Notification
                 */
                Mage::getSingleton ( 'core/session' )->addError ( $ex->getMessage () );
            }
        } else {
            try {
                /**
                 * Connection has been begin the transaction
                 */
                $connection->beginTransaction ();
                /**
                 * Initalsie the value $where,$fields
                 */
                $where = array ();
                $fields = array ();
                $fields ['is_delete'] = 1;
                $where [] = $connection->quoteInto ( 'message_id IN (?)', $messageid );
                $where [] = $connection->quoteInto ( 'customer_id = ?', $customerId );
                $connection->update ( $airhotelsCustomerReply, $fields, $where );
                /**
                 * Commit the Transction
                 */
                $selectResult = $connection->commit ();
            } catch ( Exception $ex ) {
                /**
                 * Set error message.
                 */
                Mage::getSingleton ( 'core/session' )->addError ( $ex->getMessage () );
            }
            try {
                /**
                 * Begin the Transction
                 */
                $connection->beginTransaction ();
                $where = array ();
                $fields = array ();
                $fields ['isdelete'] = 1;
                $where [] = $connection->quoteInto ( 'message_id IN (?)', $messageid );
                $connection->update ( $customerCustomerInbox, $fields, $where );
                /**
                 * Commit the Transction Value.
                 */
                $selectResult = $connection->commit ();
            } catch ( Exception $ex ) {
                /**
                 * Set error message.
                 */
                Mage::getSingleton ( 'core/session' )->addError ( $ex->getMessage () );
            }
        }
        /**
         * Retuen collection result.
         */
        return $selectResult;
    }
    /**
     * Function Name: replyMail
     * Get reply message collection
     *
     * @param int $messageid            
     * @param int $customerid            
     * @param string $message            
     * @return int
     */
    public function replyMail($messageid, $customerid, $message) {
        $selectResult = 0;
        /**
         * Get the table Name.
         */
        $tableName = Mage::getSingleton ( 'core/resource' )->getTableName ( 'airhotels_customer_inbox' );
        /**
         * update the senderRead Valeu
         */
        Mage::getModel ( 'airhotels/calendarsync' )->updateSenderRead ( $messageid, $customerid, $tableName );
        /**
         * update the senderRead Valeu
         */
        $this->updateReceiverRead ( $messageid, $customerid, $tableName );
        $msgInbox = Mage::getModel ( 'airhotels/customerreply' );
        $msgInbox->setMessageId ( $messageid );
        $msgInbox->setCustomerId ( $customerid );
        $msgInbox->setMessage ( $message );
        $msgInbox->save ();
        $selectResult = Mage::getModel ( 'airhotels/calendarsync' )->returnVal ();
        /**
         * Check inbox notification enable or not.
         */
        if (( int ) Mage::getStoreConfig ( 'airhotels/inbox_notification/enable' ) == 0) {
            Mage::getModel ( 'airhotels/calendarsync' )->getMailData ( $messageid, $customerid, $tableName );
        }
        /**
         * Return the $selectResult
         */
        return $selectResult;
    }
    /**
     * Function Name: updateReceiverRead
     * Update the receivee read mails
     *
     * @param int $messageid            
     * @param int $customerid            
     * @param string $tableName            
     */
    public function updateReceiverRead($messageid, $customerid, $tableName) {
        $connection = Mage::getSingleton ( 'core/resource' )->getConnection ( 'core_write' );
        try {
            /**
             * Transaction has been begin
             */
            $connection->beginTransaction ();
            /**
             * intialise the $fields, $where array.
             * 'is_reply'
             * 'receiver_read'
             * 'is_receiver_delete'
             * 'is_sender_delete'
             */
            $fields = array ();
            $where = array ();
            $fields ['is_reply'] = 1;
            $fields ['receiver_read'] = 0;
            $fields ['is_receiver_delete'] = 0;
            $fields ['is_sender_delete'] = 0;
            /**
             * where array used to collet the data
             * 'receiver_id'
             * 'sender_id'
             */
            $where [] = $connection->quoteInto ( 'message_id = ?', $messageid );
            $where [] = $connection->quoteInto ( 'sender_id = ?', $customerid );
            $connection->update ( $tableName, $fields, $where );
            /**
             * commiting the vlaues.
             */
            $selectResult = $connection->commit ();
            $selectResult = 1;
        } catch ( Exception $ex ) {
            /**
             * Retun error message.
             * Set select result as 0.
             */
            Mage::getSingleton ( 'core/session' )->addError ( $ex->getMessage () );
            $selectResult = 0;
        }
    }
    /**
     * Special price
     */
    public function getSpecialPriceDays($count, $value) {
        $avail = array ();
        for($j = 0; $j < $count; $j ++) {
            $avail [$value [$j] [1]] = $value [$j] [3];
        }
        return $avail;
    }
    /**
     * Blocked date price values
     */
    public function getBlockedDays($blockedCount, $blocked) {
        $avail = array ();
        for($k = 0; $k < $blockedCount; $k ++) {
            $avail [$blocked [$k] [1]] = $blocked [$k] [4];
        }
        return $avail;
    }
}