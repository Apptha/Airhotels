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
class Apptha_Airhotels_Block_Property_View extends Mage_Catalog_Block_Product_Abstract {
    /**
     * Function Name: _prepareLayout
     * Add meta information from product to head block
     *
     * @return Mage_Catalog_Block_Product_View
     */
    protected function _prepareLayout() {
        /**
         * Get Property Name
         */
        $productName = $this->getProduct ()->getName ();
        /**
         * Set page title.
         */
        Mage::app ()->getLayout ()->getBlock ( 'head' )->setTitle ( $productName );
        /**
         * Create bredcrumbs.
         */
        $this->getLayout ()->createBlock ( 'catalog/breadcrumbs' );
        /**
         * Returning the layout values.
         */
        return parent::_prepareLayout ();
    }
    
    /**
     * Function Name: getProduct
     * Retrieve current product model
     *
     * @return Mage_Catalog_Model_Product
     */
    public function getProduct() {
        if (! Mage::registry ( 'product' ) && $this->getProductId ()) {
            /**
             * Get product collection based on product id.
             */
            $product = Mage::getModel ( 'catalog/product' )->load ( $this->getProductId () );
            Mage::register ( 'product', $product );
        }
        return Mage::registry ( 'product' );
    }
    
    /**
     * Function Name: canEmailToFriend
     * Check if product can be emailed to friend
     *
     * @return bool
     */
    public function canEmailToFriend() {
        $sendToFriendModel = Mage::registry ( 'send_to_friend_model' );
        return $sendToFriendModel && $sendToFriendModel->canEmailToFriend ();
    }
    
    /**
     * Function Name: getAddToCartUrl
     * Retrieve url for direct adding product to cart
     *
     * @param Mage_Catalog_Model_Product $product            
     * @param array $additional            
     * @return string
     */
    public function getAddToCartUrl($product, $additional = array()) {
        if ($this->getRequest ()->getParam ( 'wishlist_next' )) {
            $additional ['wishlist_next'] = 1;
        }
        /**
         * Return the 'helper' url.
         */
        return $this->helper ( 'checkout/cart' )->getAddUrl ( $product, $additional );
    }
    
    /**
     * Function Name: 'getJsonConfig'
     * Get JSON encripted configuration array which can be used for JS dynamic
     * price calculation depending on product options
     *
     * @return string
     */
    public function getJsonConfig() {
        $config = array ();
        if (! $this->hasOptions ()) {
            /**
             * Convert result into json Encode format.
             */
            return Mage::helper ( 'core' )->jsonEncode ( $config );
        }
        /**
         * Get the request for '$_request'
         */
        $_request = Mage::getSingleton ( 'tax/calculation' )->getRateRequest ( false, false, false );
        $_request->setProductClassId ( $this->getProduct ()->getTaxClassId () );
        $defaultTax = Mage::getSingleton ( 'tax/calculation' )->getRate ( $_request );
        
        $_request = Mage::getSingleton ( 'tax/calculation' )->getRateRequest ();
        $_request->setProductClassId ( $this->getProduct ()->getTaxClassId () );
        $currentTax = Mage::getSingleton ( 'tax/calculation' )->getRate ( $_request );
        
        $_regularPrice = $this->getProduct ()->getPrice ();
        $_finalPrice = $this->getProduct ()->getFinalPrice ();
        $_priceInclTax = Mage::helper ( 'tax' )->getPrice ( $this->getProduct (), $_finalPrice, true );
        $_priceExclTax = Mage::helper ( 'tax' )->getPrice ( $this->getProduct (), $_finalPrice );
        /**
         * Set the config variables with
         * 'productId'
         * 'priceFormat'
         * 'includeTax'
         * 'showIncludeTax'
         * 'showBothPrices'
         * 'productPrice'
         * 'productOldPrice'
         * 'skipCalculate'
         * 'defaultTax'
         * 'currentTax'
         * 'idSuffix'
         * 'oldPlusDisposition'
         * 'plusDisposition'
         * 'oldMinusDisposition'
         * 'minusDisposition'
         */
        $config = array (
                'productId' => $this->getProduct ()->getId (),
                'priceFormat' => Mage::app ()->getLocale ()->getJsPriceFormat (),
                'includeTax' => Mage::helper ( 'tax' )->priceIncludesTax () ? 'true' : 'false',
                'showIncludeTax' => Mage::helper ( 'tax' )->displayPriceIncludingTax (),
                'showBothPrices' => Mage::helper ( 'tax' )->displayBothPrices (),
                'productPrice' => Mage::helper ( 'core' )->currency ( $_finalPrice, false, false ),
                'productOldPrice' => Mage::helper ( 'core' )->currency ( $_regularPrice, false, false ),
                /**
                 *
                 * @var skipCalculate
                 * @deprecated after 1.5.1.0
                 */
                'skipCalculate' => ($_priceExclTax != $_priceInclTax ? 0 : 1),
                'defaultTax' => $defaultTax,
                'currentTax' => $currentTax,
                'idSuffix' => '_clone',
                'oldPlusDisposition' => 0,
                'plusDisposition' => 0,
                'oldMinusDisposition' => 0,
                'minusDisposition' => 0 
        );
        $responseObject = new Varien_Object ();
        Mage::dispatchEvent ( 'catalog_product_view_config', array (
                'response_object' => $responseObject 
        ) );
        /**
         * Check weather the value in array
         */
        if (is_array ( $responseObject->getAdditionalOptions () )) {
            foreach ( $responseObject->getAdditionalOptions () as $option => $value ) {
                $config [$option] = $value;
            }
        }
        /**
         * Return the json Encode Value.
         */
        return Mage::helper ( 'core' )->jsonEncode ( $config );
    }
    /**
     * Return true if product has options
     *
     * @return bool
     */
    public function hasOptions() {
        if ($this->getProduct ()->getTypeInstance ( true )->hasOptions ( $this->getProduct () )) {
            return true;
        }
        return false;
    }
    
    /**
     * Check if product has required options
     *
     * @return bool
     */
    public function hasRequiredOptions() {
        return $this->getProduct ()->getTypeInstance ( true )->hasRequiredOptions ( $this->getProduct () );
    }
    
    /**
     * Define if setting of product options must be shown instantly.
     * Used in case when options are usually hidden and shown only when user
     * presses some button or link. In editing mode we better show these options
     * instantly.
     *
     * @return bool
     */
    public function isStartCustomization() {
        /**
         * Returning the product
         */
        return $this->getProduct ()->getConfigureMode () || Mage::app ()->getRequest ()->getParam ( 'startcustomization' );
    }
    
    /**
     * Get default qty - either as preconfigured, or as 1.
     * Also restricts it by minimal qty.
     *
     * @param
     *            null|Mage_Catalog_Model_Product
     *            
     * @return int float
     */
    public function getProductDefaultQty($product = null) {
        /**
         * Check weather the Product is not empty
         */
        if (! $product) {
            $product = $this->getProduct ();
        }
        /**
         * Quantity Values.
         */
        $qty = $this->getMinimalQty ( $product );
        $config = $product->getPreconfiguredValues ();
        $configQty = $config->getQty ();
        if ($configQty > $qty) {
            $qty = $configQty;
        }
        /**
         * Return the Quantity Value.
         */
        return $qty;
    }
    
    /**
     * Get the Total Roms
     */
    public function _getHotelRooms() {
    }
    /**
     * get the Review Count Value
     *
     * @return number
     */
    public function getReviewCount() {
        /**
         * get the Colletion of 'review/review' and add filter Values.
         */
        $review = Mage::getModel ( 'review/review' )->getCollection ()->addStoreFilter ( Mage::app ()->getStore ()->getId () )->addStatusFilter ( 'approved' )->addEntityFilter ( 'product', $this->getProduct ()->getId () )->setDateOrder ();
        /**
         * Return review count.
         */
        return intval ( count ( $review ) );
    }
    /**
     * Get booked Date Value
     *
     * @param int $productId            
     */
    public function getbooked_date($productId) {
        /**
         * Return the 'airhotels/Calendar' with date Values.
         */
        returnMage::getModel ( 'airhotels/calendar' )->getdate ( $productId );
    }
    /**
     * Get Quote Edit
     */
    public function _getQuoteToEdit() {
    }
    /**
     * get the Most Rated Property
     */
    public function getMostRatedProperty() {
        /**
         * Get Date Value of 'airhotels/calendar'
         */
        return Mage::getModel ( 'airhotels/calendar' )->getdate ( $productId );
    }
    /**
     * Function Name: getPopularProperty
     * Get Popular Property
     */
    public function getPopularProperty() {
        return Mage::getModel ( 'airhotels/property' )->getPopularProperty ();
    }
    /**
     * Function Name: getRatedProperty
     * Get Rated Property
     */
    public function getRatedProperty() {
        return Mage::getModel ( 'airhotels/property' )->getRatedProperty ();
    }
    /**
     * Function Name: validateFormServiceHours
     * Validate the from service hours
     *
     * @param int $propertyServiceFromPeriodData            
     * @param int $propertyServiceFromData            
     * @return number
     */
    public function validateFormServiceHours($propertyServiceFromPeriodData, $propertyServiceFromData) {
        if ($propertyServiceFromPeriodData == 'PM') {
            /**
             * Check weatherthe Value has not equal to 12
             */
            if ($propertyServiceFromData != 12) {
                $validateFormServiceHours = $propertyServiceFromData + 12;
            } else {
                $validateFormServiceHours = $propertyServiceFromData;
            }
        } else {
            /**
             * Check weatherthe Value has not equal to 12
             */
            if ($propertyServiceFromData != 12) {
                $validateFormServiceHours = $propertyServiceFromData;
            } else {
                $validateFormServiceHours = 0;
            }
        }
        /**
         * Return the 'validateFormServiceHours' Value.
         */
        return $validateFormServiceHours;
    }
    /**
     * Validate the to service hours
     *
     * @param int $propertyServiceToPeriodData            
     * @param int $propertyServiceToData            
     * @return number
     */
    public function validateToServiceHoursFor($propertyServiceToPeriodData, $propertyServiceToData) {
        if ($propertyServiceToPeriodData == 'PM') {
            if ($propertyServiceToData != 12) {
                $validateToServiceHours = $propertyServiceToData + 12;
            } else {
                $validateToServiceHours = $propertyServiceToData;
            }
        } else {
            if ($propertyServiceToData != 12) {
                $validateToServiceHours = $propertyServiceToData;
            } else {
                $validateToServiceHours = 0;
            }
        }
        /**
         * Retun the 'validateToServiceHours' Values
         */
        return $validateToServiceHours;
    }
    /**
     * get the current pagenination
     *
     * @param return $page            
     */
    public function getCurrentPagination($page) {
        /**
         * Check weather the Value of page equal one.
         */
        if ($page == 1) {
            echo "currentpaginationClass";
        }
    }
    
    /**
     * Get the selected Property
     *
     * @param int $propertyServiceFromData            
     * @param int $propertyServiceFromData            
     * @param
     *            string
     *            
     */
    public function propertySelected($propertyServiceFromData, $inc) {
        if (! (empty ( $propertyServiceFromData )) && ($propertyServiceFromData == $inc)) {
            /**
             * Print the selected class Value.
             */
            echo 'selected="selected"';
        }
    }
}
