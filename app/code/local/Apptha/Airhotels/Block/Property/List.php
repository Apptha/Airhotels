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
class Apptha_Airhotels_Block_Property_List extends Mage_Catalog_Block_Product_Abstract {
    
    /**
     * Get the MainImage
     *
     * @param string $mainimage            
     * @param object $_product            
     */
    public function mainImage($mainimage, $_product) {
        if ($_product->getImage () != 'no_selection') {
            /**
             * Get image url.
             */
            echo $mainimage;
        } else {
            /**
             * Get image url.
             */
            echo $_product->getImageUrl ();
        }
    }
    
    /**
     * Get collection for Inspired experience
     *
     * @return array
     */
    public function getInspiredExperience() {
        /**
         * In backend we have saved the city images
         */
        $attributeCode = 'city';
        $alias = $attributeCode . '_table';
        /**
         * Getting experience city name for which experience sales highest qty.
         * we have match the highest qty sales experience city name and Admin->Anyproperty->Add city with image section (city name)
         * Display the city images in 3 blocks in homepage.
         */
        $productCollection = Mage::getResourceModel ( 'reports/product_collection' )->addAttributeToSelect ( '*' )->addOrderedQty ()->addAttributeToFilter ( 'status', array (
                'eq' => 1 
        ) )->addAttributeToFilter ( 'type_id', array (
                'eq' => 'property' 
        ) )->addAttributeToFilter ( 'propertyapproved', array (
                'eq' => 1 
        ) )->setOrder ( 'ordered_qty', 'DESC' );
        $attribute = Mage::getSingleton ( 'eav/config' )->getAttribute ( Mage_Catalog_Model_Product::ENTITY, $attributeCode );
        $productCollection->getSelect ()->join ( array (
                $alias => $attribute->getBackendTable () 
        ), "e.entity_id = $alias.entity_id AND $alias.attribute_id={$attribute->getId()}", array (
                $attributeCode => 'value' 
        ) );
        /**
         * Get product collection group by 'entity_id'.
         */
        $productCollection->getSelect ()->group ( 'entity_id' );
        /**
         * Get sum of each city
         * get unique city values
         */
        $trimCity = array_map ( 'trim', $productCollection->getColumnValues ( 'city' ) );
        $trimCountry = array_map ( 'trim', $productCollection->getColumnValues ( 'country' ) );
        $Country = array_map ( 'ucwords', $trimCountry );
        $city = array_map ( 'ucwords', $trimCity );
        /**
         * Combine city,country.
         */
        $cityCountry = array_combine ( $city, $Country );
        
        /**
         * Sort the city count by descending
         */
        array_unique ( $cityCountry );
        
        return $cityCountry;
    }
    
    /**
     * Get the Icon
     *
     * @param object $product            
     * @param array $arrProductIds            
     */
    public function getIcon($product, $arrProductIds) {
        if (in_array ( $product->getId (), $arrProductIds )) {
            /**
             * Get pink icon.
             */
            echo 'pink-icon';
        } else {
            /**
             * Get icon.
             */
            echo 'icon';
        }
    }
    /**
     * Get the property Image
     *
     * @param object $product            
     * @param string $popularpdct            
     */
    public function getImage($product, $popularpdct) {
        if ($product->getImage () != 'no_selection') {
            /**
             * Get popular product.
             */
            echo $popularpdct;
        } else {
            /**
             * Get image url.
             */
            echo $product->getImageUrl ();
        }
    }
    /**
     * Get the Price Tag style
     *
     * @param int $propertyTime            
     * @param int $propertyTimeData            
     * @param int $hourlyEnabledOrNot            
     */
    public function PriceTag($propertyTime, $propertyTimeData, $hourlyEnabledOrNot) {
        if ($propertyTime == $propertyTimeData && $hourlyEnabledOrNot == 0) {
            /**
             * Return per hour.
             */
            echo "<span class='price-tag-price-pernight'>" . Mage::helper ( 'airhotels' )->__ ( 'Per Hour' ) . "</span>";
        } else {
            /**
             * Return per night.
             */
            echo "<span class='price-tag-price-pernight'>" . Mage::helper ( 'airhotels' )->__ ( 'Per Night' ) . "</span>";
        }
    }
    /**
     * Get the Style
     *
     * @param int $count            
     */
    public function getStyle($count) {
        if ($count == 4) {
            echo 'style=" margin-right:0;" ';
        }
    }
    /**
     * Rating the Product Image
     *
     * @param string $rateimage            
     * @param string $rateProduct            
     */
    public function rateProductImg($rateimage, $rateProduct) {
        /**
         * Get image url.
         */
        if ($rateProduct->getImage () != 'no_selection') {
            echo $rateimage;
        } else {
            echo $rateProduct->getImageUrl ();
        }
    }
    /**
     * Get Newly added experience collection
     *
     * @return array
     */
    public function newExperienceCollection() {
        /**
         * Get property collection.
         *
         * Filter by status,propertyapproved
         *
         * Set order based on created_at.
         */
        return Mage::getModel ( 'airhotels/property' )->getpropertycollection ()->addAttributeToFilter ( 'status', array (
                'eq' => 1 
        ) )->addAttributeToFilter ( 'propertyapproved', array (
                'eq' => 1 
        ) )->setPageSize ( 10 )->setOrder ( 'created_at', 'desc' )->addAttributeToSelect ( '*' );
    }
}


