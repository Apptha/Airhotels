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
class Apptha_Airhotels_Helper_Productconfiguration extends Mage_Core_Helper_Abstract {
    /**
     * Function Name: 'getModuleEnabledOrNot'
     * Retrieve airhotels module enabled or not
     *
     * @return integer
     */
    public function getModuleEnabledOrNot() {
        return Mage::getStoreConfig ( 'airhotels/custom_group/enable_airhotels' );
    }
    
    /**
     * Function Name: 'getyourlisturl'
     * Retrieve property list url
     *
     * @return string
     */
    public function getyourlisturl() {
        return $this->_getUrl ( 'property/general/show' );
    }
    
    /**
     * Function Name: 'getHourlyNotAvailableAction'
     * Get day wise blocked and not available dates
     *
     * @param int $count            
     * @param array $value            
     * @return array
     */
    public function getHourlyNotAvailableAction($count, $value) {
        $availDay = array ();
        for($j = 0; $j < $count; $j ++) {
            if (isset ( $value [$j] [1] )) {
                $availCountValue = explode ( ",", $value [$j] [1] );
                if (count ( $availCountValue ) == 1) {
                    /**
                     * Get avail days
                     */
                    $availDay [] = $value [$j] [1];
                }
            }
        }
        /**
         * Return the Value of Available Day.
         */
        return $availDay;
    }
}