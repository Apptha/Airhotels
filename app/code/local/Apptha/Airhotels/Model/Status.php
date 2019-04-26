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
class Apptha_Airhotels_Model_Status extends Varien_Object {
    /**
     * A collection is a Model type containing other Models, it is basically used in Magento to handle product lists
     * (ie.
     * from a category or a bundle option), but not only.
     *
     * @return multitype
     */
    static public function getOptionArray() {
        /**
         * Returnning an array of values for getOptionArray
         * with enabled and disabled value
         */
        /**
         * Prepare array
         * with Enabled
         * and disabled as options
         */
        return array (
                1 => Mage::helper ( 'airhotels' )->__ ( 'Enabled' ),
                2 => Mage::helper ( 'airhotels' )->__ ( 'Disabled' ) 
        );
    }
    /**
     * Function Name: getValidationDetails
     *
     * @param unknown $propertyMaximum            
     * @param unknown $propertyMinimum            
     * @param unknown $overallTotalHours            
     * @return boolean
     */
    /**
     * get the validation details from this function
     */
    public function getValidationDetails($propertyMaximum, $propertyMinimum, $overallTotalHours) {
        /**
         * check $propertyMaximum is less than $overallTotalHours or not
         */
        if ($propertyMaximum < $overallTotalHours) {
            /**
             * Set reponse messgae.
             */
            /**
             * set the hours message data
             * 
             * @var unknown
             */
            $hoursMsgData = Mage::helper ( 'airhotels' )->__ ( 'Maximum property hour(s) which is' ) . " $propertyMaximum";
        }
        if ($propertyMinimum > $overallTotalHours) {
            /**
             * Set reponse messgae.
             */
            $hoursMsgData = Mage::helper ( 'airhotels' )->__ ( 'Minimum property hour(s) which is' ) . " $propertyMinimum";
        }
        /**
         * return true for this condition
         */
        return $hoursMsgData;
    }
    /**
     * Function Name: getCycleDetails
     */
    public function getCycleDetails($subCycle, $propertyMaximum, $propertyMinimum, $pDay) {
        /**
         * Check $subCycle is undefined or not
         */
        if ($subCycle == 'undefined') {
            /**
             * Subcycle is undefined
             */
            if ($propertyMinimum > $pDay) {
                /**
                 * Getting Minimum working days of a property
                 */
                /**
                 * get thhe date days
                 * 
                 * @var unknown
                 */
                $msgDataDays = Mage::helper ( 'airhotels' )->__ ( 'Minimum property day(s) which is' ) . " $propertyMinimum";
            /**
             * set data days as reponse to body
             */
            }
            if ($propertyMaximum < $pDay) {
                /**
                 * Getting maximum working days for a property
                 */
                $msgDataDays = Mage::helper ( 'airhotels' )->__ ( 'Maximum property day(s) which is' ) . " $propertyMaximum";
            }
            return $msgDataDays;
        }
    }
    /**
     * Function name: getDatesAvailable
     *
     * @param s $checkingFromDate,$todayDateValue,$currentHours            
     */
    public function getDatesAvailable($checkingFromDate, $todayDateValue, $currentHours,$propertyServiceFromPeriod, $propertyServiceFrom) {
        if ($checkingFromDate == $todayDateValue) {
            $propertyFromHoursCheckingWithCurrentHours = Mage::getModel('airhotels/airhotels')->getRailwayTimeFormat ( $propertyServiceFromPeriod, $propertyServiceFrom );
            $currentHours = Mage::getModel ( 'core/date' )->date ( 'H' );
            if ($currentHours >= $propertyFromHoursCheckingWithCurrentHours) {
                /**
                 * Set message in response.
                 */
                return Mage::helper ( 'airhotels' )->__ ( 'Dates are not available refer to calendar' );
            }
        }
    }
    /**
     * Function searchResult
     *
     * @param
     *            $data,$copycollection
     */
    public function searchResult($data, $copycollection) {
        /**
         * Check whether the latitude zoom and address are set.
         */
        $zoomLevel = $data ['zoomLevel'];
        if (! empty ( $data ["latituteZoom"] ) && $data ["address"] == '') {
            $latitueAndLong = explode ( ",", $data ["latituteZoom"] );
            if (Mage::helper ( 'airhotels/mobile' )->isMobile ()) {
                $zoomlevelArray = array (
                        0 => '10000',
                        1 => '5000',
                        2 => '3000',
                        3 => '1500',
                        4 => '1000',
                        5 => '300',
                        6 => '200',
                        7 => '120',
                        8 => '60',
                        9 => '30',
                        10 => '15',
                        11 => '7',
                        12 => '5',
                        13 => '2',
                        14 => '1',
                        15 => '1',
                        16 => '0.75',
                        17 => '0.50',
                        18 => '0.25' 
                );
            } else {
                $zoomlevelArray = array (
                        0 => '10000',
                        1 => '5000',
                        2 => '3000',
                        3 => '1500',
                        4 => '1000',
                        5 => '500',
                        6 => '400',
                        7 => '300',
                        8 => '200',
                        9 => '100',
                        10 => '40',
                        11 => '15',
                        12 => '7',
                        13 => '5',
                        14 => '2',
                        15 => '1',
                        16 => '0.75',
                        17 => '0.50',
                        18 => '0.25' 
                );
            }
            $lat = $latitueAndLong [0];
            $long = $latitueAndLong [1];
            $collectionLat = Mage::getModel ( 'airhotels/Latitudelongitude' )->getCollection ();
            $collectionLat->addExpressionFieldToSelect ( 'distance', '( 3959 * acos( cos( radians(' . $lat . ') ) * cos( radians( {{latitude}}) ) * cos( radians( {{longitude}}) - radians(' . $long . ') ) + sin( radians(' . $lat . ') ) * sin( radians( {{latitude}}) ) ) )', array (
                    'latitude' => 'latitude',
                    'longitude' => 'longitude' 
            ) );
            $collectionLat->getSelect ()->having ( 'distance < ' . $zoomlevelArray [$zoomLevel] );
            $collectionLatDataValue = $collectionLat->getData ();
            $nearesEntityVal = '';
            foreach ( $collectionLatDataValue as $entitiesArray ) {
                $nearesEntityVal .= $entitiesArray ['entity_id'] . ',';
            }
            $nearesEntityVal = rtrim ( $nearesEntityVal, ',' );
            $entityIdsArr = '';
            $entityidsVal = explode ( ',', $nearesEntityVal );
            foreach ( $entityidsVal as $ids ) {
                $entityIdsArr [] = array ('attribute' => 'entity_id','in' => $ids );
            }
            $copycollection->addFieldToFilter ( $entityIdsArr );
        } else {
            if ($data ["address"] != '' || $addressTrimData != Mage::helper ( 'airhotels' )->__ ( 'Berlin, San Juan de Lurigancho, Peru' )) {
                $addressTrimData = trim ( $data ["address"] );                    
                $address = $addressTrimData;                    
            } else {
                $address = Mage::getStoreConfig ( 'airhotels/advance_search/defaultlocation' );
            }
            $country = $address;
            $addrsRemoveSpace = str_replace ( ' ', '+', $country );
            $addressAddPlus = str_replace ( ',', '+', $addrsRemoveSpace );
            /**
             * Check whether 'allow_url_fopen' is enabled
             */
            $config = Mage::getStoreConfig ( 'airhotels/custom_group' );
            $googleApiKey = $config['airhotels_googlemapapi'];
            $encodeAddress = urlencode ( $addressAddPlus );
            if (ini_get ( 'allow_url_fopen' )) {
                $geocode = file_get_contents ( 'https://maps.google.com/maps/api/geocode/json?address=' . rtrim ( $encodeAddress ) . '&sensor=false&key='.$googleApiKey);

            } else {
                $ch = curl_init ();
                curl_setopt ( $ch, CURLOPT_URL, 'https://maps.google.com/maps/api/geocode/json?address=' . rtrim ( $encodeAddress ) . '&sensor=false&key='.$googleApiKey);
                curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
                $geocode = curl_exec ( $ch );
            }
            /**
             * get the geo code values.
             */
            $output = json_decode ( $geocode, true );            
            $northeastlat = $output ['results'] ['0'] ['geometry'] ['viewport'] ['northeast'] ['lat'];
            $northeastlng = $output ['results'] ['0'] ['geometry'] ['viewport'] ['northeast'] ['lng'];
            $southwestlat = $output ['results'] ['0'] ['geometry'] ['viewport'] ['southwest'] ['lat'];
            $southwestlng = $output ['results'] ['0'] ['geometry'] ['viewport'] ['southwest'] ['lng'];
             $maxLatitudeValue = max($northeastlat,$southwestlat);
             $minLatitudeValue = min($northeastlat,$southwestlat);
             $maxLongitudeValue = max($northeastlng,$southwestlng);
             $minLongitudeValue = min($northeastlng,$southwestlng);
             $copycollection->addAttributeToFilter ( array (
                     array (
                             'attribute' => 'latitude',
                             'lteq' => $maxLatitudeValue
                     )
             ) );          
             $copycollection->addAttributeToFilter ( array (                   
                     array (
                            'attribute' => 'longitude',
                             'lteq' => $maxLongitudeValue
                    )
             ) );
             $copycollection->addAttributeToFilter ( array (                  
                     array (
                             'attribute' => 'latitude',
                             'gteq' => $minLatitudeValue
                     )
             ) );
             $copycollection->addAttributeToFilter ( array (                   
                     array (
                             'attribute' => 'longitude',
                             'gteq' => $minLongitudeValue
                     )
             ) );          
        }
        
        return $copycollection;
    }
    /**
     * Function Name: availableProducts
     *
     * @param
     *            $data,$copycollection,$collection
     */
    public function availableProducts($data, $copycollection) {
        /**
         * Filter by date and hour
         */
        /**
         * Initilizing date for filter
         */
        if ($data ["checkin"] != "") {
            $fromdate = date ( "Y-m-d", strtotime ( $data ["checkin"] ) );
        }
        if ($data ["checkout"] != "") {
            $todate = date ( "Y-m-d", strtotime ( $data ["checkout"] ) );
        }
        /**
         * Declare $productFilter array
         * 
         * @var unknown
         */
        $productFilter = array ();
        $count = 0;
        $bookingServiceFrom = $data ['propertyServiceFrom'];
        $bookingServiceTo = $data ['propertyServiceTo'];
        $bookingServiceFromPeriod = $data ['propertyServiceFromPeriod'];
        $bookingServiceToPeriod = $data ['propertyServiceToPeriod'];
        $bookingTimeData = Mage::helper ( 'airhotels/airhotel' )->getPropertyTimeLabelByOptionId ();
        $hourlyEnabledOrNot = Mage::helper ( 'airhotels/product' )->getHourlyEnabledOrNot ();
        if (isset ( $fromdate ) && isset ( $todate ) && count ( $copycollection )) {
            foreach ( $copycollection as $_product ) {
                /**
                 * checking whether hourly or daily based availability
                 */
                if (( int ) $bookingServiceFrom > 0 && ( int ) $bookingServiceTo > 0 && ! empty ( $bookingServiceFromPeriod ) && ! empty ( $bookingServiceToPeriod )) {
                    /**
                     * Checking whether hourly or daily based product
                     */
                    if ($bookingTimeData == $_product->getPropertyTime () && $hourlyEnabledOrNot == 0) {
                        $serviceFromDate = date ( 'm/d/Y', strtotime ( $data ["checkin"] ) );
                        $serviceToDate = date ( 'm/d/Y', strtotime ( $data ["checkout"] ) );
                        $availresult = ( int ) Mage::getModel ( 'airhotels/airhotels' )->checkHourlyAvailableProduct ( $_product->getId (), $serviceFromDate, $serviceToDate, $bookingServiceFrom, $bookingServiceFromPeriod, $bookingServiceTo, $bookingServiceToPeriod );
                        $availresultCal = ( int ) Mage::getModel ( 'airhotels/product' )->checkavalidateincal ( $_product->getId (), $fromdate, $todate );
                    } else {
                        $availresult = $availresultCal = 1;
                    }
                } else {
                    if ($bookingTimeData != $_product->getPropertyTime () || $hourlyEnabledOrNot == 1) {
                        $availresult = ( int ) Mage::getModel ( 'airhotels/customerreply' )->checkAvailableProduct ( $_product->getId (), $fromdate, $todate );
                        $availresultCal = ( int ) Mage::getModel ( 'airhotels/product' )->checkavalidateincal ( $_product->getId (), $fromdate, $todate );
                    } else {
                        $availresult = $availresultCal = 1;
                    }
                }
                if (! $availresult || ! $availresultCal) {
                    $productFilter [$count] = $_product->getId ();
                    $count ++;
                }
            }
        }
        /**
         * Filter by product id
         */
        if (count ( $productFilter )) {
            $copycollection = $copycollection->addFieldToFilter ( 'entity_id', array (
                    'nin' => $productFilter 
            ) );
        }
        /**
         * Get product collection sort by reviews.
         */
        $copycollection->joinField ( 'rating_score', 'review_entity_summary', 'rating_summary', 'entity_pk_value=entity_id', array (
                'entity_type' => 1,
                'store_id' => Mage::app ()->getStore ()->getId () 
        ), 'left' );
        return $copycollection;
    }
    
    /**
     * Function Name: roomTypeFilter
     *
     * @param
     *            $data,$collection,$copycollection
     */
    public function roomTypeFilter($data, $copycollection) {
        /**
         * Filter by seats
         */
        if (( int ) $data ["searchguest"] > 0) {
            if (( int ) $data ["searchguest"] >= 16) {
                $copycollection->addFieldToFilter ( 'people_min', array (
                        'gteq' => ( int ) $data ["searchguest"] 
                ) );
            } else {
                $copycollection->addFieldToFilter ( 'people_min', array (
                        'lteq' => ( int ) $data ["searchguest"] 
                ) );
                $copycollection->addFieldToFilter ( 'people_max', array (
                        'gteq' => ( int ) $data ["searchguest"] 
                ) );
            }
        }
        /**
         * Filter by property type
         */
        $roomtypeString = $data ["propertyType"];
        $dataRoomtypeval = explode ( ",", $roomtypeString );
        if (count ( $dataRoomtypeval ) > 0 && trim ( $roomtypeString ) != "") {
            $copycollection->addFieldToFilter ( 'propertytype', array (
                    'in' => array (
                            $dataRoomtypeval 
                    ) 
            ) );
        }
        /**
         * Filter by privacy
         */
        $roomtypeval = $data ["roomtypeval"];
        $dataRoomtype = explode ( ",", $roomtypeval );
        if (count ( $dataRoomtype ) > 0 && trim ( $roomtypeval ) != "") {
            $copycollection->addFieldToFilter ( 'privacy', array (
                    'in' => array (
                            $dataRoomtype 
                    ) 
            ) );
        }
        return $copycollection;
    }
    /**
     * Function name: saveFormData
     *
     * @param
     *            $post
     */
    public function saveFormData($post) {
        $security = array ();
        if (isset ( $post ['security'] )) {
            $security = implode ( ",", $post ['security'] );
            $security = str_replace ( " ", "", $security );
        }
        $currentExperienceId = Mage::getSingleton ( 'customer/session' )->getCurrentExperienceId ();
        $customer = Mage::getSingleton ( 'customer/session' )->getCustomer ();
        $customerId = $customer->getId ();
        $customerEmail = $customer->getEmail ();
        $storeId = $post ['store_id'];
        $language = array ();
        if (isset ( $post ['language'] )) {
            $language = implode ( ",", $post ['language'] );
            $language = str_replace ( " ", "", $language );
        }
        $propertyAddress = $post ["propertyadd"];
        $address = Mage::helper ( 'airhotels/url' )->getGeocodeDatas ( $propertyAddress );
        $random = rand ( 1, 100000000000 );
        $sku = rand ( 1, $random );
        $websiteId = Mage::app ()->getWebsite ()->getId ();
        $amenity = array ();
        /**
         * calling the amenityVal helper function
         */
        $amenity = Mage::helper ( 'airhotels/general' )->amenityVal ( $post );
        if ($currentExperienceId) {
            $product = Mage::getModel ( 'catalog/product' )->load ( $currentExperienceId );
            if ($product->getId ()) {
                $product->setName ( $post ['name'] )->setDescription ( $post ['description'] )->setShortDescription ( $post ['description'] )->setPrice ( $post ['price'] )->setPeopleMin ( $post ['accommodate_minimum'] )->setPeopleMax ( $post ['accommodate_maximum'] )->setPropertyMinimum ( $post ['property_minimum'] )->setPropertyMaximum ( $post ['property_maximum'] )->setHostemail ( $customerEmail )->setPropertyadd ( $propertyAddress )->setLanguage ( $language )->setState ( $address ['state'] )->setCity ( isset ( $address ['city'] ) ? $address ['city'] : $address ['state'] )->setCountry ( $address ['country'] )->setPropertytype ( array (
                        $post ['property_type'] 
                ) )->setCancelpolicy ( $post ['cancelpolicy'] )->setPrivacy ( array (
                        $post ['privacy'] 
                ) )->setAmenity ( $amenity )->setBedType ( $post ['bed_type'] )->setBedRooms ( $post ['bed_rooms'] );
                $product->setTaxClassId ( $post ['tax_class_id'] );
                if (isset ( $security )) {
                    $product->setSecurity ( $security );
                }
                /**
                 * Check the security deposit value
                 */
                if (isset ( $post ['duration'] )) {
                    $product->setDuration ( $post ['duration'] );
                }
                if (isset ( $post ['tags'] )) {
                    $product->setTag ( $post ['tags'] );
                }
                if (isset ( $post ['latitude'] )) {
                    $product->setLatitude ( $post ['latitude'] );
                }
                if (isset ( $post ['longitude'] )) {
                    $product->setLongitude ( $post ['longitude'] );
                }
                if (Mage::helper ( 'airhotels/smsconfig' )->getCustomAttributeEnableOrNot ()) {
                    $product = Mage::helper ( 'airhotels/smsconfig' )->customAttributeSave ( $product, $productData );
                }
            }
        } else {
            $product = Mage::getModel ( 'catalog/product' );
            $product->setStoreID ( 0 )->setSku ( $sku )->setUserid ( $customerId )->setAttributeSetId ( 4 )->setTypeId ( 'property' )->setName ( $post ['name'] )->setDescription ( $post ['description'] )->setShortDescription ( $post ['description'] )->setPrice ( $post ['price'] )->setPeopleMin ( $post ['accommodate_minimum'] )->setPeopleMax ( $post ['accommodate_maximum'] )->setHostemail ( $customerEmail )->setPropertyadd ( $propertyAddress )->setLanguage ( $language )->setState ( $address ['state'] )->setCity ( isset ( $address ['city'] ) ? $address ['city'] : $address ['state'] )->setCountry ( $address ['country'] )->setPropertytype ( array (
                    $post ['property_type'] 
            ) )->setCancelpolicy ( $post ['cancelpolicy'] )->setPrivacy ( array (
                    $post ['privacy'] 
            ) )->setAmenity ( $amenity )->setPropertyType ( $post ['property_type'] )->setBedType ( $post ['bed_type'] )->setBedRooms ( $post ['bed_rooms'] );
            $product->setTaxClassId ( $post ['tax_class_id'] );
            if (isset ( $security )) {
                $product->setSecurity ( $security );
            }
            $product->setTaxClassId ( $post ['tax_class_id'] );
            if (isset ( $post ['duration'] )) {
                $product->setDuration ( $post ['duration'] );
            }
            if (isset ( $post ['latitude'] )) {
                $product->setLatitude ( $post ['latitude'] );
            }
            if (isset ( $post ['longitude'] )) {
                $product->setLongitude ( $post ['longitude'] );
            }
            /**
             * setStatus(1) - Enable the experience from admin by set as 1.
             */
            $product->setCategoryIds ( Mage::app ()->getStore ()->getRootCategoryId () )->setVisibility ( Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH )->setStatus ( 1 )->setStockData ( array (
                    'is_in_stock' => 1,
                    'qty' => 1000000,
                    'manage_stock' => 0 
            ) )->/**
             * setPropertyapproved(0) - Experience approve status is no,setPropertyapproved(1) - Experience approve status is yes.
             */
            setPropertyapproved ( 0 )->setCreatedAt ( strtotime ( 'now' ) )->setWebsiteIDs ( array (
                    $websiteId 
            ) )->setPropertyMaximum ( $post ['property_maximum'] )->setPropertyMinimum ( $post ['property_minimum'] );
            if (isset ( $post ['tags'] )) {
                $product->setTag ( $post ['tags'] );
            }
            if (Mage::helper ( 'airhotels/smsconfig' )->getCustomAttributeEnableOrNot ()) {
                $product = Mage::helper ( 'airhotels/smsconfig' )->customAttributeSave ( $product, $productData );
            }
        }
        if ($currentExperienceId) {
            $product->setStoreId ( $storeId )->setWebsiteIDs ( array (
                    $websiteId 
            ) );
        } else {
            $product->setStoreID ( 0 )->setWebsiteIDs ( array (
                    $websiteId 
            ) );
        }
        return $this->getPropertyTimeInfo ( $product, $post );
    }
    /**
     * Function name: getPropertyTimeInfo()
     *
     * @param $product,$post Return
     *            property information
     */
    public function getPropertyTimeInfo($product, $post) {
        if (! empty ( $post ['property_time'] )) {
            /**
             * Getting hourly property time option value
             */
            $hourlyPropertyTime = Mage::helper ( 'airhotels/airhotel' )->getPropertyTimeLabelByOptionId ();
            /**
             * For hourly based property
             */
            if ($post ['property_time'] == $hourlyPropertyTime && ! empty ( $hourlyPropertyTime )) {
                /**
                 * Set the Property Service From time, PropetyService To Time Values
                 */
                $propertyServiceFromTime = $post ['property_service_from'] . ':' . $post ['property_service_from_period'];
                $propertyServiceToTime = $post ['property_service_to'] . ':' . $post ['property_service_to_period'];
                /**
                 * Check whether the Property Service From time, PropetyService To Time are set
                 */
                if (! empty ( $post ['property_service_from'] ) && ! empty ( $post ['property_service_to'] )) {
                    /**
                     * Set propertyTime,PropertyOvernightFee,PropertyServiceFromTime,PropertyServiceToTime
                     */
                    $product->setPropertyTime ( $post ['property_time'] )->setPropertyOvernightFee ( $post ['property_overnight_fee'] )->setPropertyServiceFromTime ( $propertyServiceFromTime )->setPropertyServiceToTime ( $propertyServiceToTime );
                } else {
                    Mage::getSingleton ( 'core/session' )->addError ( $this->__ ( 'Please enter all required fields' ) );
                    $this->_redirect ( '*/*/' );
                }
            } else {
                $product->setPropertyTime ( $post ['property_time'] );
            }
        } else {
            Mage::getSingleton ( 'core/session' )->addError ( $this->__ ( 'Please enter all required fields' ) );
            $this->_redirect ( '*/*/' );
        }
        return $product;
    }
    /**
     * Function Name: getDiscountDescription()
     *
     * @param s $discountAmount,$baseDiscountAmount,$firstPurchaseDiscount            
     */
    public function getDiscountDescription($discountAmount, $baseDiscountAmount, $firstPurchaseDiscount, $firstPurchaseBaseDiscount) {
        $currencySymbol = Mage::app ()->getLocale ()->currency ( Mage::app ()->getStore ()->getCurrentCurrencyCode () )->getSymbol ();
        $discountDescriptionSingle = $discountDescriptionDouble = '';
        if (! empty ( $discountAmount ) && ! empty ( $baseDiscountAmount )) {
            $discountDescriptionSingle = Mage::helper ( 'airhotels' )->__ ( 'Friends Referral Discount' );
            $discountDescriptionDouble = Mage::helper ( 'airhotels' )->__ ( ', Friends Referral Discount' );
        }
        if (! empty ( $firstPurchaseDiscount )) {
            Mage::getSingleton ( "core/session" )->setCurrentCustomerFirstPurchaseDiscount ( $firstPurchaseDiscount );
        } else {
            Mage::getSingleton ( "core/session" )->setCurrentCustomerFirstPurchaseDiscount ( 0 );
        }
        if (! empty ( $discountAmount )) {
            Mage::getSingleton ( "core/session" )->setDiscountAmountForInviteeToDisplay ( $discountAmount );
        } else {
            Mage::getSingleton ( "core/session" )->setDiscountAmountForInviteeToDisplay ( 0 );
        }
        if (! empty ( $firstPurchaseDiscount ) && ! empty ( $firstPurchaseBaseDiscount )) {
            $discountAmount = $discountAmount + $firstPurchaseDiscount;
            $baseDiscountAmount = $baseDiscountAmount + $firstPurchaseBaseDiscount;
            if (! empty ( $discountDescriptionSingle )) {
                $discountDescriptionSingle = Mage::helper ( 'airhotels' )->__ ( 'Friends Referral Discount, First Purchase Discount' ) . " $currencySymbol$firstPurchaseDiscount";
            } else {
                $discountDescriptionSingle = Mage::helper ( 'airhotels' )->__ ( 'First Purchase Discount' ) . " $currencySymbol$firstPurchaseDiscount";
            }
            if (! empty ( $discountDescriptionDouble )) {
                $discountDescriptionDouble = Mage::helper ( 'airhotels' )->__ ( ', Friends Referral Discount, First Purchase Discount' ) . " $currencySymbol$firstPurchaseDiscount";
            } else {
                $discountDescriptionDouble = Mage::helper ( 'airhotels' )->__ ( 'First Purchase Discount' ) . " $currencySymbol$firstPurchaseDiscount";
            }
        }
        return array (
                $discountDescriptionSingle,
                $discountDescriptionDouble,
                $discountAmount 
        );
    }
    /**
     * Function Name: getMonthArray()
     *
     * @param $blockedTimeSp,$propertyServiceFromDataRail, $propertyServiceToRail,
     *            $price,$totalHours
     *            
     *            Returning month information
     */
    public function getMonthArray($blockedTimeSp, $propertyServiceFromDataRail, $propertyServiceToRail, $price, $totalHours) {
        if (! empty ( $blockedTimeSp )) {
            $av [$month] [$pIn] = Mage::getModel ( 'airhotels/product' )->getHourlyBasedSpecialPrice ( $blockedTimeSp, $propertyServiceFromDataRail, $propertyServiceToRail, $price );
        } else {
            $av [$month] [$pIn] = $totalHours * $price;
        }
        return $av [$month] [$pIn];
    }
    /**
     * Function name: getBaseDiscountAmountDetails
     *
     * @param $baseCurrencyCode,$currentCurrencyCode,$discountAmount return
     *            $baseDiscountAmount
     */
    public function getBaseDiscountAmountDetails($baseCurrencyCode, $currentCurrencyCode, $discountAmount) {
        if ($baseCurrencyCode != $currentCurrencyCode) {
            $baseDiscountAmount = Mage::helper ( 'core' )->currency ( $discountAmount, false, false );
        } else {
            $baseDiscountAmount = $discountAmount;
        }
        return $baseDiscountAmount;
    }
    /**
     * Function Name: redirectAction
     *
     * redirect to edit action
     */
    public function redirectAction($server_maximum_upload_size, $filesDataArray, $id) {
        /**
         * Storing mp4 uploaded video
         */
        if (isset ( $filesDataArray ['video_url_mp4'] ['name'] ) && $filesDataArray ['video_url_mp4'] ['name'] != '') {
            if ($server_maximum_upload_size < $filesDataArray ['video_url_mp4'] ['size']) {
                /**
                 * set error message.
                 */
                Mage::getSingleton ( 'adminhtml/session' )->addError ( Mage::helper ( 'airhotels' )->__ ( 'Upload video(MP4) size limt excceds server maximum upload file limit.' ) );
                $this->_redirect ( '*/*/edit', array (
                        'id' => $id 
                ) );
                return;
            }
            /**
             * Save video.
             */
            $videoPathMP4 = $this->saveVideoTypeMp4 ( $filesDataArray, $id );
            if (empty ( $videoPathMP4 )) {
                $this->_redirect ( '*/*/' );
            }
        }
        /**
         * Storing webm uploaded videos
         */
        if (isset ( $filesDataArray ['video_url_webm'] ['name'] ) && $filesDataArray ['video_url_webm'] ['name'] != '') {
            if ($server_maximum_upload_size < $filesDataArray ['video_url_webm'] ['size']) {
                Mage::getSingleton ( 'adminhtml/session' )->addError ( Mage::helper ( 'airhotels' )->__ ( 'Upload Video(WEBM) size limt excceds server maximum upload file limit.' ) );
                $this->_redirect ( '*/*/edit', array (
                        'id' => $id 
                ) );
            }
            /**
             * Save webm video.
             */
            $videoPathWEBM = $this->saveVideoTypeWebm ( $filesDataArray, $id );
            if (empty ( $videoPathWEBM )) {
                $this->_redirect ( '*/*/' );
            }
        }
        return array (
                $videoPathMP4,
                $videoPathWEBM 
        );
    }
    /**
     * Save video type mp4 type
     *
     * @param Array $filesDataArray            
     * @param Int $id            
     */
    public function saveVideoTypeMp4($filesDataArray, $id) {
        try {
            /**
             * Storing mp4 uploaded video
             */
            $videoId = $id;
            if (empty ( $videoId )) {
                $collectionForCount = Mage::getModel ( 'airhotels/uploadvideo' )->getCollection ()->setOrder ( 'id', 'DESC' )->getFirstItem ();
                $videoId = $collectionForCount->getId () + 1;
            }
            /**
             * Path to save the mp4 video
             */
            $path = Mage::getBaseDir ( 'media' ) . DS . 'airhotels' . DS . 'video' . DS . 'mp4' . DS;
            $videoUrlName = 'video_url_mp4';
            $videoPathMP4 = Mage::helper ( 'airhotels/url' )->uploadVideoMp ( $filesDataArray, $videoUrlName, $path, $videoId );
            if (! empty ( $videoPathMP4 )) {
                move_uploaded_file ( $filesDataArray ["video_url_mp4"] ["tmp_name"], "airhotels/video/mp4" . $filesDataArray ["video_url_mp4"] ["name"] );
            }
            return $videoPathMP4;
        } catch ( Exception $e ) {
            /**
             * Set error message.
             */
            Mage::getSingleton ( 'adminhtml/session' )->addError ( $e->getMessage () );
            $this->_redirect ( '*/*/edit', array (
                    'id' => $id 
            ) );
            return;
        }
    }
    /**
     * Save the video type Webm file.
     *
     * @param Array $filesDataArray            
     * @param int $id            
     * @return void unknown
     */
    public function saveVideoTypeWebm($filesDataArray, $id) {
        try {
            /**
             * Storing webm uploaded video
             */
            $videoId = $id;
            if (empty ( $videoId )) {
                /**
                 * if the video is
                 * empty
                 * 
                 * @var unknown
                 */
                /**
                 * load the upload video collection
                 * store in an array
                 * 
                 * @var unknown
                 */
                $collectionForCount = Mage::getModel ( 'airhotels/uploadvideo' )->getCollection ()->setOrder ( 'id', 'DESC' )->getFirstItem ();
                /**
                 * inc the video upload id
                 * 
                 * @var unknown
                 */
                $videoId = $collectionForCount->getId () + 1;
            }
            /**
             * Path to save the webm video
             */
            $path = Mage::getBaseDir ( 'media' ) . DS . 'airhotels' . DS . 'video' . DS . 'webm' . DS;
            /**
             * set tghe video url name
             * 
             * @var unknown
             */
            $videoUrlName = 'video_url_webm';
            /**
             * set the video path for upload
             * 
             * @var unknown
             */
            $videoPathWEBM = Mage::helper ( 'airhotels/url' )->uploadVideoWebm ( $filesDataArray, $videoUrlName, $path, $videoId );
            if (! empty ( $videoPathWEBM )) {
                /**
                 * check if its not empty
                 */
                /**
                 * Upload video.
                 */
                /**
                 * move the uploaded file
                 */
                move_uploaded_file ( $filesDataArray ["video_url_webm"] ["tmp_name"], "airhotels/video/webm" . $filesDataArray ["video_url_webm"] ["name"] );
            }
            /**
             * return the video path
             */
            return $videoPathWEBM;
        } catch ( Exception $e ) {
            /**
             * Set error message.
             */
            Mage::getSingleton ( 'adminhtml/session' )->addError ( $e->getMessage () );
            $this->_redirect ( '*/*/edit', array (
                    'id' => $id 
            ) );
            return;
        }
    }
}