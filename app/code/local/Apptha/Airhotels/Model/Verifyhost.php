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
class Apptha_Airhotels_Model_Verifyhost extends Mage_Core_Model_Abstract {
    /**
     * (non-PHPdoc)
     *
     * @see Varien_Object::_construct()
     */
    public function _construct() {
       /**
        * Calling the parent Construct Method.
        */    
        parent::_construct ();
        /**
         * Initialzeing the verify host grid.
         */
        $this->_init ( 'airhotels/verifyhost' );
    }
    /**
     * Filter by host id
     *
     * @param unknown $hostId            
     */
    public function loadByHostId($hostId) {
        return $this->getCollection ()->addFieldToFilter ( 'host_id', array (
                'eq' => $hostId 
        ) );
    }
    /**
     * Filtering the tag
     *
     * @param Int $hostId            
     * @param Int $tagId            
     * @return String
     */
    public function filterTag($hostId, $tagId) {
        $verifyHost = $this->getCollection ()->addFieldToFilter ( 'tag_id', array (
                'eq' => $tagId 
        ) )->addFieldToFilter ( 'host_id', array (
                'eq' => $hostId 
        ) )->getFirstItem ()->getId ();
        if (count ( $verifyHost ) > 0) {
            return $verifyHost;
        }
    }
    
    /**
     * Remove existing id proof, when update his new id proof
     *
     * @param
     *            document root path
     * @param
     *            document file name
     */
    public function removeOldDocument($rootPath, $filePath) {
        
        /**
         * explode the existing file path, get old file name
         */
        $explode = explode ( '/', $filePath );
        $oldVideoPath = $rootPath . '/' . end ( $explode );
        unlink ( $oldVideoPath );
        
        return;
    }
    /**
     * Function set HTML view of hourly block section in calendar view
     * @param unknown $hourlyBlockArrayValue
     */
    public function hourlyBlockHtmlAction($hourlyBlockArrayValue){        
        if ($hourlyBlockArrayValue['fromCheck'] <= 11 && $hourlyBlockArrayValue['toCheck'] >= 12) {
            $flag = ( int ) Mage::getModel ( 'airhotels/airhotels' )->checkHourlyAvailableProduct ( $hourlyBlockArrayValue['productId'], $hourlyBlockArrayValue['from'], $hourlyBlockArrayValue['to'], 11, 'AM', 12, 'PM' );
            if (array_key_exists ( '11AM-12PM', $hourlyBlockArrayValue['blockedTimeBlocked'] )) {
                $html .= '<td id="11-AM_12-PM" class="normal time ' . $hourlyBlockArrayValue['incValue'] . ' hourly_booked_blocked_byhost" > 11AM - 12PM</td>';
            } elseif (array_key_exists ( '11AM-12PM', $hourlyBlockArrayValue['blockedTimeNot'] )) {
                $html .= '<td id="11-AM_12-PM" class="normal time ' . $hourlyBlockArrayValue['incValue'] . ' hourly_booked_notavail_byhost" > 11AM - 12PM</td>';
            } elseif (! $flag) {
                $html .= '<td class="hourly_fully_booked" > 11AM - 12PM</td>';
            } elseif (array_key_exists ( '11AM-12PM', $hourlyBlockArrayValue['blockedTimeSp'] )) {
                $keyValue = '11AM-12PM';
                $html .= '<td style="background-color:#65AA5F;" id="11-AM_12-PM" class="normal time ' . $hourlyBlockArrayValue['incValue'] . ' hourly_booked_sp_byhost" > 11AM - 12PM<div style="width: 25px;font-size: 1.0em;text-align: right;">' . Mage::app ()->getLocale ()->currency ( Mage::app ()->getStore ()->getCurrentCurrencyCode () )->getSymbol () . Mage::helper ( 'directory' )->currencyConvert ( $hourlyBlockArrayValue['blockedTimeSp'] [$keyValue], Mage::app ()->getStore ()->getBaseCurrencyCode (), Mage::app ()->getStore ()->getCurrentCurrencyCode () ) . '</div></td>';
            } else {
                $html .= '<td id="11-AM_12-PM" class="normal time ' . $hourlyBlockArrayValue['incValue'] . ' hourly_partially_avail" > 11AM - 12PM</td>';
            }
            /**
             * If loop ended
             */
        } else {
            $html .= '<td> 11AM - 12PM</td>';
        }
        return $html;
    }
    /**
     * Function to return hourly time check array
     * @param unknown $hourlyTimeCheckArray
     * @return string|unknown
     */
    public function hourlyTimecheck($hourlyTimeCheckArray){        
        $timeOneArray = array (12,1,2,3,4,5,6,7,8,9,10);
        $timeTwoArray = array (1,2,3,4,5,6,7,8,9,10,11);
        $flag = ( int ) Mage::getModel ( 'airhotels/airhotels' )->checkHourlyAvailableProduct ( $hourlyTimeCheckArray['productId'], $hourlyTimeCheckArray['from'], $hourlyTimeCheckArray['to'], $timeOneArray [$hourlyTimeCheckArray['inc']], 'AM', $timeTwoArray [$hourlyTimeCheckArray['inc']], 'AM' );
        if ($hourlyTimeCheckArray['fromCheck'] <= $hourlyTimeCheckArray['inc'] && $hourlyTimeCheckArray['toCheck'] > $hourlyTimeCheckArray['inc']) {
            if (array_key_exists ( $timeOneArray [$hourlyTimeCheckArray['inc']] . 'AM-' . $timeTwoArray [$hourlyTimeCheckArray['inc']] . 'AM', $hourlyTimeCheckArray['blockedTimeBlocked'] )) {
                $html .= '<td id="' . $timeOneArray [$hourlyTimeCheckArray['inc']] . '-AM_' . $timeTwoArray [$hourlyTimeCheckArray['inc']] . '-AM" class="normal time ' . $hourlyTimeCheckArray['incValue'] . ' hourly_booked_blocked_byhost">' . $timeOneArray [$hourlyTimeCheckArray['inc']] . 'AM - ' . $timeTwoArray [$hourlyTimeCheckArray['inc']] . 'AM </td>';
            } elseif (array_key_exists ( $timeOneArray [$hourlyTimeCheckArray['inc']] . 'AM-' . $timeTwoArray [$hourlyTimeCheckArray['inc']] . 'AM', $hourlyTimeCheckArray['blockedTimeNot'] )) {
                $html .= '<td id="' . $timeOneArray [$hourlyTimeCheckArray['inc']] . '-AM_' . $timeTwoArray [$hourlyTimeCheckArray['inc']] . '-AM" class="normal time ' . $hourlyTimeCheckArray['incValue'] . ' hourly_booked_notavail_byhost">' . $timeOneArray [$hourlyTimeCheckArray['inc']] . 'AM - ' . $timeTwoArray [$hourlyTimeCheckArray['inc']] . 'AM </td>';
            } elseif (array_key_exists ( $timeOneArray [$hourlyTimeCheckArray['inc']] . 'AM-' . $timeTwoArray [$hourlyTimeCheckArray['inc']] . 'AM', $hourlyTimeCheckArray['blockedTimeSp'] )) {
                $keyValue = $timeOneArray [$hourlyTimeCheckArray['inc']] . 'AM-' . $timeTwoArray [$hourlyTimeCheckArray['inc']] . 'AM';
                $html .= '<td style="background-color:#65AA5F;" id="' . $timeOneArray [$hourlyTimeCheckArray['inc']] . '-AM_' . $timeTwoArray [$hourlyTimeCheckArray['inc']] . '-AM" class="normal time ' . $hourlyTimeCheckArray['incValue'] . ' hourly_booked_sp_byhost">' . $timeOneArray [$hourlyTimeCheckArray['inc']] . 'AM - ' . $timeTwoArray [$hourlyTimeCheckArray['inc']] . 'AM <div style="width: 25px;font-size: 1.0em;text-align: right;">' . Mage::app ()->getLocale ()->currency ( Mage::app ()->getStore ()->getCurrentCurrencyCode () )->getSymbol () . Mage::helper ( 'directory' )->currencyConvert ( $hourlyTimeCheckArray['blockedTimeSp'] [$keyValue], Mage::app ()->getStore ()->getBaseCurrencyCode (), Mage::app ()->getStore ()->getCurrentCurrencyCode () ) . '</div></td>';
            } elseif (! $flag) {
                $html .= '<td class="hourly_fully_booked" >' . $timeOneArray [$hourlyTimeCheckArray['inc']] . 'AM - ' . $timeTwoArray [$hourlyTimeCheckArray['inc']] . 'AM </td>';
            } else {
                $html .= '<td id="' . $timeOneArray [$hourlyTimeCheckArray['inc']] . '-AM_' . $timeTwoArray [$hourlyTimeCheckArray['inc']] . '-AM"  class="normal time ' . $hourlyTimeCheckArray['incValue'] . ' hourly_partially_avail">' . $timeOneArray [$hourlyTimeCheckArray['inc']] . 'AM - ' . $timeTwoArray [$hourlyTimeCheckArray['inc']] . 'AM </td>';
            }
        } else {
            $html .= '<td>' . $timeOneArray [$hourlyTimeCheckArray['inc']] . 'AM - ' . $timeTwoArray [$hourlyTimeCheckArray['inc']] . 'AM </td>';
        }
        return $html;
    }
    /**
     * Function to return total hours fee
     * @param unknown $propertyServiceToDataRail
     * @param unknown $propertyServiceFromDataRail
     * @return number
     */
    public function setTotalHoursFee($propertyServiceToDataRail,$propertyServiceFromDataRail){
        $totalHours = ( int ) $propertyServiceToDataRail - $propertyServiceFromDataRail;
        if ($totalHours < 0) {
            $totalHours = 0;
        }
        return $totalHours;
    }
    /**
     * Function to return Average price per hour value
     * @param unknown $blockedTimeSp
     * @param unknown $propertyOverNightFee
     * @param unknown $propertyServiceFromDataRail
     * @param unknown $propertyServiceToDataRail
     * @param unknown $price
     * @return number
     */
    public function setAvMonthIn($blockedTimeSp,$propertyOverNightFee,$propertyServiceFromDataRail,$propertyServiceToDataRail, $price,$totalHours){
        if (! empty ( $blockedTimeSp )) {
            $av= $propertyOverNightFee + Mage::getModel ( 'airhotels/product' )->getHourlyBasedSpecialPrice ( $blockedTimeSp, $propertyServiceFromDataRail, $propertyServiceToDataRail, $price );
        } else {
            $av = $totalHours * $price + $propertyOverNightFee;
        }
        return $av;
    }
    /**
     * Function to set and get total hours for Average value
     * @param unknown $propertyServiceToRail
     * @param unknown $propertyServiceFromDataRail
     * @return number
     */
    public function getTotalHoursForAv($propertyServiceToRail,$propertyServiceFromDataRail){
        $totalHoursForAv = ( int ) $propertyServiceToRail - $propertyServiceFromDataRail;
        if ($totalHoursForAv < 0) {
            $totalHoursForAv = 0;
        }
        return $totalHoursForAv;
    }
    /**
     * Function to get the average total value for month in
     * @param unknown $blockedTimeSp
     * @param unknown $propertyOverNightFee
     * @param unknown $totalHours
     * @param unknown $price
     * @param unknown $propertyServiceFromRail
     * @param unknown $propertyServiceToDataRail
     * @return number
     */
    public function getAvMonthsIn($blockedTimeSp,$propertyOverNightFee,$totalHours,$price,$propertyServiceFromRail,$propertyServiceToDataRail){
        if (! empty ( $blockedTimeSp )) {
            $avM = $propertyOverNightFee + Mage::getModel ( 'airhotels/product' )->getHourlyBasedSpecialPrice ( $blockedTimeSp, $propertyServiceFromRail, $propertyServiceToDataRail, $price );
        } else {
            $avM = $totalHours * $price + $propertyOverNightFee;
        }
        return $avM;
    }
    /**
     * Function to get the average total value for month in
     * @param unknown $blockedTimeSp
     * @param unknown $propertyOverNightFee
     * @param unknown $totalHours
     * @param unknown $price
     * @param unknown $propertyServiceFromDataRail
     * @param unknown $propertyServiceToDataRail
     * @return number
     */
    public function AvMonthsInp($blockedTimeSp,$propertyOverNightFee,$totalHours,$price,$propertyServiceFromDataRail,$propertyServiceToDataRail){
            if (! empty ( $blockedTimeSp )) {
                $avp = $propertyOverNightFee + Mage::getModel ( 'airhotels/product' )->getHourlyBasedSpecialPrice ( $blockedTimeSp, $propertyServiceFromDataRail, $propertyServiceToDataRail, $price );
            } else {
                $avp = $totalHours * $price + $propertyOverNightFee;
            }
        return $avp;
    }
    public function partiallyHourlyTimecheck($partialTimeCheckArray){ 
        $timeOneArray = array (12,1,2,3,4,5,6,7,8,9,10);
        $timeTwoArray = array (1,2,3,4,5,6,7,8,9,10,11);
        $fromCheck = $partialTimeCheckArray['fromCheck'];
        $toCheck = $partialTimeCheckArray['toCheck'];
        $blockedTimeBlocked = $partialTimeCheckArray['blockedTimeBlocked'];
        $blockedTimeNot = $partialTimeCheckArray['blockedTimeNot'];
        $blockedTimeSp = $partialTimeCheckArray['blockedTimeSp'];
        $partialAvail = $partialTimeCheckArray['partialAvail'];
        $inc = $partialTimeCheckArray['inc'];        
        if ($fromCheck <= $inc + 12 && $toCheck > $inc + 12) {
            $flag = ( int ) Mage::getModel ( "airhotels/airhotels" )->checkHourlyAvailableProduct ( $partialTimeCheckArray['productId'], $partialTimeCheckArray['from'], $partialTimeCheckArray['to'], $timeOneArray [$inc], 'PM', $timeTwoArray [$inc], 'PM' );
            if (array_key_exists ( $timeOneArray [$inc] . 'PM-' . $timeTwoArray [$inc] . 'PM', $blockedTimeBlocked )) {
                $html .= '<td id="' . $timeOneArray [$inc] . '-PM_' . $timeTwoArray [$inc] . '-PM" class="hourly_booked_blocked_byhost" >' . $timeOneArray [$inc] . 'PM - ' . $timeTwoArray [$inc] . 'PM</td>';
            } elseif (array_key_exists ( $timeOneArray [$inc] . 'PM-' . $timeTwoArray [$inc] . 'PM', $blockedTimeNot )) {
                $html .= '<td id="' . $timeOneArray [$inc] . '-PM_' . $timeTwoArray [$inc] . '-PM" class="hourly_booked_notavail_byhost" >' . $timeOneArray [$inc] . 'PM - ' . $timeTwoArray [$inc] . 'PM</td>';
            } elseif (! $flag) {
                $html .= '<td class="hourly_fully_booked" >' . $timeOneArray [$inc] . 'PM - ' . $timeTwoArray [$inc] . 'PM</td>';
            } elseif (array_key_exists ( $timeOneArray [$inc] . 'PM-' . $timeTwoArray [$inc] . 'PM', $blockedTimeSp )) {
                $partialAvail = 1;
                $keyValue = $timeOneArray [$inc] . 'PM-' . $timeTwoArray [$inc] . 'PM';
                $html .= '<td style="background-color:#65AA5F;" id="' . $timeOneArray [$inc] . '-PM_' . $timeTwoArray [$inc] . '-PM" class="hourly_booked_sp_byhost" >' . $timeOneArray [$inc] . 'PM - ' . $timeTwoArray [$inc] . 'PM<div style="width: 25px;font-size: 1.0em;text-align: right;">' . Mage::app ()->getLocale ()->currency ( Mage::app ()->getStore ()->getCurrentCurrencyCode () )->getSymbol () . Mage::helper ( 'directory' )->currencyConvert ( $blockedTimeSp [$keyValue], Mage::app ()->getStore ()->getBaseCurrencyCode (), Mage::app ()->getStore ()->getCurrentCurrencyCode () ) . '</div></td>';
            } else {
                $partialAvail = 1;
                $html .= '<td class="hourly_partially_avail" >' . $timeOneArray [$inc] . 'PM - ' . $timeTwoArray [$inc] . 'PM</td>';
            }
        } else {            
            $html .= '<td>' . $timeOneArray [$inc] . 'PM - ' . $timeTwoArray [$inc] . 'PM</td>';
        }
        return array($html,$partialAvail);
    }
}