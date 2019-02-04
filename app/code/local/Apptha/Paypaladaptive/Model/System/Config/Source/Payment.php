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
 * @abstract      : Helper File
 * @Creation Date : January 02,2014
 * @Modified By   : Ramkumar M
 * @Modified Date : January 23,2014
 * */
/*
 * ********************************************************* */

class Apptha_Paypaladaptive_Model_System_Config_Source_Payment 
{  
    // Adaptive paypal order success status
    public function toOptionArray()
    {
        return array(            
            array('value' => 'chained', 'label'=>Mage::helper('paypaladaptive')->__('Chained Payment')),
            array('value' => 'parallel', 'label'=>Mage::helper('paypaladaptive')->__('Parallel Payment')),                     
        );
    }
}