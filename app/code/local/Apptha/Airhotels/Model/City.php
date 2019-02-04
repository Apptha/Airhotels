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
 * Neighbourhoods city model class
 */
class Apptha_Airhotels_Model_City extends Mage_Core_Model_Abstract {
    /**
     * Constructor class
     * (non-PHPdoc)
     *
     * @see Varien_Object::_construct()
     */
    public function _construct() {
        /**
         * Calling the parent Construct Method.
         */
        parent::_construct ();
        /**
         * Intializeing the city.
         */
        $this->_init ( 'airhotels/city' );
    }
    
    /**
     * Function Name: albumupdate
     * AlbumUpdate Function for updating the album
     *
     * @param array $post
     *            album update
     */
    public function albumupdate($post) {
        /**
         * Initialise the 'path', '$entityId' Value.
         */
        $path = $entityId = '';
        /**
         * Check weather the Value has set.
         */
        if (isset ( $post ['album_path'] )) {
            /**
             * Path Value.
             */
            $path = $post ['album_path'];
        }
        /**
         * Check weather the value of 'entity_id' set.
         */
        if (isset ( $post ['entity_id'] )) {
            /**
             * Entity Id Value.
             */
            $entityId = $post ['entity_id'];
        }
        /**
         * Check weather the value of 'video_type' set.
         */
        if (isset ( $post ['video_type'] )) {
            $videoType = $post ['video_type'];
        }
        /**
         * Check weather the value of 'video_url' set.
         */
        if (isset ( $post ['video_url'] )) {
            $videoUrl = $post ['video_url'];
        }
        
        if (isset ( $post ['remove_url'] )) {
            $videoUrl = '';
        }
        /**
         * Website Id Value.
         */
        $websiteId = Mage::app ()->getWebsite ()->getId ();
        /**
         * product load Value.
         */
        $product = Mage::getModel ( 'catalog/product' )->load ( $entityId );
        /**
         * Set the StoreID Vlaue.
         */
        $product->setStoreID ( 0 );
        /**
         * video url is valid mean set as int 1 from the photos tab, here check from the post value confirm_video_url.
         */
        if ($post ['confirm_video_url'] == 1 || $post ['remove_url'] == 1) {
            $product->setVideoUrl ( $videoUrl );
            $product->setVideoType ( $videoType );
        }
        /**
         * product with setThumbnail Image.
         */
        $product->setThumbnail ( $path )->setImage ( $path )->setSmallImage ( $path )->setStatus ( 1 )->setWebsiteIDs ( array (
                $websiteId 
        ) );
        /**
         * Current Store ID Value .
         */
        $CurrentStoreId = Mage::app ()->getStore ()->getId ();
        Mage::app ()->setCurrentStore ( Mage_Core_Model_App::ADMIN_STORE_ID );
        /**
         * Save the product Value.
         */
        $product->save ();
        Mage::app ()->setCurrentStore ( $CurrentStoreId );
        return true;
    }
}