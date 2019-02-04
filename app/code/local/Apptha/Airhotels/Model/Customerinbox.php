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
class Apptha_Airhotels_Model_Customerinbox extends Mage_Core_Model_Abstract {
 /**
  * Construct Method
  *
  * @see Varien_Object::_construct()
  */
 public function _construct() {
  /**
   * calling the parent Constrcut method.
   */
  parent::_construct ();
  $this->_init ( 'airhotels/customerinbox' );
 }

 /**
  * Convert 24 hours time format to twelve hours time format
  *
  * @param int $start
  * @param int $end
  * @return string
  */
 public function getTwelveTimeFormat($start, $end) {
  /**
   * Tweleve hours Format Time Vlaue.
   */
  $twelveHoursFormat = array ( '12AM','1AM','2AM','3AM','4AM', '5AM', '6AM','7AM','8AM','9AM','10AM',
    '11AM','12PM','1PM','2PM','3PM','4PM','5PM','6PM','7PM','8PM','9PM','10PM','11PM');
  /**
   * Return the Tweleve hours date format value.
   */
  return "$twelveHoursFormat[$start]-$twelveHoursFormat[$end]";
 }

 /**
  * Function Name: getAvailDates
  * Get Data
  *
  * @param number $serviceFee
  * @param number $subtotalValue
  * @param int $pDay
  * @param number $overallTotalHours
  * @param number $totalOverNightFee
  */
 public function getAvailDates($getAvailableData) {
     /**
      * Load product collections
      * @var unknown
      */
  $product = Mage::getModel ( 'catalog/product' )->load ( $productid );
  /**
   * set the empty vlaue to null
   */
  $actionMessage = '';
  /**
   * Set the currency Symbol
   */
  $currencySymbol = Mage::app ()->getLocale ()->currency ( Mage::app ()->getStore ()->getCurrentCurrencyCode () )->getSymbol ();
  /**
   * config Value for 'airhotels/custom_group'
   */
  $config = Mage::getStoreConfig ( 'airhotels/custom_group' );
  /**
   * Calculate service fee and $varServiceFee
   * @var unknown
   */
  $serviceFee = round ( ($getAvailableData['subtotal'] / 100) * ($config ["airhotels_servicetax"]), 2 );
  $varServiceFee = Mage::helper ( 'directory' )->currencyConvert ( $serviceFee, Mage::app ()->getStore ()->getBaseCurrencyCode (), Mage::app ()->getStore ()->getCurrentCurrencyCode () );
  /**
   * Set session for subtotal and service fee
   */
  Mage::getSingleton ( 'core/session' )->setAnyBaseSubtotal ( $getAvailableData['subtotal'] );
  Mage::getSingleton ( 'core/session' )->setAnyBaseServiceFee ( $serviceFee );
  /**
   * service Fee base Vlaue
   */
  $subtotal = Mage::helper ( 'directory' )->currencyConvert ( $getAvailableData['subtotal'], Mage::app ()->getStore ()->getBaseCurrencyCode (), Mage::app ()->getStore ()->getCurrentCurrencyCode () );
  $noDays = $getAvailableData['pday'];
  $getSecurityEnabledOrNot = Mage::helper ( 'airhotels/product' )->getSecurityEnabledOrNot ();
  /**
   * Getting security fee information
   */
  if ($getSecurityEnabledOrNot == 0 && $getAvailableData['subcycle'] =='undefined') {
   $allOptions = $product->getOptions ();
   if ($allOptions) {
    foreach ( $allOptions as $option ) {
     foreach ( $option->getValues () as $value ) {
         $serviceFeeAmount = round(Mage::helper('directory')->currencyConvert($value->getPrice(), Mage::app()->getStore()->getBaseCurrencyCode(), Mage::app()->getStore()->getCurrentCurrencyCode()), 2);
      $actionMessage .= "<p class='service-cleaning-fee-title'>" . $value->getDefaultTitle () . " Fee</p><p class='service-cleaning-fee-title'>" . $currencySymbol.$serviceFeeAmount . "</p>";
     }
    }
   }
  }
  /**
   * Action Message.
   * check subcycle is undefined or not
   */
  if($getAvailableData['subcycle'] == 'undefined') {
  /**
   * Set subtotal message.
   */
  $actionMessage = $actionMessage . "<p class='subtotal'>" . Mage::helper ( 'airhotels' )->__ ( 'Subtotal' ) . " </p>
                    <h2 class='bigTotal'>" . $currencySymbol . number_format ( $subtotal ) . "</h2> <input type='hidden' id='subtotal_days' value = '$noDays'> <input type='hidden' id='subtotal_amt' value = '$subtotal'>";
  } else {
  /**
   * Set checkout date message
   */
  $actionMessage = $actionMessage ."<p class='subtotal'>" . Mage::helper ( 'airhotels' )->__ ( 'Checkout Date' ) . "</p>". "<h2 class='bigTotal'>" . $getAvailableData['datecountFromdate'] . "</h2>"."<p class='subtotal'>" . Mage::helper ( 'airhotels' )->__ ( 'Subtotal' ) . "
                    </p><div style='font-size: 90%;'>".  Mage::helper ( 'airhotels' )->__ ( '(Per Iteration)' ) . "</div>
                    <input type='text' id='specialprice' value='' hidden/>
                    <h2 class='bigTotal'>" . $currencySymbol . $subtotal . "</h2> <input type='hidden' id='subtotal_days' value = ''> <input type='hidden' id='subtotal_amt' value = ".$subtotal."><input type='hidden' id='hiddento' readonly='' name='to' autocomplete='off' placeholder='mm/dd/yyyy' value=".$getAvailableData['datecountFromdate']." class='hasDatepicker'>";

  }
  /**
   * Check the value is equal to zero.
   */
  if ($getAvailableData['totalhours'] != 0) {
   $hourlyMessage = '';
   if ($getAvailableData['totalovernightfee'] >= 1) {
    $hourlyMessage = Mage::helper ( 'airhotels' )->__ ( 'Excluding night hour(s)' );
   }
   $hoursMsg = Mage::helper ( 'airhotels' )->__ ( 'Total Hour(s)' ) . ' : ';
   $actionMessage = $actionMessage . '<p class="allpropertyhours">' . $hoursMsg . ' ' . $getAvailableData['totalhours'] . ' ' . '<span class="allpropertyhours_span">' . $hourlyMessage . '</span></p>';
  }
  /**
   * action Messgae vlaue.
   */
  $actionMessage = $actionMessage . '<p class="subtotal processing">(* ' . Mage::helper ( 'airhotels' )->__ ( 'Exclude processing fee' ) . " " . $currencySymbol . $varServiceFee . ")
                        <input type='hidden' id='serviceFee' name='serviceFee' value='" . $serviceFee . "' />
                        <input type='hidden' id='overall_total_hours' name='overall_total_hours' value='" . $getAvailableData['totalhours'] . "' />
                        <input type='hidden' id='hourly_night_fee' name='hourly_night_fee' value='" . $getAvailableData['totalovernightfee'] . "' />
                        </p>

                    <div class='clear'></div>
                    ";
  /**
   * Set session for subtotal and service fee
   */
  Mage::getSingleton ( 'core/session' )->setSubtotal ( $subtotal );
  Mage::getSingleton ( 'core/session' )->setSubCycle ( $getAvailableData['subcycle'] );
  Mage::getSingleton ( 'core/session' )->setServiceFee ( $serviceFee );
  /**
   * Send the response to body.
   */
  Mage::app ()->getResponse ()->setBody ( $actionMessage );
 }

 /**
  * Function Name: dayWiseBooked
  * get Day wise booked
  *
  * @param int $Incr
  * @param date $numDay1
  * @param date $sTime1
  * @param date $days
  */
 public function dayWiseBooked($Incr, $numDay1, $sTime1, $days, $day, $productid) {
  $preMonth = $preYear = '';  
  if ($Incr == 0) {
   /**
    * Itearting Loop
    */
   for($d1 = 0; $d1 < $numDay1; $d1 ++) {
    $daysIn = date ( 'm/d/Y', ($sTime1 + ($d1 * $day)) );
    $dIn = date ( 'd', ($sTime1 + ($d1 * $day)) );
    $currentMonth = date ( 'm', ($sTime1 + ($d1 * $day)) );
    $currentYear = date ( 'Y', ($sTime1 + ($d1 * $day)) );
    /**
     * Check block and not available dates based on month and year
     */
    if ($preMonth != $currentMonth || $preYear != $currentYear) {
     $calendarDate = $booked = $booked = $notAvail = array ();
     /**
      * Getting booked and blocked days
      */
     $calendarDate = Mage::getModel ( 'airhotels/airhotels' )->getBlockdate ( $productid, $currentMonth, $currentYear );
     $booked = Mage::getModel ( 'airhotels/product' )->getDays ( count ( $calendarDate [1] ), $calendarDate [1] );
     $notAvail = Mage::getModel ( 'airhotels/product' )->getDays ( count ( $calendarDate [2] ), $calendarDate [2] );
     $booked = array_unique ( $booked );
     $notAvail = array_unique ( $notAvail );
     $preMonth = $currentMonth;
     $preYear = $currentYear;
    }
    /**
     * Checking whether its available in calendar ornot
     */
    
    if (in_array ( $daysIn, $days ) || in_array ( $dIn, $booked ) || in_array ( $dIn, $notAvail )) {
     $Incr = 1 + $Incr;
     break;
    }
   }
  }
  return $Incr;
 }
 /**
  * Function Name: getPartiallyBlockdateBook
  * Get partially block date
  *
  * @param type $uniquePropertyArray
  *         booked dates
  * @param type $productId
  *         product id
  * @param type $propertyDateValue
  *         date
  * @return string
  */
 public function getPartiallyBlockdateBook($uniquePropertyArray, $productId, $propertyDateValue) {
  /**
   * Getting partially booked dates
   */
  $dateValue = explode ( "__", $propertyDateValue );
  $monthValue = $dateValue [0];
  $yearValue = $dateValue [1];
  $partiallyBookedArray = array ();
  $partiallyBookedView = array ();
  /**
   * Getting property service hours
   */
  $propertyServiceFromTimeVal = Mage::helper ( 'airhotels/airhotel' )->getPropertyServiceFromTimeByProductId ( $productId );
  $propertyServiceToTimeVal = Mage::helper ( 'airhotels/airhotel' )->getPropertyServiceToTimeByProductId ( $productId );
  $propertyServiceFromArrayVal = explode ( ":", $propertyServiceFromTimeVal );
  $propertyServiceFromDataVal = $propertyServiceFromArrayVal [0];
  $propertyServiceFromPeriodDataVal = $propertyServiceFromArrayVal [1];
  $propertyServiceToArrayVal = explode ( ":", $propertyServiceToTimeVal );
  $propertyServiceToDataVal = $propertyServiceToArrayVal [0];
  $propertyServiceToPeriodDataVal = $propertyServiceToArrayVal [1];
  /**
   * Getting railway time format
   */
  $dateCheckArray = $fomToAvailArray = $fomToHourlyArray = $fomToPartialBlockArray = array();
  $fromCheck = Mage::getModel ( 'airhotels/airhotels' )->getRailwayTimeFormat ( $propertyServiceFromPeriodDataVal, $propertyServiceFromDataVal );
  $toCheck = Mage::getModel ( 'airhotels/airhotels' )->getRailwayTimeFormat ( $propertyServiceToPeriodDataVal, $propertyServiceToDataVal );
  $dateCheckArray = array($fromCheck,$toCheck);
  /**
   * Getting partially booked array
   */
  foreach ( $uniquePropertyArray as $unique ) {
   if (! empty ( $monthValue ) && ! empty ( $yearValue ) && ! empty ( $unique )) {
    /**
     * Get time wise special price array
     */
    $blockedTimeSp = Mage::getModel ( 'airhotels/product' )->getTimewiseBookedArray ( $productId, $monthValue, $yearValue, $unique, 1 );
    /**
     * Get time wise special price array
     */
    $blockedTimeBlocked = Mage::getModel ( 'airhotels/product' )->getTimewiseBookedArray ( $productId, $monthValue, $yearValue, $unique, 2 );
    /**
     * Get time wise special price array
     */
    $blockedTimeNot = Mage::getModel ( 'airhotels/product' )->getTimewiseBookedArray ( $productId, $monthValue, $yearValue, $unique, 3 );
    $from = $to = $monthValue . '/' . $unique . '/' . $yearValue;
    /**
     * For checking partial availability
     */
    $partialAvail = 0;
    $checkingFromDate = date ( 'Y-m-d', strtotime ( $from ) );
    $uniqueId = ltrim ( $unique, '0' );
    $html = '<div><div class="airhotels_calender_hourly" id="' . $uniqueId . '_hourly_table" ><div class="airhotels_hourly_overlay_close" onclick="airhotels_hourly_overlay_close()"></div><table cellspacing="0" cellpadding="2"  border="1"><tboby><tr>';
    $timeOneArray = array (12,1,2,3,4,5,6,7,8,9,10);
    $timeTwoArray = array (1,2,3,4,5,6,7,8,9,10,11);
    $checkingFromDate = date ( 'Y-m-d', strtotime ( $from ) );
    $todayDateValue = Mage::getModel ( 'core/date' )->date ( 'Y-m-d' );
    $currentTimeValue = Mage::getModel ( 'core/date' )->date ( 'H' );
    for($inc = 0; $inc <= 10; $inc ++) {
     $fromTimeValue = $this->getRailwayTimeFormat ( 'AM', $timeOneArray [$inc] );
     if ($checkingFromDate == $todayDateValue && $currentTimeValue >= $fromTimeValue) {
      $html .= '<td>' . $timeOneArray [$inc] . 'AM - ' . $timeTwoArray [$inc] . 'AM </td>';
     } else {
      $flag = ( int ) Mage::getModel ( "airhotels/airhotels" )->checkHourlyAvailableProduct ( $productId, $from, $to, $timeOneArray [$inc], 'AM', $timeTwoArray [$inc], 'AM' );
      $fomToAvailArray = array($fromCheck,$toCheck);
      $partialBlockHtml = $this->partialBlock ( $fomToAvailArray, $inc, $blockedTimeBlocked, $blockedTimeNot, $flag, $blockedTimeSp,$partialAvail );
      $html .= $partialBlockHtml[0];
      $partialAvail = $partialBlockHtml[1];
     }
     if ($inc == 5) {
      $html .= '</tr><tr>';
     }
    }
    $fromTimeValue = $this->getRailwayTimeFormat ( 'AM', 11 );
    if ($checkingFromDate == $todayDateValue && $currentTimeValue >= $fromTimeValue) {
     $html .= '<td> 11AM - 12PM</td>';
    } else {
     $fomToHourlyArray = array($from,$to);
     $partialBlockHourlyHtml = $this->partialBlockHourly ( $dateCheckArray, $productId, $fomToHourlyArray, $blockedTimeBlocked, $blockedTimeNot, $blockedTimeSp,$partialAvail );
     $html .= $partialBlockHourlyHtml[0];
     $partialAvail = $partialBlockHourlyHtml[1];
    }
    $html .= '</tr><tr>';
    for($inc = 0; $inc <= 10; $inc ++) {
     $fromTimeValue = $this->getRailwayTimeFormat ( 'PM', $timeOneArray [$inc] );
     if ($checkingFromDate == $todayDateValue && $currentTimeValue >= $fromTimeValue) {
      $html .= '<td>' . $timeOneArray [$inc] . 'PM - ' . $timeTwoArray [$inc] . 'PM</td>';
     } else {
         $partialTimeCheckArray = array('fromCheck'=>$fromCheck,'toCheck'=>$toCheck,'blockedTimeBlocked'=>$blockedTimeBlocked,'blockedTimeNot'=>$blockedTimeNot,'blockedTimeSp'=>$blockedTimeSp,'inc'=>$inc,'productId'=>$productId,'from'=>$from , 'to' => $to,'partialAvail'=>$partialAvail);
         $partialhtml = Mage::getModel('airhotels/verifyhost')->partiallyHourlyTimecheck($partialTimeCheckArray);
         $html .= $partialhtml[0];
         $partialAvail = $partialhtml[1];
     }
     if ($inc == 5) {
      $html .= '</tr><tr>';
     }
    }
    $fromTimeValue = $this->getRailwayTimeFormat ( 'PM', 11 );
    if ($checkingFromDate == $todayDateValue && $currentTimeValue >= $fromTimeValue) {
     $html .= '<td> 11PM - 12AM</td>';
    } else {
     $fomToPartialBlockArray = array($from,$to);
     $partialBlockingHtml = $this->partialBlocking ( $dateCheckArray, $productId, $fomToPartialBlockArray, $blockedTimeBlocked, $blockedTimeNot, $blockedTimeSp,$partialAvail );
     $html .= $partialBlockingHtml[0];
     $partialAvail = $partialBlockingHtml[1];
    }
    $html .= '</tr>';
    $uniqueValue = ltrim ( $unique, '0' );
    /**
     * Partially booked date
     */    
    if ($partialAvail == 1) {
     $partiallyBookedArray [] = $uniqueValue;
    }
    $html .= '</tboby></table></div></div>';
    $partiallyBookedView [$uniqueValue] = $html;
   }
  }
  Mage::getSingleton ( 'core/session' )->setPartiallyBookedArray ( $partiallyBookedArray );
  return $partiallyBookedView;
 } 
 /**
  * Print the final statement
  *
  * @param unknown $inc
  * @param unknown $html
  * @return string
  */
 public function printFinal($inc, $html) {
  if ($inc == 5) {
   $html .= '</tr><tr>';
  }
  return $html;
 }
 /**
  * Partial Block
  *
  * @param Date $fromCheck
  * @param Date $toCheck
  * @param Int $productId
  * @param Date $from
  * @param Date $to
  * @param Int $blockedTimeBlocked
  * @param Int $blockedTimeNot
  * @param Int $blockedTimeSp
  * @return string
  */
 function partialBlock($fomToAvailArray, $inc, $blockedTimeBlocked, $blockedTimeNot, $flag, $blockedTimeSp,$partialAvail) {
  $htmlVal = "";
  $timeOneArray = array (12,1, 2, 3, 4, 5, 6, 7, 8, 9,10  );
  $timeTwoArray = array ( 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11 );
  if ($fomToAvailArray[0] <= $inc && $fomToAvailArray[1] > $inc) {
   if (array_key_exists ( $timeOneArray [$inc] . 'AM-' . $timeTwoArray [$inc] . 'AM', $blockedTimeBlocked )) {
    $htmlVal .= '<td id="' . $timeOneArray [$inc] . '-AM_' . $timeTwoArray [$inc] . '-AM" class="hourly_booked_blocked_byhost">' . $timeOneArray [$inc] . 'AM - ' . $timeTwoArray [$inc] . 'AM </td>';
   } elseif (array_key_exists ( $timeOneArray [$inc] . 'AM-' . $timeTwoArray [$inc] . 'AM', $blockedTimeNot )) {
    $htmlVal .= '<td id="' . $timeOneArray [$inc] . '-AM_' . $timeTwoArray [$inc] . '-AM" class="hourly_booked_notavail_byhost">' . $timeOneArray [$inc] . 'AM - ' . $timeTwoArray [$inc] . 'AM </td>';
   } elseif (! $flag) {
    $htmlVal .= '<td class="hourly_fully_booked" >' . $timeOneArray [$inc] . 'AM - ' . $timeTwoArray [$inc] . 'AM </td>';
   } elseif (array_key_exists ( $timeOneArray [$inc] . 'AM-' . $timeTwoArray [$inc] . 'AM', $blockedTimeSp )) {
    $partialAvail = 1;
    $keyValue = $timeOneArray [$inc] . 'AM-' . $timeTwoArray [$inc] . 'AM';
    $htmlVal .= '<td style="background-color:#65AA5F;" id="' . $timeOneArray [$inc] . '-AM_' . $timeTwoArray [$inc] . '-AM" class="hourly_booked_sp_byhost">' . $timeOneArray [$inc] . 'AM - ' . $timeTwoArray [$inc] . 'AM <div style="width: 25px;font-size: 1.0em;text-align: right;">' . Mage::app ()->getLocale ()->currency ( Mage::app ()->getStore ()->getCurrentCurrencyCode () )->getSymbol () . Mage::helper ( 'directory' )->currencyConvert ( $blockedTimeSp [$keyValue], Mage::app ()->getStore ()->getBaseCurrencyCode (), Mage::app ()->getStore ()->getCurrentCurrencyCode () ) . '</div></td>';
   } else {
    $partialAvail = 1;
    $htmlVal .= '<td class="hourly_partially_avail">' . $timeOneArray [$inc] . 'AM - ' . $timeTwoArray [$inc] . 'AM </td>';
   }
  } else {
   $htmlVal .= '<td>' . $timeOneArray [$inc] . 'AM - ' . $timeTwoArray [$inc] . 'AM </td>';
  }
  return array($htmlVal,$partialAvail);
 }
 /**
  * Partial Blocking by hourly
  *
  * @param Date $fromCheck
  * @param Date $toCheck
  * @param Int $productId
  * @param Date $from
  * @param Date $to
  * @param Int $blockedTimeBlocked
  * @param Int $blockedTimeNot
  * @param Int $blockedTimeSp
  * @return string
  */
 function partialBlockHourly($dateCheckArray, $productId, $fomToHourlyArray, $blockedTimeBlocked, $blockedTimeNot, $blockedTimeSp,$partialAvail) {
  $htmlResult = ""; 
  if ($dateCheckArray[0] <= 11 && $dateCheckArray[1] >= 12) {
   $flag = ( int ) Mage::getModel ( "airhotels/airhotels" )->checkHourlyAvailableProduct ( $productId, $fomToHourlyArray[0], $fomToHourlyArray[1], 11, 'AM', 12, 'PM' );
   if (array_key_exists ( '11AM-12PM', $blockedTimeBlocked )) {
    $htmlResult .= '<td id="11-AM_12-PM" class="hourly_booked_blocked_byhost" > 11AM - 12PM</td>';
   } elseif (array_key_exists ( '11AM-12PM', $blockedTimeNot )) {
    $htmlResult .= '<td id="11-AM_12-PM" class="hourly_booked_notavail_byhost" > 11AM - 12PM</td>';
   } elseif (! $flag) {
    $htmlResult .= '<td class="hourly_fully_booked" > 11AM - 12PM</td>';
   } elseif (array_key_exists ( '11AM-12PM', $blockedTimeSp )) {
    $partialAvail = 1;
    $keyValue = '11AM-12PM';
    $htmlResult .= '<td id="11-AM_12-PM" style="background-color:#65AA5F;" class="hourly_booked_sp_byhost" > 11AM - 12PM<div style="width: 25px;font-size: 1.0em;text-align: right;">' . Mage::app ()->getLocale ()->currency ( Mage::app ()->getStore ()->getCurrentCurrencyCode () )->getSymbol () . Mage::helper ( 'directory' )->currencyConvert ( $blockedTimeSp [$keyValue], Mage::app ()->getStore ()->getBaseCurrencyCode (), Mage::app ()->getStore ()->getCurrentCurrencyCode () ) . '</div></td>';
   } else {
    $partialAvail = 1;
    $htmlResult .= '<td class="hourly_partially_avail" > 11AM - 12PM</td>';
   }
  } else {
   $htmlResult .= '<td> 11AM - 12PM</td>';
  }
  return array($htmlResult,$partialAvail);
 }
 /**
  * Partial Blocking
  *
  * @param Date $fromCheck
  * @param Date $toCheck
  * @param Int $productId
  * @param Date $from
  * @param Date $to
  * @param Int $blockedTimeBlocked
  * @param Int $blockedTimeNot
  * @param Int $blockedTimeSp
  * @return string
  */
 function partialBlocking($dateCheckArray, $productId, $fomToPartialBlockArray, $blockedTimeBlocked, $blockedTimeNot, $blockedTimeSp,$partialAvail) {
  $htmlSet = "";
  $status = false;
  if ($dateCheckArray[0] <= 23 && $dateCheckArray[1] >= 0 && $status) {
   $flag = ( int ) Mage::getModel ( "airhotels/airhotels" )->checkHourlyAvailableProduct ( $productId, $fomToPartialBlockArray[0], $fomToPartialBlockArray[1], 11, 'PM', 12, 'AM' );
   if (array_key_exists ( '11PM-12AM', $blockedTimeBlocked )) {
    $htmlSet .= '<td id="11-PM_12-AM" class="hourly_booked_blocked_byhost" > 11PM - 12AM</td>';
   } elseif (array_key_exists ( '11PM-12AM', $blockedTimeNot )) {
    $htmlSet .= '<td id="11-PM_12-AM" class="hourly_booked_notavail_byhost" > 11PM - 12AM</td>';
   } elseif (! $flag) {
    $htmlSet .= '<td class="hourly_fully_booked" > 11PM - 12AM</td>';
   } elseif (array_key_exists ( '11PM-12AM', $blockedTimeSp )) {
    $partialAvail = 1;
    $keyValue = '11PM-12AM';
    $htmlSet .= '<td id="11-PM_12-AM" style="background-color:#65AA5F;" class="hourly_booked_sp_byhost" > 11PM - 12AM<div style="width: 25px;font-size: 1.0em;text-align: right;">' . Mage::app ()->getLocale ()->currency ( Mage::app ()->getStore ()->getCurrentCurrencyCode () )->getSymbol () . Mage::helper ( 'directory' )->currencyConvert ( $blockedTimeSp [$keyValue], Mage::app ()->getStore ()->getBaseCurrencyCode (), Mage::app ()->getStore ()->getCurrentCurrencyCode () ) . '</div></td>';
   } else {
    $partialAvail = 1;
    $htmlSet .= '<td class="hourly_partially_avail" > 11PM - 12AM</td>';
   }
  } else {
   $htmlSet .= '<td> 11PM - 12AM</td>';
  }
  return array($htmlSet,$partialAvail);
 }
 /**
  * Function Name: getHourlyPrice
  * Function to get houlry price
  *
  * @param int $productid
  * @param date $from
  * @param date $to
  *
  * @return array $calendar
  */
 function getHourlyPrice($checkinDetails) {
  $productid = $checkinDetails ['pId'];
  $propertyServiceFromRail = $checkinDetails ['checkin'];
  $propertyServiceToRail = $checkinDetails ['checkout'];
  $propertyServiceFromDataRail = $checkinDetails ['checkinformat'];
  $propertyServiceToDataRail = $checkinDetails ['checkoutformat'];
  $propertyOverNightFee = $checkinDetails ['overnightfee'];
  $price = $checkinDetails ['price'];
  $pFrom = $checkinDetails ['pfrom'];
  $pDay = $checkinDetails ['pday'];
  $from = $checkinDetails ['from'];
  $day = $checkinDetails ['day'];
  $hourlyPriceArray = $blockedTimeSp = array ();
  /**
   * Initilize overalltotalhours
   */
  $overallTotalHours = 0;
  $nightHours = 0;
  $totalOverNightFee = 0;
  $av = array ();
  if ($pDay >= 2) {
   $nightHours = (24 - $propertyServiceToDataRail) + $propertyServiceFromDataRail;
   for($pr = 0; $pr < $pDay; $pr ++) {
    $pin = date ( 'd', ($pFrom + ($pr * $day)) );
    $pIn = ( int ) $pin;
    $month = date ( 'n', ($pFrom + ($pr * $day)) );
    $specialPriceYear = date ( "Y", ($pFrom + ($pr * $day)) );
    /**
     * Get time wise special price array
     */
    $blockedTimeSp = Mage::getModel ( 'airhotels/product' )->getTimewiseBookedArray ( $productid, $month, $specialPriceYear, $pIn, 1 );
    /**
     * If checking whether first date
     */
    if ($pr == 0) {
     if ($propertyServiceFromDataRail <= $propertyServiceFromRail) {
      $totalHours = ( int ) $propertyServiceToDataRail - $propertyServiceFromRail;
      $av [$month] [$pIn] = Mage::getModel('airhotels/verifyhost')->getAvMonthsIn($blockedTimeSp,$propertyOverNightFee,$totalHours,$price,$propertyServiceFromRail,$propertyServiceToDataRail);
      } else {
      $totalHours = ( int ) $propertyServiceToDataRail - $propertyServiceFromDataRail;
      $av [$month] [$pIn] = Mage::getModel('airhotels/verifyhost')->AvMonthsInp($blockedTimeSp,$propertyOverNightFee,$totalHours,$price,$propertyServiceFromDataRail,$propertyServiceToDataRail);
      }
     $totalOverNightFee = $totalOverNightFee + $propertyOverNightFee;
    /**
     * If checking whether last date
     */
    } elseif ($pr == $pDay - 1) {
     if ($propertyServiceToDataRail >= $propertyServiceToRail) {
      $totalHours = Mage::getModel('airhotels/verifyhost')->getTotalHoursForAv($propertyServiceToRail,$propertyServiceFromDataRail);
      $av [$month] [$pIn] = Mage::getModel('airhotels/status')->getMonthArray($blockedTimeSp,$propertyServiceFromDataRail, $propertyServiceToRail, $price,$totalHours);      
     } else {
      $totalHours = Mage::getModel('airhotels/verifyhost')->setTotalHoursFee($propertyServiceToDataRail,$propertyServiceFromDataRail); 
      $av [$month] [$pIn] = Mage::getModel('airhotels/verifyhost')->setAvMonthIn($blockedTimeSp,$propertyOverNightFee,$propertyServiceFromDataRail,$propertyServiceToDataRail, $price,$totalHours);
      $totalOverNightFee = $totalOverNightFee + $propertyOverNightFee;
     }
    /**
     * Middle dates
     */
    } else {
     $totalHours = ( int ) $propertyServiceToDataRail - $propertyServiceFromDataRail;
     if (! empty ( $blockedTimeSp )) {
      $av [$month] [$pIn] = $propertyOverNightFee + Mage::getModel ( 'airhotels/product' )->getHourlyBasedSpecialPrice ( $blockedTimeSp, $propertyServiceFromDataRail, $propertyServiceToDataRail, $price );
     } else {  $av [$month] [$pIn] = $totalHours * $price + $propertyOverNightFee; }
     $totalOverNightFee = $totalOverNightFee + $propertyOverNightFee;
    }
    $overallTotalHours = $overallTotalHours + $totalHours;
   }
  } else {
   $pin = date ( 'd', strtotime ( $from ) );
   $pIn = ( int ) $pin;
   $month = date ( 'n', strtotime ( $from ) );
   /**
    * Get time wise special price array
    */
   $specialPriceYear = date ( "Y", strtotime ( $from ) );
   $blockedTimeSp = Mage::getModel ( 'airhotels/product' )->getTimewiseBookedArray ( $productid, $month, $specialPriceYear, $pIn, 1 );
   if ($propertyServiceFromDataRail <= $propertyServiceFromRail && $propertyServiceToDataRail >= $propertyServiceToRail) {
    $totalHours = ( int ) $propertyServiceToRail - $propertyServiceFromRail;
    if (! empty ( $blockedTimeSp )) {
     $av [$month] [$pIn] = Mage::getModel ( 'airhotels/product' )->getHourlyBasedSpecialPrice ( $blockedTimeSp, $propertyServiceFromRail, $propertyServiceToRail, $price );
    } else {
     $av [$month] [$pIn] = $totalHours * $price;
    }
   } elseif ($propertyServiceFromDataRail <= $propertyServiceFromRail && $propertyServiceToDataRail < $propertyServiceToRail) {
    $totalHours = ( int ) $propertyServiceToDataRail - $propertyServiceFromRail;
    if (! empty ( $blockedTimeSp )) {
     $av [$month] [$pIn] = $propertyOverNightFee + Mage::getModel ( 'airhotels/product' )->getHourlyBasedSpecialPrice ( $blockedTimeSp, $propertyServiceFromRail, $propertyServiceToDataRail, $price );
    } else {
     $av [$month] [$pIn] = $totalHours * $price + $propertyOverNightFee;
    }
    $totalOverNightFee = $totalOverNightFee + $propertyOverNightFee;
   } elseif ($propertyServiceFromDataRail > $propertyServiceFromRail && $propertyServiceToDataRail >= $propertyServiceToRail) {
    $totalHours = ( int ) $propertyServiceToRail - $propertyServiceFromDataRail;
    if (! empty ( $blockedTimeSp )) {
    /**
     * Get hourly based special price.
     */
     $av [$month] [$pIn] = $propertyOverNightFee + Mage::getModel ( 'airhotels/product' )->getHourlyBasedSpecialPrice ( $blockedTimeSp, $propertyServiceFromDataRail, $propertyServiceToRail, $price );
    } else {
     $av [$month] [$pIn] = $totalHours * $price + $propertyOverNightFee;
    }
    $totalOverNightFee = $totalOverNightFee + $propertyOverNightFee;
   } else {
    $totalHours = ( int ) $propertyServiceToDataRail - $propertyServiceFromDataRail;
    if (! empty ( $blockedTimeSp )) {
    /**
     * Get hourly based special price.
     */
     $av [$month] [$pIn] = $propertyOverNightFee + Mage::getModel ( 'airhotels/product' )->getHourlyBasedSpecialPrice ( $blockedTimeSp, $propertyServiceFromDataRail, $propertyServiceToDataRail, $price );
    } else {
     $av [$month] [$pIn] = $totalHours * $price + $propertyOverNightFee;
    }
    /**
     * calculate total overnight fee.
     */
    $totalOverNightFee = $totalOverNightFee + $propertyOverNightFee;
   }
   $overallTotalHours = $overallTotalHours + $totalHours;
  }
  $hourlyPriceArray ['overallTotalHours'] = $overallTotalHours;
  $hourlyPriceArray ['totalOverNightFee'] = $totalOverNightFee;
  $hourlyPriceArray ['av'] = $av;
  return $hourlyPriceArray;
 }
}