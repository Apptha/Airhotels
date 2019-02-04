<?php
/**
 * In this class contains the product delayed chained collection functionality
 *
 * @package         Apptha PayPal Adaptive
 * @version         0.1.1
 * @since           Magento 1.5
 * @author          Apptha Team
 * @copyright       Copyright (C) 2014 Powered by Apptha
 * @license         http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @Creation Date   January 10,2014
 * @Modified By     Ramkumar M
 * @Modified Date   January 23,2014
 *
 * */

class Apptha_Paypaladaptive_Model_Mysql4_Preapprovaldetails extends Mage_Core_Model_Mysql4_Abstract {
	/*
	 * Class constructor
	*/

	public function _construct() {
		$this->_init('paypaladaptive/preapprovaldetails', 'preapprovaldetails_id');
	}

}