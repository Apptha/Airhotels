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
 * @Creation Date : January 13,2014
 * @Modified By   : Ramkumar M
 * @Modified Date : January 23,2014
 * */
/*
 * ********************************************************* */


class Apptha_Paypaladaptive_Model_Commissiondetails extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
       
        $this->_init('paypaladaptive/commissiondetails');
    }  
   
}


