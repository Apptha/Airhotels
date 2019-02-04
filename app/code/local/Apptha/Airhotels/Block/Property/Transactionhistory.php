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
class Apptha_Airhotels_Block_Property_Transactionhistory extends Mage_Catalog_Block_Product_Abstract {
    /**
     * Prepares the layout
     *
     * @see Mage_Catalog_Block_Product_Abstract::_prepareLayout()
     */
    protected function _prepareLayout() {
        /**
         * Calling the parent Construct Method.
         */
        parent::_prepareLayout ();
        /**
         * Getting property listings
         */
        $transactionCollection = $this->getCompletedPropertyHistory ();
        $this->setCollection ( $transactionCollection );
        /**
         * setting pager
         */
        $pager = $this->getLayout ()->createBlock ( 'page/html_pager', 'my.pager' )->setCollection ( $transactionCollection );
        $this->setChild ( 'pager', $pager );        
        return $this;
    }
    /**
     * Function to get pagination
     *
     * Return pagination for collection
     *
     * @return array
     */
    public function getPagerHtml() {
        return $this->getChildHtml ( 'pager' );
    }    
    /**
     * Get property collection.
     */
    public function getCompletedPropertyHistory() {
        /**
         * Get customer details.
         *
         * @var $customer
         */
        $customer = Mage::getSingleton ( 'customer/session' )->getCustomer ();
        $cus_id = $customer->getId ();
        /**
         * Get property collection.
         * Filter by customer id
         *
         * @var $products
         */
        $products = Mage::getModel ( 'catalog/product' )->getCollection ();
        $products->addFieldToFilter ( 'userid', $cus_id );
        $product_ids = array ();
        foreach ( $products as $product ) {
            $product_ids [] = $product->getId ();
        }
        /**
         * Get order item collection
         * Sort based on order_id descending order.
         *
         * @var $items
         */
        $items = Mage::getModel ( "sales/order_item" )->getCollection ()->addFieldToFilter ( "product_id", array (
                "in" => $product_ids
        ) );
        $items->getSelect ()->join ( array (
                'order_airhotels' => Mage::getSingleton ( 'core/resource' )->getTableName ( 'airhotels_property' )
        ), 'order_airhotels.order_item_id = main_table.order_id and (order_airhotels.order_status=1 or order_airhotels.cancel_request_status=3)', array (
                '*'
        ) );
        $items->getSelect ()->order ( 'main_table.order_id DESC' );
        /**
         * Add pagination
         *
         * @var $pager
         */
        $pager = $this->getLayout ()->createBlock ( 'page/html_pager', 'completed.pager' );
        $pager->setCollection ( $items );
        $this->setChild ( 'completedpager', $pager );
        /**
         * Return item collection.
         */
        return $items;
    } 
}