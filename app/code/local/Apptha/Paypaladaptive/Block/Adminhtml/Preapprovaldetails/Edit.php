<?php

/**
 * In this class contains delayed chained grid edit function.
 *
 * @package         Apptha PayPal Adaptive
 * @version         0.1.1
 * @since           Magento 1.5
 * @author          Apptha Team
 * @copyright       Copyright (C) 2014 Powered by Apptha
 * @license         http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @Creation Date   March 26,2014
 * @Modified By     Ramkumar M
 * @Modified Date   March 26,2014
 *
 * */
class Apptha_Paypaladaptive_Block_Adminhtml_Preapprovaldetails_Edit extends Mage_Adminhtml_Block_Widget_Form_Container {
    /*
     * Class constructor
     */

    public function __construct() {
        parent::__construct();
        $this->_removeButton('reset');
        $this->_removeButton('delete');
        $this->_objectId = 'id';
        $this->_blockGroup = 'paypaladaptive';
        $this->_controller = 'adminhtml_preapprovaldetails';
    }

    /*
     * Get header text
     * 
     * @return string
     */

    public function getHeaderText() {
        return $this->__('Edit Execute Payment Date');
    }

}
