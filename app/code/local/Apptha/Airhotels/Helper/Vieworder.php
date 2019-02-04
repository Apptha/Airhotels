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
class Apptha_Airhotels_Helper_Vieworder extends Mage_Core_Helper_Abstract {
    /**
     * Get order product data
     *
     * @param number $sellerId            
     * @param number $orderId            
     * @return array
     */
    public function getOrderProductIds($sellerId, $orderId) {
        $product = Mage::getModel ( 'airhotels/airhotels' )->getCollection ()->addFieldToFilter ( 'order_item_id', $orderId );
        
        return array_unique ( $product->getColumnValues ( 'entity_id' ) );
    }
    /**
     * Get order status
     *
     * @param number $orderId            
     * @param number $productId            
     * @return string
     */
    public function getPropertyOrderStatus($orderId) {
        /**
         * Load commission model
         */
        $products = Mage::getModel ( 'airhotels/airhotels' )->getCollection ();
        $products->addFieldToSelect ( '*' );
        /**
         * Filter model by order id and product id
         */
        $products->addFieldToFilter ( 'order_item_id', $orderId );
        foreach ( $products as $result ) {
            $result ['cancel_request_status'];
        }
        /**
         * Return order status
         */
        return $result;
    }
    /**
     * Function Name: getDateArray
     *
     * return $dateArray
     */
    public function getDateArray() {
        return array (
                "Sun" => 1,
                "Mon" => 2,
                "Tue" => 3,
                "Wed" => 4,
                "Thu" => 5,
                "Fri" => 6,
                "Sat" => 7 
        );
    }
    /**
     * Function Name: getDaysCount
     *
     * return $t1
     */
    public function getDaysCount($st,$totaldays) {
        if (($st >= 6 && $totaldays == 31) || ($st == 7 && $totaldays == 30)) {
            $tl = 42;
        } else {
            $tl = 35;
        }
        return $tl;
    }       
    /**
     * Function name: getAttributeDetails
     */
    public function getAttributeDetails($data){
        /**
         * Get attribute bedtype
         */
        if (isset ( $data [117] )) {
            $attribute = Mage::getModel ( 'eav/config' )->getAttribute ( 'catalog_product', 'bed_type' );
            foreach ( $attribute->getSource ()->getAllOptions ( true ) as $bedOption ) {
                if ($bedOption ['label'] == $data [117]) {
                    $data [117] = $bedOption ['value'];
                    break;
                }
            }
        }
        /**
         * Get attribute Bed Rooms
         */
        if (isset ( $data [122] )) {
            $attribute = Mage::getModel ( 'eav/config' )->getAttribute ( 'catalog_product', 'bed_rooms' );
            foreach ( $attribute->getSource ()->getAllOptions ( true ) as $bedRoomsOption ) {
                if ($bedRoomsOption ['label'] == $data [122]) {
                    $data [122] = $bedRoomsOption ['value'];
                    break;
                }
            }
        }
        /**
         * Get attribute video type
         */
        if (isset ( $data [124] )) {
            $attribute = Mage::getModel ( 'eav/config' )->getAttribute ( 'catalog_product', 'video_type' );
            foreach ( $attribute->getSource ()->getAllOptions ( true ) as $videoTypeOption ) {
                if ($videoTypeOption ['label'] == $data [124]) {
                    $data [124] = $videoTypeOption ['value'];
                    break;
                }
            }
        }
        return array($data [117],$data [122],$data [124]);
    }
}