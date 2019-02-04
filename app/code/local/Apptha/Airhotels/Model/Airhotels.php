<?php
/**
 * Apptha
 * NOTICE OF LICENSE
 *
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
class Apptha_Airhotels_Model_Airhotels extends Mage_Core_Model_Abstract {
    /**
     * FUnction Name: Construct
     * Calling the Construct Method
     */
    public function _construct() {
        /**
         * Calling the parent Construct Method.
         */
        parent::_construct ();
        /**
         * Initializing airhotels Block.
         */
        $this->_init ( 'airhotels/airhotels' );
    }
    /**
     * Function Name: arrayCountValue
     * To verify the property dates are available and
     * show the subtotal excluding processing fee .
     */
    public function arrayCountValue($avdays) {
        /**
         * Return the '$avdays' Value.
         */
        return count ( $avdays );
    }
    /**
     * Function Name: CheckAvail
     * Checkavail function is used to check the available dates for booking.
     */
    public function checkavail() {
        $from = Mage::app ()->getRequest ()->getParam ( 'from' );
        $to = Mage::app ()->getRequest ()->getParam ( 'to' );
        $productid = Mage::app ()->getRequest ()->getParam ( 'productid' );
        $price = Mage::app ()->getRequest ()->getParam ( 'price' );
        $days_count = Mage::app ()->getRequest ()->getParam ( 'days' );
        $subscriptionTotal = Mage::app ()->getRequest ()->getParam ( 'subsprice' );
        $subCycle = Mage::app ()->getRequest ()->getParam ( 'subcycle' );
        $dateCountFromDate = date ( 'm/d/Y', strtotime ( $from . ' + ' . $days_count . ' days' ) );
        $to = Mage::getModel ( 'airhotels/calendarsync' )->getToDateValue ( $to );
        $calendarDate = $this->dateVerfiy ( $productid, $from, $to );
        $avPrice = Mage::getModel ( 'airhotels/product' )->getSpecialPrice ( $calendarDate );
        $propertyTime = Mage::getModel ( 'catalog/product' )->load ( $productid )->getPropertyTime ();
        $propertyTimeData = Mage::helper ( 'airhotels/airhotel' )->getPropertyTimeLabelByOptionId ();
        $days = $av = array ();
        $to = Mage::getModel ( 'airhotels/search' )->setToCore ( $subCycle, $to, $dateCountFromDate );
        $hourlyEnabledOrNot = Mage::helper ( 'airhotels/product' )->getHourlyEnabledOrNot ();
        if ($propertyTime != $propertyTimeData || $hourlyEnabledOrNot == 1) {
            $tPrefix = ( string ) Mage::getConfig ()->getTablePrefix ();
            $orderItemTable = $tPrefix . 'sales_flat_order';
            $dealstatus [0] = "processing";
            $dealstatus [1] = "complete";
            $range = Mage::getModel ( 'airhotels/search' )->dateRangeArray ( $orderItemTable, $productid, $dealstatus );
        }
        $count = count ( $range );
        $day = 86400;
        $Incr = 0;
        $startStrTime = strtotime ( $from );
        $endStrTime = strtotime ( $to );
        $numDayRound = round ( ($endStrTime - $startStrTime) / $day ) + 1;
        if ($propertyTime == $propertyTimeData && $hourlyEnabledOrNot == 0) {
            $propertyServiceFrom = Mage::app ()->getRequest ()->getParam ( 'property_service_from' );
            $propertyServiceFromPeriod = Mage::app ()->getRequest ()->getParam ( 'property_service_from_period' );
            $propertyServiceTo = Mage::app ()->getRequest ()->getParam ( 'property_service_to' );
            $propertyServiceToPeriod = Mage::app ()->getRequest ()->getParam ( 'property_service_to_period' );
            $checkingFromDate = date ( 'Y-m-d', $startStrTime );
            $todayDateValue = Mage::getModel ( 'core/date' )->date ( 'Y-m-d' );
            $msgData = Mage::getModel ( 'airhotels/status' )->getDatesAvailable ( $checkingFromDate, $todayDateValue, $currentHours, $propertyServiceFromPeriod, $propertyServiceFrom );
            if($msgData){
                Mage::app ()->getResponse ()->setBody ( $msgData );
                return true;
            }
            $availHourlyFlag = ( int ) Mage::getModel ( 'airhotels/airhotels' )->checkHourlyAvailableProduct ( $productid, $from, $to, $propertyServiceFrom, $propertyServiceFromPeriod, $propertyServiceTo, $propertyServiceToPeriod );
            $Incr = ! $availHourlyFlag;
            $Incr = ( int ) $Incr;
        } else {
            for($i = 0; $i <= $count - 1; $i ++) {
                $fromdateValue = $range [$i] ['fromdate'];
                $todateValue = $range [$i] ['todate'];
                $startValue = $fromdateValue;
                $endValue = $todateValue;
                $startTime = strtotime ( $startValue );
                $endTime = strtotime ( $endValue );
                $numDays = round ( ($endTime - $startTime) / $day ) + 1;
                for($d = 0; $d < $numDays; $d ++) {
                    $days [] = date ( 'm/d/Y', ($startTime + ($d * $day)) );
                }
            }
        }        
        $Incr = Mage::getModel ( 'airhotels/customerinbox' )->dayWiseBooked ( $Incr, $numDayRound, $startStrTime, $days, $day, $productid );        
        $total = 0;
        $pFrom = strtotime ( $from );
        $pTo = strtotime ( $to );
        $pDay = round ( ($pTo - $pFrom) / $day ) + 1;
        if ($Incr == 0) {
            $propertyMaximum = Mage::helper ( 'airhotels/airhotel' )->getPropertyMaximumByProductId ( $productid );
            $propertyMinimum = Mage::helper ( 'airhotels/airhotel' )->getPropertyMinimumByProductId ( $productid );
            $overallTotalHours = 0;
            $totalOverNightFee = 0;
            if ($propertyTime == $propertyTimeData && $hourlyEnabledOrNot == 0) {
                $propertyServiceFromRail = $this->getRailwayTimeFormat ( $propertyServiceFromPeriod, $propertyServiceFrom );
                $propertyServiceToRail = $this->getRailwayTimeFormat ( $propertyServiceToPeriod, $propertyServiceTo );
                $propertyServiceFromRail = $this->getRailwayTimeFormat ( $propertyServiceFromPeriod, $propertyServiceFrom );
                $propertyOverNightFeeVal = Mage::helper ( 'airhotels/airhotel' )->getPropertyOverNightFeeByProductId ( $productid );
                $propertyServiceFromTimeData = Mage::helper ( 'airhotels/airhotel' )->getPropertyServiceFromTimeByProductId ( $productid );
                $propertyServiceToTimeData = Mage::helper ( 'airhotels/airhotel' )->getPropertyServiceToTimeByProductId ( $productid );
                $propertyServiceFromArrayVal = explode ( ":", $propertyServiceFromTimeData );
                $propertyServiceFromData = $propertyServiceFromArrayVal [0];
                $propertyServiceFromPeriodData = $propertyServiceFromArrayVal [1];
                $propertyServiceToArray = explode ( ":", $propertyServiceToTimeData );
                $propertyServiceToData = $propertyServiceToArray [0];
                $propertyServiceToPeriodData = $propertyServiceToArray [1];
                $propertyServiceFromDataRailVal = $this->getRailwayTimeFormat ( $propertyServiceFromPeriodData, $propertyServiceFromData );
                $propertyServiceToDataRailVal = $this->getRailwayTimeFormat ( $propertyServiceToPeriodData, $propertyServiceToData );
                $this->getRailwayTimeFormat ( $propertyServiceFromPeriodData, $propertyServiceFromData );
                $this->getRailwayTimeFormat ( $propertyServiceToPeriodData, $propertyServiceToData );                
                $checkinDetails = array (
                        'pId' => $productid,
                        'checkin' => $propertyServiceFromRail,
                        'checkout' => $propertyServiceToRail,
                        'checkinformat' => $propertyServiceFromDataRailVal,
                        'checkoutformat' => $propertyServiceToDataRailVal,
                        'overnightfee' => $propertyOverNightFeeVal,
                        'price' => $price,
                        'pfrom' => $pFrom,
                        'pday' => $pDay,
                        'day' => $day,
                        'from' => $from 
                );
                $hourlyPriceArray = Mage::getModel ( 'airhotels/customerinbox' )->getHourlyPrice ( $checkinDetails );
                $overallTotalHours = $hourlyPriceArray ['overallTotalHours'];
                $totalOverNightFee = $hourlyPriceArray ['totalOverNightFee'];
                $av = $hourlyPriceArray ['av'];
                $dayCountForOvernightFee = 0;
                $hoursMsgData = Mage::getModel ( 'airhotels/status' )->getValidationDetails ( $propertyMaximum, $propertyMinimum, $overallTotalHours );
                if($hoursMsgData){
                    Mage::app ()->getResponse ()->setBody ( $hoursMsgData );
                    return true;
                }
            } else {
                for($pr = 0; $pr < $pDay; $pr ++) {
                    $pin = date ( 'd', ($pFrom + ($pr * $day)) );
                    $pIn = ( int ) $pin;
                    $month = date ( 'n', ($pFrom + ($pr * $day)) );
                    $av [$month] [$pIn] = $price;
                }
                $msgDataDays = Mage::getModel ( 'airhotels/status' )->getCycleDetails ( $subCycle, $propertyMaximum, $propertyMinimum, $pDay );
                if($msgDataDays){
                    Mage::app ()->getResponse ()->setBody ( $msgDataDays );
                    return true;
                }
            }
            $total = Mage::getModel ( 'airhotels/search' )->setTotalFromAv ( $av, $avPrice, $hourlyEnabledOrNot, $propertyTimeData, $propertyTime, $dayCountForOvernightFee );
            $this->setOvernightFee($propertyTime,$propertyTimeData,$hourlyEnabledOrNot,$dayCountForOvernightFee,$numDayRound);
            
            $subtotalValue = $total;
            $subtotalValue = ($days_count == 'undefined') ? $subtotalValue : $subscriptionTotal;
            Mage::app ()->getLocale ()->currency ( Mage::app ()->getStore ()->getCurrentCurrencyCode () )->getSymbol ();
            $config = Mage::getStoreConfig ( 'airhotels/custom_group' );
            $serviceFee = round ( ($subtotalValue / 100) * ($config ["airhotels_servicetax"]), 2 );
            $getAvailableData = array (
                    'servicefee' => $serviceFee,
                    'subtotal' => $subtotalValue,
                    'pday' => $pDay,
                    'totalhours' => $overallTotalHours,
                    'totalovernightfee' => $totalOverNightFee,
                    'productid' => $productid,
                    'subcycle' => $subCycle,
                    'datecountFromdate' => $dateCountFromDate 
            );
            Mage::getModel ( 'airhotels/customerinbox' )->getAvailDates ( $getAvailableData );
        } else {
            $msgData = Mage::helper ( 'airhotels' )->__ ( 'Dates are not available refer to calendar' );
            Mage::app ()->getResponse ()->setBody ( $msgData );            
        }
    }
    /**
     * Function Name: status
     * Status Value
     *
     * @param sring $status            
     * @param int $pId            
     * @return string
     */
    public function status($status, $pId) {
        /**
         * Load the product id into 'catalog/product'
         */
        $product = Mage::getModel ( 'catalog/product' )->load ( $pId );
        if ($status == 0) {
            $status = 2;
        }
        /**
         * Get the CurrentStore
         */
        Mage::app ()->setCurrentStore ( Mage_Core_Model_App::ADMIN_STORE_ID );
        /**
         * Save the Product ID
         */
        $product->setStoreID ( 0 )->setStatus ( $status )->save ();
        /**
         * Set the current store value as zero
         */
        Mage::app ()->setCurrentStore ( 0 );
        return $status;
    }
    /**
     * Function Name: Review
     * Get the Review the for the Product
     */
    public function review($status, $reviewId) {
        /**
         * Get the Table Name prefix
         */
        $tPrefix = ( string ) Mage::getConfig ()->getTablePrefix ();
        /**
         * Appending the prefix with the table name
         */
        $reviewTable = $tPrefix . 'review';
        /**
         * Creating the Database Connection
         */
        $connection = Mage::getSingleton ( 'core/resource' )->getConnection ( 'core_write' );
        try {
            /**
             * Begin the Database connection
             */
            $connection->beginTransaction ();
            /**
             * Adding the Filter fields
             */
            $fields = array ();
            $where = array ();
            $fields ['status_id'] = $status;
            $where [] = $connection->quoteInto ( 'review_id = ?', $reviewId );
            /**
             * Updating the fields Here
             */
            $connection->update ( $reviewTable, $fields, $where );
            /**
             * Committing the Values
             */
            $connection->commit ();
        } catch ( Exception $ex ) {
            Mage::getSingleton ( 'core/session' )->addError ( $ex->getMessage () );
            return false;
        }
        return $status;
    }
    /**
     * Function Name: getBlockdate
     * Get day wise booked array by product id
     *
     * @param int $productid            
     * @param date $date            
     * @param date $to            
     * @return array $datesRange
     */
    public function getBlockdate($productid, $date, $to = NULL) {
        $datesBooked = $datesAvailable = $datesNotAvailable = array ();
        /**
         * Check weather date set
         */
        if (Mage::app ()->getRequest ()->getParam ( 'date' )) {
            /**
             * Split the Date
             */
            $dateSplitParams = explode ( "__", Mage::app ()->getRequest ()->getParam ( 'date' ) );
            /**
             * set $dateSplitParams Value to 'year'
             */
            $year = array (
                    $dateSplitParams [1] 
            );
            /**
             * Set $dateSplitParams Value to 'X'
             */
            $x = array (
                    $dateSplitParams [0] 
            );
        } else {
            $x = $date;
            $year = $to;
        }
        /**
         * Get daywise blocked details
         */
        $result = Mage::getModel ( 'airhotels/calendar' )->getCollection ()
                ->addFieldToFilter ( 'month', $x )
                ->addFieldToFilter ( 'year', $year )
                ->addFieldToFilter ( 'product_id', $productid )
                ->addFieldToFilter ( 'blocktime', array (
                'eq' => '' 
        ) );
        /**
         * Iterating the loop
         */
        foreach ( $result as $res ) {
            /**
             * Setting the $res Vales to $bookavailVal
             */
            $bookavailVal = $res ['book_avail'];
            /**
             * assign Values to '$fromdateVal'
             */
            $fromdateVal = $res ['blockfrom'];
            /**
             * assign Values to '$monthVal'
             */
            $monthVal = $res ['month'];
            /**
             * assign Values to '$priceVal'
             */
            $priceVal = $res ['price'];
            /**
             * Check weather the Value has equal to one.
             */
            if ($bookavailVal == 1) {
                /**
                 * Assign the $datesAvailable array.
                 */
                $datesAvailable [] = array (
                        $bookavailVal,
                        $fromdateVal,
                        $monthVal,
                        $priceVal 
                );
            }
            /**
             * Check the '$bookavailVal' value
             */
            if ($bookavailVal == 2) {
                /**
                 * Assign the $datesBooked array.
                 */
                $datesBooked [] = array (
                        $bookavailVal,
                        $fromdateVal,
                        $monthVal,
                        $year [0],
                        $priceVal 
                );
            }
            /**
             * Check the 'bookavailVal' having three
             */
            if ($bookavailVal == 3) {
                /**
                 * Assign the $datesNotAvailable array.
                 */
                $datesNotAvailable [] = array (
                        $bookavailVal,
                        $fromdateVal,
                        $monthVal,
                        $year [0] 
                );
            }
        }
        return array (
                $datesAvailable,
                $datesBooked,
                $datesNotAvailable 
        );
    }
    /**
     * Function Name: dateVerfiy
     *
     * @param int $productid            
     * @param date $from            
     * @param date $to            
     * @return array $calendar
     */
    public function dateVerfiy($productid, $from, $to) {
        /**
         * Product id Value.
         */
        $productId = $productid;
        /**
         * The From Date Value.
         */
        $From = date ( 'Y-n', strtotime ( $from ) );
        /**
         * The To date Value.
         */
        $To = date ( 'Y-n', strtotime ( $to ) );
        /**
         * Datefrom Value.
         */
        $dateFrom = explode ( "-", $From );
        /**
         * DateTO Value.
         */
        $dateTo = explode ( "-", $To );
        /**
         * Month Vlaue.
         */
        $month = array_unique ( array (
                $dateFrom [1],
                $dateTo [1] 
        ) );
        $year = array_unique ( array (
                $dateFrom [0],
                $dateTo [0] 
        ) );
        return $this->getBlockdate ( $productId, $month, $year );
    }
    /**
     * Function Name : getBlockDateBook
     * Get the blocked date values
     */
    public function getBlockdateBook($productid, $date, $to = NULL) {
        /**
         * Initalise the '$datesRange'
         */
        $datesRange = array ();
        /**
         * Dealstatus array with
         * 'processing'
         * 'complete'
         */
        $dealstatus = array (
                'processing',
                'complete' 
        );
        /**
         * Check weather the date is set
         */
        if (Mage::app ()->getRequest ()->getParam ( 'date' )) {
            /**
             * DateSplit Value.
             */
            $dateSplit = explode ( "__", Mage::app ()->getRequest ()->getParam ( 'date' ) );
            $x = array (
                    $dateSplit [0] 
            );
            $year = array (
                    $dateSplit [1] 
            );
        } else {
            $x = $date;
            $year = $to;
        }
        $yearVal = $year [0];
        /**
         * Get the colletion value for 'airhotels/airhotels'
         * 'fromdate'
         * 'todate'
         */
        $range = Mage::getModel ( 'airhotels/airhotels' )->getCollection ()->addFieldToSelect ( array (
                'fromdate',
                'todate' 
        ) );
        /**
         * Get the table prefix value
         */
        $salesFlatOrder = ( string ) Mage::getConfig ()->getTablePrefix () . 'sales_flat_order';
        /**
         * Join query for select the 'sales_flat_order' and 'main_table'
         */
        $range->getSelect ()->join ( array (
                'sales_flat_order' => $salesFlatOrder 
        ), "(sales_flat_order.entity_id = main_table.order_item_id AND main_table.entity_id = $productid AND ( YEAR(main_table.fromdate)='$yearVal' OR YEAR(main_table.todate)='$yearVal' ) AND main_table.order_status=1 AND (sales_flat_order.status='$dealstatus[1]' OR sales_flat_order.status='$dealstatus[0]'))", array () );
        if (count ( $range ) > 0) {
            foreach ( $range as $rangeVal ) {
                /**
                 * Get Collection value for 'airhotels/product'
                 */
                $dateArr = Mage::getModel ( 'airhotels/product' )->getDaysBlock ( $rangeVal ['fromdate'], $rangeVal ['todate'] );
                /**
                 * Itearting the Loop Value.
                 */
                foreach ( $dateArr as $dateArrVal ) {
                    /**
                     * Get Data Array Value.
                     */
                    $getDateArr = explode ( '-', $dateArrVal );
                    if ($getDateArr [0] == $year [0] && $getDateArr [1] == $x [0]) {
                        $datesRange [] = $getDateArr [2];
                    }
                }
            }
        }        
        return $datesRange;
    }
    /**
     * Function Name: checkDatesHourlyBlockedFlag
     * Checking whether dates blocked by host or not
     *
     * @param int $productid            
     * @param int $propertyServiceFromRail            
     * @param int $propertyServiceToRail            
     * @param array $getAllDays            
     * @return bool
     */
    public function checkDatesHourlyBlockedFlag($productid, $propertyServiceFromRail, $propertyServiceToRail, $getAllDays) {
        $Incr = 0;
        /**
         * Get product service from and to hours
         */
        $propertyServiceFromTimeData = Mage::helper ( 'airhotels/airhotel' )->getPropertyServiceFromTimeByProductId ( $productid );
        /**
         * Get the property Service To Time Data Value
         */
        $propertyServiceToTimeData = Mage::helper ( 'airhotels/airhotel' )->getPropertyServiceToTimeByProductId ( $productid );
        /**
         * Property Service From Array Value
         */
        $propertyServiceFromArray = explode ( ":", $propertyServiceFromTimeData );
        /**
         * Property Service From Data Value
         */
        $propertyServiceFromData = $propertyServiceFromArray [0];
        /**
         * Property Service From Period Data Value
         */
        $propertyServiceFromPeriodData = $propertyServiceFromArray [1];
        $propertyServiceToArray = explode ( ":", $propertyServiceToTimeData );
        $propertyServiceToData = $propertyServiceToArray [0];
        $propertyServiceToPeriodData = $propertyServiceToArray [1];
        $propertyServiceFromDataRail = $this->getRailwayTimeFormat ( $propertyServiceFromPeriodData, $propertyServiceFromData );
        $propertyServiceToDataRail = $this->getRailwayTimeFormat ( $propertyServiceToPeriodData, $propertyServiceToData );
        $datesCountValue = count ( $getAllDays );
        /**
         * check weather get the all days
         */
        if (isset ( $getAllDays [0] )) {
            $yearCheck = date ( "Y", strtotime ( $getAllDays [0] ) );
            $monthCheck = date ( "m", strtotime ( $getAllDays [0] ) );
            $dayCheck = date ( "d", strtotime ( $getAllDays [0] ) );
            /**
             * Get hourly based booked details
             */
            $blockedTimeBlocked = Mage::getModel ( 'airhotels/product' )->getTimewiseBookedArray ( $productid, $monthCheck, $yearCheck, $dayCheck, 2 );
            $blockedTimeNot = Mage::getModel ( 'airhotels/product' )->getTimewiseBookedArray ( $productid, $monthCheck, $yearCheck, $dayCheck, 3 );
            /**
             * Merging the two arrays
             */
            $mergedBlockedArray = array_merge ( $blockedTimeBlocked, $blockedTimeNot );
            if (count ( $mergedBlockedArray ) >= 1) {
                for($timeStarts = $propertyServiceFromRail; $timeStarts < $propertyServiceToDataRail; $timeStarts ++) {
                    $blockedArrayValue = Mage::getModel ( 'airhotels/customerinbox' )->getTwelveTimeFormat ( $timeStarts, $timeStarts + 1 );
                    /**
                     * Get collection for 'airhotels/customerreply' for incrementValue
                     */
                    Mage::getModel ( 'airhotels/customerreply' )->getIncrementVal ( $blockedArrayValue, $mergedBlockedArray );
                }
            }
        }
        if (isset ( $getAllDays [$datesCountValue - 1] )) {
            $yearCheck = date ( "Y", strtotime ( $getAllDays [$datesCountValue - 1] ) );
            $monthCheck = date ( "m", strtotime ( $getAllDays [$datesCountValue - 1] ) );
            $dayCheck = date ( "d", strtotime ( $getAllDays [$datesCountValue - 1] ) );
            /**
             * Get hourly based booked details
             */
            $blockedTimeBlocked = Mage::getModel ( 'airhotels/product' )->getTimewiseBookedArray ( $productid, $monthCheck, $yearCheck, $dayCheck, 2 );
            $blockedTimeNot = Mage::getModel ( 'airhotels/product' )->getTimewiseBookedArray ( $productid, $monthCheck, $yearCheck, $dayCheck, 3 );
            $mergedBlockedArray = array_merge ( $blockedTimeBlocked, $blockedTimeNot );
            if (count ( $mergedBlockedArray ) >= 1) {
                for($timeStarts = $propertyServiceFromDataRail; $timeStarts < $propertyServiceToRail; $timeStarts ++) {
                    $blockedArrayValue = Mage::getModel ( 'airhotels/customerinbox' )->getTwelveTimeFormat ( $timeStarts, $timeStarts + 1 );
                    Mage::getModel ( 'airhotels/customerreply' )->getIncrementVal ( $blockedArrayValue, $mergedBlockedArray );
                }
            }
        }
        for($datesInc = 1; $datesInc < $datesCountValue - 1; $datesInc ++) {
            /**
             * Get the Year Value
             */
            $yearCheck = date ( "Y", strtotime ( $getAllDays [$datesInc] ) );
            /**
             * Get the day Value
             */
            $monthCheck = date ( "m", strtotime ( $getAllDays [$datesInc] ) );
            /**
             * Get hourly based booked details
             */
            $dayCheck = date ( "d", strtotime ( $getAllDays [$datesInc] ) );
            /**
             * Get hourly based booked details
             */
            $blockedTimeBlocked = Mage::getModel ( 'airhotels/product' )->getTimewiseBookedArray ( $productid, $monthCheck, $yearCheck, $dayCheck, 2 );
            $blockedTimeNot = Mage::getModel ( 'airhotels/product' )->getTimewiseBookedArray ( $productid, $monthCheck, $yearCheck, $dayCheck, 3 );
            $mergedBlockedArray = array_merge ( $blockedTimeBlocked, $blockedTimeNot );
            /**
             * Get the 'airhotels/customerreply' Value with countIncremental
             */
            Mage::getModel ( 'airhotels/customerreply' )->countIncrementVal ( $mergedBlockedArray );
        }
        return $Incr;
    }
    /**
     * Function Name: 'checkTwoDatesHourlyBlockedFlag'
     * Checking whether dates (two dates interval) blocked by host or not
     *
     * @param int $productid            
     * @param int $propertyServiceFromRail            
     * @param int $propertyServiceToRail            
     * @param array $getAllDays            
     * @return bool
     */
    public function checkTwoDatesHourlyBlockedFlag($productid, $propertyServiceFromRail, $propertyServiceToRail, $getAllDays) {
        $Incr = 0;
        /**
         * Get product service from and to hours
         */
        $propertyServiceFromTimeDataVal = Mage::helper ( 'airhotels/airhotel' )->getPropertyServiceFromTimeByProductId ( $productid );
        $propertyServiceToTimeDataVal = Mage::helper ( 'airhotels/airhotel' )->getPropertyServiceToTimeByProductId ( $productid );
        $propertyServiceFromArrayVal = explode ( ":", $propertyServiceFromTimeDataVal );
        $propertyServiceFromDataVal = $propertyServiceFromArrayVal [0];
        $propertyServiceFromPeriodDataVal = $propertyServiceFromArrayVal [1];
        $propertyServiceToArrayVal = explode ( ":", $propertyServiceToTimeDataVal );
        $propertyServiceToDataVal = $propertyServiceToArrayVal [0];
        $propertyServiceToPeriodDataVal = $propertyServiceToArrayVal [1];
        $propertyServiceToDataRailVal = $this->getRailwayTimeFormat ( $propertyServiceToPeriodDataVal, $propertyServiceToDataVal );
        $propertyServiceFromDataRailVal = $this->getRailwayTimeFormat ( $propertyServiceFromPeriodDataVal, $propertyServiceFromDataVal );
        /**
         * Check weather the getAllDays is set
         */
        if (isset ( $getAllDays [0] )) {
            $yearCheckVal = date ( "Y", strtotime ( $getAllDays [0] ) );
            $monthCheckVal = date ( "m", strtotime ( $getAllDays [0] ) );
            $dayCheckVal = date ( "d", strtotime ( $getAllDays [0] ) );
            /**
             * Get hourly based booked details
             */
            $blockedTimeBlocked = Mage::getModel ( 'airhotels/product' )->getTimewiseBookedArray ( $productid, $monthCheckVal, $yearCheckVal, $dayCheckVal, 2 );
            /**
             * Get the collection of 'airhotels/product' with bookedArray values
             */
            $blockedTimeNot = Mage::getModel ( 'airhotels/product' )->getTimewiseBookedArray ( $productid, $monthCheckVal, $yearCheckVal, $dayCheckVal, 3 );
            /**
             * Merged the two blocked arrays
             */
            $mergedBlockedArray = array_merge ( $blockedTimeBlocked, $blockedTimeNot );
            
            if (count ( $mergedBlockedArray ) >= 1) {
                for($timeStarts = $propertyServiceFromRail; $timeStarts < $propertyServiceToDataRailVal; $timeStarts ++) {
                    $blockedArrayValue = Mage::getModel ( 'airhotels/customerinbox' )->getTwelveTimeFormat ( $timeStarts, $timeStarts + 1 );
                    Mage::getModel ( 'airhotels/customerreply' )->getIncrementVal ( $blockedArrayValue, $mergedBlockedArray );
                }
            }
        }
        if (isset ( $getAllDays [1] )) {
            $yearCheck = date ( "Y", strtotime ( $getAllDays [1] ) );
            $monthCheck = date ( "m", strtotime ( $getAllDays [1] ) );
            $yearCheckVal = date ( "d", strtotime ( $getAllDays [1] ) );
            /**
             * Get hourly based booked details
             */
            $blockedTimeBlocked = Mage::getModel ( 'airhotels/product' )->getTimewiseBookedArray ( $productid, $monthCheckVal, $yearCheckVal, $dayCheckVal, 2 );
            $blockedTimeNot = Mage::getModel ( 'airhotels/product' )->getTimewiseBookedArray ( $productid, $monthCheck, $yearCheck, $dayCheckVal, 3 );
            $mergedBlockedArray = array_merge ( $blockedTimeBlocked, $blockedTimeNot );
            if (count ( $mergedBlockedArray ) >= 1) {
                for($timeStarts = $propertyServiceFromDataRailVal; $timeStarts < $propertyServiceToRail; $timeStarts ++) {
                    $blockedArrayValue = Mage::getModel ( 'airhotels/customerinbox' )->getTwelveTimeFormat ( $timeStarts, $timeStarts + 1 );
                    Mage::getModel ( 'airhotels/customerreply' )->getIncrementVal ( $blockedArrayValue, $mergedBlockedArray );
                }
            }
        }
        return $Incr;
    }
    /**
     * Function Name: getRailwayTimeFormat
     * Get Railway TimeFormat
     *
     * @param string $propertyServiceFromPeriod            
     * @param string $propertyServiceFrom            
     * @return number
     */
    public function getRailwayTimeFormat($propertyServiceFromPeriod, $propertyServiceFrom) {
        if ($propertyServiceFromPeriod == 'PM') {
            if ($propertyServiceFrom != 12) {
                /**
                 * Propert Service From Rail Value
                 */
                $propertyServiceFromRail = $propertyServiceFrom + 12;
            } else {
                /**
                 * Property Service from From Rail Value
                 */
                $propertyServiceFromRail = $propertyServiceFrom;
            }
        } else {
            if ($propertyServiceFrom != 12) {
                $propertyServiceFromRail = $propertyServiceFrom;
            } else {
                $propertyServiceFromRail = 0;
            }
        }
        /**
         * Returning the PropertyService from Rail Value
         */
        return $propertyServiceFromRail;
    }
    /**
     * Function Name: getHourlywiseBlockdate
     * Get hourly wise booked details by product id
     *
     * @param int $productid            
     * @param date $date            
     * @param date $to            
     * @return array $datesRange
     */
    public function getHourlywiseBlockdate($productid, $date, $to = NULL) {
        $datesBooked = $datesAvailable = $datesNotAvailable = array ();
        if (Mage::app ()->getRequest ()->getParam ( 'date' )) {
            /**
             * Splitting the Vlaues
             */
            $dateSplit = explode ( "__", Mage::app ()->getRequest ()->getParam ( 'date' ) );
            $x = array (
                    $dateSplit [0] 
            );
            /**
             * Set the Year Value
             */
            $year = array (
                    $dateSplit [1] 
            );
        } else {
            $x = $date;
            $year = $to;
        }
        $result = Mage::getModel ( 'airhotels/calendar' )->getCollection ()->addFieldToFilter ( 'month', $x )->addFieldToFilter ( 'year', $year )->addFieldToFilter ( 'product_id', $productid )->addFieldToFilter ( 'blocktime', array (
                'neq' => '' 
        ) );
        foreach ( $result as $res ) {
            $bookavail = $res ['book_avail'];
            $price = $res ['price'];
            $fromdate = $res ['blockfrom'];
            $month = $res ['month'];
            if ($bookavail == 1) {
                $datesAvailable [] = array (
                        $bookavail,
                        $fromdate,
                        $month,
                        $price 
                );
            }
            if ($bookavail == 2) {
                $datesBooked [] = array (
                        $bookavail,
                        $fromdate 
                );
            }
            if ($bookavail == 3) {
                $datesNotAvailable [] = array (
                        $bookavail,
                        $fromdate 
                );
            }
        }
        /**
         * Returning the array with
         * 'dates_available'
         * 'dates_booked'
         * 'dates_notavailable'
         */
        return array (
                $datesAvailable,
                $datesBooked,
                $datesNotAvailable 
        );
    }
    /**
     * Get day wise booked array by product id
     *
     * @param int $productid            
     * @param date $date            
     * @param date $to            
     * @return array $datesRange
     */
    public function getBlockdateAdvanced($productid, $date, $to = NULL) {
        $datesBooked = $datesAvailable = $datesNotAvailable = array ();
        if (Mage::app ()->getRequest ()->getParam ( 'date' )) {
            $dateSplit = explode ( "__", Mage::app ()->getRequest ()->getParam ( 'date' ) );
            $x = array (
                    $dateSplit [0] 
            );
            $year = array (
                    $dateSplit [1] 
            );
            /**
             * Get daywise blocked details
             */
            $result = Mage::getModel ( 'airhotels/calendar' )->getCollection ()->addFieldToFilter ( 'month', $x )->addFieldToFilter ( 'year', $year )->addFieldToFilter ( 'product_id', $productid )->addFieldToFilter ( 'blocktime', array (
                    'eq' => '' 
            ) );
        } else {
            $months = $date;
            $year = $to;
            $inc = 0;
            foreach ( $months as $month ) {
                /**
                 * Get daywise blocked details
                 */
                $yearVal = $year [$inc];
                $result = Mage::getModel ( 'airhotels/calendar' )->getCollection ()->addFieldToFilter ( 'month', $month )->addFieldToFilter ( 'year', $yearVal )->addFieldToFilter ( 'product_id', $productid )->addFieldToFilter ( 'blocktime', array (
                        'eq' => '' 
                ) );
                foreach ( $result as $res ) {
                    $bookavail = $res ['book_avail'];
                    $fromdate = $res ['blockfrom'];
                    $month = $res ['month'];
                    $price = $res ['price'];
                    if ($bookavail == 1) {
                        $datesAvailable [] = array (
                                $bookavail,
                                $fromdate,
                                $month,
                                $price 
                        );
                    }
                    if ($bookavail == 2) {
                        $datesBooked [] = array (
                                $bookavail,
                                $fromdate,
                                $month,
                                $year [0] 
                        );
                    }
                    if ($bookavail == 3) {
                        $datesNotAvailable [] = array (
                                $bookavail,
                                $fromdate,
                                $month,
                                $year [0] 
                        );
                    }
                }
                $inc = $inc + 1;
            }
        }
        return array (
                $datesAvailable,
                $datesBooked,
                $datesNotAvailable 
        );
    }
    /**
     * Function Name: checkHourlyAvailableProduct
     * checking hourly available or not
     *
     * @param int $productid            
     * @param date $from            
     * @param date $to            
     * @return array $calendar
     */
    public function checkHourlyAvailableProduct($productid, $from, $to, $propertyServiceFrom, $propertyServiceFromPeriod, $propertyServiceTo, $propertyServiceToPeriod) {
        $propertyServiceFromRail = $this->getRailwayTimeFormat ( $propertyServiceFromPeriod, $propertyServiceFrom );
        $propertyServiceToRail = $this->getRailwayTimeFormat ( $propertyServiceToPeriod, $propertyServiceTo );
        $fromArray = explode ( '/', $from );
        $checkinTime = mktime ( $propertyServiceFromRail, 0, 0, $fromArray [0], $fromArray [1], $fromArray [2] );
        $checkinTimeValue = date ( 'Y-m-d H:i:s', $checkinTime );
        $toArray = explode ( '/', $to );
        $checkoutTime = mktime ( $propertyServiceToRail, 0, 0, $toArray [0], $toArray [1], $toArray [2] );
        $checkoutTimeValue = date ( 'Y-m-d H:i:s', $checkoutTime );
        $tPrefix = ( string ) Mage::getConfig ()->getTablePrefix ();
        $orderItemTable = $tPrefix . 'sales_flat_order';
        $dealstatus [0] = "processing";
        $dealstatus [1] = "complete";
        $dateRange = Mage::getModel ( 'airhotels/airhotels' )->getCollection ()->addFieldToSelect ( array (
                'entity_id',
                'checkin_time',
                'checkout_time' 
        ) )->addFieldToFilter ( 'checkin_time', array (
                'lt' => $checkoutTimeValue 
        ) )->addFieldToFilter ( 'checkout_time', array (
                'gt' => $checkinTimeValue 
        ) )->addFieldToFilter ( 'order_status', array (
                'eq' => '1' 
        ) );
        $dateRange->getSelect ()->join ( array (
                'sales_flat_order' => $orderItemTable 
        ), "(sales_flat_order.entity_id = main_table.order_item_id AND main_table.entity_id = $productid  AND (sales_flat_order.status='$dealstatus[1]' OR sales_flat_order.status='$dealstatus[0]'))", array () );
        $rangeOne = array ();           
        foreach ( $dateRange as $dateRan ) {
            $rangeOne [] = $dateRan;
        }
        $Incr = count ( $rangeOne );
        if ($Incr <= 0) {
            /**
             * Check whether hourly based property or not
             */
            $getAllDays = Mage::getModel ( 'airhotels/product' )->getAllDatesBetweenTwoDates ( $from, $to );
            if (count ( $getAllDays ) >= 3) {
                $Incr = $this->checkDatesHourlyBlockedFlag ( $productid, $propertyServiceFromRail, $propertyServiceToRail, $getAllDays );
            } elseif (count ( $getAllDays ) == 2) {
                $Incr = $this->checkTwoDatesHourlyBlockedFlag ( $productid, $propertyServiceFromRail, $propertyServiceToRail, $getAllDays );
            } else {
                if (isset ( $getAllDays [0] )) {
                    $yearCheck = date ( "Y", strtotime ( $getAllDays [0] ) );
                    $monthCheck = date ( "m", strtotime ( $getAllDays [0] ) );
                    $dayCheck = date ( "d", strtotime ( $getAllDays [0] ) );
                    $blockedTimeBlocked = Mage::getModel ( 'airhotels/product' )->getTimewiseBookedArray ( $productid, $monthCheck, $yearCheck, $dayCheck, 2 );
                    $blockedTimeNot = Mage::getModel ( 'airhotels/product' )->getTimewiseBookedArray ( $productid, $monthCheck, $yearCheck, $dayCheck, 3 );
                    $mergedBlockedArray = array_merge ( $blockedTimeBlocked, $blockedTimeNot );
                    $Incr = Mage::getModel ( 'airhotels/customerreply' )->getIncrement ( $mergedBlockedArray, $propertyServiceFromRail, $propertyServiceToRail, $Incr );
                }
            }
        }
        if ($Incr != 0) {
            return 0;
        } else {
            return 1;
        }
    }
    /**
     * Check availablity condition
     *
     * @param Int $propertyMaximum            
     * @param Int $overallTotalHours            
     * @param String $msgData            
     * @param int $propertyMinimum            
     * @return boolean
     */
    public function checkAvailvalidationForHours($propertyMaximumHours, $overallTotalHours, $propertyMinimumHours) {
        if ($propertyMaximumHours < $overallTotalHours) {
            $msgDataHours = Mage::helper ( 'airhotels' )->__ ( 'Maximum property hour(s) which is' ) . " $propertyMaximumHours";
            Mage::app ()->getResponse ()->setBody ( $msgDataHours );
            return TRUE;
        }
        if ($propertyMinimumHours > $overallTotalHours) {
            $msgDataHours = Mage::helper ( 'airhotels' )->__ ( 'Minimum property hour(s) which is' ) . " $propertyMinimumHours";
            Mage::app ()->getResponse ()->setBody ( $msgDataHours );
            return TRUE;
        }
    }
    /**
     * check Availability for days
     *
     * @param Int $propertyMinimum            
     * @param Date $pDay            
     * @param Int $propertyMaximum            
     * @return boolean
     */
    public function checkAvailvalidationForDays($propertyMinimum, $pDay, $propertyMaximum) {
        if ($propertyMinimum > $pDay) {
            /**
             * Getting Minimum working hours of a property
             */
            $msgData = Mage::helper ( 'airhotels' )->__ ( 'Minimum property day(s) which is' ) . " $propertyMinimum";
            Mage::app ()->getResponse ()->setBody ( $msgData );
            return TRUE;
        }
        if ($propertyMaximum < $pDay) {
            /**
             * Getting maximum working hours for a property
             */
            $msgData = Mage::helper ( 'airhotels' )->__ ( 'Maximum property day(s) which is' ) . " $propertyMaximum";
            Mage::app ()->getResponse ()->setBody ( $msgData );
            return TRUE;
        }        
    }
    
    /**
     * Function name : setOvernightFee
     * 
     * Set overnight fee value to session
     */
    public function setOvernightFee($propertyTime,$propertyTimeData,$hourlyEnabledOrNot,$dayCountForOvernightFee,$numDayRound){
        Mage::getSingleton ( 'core/session' )->setRemoveIncludedOvernightFeeInCart ( 0 );
        if ($propertyTime == $propertyTimeData && $hourlyEnabledOrNot == 0 && $dayCountForOvernightFee == $numDayRound) {
            Mage::getSingleton ( 'core/session' )->setRemoveIncludedOvernightFeeInCart ( 1 );
        }
    }
}