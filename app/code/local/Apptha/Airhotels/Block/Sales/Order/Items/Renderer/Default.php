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
class Apptha_Airhotels_Block_Sales_Order_Items_Renderer_Default extends Mage_Sales_Block_Order_Item_Renderer_Default {
    /**
     * To show the check in, check out , service fee and Accomodates details
     * in the order mail and admin side view order
     *
     *
     * @return array $result
     */
    public function getItemOptions() {
        $result = array ();
        /**
         * Getting currency and symbol
         */
        /**
         * Check whether propety has options or not
         */
        if ($this->getOrderItem ()->getProductOptions ()) {
            /**
             * Getting Product Details
             */
            $productOptions = $this->getOrderItem ()->getProductOptions ();
            /**
             * Iterating Loop
             */
            foreach ( $productOptions as $options1 ) {
                /**
                 * Get check in
                 */
                $checkIn = $options1 ['fromdate'];
                /**
                 * Get check out
                 */
                $checkOut = $options1 ['todate'];
                /**
                 * Get no fo guest
                 */
                $rooms = $options1 ['accomodate'];
                /**
                 * Get service fee
                 */
                if (isset ( $options1 ['property_service_from'], $options1 ['per_hour_night_fee'], $options1 ['property_service_from_period'], $options1 ['property_service_to'] ) && isset ( $options1 ['property_service_to_period'], $options1 ['overall_total_hours'], $options1 ['hourly_night_fee'] )) {
                    /**
                     * Getting PROPEPRTY SERVICE FROM
                     */
                    $propertyServiceFrom = $options1 ['property_service_from'];
                    /**
                     * GETTING AM/PM
                     */
                    $propertyServiceFromPeriod = $options1 ['property_service_from_period'];
                    /**
                     * Getting Property To time
                     */
                    $propertyServiceTo = $options1 ['property_service_to'];
                    /**
                     * Getting Property AM/PM
                     */
                    $propertyServiceToPeriod = $options1 ['property_service_to_period'];
                    /**
                     * Getting overall total hours
                     */
                    $overallTotalHours = $options1 ['overall_total_hours'];
                    $perHourNightFee = '';
                    /**
                     * Getting Per Night Fee
                     */
                    $perHourNightFee = $options1 ['per_hour_night_fee'];
                    $overallTotalHours = Mage::helper ( 'airhotels/airhotel' )->overallTotalHours ( $perHourNightFee, $overallHours );
                }
                break;
            }
            
            if (isset ( $propertyServiceFrom, $propertyServiceFromPeriod ) && isset ( $propertyServiceTo, $propertyServiceToPeriod )) {
                $result = $this->perHourNightFeeGreater ( $perHourNightFee, $propertyServiceFrom, $propertyServiceTo, $propertyServiceFromPeriod, $propertyServiceToPeriod, $overallTotalHours, $productOptions );
            } else {
                /**
                 * Daily property
                 */
                $result = array (
                        array (
                                'label' => $this->__ ( 'Check In' ),
                                'value' => str_replace ( '@', '/', $checkIn ) 
                        ),
                        array (
                                'label' => $this->__ ( 'Check Out' ),
                                'value' => str_replace ( '@', '/', $checkOut ) 
                        ),
                        array (
                                'label' => $this->__ ( 'Accomodate(s)' ),
                                'value' => $rooms 
                        ) 
                );
            }
        }
        return $result;
    }
    
    /**
     * check whether the Per night Fee is Greater
     *
     * @param number $perHourNightFee            
     * @param date $checkIn            
     * @param date $checkOut            
     * @param date $propertyServiceFrom            
     * @param date $propertyServiceTo            
     * @param date $propertyServiceFromPeriod            
     * @param date $propertyServiceToPeriod            
     * @param number $rooms            
     * @param number $overallTotalHours            
     * @param symbol $currencySymbol            
     * @param number $perHourNightFee            
     * @param number $guests            
     * @return Ambigous <multitype:multitype:string multitype:unknown string , multitype:multitype:string multitype:unknown string multitype:string Ambigous <string, string, multitype:> >
     */
    public function perHourNightFeeGreater($perHourNightFee, $propertyServiceFrom, $propertyServiceTo, $propertyServiceFromPeriod, $propertyServiceToPeriod, $overallTotalHours, $productOptions) {
        $varPropertyServiceTo = $propertyServiceTo;
        foreach ( $productOptions as $options1 ) {
            /**
             * Getting From date
             */
            $checkIn = $options1 ['fromdate'];
            /**
             * Getting TODATE
             */
            $checkOut = $options1 ['todate'];
            /**
             * Getting Nuber of accomodates
             */
            $rooms = $options1 ['accomodate'];
            /**
             * Getting service fee
             */
            $guests = $options1 ['serviceFee'];
        }
        /**
         * Getting currency symbol
         */
        $currencySymbol = Mage::app ()->getLocale ()->currency ( Mage::app ()->getStore ()->getBaseCurrencyCode () )->getSymbol ();
        $result = array (
                /**
                 * Set check in labels and values
                */
                array (
                        'label' => $this->__ ( 'Check In' ),
                        'value' => str_replace ( '@', '/', $checkIn ) . '  -  ' . $propertyServiceFrom . ' ' . $propertyServiceFromPeriod
                ),
                /**
                 * Set check out labels and values
                */
                array (
                        'label' => $this->__ ( 'Check Out' ),
                        'value' => str_replace ( '@', '/', $checkOut ) . '  -  ' . $varPropertyServiceTo . ' ' . $propertyServiceToPeriod
                ),
                /**
                 * Set Accomodate(s) labels and values
                */
                array (
                        'label' => $this->__ ( 'Accomodate(s)' ),
                        'value' => $rooms
                ),
                /**
                 * Set No. of hour(s) labels and values
                */
                array (
                        'label' => $this->__ ( 'No. of hour(s)' ),
                        'value' => $overallTotalHours
                ),
                /**
                 * Set Processing Fee labels and values
                 */
                array (
                        'label' => $this->__ ( 'Processing Fee' ),
                        'value' => $currencySymbol . $guests
                )                
         );
        if ($perHourNightFee >= 1) {
            /**
             * Hourly property with overnight fee
             */
            /**
             * Set Overnight Fee (per night) labels and values
             */
             array_push($result,array (
                            'label' => $this->__ ( 'Overnight Fee (per night)' ),
                            'value' => $currencySymbol . $perHourNightFee 
                    )
              );             
        } 
        return $result;
    }
}