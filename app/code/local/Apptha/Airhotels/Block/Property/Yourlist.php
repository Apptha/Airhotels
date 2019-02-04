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
class Apptha_Airhotels_Block_Property_Yourlist extends Mage_Catalog_Block_Product_Abstract {
    /**
     * Prepares the layout
     *
     * @see Mage_Catalog_Block_Product_Abstract::_prepareLayout()
     */
    protected function _prepareLayout() {
        /**
         * Calling the parent Construct Method.
         */
        parent::_prepareLayout ();
        /**
         * Getting property listings
         */
        $listingCollection = $this->getListings ();
        $this->setCollection ( $listingCollection );
        /**
         * setting pager
         */
        $pager = $this->getLayout ()->createBlock ( 'page/html_pager', 'my.pager' )->setCollection ( $listingCollection );
        $this->setChild ( 'pager', $pager );
        
        return $this;
    }
    /**
     * Function to get pagination
     *
     * Return pagination for collection
     *
     * @return array
     */
    public function getPagerHtml() {
        return $this->getChildHtml ( 'pager' );
    }    
    /**
     * List all your host property
     *
     *
     * Return the property collection
     *
     * @return array
     */
    public function getListings() {
        /**
         * Getting customer session
         */
        $customer = Mage::getSingleton ( 'customer/session' )->getCustomer ();
        /**
         * Getting customer Id
         */
        $cusId = $customer->getId ();
        
        if (Mage::helper ( 'catalog/category_flat' )->isEnabled ()) {
            /**
             * Listings by Id
             */
            $storeId = Mage::app ()->getStore ()->getStoreId ();
            /**
             * Setting current store
             */
            Mage::app ()->setCurrentStore ( Mage_Core_Model_App::ADMIN_STORE_ID );
            /**
             * product Collection
             */
            $products = Mage::getModel ( 'catalog/product' )->getCollection ()->setDisableFlat ( true )->addAttributeToSort ( 'entity_id', 'DESC' )->/**
             * Filter by user ID
             */
            addFieldToFilter ( array (
                    array (
                            'attribute' => 'userid',
                            'eq' => $cusId 
                    ) 
            ) );
            Mage::app ()->setCurrentStore ( $storeId );
        } else {
            /**
             * Property Collection
             */
            $products = Mage::getModel ( 'airhotels/property' )->getpropertycollection ()->addAttributeToSort ( 'entity_id', 'DESC' )->/**
             * Filter by userid
             */
            addFieldToFilter ( array (
                    array (
                            'attribute' => 'userid',
                            'eq' => $cusId 
                    ) 
            ) );
        }
        /**
         * return array
         */
        return $products;
    }    
    /**
     * Getting the reply messages
     *
     * @param int $messageid            
     */
    public function getReplyMessages($messageid) {
        /**
         * Getting collection using message Id
         */
        return Mage::getModel ( 'airhotels/product' )->getReplyMessages ( $messageid );
    }
    
    /**
     * Getting the advance Search result
     */
    public function getAdvanceSearchResult() {
        /**
         * Get zoom level from parameters
         *
         * @var $zoomLevel
         */
        $zoomLevel = $this->getRequest ()->getParam ( 'zoomLevel' );
        $searchAddressFrom = $this->getRequest ()->getParam ( 'searchAddressFrom' );
        if (isset ( $zoomLevel )) {
            $address = $this->getRequest ()->getParam ( 'searchAddress' );
        } else {
            if (isset ( $searchAddressFrom )) {
                $address = $searchAddressFrom;
            } else {
                /**
                 * get Address
                 */
                $address = $this->getRequest ()->getParam ( 'searchAddress' );
            }
        }
        /**
         * get Checkin
         */
        $checkin = $this->getRequest ()->getParam ( 'checkin' );
        /**
         * get Checkout
         */
        $checkout = $this->getRequest ()->getParam ( 'checkout' );
        /**
         * get Search Guest
         */
        $searchguest = $this->getRequest ()->getParam ( 'searchguest' );
        /**
         * get Amount
         */
        $amount = $this->getRequest ()->getParam ( 'amount' );
        /**
         * get Room type Value
         */
        $roomtypeVal = $this->getRequest ()->getParam ( 'roomtypeval' );
        /**
         * get amenity Value
         */
        $amenityval = $this->getRequest ()->getParam ( 'amenityval' );
        /**
         * get Property Details
         */
        $pageno = $this->getRequest ()->getParam ( 'pageno' );
        /**
         * Getting page number
         */
        $upperLimitPrice = $this->getRequest ()->getParam ( 'upperLimitPrice' );
        /**
         * Getting Property service from
         */
        $propertyServiceFrom = $this->getRequest ()->getParam ( 'propertyServiceFrom' );
        /**
         * Getting Property service to
         */
        $propertyServiceTo = $this->getRequest ()->getParam ( 'propertyServiceTo' );
        /**
         * Getting am/pm
         */
        $propertyServiceFromPeriod = $this->getRequest ()->getParam ( 'propertyServiceFromPeriod' );
        $propertyServiceToPeriod = $this->getRequest ()->getParam ( 'propertyServiceToPeriod' );
        /**
         * Get the latitute and longtitude
         */
        $lattitudeZoom = $this->getRequest ()->getParam ( 'latituteZoom' );
        /**
         * Getting lattitude Zoomlevel
         */
        $propertyType = $this->getRequest ()->getParam ( 'proptypeVal' );
        /**
         * make an data array
         */
        $data = array (
                "address" => $address,
                "checkin" => $checkin,
                "checkout" => $checkout,
                "searchguest" => $searchguest,
                "amount" => $amount,
                "pageno" => $pageno,
                "roomtypeval" => $roomtypeVal,
                "amenityVal" => $amenityval,
                "upperLimitPrice" => $upperLimitPrice,
                "propertyServiceFrom" => $propertyServiceFrom,
                "propertyServiceTo" => $propertyServiceTo,
                "propertyServiceFromPeriod" => $propertyServiceFromPeriod,
                "propertyServiceToPeriod" => $propertyServiceToPeriod,
                'latituteZoom' => $lattitudeZoom,
                'zoomLevel' => $zoomLevel,
                "propertyType" => $propertyType 
        );
        /**
         * make sure the check in is present
         */
        if ($data ["checkin"] == "mm/dd/yyyy") {
            $data ["checkin"] = "";
        }
        /**
         * Make sure the checkout is present
         */
        if ($data ["checkout"] == "mm/dd/yyyy") {
            $data ["checkout"] = "";
        }
        /**
         * Check the value is present
         */
        if (trim ( $data ["address"] ) == "e.g. Berlin, Germany") {
            $data ["address"] = "";
        }
        /**
         * return array
         */
        return Mage::getModel ( 'airhotels/customerreply' )->advanceSearch ( $data );
    }
    /**
     * get the Readed emails
     *
     * @param int $inboxDetails            
     * @param int $i            
     * @return string
     */
    public function readClass($inboxDetails, $i) {
        /**
         * Inboxed message
         */
        if ($inboxDetails [$i] ["receiver_read"] == '1') {
            $readClass = "class='read'";
        } else {
            $readClass = "class='unread' ";
        }
        /**
         * Returns status of mail class
         */
        return $readClass;
    }
    /**
     * Get the sender readed emails
     *
     * @param int $inboxDetails            
     * @param int $i            
     * @return string
     */
    public function senderReadClass($inboxDetails, $i) {
        /**
         * Inboxed message read or unread
         */
        if ($inboxDetails [$i] ["sender_read"] == '1') {
            $readClass = "class='read'";
        } else {
            $readClass = "class='unread' ";
        }
        /**
         * Returns status of mail
         */
        return $readClass;
    }
    /**
     * Getting the image url status
     *
     * @param string $preImageUrl            
     * @param string $currentImageUrl            
     * @return number
     */
    public function imageUrlStatus($preImageUrl, $currentImageUrl) {
        /**
         * Getting the imageUrl
         */
        if ($preImageUrl == $currentImageUrl && $preImageUrl != '') {
            $imageUrlStatusForProperty = 1;
        } else {
            $imageUrlStatusForProperty = 0;
        }
        /**
         * Returns the image Url
         */
        return $imageUrlStatusForProperty;
    }
    /**
     * Function getGeocode()
     *
     * @param unknown $address            
     * @return unknown
     */
    public function getGeocode($address) {
        /**
         * Check weather the 'allow_url_fopen' is enabled
         */
        if (ini_get ( 'allow_url_fopen' )) {
            $geocode = file_get_contents ( 'http://maps.google.com/maps/api/geocode/json?address=' . urlencode ( $address ) . '&sensor=false' );
        } else {
            /**
             * Initialise the CURL
             */
            $ch = curl_init ();
            /**
             * passing the curl parameters
             */
            curl_setopt ( $ch, CURLOPT_URL, 'http://maps.google.com/maps/api/geocode/json?address=' . urlencode ( $address ) . '&sensor=false' );
            curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
            /**
             * Execute the CURL
             */
            $geocode = curl_exec ( $ch );
        }
        /**
         * decode the geocode
         *
         * @var $jsondata
         */
        $jsondata = json_decode ( $geocode, true );
        /**
         * city
         */
        foreach ( $jsondata ["results"] as $results ) {
            foreach ( $results ["address_components"] as $addresss ) {
                if (in_array ( "locality", $addresss ["types"] )) {
                    $arrayAddress ['city'] = $addresss ["long_name"];
                }
            }
        }
        /**
         * country
         */
        foreach ( $jsondata ["results"] as $resultKey ) {
            foreach ( $resultKey ["address_components"] as $addressKey ) {
                if (in_array ( "administrative_area_level_1", $addressKey ["types"] )) {
                    $arrayAddress ['state'] = $addressKey ["long_name"];
                }
            }
        }
        /**
         * country
         */
        foreach ( $jsondata ["results"] as $result ) {
            foreach ( $result ["address_components"] as $address ) {
                if (in_array ( "country", $address ["types"] )) {
                    $arrayAddress ['country'] = $address ["long_name"];
                }
            }
        }                
        /**
         * Impload array array of address.
         */
        return implode ( ", ", $arrayAddress );
    }
    /**
     * Function viewWishlist
     * users wishlist
     *
     * @return wishlist collection
     */
    public function viewWishlist() {
        $id = $this->getRequest ()->getParam ( 'id' );
        if ($id) {
            /**
             * Get whislist collection based on id.
             *
             * @var $wishlist
             */
            $wishlist = Mage::getModel ( 'wishlist/wishlist' )->loadByCustomer ( $id, true );
            $wishListItemCollection = $wishlist->getItemCollection ();
        }
        /**
         * Return whislist collection.
         */
        return $wishListItemCollection;
    }
    /**
     * Get verification profile status
     *
     * @return verify tag
     */
    public function verificationProfile() {
        $id = $this->getRequest ()->getParam ( 'id' );
        if ($id) {
            /**
             * Get verify host collection.
             * Filter by host_id
             * Filter by host_tag
             *
             * @var $verifiedTags
             */
            $verifiedTags = Mage::getModel ( 'airhotels/verifyhost' )->getCollection ()->addFieldToFilter ( 'host_id', array (
                    'eq' => $id 
            ) )->addFieldToFilter ( 'host_tags', array (
                    'eq' => 1 
            ) );
        }
        /**
         * Return verified tags value.
         */
        return $verifiedTags;
    }    
}