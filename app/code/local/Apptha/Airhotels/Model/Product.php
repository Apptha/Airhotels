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
class Apptha_Airhotels_Model_Product extends Mage_Core_Helper_Abstract {
    /**
     * Get time wise blacked array
     *
     * @param int $productId            
     * @param int $month            
     * @param int $year            
     * @param int $bookAvail
     *            time wise blacked value
     *            
     * @return array
     */
    public function getTimewiseBookedArray($productId, $month, $year, $day, $bookAvail) {
        /**
         * Defining array
         */
        $blockTimeArr = array ();
        /**
         * Get the collection of airhotels calendar
         */
        $day = ( int ) $day;
        if ($day < 10) {
            $day = "0" . $day;
        }
        $calendarCollection = Mage::getModel ( 'airhotels/calendar' )->getCollection ()->addFieldToFilter ( 'product_id', $productId )->addFieldToFilter ( 'month', $month )->addFieldToFilter ( 'year', $year )->addFieldToFilter ( 'blockfrom', $day )->addFieldToFilter ( 'book_avail', $bookAvail );
        /**
         * set 'collectionCount' valuse as zero.
         */
        $collectionCount = 0;
        /**
         * Iterating Foreach
         */
        foreach ( $calendarCollection as $collection ) {
            $blockTime = $collection->getBlocktime ();
            $blockTimeArr [$collectionCount] ['blockTime'] = explode ( ',', $blockTime );
            $blockTimeArr [$collectionCount] ['price'] = $collection->getPrice ();
            $collectionCount = $collectionCount + 1;
        }
        /**
         * set the $blockedTimeSp as empty array.
         */
        $blockedTimeSp = array ();
        /**
         * Iterating Foreach
         */
        foreach ( $blockTimeArr as $blockTimeData ) {
            $timeLastCount = count ( $blockTimeData ['blockTime'] );
            for($incTime = 0; $incTime < $timeLastCount - 1; $incTime ++) {
                $timeDateValue = Mage::getModel ( 'airhotels/customerinbox' )->getTwelveTimeFormat ( $blockTimeData ['blockTime'] [$incTime], $blockTimeData ['blockTime'] [$incTime + 1] );
                $blockedTimeSp [$timeDateValue] = $blockTimeData ['price'];
            }
        }
        /**
         * return the $blockedTimeSp
         */
        return $blockedTimeSp;
    }    
    /**
     * Function Name: hourlyDateBlockByHost
     * Create time string for blocked hours
     *
     * @param string $startDate            
     * @param string $endDate            
     * @return string
     */
    public function hourlyDateBlockByHost($startDate, $endDate) {
        /**
         * Getting start time array
         */
        $startTimeArray = explode ( '-', $startDate );
        /**
         * Getting endtime array
         */
        $endTimeArray = explode ( '-', $endDate );
        $startTime = $startTimeArray [0];
        $startTimePeriod = $startTimeArray [1];
        $endTime = $endTimeArray [0];
        $endTimePeriod = $endTimeArray [1];
        /**
         * get the collection of airhotels for startRailTime
         */
        $startRailTime = Mage::getModel ( 'airhotels/airhotels' )->getRailwayTimeFormat ( $startTimePeriod, $startTime );
        /**
         * get the collection of airhotels for endRailTime
         */
        $endRailTime = Mage::getModel ( 'airhotels/airhotels' )->getRailwayTimeFormat ( $endTimePeriod, $endTime );
        $first = 0;
        $timesValue = '';
        /**
         * Iterating Foreach
         */
        for($inc = $startRailTime; $inc <= $endRailTime; $inc ++) {
            /**
             * Check value of $first as zero.
             */
            if ($first == 0) {
                $timesValue .= $inc;
                $first = 1;
            } else {
                $timesValue .= ',' . $inc;
            }
        }
        /**
         * Returning the time Value
         */
        return $timesValue;
    }    
    /**
     * Functio Name: getMonthwiseArrayData
     * Get monthwise date array
     *
     * @param array $days            
     * @return array $monthwiseArray
     */
    public function getMonthwiseArrayData($days) {
        $monthwiseArray = array ();
        /**
         * Looping the Days array
         */
        foreach ( $days as $day ) {
            $monthYear = date ( "m__Y", strtotime ( $day ) );
            /**
             * check weather the monthYear Value does exist in monthWiseArray
             */
            if (array_key_exists ( $monthYear, $monthwiseArray )) {
                /**
                 * setting the Value of monthWiseArray
                 */
                $monthwiseArray [$monthYear] ['toDate'] = date ( "Y-m-d", strtotime ( $day ) );
            } else {
                /**
                 * Setting the value of monthWiseArray
                 */
                $monthwiseArray [$monthYear] ['fromDate'] = date ( "Y-m-d", strtotime ( $day ) );
                $monthwiseArray [$monthYear] ['toDate'] = date ( "Y-m-d", strtotime ( $day ) );
            }
        }
        return $monthwiseArray;
    }
    /**
     * Get all dates between two dates interval
     *
     * @param date $eventStartDate            
     * @param date $eventEndDate            
     * @return array dates
     */
    public function getAllDatesBetweenTwoDates($eventStartDate, $eventEndDate) {
        $day = 86400;
        $format = 'Y-m-d';
        /**
         * Getting start date
         */
        $startTime = strtotime ( $eventStartDate );
        /**
         * Getting End date
         */
        $endTime = strtotime ( $eventEndDate );
        /**
         * calculating number of days
         */
        $numDays = round ( ($endTime - $startTime) / $day ) + 1;
        $days = array ();
        /**
         * Iterating for loop
         */
        for($i = 0; $i < $numDays; $i ++) {
            $days [] = date ( $format, ($startTime + ($i * $day)) );
        }
        /**
         * returning the days
         */
        return $days;
    }    
    /**
     * Function Name: getSpecialPrice
     * Get the special Price
     *
     * @param string $calendarDate            
     * @return multitype
     */
    public function getSpecialPrice($calendarDate) {
        /**
         * init an empty array.
         */
        $availPrice = array ();
        $avPrice = array ();
        /**
         * Iterating foreach loop
         */        
        foreach ( $calendarDate as $avail ) {
            $available = $avail;
            foreach ( $available as $availPrice ) {
                $availMonth = $availPrice [2];
                $availDays = explode ( ",", $availPrice [1] );
                $availDaysCount = $this->getArraySize ( $availDays );
                /**
                 * Iterating for loop
                 */
                for($availDay = 0; $availDay < $availDaysCount; $availDay ++) {
                    $spDay = ( int ) $availDays [$availDay];
                    $avPrice [$availMonth] [$spDay] = $availPrice [3];
                }
                /**
                 * Set avilable day as zero.
                 */
                $availDay = 0;
            }
            break;
        }
        /**
         * Return the $availPrice value.
         */        
        return $avPrice;
    }    
    /**
     * Function Name: getArraySize
     * Get the array size
     *
     * @param array $arrayData            
     * @return number
     */
    public function getArraySize($arrayData) {
        return count ( $arrayData );
    }    
    /**
     * Function Name: getDaysBlock
     * Get the days
     *
     * @param date $sStartDate            
     * @param date $sEndDate            
     * @return string
     */
    public function getDaysBlock($sStartDate, $sEndDate) {
        /**
         * setting the StartDate
         */
        $sStartDate = gmdate ( "Y-m-d", strtotime ( $sStartDate ) );
        /**
         * setting the End Date
         */
        $sEndDate = gmdate ( "Y-m-d", strtotime ( $sEndDate ) );
        /**
         * Setting the startDate to Days array
         */
        $aDays [] = $sStartDate;
        $sCurrentDate = $sStartDate;
        /**
         * Iterating while loop
         */
        while ( $sCurrentDate < $sEndDate ) {
            $sCurrentDate = gmdate ( "Y-m-d", strtotime ( "+1 day", strtotime ( $sCurrentDate ) ) );
            $aDays [] = $sCurrentDate;
        }
        /**
         * Return days blocked.
         */
        return $aDays;
    }    
    /**
     * get Days Value
     *
     * @param int $count            
     * @param array $value            
     * @return array $availDays
     */
    public function getDays($count, $value) {
        $availDay = array ();
        /**
         * Iterating For loop
         */
        for($j = 0; $j < $count; $j ++) {
            /**
             * set the Values to AvailDay array
             */
            $availDay [] = $value [$j] [1];
        }
        return explode ( ",", implode ( ",", $availDay ) );
    }    
    /**
     * Function Name: check_in_range
     * Check In Range
     *
     * @param date $start_date            
     * @param date $end_date            
     * @param date $date_from_user            
     * @return boolean
     */
    public function check_in_range($startDate, $endDate, $dateFromUser) {
        /**
         * Convert to timestamp
         */
        $startTime = strtotime ( $startDate );
        $endTime = strtotime ( $endDate );
        $userTime = strtotime ( $dateFromUser );
        /**
         * Check that user date is between start & end
         */
        $returnValue = true;
        if ((($userTime > $startTime) && ($userTime < $endTime)) || (($userTime == $startTime) || ($userTime == $endTime))) {
            $returnValue = false;
        }
        return $returnValue;
    }    
    /**
     * Function Name: checkavalidateincal
     * Check avalilable date in calendar
     *
     * @param int $productid            
     * @param date $fromdate            
     * @param date $todate            
     * @return boolean
     */
    public function checkavalidateincal($productid, $fromdate = "", $todate = "") {
        $myCalendar = Mage::getModel ( 'airhotels/product' )->dateVerfiyAdvanced ( $productid, $fromdate, $todate );
        $day = 86400;
        /**
         * Start as time
         */
        $sTime = strtotime ( $fromdate );
        /**
         * End as time
         */
        $eTime = strtotime ( $todate );
        $numDay = round ( ($eTime - $sTime) / $day ) + 1;
        /**
         * Get days
         */
        $currentMonthVal = '';
        $currentYearVal = '';
        /**
         * Iterating For loop
         */
        for($d1 = 0; $d1 < $numDay; $d1 ++) {
            date ( 'm/d/Y', ($sTime + ($d1 * $day)) );
            $checkingMonth = date ( 'm', ($sTime + ($d1 * $day)) );
            $checkingYear = date ( 'Y', ($sTime + ($d1 * $day)) );
            if (empty ( $currentMonthVal ) && empty ( $currentYearVal ) || $currentMonthVal != $checkingMonth || $currentYearVal != $checkingYear) {
                $blocked = $notAvail = array ();
                /**
                 * Get blocked collection.
                 */
                $blocked = Mage::getModel ( 'airhotels/product' )->getDaysAdvancedSearch ( count ( $myCalendar [1] ), $myCalendar [1], $checkingMonth, $checkingYear );
                $notAvail = Mage::getModel ( 'airhotels/product' )->getDaysAdvancedSearch ( count ( $myCalendar [2] ), $myCalendar [2], $checkingMonth, $checkingYear );
                $currentMonthVal = $checkingMonth;
                $currentYearVal = $checkingYear;
            }
            $dIn = date ( 'd', ($sTime + ($d1 * $day)) );
            if (in_array ( $dIn, $blocked ) || in_array ( $dIn, $notAvail )) {
                return false;
            }
        }
        return true;
    }    
    /**
     * Function Name: getReplyCount
     * Get reply message count
     *
     * @param int $messageid            
     * @return array reply count
     */
    public function getReplyCount($messageid) {
        $value = 0;
        /**
         * Collection of customerreply
         */
        $result = Mage::getModel ( 'airhotels/customerreply' )->getCollection ()->addFieldToFilter ( 'message_id', $messageid );
        $result->getSelect ()->columns ( 'count(*) as count' );
        $result->getSelect ()->limit ( 1 );
        /**
         * Iterating Foreach
         */
        foreach ( $result as $res ) {
            $value = $res;
            break;
        }
        /**
         * Return the reply count value.
         */
        return $value;
    }
    /**
     * Function Name: getReplyMessages
     * Retreive the reply Messges
     *
     * @param int $messageid            
     * @return multitype
     */
    public function getReplyMessages($messageid) {
        $resultArray = array ();
        /**
         * Adding filter
         */
        $result = Mage::getModel ( 'airhotels/customerreply' )->getCollection ()->addFieldToFilter ( 'message_id', $messageid );
        /**
         * Iterating Foreach
         */
        foreach ( $result as $res ) {
            $resultArray [] = $res;
        }
        /**
         * return the reply messages array
         */
        return $resultArray;
    }    
    /**
     * Function Name: getCustomerPictureById
     * Get customer picture value by id
     *
     * @param int $customer_id            
     * @return multitype
     */
    public function getCustomerPictureById($customer_id) {
        /**
         * adding filter
         */
        $result = Mage::getModel ( 'airhotels/customerphoto' )->getCollection ()->addFieldToFilter ( 'customer_id', $customer_id );
        $result->getSelect ()->limit ( 1 );
        
        $searchresult = array ();
        /**
         * Iterating Foreach
         */
        foreach ( $result as $res ) {
            $searchresult [] = $res;
            break;
        }
        /**
         * Return the searchResult by id
         */
        return $searchresult;
    }    
    /**
     * get Customer Ratings value
     *
     * @param int $productId            
     * @return multitype
     */
    public function getCustomerRatings($productId) {
        $coreResource = Mage::getSingleton ( 'core/resource' );
        $connection = $coreResource->getConnection ( 'core_read' );
        /**
         * Selecting the table fields
         */
        $select = $connection->select ()->from ( array (
                'vote' => $coreResource->getTableName ( 'rating_option_vote' ) 
        ), new Zend_Db_Expr ( 'rt.rating_code,avg(vote.percent) as percent' ) )->join ( array (
                'rt' => $coreResource->getTableName ( 'rating' ) 
        ), 'vote.rating_id=rt.rating_id', array () )->join ( array (
                'rr' => $coreResource->getTableName ( 'review' ) 
        ), 'vote.review_id=rr.review_id', array () )->join ( array (
                're' => $coreResource->getTableName ( 'review_entity_summary' ) 
        ), 'vote.review_id=re.primary_id', array () )        
        ->where ( 're.store_id = ?', Mage::app ()->getStore ()->getId () )->where ( 'rt.entity_id = ?', 1 )->where ( 'vote.entity_pk_value = ?', $productId )->where ( 'rr.status_id = ?', 1 )->group ( 'rt.rating_code' );
        
        $query = $connection->query ( $select );       
        $count = array ();
        /**
         * Iterating while loop
         */
        while ( $row = $query->fetch () ) {
            $count [] = $row;
        }
        /**
         * return the count Value to customerratings
         */
        return $count;
    }
    /**
     * Function Name: getinboxMessageDetails
     * Get the inbox Message Details
     *
     * @param int $messageid            
     * @return multitype
     */
    public function getinboxMessageDetails($messageid) {
        $selectResult = array ();
        /**
         * Add Field to Filter
         */
        $result = Mage::getModel ( 'airhotels/customerinbox' )->getCollection ()->addFieldToFilter ( 'message_id', $messageid );
        /**
         * Iterating Foreach
         */
        foreach ( $result as $res ) {
            $selectResult [] = $res;
        }
        /**
         * Return inbox message details.
         */
        return $selectResult;
    }
    /**
     * Function Name: getsendMessageDetails
     * get Send Message Details
     *
     * @param int $messageid            
     * @return multitype:unknown
     */
    public function getsendMessageDetails($messageid) {
        /**
         * Get customer details.
         */
        $customer = Mage::getSingleton ( 'customer/session' )->getCustomer ();
        /**
         * Get customer Id.
         */
        $customerId = $customer->getId ();
        $selectResult = array ();
        /**
         * Add field to filter to result.
         */
        $result = Mage::getModel ( 'airhotels/customerinbox' )->getCollection ()->addFieldToFilter ( 'sender_id', $customerId )->addFieldToFilter ( 'message_id', $messageid );
        /**
         * Iterating Foreach
         */
        foreach ( $result as $res ) {
            $selectResult [] = $res;
        }
        /**
         * returning the colletion of selectedResult.
         */
        return $selectResult;
    }
    /**
     * Function Name: getInboxDetails
     * Get inbox detils
     *
     * @return multitype
     */
    public function getInboxDetails() {
        $resultData = array ();
        /**
         * get customer details
         */
        $customer = Mage::getSingleton ( 'customer/session' )->getCustomer ();
        $customerId = $customer->getId ();
        /**
         * Add field to filetr for colletion
         */
        $resultOne = Mage::getModel ( 'airhotels/customerinbox' )->getCollection ()->addFieldToFilter ( 'receiver_id', $customerId )->addFieldToFilter ( 'is_receiver_delete', 0 )->setOrder ( 'created_date', 'DESC' );
        /**
         * Add field to filetr for customerinbox
         */
        $resultTwo = Mage::getModel ( 'airhotels/customerinbox' )->getCollection ()->addFieldToFilter ( 'sender_id', $customerId )->addFieldToFilter ( 'is_reply', 1 )->addFieldToFilter ( 'is_sender_delete', 0 )->setOrder ( 'created_date', 'DESC' );
        /**
         * Iterating Foreach
         */
        foreach ( $resultOne as $res ) {
            $resultData [] = $res;
        }
        
        foreach ( $resultTwo as $res ) {
            $resultData [] = $res;
        }
        /**
         * Returning the result Data.
         */
        return $resultData;
    }
    /**
     * Function Name: dateVerfiyAdvanced
     * Dateverify Advanced has used to verifying the advanced datas
     *
     * @param int $productid            
     * @param date $from            
     * @param date $to            
     *
     * @return array $calendar
     */
    public function dateVerfiyAdvanced($productId, $from, $to) {
        $day = 86400;
        $sTime = strtotime ( $from );
        $eTime = strtotime ( $to );
        $numDay = round ( ($eTime - $sTime) / $day ) + 1;
        $month = $year = array ();
        $currentMonth = $currentYear = '';
        for($d1 = 0; $d1 < $numDay; $d1 ++) {
            date ( 'Y-n', ($sTime + ($d1 * $day)) );
            $checkingMonth = date ( 'n', ($sTime + ($d1 * $day)) );
            $checkingYear = date ( 'Y', ($eTime + ($d1 * $day)) );
            if (empty ( $currentMonth ) || empty ( $currentYear ) || $currentMonth != $checkingMonth || $currentYear != $checkingYear) {
                $month [] = $checkingMonth;
                $year [] = $checkingYear;
                $currentMonth = $checkingMonth;
                $currentYear = $checkingYear;
            }
        }
        /**
         * Return blocked dates.
         */
        return Mage::getModel ( 'airhotels/airhotels' )->getBlockdateAdvanced ( $productId, $month, $year );
    }    
    /**
     * Function Name: getHourlyBasedSpecialPrice
     *
     * @param array $blockedTimeSp            
     * @param int $propertyServiceFromRail            
     * @param int $propertyServiceToRail            
     * @param int $price            
     * @return int $spPriceTotal
     */
    public function getHourlyBasedSpecialPrice($blockedTimeSp, $propertyServiceFromRail, $propertyServiceToRail, $price) {
        $spPriceTotal = 0;
        for($specialPriceInc = $propertyServiceFromRail; $specialPriceInc < $propertyServiceToRail; $specialPriceInc ++) {
            /**
             * Get blcoked array value.
             */
            $blockedArrayValue = Mage::getModel ( 'airhotels/customerinbox' )->getTwelveTimeFormat ( $specialPriceInc, $specialPriceInc + 1 );
            if (array_key_exists ( $blockedArrayValue, $blockedTimeSp )) {
                $spPriceTotal = $spPriceTotal + $blockedTimeSp [$blockedArrayValue];
            } else {
                $spPriceTotal = $spPriceTotal + $price;
            }
        }
        /**
         * Return the Special Price Value
         */
        return $spPriceTotal;
    }    
    /**
     * Function Name: getDaysAdvancedSearch
     * Get the advance search result.
     *
     * @param int $count            
     * @param int $value            
     * @param int $checkingMonth            
     * @param int $checkingYear            
     * @return multitype:
     */
    public function getDaysAdvancedSearch($count, $value, $checkingMonth, $checkingYear) {
        $availDay = array ();
        for($j = 0; $j < $count; $j ++) {
            if ($value [$j] [2] == $checkingMonth && $value [$j] [3] == $checkingYear) {
                $availDay [] = $value [$j] [1];
            }
        }
        /**
         * Return an array.
         */
        return explode ( ",", implode ( ",", $availDay ) );
    }    
}