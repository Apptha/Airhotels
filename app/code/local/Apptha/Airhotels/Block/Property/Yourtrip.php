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
class Apptha_Airhotels_Block_Property_Yourtrip extends Mage_Catalog_Block_Product_Abstract {
    
    /**
     * FUnction Name: upcomingTrip
     * Get Upcoming trip
     *
     * @return array
     */
    public function upcomingTrip() {
        /**
         * Initialise the ' porductId', 'upcoming fromDAte', values to array
         */
        $productId = $upcomingFromdate = $upcomingTodate = $orderId = $cancelStatus = $dateTimeStatus = array ();
        /**
         * Save the details into customer info.
         */
        $customer = Mage::getSingleton ( 'customer/session' )->getCustomer ();
        $cusId = $customer->getId ();
        $todayData = Mage::getModel ( 'core/date' )->timestamp ( time () );
        $todayDate = date ( 'Y-m-d', $todayData );
        /**
         * Get collection of airhotels
         */
        $result = Mage::getModel ( 'airhotels/airhotels' )->getCollection ()->addFieldToFilter ( 'order_status', 1 )->addFieldToFilter ( 'fromdate', array (
                'gteq' => $todayDate
        ) )->addFieldToFilter ( 'customer_id', $cusId )->setOrder ( 'id', 'DESC' );        
        foreach ( $result as $res ) {
            $dayflag = 0;
            if (! empty ( $res ['checkin_time'] ) && strtotime ( $res ['fromdate'] ) == strtotime ( $todayDate )) {
                if (strtotime ( $res ['checkin_time'] ) > Mage::getModel ( 'core/date' )->timestamp ( time () )) {
                    $dayflag = 1;
                } else {
                    $dayflag = 0;
                }
            } else {
                $dayflag = 0;
            }
            /**
             * check wether the $res array not an empty and get the
             * fromdate, dateTimeStatus
             */
            if (strtotime ( $res ['fromdate'] ) > strtotime ( $todayDate )) {
                $dayflag = 1;
            }
            /**
             * check wether the $res array not an empty and get the
             * fromdate, dateTimeStatus
             */
            if ($dayflag == 1) {
                if (! empty ( $res ['checkin_time'] )) {
                    $upcomingFromdate [] = $res ['checkin_time'];
                    $dateTimeStatus [] = 1;
                } else {
                    $upcomingFromdate [] = $res ['fromdate'];
                }
                /**
                 * check weather the $res array not an empty
                 * and get the upcoming date value
                 */
                if (! empty ( $res ['checkout_time'] )) {
                    $upcomingTodate [] = $res ['checkout_time'];
                } else {
                    $upcomingTodate [] = $res ['todate'];
                }
    
                $productId [] = $res ['entity_id'];
                $orderId [] = $res ['order_id'];
                $cancelStatus [] = $res ['cancel_order_status'];
                $orderItemId [] = $res ['order_item_id'];
                $cancelRequestStatus [] = $res ['cancel_request_status'];
            }
        }
        /**
         * Return an array.
         */
        return array (
                $productId,
                $upcomingFromdate,
                $upcomingTodate,
                $orderId,
                $cancelStatus,
                $dateTimeStatus,
                $orderItemId,
                $cancelRequestStatus
        );
    }
}
