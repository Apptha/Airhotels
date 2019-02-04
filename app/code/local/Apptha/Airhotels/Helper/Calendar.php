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
 * class Apptha_Airhotels_Helper_Calendar 
 * 
 * extend Mage_Core_Helper_Url
 * @author user
 *
 */
class Apptha_Airhotels_Helper_Calendar extends Mage_Core_Helper_Url {
 /**
  * Function name: HtmlTable
  * Html Table Value
  * 
  * @var $prev_month
  * @var $prev_year
  * @var $next_month
  * @var $next_year
  * @var $productId
  * @var $date
  */
 public function HtmlTable($prev_month, $prev_year, $next_month, $next_year, $productId, $date) {
  $htmlElementValue = '';
  /**
   * Text message.
   */
  $nextTextMessage = Mage::helper ( 'airhotels' )->__ ( 'Next' );
  $previousTextMessage = Mage::helper ( 'airhotels' )->__ ( 'Previous' );
  $htmlElementValue = $htmlElementValue . '<a class="pre_grid" href="javascript:void(0);" onclick="ajaxLoadCalendar(\'' . Mage::getBaseUrl () . 'property/property/calendarview/?date=' . $prev_month . '__' . $prev_year . '&productid=' . $productId . '\')" >' . $previousTextMessage . '</a>';
  $htmlElementValue = $htmlElementValue . '<div class="date_grid">' . date ( "F, Y", $date ) . '</div>';
  $htmlElementValue = $htmlElementValue . '<a class="next_grid" href="javascript:void(0);" onclick="ajaxLoadCalendar(\'' . Mage::getBaseUrl () . 'property/property/calendarview/?date=' . $next_month . '__' . $next_year . '&productid=' . $productId . '\')" >' . $nextTextMessage . '</a>';
  /**
   * return $htmlElementValue
   */
  return $htmlElementValue . "<table border = '1' cellspacing = '0'  bordercolor='blue' cellpadding ='2' class='calend airhotels_host_calender_hourly airhotels_host_calender'>
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
  * Function Name: getPricePerVlaue
  * Get the price Per value
  */
 public function getPricePerVlaue($bookAvail, $pricePer) {
  if ($bookAvail == 3) {
   $pricePer = '1';
  }
  /**
   * return price value
   */
  return $pricePer;
 }
 /**
  * Function Name: getSimilarLocation
  * Get the similarlocation property
  *
  * @param string $city
  * @param int $productId
  * @return array $PropertyCollection
  */
 public function getSimilarLocation($city, $productId) {
  /**
   * Return the collection of airhotels property.
   */
  return Mage::getModel ( 'airhotels/property' )->getpropertycollection ()->addAttributeToSelect ( '*' )->/**
   * filter by entity id
   */
  addAttributeToFilter ( 'entity_id', array (
    'neq' => $productId
  ) )->/**
   * Filter by city
   */
  addAttributeToFilter ( 'city', array (
    'like' => $city . "%"
  ) )->/**
   * Flter by status
   */
  addFieldToFilter ( array (
    array (
      'attribute' => 'status',
      'eq' => '1'
    )
  ) )->addAttributeToFilter ( 'propertyapproved', array (
    'eq' => 1
  ) )->setPageSize ( 10 )->setOrder ( 'created_at', 'desc' );
 }
 /**
  * Function Name: propertyDataValue
  * 
  * @param Array $blockingTimesArray
  * @param String $propertyDataValue
  * @return String
  */
 public function propertyDataValue($blockingTimesArray, $propertyDataValue) {
  if ((count ( $blockingTimesArray ) > 0) && (isset ( $blockingTimesArray [0] ))) {
   $propertyDataValue = $blockingTimesArray [0];
  }
  /**
   * return $propertyDataValue
   */
  return $propertyDataValue;
 }
 /**
  * Get the property times Data
  *
  * @param String $blockingTimesValue
  * @param Time $startblockingTimes
  * @param Array $propertyTimesData
  */
 public function getPropertyTimesData($blockingTimesValue, $startblockingTimes, $propertyTimesData) {
     /**
      * Getting getPropertyTimesData
      */
  if ($blockingTimesValue == $startblockingTimes) {
   $propertyTimesData = $propertyTimesData . ',' . $blockingTimesValue;
  }
  /**
   * return $propertyTimesData
   */
  return $propertyTimesData;
 }
 /**
  * Get the property value
  *
  * @param String $blockingTimesValue
  * @param Date $startblockingTimes
  * @param String $propertyDataValue
  */
 public function getPropertyValue($blockingTimesValue, $startblockingTimes, $propertyDataValue) {
  if ($blockingTimesValue != $startblockingTimes) {
   $propertyDataValue = $propertyDataValue + 1;
  }
  /**
   * Getting $propertyDataValue
   */
  return $propertyDataValue;
 }

 /**
  * Get the Geo Ip address
  *
  * @param String $ip
  * @return boolean multitype:Ambigous unknown>
  */
 function geoCheckIP($ip) {
  /**
   * check, if the provided ip is valid
   */
  if (! filter_var ( $ip, FILTER_VALIDATE_IP )) {
   return false;
  }
  /**
   * contact ip-server
   */
  $response = @file_get_contents ( 'http://www.netip.de/search?query=' . $ip );
  if (empty ( $response )) {
   return false;
  }
  /**
   * return array()
   * @var unknown
   */
  $patterns = array ();
  $patterns ["domain"] = '#Domain: (.*?)&nbsp;#i';
  $patterns ["country"] = '#Country: (.*?)&nbsp;#i';
  $patterns ["state"] = '#State/Region: (.*?)<br#i';
  $patterns ["town"] = '#City: (.*?)<br#i';
  /**
   * Array where results will be stored
   */
  $ipInfo = array ();
  /**
   * check response from ipserver for above patterns
   */
  foreach ( $patterns as $key => $pattern ) {
   /**
    * store the result in array
    */
   $ipInfo [$key] = preg_match ( $pattern, $response, $value ) && ! empty ( $value [1] ) ? $value [1] : '';
  }
  return $ipInfo;
 }
 /**
  * Function Name: secondsToWords
  * Convert seconds to words
  *
  * @param Int $seconds
  * @return string
  */
 function secondsToWords($seconds) {
  return gmdate ( "l jS \of F Y h:i:s A", $seconds );
 }
 /**
  * Function Name: getBrowser
  * Get the Browser Value
  *
  * @param String $agent
  * @return multitype:string unknown
  */
 function getBrowser($agent) {
     /**
      * @var $bname
      * @var unknown
      */
  $u_agent = $agent;
  $bname = 'Unknown';
  $ub = 'Unknown';
  $platform = 'Unknown';
  $version = "";
  /**
   * First get the platform?
   */
  if (preg_match ( '/linux/i', $u_agent )) {
   $platform = 'linux';
  } elseif (preg_match ( '/macintosh|mac os x/i', $u_agent )) {
   $platform = 'mac';
  } elseif (preg_match ( '/windows|win32/i', $u_agent )) {
   $platform = 'windows';
  } else {
   $platform = 'unkown';
  }
  /**
   * Next get the name of the useragent yes seperately and for good reason
   */
  if (preg_match ( '/MSIE/i', $u_agent ) && ! preg_match ( '/Opera/i', $u_agent )) {
   $bname = 'Internet Explorer';
   $ub = "MSIE";
  } elseif (preg_match ( '/Firefox/i', $u_agent )) {
   $bname = 'Mozilla Firefox';
   $ub = "Firefox";
  } elseif (preg_match ( '/Chrome/i', $u_agent )) {
   $bname = 'Google Chrome';
   $ub = "Chrome";
  } elseif (preg_match ( '/Safari/i', $u_agent )) {
   $bname = 'Apple Safari';
   $ub = "Safari";
  } elseif (preg_match ( '/Opera/i', $u_agent )) {
   $bname = 'Opera';
   $ub = "Opera";
  } elseif (preg_match ( '/Netscape/i', $u_agent )) {
   $bname = 'Netscape';
   $ub = "Netscape";
  } else {
   $bname = 'Unkown';
   $ub = "Unkown";
  }
  /**
   * finally get the correct version number
   */
  $known = array (
    'Version',
    $ub,
    'other'
  );
  $pattern = '#(?<browser>' . join ( '|', $known ) . ')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
  /**
   * check if we have a number
   */
  if ($version == null || $version == "") {
   $version = "?";
  }
  /**
   * return array values
   */
  return array (
    'userAgent' => $u_agent,
    'name' => $bname,
    'version' => $version,
    'platform' => $platform,
    'pattern' => $pattern
  );
 }
 /**
  * Notofication Mesage
  *
  * @param unknown $data
  */
 public function NotificationMsg($data) {
     /**
      * save notification message to inbox
      */
  if (Mage::getModel ( 'airhotels/calendar' )->saveInbox ( $data )) {
  /**
   * Set success message.
   */
   Mage::getSingleton ( 'core/session' )->addSuccess ( $this->__ ( 'Message sent successfully' ) );
  } else {
  /**
   * Ser error message.
   */
   Mage::getSingleton ( 'core/session' )->addError ( $this->__ ( 'Message sending failed' ) );
  }
 }
 /**
  * Get the date Vlaue
  *
  * @param Date $fromDate
  * @param Date $toDate
  * @param Array $dateValue
  * @return string
  */
 public function getDateValue($fromDate, $toDate, $dateValue) {
     /**
      * check condition if $toDate is greater than $fromDate
      */
  if ($fromDate <= $toDate) {
      /**
       * Convert date to string to time
       * 
       * @var unknown
       */
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
  /**
   * return $dateValue
   */
  return $dateValue;
 }
 /**
  * Function Name: displayVideoType
  * 
  * Get Video Type
  *
  * @param String $getVideoValue
  * @param String $videoType
  * @param Array $option
  * @return unknown
  */
 public function displayVideoType($getVideoValue, $videoType, $option) {
  $displayVideoType = '';
  if ($getVideoValue == $videoType) {
   $displayVideoType = $option ['label'];
  }
  /**
   * return $displayVideoType
   */
  return $displayVideoType;
 }
 /**
  * DisplayCategory Value
  */
 public function displayCategory($getCategoryValue, $category, $option, $displayCategory) {
  if ($getCategoryValue == $category) {
   $displayCategory = $option ['label'];
  }
  /**
   * return $displayCategory
   */
  return $displayCategory;
 }
 /**
  * Function Name: validateFormServiceHours
  * 
  * Validate form service hours
  *
  * @param Int $propertyServiceFromData
  * @return Ambigous <number, unknown>
  */
 public function validateFormServiceHours($propertyServiceFromData) {
  if ($propertyServiceFromData != 12) {
   $validateFormServiceHours = $propertyServiceFromData + 12;
  } else {
   $validateFormServiceHours = $propertyServiceFromData;
  }
  /**
   * return $validateFormServiceHours
   */
  return $validateFormServiceHours;
 }
 /**
  * Calculating the service from hours
  *
  * @param Int $propertyServiceFromData
  * @return Ambigous <number, unknown>
  */
 public function calculateFormServiceHours($propertyServiceFromData) {
  if ($propertyServiceFromData != 12) {
   $validateFormServiceHours = $propertyServiceFromData;
  } else {
   $validateFormServiceHours = 0;
  }
  /**
   * return $validateFormServiceHours
   */
  return $validateFormServiceHours;
 }
 /**
  * Function Name: validateToServiceHours
  * 
  * Validate to service hours
  *
  * @param Int $propertyServiceToData
  * @return Ambigous <number, unknown>
  */
 public function validateToServiceHours($propertyServiceToData) {
  if ($propertyServiceToData != 12) {
   $validateToServiceHours = $propertyServiceToData + 12;
  } else {
   $validateToServiceHours = $propertyServiceToData;
  }
  /**
   * return $validateToServiceHours
   */
  return $validateToServiceHours;
 }
 /**
  * Calculating the to service data
  *
  * @param Int $propertyServiceToData
  */
 public function calculateToServiceHours($propertyServiceToData) {
  if ($propertyServiceToData != 12) {
   $validateToServiceHours = $propertyServiceToData;
  } else {
   $validateToServiceHours = 0;
  }
  /**
   * return $validateToServiceHours
   */
  return $validateToServiceHours;
 }
 /**
  * Function Name: propertyServiceFromTimeValue
  * property Service From Time Value
  *
  * @param Time $propertyServiceFromTimeValue
  * @return unknown
  */
 public function propertyServiceFromTimeValue($propertyServiceFromTimeValue) {
  if (isset ( $propertyServiceFromTimeValue )) {
   echo $propertyServiceFromTimeValue;
  }
  /**
   * return propertyServiceFromTimeValue
   */
  return $propertyServiceFromTimeValue;
 }
 /**
  * Function getMaximumDiscountAmount()
  * 
  * @var $amount
  * @return amount
  */
 public function getMaximumDiscountAmount() {
  $amount = Mage::getStoreConfig ( 'airhotels/invitefriends/maximum_limit' );
  if (empty ( $amount )) {
   $amount = 1000;
  }
  /**
   * return maximum discount amount
   */
  return $amount;
 }
 /**
  * Function Name: 'getwishlistpage'
  * Retrieve wishlist page url
  *
  * @return string
  */
 public function getwishlistpage() {
     /**
      * Set common wishlist Url
      */
  return $this->_getUrl ( 'wishlist/' );
 }
 /**
  * Function Name: 'getpopularpage'
  * Retrieve popular property list url
  *
  * @return string
  */
}