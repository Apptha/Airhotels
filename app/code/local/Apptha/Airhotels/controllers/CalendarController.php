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
 * This class contains calendar import and export functionality
 *
 * Import ics url calendar datas to Airhotels host calendar
 * Generate ics file for external calendar
 */
class Apptha_Airhotels_CalendarController extends Mage_Core_Controller_Front_Action {
 
 /**
  *
  *
  * To import external ics file url data to Airhotels calendar
  *
  * @param string $url
  *         external ics file url
  *         
  */
 public function importExternalCalendarAction() {
  /**
   * Get the params Value
   * 
   * @var $url. 
   */ 
  $url = trim ( Mage::app ()->getRequest ()->getParam ( 'external_calendar_ics_file_url' ) );
  /**
   * Product Id Vlaue.
   */
  $productId = Mage::app ()->getRequest ()->getParam ( 'property_id' );
  /**
   * Get the Status Vlaue.
   */
  $status = Mage::app ()->getRequest ()->getParam ( 'auto_ics_sync_value' );
  /**
   * Get the Vlaue of icalString
   */
  $icalString = Mage::getModel ( 'airhotels/calendarsync' )->readIcsUrl ( $url );
  /**
   * get the Vlaues of '$icalString'
   */
  $icsDates = Mage::getModel ( 'airhotels/calendarsync' )->convertIcsStringToArray ( $icalString );
  /**
   * Call Model for function 'importFromGoogleIcsUrl'
   */
  Mage::getModel ( 'airhotels/calendarsync' )->importFromGoogleIcsUrl ( $icalString, $icsDates, $productId );
  /**
   * load product by product id
   */
  $product = Mage::getModel ( "catalog/product" )->load ( $productId );
  if (! isset ( $url )) {
   $url = '';
  }
  $value = '';
  /**
   * Check the Vlaue of 'status ' one.
   */
  if ($status == 1) {
   /**
    * Get the auto_ics_sync Attribute value
    */
   $attribute = Mage::getModel ( 'eav/config' )->getAttribute ( 'catalog_product', 'auto_ics_sync' );
   /**
    * Iterating the loop
    */
   foreach ( $attribute->getSource ()->getAllOptions ( true, true ) as $option ) {
    if (trim ( $option ['label'] ) == 'Yes') {
     $value = $option ['value'];
     break;
    }
   }
  } else {
   /**
    * Get the auto_ics_sync Attribute value
    */
   $attribute = Mage::getModel ( 'eav/config' )->getAttribute ( 'catalog_product', 'auto_ics_sync' );
   /**
    * Iterating the loop.
    */
   foreach ( $attribute->getSource ()->getAllOptions ( true, true ) as $option ) {
    if (trim ( $option ['label'] ) == 'No') {
     $value = $option ['value'];
     break;
    }
   }
  }
  /**
   * Update product details
   */
  $product->setGoogleCalendarIcsUrl ( $url );
  /**
   * Set the Product
   */
  $product->setAutoIcsSync ( $value );
  try {
   /**
    * Get the current Store ID
    */
   $CurrentStoreId = Mage::app ()->getStore ()->getId ();
   /**
    * Set current store
    */
   Mage::app ()->setCurrentStore ( Mage_Core_Model_App::ADMIN_STORE_ID );
   /**
    * Save data
    */
   $product->save ();
   Mage::app ()->setCurrentStore ( $CurrentStoreId );
  } catch ( Exception $e ) {
   /**
    * Adding error message
    */
   Mage::getSingleton ( 'core/session' )->addError ( "Error :" . $e->getMessage () );
   return false;
  }
  /**
   * Redirect the url
   */
  $redirectedUrl = Mage::getBaseUrl () . "property/property/blockcalendar/id/$productId";
  /**
   * Send the response to '$redirectedUrl'
   */
  Mage::app ()->getResponse ()->setRedirect ( $redirectedUrl );
  return;
 }
 
 /**
  * Function Name: 'exportExternalCalendarAction'
  * To export ics file for external resource
  */
 public function exportExternalCalendarAction() {
  /**
   * Get the param of 'property_name'
   */
  $proudctName = trim ( Mage::app ()->getRequest ()->getParam ( 'property_name' ) );
  /**
   * Product Id Value.
   */
  $productId = Mage::app ()->getRequest ()->getParam ( 'property_id' );
  /**
   * Call the Method Vlaue.
   */
  $this->generateIcsFileByProductId ( $proudctName, $productId );
  return;
 }
 
 /**
  * Function Name: 'generateIcsFileByProductId'
  * Generate ics file for product
  *
  * @param type $productId         
  */
 function generateIcsFileByProductId($proudctName, $productId) {
  $currentDate = date ( "Y-m-d", Mage::getModel ( 'core/date' )->timestamp ( time () ) );
  /**
   * Get the collection of airhotels calendar
   * 'book_avail'
   * 'created'
   * 'product_id'
   */
  $calendarCollections = Mage::getModel ( 'airhotels/calendar' )->getcollection ()->/**
   * Filter by book avail and product id
   */
  addFieldToFilter ( 'book_avail', array ('2','3') )->addFieldToFilter ( 'created', array (
    'from' => $currentDate ) )->addFieldToFilter ( 'product_id', $productId );
  /**
   * set the Product name.
   */
  $fileName = $proudctName . '.ics';
  /**
   * Get the Base url
   */
  $webUrl = Mage::getBaseUrl ( Mage_Core_Model_Store::URL_TYPE_WEB );
  $domainArray = explode ( "/", $webUrl );
  $domain = $domainArray [2];
  /**
   * header informations are set
   */
  $domainName = str_replace ( "www.", "", $domain );
  /**
   * header Infos.
   */
  header ( 'Content-type: text/calendar; charset=utf-8' );
  header ( 'Content-Disposition: attachment; filename=' . $fileName );
  $calIcs = '';
  $calIcs .= "BEGIN:VCALENDAR\nVERSION:2.0\nPRODID:-//$domainName//Hosting//CALENDAR//EN\nCALSCALE:GREGORIAN\nMETHOD:PUBLISH\n";
  /**
   * Loop over blocked and not available dates
   */
  foreach ( $calendarCollections as $calendarRow ) {
   $calUid = $calendarRow ['id'];
   if ($calendarRow ['id'] == 2) {
    $calDescription = 'Blocked';
   } else {
    $calDescription = 'Not Available';
   }
   /**
    * Calender Month Vlaue.
    */
   $calMonth = $calendarRow ['month'];
   $calYear = $calendarRow ['year'];
   /**
    * Calender Days Value.
    */
   $calDays = $calendarRow ['blockfrom'];
   /**
    * Calender Day Value.
    */
   $calDay = explode ( ",", $calDays );
   /**
    * Claculate the DayInetrvAl Value.
    */
   $dayInterval = count ( $calDay );
   /**
    * Calendar Start Date Vlaue.
    */
   $calStartDate = $calDay [0];
   /**
    * Calendar Start Date Vlaue.
    */
   $calEndDate = $calDay [$dayInterval - 1];
   /**
    * Calendar Start Time Vlaue.
    */
   $calStartTime = mktime ( 0, 0, 0, $calMonth, $calStartDate, $calYear );
   /**
    * Calendar End Time Vlaue.
    */
   $calEndTime = mktime ( 0, 0, 0, $calMonth, $calEndDate, $calYear );
   /**
    * Calendar End Date.
    */
   $calEnd = date ( 'Y-m-d', $calEndTime );
   /**
    * Calendar Summary.
    */
   $calSummary = 'Busy';
   /**
    * claendar ICSVlaue.
    */
   $calIcs .= "BEGIN:VEVENT\n";
   $calIcs .= 'DTSTART;VALUE=DATE:' . date ( 'Ymd', $calStartTime ) . "\n";
   $calIcs .= 'DTEND;VALUE=DATE:' . date ( 'Ymd', strtotime ( $calEnd . " +1 day" ) ) . "\n";
   $calIcs .= 'DTSTAMP:' . date ( 'Ymd' ) . 'T' . date ( 'His' ) . "Z" . "\n";
   $calIcs .= 'SUMMARY:' . $calSummary . "\n";
   $calIcs .= 'DESCRIPTION:' . $calDescription . "\n";
   $calIcs .= 'UID:' . $calUid . $domainName . "\n";
   $calIcs .= 'END:VEVENT' . "\n";
  }
  $calIcs .= 'END:VCALENDAR' . "\n";
  /**
   * setting the ICS value to body
   */
  $this->getResponse ()->setBody ( $calIcs );
 }
 
 /**
  * Function Name: removeExternalCalendarAction
  * Remove external ics file url from product
  */
 public function removeExternalCalendarAction() {
  /**
   * get the property_id Value
   */
  $productId = Mage::app ()->getRequest ()->getParam ( 'property_id' );
  /**
   * load product by product id
   */
  $product = Mage::getModel ( "catalog/product" )->load ( $productId );
  /**
   * Update product details
   */
  $product->setGoogleCalendarIcsUrl ( '' );
  /**
   * Call the product 'setAutoIcsSync' Method.
   */
  $product->setAutoIcsSync ( '' );
  try {
   $CurrentStoreId = Mage::app ()->getStore ()->getId ();
   /**
    * Getting store id
    */
   Mage::app ()->setCurrentStore ( Mage_Core_Model_App::ADMIN_STORE_ID );
   /**
    * Save the value
    */
   $product->save ();
   /**
    * set the current store Vaslue
    */
   Mage::app ()->setCurrentStore ( $CurrentStoreId );
  } catch ( Exception $e ) {
   /**
    * Set error message.
    */
   Mage::getSingleton ( 'core/session' )->addError ( "Error :" . $e->getMessage () );
   return false;
  }
  /**
   * Adding the success notofication
   */
  Mage::getSingleton ( 'core/session' )->addSuccess ( "Ics Link has been removed successfully" );
  $redirectedUrl = Mage::getBaseUrl () . "property/property/blockcalendar/id/$productId";
  /**
   * Redirect url
   */
  Mage::app ()->getResponse ()->setRedirect ( $redirectedUrl );
  return;
 }
 /**
  * Function Name: searchresultAction
  * search Result for properties
  */
 public function searchresultAction() {
 /**
  * Load layout, render layout.
  */
  $this->loadLayout ();
  $this->renderLayout ();
 }
}