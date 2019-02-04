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
class Apptha_Airhotels_Block_Property_View_Media extends Mage_Catalog_Block_Product_View_Abstract {
 protected $_isGalleryDisabled;
 /**
  * Get all gallery Images
  */
 public function getGalleryImages() {
  if ($this->_isGalleryDisabled) {
   return array ();
  }
  return $this->getProduct ()->getMediaGalleryImages ();
 }
 /**
  * Get the Gallery Url
  */
 public function getGalleryUrl($image = null) {
  /**
   * Get Product Id
   */
  $params = array (
    'id' => $this->getProduct ()->getId () 
  );
  if ($image) {
   $params ['image'] = $image->getValueId ();
   return $this->getUrl ( '*/*/gallery', $params );
  }
  return $this->getUrl ( '*/*/gallery', $params );
 }
 /**
  * Disable Gallery
  */
 public function disableGallery() {
  $this->_isGalleryDisabled = true;
 }
}