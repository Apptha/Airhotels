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
 * @Creation Date : January 24,2014
 * @Modified By   : Ramkumar M
 * @Modified Date : January 24,2014
 * */
/*
 * ********************************************************* */

class Apptha_Paypaladaptive_Block_Adminhtml_Renderersource_Orderstatus extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    // Getting order status
    public function render(Varien_Object $row) {
        $orderId = $row->getData($this->getColumn()->getIndex());
        $orders = Mage::getModel('sales/order')->getCollection()
                ->addFieldToFilter('entity_id', $orderId);
        foreach ($orders as $order) {

            // Changing order status first letter capital 
            return str_replace('\' ', '\'', ucwords(str_replace('\'', '\' ', strtolower($order->getStatus()))));
        }
    }

}
