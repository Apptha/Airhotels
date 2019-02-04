<?php

/*
 * ********************************************************* */

/**
 * @name          : Apptha Paypal Adaptive
 * @version	  : 1.0
 * @package       : Apptha
 * @since         : Magento 1.5
 * @subpackage    : Paypal Adaptive
 * @author        : Apptha - http://www.apptha.com
 * @copyright     : Copyright (C) 2013 Powered by Apptha
 * @license       : GNU/GPL http://www.gnu.org/licenses/gpl-3.0.html
 * @abstract      : Payment Model File
 * @Creation Date : January 02,2014
 * @Modified By   : Ramkumar M
 * @Modified Date : January 23,2014
 * */
/*
 * ********************************************************* */

class Apptha_Paypaladaptive_Model_Payment extends Mage_Payment_Model_Method_Abstract {

    // Initilize payment code and form block type
    protected $_code = 'paypaladaptive';
    protected $_formBlockType = 'paypaladaptive/displayform';
    protected $_canAuthorize = true;

    // Initilize order place redirect url   
    public function getOrderPlaceRedirectUrl() {
        return Mage::getUrl('paypaladaptive/adaptive/redirect', array('_secure' => true));
    }

}