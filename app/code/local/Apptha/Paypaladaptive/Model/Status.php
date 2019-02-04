<?php

/*
 * ********************************************************* */

/**
 * @name          : Apptha Paypal Adaptive
 * @version       : 1.0
 * @package       : Apptha
 * @since         : Magento 1.5
 * @subpackage    : Paypal Adaptive
 * @author        : Apptha - http://www.apptha.com
 * @copyright     : Copyright (C) 2013 Powered by Apptha
 * @license       : GNU/GPL http://www.gnu.org/licenses/gpl-3.0.html
 * @abstract      : Controller File
 * @Creation Date : January 10,2014
 * @Modified By   : Ramkumar M
 * @Modified Date : January 10,2014
 * */
/*
 * ********************************************************* */


class Apptha_Paypaladaptive_Model_Status extends Varien_Object
{
    const STATUS_ENABLED	= 1;
    const STATUS_DISABLED	= 2;

    static public function getOptionArray()
    {
        return array(
            self::STATUS_ENABLED    => Mage::helper('paypaladaptive')->__('Enabled'),
            self::STATUS_DISABLED   => Mage::helper('paypaladaptive')->__('Disabled')
        );
    }
}