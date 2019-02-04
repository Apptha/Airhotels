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
class Apptha_Airhotels_Helper_General extends Mage_Core_Helper_Abstract {
    
    /**
     * Function Name: 'domainKey'
     * Generate domain key
     *
     * @return string
     */
    public function domainKey($tkey) {
        /**
         * set message.
         *
         * @var $message
         */
        $message = "EM-AIRHOTELMP0EFIL9XEV8YZAL7KCIUQ6NI5OREH4TSEB3TSRIF2SI1ROTAIDALG-JW";
        /**
         * Get the key Value
         */
        $tKeyCondition = strlen ( $tkey );
        for($i = 0; $i < $tKeyCondition; $i ++) {
            $key_array [] = $tkey [$i];
        }
        $encriptMessage = "";
        $kPos = 0;
        /**
         * Set character string.
         */
        $charsStr = "WJ-GLADIATOR1IS2FIRST3BEST4HERO5IN6QUICK7LAZY8VEX9LIFEMP0";
        /**
         * Character String for Value
         */
        $charsStrCondition = strlen ( $charsStr );
        for($i = 0; $i < $charsStrCondition; $i ++) {
            $chars_array [] = $charsStr [$i];
        }
        /**
         * message Condition
         */
        $messageCondition = strlen ( $message );
        $countKeyArray = count ( $key_array );
        for($i = 0; $i < $messageCondition; $i ++) {
            $char = substr ( $message, $i, 1 );
            /**
             * Get collection.
             */
            $offset = Mage::helper ( 'airhotels/product' )->getOffset ( $key_array [$kPos], $char );
            /**
             * get Offset Value
             */
            $encriptMessage .= $chars_array [$offset];
            $kPos ++;
            if ($kPos >= $countKeyArray) {
                $kPos = 0;
            }
        }
        /**
         * Return encrypt message.
         */
        return $encriptMessage;
    }
    
    /**
     * Function Name: advanceSearchResult
     * Advance Saerch Result
     *
     * @param array $propertyServiceToDataRail            
     * @param array $propertyServiceToRail            
     * @param array $propertyServiceFromDataRail            
     * @param array $blockedTimeSp            
     * @param float $hourlyBasedSpecialPrice            
     * @param array $customArray            
     * @param float $totalOverNightFee            
     * @return array
     */
    public function advanceSearchResult($propertyServiceToDataRail, $propertyServiceToRail, $propertyServiceFromDataRail, $blockedTimeSp, $hourlyBasedSpecialPrice, $customArray, $totalOverNightFee) {
        /**
         * Setting the Month
         */
        $month = $customArray ['month'];
        /**
         * setting Pin
         */
        $pIn = $customArray ['pIn'];
        /**
         * setting price
         */
        $price = $customArray ['price'];
        /**
         * Property OverNight Fee Value
         */
        $propertyOverNightFee = $customArray ['propertyOverNightFee'];
        $array = array ();
        /**
         * Check weather the value
         */
        if ($propertyServiceToDataRail >= $propertyServiceToRail) {
            /**
             * Calculate total hours.
             */
            $totalHours = ( int ) $propertyServiceToRail - $propertyServiceFromDataRail;
            $totalHours = Mage::getModel ( 'airhotels/customerphoto' )->getTotalHours ( $totalHours );
            $array ['av'] = Mage::getModel ( 'airhotels/customerreply' )->checkSecondDate ( $blockedTimeSp, $hourlyBasedSpecialPrice, $month, $pIn, $totalHours, $price );
        } else {
            /**
             * Total hours Value
             */
            $totalHours = ( int ) $propertyServiceToDataRail - $propertyServiceFromDataRail;
            $totalHours = Mage::getModel ( 'airhotels/customerphoto' )->getTotalHours ( $totalHours );
            $array ['av'] = Mage::getModel ( 'airhotels/customerreply' )->checkFirstDate ( $blockedTimeSp, $hourlyBasedSpecialPrice, $month, $pIn, $totalHours, $price, $propertyOverNightFee );
            /**
             * Calculate total overnight fee.
             */
            $totalOverNightFee ['totalOverNightFee'] = $totalOverNightFee + $propertyOverNightFee;
        }
        /**
         * Return result array.
         */
        return $array;
    }
    
    /**
     * Function Name: calendarStyleDayData
     *
     * calendar Style Day Data
     *
     * @param day $day            
     * @param Array $dayArray            
     * @return array
     */
    public function calendarStyleDayData($day, $dayArray) {
        /**
         * DayDataArray
         */
        $dayDataArray = array (
                "Sun" => 1,
                "Mon" => 2,
                "Tue" => 3,
                "Wed" => 4,
                "Thu" => 5,
                "Fri" => 6,
                "Sat" => 7 
        );
        /**
         * check the day data array is available in $day
         */
        if (in_array ( $day, $dayArray )) {
            return $dayDataArray [$day];
        }
    }
    
    /**
     * Function Name: calendarStyleHtmlValue
     * Get the calendar html Value
     *
     * @param unknown $ctr            
     * @param unknown $tableHtmlValue            
     * @return string
     */
    public function calendarStyleHtmlValue($ctr, $tableHtmlValue) {
        if ($ctr == 1) {
            return $tableHtmlValue . "<tr class='blockcal'>";
        }
    }
    
    /**
     * Function Name: calendarStyleValue
     * Get the Calendar style
     *
     * @param string $d            
     * @param array $speAvailArray            
     * @param array $blocked            
     * @return string
     */
    public function calendarStyleValue($d, $speAvailArray, $blocked) {
        if (in_array ( "$d", $speAvailArray ) && ! in_array ( "$d", $blocked )) {
            $style = "style='background-color:#65AA5F;cursor:pointer;'";
        } else {
            $style = "style='background-color:#FFFF00;cursor:pointer;'";
        }
        return $style;
    }
    /**
     * Function Name: calendarStyleCtrVal
     * Get the calendar style Value
     *
     * @param int $ctr            
     * @param string $tableHtmlValue            
     * @return string
     */
    public function calendarStyleCtrVal($ctr, $tableHtmlValue) {
        if ($ctr > 7) {
            $ctr = 1;
            return $tableHtmlValue . "</tr>";
        }
    }
    
    /**
     * Function Name: getTierVal
     * get Tier Value
     *
     * @param float $prices            
     * @param int $j            
     * @param float $price            
     * @param int $i            
     * @return array
     */
    public function getTierVal($prices, $j, $price, $i) {
        $qtyCache = array ();
        if ($prices [$j] ['website_price'] > $price ['website_price']) {
            unset ( $prices [$j] );
            $qtyCache [$price ['price_qty']] = $i;
        } else {
            unset ( $prices [$i] );
        }
        return $qtyCache;
    }
    
    /**
     * Function Name: sendEmailTempate
     * Send Mail Template
     *
     * @param object $dataValue            
     * @return boolean
     */
    public function sendEmailTempate($dataValue) {
        /**
         * Get entity id.
         *
         * @var $entityId.
         */
        $entityId = $dataValue->getEntityId ();
        /**
         * Get the Cutomer Id Value
         */
        $userId = Mage::getModel ( 'catalog/product' )->load ( $entityId )->getUserid ();
        /**
         * Load the Cutomer Id to get Customer Info
         */
        $Hoster = Mage::getModel ( 'customer/customer' )->load ( $userId );
        /**
         * get the customer id
         */
        $customerId = $dataValue->getCustomerId ();
        /**
         * Load the customer id into 'cutomer/customer' table.
         */
        $customer = Mage::getModel ( 'customer/customer' )->load ( $customerId );
        /**
         * Get Store Confog Values
         */
        $templateId = Mage::getStoreConfig ( 'airhotels/refund_email/refund_template' );
        /**
         * Get the Template Value
         */
        $adminName = Mage::getStoreConfig ( 'trans_email/ident_general/name' );
        /**
         * Admin Email Id
         */
        $adminEmail = Mage::getStoreConfig ( 'trans_email/ident_general/email' );
        /**
         * Send the vslue to adminEmail
         */
        $sendbyAdmin = Array (
                'name' => $adminName,
                'email' => $adminEmail 
        );
        /**
         * In case of multiple recipient use array here.
         */
        $email = $Hoster->getEmail ();
        $vars = Array ();
        $checkinTime = $checkoutTime = '';
        if ($dataValue->getCheckinTime () != '' && $dataValue->getCheckoutTime () != '') {
            $checkinTimeValue = $checkinTimeArray = array ();
            $checkinHourlyValue = $dataValue->getCheckinTime ();
            $checkinTimeValue = explode ( " ", $checkinHourlyValue );
            $checkinTimeArray = explode ( ":", $checkinHourlyValue [0] );
            /**
             * make sure the scheckin hourly time is hreater than 12
             */
            $checkinHourlyTime = $checkinTimeArray [0];
            /**
             * make sure the scheckin hourly time is hreater than 12
             */
            $checkinHourlyTime = 1 + $checkinHourlyTime;
            if ($checkinHourlyTime > 12) {
                $checkinHourlyTime = $checkinHourlyTime - 12;
                $checkinTime = $checkinHourlyTime . " PM";
            } else {
                $checkinTime = $checkinHourlyTime . " AM";
            }
            $checkoutTimeValue = $checkoutTimeArray = array ();
            $checkoutHourlyValue = $dataValue->getCheckoutTime ();
            $checkoutTimeValue = explode ( " ", $checkoutHourlyValue );
            $checkoutTimeArray = explode ( ":", $checkoutHourlyValue [0] );
            $checkoutHourlyTime = $checkoutTimeArray [0];
            $checkoutHourlyTime = 1 + $checkoutHourlyTime;
            /**
             * Check the checkout_hourly_value is greater than 12
             */
            if ($checkoutHourlyTime > 12) {
                $checkoutHourlyTime = $checkoutHourlyTime - 12;
                $checkoutTime = $checkoutHourlyTime . " PM";
            } else {
                $checkoutTime = $checkoutHourlyTime . " AM";
            }
        }
        /**
         * Array Containig More Values
         * 'orderid'
         * 'productname'
         * 'checkin'
         * 'admin_message'
         */
        $vars = Array (
                'orderid' => $dataValue->getOrderId (),
                'productname' => $dataValue->getProductName (),
                'checkin' => $dataValue->getFromdate () . ' ' . $checkinTime,
                'checkout' => $dataValue->getTodate () . ' ' . $checkoutTime,
                'customer_name' => $customer->getName (),
                'customer_email' => $customer->getEmail (),
                'admin_message' => nl2br ( $dataValue->getMessage () ) 
        );
        $storeId = Mage::app ()->getStore ()->getId ();
        $translate = Mage::getSingleton ( 'core/translate' );
        $mailTemplate = Mage::getModel ( 'core/email_template' )->sendTransactional ( $templateId, $sendbyAdmin, $email, $name, $vars, $storeId );
        $translate->setTranslateInline ( true );
        /**
         * Return mail templete.
         */
        return $mailTemplate;
    }
    
    /**
     * fUnction Name: 'bannerVal'
     * Get the Banner Value
     *
     * @param array $post            
     * @return number
     */
    public function bannerVal($post) {
        if (! empty ( $post ['banner'] )) {
            $banner = 1;
        } else {
            $banner = 0;
        }
        return $banner;
    }
    /**
     * Function Name: amenityVal
     * Get the Amenity Value
     *
     * @param array $post            
     * @return mixed
     */
    public function amenityVal($post) {
        $amenity = "";
        /**
         * Check weather the valuse has been set
         */
        if (isset ( $post ['amenity'] )) {
            /**
             * Amenity Value
             */
            $amenity = implode ( ",", $post ['amenity'] );
            /**
             * replacing the colletion with $amenity
             */
            $amenity = str_replace ( " ", "", $amenity );
        }
        return $amenity;
    }
    
    /**
     * Function Name: 'notification'
     * Get the Notification Messages
     */
    public function notification() {
        if (Mage::getSingleton ( 'core/session' )->getProfileImgSaveMsg () == 1) {
            /**
             * set sussess message.
             */
            Mage::getSingleton ( 'core/session' )->addSuccess ( 'Profile picture has been saved successfully.' );
        } else {
            /**
             * set error message.
             */
            Mage::getSingleton ( 'core/session' )->addError ( Mage::getSingleton ( 'core/session' )->getProfileImgSaveMsg () );
        }
    }
    
    /**
     * Function Name: startEndDate
     * Get the Start and End Date
     *
     * @param date $checkIn            
     * @param date $checkOut            
     * @return multitype:string
     */
    public function startEndDate($checkIn, $checkOut) {
        $date = array ();
        /**
         * Convert string to time.
         */
        if (strtotime ( $checkIn ) >= strtotime ( $checkOut )) {
            $date ['startDate'] = date ( "Y-m-d", strtotime ( $checkOut ) );
            $date ['endDate'] = date ( "Y-m-d", strtotime ( $checkIn ) );
        } else {
            $date ['startDate'] = date ( "Y-m-d", strtotime ( $checkIn ) );
            $date ['endDate'] = date ( "Y-m-d", strtotime ( $checkOut ) );
        }
        /**
         * Returnthe date
         */
        return $date;
    }
    
    /**
     * Function Name: datePriceUpdate
     * New date price update fucntion
     *
     * @param int $productId            
     * @param int $month            
     * @param int $year            
     * @param int $dateValue            
     */
    public function datePriceUpdate($productId, $month, $year, $dateValue) {
        /**
         * Get the Colletion for calendar with such additonal filters
         * 'product_id'
         * 'month'
         * 'year'
         * 'blockfrom'
         * 'blocktime'
         */
        $collections = Mage::getModel ( 'airhotels/calendar' )->getCollection ()->addFieldToFilter ( 'product_id', $productId )->addFieldToFilter ( 'month', $month )->addFieldToFilter ( 'year', $year )->addFieldToFilter ( 'blockfrom', array (
                'like' => "%" . $dateValue . "%" 
        ) )->addFieldToFilter ( 'blocktime', array (
                'eq' => '' 
        ) );
        /**
         * Block dat string
         */
        $blockingDaysString = '';
        foreach ( $collections as $collection ) {
            /**
             * Blocked String Data
             */
            $blockedStringData = $collection->getBlockfrom ();
            /**
             * Blocked days
             */
            $blockedDays = explode ( ",", $blockedStringData );
            /**
             * Blocking days
             */
            $blockingDays = array (
                    $dateValue 
            );
            /**
             * Get blocking days.
             *
             * @var $blockingDaysArray
             */
            $blockingDaysArray = array_diff ( $blockedDays, $blockingDays );
            $blockingDaysString = implode ( ",", $blockingDaysArray );
            $blockCalendartable = 'airhotels_calendar';
            $coreResource = Mage::getSingleton ( 'core/resource' );
            $connection = $coreResource->getConnection ( 'core_read' );
            /**
             * Updating the table with the fields
             * 'product_id'
             * 'month'
             * 'year'
             * 'blockfrom'
             */
            $connection->update ( $coreResource->getTableName ( $blockCalendartable ), array (
                    'blockfrom' => $blockingDaysString 
            ), array (
                    'product_id = ?' => $productId,
                    'month = ?' => $month,
                    'year = ?' => $year,
                    'blockfrom = ?' => $collection->getBlockfrom () 
            ) );
        }
    }
    
    /**
     * Function Name: 'sDaywiseBookedData'
     * Update hourly wise blocking by host
     *
     * @param int $productId            
     * @param int $month            
     * @param int $year            
     * @param string $dateValue            
     * @param string $blockTime            
     * @return void
     */
    public function deleteDaywiseBookedData($productId, $month, $year, $dateValue, $blockTime) {
        /**
         * Get collection filter by product id.
         *
         * @var $collections
         */
        $collections = Mage::getModel ( 'airhotels/calendar' )->getCollection ()->addFieldToFilter ( 'product_id', $productId )->addFieldToFilter ( 'month', $month )->addFieldToFilter ( 'year', $year )->addFieldToFilter ( 'blockfrom', $dateValue )->addFieldToFilter ( 'blocktime', array (
                'neq' => '' 
        ) );
        $coreResource = Mage::getSingleton ( 'core/resource' );
        $connection = $coreResource->getConnection ( 'core_read' );
        $blockCalendartable = 'airhotels_calendar';
        foreach ( $collections as $collection ) {
            /**
             * Get property calender id.
             *
             * @var $calendarPropertyId
             */
            $calendarPropertyId = $collection->getId ();
            $bookAvailData = $collection->getBookAvail ();
            $fDateData = $collection->getBlockfrom ();
            $pricePerData = $collection->getPrice ();
            $blockedStringData = $collection->getBlocktime ();
            $blockedTimes = explode ( ",", $blockedStringData );
            $blockingTimes = explode ( ",", $blockTime );
            $blockingTimesCount = count ( $blockingTimes );
            $startblockingTimes = $blockingTimes [0];
            /**
             * Initialize blocking times array as empty.
             *
             * @var $realBlockingTimes
             */
            $realBlockingTimes = array ();
            for($blockingTimesInc = 1; $blockingTimesInc < $blockingTimesCount - 1; $blockingTimesInc ++) {
                $realBlockingTimes [] = $blockingTimes [$blockingTimesInc];
            }
            /**
             * Get blocking time array.
             *
             * @var $blockingTimesArray
             */
            $blockingTimesArray = array_diff ( $blockedTimes, $realBlockingTimes );
            if (count ( $blockingTimesArray ) > 0 && isset ( $blockingTimesArray [0] ) && $startblockingTimes == $blockingTimesArray [0]) {
                /**
                 * Shift array values.
                 */
                array_shift ( $blockingTimesArray );
            }
            $propertyDataValue = $propertyTimesData = '';
            $propertyFlag = 0;
            /**
             * Check blocking time array is grater than 0.
             */
            if (isset ( $blockingTimesArray [0] ) && count ( $blockingTimesArray ) > 0) {
                $propertyDataValue = $blockingTimesArray [0];
            }
            foreach ( $blockingTimesArray as $blockingTimesValue ) {
                if ($propertyFlag == 0) {
                    $propertyTimesData = $blockingTimesValue;
                    $propertyDataValue = $propertyDataValue + 1;
                    /**
                     * Set property flag as 1.
                     *
                     * @var $propertyFlag
                     */
                    $propertyFlag = 1;
                } else {
                    if ($propertyDataValue != $blockingTimesValue || $blockingTimesValue == $startblockingTimes) {
                        $propertyTimesData = $propertyTimesData . ',' . $blockingTimesValue;
                        /**
                         * Get flag value.
                         *
                         * @var $flagValue
                         */
                        $flagValue = explode ( ",", $propertyTimesData );
                        
                        /**
                         * Check flag value is grater than one.
                         */
                        $calendarTableArray = array($productId,$bookAvailData,$month,$year,$fDateData,$pricePerData,$propertyTimesData);
                        Mage::getModel('airhotels/search')->insertIntoCalenderTable($flagValue,$calendarTableArray,$connection,$coreResource);
                        $propertyDataValue = $blockingTimesValue;
                        $propertyTimesData = $blockingTimesValue;
                        $propertyDataValue = $this->propertyDataValue ( $blockingTimesValue, $startblockingTimes, $propertyDataValue );
                        } else {
                        $propertyTimesData = $propertyTimesData . ',' . $blockingTimesValue;
                        $propertyDataValue = $propertyDataValue + 1;
                    }
                }
            }
            $flagValue = explode ( ",", $propertyTimesData );
            if (count ( $flagValue ) > 1) {
                /**
                 * Insert row in tables.
                 */
                $connection->insert ( $coreResource->getTableName ( $blockCalendartable ), array (
                        'product_id' => $productId,
                        'book_avail' => $bookAvailData,
                        'month' => $month,
                        'year' => $year,
                        'blockfrom' => $fDateData,
                        'price' => $pricePerData,
                        'created' => now (),
                        'updated' => now (),
                        'blocktime' => $propertyTimesData,
                        'google_calendar_event_uid' => 'ownsite' 
                ) );
            }
            /**
             * Delete row in tables.
             */
            $connection->delete ( $coreResource->getTableName ( $blockCalendartable ), array (
                    'product_id = ? ' => $productId,
                    'id = ? ' => $calendarPropertyId 
            ) );
        }
    }
    /**
     * Function Name: getProductImageUrl
     * Get Product image Url from the customerdata
     *
     * @param array $customerData            
     * @return string
     */
    public function getProductImageUrl($customerData) {
        /**
         * Initialize image path..
         *
         * @var $imageResizedValue,$imageDirUrlValue
         */
        $imageResizedValue = Mage::getBaseDir ( "media" ) . "/catalog/customer/resz_" . $customerData [0] ["imagename"];
        $imageDirUrlValue = Mage::getBaseDir ( "media" ) . "/catalog/customer/" . $customerData [0] ["imagename"];
        /**
         * Check image exist or not.
         */
        if (! file_exists ( $imageResizedValue ) && file_exists ( $imageDirUrlValue )) :
            $imageObject = new Varien_Image ( $imageDirUrlValue );
            /**
             * Creating instances for Varien_Image
             */
            $imageObject->constrainOnly ( TRUE );
            $imageObject->keepAspectRatio ( TRUE );
            $imageObject->keepFrame ( FALSE );
            /**
             * Resize the image urldecode
             *
             * Resized image resolution as 72 x 72
             */
            $imageObject->resize ( 72, 72 );
            /**
             * Save the Resized Image
             */
            $imageObject->save ( $imageResizedValue );
        
  
  
  
        
endif;
        /**
         * image url pathinfo
         */
        $imageUrlVal = Mage::getBaseUrl ( 'media' ) . "catalog/customer/" . $customerData [0] ["imagename"];
        if (file_exists ( $imageResizedValue )) {
            $imageUrlVal = Mage::getBaseUrl ( 'media' ) . "catalog/customer/resz_" . $customerData [0] ["imagename"];
        }
        /**
         * Resized image url.
         */
        return $imageUrlVal;
    }
    
    /**
     * Function Name: propertyReviewFor
     * property Review code
     *
     * @param number $avgRating            
     * @param string $PropertyReviewBlock            
     */
    public function propertyReviewFor($avgRating, $PropertyReviewBlock) {
        echo $PropertyReviewBlock->showratingCode ( ceil ( $avgRating / 20 ) );
    }
    
    /**
     * Function Name: 'propertyReviewForBlock'
     * property Review
     *
     * @param number $rate            
     * @param string $PropertyReviewBlock            
     */
    public function propertyReviewForBlock($rate, $PropertyReviewBlock) {
        echo $PropertyReviewBlock->showratingCode ( ceil ( $rate ["percent"] / 20 ) );
    }
    
    /**
     * Function Name: 'getPropertyDailyLabelByOptionId'
     * property Review
     *
     * @param number $rate            
     * @param string $PropertyReviewBlock            
     */
    public function getPropertyDailyLabelByOptionId() {
        /**
         * Getting Property Time
         */
        $propertyTimeId = Mage::helper ( 'airhotels/airhotel' )->getPropertyTime ();
        $propertyTimeValue = '';
        $propertyAttribute = Mage::getModel ( 'eav/config' )->getAttribute ( 'catalog_product', $propertyTimeId );
        /**
         * Defining Foreach
         */
        foreach ( $propertyAttribute->getSource ()->getAllOptions () as $propertyTimeOption ) {
            /**
             * Getting label
             */
            $propertyTimeLabel = $propertyTimeOption ['label'];
            /**
             * Getting value
             */
            $propertyTimeValue = $propertyTimeOption ['value'];
            if (! empty ( $propertyTimeLabel ) && $propertyTimeLabel == 'Daily') {
                return $propertyTimeValue;
            }
        }
        /**
         * return array
         */
        return $propertyTimeValue;
    }
    
    /**
     * Send host vaerification email
     *
     * @param documents $documentId            
     * @param
     *            action for verification $action
     */
    public function verifyHostMail($documentId, $action) {
        $documentCollection = Mage::getModel ( 'airhotels/verifyhost' )->load ( $documentId );
        
        /**
         * Get documentation id
         */
        if ($documentCollection ['tag_id'] == 'id') {
            /**
             * Check document id type.
             */
            if ($documentCollection ['id_type'] == 0) {
                $verification_id = 'Passport';
            } elseif ($documentCollection ['id_type'] == 1) {
                $verification_id = 'Identicard';
            } else {
                $verification_id = 'Driving Licence';
            }
        } else {
            $verification_id = $documentCollection ['tag_id'];
        }
        /**
         * Get admin email id.
         * Get from email id.
         * Get from name.
         */
        $adminEmailId = Mage::getStoreConfig ( 'airhotels/custom_email/admin_email_id' );
        $fromMailId = Mage::getStoreConfig ( "trans_email/ident_$adminEmailId/email" );
        $fromName = Mage::getStoreConfig ( "trans_email/ident_$adminEmailId/name" );
        /**
         * Load host verify email templete.
         *
         * @var $emailTemplate
         */
        $emailTemplate = Mage::getModel ( 'core/email_template' )->loadDefault ( 'airhotels_host_verify_email_template' );
        
        /**
         * Set email sender name
         * Set sender email(from email id).
         */
        $emailTemplate->setSenderName ( $fromName );
        $emailTemplate->setSenderEmail ( $fromMailId );
        $emailTemplateVariables = (array (
                'ownername' => $documentCollection ['host_name'],
                'action' => $action,
                'verification_id' => $verification_id 
        ));
        $emailTemplate->setDesignConfig ( array (
                'area' => 'frontend' 
        ) );
        /**
         * send mail to customer email ids
         */
        $emailTemplate->send ( $documentCollection ['host_email'], $fromName, $emailTemplateVariables );
        
        return;
    }
    /**
     * Function to set property data value
     * @param unknown $blockingTimesValue
     * @param unknown $startblockingTimes
     * @param unknown $propertyDataValue
     */
    public function propertyDataValue($blockingTimesValue,$startblockingTimes,$propertyDataValue){
        if ($blockingTimesValue != $startblockingTimes) {
            $propertyDataValue = $propertyDataValue + 1;
        }
        return $propertyDataValue;
    }
}