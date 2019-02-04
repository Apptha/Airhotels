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
class Apptha_Airhotels_Model_Customerphoto extends Mage_Core_Model_Abstract {
 /**
  * Construct Method
  *
  * @see Varien_Object::_construct()
  */
 public function _construct() {
  parent::_construct ();
  $this->_init ( 'airhotels/customerphoto' );
 } 
 /**
  * Functio Name: checkICSEvent
  * Check ICSEvent
  *
  * @param array $icsEvent         
  * @return string
  */
 public function checkICSEvent($icsEvent) {
  /**
   * check wether the ics is set
   */
  if (isset ( $icsEvent ['BEGIN'] )) {
   $icsEventBegin = trim ( $icsEvent ['BEGIN'] );
  } else {
   $icsEventBegin = '';
  }
  return $icsEventBegin;
 } 
 /**
  * Function Name: tableHtmlValue
  * Setting the Html Table Value
  *
  * @return string
  */
 public function tableHtmlValue() {
  /**
   * Html Table Value
   */
  $tableHtmlValue = '';
  return $tableHtmlValue . "<table border = '1' cellspacing = '0'  bordercolor='blue' cellpadding ='2' class='calend'>
                        <tr class='weekDays'>
                        <th><font size = '2' face = 'tahoma'>Sun</font></th>
                        <th><font size = '2' face = 'tahoma'>Mon</font></th>
                        <th><font size = '2' face = 'tahoma'>Tue</font></th>
                        <th><font size = '2' face = 'tahoma'>Wed</font></th>
                        <th><font size = '2' face = 'tahoma'>Thu</font></th>
                        <th><font size = '2' face = 'tahoma'>Fri</font></th>
                        <th><font size = '2' face = 'tahoma'>Sat</font></th>
                        </tr> ";
 } 
 /**
  * Function Name: checkICSEventVal
  * Get the ICSEvebt Value
  *
  * @param array $icsEvent         
  * @return string
  */
 public function checkICSEventVal($icsEvent, $endDate) {
  /**
   * check the ics value
   */
  if (empty ( $icsEvent ['DTSTART;VALUE=DATE'] ) && empty ( $icsEvent ['DTEND;VALUE=DATE'] )) {
   $eventEndDate = date ( "Y-m-d", strtotime ( $endDate ) );
  } else {
   $eventEndDate = date ( "Y-m-d", strtotime ( $endDate . " -1 day" ) );
  }
  return $eventEndDate;
 } 
 /**
  * Function Name: getTotalHours
  * Get the totals Hours
  *
  * @param float $totalHours         
  * @return number
  */
 public function getTotalHours($totalHours) {
  /**
   * Total Hours is not empty
   */
  if ($totalHours < 0) {
   $totalHours = 0;
  }
  return $totalHours;
 } 
 /**
  * Function Name: calendarStyle
  * get style for blocked, Available dates
  *
  * @param string $d         
  * @param array $speAvailArray         
  * @param array $blocked         
  * @return string
  */
 public function calendarStyle($speAvailArray, $blocked, $partiallyBookedArray, $tableHtmlValue, $_sp, $partiallyBookedView, $customArray) {
  /**
   * Extracting the customArray
   */
  $day = $customArray ['day'];
  $totaldays = $customArray ['totaldays'];
  $x = $customArray ['x'];
  $year = $customArray ['year'];
  $propertyTimeData = $customArray ['propertyTimeData'];
  $propertyTime = $customArray ['propertyTime'];
  $notAvail = $customArray ['notAvail'];
  $dayArray = array (    "Sun",   "Mon",    "Tue",    "Wed",    "Thu",    "Fri",    "Sat"  );
  $styleData = Mage::helper ( 'airhotels/general' )->calendarStyleDayData ( $day, $dayArray );
  $tl = $this->getTL ( $styleData, $totaldays );
  $ctr = 1;
  $d = 1;
  $hourlyEnabledOrNot = Mage::helper ( 'airhotels/product' )->getHourlyEnabledOrNot ();
  for($i = 1; $i <= $tl; $i ++) {
   
   if ($ctr == 1) {
    $tableHtmlValue = $tableHtmlValue . "<tr class='blockcal'>";
   }
   if ($i >= $styleData && $d <= $totaldays) {
    if (strtotime ( "$year-$x-$d" ) < strtotime ( date ( "Y-n-j" ) )) {
     $tableHtmlValue = $tableHtmlValue . "<td align='center' class='previous days '><font size = '2' face = 'tahoma'>$d</font></td>";
    } else {
    /**
     * Store calender dates and data
     * 'year','x','d','tableHtmlValue'
     * 'partiallyBookedView','sp','not_avail'
     * 'speAvailArray','blocked','partiallyBookedArray','propertyTime'
     * 'propertyTimeData','hourlyEnabledOrNot'
     */
     $calendarDatesData = array (
       'year' => $year,
       'x' => $x,
       'd' => $d,
       'tableHtmlValue' => $tableHtmlValue,
       'partiallyBookedView' => $partiallyBookedView,
       'sp' => $_sp,
       'not_avail' => $notAvail,
       'speAvailArray' => $speAvailArray,
       'blocked' => $blocked,
       'partiallyBookedArray' => $partiallyBookedArray,
       'propertyTime' => $propertyTime,
       'propertyTimeData' => $propertyTimeData,
       'hourlyEnabledOrNot' => $hourlyEnabledOrNot 
     );
     $tableHtmlValue = $this->getCalendarDates ( $calendarDatesData );
    }
    $d ++;
   } else {
    $tableHtmlValue = $tableHtmlValue . "<td>&nbsp</td>";
   }
   
   $ctr ++;
   
   if ($ctr > 7) {
    $ctr = 1;
    $tableHtmlValue = $tableHtmlValue . "</tr>";
   }
  }
  /**
   * Returning the table html Value
   */
  return $tableHtmlValue;
 }
 
 /**
  * Function Name: getCalendarDates
  *
  * @param int $styleData         
  * @param int $totaldays         
  * @return number
  */
 public function getCalendarDates($calendarDatesData) {
  $year = $calendarDatesData ['year'];
  $x = $calendarDatesData ['x'];
  $d = $calendarDatesData ['d'];
  $tableHtmlValue = $calendarDatesData ['tableHtmlValue'];
  $partiallyBookedView = $calendarDatesData ['partiallyBookedView'];
  $_sp = $calendarDatesData ['sp'];
  $notAvail = $calendarDatesData ['not_avail'];
  $speAvailArray = $calendarDatesData ['speAvailArray'];
  $blocked = $calendarDatesData ['blocked'];
  $partiallyBookedArray = $calendarDatesData ['partiallyBookedArray'];
  $propertyTime = $calendarDatesData ['propertyTime'];
  $propertyTimeData = $calendarDatesData ['propertyTimeData'];
  $hourlyEnabledOrNot = $calendarDatesData ['hourlyEnabledOrNot'];
  $date = strtotime ( "$year/$x/$d" );
  $tdDate = 'tdId' . '_' . date ( "m/d/Y", $date );
  if (in_array ( "$d", $partiallyBookedArray ) && $propertyTime == $propertyTimeData && $hourlyEnabledOrNot == 0) {
   $style = "";
   /**
    * Get the Html Style Value
    */
   if (in_array ( "$d", $speAvailArray ) && ! in_array ( "$d", $blocked )) {
    $style = "style='background-color:#65AA5F;cursor:pointer;'";
   } else {
    $style = "style='background-color:#FFFF00;cursor:pointer;'";
   }
   /**
    * Table html Value
    */
   $timeValue = 'onclick="return showHourlyCalendar(' . "'#" . $d . "_hourly_table" . "'" . ')"';
   $tableHtmlValue = $tableHtmlValue . "<td $timeValue id=" . $tdDate . " class='normal customer days " . $d . " ' align='center' " . $style . "><font size = '2' face = 'tahoma'>$d</font>";
   $tableHtmlValue = $tableHtmlValue . $partiallyBookedView [$d];
   $tableHtmlValue = $tableHtmlValue . "</td>";
  } else if (in_array ( "$d", $blocked )) {
   $tableHtmlValue = $tableHtmlValue . "<td id=" . $tdDate . " class='normal customer days " . $d . " ' align='center' style='background-color:#E07272;'><font size = '2' face = 'tahoma'>$d</font></td>";
  } else if (in_array ( "$d", $notAvail )) {
   $tableHtmlValue = $tableHtmlValue . "<td id=" . $tdDate . " class='normal customer days " . $d . " ' align='center'style='background-color:#F18200;color: black !important;' ><font size = '2' face = 'tahoma'>$d</font></td>";
  } else if (array_key_exists ( $d, $_sp )) {
   $tableHtmlValue = $tableHtmlValue . "<td style='background-color:#65AA5F;padding: 11px 23px;' id=" . $tdDate . " class='normal customer days " . $d . " ' align='center' ><font size = '2' face = 'tahoma'>$d</font><br><div style='width: 25px;font-size: 1.0em;text-align: right;'>" . Mage::app ()->getLocale ()->currency ( Mage::app ()->getStore ()->getCurrentCurrencyCode () )->getSymbol () . Mage::helper ( 'directory' )->currencyConvert ( $_sp [$d], Mage::app ()->getStore ()->getBaseCurrencyCode (), Mage::app ()->getStore ()->getCurrentCurrencyCode () ) . "</div></td>";
  } else {
   $tableHtmlValue = $tableHtmlValue . "<td id=" . $tdDate . " class='normal customer days " . $d . " ' align='center' ><font size = '2' face = 'tahoma'>$d</font></td>";
  }
  return $tableHtmlValue;
 }
 /**
  * Function Name: getTL
  *
  * @param int $styleData         
  * @param int $totaldays         
  * @return number
  */
 public function getTL($styleData, $totalDays) {
  if (($styleData >= 6 && $totalDays == 31) || ($styleData == 7 && $totalDays == 30)) {
   $total = 42;
  } else {
   $total = 35;
  }
  return $total;
 }
 
 /**
  * Function Name: getDaysForAvailDays
  * Get Days for Controller
  *
  * @param int $count         
  * @param int $value         
  * @return multitype:
  */
 public function getDaysForAvailDays($count, $value) {
  $availDay = array ();
  for($j = 0; $j < $count; $j ++) {
   $availDay [] = $value [$j] [1];
  }
  return explode ( ",", implode ( ",", $availDay ) );  
 }
 
 /**
  * Function Name: 'updateProfilePicture'
  * Update Profile Picture
  *
  * @return void boolean
  */
 public function updateProfilePicture($data) {
  $logoName = "";
  $email = Mage::getSingleton ( 'customer/session' )->getCustomer ()->getEmail ();
  $emailPart = explode ( "@", $email );
  if (isset ( $data )) {
   try {
    $localMediaPath = Mage::getBaseDir ( 'media' ) . DS;
    $documentPath = 'catalog' . DS . 'customer'. DS;
    $path = $localMediaPath . $documentPath;
    if (! file_exists ( $path )) {
     mkdir ( $path, 0777, true );
    }
    $data = str_replace ( 'data:image/png;base64,', '', $data );
    $data = str_replace ( ' ', '+', $data );
    $data = base64_decode ( $data );  
    $logoName = $emailPart [0] . uniqid () . '.png';    
     $file = $path.$logoName;
     file_put_contents($file, $data); 
   } catch ( Exception $e ) {
    Mage::getSingleton ( 'core/session' )->addError ( $e->getMessage () );
    return;
   }
  }
  $customerId = Mage::getSingleton ( 'customer/session' )->getId ();
  $result = Mage::getModel ( 'airhotels/customerphoto' )->getCollection ()->addFieldToFilter ( 'customer_id', $customerId );
  try {
   if (count ( $result ) == 0) {
    $coreResource = Mage::getSingleton ( 'core/resource' );
    $conn = $coreResource->getConnection ( 'core_read' );
    $conn->insert ( $coreResource->getTableName ( 'airhotels_customer_photo' ), array (
      'customer_id' => $customerId,
      'imagename' => $logoName 
    ) );
   } else {
    $data = array (
      'imagename' => $logoName 
    );
    $model = Mage::getModel ( 'airhotels/customerphoto' )->load ( $customerId )->addData ( $data );
    $model->setId ( $customerId )->save ();
   }
  } catch ( Exception $ex ) {
   Mage::getSingleton ( 'core/session' )->addError ( $ex->getMessage () );
   return false;
  }
  return true;
 }
 
 /**
  * Function Name: getWishList
  * Get the Wishlist collection based on the customer login
  *
  * @return array $PropertyCollection
  */
 public function getWishList() {
  $itemCollection = Mage::helper ( 'wishlist' )->getWishlistItemCollection ();
  $count = 0;
  /**
   * Iterating the loop
   */
  foreach ( $itemCollection as $item ) {
   $product = $item->getProduct ();
   $ids [$count] = $product->getId ();
   $count = 1 + $count;
  }
  /**
   * Return the airhotels property Collection
   */
  return Mage::getModel ( 'airhotels/property' )->getpropertycollection ()->addAttributeToFilter ( 'status', array (
    'eq' => 1 
  ) )->addAttributeToFilter ( 'propertyapproved', array (
    'eq' => 1 
  ) )->addAttributeToSelect ( 'url_path' )->addAttributeToFilter ( 'entity_id', array (
    'in' => $ids 
  ) );
 }
}