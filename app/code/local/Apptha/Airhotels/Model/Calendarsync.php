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
 * This class contains calendar import, export and synchronization functionality
 *
 * Import ics url calendar data to Airhotels host calendar
 * Create ics file to external calendar import functionality
 * Synchronize with external calendar with Airhotels host calendar
 */
class Apptha_Airhotels_Model_Calendarsync {
    /**
     * Function Name: readIcsUrl
     * Get ics file data
     *
     * @param string $url            
     * @return string $icalString
     */
    function readIcsUrl($url) {
        $icalString = '';
        /**
         * create the object for the Curl
         */
        $curl = new Varien_Http_Adapter_Curl ();
        /**
         * set the config tome out Vlaue.
         */
        $config = array (
                'timeout' => 30 
        );
        /**
         * Set ConfigVlaue.
         */
        $curl->setConfig ( $config );
        /**
         * Curl Write with 'Zend_Http_Client'
         */
        $curl->write ( Zend_Http_Client::GET, $url, '1.0' );
        /**
         * read the curl Vlaue.
         */
        $icalString = $curl->read ();
        /**
         * Curl Close.
         */
        $curl->close ();
        return $icalString;
    }
    /**
     * Function Name: importFromGoogleIcsUrl
     * Update block dates from Google Calendar ICS Url
     *
     * @param string $icalString            
     * @param array $icsDates            
     * @return boolean
     */
    public function importFromGoogleIcsUrl($icalString, $icsDates, $productId) {
        try {
            /**
             * craete the databse the connetion.
             */
            $resource = Mage::getSingleton ( 'core/resource' );
            /**
             * namely the table anme.
             */
            $tableName = $resource->getTableName ( 'airhotels_calendar' );
            /**
             * Made the connetion Value.
             */
            $connection = Mage::getSingleton ( 'core/resource' )->getConnection ( 'core_read' );
            /**
             * Condition set to variables.
             */
            $condition [] = $connection->quoteInto ( 'google_calendar_event_uid != ?', '' );
            $condition [] = $connection->quoteInto ( 'google_calendar_event_uid != ?', 'ownsite' );
            $condition [] = $connection->quoteInto ( 'product_id = ?', $productId );
            /**
             * Delete the table feild values.
             */
            $connection->delete ( $tableName, $condition );
        } catch ( Exception $e ) {
            Mage::getSingleton ( 'core/session' )->addError ( $e->getMessage () );
        }
        /**
         * Check the value is Set
         */
        if (! empty ( $icsDates ) && ! empty ( $icalString )) {
            $uidArray = array ();
            /**
             * Itearting the $icsDates vlaue.
             */
            foreach ( $icsDates as $icsEvent ) {
                /**
                 * get Model Vlaue for '$icsEventBegin'
                 */
                $icsEventBegin = Mage::getModel ( 'airhotels/customerphoto' )->checkICSEvent ( $icsEvent );
                /**
                 * Check the '$icsEventBegin' value
                 */
                if ($icsEventBegin == 'VEVENT') {
                    /**
                     * Change dates based on Date/Time option
                     */
                    $eventStartsDate = $eventEndsDate = array ();
                    /**
                     * Evebnt Start Date
                     */
                    $eventStartsDate = Mage::getModel ( 'airhotels/customerreply' )->eventStartsDate ( $icsEvent );
                    /**
                     * Evebnt Start Date
                     */
                    $eventEndsDate = Mage::getModel ( 'airhotels/customerreply' )->eventEndsDate ( $icsEvent );
                    /**
                     * Set the vlaue to $eventStartsYear
                     */
                    $eventStartsYear = substr ( $eventStartsDate [0], 0, - 4 );
                    /**
                     * set the value to $eventStartsMonth
                     */
                    $eventStartsMonth = substr ( $eventStartsDate [0], 4, - 2 );
                    /**
                     * set the vlaue to $eventStartsDay
                     */
                    $eventStartsDay = substr ( $eventStartsDate [0], 6 );
                    /**
                     * set the vlaue to $startsDate
                     */
                    $startsDate = "$eventStartsYear-$eventStartsMonth-$eventStartsDay";
                    /**
                     * $eventStartDate vlaue.
                     */
                    $eventStartDate = date ( "Y-m-d", strtotime ( $startsDate ) );
                    /**
                     * set the value to $eventEndsYear
                     */
                    $eventEndsYear = substr ( $eventEndsDate [0], 0, - 4 );
                    /**
                     * Set the Value to $eventEndsMonth
                     */
                    $eventEndsMonth = substr ( $eventEndsDate [0], 4, - 2 );
                    /**
                     * set the vlaue to '$eventEndsDay'
                     */
                    $eventEndsDay = substr ( $eventEndsDate [0], 6 );
                    /**
                     * End Date Value.
                     */
                    $endDate = "$eventEndsYear-$eventEndsMonth-$eventEndsDay";
                    /**
                     * Uid Vlaue.
                     */
                    $uidArray = $this->getArray ( $icsEvent );
                    /**
                     * Change end date based on Date/Time option
                     */
                    $eventEndDate = Mage::getModel ( 'airhotels/customerphoto' )->checkICSEventVal ( $icsEvent, $endDate );
                    /**
                     * calculate the Days
                     */
                    $days = Mage::getModel ( 'airhotels/product' )->getAllDatesBetweenTwoDates ( $eventStartDate, $eventEndDate );
                    $this->getDate ( $days, $productId );
                }
            }
            /**
             * Set success message.
             */
            Mage::getSingleton ( 'core/session' )->addSuccess ( "Calendar import complete" );
            return true;
        } else {
            /**
             * Set No data message.
             */
            Mage::getSingleton ( 'core/session' )->addError ( "No data to proceed" );
            return false;
        }
    }
    /**
     * Function Name: convertIcsStringToArray
     * Convert ICS string to array
     *
     * @param string $icalString            
     * @return array $icsDates
     */
    public function convertIcsStringToArray($icalString) {
        $icsDates = array ();
        $icsData = explode ( "BEGIN:", $icalString );
        foreach ( $icsData as $key => $value ) {
            $icsDatesMeta [$key] = explode ( "\n", $value );
        }
        /**
         * Iteration of $icsDatesMeta.
         */
        foreach ( $icsDatesMeta as $key => $value ) {
            foreach ( $value as $subKey => $subValue ) {
                if ($subValue != "") {
                    if ($key != 0 && $subKey == 0) {
                        $icsDates [$key] ["BEGIN"] = $subValue;
                    } else {
                        /**
                         * Explode the array $sub value.
                         */
                        $subValueArr = explode ( ":", $subValue, 2 );
                        $icsDates [$key] [$subValueArr [0]] = $this->icsDates ( $subValueArr [1] );
                    }
                }
            }
        }
        /**
         * Return as array.
         */
        return $icsDates;
    }
    /**
     * Function Name: bookDate
     * Block calendar date in airhotels_calendar table
     *
     * @param date $fromDate            
     * @param date $toDate            
     * @param string $date            
     * @return boolean
     */
    public function bookDate($fromDate, $toDate, $date, $productId, $eventUid) {
        /**
         * Event Uid array
         */
        $eventUidArray = array (
                $eventUid,
                'ownsite' 
        );
        /**
         * book Available Array Value.
         */
        $bookAvail = 2;
        $pricePer = '';
        $dateExplode = explode ( "__", $date );
        $month = $dateExplode [0];
        $year = $dateExplode [1];
        if ($fromDate <= $toDate) {
            $fromDateStr = strtotime ( $fromDate );
            $toDateStr = strtotime ( $toDate );
            /**
             * Get all days between two dates interval
             */
            while ( $fromDateStr <= $toDateStr ) {
                $dateValue [] = date ( "d", $fromDateStr );
                $fromDateStr = $fromDateStr + 86400;
            }
        }
        $fDate = implode ( ",", $dateValue );
        /**
         * Create the colletion for 'airhotels/calendar'
         * 'product_id'
         * 'month'
         * 'year'
         * 'blockfrom'
         * 'google_calendar_event_uid'
         */
        $collections = Mage::getModel ( 'airhotels/calendar' )->getCollection ()->/**
         * Filter by Product Id
         */
        addFieldToFilter ( 'product_id', $productId )->/**
         * Filter by month
         */
        addFieldToFilter ( 'month', $month )->/**
         * Filter by year
         */
        addFieldToFilter ( 'year', $year )->/**
         * Filter by date
         */
        addFieldToFilter ( 'blockfrom', $fDate )->addFieldToFilter ( 'google_calendar_event_uid', array (
                'in' => $eventUidArray 
        ) );
        $uid = '';
        if (count ( $collections ) <= 0 && $uid != 'ownsite') {
            /**
             * Get Model Colletion of 'airhotels/calendar'
             * 'product_id'
             * 'month'
             * 'year'
             * 'google_calendar_event_uid'
             */
            $collections = Mage::getModel ( 'airhotels/calendar' )->getCollection ()->addFieldToFilter ( 'product_id', $productId )->addFieldToFilter ( 'month', $month )->addFieldToFilter ( 'year', $year )->addFieldToFilter ( 'google_calendar_event_uid', array (
                    'in' => 'ownsite' 
            ) );
            $blockedDateString = '';
            $inc = 0;
            /**
             * Itearing the loop
             */
            foreach ( $collections as $collection ) {
                $blockedDateString = (($inc == 0) ? $collection->getBlockfrom () : ',' . $collection->getBlockfrom ());
            }
            $blockingDaysString = '';
            /**
             * Check blocking date string.
             */
            if (! empty ( $blockedDateString )) {
                $blockedDays = explode ( ",", $blockedDateString );
                $blockingDays = explode ( ",", $fDate );
                $blockingDaysArray = array_diff ( $blockingDays, $blockedDays );
                $blockingDaysString = implode ( ",", $blockingDaysArray );
            } else {
                $blockingDaysString = $fDate;
            }
            /**
             * core Resource for databse CVonnetion.
             */
            $coreResource = Mage::getSingleton ( 'core/resource' );
            $connection = $coreResource->getConnection ( 'core_read' );
            /**
             * tableName
             */
            $blockCalendartable = 'airhotels_calendar';
            /**
             * Inserting data
             */
            $connection->insert ( $coreResource->getTableName ( $blockCalendartable ), array (
                    'product_id' => $productId,
                    'book_avail' => $bookAvail,
                    'month' => $month,
                    'year' => $year,
                    'blockfrom' => $blockingDaysString,
                    'price' => $pricePer,
                    'created' => now (),
                    'updated' => now (),
                    'google_calendar_event_uid' => $eventUid,
                    'blocktime' => '' 
            ) );
            Mage::getModel ( 'airhotels/customerreply' )->deleteTableName ( $productId, $month, $year );
        }
        return true;
    }
    /**
     * Function Name: getCollectionInfo
     *
     * get Collection Information
     *
     * @param array $collection            
     * @param date $fromdate            
     * @param date $todate            
     * @param date $dateFrom            
     * @param date $dateTo            
     * @param array $blocked            
     * @param array $not_avail            
     * @return boolean
     */
    public function getCollectionInfo($collection, $fromdate, $todate, $dateFrom, $dateTo, $blocked, $not_avail) {
        /**
         * Initialise the vlaue to '$availabilityFrom' and '$availabilityTo'
         */
        $availabilityFrom = false;
        $availabilityTo = false;
        /**
         * Count the colletion Vlaue.
         */
        if (count ( $collection )) {
            $collectionCount = count ( $collection );
            for($i = 1; $i <= $collectionCount; $i += 2) {
                $myDates = date ( 'Y-n-d', strtotime ( $collection [$i] ) );
                $myMonth = explode ( "-", $myDates );
                $availabilityFrom = $this->availabilityFrom ( $collection, $i, $fromdate );
                $availabilityTo = $this->availabilityTo ( $collection, $i, $todate );
                if (($myMonth [1] == $dateFrom [1]) && ($myMonth [0] == $dateTo [0]) && array_search ( $myMonth [2], $blocked ) || array_search ( $myMonth [2], $not_avail )) {
                    $availabilityFrom = false;
                    $availabilityTo = false;
                }
                /**
                 * ge tthe colletion of 'airhotels/customerreply'
                 * 'availability_from'
                 * 'availability_to'
                 */
                if ((! $availabilityFrom) || (! $availabilityTo)) {
                    return false;
                }
            }
        } else {
            $availabilityFrom = true;
            $availabilityTo = true;
        }
        /**
         * Return availability.
         */
        return $availabilityFrom;
    }
    /**
     * Function name: getArray
     * Array For get the UID
     *
     * @param array $icsEvent            
     * @return multitype
     */
    public function getArray($icsEvent) {
        $uidArray = array ();
        /**
         * Check weather the value has been set
         */
        if (isset ( $icsEvent ['UID'] )) {
            $eventUid = $icsEvent ['UID'];
            $uidArray [] = $eventUid;
        }
        /**
         * Return the $uidArray value.
         */
        return $uidArray;
    }
    /**
     * Function Name : getDate
     * Get all Available Dates
     *
     * @param int $days            
     */
    public function getDate($days, $productId) {
        $monthwiseArray = Mage::getModel ( 'airhotels/product' )->getMonthwiseArrayData ( $days );
        /**
         * Itearting the colletion value.
         */
        foreach ( $monthwiseArray as $key => $monthArr ) {
            $fromDate = $monthArr ['fromDate'];
            $toDate = $monthArr ['toDate'];
            $date = $key;
            /**
             * Call the bookDate method with parameters,
             * '$fromDate'
             * '$toDate'
             * '$date'
             * '$productId'
             * '$eventUid'
             */
            $this->bookDate ( $fromDate, $toDate, $date, $productId, $eventUid );
        }
    }
    /**
     * Function Name: getICSDates
     * Get the ICS Date
     *
     * @param int $key            
     * @param int $subKey            
     * @param string $subValue            
     */
    public function getICSDates($key, $subKey, $subValue) {
        $icsDates = array ();
        if ($key != 0 && $subKey == 0) {
            $icsDates [$key] ["BEGIN"] = $subValue;
        } else {
            /**
             * Explode the array $subValues.
             */
            $subValueArr = explode ( ":", $subValue, 2 );
            if (isset ( $subValueArr [1] )) {
                $icsDates [$key] [$subValueArr [0]] = $subValueArr [1];
            }
        }
        /**
         * Return an array.
         */
        return $icsDates;
    }
    /**
     * Function Name: updateCustInbox
     * Update the airhotels_customer_inbox Table
     *
     * @param int $messageid            
     * @param int $customerId            
     * @param string $table_name            
     * @param string $fieldReceiver            
     * @return number
     */
    public function updateCustInbox($messageid, $customerId, $table_name, $fieldReceiver) {
        $connection = Mage::getSingleton ( 'core/resource' )->getConnection ( 'core_write' );
        try {
            /**
             * Begin the tRanscation Value.
             */
            $connection->beginTransaction ();
            /**
             * Init the $fields,$where value.
             */
            $fields = array ();
            $where = array ();
            $fields [$fieldReceiver] = 1;
            /**
             * Append the where clasue values.
             */
            $where [] = $connection->quoteInto ( 'message_id = ?', $messageid );
            if ($fieldReceiver == 'sender_read') {
                $where [] = $connection->quoteInto ( 'sender_id = ?', $customerId );
            } else {
                $where [] = $connection->quoteInto ( 'receiver_id = ?', $customerId );
            }
            /**
             * Update the table Vlaue.
             */
            $connection->update ( $table_name, $fields, $where );
            /**
             * Commiting the COnnetion
             */
            $connection->commit ();
            $selectResult = 1;
        } catch ( Exception $ex ) {
            /**
             * Add the error Notification
             */
            Mage::getSingleton ( 'core/session' )->addError ( $ex->getMessage () );
            $selectResult = 0;
        }
        return $selectResult;
    }
    /**
     * Function Name: getToDateValue
     * Get to Date
     *
     * @param date $to            
     * @return string
     */
    public function getToDateValue($to) {
        if (Mage::helper ( 'airhotels/product' )->getHourlyEnabledOrNot () != 0) {
            /**
             * convert the $to Date Vlaue.
             */
            $to = strtotime ( $to );
            $to = strtotime ( '-1 day', $to );
            $to = date ( 'm/d/Y', $to );
        }
        /**
         * Return the To date Vlaue.
         */
        return $to;
    }
    /**
     * Function Name: getAccDefaultVal
     * Get the default Account value
     *
     * @return number
     */
    public function getAccDefaultVal() {
        /**
         * Create the eav attribute for 'airhotels/airhotel'
         */
        $accommodatesAttr = Mage::getModel ( 'eav/config' )->getAttribute ( 'catalog_product', Mage::helper ( 'airhotels/airhotel' )->getaccomodatesType () );
        $accommodateDefault = ( int ) $accommodatesAttr->getDefaultValue ();
        if ($accommodateDefault <= 0) {
            $accommodateDefault = 16;
        }
        /**
         * Return the $accDefault value.
         */
        return $accommodateDefault;
    }
    /**
     * Function Name: getProductTypes
     * Getting product types
     *
     * Return enabled product types
     *
     * @return int
     */
    public function getProductTypes() {
        /**
         * Return the arrya Vlaue.
         */
        return array (
                "property" => "Property Registration" 
        );
    }
    /**
     * Function Name: getPropertyDailyLabelByOptionId
     * Retrieve attribute id by property time daily
     *
     * @return integer
     */
    public function getPropertyDailyLabelByOptionId() {
        $type = 'Daily';
        /**
         * Return the Vlaue.
         */
        return $this->propertyTimeValueforDaily ( $type );
    }
    /**
     * Function Name: TripDetails
     * Trip Detilas
     */
    public function TripDetails() {
        /**
         * Get the Customer Detils
         */
        $customer = Mage::getSingleton ( 'customer/session' )->getCustomer ();
        /**
         * Get the Customer ID
         */
        $customerId = $customer->getId ();
        $todayData = Mage::getModel ( 'core/date' )->timestamp ( time () );
        $todayDate = date ( 'Y-m-d', $todayData );
        /**
         * Get Colletion for the 'airhotels/airhotels/airhotels' with some filter
         * 'order_status'
         * 'fromDate'
         * 'Todate'
         * CustomerID
         */     
        return Mage::getModel ( 'airhotels/airhotels' )->getCollection ()->addFieldToFilter ( 'order_status', 1 )->addFieldToFilter ( 'todate', array (
                'lteq' => $todayDate 
        ) )->addFieldToFilter ( 'customer_id', $customerId )->setOrder ( 'id', 'DESC' );
    }
    /**
     * Function name: returnVal
     * Return Value
     *
     * @return number
     */
    public function returnVal() {
        /**
         * get the message inbox value form model
         */
        $msgInbox = Mage::getModel ( 'airhotels/customerreply' );
        try {
            /**
             * Save the message inbox vlaue.
             */
            $msgInbox->save ();
            $selectResult = 1;
        } catch ( Exception $ex ) {
            /**
             * Add error Notification
             */
            Mage::getSingleton ( 'core/session' )->addError ( $ex->getMessage () );
            $selectResult = 0;
        }
        /**
         * Returning the SelectResult
         */
        return $selectResult;
    }
    /**
     * Function name: getMailData
     * Return Value
     *
     * @return number
     */
    public function getMailData($messageid, $customerid) {
        /**
         * Initilizing inbox message data
         */
        $customerData = Mage::getModel ( 'customer/customer' )->load ( $customerid );
        $cusomerName = $customerData->getName ();
        $inboxUrl = Mage::getUrl ( 'property/property/inbox' );
        /**
         * Initialise the FlagVlaue
         */
        $flagValue = array ();
        /**
         * flag Value array
         */
        $flagValue = Mage::getModel ( 'airhotels/customerinbox' )->getCollection ()->addFieldToFilter ( 'sender_id', $customerid )->addFieldToFilter ( 'message_id', $messageid );
        /**
         * Count the Value '$flagValue'
         */
        if (count ( $flagValue )) {
            $receiverId = '';
            /**
             * Get customerinbox table collection.
             *
             * Filter by message id.
             */
            $selectCollections = Mage::getModel ( 'airhotels/customerinbox' )->getCollection ()->addFieldToFilter ( 'message_id', $messageid );
            
            foreach ( $selectCollections as $selectCollection ) {
                $receiverId = $selectCollection->getReceiverId ();
                break;
            }
        } else {
            /**
             * Receicver ID Vlaue.
             */
            $receiverId = '';
            $selectCollections = Mage::getModel ( 'airhotels/customerinbox' )->getCollection ()->addFieldToFilter ( 'message_id', $messageid );
            /**
             * Iterating the $selectCollections Value.
             */
            foreach ( $selectCollections as $selectCollection ) {
                $receiverId = $selectCollection->getSenderId ();
                break;
            }
        }
        $host = Mage::getModel ( 'customer/customer' )->load ( $receiverId );
        /**
         * Get the HOstName.
         */
        $hostName = $host->getName ();
        /**
         * Get the Host Email
         */
        $hostEmail = $host->getEmail ();
        /**
         * Get the DomainName.
         */
        $domainName = Mage::app ()->getFrontController ()->getRequest ()->getHttpHost ();
        /**
         * Getting store name
         */
        $storeName = Mage::app ()->getStore ()->getGroup ()->getName ();
        /**
         * Templae Id Vlaue.
         */
        $templateId = ( int ) Mage::getStoreConfig ( 'airhotels/inbox_notification/newinbox_template' );
        /**
         * Check the templae Value.
         */
        if ($templateId) {
            /**
             * load the $templateId into 'core/email_template'
             */
            $emailTemplate = Mage::getModel ( 'core/email_template' )->load ( $templateId );
        } else {
            /**
             * load the template_id into ''core/email_template''
             */
            $emailTemplate = Mage::getModel ( 'core/email_template' )->loadDefault ( 'airhotels_inbox_notification_newinbox_template' );
        }
        /**
         * load the customer Name.
         */
        $emailTemplate->setSenderName ( $cusomerName );
        /**
         * load the CustomerEmail
         */
        $emailTemplate->setSenderEmail ( 'noreply@' . Mage::app ()->getRequest ()->getServer ( 'HTTP_HOST' ) );
        $emailTemplateVariables = (array (
                'hostname' => $hostName,
                'domainname' => $domainName,
                'cusomername' => $cusomerName,
                'storename' => $storeName,
                'inboxurl' => $inboxUrl 
        ));
        $emailTemplate->setDesignConfig ( array (
                'area' => 'frontend' 
        ) );
        $emailTemplate->getProcessedTemplate ( $emailTemplateVariables );
        /**
         * Send email.
         */
        $emailTemplate->send ( $hostEmail, $hostName, $emailTemplateVariables );
    }
    /**
     * Function name: updateSenderRead
     * update the Sender
     *
     * @param int $messageid            
     * @param int $customerid            
     */
    public function updateSenderRead($messageid, $customerid, $table_name) {
        $connection = Mage::getSingleton ( 'core/resource' )->getConnection ( 'core_write' );
        try {
            /**
             * Begin Transaction Vlaue.
             */
            $connection->beginTransaction ();
            /**
             * Initalise the '$fields' array
             */
            $fields = array ();
            /**
             * set the fields value to
             * 'is_reply'
             * 'sender_read'
             * 'is_sender_delete'
             * 'is_receiver_delete'
             */
            $fields ['is_reply'] = 1;
            $fields ['sender_read'] = 0;
            $fields ['is_sender_delete'] = 0;
            $fields ['is_receiver_delete'] = 0;
            /**
             * and delete the param id value
             * 'message_id'
             * 'receiver_id'
             */
            $where [] = $connection->quoteInto ( 'message_id = ?', $messageid );
            $where [] = $connection->quoteInto ( 'receiver_id = ?', $customerid );
            /**
             * Update the table
             */
            $connection->update ( $table_name, $fields, $where );
            /**
             * Commit the table value.
             */
            $connection->commit ();
            $selectResult = 1;
        } catch ( Exception $ex ) {
            /**
             * Set error message.
             */
            Mage::getSingleton ( 'core/session' )->addError ( $ex->getMessage () );
            $selectResult = 0;
        }
    }
    /**
     *
     * @param Arrray $collection            
     * @param int $i            
     * @param date $fromdate            
     * @return boolean
     */
    public function availabilityFrom($collection, $i, $fromdate) {
        /**
         * Check date range.
         */
        return Mage::getModel ( 'airhotels/product' )->check_in_range ( $collection [$i], $collection [$i + 1], $fromdate );
    }
    /**
     *
     * @param Arrray $collection            
     * @param int $i            
     * @param date $todate            
     * @return boolean
     */
    public function availabilityTo($collection, $i, $todate) {
        $availabilityTo = false;
        if (Mage::getModel ( 'airhotels/product' )->check_in_range ( $collection [$i], $collection [$i + 1], $todate )) {
            $availabilityTo = true;
        }
        /**
         * Return availablility.
         */
        return $availabilityTo;
    }
    /**
     * Function Ics date conversion
     * 
     * @param unknown $subValueArr            
     */
    public function icsDates($subValueArr) {
        $icsDates = '';
        if (isset ( $subValueArr )) {
            $icsDates = $subValueArr;
        }
        return $icsDates;
    }
}