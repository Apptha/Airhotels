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
 * @Creation Date : January 02,2014
 * @Modified By   : Ramkumar M
 * @Modified Date : January 16,2014
 * */
/*
 * ********************************************************* */

class Apptha_Paypaladaptive_Model_Mysql4_Preapprovaldetails_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract {
	/**
	 * Class constructor
	 */

	public function _construct() {
		parent::_construct();
		$this->_init('paypaladaptive/preapprovaldetails');
	}

}