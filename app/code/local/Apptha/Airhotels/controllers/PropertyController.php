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
class Apptha_Airhotels_PropertyController extends Mage_Core_Controller_Front_Action {
    /**
     * Function Name: indexAction
     * Airhotels Index property Action
     */
    public function indexAction() {
        /**
         * Load layout.
         */
        $this->loadLayout ();
        $this->renderLayout ();
        /**
         * Redirect to base url.
         */
        $this->_redirectUrl ( Mage::getBaseUrl () );
    }
    /**
     * Function Name: checkavailAction
     * To Check the property dates the property was available or not
     */
    public function checkavailAction() {
        Mage::getModel ( 'airhotels/airhotels' )->checkavail ();
    }
    /**
     * Function Name: calenderAction
     * Calendar for Property Page
     */
    public function calenderAction() {
        $tableHtmlValue = '';
        /**
         * Get product id.
         */
        $productId = Mage::app ()->getRequest ()->getParam ( 'productid' );
        /**
         * Explode the date Value
         */
        $dateSplit = explode ( "__", Mage::app ()->getRequest ()->getParam ( 'date' ) );
        /**
         * Getting type of product hourly or daily
         * Get property time.
         */
        $propertyTime = Mage::getModel ( 'catalog/product' )->load ( $productId )->getPropertyTime ();
        $propertyTimeData = Mage::helper ( 'airhotels/airhotel' )->getPropertyTimeLabelByOptionId ();
        $propertyDateValue = Mage::app ()->getRequest ()->getParam ( 'date' );
        /**
         * Get blocked date array.
         */
        $blockedArray = Mage::getModel ( 'airhotels/airhotels' )->getBlockdate ( $productId, $propertyDateValue );
        /**
         * Assign availiable, blocked and not availiable date
         */
        $avail = Mage::getModel ( 'airhotels/customerphoto' )->getDaysForAvailDays ( count ( $blockedArray [0] ), $blockedArray [0] );
        $blockedArr = Mage::getModel ( 'airhotels/customerphoto' )->getDaysForAvailDays ( count ( $blockedArray [1] ), $blockedArray [1] );
        $blockedArrayCust = Mage::getModel ( 'airhotels/airhotels' )->getBlockdateBook ( $productId, $propertyDateValue );
        /**
         * Get bloacked dates for property.
         */
        $blocked = array_merge ( $blockedArr, $blockedArrayCust );
        /**
         * get Not Available Days
         */
        $not_available = Mage::getModel ( 'airhotels/customerphoto' )->getDaysForAvailDays ( count ( $blockedArray [2] ), $blockedArray [2] );
        $specialAvailable = Mage::getModel ( 'airhotels/calendar' )->getSpecialPriceDays ( count ( $blockedArray [0] ), $blockedArray [0] );
        $_spl = array ();
        foreach ( $specialAvailable as $key => $value ) {
            $available = explode ( ",", $key );
            foreach ( $available as $_val ) {
                $spDay = ( int ) $_val;
                $_spl [$spDay] = $value;
            }
        }
        $partiallyBookedview = '';
        $partiallyBookedarray = array ();
        /**
         * Get houly property or not
         */
        $hourlyEnabledOrNot = Mage::helper ( 'airhotels/product' )->getHourlyEnabledOrNot ();
        $speAvailArray = array ();
        if ($propertyTime == $propertyTimeData && $hourlyEnabledOrNot == 0) {            
            /**
             * Get dates for not available by host
             */
            $blockedHourlyArray = Mage::getModel ( 'airhotels/airhotels' )->getHourlywiseBlockdate ( $productId, $propertyDateValue );
            $blockedArrValue = Mage::helper ( 'airhotels/productconfiguration' )->getHourlyNotAvailableAction ( count ( $blockedHourlyArray [1] ), $blockedHourlyArray [1] );
            $speAvailHourly = Mage::getModel ( 'airhotels/calendar' )->getSpecialPriceDays ( count ( $blockedHourlyArray [0] ), $blockedHourlyArray [0] );
            /**
             * Get dates blocked by host
             */
            
            $notAvailArray = Mage::helper ( 'airhotels/productconfiguration' )->getHourlyNotAvailableAction ( count ( $blockedHourlyArray [2] ), $blockedHourlyArray [2] );
            /**
             * Get houlywise blocked details.
             */
            $hourlywiseBlockDetails = array_merge ( $notAvailArray, $blockedArrValue );            
            $blocked = array_merge ( $blocked, $hourlywiseBlockDetails );
            /**
             * Merge hours and days wise block dates
             */
            foreach ( $speAvailHourly as $key => $value ) {
                $avail = explode ( ",", $key );
                if (count ( $avail ) == 1) {
                    $speAvailArray [] = $key;
                }
            }
            /**
             * Day wise blocked details
             */
            $specialAndNotAvailableDays = array_merge ( $notAvailArray, $speAvailArray );
            
            $blockAndSpeicalAndNotAvailableDays = array_merge ( $blockedArrValue, $specialAndNotAvailableDays );            
            $daywiseBlockedarray = array_merge ( $blockedArrayCust, $blockAndSpeicalAndNotAvailableDays );
            $uniquePropertyarray = array_unique ( $daywiseBlockedarray );
            $partiallyBookedview = Mage::getModel ( 'airhotels/customerinbox' )->getPartiallyBlockdateBook ( $uniquePropertyarray, $productId, $propertyDateValue );
            $partiallyBookedarray = Mage::getSingleton ( 'core/session' )->getPartiallyBookedArray ();
        }
        /**
         * Split dates.
         */
        $x = $dateSplit [0];
        if ($x == "") {
            $x = date ( "n" );
        }
        $yearVal = $dateSplit [1];
        $dateVal = strtotime ( "$yearVal/$x/1" );
        $dayVal = date ( "D", $dateVal );
        $prevYearVal = $yearVal;
        $nextYearVal = $yearVal;
        $prevMonthVal = intval ( $x ) - 1;
        $nextMonthVal = intval ( $x ) + 1;
        /**
         * if current month is December or January month navigation links have to be updated to point to next / prev years
         */
        if ($x == 12) {
            $nextMonthVal = 1;
            $nextYearVal = $yearVal + 1;
        }
        if ($x == 1) {
            $prevMonthVal = 12;
            $prevYearVal = $yearVal - 1;
        }
        $totaldays = date ( "t", $dateVal );
        /**
         * Get the table Value
         */
        $tableHtmlValue = Mage::getModel ( 'airhotels/customerphoto' )->tableHtmlValue ();
        /**
         * Initialize custom array.
         */
        $customArray = array (
                'day' => $dayVal,
                'totaldays' => $totaldays,
                'x' => $x,
                'year' => $yearVal,
                'propertyTimeData' => $propertyTimeData,
                'propertyTime' => $propertyTime,
                'notAvail' => $not_available 
        );
        /**
         * Get calender html style.
         */
        $tableHtmlValue = Mage::getModel ( 'airhotels/customerphoto' )->calendarStyle ( $speAvailArray, $blocked, $partiallyBookedarray, $tableHtmlValue, $_spl, $partiallyBookedview, $customArray );
        $tableHtmlValue = $tableHtmlValue . '</table>';
        $this->getResponse ()->setBody ( $tableHtmlValue );
    }
    /**
     * Function Name: calendarviewAction()
     * Assign available, blocked and not availiable date
     *
     * Mycalendar layout
     */
    public function calendarviewAction() {
        $htmlElementValue = $partiallyBookedView = '';
        /**
         * Get product id.
         */
        $productId = $this->getRequest ()->getParam ( 'productid' );
        $dateSplit = explode ( "__", $this->getRequest ()->getParam ( 'date' ) );
        /**
         * Get blocked array.
         */
        $blockedArray = Mage::getModel ( 'airhotels/airhotels' )->getBlockdate ( $productId, $this->getRequest ()->getParam ( 'date' ) );
        $avail = Mage::getModel ( 'airhotels/customerphoto' )->getDaysForAvailDays ( count ( $blockedArray [0] ), $blockedArray [0] );
        $blockedArr = Mage::getModel ( 'airhotels/customerphoto' )->getDaysForAvailDays ( count ( $blockedArray [1] ), $blockedArray [1] );
        $blockedArrayCust = Mage::getModel ( 'airhotels/airhotels' )->getBlockdateBook ( $productId, $this->getRequest ()->getParam ( 'date' ) );
        $blocked = array_merge ( $blockedArr, $blockedArrayCust );  
        /**
         * Get type of product hourly or daily
         */
        $propertyTime = Mage::getModel ( 'catalog/product' )->load ( $productId )->getPropertyTime ();
        $propertyTimeData = Mage::helper ( 'airhotels/airhotel' )->getPropertyTimeLabelByOptionId ();
        $partiallyBookedArray = $_sp = $_blocked = $speAvailArray = array ();
        $notAvail = Mage::getModel ( 'airhotels/customerphoto' )->getDaysForAvailDays ( count ( $blockedArray [2] ), $blockedArray [2] );
        /**
         * Get special aviail dates.
         */
        $specialAvail = Mage::getModel ( 'airhotels/calendar' )->getSpecialPriceDays ( count ( $blockedArray [0] ), $blockedArray [0] );        
        foreach ( $specialAvail as $key => $value ) {
            $avail = explode ( ",", $key );
            foreach ( $avail as $_val ) {
                $spDay = ( int ) $_val;
                $_sp [$spDay] = $value;
            }
        }  
        /**
         * Get houly property or not.
         */
        $hourlyEnabledOrNot = Mage::helper ( 'airhotels/product' )->getHourlyEnabledOrNot ();
        if ($propertyTime == $propertyTimeData && $hourlyEnabledOrNot == 0) {
            /**
             * Get dates blocked by host
             */
            $propertyDateValue = Mage::app ()->getRequest ()->getParam ( 'date' );
            $blockedHourlyArray = Mage::getModel ( 'airhotels/airhotels' )->getHourlywiseBlockdate ( $productId, $propertyDateValue );
            $notAvailArray = Mage::helper ( 'airhotels/productconfiguration' )->getHourlyNotAvailableAction ( count ( $blockedHourlyArray [2] ), $blockedHourlyArray [2] );
            $blockedArrValue = Mage::helper ( 'airhotels/productconfiguration' )->getHourlyNotAvailableAction ( count ( $blockedHourlyArray [1] ), $blockedHourlyArray [1] );
            $speAvailHourly = Mage::getModel ( 'airhotels/calendar' )->getSpecialPriceDays ( count ( $blockedHourlyArray [0] ), $blockedHourlyArray [0] );
            $hourlywiseBlockDetails = array_merge ( $notAvailArray, $blockedArrValue );
            /**
             * Merge hours and days wise block dates
             */
            $blocked = array_merge ( $blocked, $hourlywiseBlockDetails );
            foreach ( $speAvailHourly as $key => $value ) {
                $avail = explode ( ",", $key );
                if (count ( $avail ) == 1) {
                    $speAvailArray [] = $key;
                }
            }
            $specialAndNotAvailDays = array_merge ( $notAvailArray, $speAvailArray );
            $blockAndSpeicalAndNotDays = array_merge ( $blockedArrValue, $specialAndNotAvailDays );
            /**
             * Day wise blocked details
             */
            $daywiseBlockedArray = array_merge ( $blockedArrayCust, $blockAndSpeicalAndNotDays );
            $uniquePropertyArray = array_unique ( $daywiseBlockedArray );
            $partiallyBookedView = Mage::getModel ( 'airhotels/customerinbox' )->getPartiallyBlockdateBook ( $uniquePropertyArray, $productId, $propertyDateValue );
            $partiallyBookedArray = Mage::getSingleton ( 'core/session' )->getPartiallyBookedArray ();
        }
        $x = $dateSplit [0];
        if ($x == "") {
            $x = date ( "n" );
        }
        $year = $dateSplit [1];
        $date = strtotime ( "$year/$x/1" );
        $day = date ( "D", $date );
        $prevYear = $year;
        $nextYear = $year;
        $prevMonth = intval ( $x ) - 1;
        $nextMonth = intval ( $x ) + 1;
        /**
         * if current month is Decembe or January month navigation links have to be updated to point to next / prev years
         */
        if ($x == 12) {
            $nextMonth = 1;
            $nextYear = $year + 1;
        }
        if ($x == 1) {
            $prevMonth = 12;
            $prevYear = $year - 1;
        }
        $totaldays = date ( "t", $date );
        $htmlElementValue = Mage::helper ( 'airhotels/calendar' )->HtmlTable ( $prevMonth, $prevYear, $nextMonth, $nextYear, $productId, $date );
        $dayDataArray = Mage::helper ( 'airhotels/vieworder' )->getDateArray ();
        $st = $dayDataArray [$day];
        $tl = Mage::helper ( 'airhotels/vieworder' )->getDaysCount ($st,$totaldays);
        $ctr = $d = 1;
        for($i = 1; $i <= $tl; $i ++) {
            if ($ctr == 1) {
                $htmlElementValue = $htmlElementValue . "<tr class='blockcal'>";
            }            
            $arrayHtmlElement = array('i'=>$i,'st'=>$st,'d'=>$d,'totaldays'=>$totaldays,'year'=>$year,'x'=>$x,'htmlElementValue'=>$htmlElementValue,'date'=>$date,'partiallyBookedArray'=>$partiallyBookedArray,'propertyTime'=>$propertyTime,'propertyTimeData'=>$propertyTimeData,'hourlyEnabledOrNot'=>$hourlyEnabledOrNot,'blocked'=>$blocked,'speAvailArray'=>$speAvailArray,'notAvail'=>$notAvail,'_sp'=>$_sp,'_blocked'=>$_blocked);            
            $htmlElementValue = Mage::getModel('airhotels/search')->htmlElementCalenderView($arrayHtmlElement);
            if ($i >= $st && $d <= $totaldays) {
                $d ++;
            }
            $ctr ++;
            if ($ctr > 7) {
                $ctr = 1;
                $htmlElementValue = $htmlElementValue . "</tr>";
            }
        }
        $htmlElementValue = $htmlElementValue . '</table>';
        $htmlElementValue = $htmlElementValue . '<input type="hidden" value="' . $x . '" id="currentMonth" />';
        $htmlElementValue = $htmlElementValue . '<input type="hidden" value="' . $year . '" id="currentYear" />';
        $this->getResponse ()->setBody ( $htmlElementValue );
    }
    /**
     * Function Name: saveInbox
     * Insert new message in customerinbox table and send inbox notification
     *
     * @param array $data            
     * @return boolean
     */
    public function saveinboxAction() {
        /**
         * If not logges in redirect to login page.
         */
        if (! Mage::getSingleton ( 'customer/session' )->isLoggedIn ()) {
            $this->_redirectUrl ( Mage::helper ( 'customer' )->getLoginUrl () );
        } else {
            /**
             * get the parameters
             */
            $hostId = $this->getRequest ()->getParam ( 'hostId' );
            $experienceId = $this->getRequest ()->getParam ( 'experienceId' );
            /**
             * Start on
             */
            $startOn = date ( "Y-m-d", strtotime ( $this->getRequest ()->getParam ( 'from' ) ) );
            $endOn = date ( "Y-m-d", strtotime ( $this->getRequest ()->getParam ( 'to' ) ) );
            $startAt = $this->getRequest ()->getParam ( 'select_timepicker' );
            $startAt = date ( 'H:i:s', strtotime ( $startAt ) );
            $peoples = $this->getRequest ()->getParam ( 'number_of_guests' );
            $mailSubject = $this->getRequest ()->getParam ( 'mailSubject' );
            $hostCall = $this->getRequest ()->getParam ( 'hostCall' );
            $propertyServiceFrom = $this->getRequest ()->getParam ( 'property_service_from' );
            $propertyServiceFromPeriod = $this->getRequest ()->getParam ( 'property_service_from_period' );
            $propertyServiceTo = $this->getRequest ()->getParam ( 'property_service_to' );
            $propertyServiceToPeriod = $this->getRequest ()->getParam ( 'property_service_to_period' );
            /**
             * guest Preferences
             */
            $guestPreferences = $this->getRequest ()->getParam ( 'guest_preferences' );
            /**
             * Mobile No
             */
            $mobileNo = $this->getRequest ()->getParam ( 'mobileNo' );
            $model = Mage::getModel ( 'catalog/product' );
            $_product = $model->load ( $experienceId );
            $duration = $_product->getDuration ();
            $propertyTime = $_product->getBookingTime ();
            $attributeCode = "property_time";
            $attribute_details = Mage::getSingleton ( "eav/config" )->getAttribute ( "catalog_product", $attributeCode );
            $options = $attribute_details->getSource ()->getAllOptions ( false );
            Foreach ( $options as $option ) {
                if ($option ['value'] == $propertyTime && $option ['label'] == "Hourly") {
                    $endAt = date ( 'H:i:s', strtotime ( "$startAt + $duration hours" ) );
                } else {
                    $endAt = $startAt;
                }
            }
            /**
             * Initilize data of array.
             */
            $data = array (
                    "hostId" => $hostId,
                    "experienceId" => $experienceId,
                    "startOn" => $startOn,
                    "endOn" => $endOn,
                    "startAt" => $startAt,
                    "endAt" => $endAt,
                    "peoples" => $peoples,
                    "mailContent" => $mailSubject,
                    "hostCall" => $hostCall,
                    "guest_preference" => $guestPreferences,
                    "mobileNo" => $mobileNo,
                    "property_service_from" => $propertyServiceFrom,
                    "property_service_from_period" => $propertyServiceFromPeriod,
                    "property_service_to" => $propertyServiceTo,
                    "property_service_to_period" => $propertyServiceToPeriod 
            );
            /**
             * Get property host id.
             * If customer id and host id is same display error message.
             */
            $propertyHostId = Mage::getSingleton ( 'customer/session' )->getCustomer ()->getId ();
            if ($propertyHostId == $_product->getUserid ()) {
                Mage::getSingleton ( 'core/session' )->addError ( Mage::helper ( 'airhotels' )->__ ( "You can't contact your own List." ) );
                return $this->_redirectUrl ( Mage::getBaseUrl () . $_product->getUrlPath () );
            }
            /**
             * Set error message.
             */
            $errorMsg = $this->__ ( 'Warning: you may be trying to send contact information.' );
            /**
             * Checks the email
             */
            $this->checkEmail ( $_product, $mailSubject );
            /**
             * Redirect to product page
             */
            if (preg_match ( '/(?:\+?1[-. ]?)?\(?([0-9]{3})\)?[-. ]?([0-9]{3})[-. ]?([0-9]{4})/', $mailSubject )) {
                Mage::getSingleton ( 'core/session' )->addError ( $errorMsg );
                return $this->_redirectUrl ( Mage::getBaseUrl () . $_product->getUrlPath () );
            }
            /**
             * Data are saved inbox
             */
            Mage::helper ( 'airhotels/calendar' )->NotificationMsg ( $data );
            return $this->_redirectUrl ( Mage::getBaseUrl () . $_product->getUrlPath () );
        }
    }
    /**
     * Validating the Email
     *
     * @param object $_product            
     * @param string $mailSubject            
     * @return Ambigous <Mage_Core_Controller_Varien_Action, Apptha_Airhotels_PropertyController>
     */
    public function checkEmail($_product, $mailSubject) {
        /**
         * Add the errorMessage
         */
        $errorMsg = $this->__ ( 'Warning: you may be trying to send contact information.' );
        if (preg_match ( '/\b[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}\b/si', $mailSubject )) {
            Mage::getSingleton ( 'core/session' )->addError ( $errorMsg );
            return $this->_redirectUrl ( Mage::getBaseUrl () . $_product->getUrlPath () );
        }
    }
    /**
     * Mails are saved in Inbox
     */
    public function inboxAction() {
        $type = 'in';
        $text = 'Inbox';
        /**
         * Get message id.
         */
        $messageid = $this->getRequest ()->getParam ( 'messageid' );
        $this->sendAndReceive ( $type, $text, $messageid );
    }
    /**
     * Sent mails are saved in senditem
     */
    public function senditemAction() {
        $type = 'out';
        $text = 'Send Item';
        /**
         * Get message Id.
         */
        $messageid = $this->getRequest ()->getParam ( 'messageid' );
        $this->sendAndReceive ( $type, $text, $messageid );
    }
    /**
     * Email sending and receiving
     *
     * @param string $type            
     * @param string $text            
     */
    public function sendAndReceive($type, $text, $messageid) {
        /**
         * Check customer is logged in or not.
         * If not logged in redirect to login page.
         */
        if (! Mage::getSingleton ( 'customer/session' )->isLoggedIn ()) {
            $this->_redirectUrl ( Mage::helper ( 'customer' )->getLoginUrl () );
        } else {
            /**
             * Get the post param 'messageId' Value.
             */
            if ($messageid) {
                if (Mage::getModel ( 'airhotels/calendar' )->deleteMessage ( $messageid, $type )) {
                    /**
                     * Success Notification Value.
                     */
                    Mage::getSingleton ( 'core/session' )->addSuccess ( $this->__ ( 'Deleted successfully' ) );
                } else {
                    /**
                     * Success Notification Value.
                     */
                    Mage::getSingleton ( 'core/session' )->addSuccess ( $this->__ ( 'Deletion failed. Try again' ) );
                }
            }
            /**
             * load the layout.
             */
            $this->loadLayout ();
            $this->getLayout ()->getBlock ( 'head' )->setTitle ( $this->__ ( $text ) );
            /**
             * Rendering the Layout
             */
            $this->renderLayout ();
        }
    }
    /**
     * Received messages are shown
     */
    public function showmessageAction() {
        /**
         * Check the customer logged in
         */
        if (! Mage::getSingleton ( 'customer/session' )->isLoggedIn ()) {
            $this->_redirectUrl ( Mage::helper ( 'customer' )->getLoginUrl () );
        } else {
            $mess_Id = $this->getRequest ()->getParam ( 'id' );
            Mage::getSingleton ( 'core/session' )->setmId ( $mess_Id );
            $this->loadLayout ();
            $this->renderLayout ();
        }
    }
    /**
     * Function Name: uploadphotoAction
     * Upload photo action for uploading new properties photos
     */
    public function uploadphotoAction() {
        /**
         * Check the customer logged in
         */
        if (! Mage::getSingleton ( 'customer/session' )->isLoggedIn ()) {
            $this->_redirectUrl ( Mage::helper ( 'customer' )->getLoginUrl () );
        } else {
            $getImage = $this->getRequest ()->getParam ( 'crop' );
            $data = $getImage ['image'];
            $deleteImage = $this->getRequest ()->getParam ( 'deleteimage' );
            /**
             * Update profile image.
             */
            if (isset ( $data ) && $deleteImage != 1) {
                Mage::getModel ( 'airhotels/customerphoto' )->updateProfilePicture ( $data );
                Mage::getSingleton ( 'core/session' )->addSuccess ( $this->__ ( 'Your profile image is saved successfully' ) );
                $url = Mage::getBaseUrl () . "property/property/uploadphoto";
                return $this->_redirectUrl ( $url );
            }
            /**
             * Delete profile image.
             */
            if ($deleteImage == 1) {
                Mage::getModel ( 'airhotels/customerphoto' )->updateProfilePicture ();
            }
            /**
             * Load the layout and rendering the layout
             */
            $this->loadLayout ();
            /**
             * Set page title.
             */
            $this->getLayout ()->getBlock ( 'head' )->setTitle ( $this->__ ( 'Edit Profile image' ) );
            $this->renderLayout ();
        }
    }
    /**
     * Function Name: replyAction
     * reply mails
     */
    public function replyAction() {
        if (! Mage::getSingleton ( 'customer/session' )->isLoggedIn ()) {
            $this->_redirectUrl ( Mage::helper ( 'customer' )->getLoginUrl () );
        } else {
            /**
             * Get the message id
             */
            $messageid = $this->getRequest ()->getParam ( 'message_id' );
            $customer = Mage::getSingleton ( 'customer/session' )->getCustomer ();
            $customerid = $customer->getId ();
            /**
             * Get the Message
             */
            $message = $this->getRequest ()->getParam ( 'message' );
            $flag = 0;
            $url = Mage::getBaseUrl () . "property/property/inbox/";
            /**
             * add the error Message
             */
            $errorMsg = $this->__ ( 'Warning: you may be trying to send contact information.' );
            if (preg_match ( '/\b[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}\b/si', $message )) {
                $flag = 1;
                Mage::getSingleton ( 'core/session' )->addError ( $errorMsg );
                Mage::app ()->getFrontController ()->getResponse ()->setRedirect ( $url );
            }
            /**
             * setting the redirect
             */
            if (preg_match ( '/(?:\+?1[-. ]?)?\(?([0-9]{3})\)?[-. ]?([0-9]{3})[-. ]?([0-9]{4})/', $message )) {
                $flag = 1;
                Mage::getSingleton ( 'core/session' )->addError ( $errorMsg );
                Mage::app ()->getFrontController ()->getResponse ()->setRedirect ( $url );
            }
            if ($flag == 0) {                
                if (Mage::getModel ( 'airhotels/calendar' )->replyMail ( $messageid, $customerid, $message )) {
                    Mage::getSingleton ( 'core/session' )->addSuccess ( $this->__ ( 'Mail sent successfully' ) );
                } else {
                    Mage::getSingleton ( 'core/session' )->addError ( $this->__ ( 'Mail sent failed' ) );
                }
            }
        }
        $url = Mage::getBaseUrl () . "property/property/inbox/";
        Mage::app ()->getFrontController ()->getResponse ()->setRedirect ( $url );
    }
    /**
     * Function Name: advsearchAction
     * Advance Search for properties
     */
    public function advsearchAction() {
        $this->loadLayout ();
        $this->renderLayout ();
    }
    /**
     * Function Name: blockcalendarAction
     * Blocking calendar fucntionality start
     */
    public function blockcalendarAction() {
        if (! Mage::getSingleton ( 'customer/session' )->isLoggedIn ()) {
            $this->_redirectUrl ( Mage::helper ( 'customer' )->getLoginUrl () );
        }
        /**
         * Getting customer Id from session
         */
        $customerIdVal = Mage::getSingleton ( 'customer/session' )->getCustomer ()->getId ();
        $entityId = ( int ) $this->getRequest ()->getParam ( 'id' );
        $collectionInfo = Mage::getModel ( 'catalog/product' )->load ( $entityId );
        $CustomerId = $collectionInfo->getUserid ();
        if ($customerIdVal != $CustomerId) {
            Mage::getSingleton ( 'core/session' )->addError ( $this->__ ( "Access denied" ) );
            $this->_redirect ( '*/property/show/' );
            return;
        }
        /**
         * redirect to owner permission
         */
        $this->redirectForOwnerPermission ();
        $this->loadLayout ();
        $this->renderLayout ();
    }
    /**
     * Function Name: blockdateAction
     * using this we can insert the deatils blocked dates
     */
    public function blockdateAction() {
        /**
         * Get the post data such as
         * 'check_in'
         * 'check_out'
         * 'book_avail'
         * 'price_per'
         * 'hourlyDateValue'
         */
        $blockTime = '';
        $checkIn = Mage::app ()->getRequest ()->getPost ( 'check_in' );
        $checkOut = Mage::app ()->getRequest ()->getPost ( 'check_out' );
        $bookAvail = $this->getRequest ()->getPost ( 'book_avail' );
        $productId = $this->getRequest ()->getPost ( 'productid' );
        $pricePer = trim ( $this->getRequest ()->getPost ( 'price_per' ) );
        $hourlyDateValue = trim ( $this->getRequest ()->getPost ( 'hourlyDateValue' ) );
        /**
         * Get the Model for 'catalog/product' and load the id
         */
        $property = Mage::getModel ( 'catalog/product' )->load ( $productId );
        $propertyTime = $property->getPropertyTime ();
        $propertyTimeData = Mage::helper ( 'airhotels/airhotel' )->getPropertyTimeLabelByOptionId ();
        $hourlyEnabledOrNot = Mage::helper ( 'airhotels/product' )->getHourlyEnabledOrNot ();
        if ($propertyTime == $propertyTimeData && ! empty ( $hourlyDateValue ) && $hourlyEnabledOrNot == 0) {
            $blockTime = Mage::getModel ( 'airhotels/product' )->hourlyDateBlockByHost ( $checkIn, $checkOut );
            $startDate = $endDate = date ( "Y-m-d", strtotime ( $hourlyDateValue ) );
        } else {
            /**
             * Save blocked date by reverse order
             */
            $date = Mage::helper ( 'airhotels/general' )->startEndDate ( $checkIn, $checkOut );
            $startDate = $date ['startDate'];
            $endDate = $date ['endDate'];
        }
        if ($bookAvail == 3) {
            $pricePer = '1';
        }
        /**
         * Get the 'airhotels/product' of all dates between Two dates
         */
        $daysData = Mage::getModel ( 'airhotels/product' )->getAllDatesBetweenTwoDates ( $startDate, $endDate );
        $monthwiseArray = Mage::getModel ( 'airhotels/product' )->getMonthwiseArrayData ( $daysData );
        /**
         * Iterating the loop
         */
        foreach ( $monthwiseArray as $key => $monthArr ) {
            $dateValue = array ();
            $fromDate = $toDate = '';
            $fromDate = $monthArr ['fromDate'];
            $toDate = $monthArr ['toDate'];
            $calDate = $key;
            $mY = explode ( "__", $calDate );
            $month = $mY [0];
            $year = $mY [1];
            if ($fromDate <= $toDate) {
                $date1 = strtotime ( $fromDate );
                $date2 = strtotime ( $toDate );
                /**
                 * Get all days between two dates interval
                 */
                while ( $date1 <= $date2 ) {
                    $dateValue [] = date ( "d", $date1 );
                    $date1 = $date1 + 86400;
                }
            }
            $fDate = implode ( ",", $dateValue );
            /**
             * New formate for price
             */
            $dateValueCondition = count ( $dateValue );
            $hourlyEnabledOrNot = Mage::helper ( 'airhotels/product' )->getHourlyEnabledOrNot ();
            if ($propertyTime == $propertyTimeData && ! empty ( $hourlyDateValue ) && $hourlyEnabledOrNot == 0) {
                /**
                 * Update hourly wise blocking by host
                 */
                Mage::helper ( 'airhotels/general' )->deleteDaywiseBookedData ( $productId, $month, $year, $fDate, $blockTime );
                /**
                 * Remove blocked dates for particular data
                 */
                if (count ( $dateValue ) == 1) {
                    Mage::helper ( 'airhotels/general' )->datePriceUpdate ( $productId, $month, $year, $fDate );
                }
            } else {
                for($j = 0; $j < $dateValueCondition; $j ++) {
                    Mage::helper ( 'airhotels/general' )->datePriceUpdate ( $productId, $month, $year, $dateValue [$j] );
                }
            }
            $this->calendarDelete ( $pricePer, $dateValue, $month, $year, $fDate, $blockTime );
        }
        $this->calendarviewAction ();
    }
    /**
     * Function Name: calendarDelete
     * Delete the Calendar
     *
     * @param int $pricePer            
     * @param int $dateValue            
     * @param int $month            
     * @param int $year            
     * @param int $fDate            
     * @param int $blockTime            
     */
    public function calendarDelete($pricePer, $dateValue, $month, $year, $fDate, $blockTime) {
        /**
         * Get the Product Id Value.
         */
        $productId = $this->getRequest ()->getPost ( 'productid' );
        /**
         * load the product Id into Property Value.
         */
        $property = Mage::getModel ( 'catalog/product' )->load ( $productId );
        /**
         * get the Property Time Vlaue.
         */
        $propertyTime = $property->getPropertyTime ();
        /**
         * get the PropertyTimeData by calling the getPropertyTimeLabelByOptionId
         */
        $propertyTimeData = Mage::helper ( 'airhotels/airhotel' )->getPropertyTimeLabelByOptionId ();
        /**
         * HOurly Date Value
         */
        $hourlyDateValue = trim ( $this->getRequest ()->getPost ( 'hourlyDateValue' ) );
        /**
         * Block Calendar Value.
         */
        $blockCalendartable = 'airhotels_calendar';
        /**
         * get the BookAvil Value.
         */
        $bookAvail = $this->getRequest ()->getPost ( 'book_avail' );
        /**
         * Get core Connetion from DB DLL
         */
        $coreResource = Mage::getSingleton ( 'core/resource' );
        /**
         * conetion Resource for Model file.
         */
        $conn = $coreResource->getConnection ( 'core_read' );
        /**
         * Check the '$pricePer' value is not empty.
         */
        if ($pricePer != '') {
            /**
             * Get the hourlyEnabled Vlaue.
             */
            $hourlyEnabledOrNot = Mage::helper ( 'airhotels/product' )->getHourlyEnabledOrNot ();
            /**
             * Make sure the $propertyTime and $propertyTimeData are same.
             * As well as $hourlyEnabledOrNot value has zero.
             */
            if ($propertyTime == $propertyTimeData && empty ( $hourlyDateValue ) && $hourlyEnabledOrNot == 0) {
                foreach ( $dateValue as $dateVal ) {
                    /**
                     * Deleting the row from table.
                     */
                    $conn->delete ( $coreResource->getTableName ( $blockCalendartable ), array (
                            'product_id = ? ' => $productId,
                            'month = ? ' => $month,
                            'year = ? ' => $year,
                            'blockfrom = ?' => $dateVal,
                            'blocktime != ?' => '' 
                    ) );
                }
            }
            /**
             * Delete the row from table.
             */
            $conn->delete ( $coreResource->getTableName ( $blockCalendartable ), array (
                    'product_id = ? ' => $productId,
                    'month = ? ' => $month,
                    'year = ? ' => $year,
                    'blockfrom = ?' => $fDate,
                    'blocktime = ?' => $blockTime 
            ) );
            /**
             * Check bookavail value is one.
             */
            if ($bookAvail == 1) {
                /**
                 * Insert the Values into Table.
                 */
                $conn->insert ( $coreResource->getTableName ( $blockCalendartable ), array (
                        'product_id' => $productId,
                        'book_avail' => $bookAvail,
                        'month' => $month,
                        'year' => $year,
                        'blockfrom' => $fDate,
                        'price' => $pricePer,
                        'created' => now (),
                        'updated' => now (),
                        'blocktime' => $blockTime,
                        'google_calendar_event_uid' => '' 
                ) );
            } else {
                /**
                 * Insert the Values into Table.
                 */
                $conn->insert ( $coreResource->getTableName ( $blockCalendartable ), array (
                        'product_id' => $productId,
                        'book_avail' => $bookAvail,
                        'month' => $month,
                        'year' => $year,
                        'blockfrom' => $fDate,
                        'price' => $pricePer,
                        'created' => now (),
                        'updated' => now (),
                        'blocktime' => $blockTime,
                        'google_calendar_event_uid' => 'ownsite' 
                ) );
            }
        }
        /**
         * Check weather the productId is set.
         */
        if (isset ( $productId ) && isset ( $month ) && isset ( $year )) {
            $blockfromValue = '';
            $conn->delete ( $coreResource->getTableName ( $blockCalendartable ), array (
                    'product_id = ? ' => $productId,
                    'month = ? ' => $month,
                    'year = ? ' => $year,
                    'blockfrom = ? ' => $blockfromValue 
            ) );
        }
    }
    /**
     * Function Name: redirectForOwnerPermission
     * Getting the redirect Permission
     */
    public function redirectForOwnerPermission() {
        /**
         * Get customerId.
         */
        $customerId = Mage::getSingleton ( 'customer/session' )->getCustomer ()->getId ();
        $entityId = ( int ) Mage::app ()->getRequest ()->getParam ( 'id' );
        $collection = Mage::getModel ( 'catalog/product' )->load ( $entityId );
        $userId = $collection->getUserid ();
        if ($customerId != $userId) {
            Mage::getSingleton ( 'core/session' )->addError ( $this->__ ( "Access denied" ) );
            $this->_redirect ( '*/general/show/' );
            return;
        }
    }
    /**
     * Redirect to the customer login page when you click on the List the new space
     */
    public function formAction() {
        Mage::getSingleton ( 'customer/session' )->setBeforeAuthUrl ( Mage::helper ( 'airhotels' )->getformUrl () );
        /**
         * Get customer Id.
         */
        $customerId = Mage::getSingleton ( 'customer/session' )->getCustomer ()->getId ();        
        if ($customerId) {
            $stepAction = $this->getRequest ()->getParam ( 'step' );
            if ($stepAction == "publish") {
                $customerId = Mage::getSingleton ( 'customer/session' )->getId ();
                $customerData = Mage::getModel ( 'airhotels/product' )->getCustomerPictureById ( $customerId );
                $smsEnabledOrNot = Mage::helper ( 'airhotels/smsconfig' )->getSmsEnabledOrNot ();
                if ($smsEnabledOrNot == 0) {
                    $phoneNumberVerify = $customerData [0] ['mobile_verified_profile'];
                    if ($phoneNumberVerify != "verified") {
                        Mage::getSingleton ( 'core/session' )->addError ( $this->__ ( 'You need to verify your phone number.' ) );
                        return $this->_redirect ( '*/property/form/step/profile' );
                    }
                }
            }
            if (Mage::getSingleton ( 'customer/session' )->getCurrentExperienceId () && ! $stepAction) {
                Mage::getSingleton ( 'customer/session' )->unsCurrentExperienceId ();
            }
            $this->loadLayout ();
            $this->_initLayoutMessages ( 'catalog/session' );
            $this->getLayout ()->getBlock ( 'head' )->setTitle ( $this->__ ( 'List an Experience' ) );
            $this->renderLayout ();
        } else {
            Mage::getSingleton ( 'core/session' )->addError ( $this->__ ( 'You are not currently logged in.' ) );
            $this->_redirectUrl ( Mage::helper ( 'customer' )->getLoginUrl () );
        }
    }
    /**
     * Function for subscription action
     */
    public function subscriptionAction() {
        /**
         * Get subscription.
         */
        return Mage::getModel ( 'airhotels/subscriptiontype' )->subscription ();
    }
}