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
 * To show the popular property details
 */
class Apptha_Airhotels_Block_Property_Popular extends Mage_Core_Block_Template {
 /**
  * To call the layout
  */ 
 /**
  * Get Popular Image
  * 
  * @param object $product         
  */
 public function getPopularImage($product) {
  if ($product->getImage () != 'no_selection') {
   echo Mage::getBaseUrl ( 'media' ) . 'catalog/product/' . $product->getImage ();
  } else {
   echo $product->getImageUrl ();
  }
 }
 /**
  * get the style
  * 
  * @param int $propertyTime         
  * @param int $propertyTimeData         
  * @param int $hourlyEnabledOrNot         
  */
 public function getPopularStyle($propertyTime, $propertyTimeData, $hourlyEnabledOrNot) {
  if ($propertyTime == $propertyTimeData && $hourlyEnabledOrNot == 0) {
   echo '<span class="price-tag-price-pernight">' . Mage::helper ( 'airhotels' )->__ ( 'Per Hour' ) . '</span>';
  } else {
   echo '<span class="price-tag-price-pernight">' . Mage::helper ( 'airhotels' )->__ ( 'Per Night' ) . '</span>';
  }
 }
}