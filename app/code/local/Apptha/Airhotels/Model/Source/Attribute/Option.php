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
class Apptha_Airhotels_Model_Source_Attribute_Option extends Varien_Object {
 /**
  * Function Name: getAllOptions
  * To option Array
  * 
  * @return multitype
  */
public function getAllOptions()
    {
        if (is_null($this->_options)) {
            $this->_options = array(
                array( 'label' => Mage::helper('airhotels')->__('Main Product'), 'value' =>  '1' ),
                array('label' => Mage::helper('airhotels')->__('Other Product'), 'value' =>  '2'),
            );
        }
        return $this->_options;
    }
    /**
     * Function Name: toOptionArray
     * To option Array
     *
     * @return multitype
     */
    public function toOptionArray()
    {
        return $this->getAllOptions();
    }
}