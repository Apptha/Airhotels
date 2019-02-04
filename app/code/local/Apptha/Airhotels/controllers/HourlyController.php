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
 * This class contains property and customer manipulation functionality
 */
class Apptha_Airhotels_HourlyController extends Mage_Core_Controller_Front_Action {
    /**
     * Function Name: indexAction
     * Airhotels Index property Action
     */
    public function indexAction() {
        /**
         * load the Layout.
         */
        $this->loadLayout ();
        /**
         * endering the layout.
         */
        $this->renderLayout ();
        /**
         * Redirect the UrI
         */
        $this->_redirectUrl ( Mage::getBaseUrl () );
    }
    /**
     * Function Name: 'hourlyBlockAction'
     * Get time wise calendar for block particular day
     */
    public function hourlyBlockAction() {
        $productId = Mage::app ()->getRequest ()->getParam ( 'productid' );
        $date = strtotime ( Mage::app ()->getRequest ()->getParam ( 'dateTime' ) );
        $propertyServiceFromTime = Mage::helper ( 'airhotels/airhotel' )->getPropertyServiceFromTimeByProductId ( $productId );
        $propertyServiceToTime = Mage::helper ( 'airhotels/airhotel' )->getPropertyServiceToTimeByProductId ( $productId );
        $propertyServiceFromArray = explode ( ":", $propertyServiceFromTime );
        $propertyServiceFromData = $propertyServiceFromArray [0];
        $propertyServiceFromPeriodData = $propertyServiceFromArray [1];
        $propertyServiceToArray = explode ( ":", $propertyServiceToTime );
        $propertyServiceToData = $propertyServiceToArray [0];
        $propertyServiceToPeriodData = $propertyServiceToArray [1];
        /**
         * Convert service time to 24 hours format
         */
        $fromCheck = Mage::getModel ( 'airhotels/airhotels' )->getRailwayTimeFormat ( $propertyServiceFromPeriodData, $propertyServiceFromData );
        $toCheck = Mage::getModel ( 'airhotels/airhotels' )->getRailwayTimeFormat ( $propertyServiceToPeriodData, $propertyServiceToData );
        $year = date ( "Y", $date );
        $month = date ( "m", $date );
        $day = date ( "d", $date );
        $blockedTimeSp = Mage::getModel ( 'airhotels/product' )->getTimewiseBookedArray ( $productId, $month, $year, $day, 1 );
        $blockedTimeBlocked = Mage::getModel ( 'airhotels/product' )->getTimewiseBookedArray ( $productId, $month, $year, $day, 2 );
        $blockedTimeNot = Mage::getModel ( 'airhotels/product' )->getTimewiseBookedArray ( $productId, $month, $year, $day, 3 );
        $from = $to = $month . '/' . $day . '/' . $year;
        $incValue = 0;
        $checkingFromDate = date ( 'Y-m-d', strtotime ( $from ) );
        $todayDateValue = Mage::getModel ( 'core/date' )->date ( 'Y-m-d' );
        $currentTimeValue = Mage::getModel ( 'core/date' )->date ( 'H' );
        $timeOneArray = array (12,1,2,3,4,5,6,7,8,9,10);
        $timeTwoArray = array (1,2,3,4,5,6,7,8,9,10,11);
        $html = '<div class="airhotels_host_calender_hourly"><a href="javascript:void(0)" onclick="closePoP()" class="close_link"></a><table border="1" cellspacing="0" bordercolor="blue" cellpadding="2" class="calend"><tboby><tr  class="blockcal"><tboby><tr>';
        for($inc = 0; $inc <= 10; $inc ++) {
            $incValue = $inc + 100;
            $fromTimeValue = Mage::getModel ( 'airhotels/airhotels' )->getRailwayTimeFormat ( 'AM', $timeOneArray [$inc] );
            if ($checkingFromDate == $todayDateValue && $currentTimeValue >= $fromTimeValue) {
                $html .= '<td>' . $timeOneArray [$inc] . 'AM - ' . $timeTwoArray [$inc] . 'AM </td>';
            } else {
                $hourlyTimeCheckArray = array('fromCheck'=>$fromCheck,'toCheck'=>$toCheck,'blockedTimeBlocked'=>$blockedTimeBlocked,'incValue'=>$incValue,'blockedTimeNot'=>$blockedTimeNot,'blockedTimeSp'=>$blockedTimeSp,'inc'=>$inc,'productId'=>$productId,'from'=>$from , 'to' => $to);
                $html .= Mage::getModel('airhotels/verifyhost')->hourlyTimecheck($hourlyTimeCheckArray);
              }
            if ($inc == 5) {
                $html .= '</tr><tr>';
            }
        }
        $incValue = $inc + 100;
        $fromTimeValue = Mage::getModel ( 'airhotels/airhotels' )->getRailwayTimeFormat ( 'AM', 11 );
        if ($checkingFromDate == $todayDateValue && $currentTimeValue >= $fromTimeValue) {
            $html .= '<td> 11AM - 12PM</td>';
        } else {            
            $hourlyBlockArrayValue = array('fromCheck'=>$fromCheck,'toCheck'=>$toCheck,'productId'=>$productId,'from'=>$from,'to'=>$to,'blockedTimeBlocked'=>$blockedTimeBlocked,'incValue'=>$incValue,'blockedTimeNot'=>$blockedTimeNot,'blockedTimeSp'=>$blockedTimeSp);
            $html .= Mage::getModel('airhotels/verifyhost')->hourlyBlockHtmlAction($hourlyBlockArrayValue);
        }
        $html .= '</tr><tr>';
        for($inc = 0; $inc <= 10; $inc ++) {
            $fromTimeValue = Mage::getModel ( 'airhotels/airhotels' )->getRailwayTimeFormat ( 'PM', $timeOneArray [$inc] );
            if ($checkingFromDate == $todayDateValue && $currentTimeValue >= $fromTimeValue) {
                $html .= '<td>' . $timeOneArray [$inc] . 'PM - ' . $timeTwoArray [$inc] . 'PM</td>';
            } else {
                if ($fromCheck <= $inc + 12 && $toCheck > $inc + 12) {
                    $flag = ( int ) Mage::getModel ( 'airhotels/airhotels' )->checkHourlyAvailableProduct ( $productId, $from, $to, $timeOneArray [$inc], 'PM', $timeTwoArray [$inc], 'PM' );
                    $incValue = $inc + 200;
                    if (array_key_exists ( $timeOneArray [$inc] . 'PM-' . $timeTwoArray [$inc] . 'PM', $blockedTimeBlocked )) {
                        $html .= '<td id="' . $timeOneArray [$inc] . '-PM_' . $timeTwoArray [$inc] . '-PM" class="normal time ' . $incValue . ' hourly_booked_blocked_byhost" >' . $timeOneArray [$inc] . 'PM - ' . $timeTwoArray [$inc] . 'PM</td>';
                    } elseif (array_key_exists ( $timeOneArray [$inc] . 'PM-' . $timeTwoArray [$inc] . 'PM', $blockedTimeNot )) {
                        $html .= '<td id="' . $timeOneArray [$inc] . '-PM_' . $timeTwoArray [$inc] . '-PM" class="normal time ' . $incValue . ' hourly_booked_notavail_byhost" >' . $timeOneArray [$inc] . 'PM - ' . $timeTwoArray [$inc] . 'PM</td>';
                    } elseif (! $flag) {
                        $html .= '<td class="hourly_fully_booked" >' . $timeOneArray [$inc] . 'PM - ' . $timeTwoArray [$inc] . 'PM</td>';
                    } elseif (array_key_exists ( $timeOneArray [$inc] . 'PM-' . $timeTwoArray [$inc] . 'PM', $blockedTimeSp )) {
                        $keyValue = $timeOneArray [$inc] . 'PM-' . $timeTwoArray [$inc] . 'PM';
                        $html .= '<td style="background-color:#65AA5F;" id="' . $timeOneArray [$inc] . '-PM_' . $timeTwoArray [$inc] . '-PM" class="normal time ' . $incValue . ' hourly_booked_sp_byhost" >' . $timeOneArray [$inc] . 'PM - ' . $timeTwoArray [$inc] . 'PM<div style="width: 25px;font-size: 1.0em;text-align: right;">' . Mage::app ()->getLocale ()->currency ( Mage::app ()->getStore ()->getCurrentCurrencyCode () )->getSymbol () . Mage::helper ( 'directory' )->currencyConvert ( $blockedTimeSp [$keyValue], Mage::app ()->getStore ()->getBaseCurrencyCode (), Mage::app ()->getStore ()->getCurrentCurrencyCode () ) . '</div></td>';
                    } else {
                        /**
                         * Setting partially available
                         */
                        $html .= '<td id="' . $timeOneArray [$inc] . '-PM_' . $timeTwoArray [$inc] . '-PM" class="normal time ' . $incValue . ' hourly_partially_avail" >' . $timeOneArray [$inc] . 'PM - ' . $timeTwoArray [$inc] . 'PM</td>';
                    }
                } else {
                    /**
                     * Setting PM/AM to hourly booking
                     */
                    $html .= '<td>' . $timeOneArray [$inc] . 'PM - ' . $timeTwoArray [$inc] . 'PM</td>';
                }
            }
            if ($inc == 5) {
                $html .= '</tr><tr>';
            }
        }
        $incValue = $inc + 100;
        $fromTimeValue = Mage::getModel ( 'airhotels/airhotels' )->getRailwayTimeFormat ( 'PM', 11 );
        if ($checkingFromDate == $todayDateValue && $currentTimeValue >= $fromTimeValue) {
            $html .= '<td> 11PM - 12AM</td>';
        } else {
            $status = false;
            if ($fromCheck <= 23 && $toCheck >= 0 && $status) {
                $flag = ( int ) Mage::getModel ( 'airhotels/airhotels' )->checkHourlyAvailableProduct ( $productId, $from, $to, 11, 'PM', 12, 'AM' );
                if (array_key_exists ( '11PM-12AM', $blockedTimeBlocked )) {
                    $html .= '<td id="11-PM_12-AM" class="normal time ' . $incValue . ' hourly_booked_blocked_byhost" > 11PM - 12AM</td>';
                } elseif (array_key_exists ( '11PM-12AM', $blockedTimeNot )) {
                    $html .= '<td id="11-PM_12-AM" class="normal time ' . $incValue . ' hourly_booked_notavail_byhost" > 11PM - 12AM</td>';
                } elseif (! $flag) {
                    $html .= '<td class="hourly_fully_booked" > 11PM - 12AM</td>';
                } elseif (array_key_exists ( '11PM-12AM', $blockedTimeSp )) {
                    $keyValue = '11PM-12AM';
                    $html .= '<td id="11-PM_12-AM" style="background-color:#65AA5F;" class="normal time ' . $incValue . ' hourly_booked_sp_byhost" > 11PM - 12AM<div style="width: 25px;font-size: 1.0em;text-align: right;">' . Mage::app ()->getLocale ()->currency ( Mage::app ()->getStore ()->getCurrentCurrencyCode () )->getSymbol () . Mage::helper ( 'directory' )->currencyConvert ( $blockedTimeSp [$keyValue], Mage::app ()->getStore ()->getBaseCurrencyCode (), Mage::app ()->getStore ()->getCurrentCurrencyCode () ) . '</div></td>';
                } else {
                    $html .= '<td id="11-PM_12-AM" class="normal time ' . $incValue . ' hourly_partially_avail" > 11PM - 12AM</td>';
                }
            } else {
                $html .= '<td> 11PM - 12AM</td>';
            }
        }
        $html .= '</tr></tboby></table></div>';
        $this->getResponse ()->setBody ( $html );
    }
     /**
     * Function Name: maploadAction
     * Display map for propertys
     */
    public function maploadAction() {
        /**
         * Error Message.
         */
        $errorMessage = Mage::helper ( 'airhotels' )->__ ( 'Address was not found in google map' );
        /**
         * Html Vlaue.
         */
        $html = $lat = $long = $address = '';
        /**
         * get the Page Value.
         */
        $page = $this->getRequest ()->getParam ( 'address' );
        /**
         * Set the PreAddr Value.
         */
        $prepAddr = str_replace ( ' ', '+', $page );
        $geoCode = '';
        /**
         * Get the contents form the fun using 'file_get_contents'
         */
        if (ini_get ( 'allow_url_fopen' )) {
            $geoCode = file_get_contents ( 'http://maps.google.com/maps/api/geocode/json?address=' . $prepAddr . '&sensor=false' );
        } else {
            /**
             * Get map function based on curl.
             */
            $ch = curl_init ();
            curl_setopt ( $ch, CURLOPT_URL, 'http://maps.google.com/maps/api/geocode/json?address=' . $prepAddr . '&sensor=false' );
            curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
            /**
             * Execute the curl function.
             */
            $geoCode = curl_exec ( $ch );
        }
        /**
         * Output Vlaue.
         */
        $output = json_decode ( $geoCode );
        /**
         * Check the vlaue for output array
         */
        if (isset ( $output->results [0] )) {
            $lat = $output->results [0]->geometry->location->lat;
        }
        if (isset ( $output->results [0] )) {
            $long = $output->results [0]->geometry->location->lng;
        }
        if (isset ( $output->results [0] )) {
            $address = $output->results [0]->formatted_address;
        }
        if (($lat != "") && ($long != "")) {
            /**
             * Set the Vlaue of 'latitude' and 'longitude' value.
             */
            $latlongitude = $lat . ',' . $long;
            /**
             * add the html Values
             */
            $html = $html . '<input type="hidden" id="latlongitude" name="latlongitude" value= "' . $latlongitude . '">';
            $html = $html . '<input type="hidden" name="lat" id="lat" value="' . $lat . '" >';
            $html = $html . '<input type="hidden" name="long" id="long" value="' . $long . '" >';
        } else {
            /**
             * add the html Values
             */
            $html = $html . '<input type="hidden" id="latlongitude" name="latlongitude" value= "">';
            $html = $html . '<input type="hidden" id="lat" value="0" >';
            $html = $html . '<input type="hidden" id="long" value="0" >';
            $html = $html . "<span style='color:#F00'>$errorMessage<span>";
        }
        /**
         * Send the html values
         */
        $this->getResponse ()->setBody ( $html );
    }
    /**
     * Function Name: contactAction
     * Cantact page Action
     */
    public function contactAction() {
        /**
         * Load the Layout.
         */
        $this->loadLayout ();
        $this->_initLayoutMessages ( 'catalog/session' );
        /**
         * Check logged in or not
         */
        if (! Mage::getSingleton ( 'customer/session' )->isLoggedIn ()) {
            /**
             * Set message.
             */
            $messageValue = $this->__ ( 'You are not currently logged in' );
            $this->getResponse ()->setBody ( $messageValue );
        } else {
            /**
             * Load and rendering the layout.
             */
            $this->loadLayout ();
            /**
             * Rendering the Layout..
             */
            $this->renderLayout ();
        }
    }
    /**
     * Function Name: 'reviewstatusAction'
     * Review Status for Properties
     */
    public function reviewstatusAction() {
        /**
         * get the status of review
         * get review id.
         */
        $statusValue = $this->getRequest ()->getParam ( 'status' );
        $reviewId = Mage::app ()->getRequest ()->getParam ( 'reviewid' );
        /**
         * Get status based on review.
         */
        $status = Mage::getModel ( 'airhotels/airhotels' )->review ( $statusValue, $reviewId );
        /**
         * check review status value
         * and set message
         */
        if ($status == "2") {
            $this->getResponse ()->setBody ( "Available" );
        } else {
            $this->getResponse ()->setBody ( "NotAvailable" );
        }
    }
}