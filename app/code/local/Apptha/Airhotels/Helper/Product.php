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
class Apptha_Airhotels_Helper_Product extends Mage_Core_Helper_Abstract {

    /**
     * Function Name: 'getprofilepage'
     * Retrieve profile url
     *
     * @return string
     */
    public function getprofilepage() {
        return $this->_getUrl ( 'property/index/profile' );
    }
    /**
     * Function Name: 'getcancelpolicy'
     * Retrieve attribute id for cancel policy
     *
     * @return integer
     */
    public function getcancelpolicy() {
        /**
         * Getting entity attribute model
         */
        return Mage::getResourceModel ( 'eav/entity_attribute' )->getIdByCode ( 'catalog_product', 'cancelpolicy' );
    }
    /**
     * Function Name: 'getmediagallery'
     * Retrieve attribute id for media gallery
     *
     * @return integer
     */
    public function getmediagallery() {
        /**
         * Getting entity attribute model
         */
        return Mage::getResourceModel ( 'eav/entity_attribute' )->getIdByCode ( 'catalog_product', 'media_gallery' );
    }
    /**
     * Function Name: 'getprivacy'
     * Retrieve attribute id for privacy
     *
     * @return integer
     */
    public function getprivacy() {
        /**
         * Getting entity attribute model
         */
        return Mage::getResourceModel ( 'eav/entity_attribute' )->getIdByCode ( 'catalog_product', 'privacy' );
    }
    /**
     * Function Name: 'getpropertytype'
     * Retrieve attribute id for propertytype
     *
     * @return integer
     */
    public function getpropertytype() {
        /**
         * Getting entity attribute model
         */
        return Mage::getResourceModel ( 'eav/entity_attribute' )->getIdByCode ( 'catalog_product', 'propertytype' );
    }

    /**
     * Function Name: 'getBedRoom'
     * Retrieve attribute id for bed_room
     *
     * @return integer
     */
    public function getBedRoom() {
        /**
         * Getting entity attribute model
         */
        return Mage::getResourceModel ( 'eav/entity_attribute' )->getIdByCode ( 'catalog_product', 'bed_rooms' );
    }

    /**
     * Function Name: 'getBedType'
     * Retrieve attribute id for bed_type
     *
     * @return integer
     */
    public function getBedType() {
        /**
         * Getting entity attribute model
         */
        return Mage::getResourceModel ( 'eav/entity_attribute' )->getIdByCode ( 'catalog_product', 'bed_type' );
    }

    /**
     * Function Name: 'getamenity'
     * Retrieve attribute id for amenity
     *
     * @return integer
     */
    public function getamenity() {
        /**
         * Getting entity attribute model
         */
        return Mage::getResourceModel ( 'eav/entity_attribute' )->getIdByCode ( 'catalog_product', 'amenity' );
    }
    /**
     * Function Name: 'getshowlisturl'
     * Retrieve property list
     *
     * @return string
     */
    public function getshowlisturl() {
        /**
         * Set listing url
         */
        return $this->_getUrl ( 'property/general/show' );
    }
    /**
     * Function Name: 'getimageurl'
     * Retrieve property image upload url
     *
     * @return string
     */
    public function getimageurl() {
        /**
         * Set listing url
         */
        return $this->_getUrl ( 'property/general/image' );
    }
    /**
     * Function Name: 'getediturl'
     * Retrieve property edit url
     *
     * @return string
     */
    public function getediturl() {
        /**
         * Set listing url
         */
        return $this->_getUrl ( 'property/general/edit' );
    }
    /**
     * Function Name: 'getyourtripurl'
     * Retrieve trip url
     *
     * @return string
     */
    public function getyourtripurl() {
        /**
         * Set listing url
         */
        return $this->_getUrl ( 'property/general/yourtrip' );
    }
    /**
     * Function Name: 'getblockurl'
     * Retrieve calendar url
     *
     * @return string
     */
    public function getblockurl() {
        /**
         * Set listing url
         */
        return $this->_getUrl ( 'property/property/blockdate' );
    }
    /**
     * Function Name: 'getHourlyEnabledOrNot'
     * Retrieve Hourly enabled or not
     *
     * @return string
     */
    public function getHourlyEnabledOrNot() {
        /**
         * Set listing url
         */
        return Mage::getStoreConfig ( 'airhotels/hourly/enable' );
    }
    /**
     * Function Name: 'getSecurityEnabledOrNot'
     * Retrieve security enabled or not
     *
     * @return string
     */
    public function getSecurityEnabledOrNot() {
        /**
         * Get security configuration from admin
         */
        return Mage::getStoreConfig ( 'airhotels/security_deposit_configuration/security_deposit' );
    }
    /**
     * Function Name: 'getProductDetailsById'
     * Retrieve property details by id
     *
     * @return array
     */
    public function getProductDetailsById($productId) {
        /**
         * Get the Store Id Value
         */
        $storeId = Mage::app ()->getStore ()->getId ();
        return Mage::getModel ( 'catalog/product' )->setStoreId ( $storeId )->load ( $productId );
    }
    /**
     * Function Name: 'getOffset'
     * Retrieve key
     *
     * @return string
     */
    public function getOffset($start, $end) {
        /**
         * Define character string.
         */
        $chars_str = "WJ-GLADIATOR1IS2FIRST3BEST4HERO5IN6QUICK7LAZY8VEX9LIFEMP0";
        $charsStr = strlen ( $chars_str );
        for($i = 0; $i < $charsStr; $i ++) {
            $chars_array [] = $chars_str [$i];
        }

        for($i = count ( $chars_array ) - 1; $i >= 0; $i --) {
            $lookupObj [ord ( $chars_array [$i] )] = $i;
        }
        /**
         * Get the $sNum, $eNum Values
         */
        $sNum = $lookupObj [ord ( $start )];
        $eNum = $lookupObj [ord ( $end )];

        $offset = $eNum - $sNum;

        if ($offset < 0) {
            $offset = count ( $chars_array ) + ($offset);
        }
        /**
         * Returning the Offset Values
         */
        return $offset;
    }

    /**
     * Function Name : getAttributevalue
     * getAttriobute value
     *
     * @var $options
     * @return multitype:
     */
    function getAttributevalue() {
        $options = array ();
        $attribute = Mage::getSingleton ( 'eav/config' )->getAttribute ( 'customer', 'id_type' );
        if ($attribute->usesSource ()) {
            /**
             * Get attribute options
             */
            $options = $attribute->getSource ()->getAllOptions ( false );
        }
        return $options;
    }
}