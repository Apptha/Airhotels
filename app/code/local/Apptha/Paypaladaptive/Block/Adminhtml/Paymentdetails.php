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
 * @abstract      : Admin Block File
 * @Creation Date : January 11,2014
 * @Modified By   : Ramkumar M
 * @Modified Date : January 23,2014
 * */
/*
 * ********************************************************* */

class Apptha_Paypaladaptive_Block_Adminhtml_Paymentdetails extends Mage_Adminhtml_Block_Widget_Grid_Container {

    public function __construct() {
    
        $this->_controller = 'adminhtml_paymentdetails';
        $this->_blockGroup = 'paypaladaptive';
        $this->_headerText = Mage::helper('paypaladaptive')->__('Payment Details');
        $this->_addButtonLabel = Mage::helper('paypaladaptive')->__('');
        parent::__construct();
        $this->_removeButton('add');
    }

}
