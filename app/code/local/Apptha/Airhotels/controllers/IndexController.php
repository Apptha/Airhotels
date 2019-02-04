<?php
/**
 * Apptha
 * NOTICE OF LICENSE
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.apptha.com/LICENSE.txt
 * ==============================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * ==============================================================
 * This package designed for Magento COMMUNITY edition
 * Apptha does not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * Apptha does not provide extension support in case of
 * incorrect edition usage.
 * ==============================================================
 * @category    Apptha
 * @package     Apptha_Airhotels
 * @version     0.2.9
 * @author      Apptha Team <developers@contus.in>
 * @copyright   Copyright (c) 2014 Apptha. (http://www.apptha.com)
 * @license     http://www.apptha.com/LICENSE.txt
 *
 */
class Apptha_Airhotels_IndexController extends Mage_Core_Controller_Front_Action {
    public function indexAction() {
        $this->loadLayout ();
        /**
         * Set the Initial Layout Message
        */
        $this->_initLayoutMessages ( 'customer/session' );
        $this->_initLayoutMessages ( 'catalog/session' );
        $this->renderLayout ();
    }
    /**
     * Function Name: 'viewAction'
     * Function is used to view the product details in the page
     */
    public function viewAction() {
        /**
         * Get the ID Value
         */
        $id = ( int ) Mage::app ()->getRequest ()->getParam ( 'id' );
        /**
         * Declaring the empty array to "$productIds"
         */
        $productIds = array ();
        $productIds [0] = $id;
        /**
         * Save the product ID into Session
         */
        Mage::getSingleton ( 'core/session' )->setProductIds ( $productIds );
        /**
         * Load the Layout
         */
        $this->loadLayout ();
        if (isset ( $id )) {
            /**
             * Getting product model
             */
            $model = Mage::getModel ( 'catalog/product' );
            /**
             * Load the Id to the 'catalog/product' Model
             */
            $_product = $model->load ( $id );
            /**
             * Property in disabled model
             */
            if ($_product->getStatus () != 1 || $_product->getPropertyapproved () != 1) {
                /**
                 * Adding the Error Notification
                 */
                Mage::getSingleton ( 'core/session' )->addError ( $this->__ ( 'Property not found' ) );
                $this->_redirectUrl ( Mage::getBaseUrl () );
                return;
            }
            /**
             * Check weather the Product Meta Tile is enabled
             * or otherwise set hte default Title
             */
            if ($_product->getMetaTitle ()) {
                /**
                 * Get the layout for Block of 'head'
                 */
                $this->getLayout ()->getBlock ( 'head' )->setTitle ( htmlspecialchars ( $_product->getMetaTitle () ) );
            } else {
                $this->getLayout ()->getBlock ( 'head' )->setTitle ( htmlspecialchars ( $_product->getName () ) );
            }
            /**
             * create the layout for Keywords
             */
            $this->getLayout ()->getBlock ( 'head' )->setKeywords ( htmlspecialchars ( $_product->getMetaKeyword () ) );
            /**
             * create the layout for description file.
             */
            $this->getLayout ()->getBlock ( 'head' )->setDescription ( htmlspecialchars ( $_product->getMetaDescription () ) );
        }
        /**
         * Rendering the Layout
         */
        $this->renderLayout ();
    }
    /**
     * Update host profile more option
     */
    public function updatemoreAction() {
        $data = $this->getRequest ()->getPost ();
        $customerId = Mage::getSingleton ( 'customer/session' )->getId ();
        $customerData = Mage::getModel ( 'customer/customer' )->load ( $customerId );
        $customerPhotoCollection = Mage::getModel ( 'airhotels/customerphoto' )->load ( $customerId, 'customer_id' );
        $uploadsData = new Zend_File_Transfer_Adapter_Http ();
        $filesDataArray = $uploadsData->getFileInfo ();
        $smsEnabledOrNot = Mage::helper ( 'airhotels/smsconfig' )->getSmsEnabledOrNot ();
        if ((isset ( $data ["edit_profile"] )) && $smsEnabledOrNot == 0) {
            $verifyStatus = $customerPhotoCollection->getMobileVerifiedProfile ();
            if ($verifyStatus != "verified") {
                Mage::getSingleton ( 'core/session' )->addError ( $this->__ ( 'You need to verify your phone number.' ) );
                return $this->_redirectUrl ( Mage::helper ( 'airhotels/url' )->getProfileUrl () );
            }
        }
        $name = $emailId = $city = $country = '';
        $notification = $responseTime = $moreHost = '';
        $country = $data ['country'];
        $city = $data ['city'];
        $dob = date ( "m/d/Y", strtotime ( $data ['day'] . $data ['month'] . $data ['year'] ) );
        $emergencyContact = array (
                "emergencyContactName" => $data ['emergencyContactName'],
                "emergencyContactPhone" => $data ['emergencyContactPhone'],
                "emergencyContactEmail" => $data ['emergencyContactEmail'],
                "emergencyContactRelationship" => $data ['emergencyContactRelationship'] 
        );
        if (isset ( $data ['contact'] )) {
            $contact = $data ['contact'];
        }
        $dataCustomerArray = array (
                'contact_number' => $contact,
                "isd_code" => '',
                "country_code" => '' 
        );
        $dataCustomerArray = serialize ( $dataCustomerArray );
        $emergencyContact = serialize ( $emergencyContact );  
        if($smsEnabledOrNot == 1){
            $tempContact =  $data ['contact'];
        }else{
            $tempContact = $data ['emergencyContactCheck'];
        }
        if ($data ['emergencyContactPhone'] != $tempContact) {           
            if (isset ( $data ['languages'] )) {
                $language = $data ['languages'];
                $language = implode ( ",", $language );
            }
            $name = $customerData->getName ();
            $emailId = $customerData->getEmail ();
            $uploadsData = new Zend_File_Transfer_Adapter_Http ();
            $filesDataArray = $uploadsData->getFileInfo ();
            if (isset ( $data ['notification'] )) {
                $notification = 1;
                Mage::getModel ( 'newsletter/subscriber' )->subscribe ( $customerData->getEmail () );
            }
            if (isset ( $data ['responseTime'] )) {
                $responseTime = $data ['responseTime'];
            }
            $moreHost = $data ['moreAboutHost'];
            $customer = Mage::getModel ( 'customer/customer' )->load ( $customerId );
            $customer->setWebsiteId ( Mage::app ()->getWebsite ()->getId () );
            $customer->setFirstname ( $data ['firstName'] );
            $customer->setLastname ( $data ['lastName'] );
            $customer->setGender ( $data ['gender'] );
            $customer->setDob ( $dob );
            $customer->save ();
            $collection = Mage::getModel ( 'airhotels/customerphoto' )->load ( $customerId, 'customer_id' );
            $getId = $collection->getId ();
            $returnValue = true;
            try {
                if ($getId) {
                    $collection = Mage::getModel ( 'airhotels/customerphoto' )->load ( $customerId, 'customer_id' );
                    $collection->setCustomerId ( $customerId );
                    if (! empty ( $imagesPath )) {
                        $collection->setDocumentUrl ( $imagesPath );
                    }
                    $collection->setCountry ( $country );
                    $collection = Mage::getModel ( 'airhotels/search' )->setContactNumber ( $contact, $dataCustomerArray, $data ['sms_enabled'],$collection );
                    $collection->setTimeZone ( $data ['time_zone'] );
                    $collection->setCity ( $city );
                    $collection->setResponseTime ( $responseTime );
                    $collection->setMoreHost ( $moreHost );
                    $collection->setName ( $name );
                    $collection->setEmailId ( $emailId );
                    if (isset ( $language )) {
                        $collection->setLanguage ( $language );
                    }
                    $collection->setSchool ( $data ['school'] );
                    $collection->setWork ( $data ['work'] );
                    $collection->setEmergencyContact ( $emergencyContact );
                    $collection->setCreatedDate ( date ( "m/d/Y H:i:s" ) );
                    $collection->save ();
                    Mage::getSingleton ( 'core/session' )->addSuccess ( $this->__ ( 'Your profile information is saved successfully' ) );
                    if (isset ( $data ['abouthost'] )) {
                        $this->_redirect ( 'airhotels/invitefriends/abouthost' );
                    } elseif (isset ( $data ['photo'] )) {
                        $this->_redirect ( 'property/property/uploadphoto' );
                    } else {
                        $this->_redirectUrl ( Mage::helper ( 'airhotels/invitefriends' )->getProfileUrl () );
                    }                    
                } else {
                    $data = array (
                            'customer_id' => $customerId,
                            'document_url' => $imagesPath,
                            'country' => $country,
                            'city' => $city,
                            'notification' => $notification,
                            'response_time' => $responseTime,
                            'more_host' => $moreHost,
                            'time_zone' => $data ['time_zone'],
                            'video_verified' => 0,
                            'name' => $name,
                            'email_id' => $emailId,
                            'language' => $language,
                            'school' => $data ['school'],
                            'work' => $data ['work'],
                            'emergency_contact' => $emergencyContact 
                    );
                    $collection = Mage::getModel ( 'airhotels/customerphoto' )->setData ( $data );
                    $collection->save ()->getId ();
                    Mage::getSingleton ( 'core/session' )->addSuccess ( $this->__ ( 'Your profile information is saved successfully' ) );
                    if (isset ( $data ['abouthost'] )) {
                        $this->_redirect ( 'airhotels/invitefriends/abouthost' );
                    } elseif (isset ( $data ['photo'] )) {
                        $this->_redirect ( 'property/property/uploadphoto' );
                    } else {
                        $this->_redirectUrl ( Mage::helper ( 'airhotels/invitefriends' )->getProfileUrl () );
                    }                    
                }
                return $returnValue;
            } catch ( Exception $e ) {
                Mage::getSingleton ( 'core/session' )->addError ( $this->__ ( $e->getMessage () ) );
                $this->_redirectUrl ( Mage::helper ( 'airhotels/invitefriends' )->getProfileUrl () );
            }
        } else {
            Mage::getSingleton ( 'core/session' )->addError ( $this->__ ( 'Phone number and Emergency contact number should be different.' ) );
            return $this->_redirectUrl ( Mage::helper ( 'airhotels/url' )->getProfileUrl () );
        }
    }
    /**
     * Getting host profile details
     */
    public function profileAction() {
        /**
         * get the CustomerID Valeu.
         */
        $customerid = Mage::app ()->getRequest ()->getParam ( 'id' );
        /**
         * Get the Customer Info by loading the customerID
         */
        $customer = Mage::getModel ( 'customer/customer' )->load ( $customerid );
        /**
         * Cust Name.
         */
        $customerName = $customer->getName ();
        /**
         * loading the layout.
         */
        $this->loadLayout ();
        /**
         * Set Title to the block
         */
        $this->getLayout ()->getBlock ( 'head' )->setTitle ( $this->__ ( $customerName ) );
        /**
         * Render the Layout
         */
        $this->renderLayout ();
    }
    /**
     * Default index action (with 404 Not Found headers)
     * Used if default page don't configure or available
     */
    public function noRouteAction($coreRoute = null) {
        $coreRoute = '';
        /**
         * GetResponce Value.
         */
        $this->getResponse ()->setHeader ( 'HTTP/1.1', '404 Not Found' );
        /**
         * Set Response Vlaue.
         */
        $this->getResponse ()->setHeader ( 'Status', '404 File not found' );
        /**
         * page ID Value.
         */
        $pageId = Mage::getStoreConfig ( Mage_Cms_Helper_Page::XML_PATH_NO_ROUTE_PAGE );
        if (! Mage::helper ( 'cms/page' )->renderPage ( $this, $pageId )) {
            /**
             * Redirecting the loop.
             */
            $this->_forward ( 'defaultNoRoute' );
        }
    }
    /**
     * Default no route page action
     * Used if no route page don't configure or available
     */
    public function defaultNoRouteAction() {
        /**
         * get Response from header
         */
        $this->getResponse ()->setHeader ( 'HTTP/1.1', '404 Not Found' );
        /**
         * get Response from header sataus
         */
        $this->getResponse ()->setHeader ( 'Status', '404 File not found' );
        /**
         * Load the Layout
         */
        $this->loadLayout ();
        /**
         * Rendering the Layout Values.
         */
        $this->renderLayout ();
    }
    
    /**
     * Render Disable cookies page
     */
    public function noCookiesAction() {
        /**
         * get the page ID Vlaue.
         */
        $pageId = Mage::getStoreConfig ( Mage_Cms_Helper_Page::XML_PATH_NO_COOKIES_PAGE );
        if (! Mage::helper ( 'cms/page' )->renderPage ( $this, $pageId )) {
            $this->_forward ( 'defaultNoCookies' );
        }
    }
    /**
     * No cookies function
     */
    public function defaultNoCookiesAction() {
        $this->loadLayout ();
        $this->renderLayout ();
    }
    /**
     * Initilize sample data functionality
     */
    public function addAction() {
        $row = 0;
        $file = "skin/frontend/default/stylish/sample_data/catalog_product.csv";
        $csv = new Varien_File_Csv ();
        $csv->setLineLength ( 1000 );
        $datas = $csv->getData ( $file );
        if (count ( $datas ) > 1) {
            /**
             * Getting Property host Id
             */
            $customerId = $this->getSampleDataHostId ();
        }
        /**
         * Itearating loop
         */
        foreach ( $datas as $data ) {
            $row ++;
            if ($row == 1) {
                continue;
            }
            /**
             * Get attribute Property Type
             */
            $attribute = Mage::getModel ( 'eav/config' )->getAttribute ( 'catalog_product', 'propertytype' );
            if (isset ( $data [46] )) {
                foreach ( $attribute->getSource ()->getAllOptions ( true ) as $propertytypeOption ) {
                    if ($propertytypeOption ['label'] == $data [46]) {
                        $data [46] = $propertytypeOption ['value'];
                        break;
                    }
                }
            }
            /**
             * Get attribute Privacy
             */
            if (isset ( $data [44] )) {
                $attribute = Mage::getModel ( 'eav/config' )->getAttribute ( 'catalog_product', 'privacy' );
                foreach ( $attribute->getSource ()->getAllOptions ( true ) as $privacyOption ) {
                    if ($privacyOption ['label'] == $data [44]) {
                        $data [44] = $privacyOption ['value'];
                        break;
                    }
                }
            }
            /**
             * Get attribute cancellation policy
             */
            if (isset ( $data [9] )) {
                $attribute = Mage::getModel ( 'eav/config' )->getAttribute ( 'catalog_product', 'cancelpolicy' );
                foreach ( $attribute->getSource ()->getAllOptions ( true ) as $canceloption ) {
                    if ($canceloption ['label'] == $data [9]) {
                        $data [9] = $canceloption ['value'];
                        break;
                    }
                }
            }
            /**
             * Get attribute amenity
             */
            if (isset ( $data [8] )) {
                $attribute = Mage::getModel ( 'eav/config' )->getAttribute ( 'catalog_product', 'amenity' );
                foreach ( $attribute->getSource ()->getAllOptions ( true ) as $amenityOption ) {
                    if ($amenityOption ['label'] == $data [8]) {
                        $data [8] = $amenityOption ['value'];
                        break;
                    }
                }
            }
            $result = Mage::helper('airhotels/vieworder')->getAttributeDetails($data);
            $data [117] = $result[1];
            $data [122] = $result[2];
            $data [124] = $result[3];            
            /**
             * Add Sample Data
             */
            $this->addSampleData ( $data, $customerId );
        }
        $this->getSampleorderId ();
        $this->addCityImages ();
        Mage::getSingleton ( 'core/session' )->addSuccess ( $this->__ ( 'Sample Data Added Successfully' ) );
        $this->_redirectUrl ( Mage::getBaseUrl () );
    }
    /**
     * Adding Sample data functionality
     */
    public function addSampleData($data, $customerId) {
        $categories = array ();
        /**
         * Get Product Details
         */
        $product = Mage::getModel ( 'catalog/product' );
        $product->setSku ( $data [0] );
        $product->setAccomodates ( $data [7] );
        $product->setAmenity ( $data [8] );
        $product->setCancelpolicy ( $data [9] );
        $product->setCity ( $data [10] );        
        /**
         * Set product Details
         */
        $product = Mage::getModel ( 'airhotels/search' )->getProductAttributeSave ( $product, $data );
        $product = Mage::getModel ( 'airhotels/search' )->getProductSaveInfo ( $product, $data );
        $product = Mage::getModel ( 'airhotels/search' )->getProductSaveDetails ( $product, $data );
        $product->setUserid ( $customerId );
        $product->setAttributeSetId ( Mage::getModel ( 'catalog/product' )->getResource ()->getEntityType ()->getDefaultAttributeSetId () );
        /**
         * need to look this up
         */
        if (isset ( $data [0] ) && isset ( $categories [$data [0]] )) {
            $product->setCategoryIds ( array (
                    $categories [$data [0]] 
            ) );
        /**
         * need to look these up
         */
        }
        $product->setManufacturer ( '' );
        $product->setTypeId ( 'property' );
        $product->setBanner ( 1 );
        $product->setPropertyapproved ( 1 );
        /**
         * approved, assign product to the default website
         */
        $product->setWebsiteIds ( array (
                Mage::app ()->getStore ( true )->getWebsite ()->getId () 
        ) );
        $product->setStoreId ( 0 );
        $stockData = $product->getStockData ();
        if (isset ( $data [67] )) {
            $stockData ['qty'] = $data [67];
        }
        if (isset ( $data [77] )) {
            $stockData ['is_in_stock'] = $data [77] == "In Stock" ? 1 : 0;
        }
        $stockData ['manage_stock'] = 0;
        $stockData ['use_config_manage_stock'] = 0;
        $product->setStockData ( array (
                'is_in_stock' => 1,
                'qty' => 100000 
        ) );
        $propertyTime = '';
        if (isset ( $data [108] )) {
            if ($data [108] == 'Hourly') {
                $propertyTime = Mage::helper ( 'airhotels/airhotel' )->getPropertyTimeLabelByOptionId ();
            } else {
                $propertyTime = Mage::helper ( 'airhotels/general' )->getPropertyDailyLabelByOptionId ();
            }
        }
        $product->setPropertyTime ( $propertyTime );
        if (isset ( $data [109] )) {
            $product->setPropertyOvernightFee ( $data [109] );
        }
        if (isset ( $data [110] )) {
            $product->setPropertyServiceFromTime ( $data [110] );
        }
        if (isset ( $data [111] )) {
            $product->setPropertyServiceToTime ( $data [111] );
        }
        if (isset ( $data [112] )) {
            $product->setPropertyMaximum ( $data [112] );
        }
        if (isset ( $data [113] )) {
            $product->setPropertyMinimum ( $data [113] );
        }
        if (isset ( $data [119] )) {
            $product->setThumbnail ( $data [119] )->setImage ( $data [119] )->setSmallImage ( $data [119] );
            /**
             * Add three image sizes to media gallery
             */
            $mediaArray = array (
                    'image' 
            );
            /**
             * Remove unset images, add image to gallery if exists
             */
            $importDir = Mage::getBaseDir ( 'media' ) . DS . 'catalog/product' . $data [119];
            
            if (file_exists ( $importDir )) {
                $product->addImageToMediaGallery ( $importDir, $mediaArray, false, false );
            }
        }
        try {
            $product->save ();
            /**
             * Save all 'latitude' and 'longitude' values
             */
            $entityId = $product->getId ();
            $dataLattitude = array (
                    'latitude' => $data [115],
                    "longitude" => $data [116],
                    "entity_id" => $entityId 
            );
            $collection = Mage::getModel ( 'airhotels/latitudelongitude' )->setData ( $dataLattitude );
            $collection->save ()->getId ();
            $configModel = Mage::getModel ( 'core/config' );
            $configModel->saveConfig ( 'airhotels/sampledata/enable_data', "1", 'default', 0 );
            Mage::app ()->getCacheInstance ()->flush ();
            $this->_redirectUrl ( Mage::getBaseUrl () );
        } catch ( Exception $ex ) {
            $this->_redirectUrl ( Mage::getBaseUrl () );
            return;
        }
    }
    /**
     * Function NAme: getSampleDataHostId Getting sample data host details
     */
    public function getSampleDataHostId() {
        $arrCustomer = array (
                '0' => array (
                        'email' => 'william@sample.in',
                        'first_name' => 'william',
                        'last_name' => 'Fernandos',
                        'password' => '123456' 
                ),
                '1' => array (
                        'email' => 'john@sample.in',
                        'first_name' => 'John',
                        'last_name' => 'Radburn',
                        'password' => '123456' 
                ) 
        );
        foreach ( $arrCustomer as $value ) {
            $email = $value ['email'];
            /**
             * Get the Website Id
             */
            $websiteId = Mage::app ()->getWebsite ()->getId ();
            /**
             * get the customer model Vlaue.
             */
            $customer = Mage::getModel ( 'customer/customer' );
            if ($websiteId) {
                $customer->setWebsiteId ( $websiteId );
            }
            /**
             * Load the customer by email
             */
            $customer->loadByEmail ( $email );
            /**
             * Check weather the vlaue has been set.
             */
            if (! $customer->getId ()) {
                /**
                 * Call the customers value
                 */
                $customer = Mage::getModel ( "customer/customer" );
                $customer->setWebsiteId ( $websiteId );
                /**
                 * Customer Name.
                 */
                $customer->setStore ( Mage::app ()->getStore () );
                /**
                 * Set Name Value.
                 */
                $customer->setFirstname ( $value ['first_name'] );
                $customer->setLastname ( $value ['last_name'] );
                /**
                 * Set Email Id Value.
                 */
                $customer->setEmail ( $email );
                /**
                 * Set password with hasing
                 */
                $customer->setPasswordHash ( md5 ( $value ['password'] ) );
                /**
                 * Save the customer
                 */
                $customer->save ();
            }
        }
        /**
         * Return the Url Vlaue
         */
        return ( int ) $customer->getId ();
    }
    /**
     * Sample order data for installation process
     */
    public function getSampleorderId() {
        $_catalog = Mage::getModel ( 'catalog/product' );
        $_productId = $_catalog->getIdBySku ( '599513917' );
        for($id = $_productId; $id < ($_productId + 10); $id ++) {
            $websiteId = Mage::app ()->getWebsite ()->getId ();
            $store = Mage::app ()->getStore ();
            /**
             * Start New Sales Order Quote
             */
            $quote = Mage::getModel ( 'sales/quote' )->setStoreId ( $store->getId () );
            /**
             * Set Sales Order Quote Currency
             */
            $quote->setCurrency ( 'USD' );
            $customer = Mage::getModel ( 'customer/customer' )->setWebsiteId ( $websiteId )->loadByEmail ( 'william@sample.in' );
            if ($customer->getId () == "") {
                $customer = Mage::getModel ( 'customer/customer' );
                $customer->setWebsiteId ( $websiteId )->setStore ( $store )->setFirstname ( 'william' )->setLastname ( 'Radburn' )->setEmail ( 'william@sample.in' )->setPassword ( "123456" );
                $customer->save ();
            }
            /**
             * Assign Customer To Sales Order Quote
             */
            $quote->assignCustomer ( $customer );
            /**
             * Configure Notification
             */
            $quote->setSendCconfirmation ( 1 );
            $product = Mage::getModel ( 'catalog/product' )->load ( $id );
            $quote->addProduct ( $product, new Varien_Object ( array (
                    'qty' => 1 
            ) ) );
            /**
             * Set Sales Order Billing Address
             */
            $quote->getBillingAddress ()->addData ( array (
                    'customer_address_id' => '',
                    'prefix' => '',
                    'firstname' => 'william',
                    'middlename' => '',
                    'lastname' => 'Fernandos',
                    'suffix' => '',
                    'company' => '',
                    'street' => array (
                            '0' => 'Noida',
                            '1' => 'Sector 64' 
                    ),
                    'city' => 'Noida',
                    'country_id' => 'IN',
                    'region' => 'UP',
                    'postcode' => '201301',
                    'telephone' => '78676789',
                    'fax' => 'gghlhu',
                    'vat_id' => '',
                    'save_in_address_book' => 1 
            ) );
            /**
             * Set Sales Order Shipping Address
             */
            $shippingAddress = $quote->getShippingAddress ()->addData ( array (
                    'customer_address_id' => '',
                    'prefix' => '',
                    'firstname' => 'john',
                    'middlename' => '',
                    'lastname' => 'Fernandos',
                    'suffix' => '',
                    'company' => '',
                    'street' => array (
                            '0' => 'Noida',
                            '1' => 'Sector 64' 
                    ),
                    'city' => 'Noida',
                    'country_id' => 'IN',
                    'region' => 'UP',
                    'postcode' => '201301',
                    'telephone' => '78676789',
                    'fax' => 'gghlhu',
                    'vat_id' => '',
                    'save_in_address_book' => 1 
            ) );
            /**
             * Collect Rates and Set Shipping & Payment Method
             */
            $shippingAddress->setCollectShippingRates ( true )->collectShippingRates ()->setShippingMethod ( 'flatrate_flatrate' )->setPaymentMethod ( 'checkmo' );
            /**
             * Set Sales Order Payment
             */
            $quote->getPayment ()->importData ( array (
                    'method' => 'checkmo' 
            ) );
            /**
             * Collect Totals & Save Quote
             */
            $quote->collectTotals ()->save ();
            /**
             * Create Order From Quote
             */
            $service = Mage::getModel ( 'sales/service_quote', $quote );
            $service->submitAll ();
            $order = $service->getOrder ();
            $order->setStatus ( 'complete' );
            $order->save ();
            $increment_id = $service->getOrder ()->getRealOrderId ();
            /**
             * Resource Clean-Up
             */
            $quote = $customer = $service = null;
        }
        return $increment_id;
    }
    /**
     * Functio name: getPropertyValuesforPolicy
     */
    public function getPropertyValuesforPolicy($data) {
        $datas = array ();
        $datas [46] = $this->preparePropertyData ( $data, 'propertytype', 46 );
        $datas [44] = $this->preparePropertyData ( $data, 'privacy', 44 );
        $datas [9] = $this->preparePropertyData ( $data, 'cancelpolicy', 9 );
        return $datas;
    }
    /**
     * Function Name: getPropertyValuesforAmenity
     * 
     * @param array $data            
     */
    public function getPropertyValuesforAmenity($data) {
        $datas = array ();
        $datas [8] = $this->preparePropertyData ( $data, 'amenity', 8 );
        $datas [117] = $this->preparePropertyData ( $data, 'bedtype', 117 );
        $datas [118] = $this->preparePropertyData ( $data, 'pets', 118 );
        return $datas;
    }
    /**
     * Function Name : preparePropertyData
     * 
     * @param array $data            
     * @param int $attributeValue            
     * @param int $attributeIndexVal            
     * @return multitype:unknown
     */
    public function preparePropertyData($data, $attributeValue, $attributeIndexVal) {
        $datas = array ();
        /**
         * Getting attribute for property
         */
        $attribute = Mage::getModel ( 'eav/config' )->getAttribute ( 'catalog_product', $attributeValue );
        if (isset ( $data [$attributeIndexVal] )) {
            /**
             * Iterating the loop
             */
            foreach ( $attribute->getSource ()->getAllOptions ( true ) as $option ) {
                if ($option ['label'] == $data [$attributeIndexVal]) {
                    $datas [$attributeIndexVal] = $option ['value'];
                    break;
                }
            }
        }
        return $datas;
    }
    /**
     * Add city image when upload sample data at the time of installation
     */
    public function addCityImages() {
        $row = 0;
        $file = "skin/frontend/default/stylish/sample_data/add_city.csv";
        $csv = new Varien_File_Csv ();
        $csv->setLineLength ( 1000 );
        $datas = $csv->getData ( $file );
        foreach ( $datas as $data ) {
            $row ++;
            if ($row == 1) {
                continue;
            }
            $city = Mage::getModel ( 'airhotels/city' );
            $city->setCity ( $data [0] );
            $city->setCityDescription ( $data [1] );
            $city->setCityImage ( $data [2] );
            $city->setThumbImage ( $data [3] );
            $city->setSmallImage ( $data [4] );
            $city->save ();
        }
    }
}