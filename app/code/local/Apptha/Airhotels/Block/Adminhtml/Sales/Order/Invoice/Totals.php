<?php
/**
 * Apptha
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.apptha.com/LICENSE.txt
 *
 * ==============================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * ==============================================================
 * This package designed for Magento COMMUNITY edition
 * Apptha does not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * Apptha does not provide extension support in case of
 * incorrect edition usage.
 * ==============================================================
 *
 * @category    Apptha
 * @package     Apptha_Airhotels
 * @version     0.2.9
 * @author      Apptha Team <developers@contus.in>
 * @copyright   Copyright (c) 2014 Apptha. (http://www.apptha.com)
 * @license     http://www.apptha.com/LICENSE.txt
 * 
 */

/**
 * Adminhtml order invoice totals block
 *
 * @category Mage
 * @package Mage_Adminhtml
 * @author Magento Core Team <core@magentocommerce.com>
 */
class Apptha_Airhotels_Block_Adminhtml_Sales_Order_Invoice_Totals extends Mage_Adminhtml_Block_Sales_Order_Invoice_Totals {
    
    /**
     * Initialize order totals array
     *
     * @return Mage_Sales_Block_Order_Totals
     */
    protected function _initTotals() {
        /**
         * Calling the parent Construct Method.
         */
        parent::_initTotals ();
        /**
         * Add total before to the orders
         */
        $this->addTotalBefore ( new Varien_Object ( array (
                'code' => 'fee_amount',
                'value' => Mage::getModel ( 'sales/order' )->load ( $this->getSource ()->getOrderId () )->getFeeAmount (),
                'base_value' => $this->getSource ()->getBaseFeeAmount (),
                'label' => $this->helper ( 'airhotels' )->__ ( 'Processing Fee' ) 
        ), array (
                'shipping',
                'tax' 
        ) ) );
        /**
         * Returning the Total Value
         */
        return $this;
    }
}