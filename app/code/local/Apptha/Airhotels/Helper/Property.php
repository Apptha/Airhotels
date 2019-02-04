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
class Apptha_Airhotels_Helper_Property extends Mage_Core_Helper_Url {
    const XML_PATH_PRODUCT_URL_USE_CATEGORY = 'catalog/seo/product_use_categories';
    
    /**
     * Cache for product rewrite suffix
     *
     * @var array
     */
    protected $_productUrlSuffix = array ();
    protected $_statuses;
    protected $_priceBlock;
    
    /**
     * Function Name: 'getProductUrl'
     * Retrieve product view page url
     *
     * @param mixed $product            
     * @return string
     */
    public function getProductUrl($product) {
        /**
         * check weather the '$produt' is instance of 'Mage_Catalog_Model_Product'
         */
        if ($product instanceof Mage_Catalog_Model_Product) {
            return $product->getProductUrl ();
        }
        /**
         * Check the '$product' is numeric
         */
        if (is_numeric ( $product )) {
            /**
             * Loading in product table
             */
            return Mage::getModel ( 'catalog/product' )->load ( $product )->getProductUrl ();
        }
        return false;
    }
    
    /**
     * Function Name: getPrice
     * Retrieve product price
     *
     * @param Mage_Catalog_Model_Product $product            
     * @return float
     */
    public function getPrice($product) {
        /**
         * Return Product Price
         */
        return $product->getPrice ();
    }
    
    /**
     * Function Name: 'getFinalPrice'
     * Retrieve product final price
     *
     * @param Mage_Catalog_Model_Product $product            
     * @return float
     */
    public function getFinalPrice($product) {
        return $product->getFinalPrice ();
    }
    
    /**
     * Function Name:'getImageUrl'
     * Retrieve base image url
     *
     * @return string
     */
    public function getImageUrl($product) {
        $url = false;
        /**
         * Getting Product Image
         */
        if (! $product->getImage ()) {
            /**
             * Getting no image icon
             */
            $url = Mage::getDesign ()->getSkinUrl ( 'images/no_image.jpg' );
        }
        if ($attribute = $product->getResource ()->getAttribute ( 'image' )) {
            /**
             * Getting Product Url
             */
            $url = $attribute->getFrontend ()->getUrl ( $product );
        }
        /**
         * return url.
         */
        return $url;
    }
    
    /**
     * Function Name : getSmallImageUrl
     * Retrieve small image url
     *
     * @return unknown
     */
    public function getSmallImageUrl($product) {
        $url = false;
        if (! $product->getSmallImage ()) {
            /**
             * Defining no image icon
             */
            $url = Mage::getDesign ()->getSkinUrl ( 'images/no_image.jpg' );
        }
        /**
         * Get small image path.
         */
        if ($attribute = $product->getResource ()->getAttribute ( 'small_image' )) {
            $url = $attribute->getFrontend ()->getUrl ( $product );
        }
        /**
         * Return smaall image url.
         */
        return $url;
    }
    /**
     * Function Name:getEmailToFriendUrl
     * Get the email url to friend
     *
     * @param object $product            
     * @return string
     */
    public function getEmailToFriendUrl($product) {
        $categoryId = null;
        if ($category = Mage::registry ( 'current_category' )) {
            /**
             * Getting category Id
             */
            $categoryId = $category->getId ();
        }
        /**
         * Returning the Catogery id,Product ID Values
         */
        return $this->_getUrl ( 'sendfriend/product/send', array (
                'id' => $product->getId (),
                'cat_id' => $categoryId 
        ) );
    }
    /**
     * Function Name: 'getStatuses'
     * Retrieve the status
     *
     * @return multitype:
     */
    public function getStatuses() {
        /**
         * Get status
         */
        if (is_null ( $this->_statuses )) {
            $this->_statuses = array ();
        }
        
        return $this->_statuses;
    }
    
    /**
     * Function Name: 'canShow'
     * Check if a product can be shown
     *
     * @param Mage_Catalog_Model_Product|int $product            
     * @return boolean
     */
    public function canShow($product) {
        /**
         * Check weather the '$product' is int
         */
        if (is_int ( $product )) {
            /**
             * Loading product in product table
             */
            $product = Mage::getModel ( 'catalog/product' )->load ( $product );
        }
        
        /**
         * $product Mage_Catalog_Model_Product
         */
        if (! $product->getId ()) {
            return false;
        }
        /**
         * Returning the $product
         */
        return $product->isVisibleInCatalog () && $product->isVisibleInSiteVisibility ();
    }
    
    /**
     * Function Name: 'getProductUrlSuffix'
     * Retrieve product rewrite sufix for store
     *
     * @param int $storeId            
     * @return string
     */
    public function getProductUrlSuffix($storeId = null) {
        if (is_null ( $storeId )) {
            /**
             * Getting store id
             */
            $storeId = Mage::app ()->getStore ()->getId ();
        }
        
        if (! isset ( $this->_productUrlSuffix [$storeId] )) {
            $this->_productUrlSuffix [$storeId] = Mage::getStoreConfig ( 'catalog/seo/product_url_suffix', $storeId );
        }
        return $this->_productUrlSuffix [$storeId];
    }
    
    /**
     * Function Name: 'canUseCanonicalTag'
     * Check if <link rel="canonical"> can be used for product
     *
     * @param
     *            $store
     * @return bool
     */
    public function canUseCanonicalTag($store = null) {
        return Mage::getStoreConfig ( 'catalog/seo/product_canonical_tag', $store );
    }
    
    /**
     * Function Name: 'getAttributeInputTypes'
     * Return information array of product attribute input types
     * Only a small number of settings returned, so we won't break anything in current dataflow
     * As soon as development process goes on we need to add there all possible settings
     *
     * @param string $inputType            
     * @return array
     */
    public function getAttributeInputTypes($inputType = null) {
        $inputTypes = array (
                'multiselect' => array (
                        'backend_model' => 'eav/entity_attribute_backend_array' 
                ),
                'boolean' => array (
                        'source_model' => 'eav/entity_attribute_source_boolean' 
                ) 
        );
        /**
         * Check the inpu type empty
         */
        if (is_null ( $inputType )) {
            return $inputTypes;
        }
        /**
         * Check the input type
         */
        if (isset ( $inputTypes [$inputType] )) {
            return $inputTypes [$inputType];
        }
        return array ();
    }
    
    /**
     * Function Name: 'getAttributeBackendModelByInputType'
     * Return default attribute backend model by input type
     *
     * @param string $inputType            
     * @return string null
     */
    public function getAttributeBackendModelByInputType($inputType) {
        $inputTypes = $this->getAttributeInputTypes ();
        if (! empty ( $inputTypes [$inputType] ['backend_model'] )) {
            return $inputTypes [$inputType] ['backend_model'];
        }
        return null;
    }
    
    /**
     * Function Name: 'getAttributeSourceModelByInputType'
     * Return default attribute source model by input type
     *
     * @param string $inputType            
     * @return string null
     */
    public function getAttributeSourceModelByInputType($inputType) {
        $inputTypes = $this->getAttributeInputTypes ();
        if (! empty ( $inputTypes [$inputType] ['source_model'] )) {
            /**
             * Return input type.
             */
            return $inputTypes [$inputType] ['source_model'];
        }
        return null;
    }
    
    /**
     * Function Name: 'initProduct'
     * Inits product to be used for product controller actions and layouts
     * $params can have following data:
     * 'category_id' - id of category to check and append to product as current.
     * If empty (except FALSE) - will be guessed (e.g. from last visited) to load as current.
     *
     * @param int $productId            
     * @param Mage_Core_Controller_Front_Action $controller            
     * @param Varien_Object $params            
     *
     * @return false Mage_Catalog_Model_Product
     */
    public function initProduct($productId, $controller, $params = null) {
        /**
         * Prepare data for routine
         */
        if (! ($params && $productId)) {
            $params = new Varien_Object ();
            return false;
        }
        /**
         * Init and load product
         */
        Mage::dispatchEvent ( 'catalog_controller_product_init_before', array (
                'controller_action' => $controller 
        ) );
        
        $product = Mage::getModel ( 'catalog/product' )->setStoreId ( Mage::app ()->getStore ()->getId () )->load ( $productId );
        Mage::helper ( 'airhotels' )->checkInArray ( $product );
        /**
         * Load product current category
         */
        $categoryIdParam = $params->getCategoryId ();
        /**
         * CategoryId Value
         */
        $categoryId = Mage::helper ( 'airhotels/airhotel' )->categoryIdForinitProduct ( $categoryIdParam, $product );
        /**
         * Check the category id has been set
         */
        if ($categoryId) {
            $category = Mage::getModel ( 'catalog/category' )->load ( $categoryId );
            $product->setCategory ( $category );
            Mage::register ( 'current_category', $category );
        }
        
        /**
         * Register current data and dispatch final events
         */
        Mage::register ( 'current_product', $product );
        Mage::register ( 'product', $product );
        /**
         * Defining dispatch event
         */
        try {
            Mage::dispatchEvent ( 'catalog_controller_product_init', array (
                    'product' => $product 
            ) );
            /**
             * Dispatch product init after event.
             */
            Mage::dispatchEvent ( 'catalog_controller_product_init_after', array (
                    'product' => $product,
                    'controller_action' => $controller 
            ) );
        } catch ( Mage_Core_Exception $e ) {
            /**
             * Error store in log files.
             */
            Mage::logException ( $e );
            return false;
        }
        /**
         * Return product detials.
         */
        return $product;
    }
    
    /**
     * Function Name: 'prepareProductOptions'
     * Prepares product options by buyRequest: retrieves values and assigns them as default.
     * Also parses and adds product management related values - e.g. qty
     *
     * @param Mage_Catalog_Model_Product $product            
     * @param Varien_Object $buyRequest            
     * @return Mage_Catalog_Helper_Product
     */
    public function prepareProductOptions($product, $buyRequest) {
        /**
         * Set Quauntity
         */
        $optionValues = $product->processBuyRequest ( $buyRequest );
        /**
         * Set preConfigured Value
         */
        $optionValues->setQty ( $buyRequest->getQty () );
        $product->setPreconfiguredValues ( $optionValues );
        return $this;
    }
    
    /**
     * Function Name: 'addParamsToBuyRequest'
     * Process $buyRequest and sets its options before saving configuration to some product item.
     * This method is used to attach additional parameters to processed buyRequest.
     *
     * $params holds parameters of what operation must be performed:
     * - 'current_config', Varien_Object or array - current buyRequest that configures product in this item,
     * used to restore currently attached files
     * - 'files_prefix': string[a-z0-9_] - prefix that was added at frontend to names of file inputs,
     * so they won't intersect with other submitted options
     *
     * @param Varien_Object|array $buyRequest            
     * @param Varien_Object|array $params            
     * @return Varien_Object
     */
    public function addParamsToBuyRequest($buyRequest, $params) {
        /**
         * check the 'buyRequest' is an array
         */
        if (is_array ( $buyRequest )) {
            $buyRequest = new Varien_Object ( $buyRequest );
        }
        /**
         * Check the $params is an array
         */
        if (is_array ( $params )) {
            $params = new Varien_Object ( $params );
        }
        
        /**
         * Ensure that currentConfig goes as Varien_Object - for easier work with it later
         */
        $currentConfig = $params->getCurrentConfig ();
        /**
         * Check the current config Value
         */
        if ($currentConfig) {
            if (is_array ( $currentConfig )) {
                $params->setCurrentConfig ( new Varien_Object ( $currentConfig ) );
            }
            if (! ($currentConfig instanceof Varien_Object)) {
                $params->unsCurrentConfig ();
            }
        }
        
        /**
         * Notice that '_processing_params' must always be object to protect processing forged requests
         * where '_processing_params' comes in $buyRequest as array from user input
         */
        $processingParams = $buyRequest->getData ( '_processing_params' );
        if (! $processingParams || ! ($processingParams instanceof Varien_Object)) {
            $processingParams = new Varien_Object ();
            $buyRequest->setData ( '_processing_params', $processingParams );
        }
        $processingParams->addData ( $params->getData () );
        /**
         * return the $buyRequest
         */
        return $buyRequest;
    }
    
    /**
     * Function Name: getProduct
     * Return loaded product instance
     *
     * @param int|string $productId
     *            (SKU or ID)
     * @param int $store            
     * @param string $identifierType            
     * @return Mage_Catalog_Model_Product
     */
    public function getProduct($productId, $store, $identifierType = null) {
        /**
         * Get identity filter type.
         */
        $loadByIdOnFalse = false;
        $identifierTypes = $this->identifier ( $identifierType, $productId );
        $identifierType = $identifierTypes ['type'];
        $loadByIdOnFalse = $identifierTypes ['loadByIdOnFalse'];
        /**
         *
         * @var $product Mage_Catalog_Model_Product
         */
        $product = Mage::getModel ( 'catalog/product' );
        if ($store !== null) {
            $product->setStoreId ( $store );
        }
        /**
         * Check identity filter type as sku
         */
        if ($identifierType == 'sku') {
            $idBySku = $product->getIdBySku ( $productId );
            if ($idBySku && $loadByIdOnFalse) {
                $productId = $idBySku;
                $identifierType = 'id';
            }
        }
        /**
         * Check identity filter type as id.
         */
        if ($identifierType == 'id' && is_numeric ( $productId )) {
            $productId = ! is_float ( $productId ) ? ( int ) $productId : 0;
            $product->load ( $productId );
        }
        return $product;
    }
    /**
     * Funnction Name: getPropertyName
     * reduce Property name length
     *
     * @param string $propertyName            
     *
     * @return multitype:string boolean
     */
    public function getPropertyName($propertyName) {
        $propertyNameSub = substr ( $propertyName, 0, 45 );
        if (strlen ( $propertyName ) > 45) {
            $propertyNameSub .= '...';
        }
        return $propertyNameSub;
    }
    /**
     * Funnction Name: identifier
     * check the identifier
     *
     * @param string $identifierType            
     * @param number $productId            
     * @return multitype:string boolean
     */
    public function identifier($identifierType, $productId) {
        $identifierTypes = array ();
        if ($identifierType == null) {
            if (is_string ( $productId ) && ! preg_match ( "/^[+-]?[1-9][0-9]*$|^0$/", $productId )) {
                $identifierTypes ['type'] = 'sku';
                $identifierTypes ['loadByIdOnFalse'] = true;
            } else {
                $identifierTypes ['type'] = 'id';
            }
        }
        /**
         * Return the propertyName
         */
        return $identifierTypes;
    }
    /**
     * Function to get the myexperience listing page url
     *
     * Passed $userId to set in param
     *
     * @param
     *            $userId
     * @return string
     */
    public function getMyExperienceUrl($userId) {
        return Mage::getUrl ( 'anybooking/index/myexperience', array (
                'id' => $userId 
        ) );
    }
}