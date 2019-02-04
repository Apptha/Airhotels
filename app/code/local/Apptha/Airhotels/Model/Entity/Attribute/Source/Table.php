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
 * @copyright   Copyright (c) 2015 Apptha. (http://www.apptha.com)
 * @license     http://www.apptha.com/LICENSE.txt
 * 
 */
class Apptha_Airhotels_Model_Entity_Attribute_Source_Table extends Mage_Eav_Model_Entity_Attribute_Source_Abstract {
 
 /**
  * Add approve and disapprove action in grid
  *
  * Return the available options
  * 
  * @return string
  */
 public function getAllOptions($withEmpty = true, $defaultValues = false) {
  $withEmpty = true;
  $defaultValues = false;
  /**
   * Add options.
   */
  $options [] = array ( 'value' => 0, 'label' => 'Passport' );
  $options [] = array ('value' => 1, 'label' => 'Identity Card' );
  $options [] = array ( 'value' => 2, 'label' => 'Driving License');
  return $options;
 }
} 
