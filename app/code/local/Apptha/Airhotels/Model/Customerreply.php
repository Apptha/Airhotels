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
class Apptha_Airhotels_Model_Customerreply extends Mage_Core_Model_Abstract {
    /**
     * Function Name: 'construct'
     * Construct Method
     *
     * @see Varien_Object::_construct()
     */
    public function _construct() {
        parent::_construct ();
        $this->_init ( 'airhotels/customerreply' );
    }
    
    /**
     * Function Name : getIncrementVal
     * Get the Incremenental Value
     *
     * @param string $blockedArrayValue            
     * @param array $mergedBlockedArray            
     * @return number
     */
    public function getIncrementVal($blockedArrayValue, $mergedBlockedArray) {
        $Incr = 0;
        /**
         * Check the value exist in mergedBlockedArray
         */
        if (array_key_exists ( $blockedArrayValue, $mergedBlockedArray )) {
            return $Incr + 1;
        }
    }
    /**
     * Function Name: checkFirstDate
     * Get the Count Incremenental Value
     *
     * @param array $mergedBlockedArray            
     * @return number
     */
    public function countIncrementVal($mergedBlockedArray) {
        $Incr = 0;
        /**
         * Count the $mergedBlockedArray
         */
        if (count ( $mergedBlockedArray ) >= 1) {
            return $Incr + 1;
        }
    }
    /**
     * Function Name: checkFirstDate
     * Check the First Date
     *
     * @param int $blockedTimeSp            
     * @param int $propertyOverNightFee            
     * @param float $hourlyBasedSpecialPrice            
     * @param float $price            
     * @param float $totalHours            
     * @param int $month            
     * @param int $pIn            
     * @return Ambigous <multitype:, number>
     */
    public function checkFirstDate($blockedTimeSp, $propertyOverNightFee, $hourlyBasedSpecialPrice, $price, $totalHours, $month, $pIn) {
        $available = array ();
        /**
         * Make sure the $blockedTimeSp is not an empty
         */
        if (! empty ( $blockedTimeSp )) {
            $available [$month] [$pIn] = $propertyOverNightFee + $hourlyBasedSpecialPrice;
        } else {
            $available [$month] [$pIn] = $totalHours * $price + $propertyOverNightFee;
        }
        return $available;
    }
    
    /**
     * Check the Second Date
     *
     * @param int $blockedTimeSp            
     * @param float $hourlyBasedSpecialPrice            
     * @param int $month            
     * @param int $pIn            
     * @param int $totalHours            
     * @param float $price            
     * @return Ambigous <multitype:, number, unknown>
     */
    public function checkSecondDate($blockedTimeSp, $hourlyBasedSpecialPrice, $month, $pIn, $totalHours, $price) {
        $available = array ();
        /**
         * Check the Value of $blockedTimeSp
         */
        if (! empty ( $blockedTimeSp )) {
            $available [$month] [$pIn] = $hourlyBasedSpecialPrice;
        } else {
            $available [$month] [$pIn] = $totalHours * $price;
        }
        return $available;
    }
    
    /**
     * Delete the table columns of 'airhotels_calendar'
     *
     * @param number $productId            
     * @param int $month            
     * @param int $year            
     */
    public function deleteTableName($productId, $month, $year) {
        $coreResource = Mage::getSingleton ( 'core/resource' );
        $connection = $coreResource->getConnection ( 'core_read' );
        $blockCalendartable = 'airhotels_calendar';
        /**
         * check the values are set
         */
        if (isset ( $productId ) && isset ( $month ) && isset ( $year )) {
            $blockfromValue = '';
            $connection->delete ( $coreResource->getTableName ( $blockCalendartable ), array (
                    'product_id = ? ' => $productId,
                    'month = ? ' => $month,
                    'year = ? ' => $year,
                    'blockfrom = ? ' => $blockfromValue 
            ) );
        }
    }
    /**
     * Get the event Starts Date
     *
     * @param array $icsEvent            
     * @return Ambigous <multitype:, multitype:string >
     */
    public function eventStartsDate($icsEvent) {
        $eventStartsDate = array ();
        /**
         * check the icsEvent values are empty
         */
        if (empty ( $icsEvent ['DTSTART;VALUE=DATE'] ) && empty ( $icsEvent ['DTEND;VALUE=DATE'] )) {
            $eventStartsTimestamp = $icsEvent ['DTSTART'];
            $eventStartsDate = explode ( "T", $eventStartsTimestamp );
        } else {
            $eventStartsDate [] = trim ( $icsEvent ['DTSTART;VALUE=DATE'] );
        }
        return $eventStartsDate;
    }
    /**
     * Get the event Ends Date
     *
     * @param array $icsEvent            
     * @return Ambigous <multitype:, multitype:string >
     */
    public function eventEndsDate($icsEvent) {
        $eventEndsDate = array ();
        /**
         * check the icsEvent values are empty
         */
        if (empty ( $icsEvent ['DTSTART;VALUE=DATE'] ) && empty ( $icsEvent ['DTEND;VALUE=DATE'] )) {
            $eventEndsTimestamp = $icsEvent ['DTSTART'];
            $eventEndsDate = explode ( "T", $eventEndsTimestamp );
        } else {
            $eventEndsDate [] = trim ( $icsEvent ['DTEND;VALUE=DATE'] );
        }
        return $eventEndsDate;
    }
    
    /**
     * Check has Discount Available
     *
     * @param array $item            
     * @param array $eventArgs            
     * @return multitype:NULL
     */
    public function hasDiscount($item, $eventArgs) {
        $discount = array ();
        foreach ( $item->getChildren () as $child ) {
            $eventArgs ['item'] = $child;
            Mage::dispatchEvent ( 'sales_quote_address_discount_item', $eventArgs );
            
            /**
             * Parent free shipping we apply to all children
             */
            if ($item->getFreeShipping ()) {
                $child->setFreeShipping ( $item->getFreeShipping () );
            }
            /**
             * Calculate total discount amount
             * calculate base total discount amount.
             */
            $discount ['totalDiscountAmount'] += $child->getDiscountAmount ();
            $discount ['baseTotalDiscountAmount'] += $child->getBaseDiscountAmount ();
            $child->setRowTotalWithDiscount ( $child->getRowTotal () - $child->getDiscountAmount () );
            $child->setBaseRowTotalWithDiscount ( $child->getBaseRowTotal () - $child->getBaseDiscountAmount () );
            /**
             * Calculate subtotal with discount
             * Calculate base subtotal with discount.
             */
            $discount ['subtotalWithDiscount'] += $child->getRowTotalWithDiscount ();
            $discount ['baseSubtotalWithDiscount'] += $child->getBaseRowTotalWithDiscount ();
        }
        return $discount;
    }
    /**
     * function to search property
     */
    public function advanceSearch($data) {
        /**
         * Initilizing price for filter
         */
        $amount = explode ( "-", $data ["amount"] );
        $minval = $amount [0];
        $maxval = $amount [1];                
        /**
         * Filter by booking enable
         */       
        $copycollection = Mage::getModel ( 'catalog/product' )->getCollection ()->addAttributeToSelect ( '*' )->addFieldToFilter ( array (
                array (
                        'attribute' => 'status',
                        'eq' => '1' 
                ) 
        ) )->addFieldToFilter ( array (
                array (
                        'attribute' => 'propertyapproved',
                        'eq' => '1' 
                ) 
        ) );        
        /**
         * Filter by price
         */       
        if (Mage::getStoreConfig ( 'airhotels/max_min/price_select' ) == 0) {
            $copycollection->setOrder ( 'price', 'asc' );
        } else {
            $copycollection->setOrder ( 'price', 'desc' );
        }       
        $copycollection->addFieldToFilter ( 'price', array (
                'gteq' => $minval 
        ) );        
        $copycollection->addFieldToFilter ( 'price', array (
                'lteq' => $maxval 
        ) );               
        /**
         * Filter by city, state, country value from address value
         */
        $copycollection = Mage::getModel('airhotels/status')->roomTypeFilter($data,$copycollection);               
        $copycollection = Mage::getModel('airhotels/status')->searchResult($data,$copycollection);         
        if ($data ["amenityVal"] != '') {
            /**
             * Filter by amenity
             */
            $amenityString = $data ["amenityVal"];
            $amenityArray = explode ( ",", $amenityString );
            if (count ( $amenityArray ) >= 1) {
                foreach ( $amenityArray as $amenity ) {                    
                    $copycollection->addFieldToFilter ( array (
                            array (
                                    'attribute' => 'amenity',
                                    'like' => "%$amenity%" 
                            ) 
                    ) );
                }
            } else {                
                $copycollection->addFieldToFilter ( array (
                        array (
                                'attribute' => 'amenity',
                                'like' => "%$amenityString%" 
                        ) 
                ) );
            }
        }
        $copycollection = Mage::getModel('airhotels/status')->availableProducts($data,$copycollection);
        /**
         * Set page size for display result
         */        
        $copycollection->setPage ( $data ["pageno"], 6 );        
        return $copycollection;
    }    
    /**
     * Get Increment Value
     *
     * @param array $mergedBlockedArray            
     * @param int $propertyServiceFromRail            
     * @param int $propertyServiceToRail            
     */
    public function getIncrement($mergedBlockedArray, $propertyServiceFromRail, $propertyServiceToRail, $Incr) {        
        if (count ( $mergedBlockedArray ) >= 1) {
            for($timeStarts = $propertyServiceFromRail; $timeStarts < $propertyServiceToRail; $timeStarts ++) {
                $blockedArrayValue = Mage::getModel ( 'airhotels/customerinbox' )->getTwelveTimeFormat ( $timeStarts, $timeStarts + 1 );                
                if (array_key_exists ( $blockedArrayValue, $mergedBlockedArray )) {
                    $Incr = $Incr + 1;
                    break;
                }
            }
        }
        return $Incr;
    }
    /**
     * Function Name: checkAvailableProduct
     *
     * @param int $productid            
     * @param date $fromdate            
     * @param date $todate            
     * @return boolean
     */
    public function checkAvailableProduct($productid, $fromdate = "", $todate = "") {
        /**
         * Get the Customer Current Values
         */
        $myCalendar = Mage::getModel ( 'airhotels/airhotels' )->dateVerfiy ( $productid, $fromdate, $todate );
        /**
         * Get the Blocked Date
         */
        $blocked = Mage::getModel ( 'airhotels/product' )->getDays ( count ( $myCalendar [1] ), $myCalendar [1] );
        /**
         * Get the not available value
         */
        $notAvail = Mage::getModel ( 'airhotels/product' )->getDays ( count ( $myCalendar [2] ), $myCalendar [2] );
        /**
         * Checkin selected date
         */
        $From = date ( 'Y-n', strtotime ( $fromdate ) );
        /**
         * Checkout selected date
         */
        $To = date ( 'Y-n', strtotime ( $todate ) );
        $dateFrom = explode ( "-", $From );
        $dateTo = explode ( "-", $To );
        /**
         * Setting array with dealstatus with following options
         * 'processing' , 'complete'
         */
        $dealstatus = array (
                'processing',
                'complete' 
        );
        /**
         * Get the collection for 'airhotels/airhotels'
         */
        $ranges = Mage::getModel ( 'airhotels/airhotels' )->getCollection ()->addFieldToSelect ( array (
                'entity_id',
                'fromdate',
                'todate',
                'order_id',
                'order_item_id' 
        ) )->addFieldToFilter ( 'order_status', array (
                'eq' => '1' 
        ) );
        /**
         * Setting the Sales_Flat_order Value
         */
        $salesFlatOrder = ( string ) Mage::getConfig ()->getTablePrefix () . 'sales_flat_order';
        $ranges->getSelect ()->join ( array (
                'sales_flat_order' => $salesFlatOrder 
        ), "(sales_flat_order.entity_id = main_table.order_item_id AND main_table.entity_id = $productid  AND (sales_flat_order.status='$dealstatus[1]' OR sales_flat_order.status='$dealstatus[0]'))", array () );
        $collection = array ();
        $c = 1;
        foreach ( $ranges as $range ) {
            if ($range ["fromdate"] != "") {
                $collection [$c] = $range ["fromdate"];
                $c ++;
            }
            if ($range ["todate"]) {
                $collection [$c] = $range ["todate"];
                $c ++;
            }
        }
        /**
         * Call the 'airhotels/calendarsync' for colletion info.
         */
        return Mage::getModel ( 'airhotels/calendarsync' )->getCollectionInfo ( $collection, $fromdate, $todate, $dateFrom, $dateTo, $blocked, $notAvail );
    }
}