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
 * Search Class model for advanced search action
 * @author user
 *
 */
class Apptha_Airhotels_Model_Search extends Mage_Core_Model_Abstract {
 /**
  * Getting function for amenity filter
  * 
  * @param unknown $collection         
  * @param unknown $amenitySearchArray         
  * @param unknown $copycollection         
  * @return unknown
  */
 public function getFilterByAmenity($collection, $amenitySearchArray, $copycollection) {
     /**
      * Get the collection for amenity
      */
  $collection->addFieldToFilter ( array (
    array (
      'attribute' => 'amenity',
      $amenitySearchArray 
    ) 
  ) );
  /**
   * Copy collection for amenity
   */
  $copycollection->addFieldToFilter ( array (
    array (
      'attribute' => 'amenity',
      $amenitySearchArray 
    ) 
  ) );
  /**
   * Return collection the amenity collection
   */
  return $collection;
 }
 /**
  * Getting function for propertytype filter
  * 
  * @param unknown $collection         
  * @param unknown $dataRoomtypeval         
  * @param unknown $copycollection         
  * @return unknown
  */
 public function getFilterByType($collection, $dataRoomtypeval, $copycollection) {
     /**
      * Property Collection based on filter property type
      */
  $collection->addFieldToFilter ( 'propertytype', array (
    'in' => array (
      $dataRoomtypeval 
    ) 
  ) );
  /**
   * Copy collection with property type filter
   */
  $copycollection->addFieldToFilter ( 'propertytype', array (
    'in' => array (
      $dataRoomtypeval 
    ) 
  ) );
  /**
   * Return collectio the property type filter collection
   */
  return $collection;
 }
 /**
  * Getting function for guest filter
  * 
  * @param unknown $collection         
  * @param unknown $searchGuest         
  * @param unknown $copycollection         
  * @return unknown
  */
 public function getFilterByGuest($collection, $searchGuest, $copycollection) {
     /**
      * Filter collection
      * based on accommadates
      */
     /**
      * condition based on greater than equal to
      */
  $collection->addAttributeToFilter ( 'accomodates', array (
    'gteq' => $searchGuest 
  ) );
  $copycollection->addAttributeToFilter ( 'accomodates', array (
    'gteq' => $searchGuest 
  ) );
  /**
   * Return collection the filter collection of accommodates
   */
  return $collection;
 }
 /**
  * Check item has Discount
  *
  * @param object $item         
  * @return boolean
  */
 public function itemHasDiscount($item) {
     /**
      * Item has discount check
      */
  if ($item->getDiscountAmount () || $item->getFreeShipping ()) {
      /**
       * If the items has discount
       * @var unknown
       */
      /**
       * set it as true
       * @var unknown
       */
   $hasDiscount = true;
  }
  /**
   * return the has discount tata
   */
  return $hasDiscount;
 }
 /**
  * Checks the property Minimum
  *
  * @param number $propertyMinimum         
  * @param number $overallTotalHours         
  * @return boolean
  */
 function checkPropertyMin($propertyMinimum, $overallTotalHours) {
     /**
      * Check property minimum function
      */
  if ($propertyMinimum > $overallTotalHours) {
   /**
    * Adding Minimum hours error message
    */
   $msgData = Mage::helper ( 'airhotels' )->__ ( 'Minimum property hour(s) which is' ) . "$propertyMinimum";
   /**
    * Set the response to body with error message
    */
   Mage::app ()->getResponse ()->setBody ( $msgData );
   /**
    * return the error message
    */
   return TRUE;
  }
 }
 /**
  * save invite friends action
  * @param number customer id
  * @param email Customer email
  * @return boolean
  */
 public function saveInviteFriends($inviteFriendsArrayValue){
     /**
      * get store id
      * @var unknown
      */
     $storeId = Mage::app ()->getStore ()->getStoreId ();
     /**
      * get the website id
      * @var unknown
      */
     $websiteId = Mage::app ()->getStore ()->getWebsiteId ();
     /**
      * get model collection of invite friends
      * @var unknown
      */
     $model = Mage::getModel ( 'airhotels/invitefriends' );
     /**
      * set the post values from form
      */
     $model->setCustomerId ( $inviteFriendsArrayValue['cus_id'] );
     $model->setCustomerName ( $inviteFriendsArrayValue['cus_name'] );
     $model->setCustomerEmail ( $inviteFriendsArrayValue['cus_email'] );
     $model->setInviteeName ( '' );
     $model->setInviteeEmail ( '' );
     $model->setStoreId ( $storeId );
     $model->setWebsiteId ( $websiteId );
     $model->setCurrentCreditAmount ( 0 );
     $model->setFriendsPurchaseCount ( 0 );
     $model->setFriendsListingCount ( 0 );
     $model->setOverallCreditAmount ( 0 );
     $model->save ();
 }
 /**
  * Product status change on customer delete
  * @param number customer id
  * @param email Customer email
  * @return boolean
  */
 public function productDisableStatus($customerId){
     $productCollections = Mage::getModel ( 'catalog/product' )->getCollection ()->addAttributeToFilter ( 'userid', $customerId)->addAttributeToFilter ( 'type_id', array (
             'eq' => 'property'
     ) );
     /**
      * Iterating the loop
      */
     foreach ( $productCollections as $product ) {
         /**
          * Get Product Id
          */
         $productId = $product->getEntityId ();
         $data = array (
                 'propertyapproved' => 0,
                 'status' => 0
         );
         $model = Mage::getModel ( 'catalog/product' )->load ( $productId )->addData ( $data );
         $model->setId ( $productId )->save ();
     }
 }
 /**
  * Catalog save before based product type
  * @param number customer id
  * @param email Customer email
  * @return boolean
  */
 public function catalogProductSaveBefore($productType,$product){
     /**
      * Check $productType has been set to 'property'
      */
     if ($productType == 'property') {
         /**
          * Getting Product's user Id
          */
         $customerId = $product->getUserid ();
         /**
          * Loading customer Details
          */
         $customer = Mage::getModel ( 'customer/customer' )->load ( $customerId );
         /**
          * checking empty or not
          */
         if ($customer->getId () == '') {
             $product->setCanSaveCustomOptions ( true );
             Mage::throwException ( Mage::helper ( 'adminhtml' )->__ ( 'User id was invalid' ) );
         }
     }
 }
 /**
  * Function to clear cart session
  */
 public function clearCart(){
     Mage::getSingleton ( 'checkout/cart' )->truncate ()->save ();
     Mage::getSingleton ( 'checkout/session' )->clear ();
 }
 /**
  * Function name: getProductSaveDetails()
  */
 public function getProductAttributeSave($product,$data){
     if (isset ( $data [114] )) {
         $product->setState ( $data [114] );
     }
     if (isset ( $data [115] )) {
         $product->setLatitude ( $data [115] );
     }
     if (isset ( $data [116] )) {
         $product->setLongitude ( $data [116] );
     }
     if (isset ( $data [117] )) {
         $product->setBedType ( $data [117] );
     }
     if (isset ( $data [118] )) {
         $product->setPets ( $data [118] );
     }
     if (isset ( $data [121] )) {
         $product->setTaxClassId ( $data [121] );
     }
     if (isset ( $data [122] )) {
         $product->setBedRooms ( $data [122] );
     }
     if (isset ( $data [123] )) {
         $product->setVideoUrl ( $data [123] );
     }
     if (isset ( $data [124] )) {
         $product->setVideoType ( $data [124] );
     }
     return $product;
 }
 /**
  * Function Name: getOrder
  *
  * return $product
  */
 public function getProductSaveInfo($product, $data) {
     $product->setColor ( $data [11] );
     $product->setCost ( $data [12] );
     $product->setCountry ( $data [13] );
     $product->setCountryOfManufacture ( $data [14] );
     $product->setCreatedAt ( $data [15] );
     $product->setDescription ( $data [20] );
     $product->setEnableGooglecheckout ( $data [21] );
     $product->setGallery ( $data [22] );
     $product->setGiftMessageAvailable ( $data [23] );
     $product->setHasOptions ( $data [24] );
     $product->setHostemail ( $data [25] );
     $product->setHouserule ( $data [26] );
     $product->setImage ( $data [27] );
     $product->setImageLabel ( $data [28] );
     $product->setMediaGallery ( $data [30] );
     $product->setMinimalPrice ( $data [34] );
     $product->setMsrp ( $data [35] );
     $product->setMsrpDisplayActualPriceType ( $data [36] );
     $product->setMsrpEnabled ( $data [37] );
     $product->setName ( $data [38] );
     $product->setNewsFromDate ( strtotime ( $data [39] ) );
     $product->setNewsToDate ( '' );
     if (isset ( $data [41] )) {
         $product->setOptionsContainer ( $data [41] );
     }
     if (isset ( $data [43] )) {
         $product->setPrice ( $data [43] );
     }
     if (isset ( $data [44] )) {
         $product->setPrivacy ( $data [44] );
     }
     if (isset ( $data [45] )) {
         $product->setPropertyadd ( $data [45] );
     }
     if (isset ( $data [46] )) {
         $product->setPropertytype ( $data [46] );
     }
     if (isset ( $data [47] )) {
         $product->setPropertyWebsite ( $data [47] );
     }
     if (isset ( $data [48] )) {
         $product->setRequiredOptions ( $data [48] );
     }
     if (isset ( $data [49] )) {
         $product->setShortDescription ( $data [49] );
     }
     if (isset ( $data [50] )) {
         $product->setSmallImage ( $data [50] );
     }
     if (isset ( $data [51] )) {
         $product->setSmallImageLabel ( $data [51] );
     }
     if (isset ( $data [52] )) {
         $product->setSpecialFromDate ( $data [52] );
     }
     if (isset ( $data [53] )) {
         $product->setSpecialPrice ( $data [53] );
     }
     if (isset ( $data [54] )) {
         $product->setSpecialToDate ( $data [54] );
     }
     if (isset ( $data [55] )) {
         $product->setStatus ( $data [55] );
     }
     if (isset ( $data [56] )) {
         $product->setTaxClassId ( $data [56] );
     }
     if (isset ( $data [57] )) {
         $product->setThumbnail ( $data [57] );
     }    
     return $product;
 }
 /**
  * Function name: getProductSaveDetails()
  */
 public function getProductSaveDetails($product,$data){
     if (isset ( $data [58] )) {
         $product->setThumbnailLabel ( $data [58] );
     }
     if (isset ( $data [59] )) {
         $product->setTotalrooms ( $data [59] );
     }
     if (isset ( $data [60] )) {
         $product->setUpdatedAt ( $data [60] );
     }
     if (isset ( $data [61] )) {
         $product->setUrlKey ( $data [61] );
     }
     if (isset ( $data [62] )) {
         $product->setUrlPath ( $data [62] );
     }
     if (isset ( $data [4] )) {
         $product->setVisibility ( 4 );
     }
     if (isset ( $data [65] )) {
         $product->setWeight ( $data [65] );
     }
     if (isset ( $data [66] )) {
         $product->setQty ( $data [66] );
     }
     if (isset ( $data [67] )) {
         $product->setMinQty ( $data [67] );
     }
     if (isset ( $data [68] )) {
         $product->setUseConfigMinQty ( $data [68] );
     }
     if (isset ( $data [69] )) {
         $product->setIsQtyDecimal ( $data [69] );
     }
     if (isset ( $data [70] )) {
         $product->setBackorders ( $data [70] );
     }
     if (isset ( $data [71] )) {
         $product->setUseConfigBackorders ( $data [71] );
     }
     if (isset ( $data [72] )) {
         $product->setMinSaleQty ( $data [72] );
     }
     if (isset ( $data [73] )) {
         $product->setUseConfigMinSaleQty ( $data [73] );
     }
     if (isset ( $data [74] )) {
         $product->setMaxSaleQty ( $data [74] );
     }
     if (isset ( $data [75] )) {
         $product->setUseConfigMaxSaleQty ( $data [75] );
     }
     if (isset ( $data [76] )) {
         $product->setIsInStock ( $data [76] );
     }
     return $product;
 }
 /**
  * Function to change the style based on date blocked
  * Param day,blocked day,special arrival day array
  */
 public function styleChanges($d,$speAvailArray,$blocked){
     if (in_array ( $d, $speAvailArray ) && ! in_array ( $d, $blocked )) {
         $style = "style='background-color:#65AA5F;'";
     } else {
         $style = "style='background-color:#FFFF00;cursor:pointer;'";
     }
     return $style;
 }
 /**
  * Function to add wishlist items to account
  * @param unknown $requestParams
  * @param unknown $wishlist
  * @param unknown $productId
  */
 public function wishlistAdd($requestParams,$wishlist,$productId){
     $response = array();
     if (! $productId) {
         /**
          * Set product not fount error message.
          */
         $response ['status'] = 'ERROR';
         $response ['message'] = $this->__ ( 'Product Not Found' );
     } else {
         /**
          * Get product collection
          * @var $product
          */
         $product = Mage::getModel ( 'catalog/product' )->load ( $productId );
         if (! $product->getId () || ! $product->isVisibleInCatalog ()) {
             /**
              * Set error message.
              */
             $response ['status'] = 'ERROR';
             $response ['message'] = $this->__ ( 'Cannot specify product.' );
         } else {
             try {
                 /**
                  * Get parameters.
                  */
                 $buyRequest = new Varien_Object ( $requestParams );
                 /**
                  * Whislist add new product.
                  */
                 $result = $wishlist->addNewItem ( $product, $buyRequest );
                 if (is_string ( $result )) {
                     Mage::throwException ( $result );
                 }
                 $wishlist->save ();
                 Mage::dispatchEvent ( 'wishlist_add_product', array (
                         'wishlist' => $wishlist,
                         'product' => $product,
                         'item' => $result
                 ) );
                 /**
                  * Set success message.
                  */
                 Mage::helper ( 'wishlist' )->calculate ();
                 $message = $product->getName ().' has been added to your wishlist.';
                 $response ['status'] = 'SUCCESS';
                 $response ['message'] = $message;
                 /**
                  * Unset registry.
                  */
                 Mage::unregister ( 'wishlist' );
                 return $response;
             } catch ( Mage_Core_Exception $e ) {
                 /**
                  * Set error message.
                  */
                 $response ['status'] = 'ERROR';
                 $response ['message'] = $this->__ ( 'An error occurred while adding item to wishlist: %s', $e->getMessage () );
                 return $response;
             }
         }
     }
  }
  /**
   * Function to set customer contact details
   * @param unknown $contact
   * @param unknown $dataCustomerArray
   * @param unknown $smsEnabled
   */
  public function setContactNumber($contact,$dataCustomerArray,$smsEnabled,$collection){
      if (isset ( $contact )) {
          if(isset($smsEnabled)){
              $collection->setContactNumber ( $dataCustomerArray );
          }else{
              $collection->setContactNumber ( $contact );
          }
      }
      return $collection;
  }
  /**
   * Function to insert values into calendar table
   * @param unknown $flagValue
   * @param unknown $calendarTableArray
   */
  public function insertIntoCalenderTable($flagValue,$calendarTableArray,$connection,$coreResource){
      if (count ( $flagValue ) > 1) {
          /**
           * Insert into tables.
           */
          $connection->insert ( $coreResource->getTableName ( 'airhotels_calendar' ), array (
                  'product_id' => $calendarTableArray['0'],
                  'book_avail' => $calendarTableArray['1'],
                  'month' => $calendarTableArray['2'],
                  'year' => $calendarTableArray['3'],
                  'blockfrom' => $calendarTableArray['4'],
                  'price' => $calendarTableArray['5'],
                  'created' => now (),
                  'updated' => now (),
                  'blocktime' => $calendarTableArray['6'],
                  'google_calendar_event_uid' => 'ownsite'
          ) );
      }
  }
     /**
      * Fucntion date range array
      * @param unknown $orderItemTable
      * @param unknown $productid
      * @param unknown $dealstatus
      * @return range[]
      */
      public function dateRangeArray($orderItemTable,$productid,$dealstatus){
          $range = array();
          /**
           * get collections from airhotels table
           * @var unknown
           */
          $dateRange = Mage::getModel ( 'airhotels/airhotels' )->getCollection ()->addFieldToSelect ( array (
                  'entity_id',
                  'fromdate',
                  'todate',
                  'order_id',
                  'order_item_id'
          ) )->addFieldToFilter ( 'order_status', array (
                  'eq' => '1'
          ) );
          /**
           * add field to filter for order status
           * as 1
           */
          /**
           * join two tables as sales_flat_order
           * and the previous collection
           */
          $dateRange->getSelect ()->join ( array (
                  'sales_flat_order' => $orderItemTable
          ), "(sales_flat_order.entity_id = main_table.order_item_id AND main_table.entity_id = $productid  AND (sales_flat_order.status='$dealstatus[1]' OR sales_flat_order.status='$dealstatus[0]'))", array () );
          foreach ( $dateRange as $dateRan ) {
              $range [] = $dateRan;
          }
          /**
           * get the ranges from the collections 
           */
          /**
           * return the range array from collections
           */
          return $range;
      }
      /**
       * Function to set To value in core session
       * @param unknown $subCycle
       * @param unknown $to
       * @param unknown $dateCountFromDate
       */
      public function setToCore($subCycle,$to,$dateCountFromDate){
          if ($subCycle != 'undefined') {
              $to = $dateCountFromDate;
              Mage::getSingleton ( 'core/session' )->setTo ( $to );
          }
          return $to;
      }
      /**
       * Fucntion to set the totat
       * for the average value
       * @param unknown $av
       * @param unknown $avPrice
       * @param unknown $hourlyEnabledOrNot
       * @param unknown $propertyTimeData
       * @param unknown $propertyTime
       * @param unknown $dayCountForOvernightFee
       * @return number
       */
      public function setTotalFromAv($av,$avPrice,$hourlyEnabledOrNot,$propertyTimeData,$propertyTime,$dayCountForOvernightFee){
          /**
           * Declare total as 0
           * @var unknown
           */
          $total = 0;
          /**
           * foreach the av array
           */
          foreach ( $av as $key => $av1 ) {
              /**
               * foreach the av1 array
               */
              foreach ( $av1 as $avkey => $av2 ) {
                  if (! empty ( $avPrice [$key] [$avkey] )) {
                      if ($propertyTime == $propertyTimeData && $hourlyEnabledOrNot == 0) {
                          $dayCountForOvernightFee = $dayCountForOvernightFee + 1;
                      }
                      /**
                       * set total if condition satisfies
                       * @var unknown
                       */
                      $total = $total + $avPrice [$key] [$avkey];
                  } else {
                      /**
                       * set total if condition not satisfies
                       * @var unknown
                       */
                      $total = $total + $av [$key] [$avkey];
                  }
              }
          }
          return $total;
      }
      /**
       * Function to render HTML view of calendar view action
       * @param unknown $arrayHtmlElement
       */
      public function htmlElementCalenderView($arrayHtmlElement){
          /**
           * Assign array elements to corresponding variables
           * @var unknown
           */
          $i = $arrayHtmlElement['i'];
          $st = $arrayHtmlElement['st'];
          $d = $arrayHtmlElement['d'];
          $totaldays = $arrayHtmlElement['totaldays'];
          $year = $arrayHtmlElement['year'];
          $x = $arrayHtmlElement['x'];
          $htmlElementValue = $arrayHtmlElement['htmlElementValue'];
          $date = $arrayHtmlElement['date'];
          $partiallyBookedArray = $arrayHtmlElement['partiallyBookedArray'];
          $propertyTime = $arrayHtmlElement['propertyTime'];
          $propertyTimeData = $arrayHtmlElement['propertyTimeData'];
          $hourlyEnabledOrNot = Mage::helper ( 'airhotels/product' )->getHourlyEnabledOrNot ();
          $blocked = $arrayHtmlElement['blocked'];
          $speAvailArray = $arrayHtmlElement['speAvailArray'];
          $notAvail = $arrayHtmlElement['notAvail'];
          $_sp = $arrayHtmlElement['_sp'];          
          /**
           * Check condition for total days
           */
          if ($i >= $st && $d <= $totaldays) {
              if (strtotime ( "$year-$x-$d" ) < strtotime ( date ( "Y-n-j" ) )) {
                  $htmlElementValue = $htmlElementValue . "<td align='center' class='previous days '><font size = '2' face = 'tahoma'>$d</font></td>";
              } else {
                  $date = strtotime ( "$year/$x/$d" );
                  $tdDate = 'tdId' . '_' . date ( "m/d/Y", $date );
                  if (in_array ( $d, $partiallyBookedArray ) && $propertyTime == $propertyTimeData && $hourlyEnabledOrNot == 0) {
                      $style = $this->styleChanges($d,$speAvailArray,$blocked);
                      $htmlElementValue = $htmlElementValue . "<td id=" . $tdDate . " class='normal days " . $d . " host_calendar_hourly_partially_avail' align='center' " . $style . "><font size = '2' face = 'tahoma'>$d</font></td>";
                  } else if (in_array ( $d, $notAvail )) {
                      $htmlElementValue = $htmlElementValue . "<td id=" . $tdDate . " class='normal days " . $d . " ' align='center'style='background-color:#F18200;color: black !important;' ><font size = '2' face = 'tahoma'>$d</font></td>";
                  } else if (array_key_exists ( $d, $_sp )) {
                      $htmlElementValue = $htmlElementValue . "<td style='background-color:#65AA5F;padding: 11px 23px;' id=" . $tdDate . " class='normal days " . $d . " ' align='center' ><font size = '2' face = 'tahoma'>$d</font><br><div style='width: 25px;font-size: 1.0em;text-align: right;'>" . Mage::app ()->getLocale ()->currency ( Mage::app ()->getStore ()->getCurrentCurrencyCode () )->getSymbol () . Mage::helper ( 'directory' )->currencyConvert ( $_sp [$d], Mage::app ()->getStore ()->getBaseCurrencyCode (), Mage::app ()->getStore ()->getCurrentCurrencyCode () ) . "</div></td>";                  
                  } else if (in_array ( $d, $blocked )) {
                      $htmlElementValue = $htmlElementValue . "<td id=" . $tdDate . " class='normal days " . $d . " ' align='center' style='background-color:#E07272;'><font size = '2' face = 'tahoma'>$d</font></td>";
                  } else {
                      $htmlElementValue = $htmlElementValue . "<td id=" . $tdDate . " class='normal days " . $d . " ' align='center' ><font size = '2' face = 'tahoma'>$d</font></td>";
                  }
              }              
          } else {
              $htmlElementValue = $htmlElementValue . "<td>&nbsp</td>";
          }
          /**
           * return html element value
           */          
          return $htmlElementValue;
      }
}