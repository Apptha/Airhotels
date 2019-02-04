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
 * @abstract      : Model File
 * @Creation Date : January 10,2014
 * @Modified By   : Ramkumar M
 * @Modified Date : January 23,2014
 * */
/*
 * ********************************************************* */


class Apptha_Paypaladaptive_Model_Mysql4_Productdetails extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the productdetails_id refers to the key field in your database table
        $this->_init('paypaladaptive/productdetails', 'productdetails_id');
    }
}