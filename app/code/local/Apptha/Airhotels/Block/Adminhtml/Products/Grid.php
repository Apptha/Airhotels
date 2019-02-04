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
 * Manage Products Grid
 */
class Apptha_Airhotels_Block_Adminhtml_Products_Grid extends Mage_Adminhtml_Block_Widget_Grid {
    
    /**
     * Construct the inital display of grid information
     * Set the default sort for collection
     * Set the sort order as "DESC"
     *
     * Return array of data to display order information
     *
     * @return array
     */
    public function __construct() {
        /**
         * Calling the parent Construct Method.
         */
        parent::__construct ();
        /**
         * Set id for product grid
         */
        $this->setId ( 'productsGrid' );
        /**
         * Set Default sort
         */
        $this->setDefaultSort ( 'entity_id' );
        /**
         * Set Default order
         */
        $this->setDefaultDir ( 'DESC' );
        $this->setSaveParametersInSession ( true );
    }
    
    /**
     * Get current store id
     *
     * Return current store id
     *
     * @return type
     */
    protected function _getStore() {
        /**
         * Get Store Id
         */
        $storeId = ( int ) $this->getRequest ()->getParam ( 'store', 0 );
        /**
         * Return store Id
         */
        return Mage::app ()->getStore ( $storeId );
    }
    
    /**
     * Function to get seller product collection
     *
     * Return the seller product collection information
     * return array
     */
    protected function _prepareCollection() {
        $store = $this->_getStore ();
        /**
         * creating the collection for product
         */
        $collection = Mage::getModel ( 'catalog/product' )->getCollection ()->/**
         * Filter by sku
         */
        addAttributeToSelect ( 'sku' )->/**
         * Filter by name
         */
        addAttributeToSelect ( 'name' )->

        addAttributeToSelect ( 'attribute_set_id' )->/**
         * Filter by type ID
         */
        addAttributeToSelect ( 'type_id' );
        /**
         * Check Catalog Inventory has been enabled or not
         */
        if (Mage::helper ( 'catalog' )->isModuleEnabled ( 'Mage_CatalogInventory' )) {
            $collection->joinField ( 'qty', 'cataloginventory/stock_item', 'qty', 'product_id=entity_id', '{{table}}.stock_id=1', 'left' );
        }
        
        /**
         * joining attributes
         */
        $varStoreId = $store->getId ();
        if ($varStoreId) {
            $adminStore = Mage_Core_Model_App::ADMIN_STORE_ID;
            $collection->addStoreFilter ( $store );
            /**
             * Using join query
             */
            $collection->joinAttribute ( 'name', 'catalog_product/name', 'entity_id', null, 'inner', $adminStore );
            $collection->joinAttribute ( 'custom_name', 'catalog_product/name', 'entity_id', null, 'inner', $varStoreId );
            $collection->joinAttribute ( 'status', 'catalog_product/status', 'entity_id', null, 'inner', $varStoreId );
            $collection->joinAttribute ( 'visibility', 'catalog_product/visibility', 'entity_id', null, 'inner', $varStoreId );
            $collection->joinAttribute ( 'price', 'catalog_product/price', 'entity_id', null, 'left', $varStoreId );
        } else {
            /**
             * Filter by status and visibility
             */
            $collection->addAttributeToSelect ( 'price' );
            $collection->joinAttribute ( 'status', 'catalog_product/status', 'entity_id', null, 'inner' );
            $collection->joinAttribute ( 'visibility', 'catalog_product/visibility', 'entity_id', null, 'inner' );
        }
        $this->setCollection ( $collection );
        /**
         * Calling the parent Construct Method.
         */
        parent::_prepareCollection ();
        $this->getCollection ()->addWebsiteNamesToResult ();
        return $this;
    }
    
    /**
     * Function to filter product according to website
     *
     * Return the filter product collection
     * return array
     */
    protected function _addColumnFilterToCollection($objColumn) {
        if (($this->getCollection ()) && ($objColumn->getId () == 'websites')) {
            /**
             * USING JOIN QUERY
             */
            $this->getCollection ()->joinField ( 'websites', 'catalog/product_website', 'website_id', 'product_id=entity_id', null, 'left' );
        }
        return parent::_addColumnFilterToCollection ( $objColumn );
    }
    
    /**
     * Function to display fields with data
     *
     * Display information about orders
     *
     * @return void
     */
    protected function _prepareColumns() {
        $store = $this->_getStore ();
        $this->addColumn ( 'entity_id', array (
                'header' => Mage::helper ( 'catalog' )->__ ( 'ID' ),
                'width' => '50px','index' => 'entity_id' 
        ) );
        /**
         * creating column for Name
         */
        $this->addColumn ( 'name', array (
                'header' => Mage::helper ( 'catalog' )->__ ( 'Name' ),
                'index' => 'name','width' => '250px' 
        ) );
        /**
         * creating column for Type
         */
        $this->addColumn ( 'type', array (
                'header' => Mage::helper ( 'catalog' )->__ ( 'Type' ),
                'options' => Mage::getModel ( 'airhotels/calendarsync' )->getProductTypes (),
                'width' => '100px','index' => 'type_id','type' => 'options'                 
        ) );
        $this->addColumn ( 'sku', array (
                'header' => Mage::helper ( 'catalog' )->__ ( 'SKU' ),
                'width' => '100px',
                'index' => 'sku' 
        ) );
        /**
         * creating column for Price
         */
        $this->addColumn ( 'price', array (
                'header' => Mage::helper ( 'catalog' )->__ ( 'Price' ),
                'type' => 'price','currency_code' => $store->getBaseCurrency ()->getCode (),
                'index' => 'price','width' => '50px' 
        ) );
        /**
         * creating column for Qty
         */
        if (Mage::helper ( 'catalog' )->isModuleEnabled ( 'Mage_CatalogInventory' )) {
            $this->addColumn ( 'qty', array (
                    'header' => Mage::helper ( 'catalog' )->__ ( 'Qty' ),
                    'type' => 'number',
                    'index' => 'qty','width' => '50px'                     
            ) );
        }
        /**
         * creating column for Status
         */
        $this->addColumn ( 'status', array (
                'header' => Mage::helper ( 'catalog' )->__ ( 'Status' ),
                'options' => Mage::getSingleton ( 'catalog/product_status' )->getOptionArray (),
                'width' => '100px',
                'index' => 'status',
                'type' => 'options'               
        ) );
        /**
         * creating column for Websites
         */
        if (! Mage::app ()->isSingleStoreMode ()) {
            $this->addColumn ( 'websites', array (
                    'header' => Mage::helper ( 'catalog' )->__ ( 'Websites' ),
                    'width' => '100px',
                    'options' => Mage::getModel ( 'core/website' )->getCollection ()->toOptionHash (),
                    'sortable' => false,
                    'index' => 'websites',
                    'type' => 'options'
                     
            ) );
        }
        /**
         * creating column for Action
         */
        $this->addColumn ( 'action', array (
                'header' => Mage::helper ( 'catalog' )->__ ( 'Action' ),                                
                'actions' => array (
                        array (
                                'caption' => Mage::helper ( 'catalog' )->__ ( 'View' ),
                                'url' => array (
                                        'base' => 'adminhtml/catalog_product/edit',
                                        'params' => array (
                                                'store' => $this->getRequest ()->getParam ( 'store' ) 
                                        ) 
                                ),
                                'field' => 'id' 
                        ) 
                ),
                'width' => '50px',
                'type' => 'action',
                'filter' => false,
                'sortable' => false,
                'getter' => 'getId',
                'index' => 'stores' 
        ) );
        /**
         * Calling the parent Construct Method.
         */
        return parent::_prepareColumns ();
    }
    
    /**
     * Function for Mass action
     *
     * return void
     */
    protected function _prepareMassaction() {
        return $this;
    }
    
    /**
     * Function for link url
     *
     * Return the product edit page url
     * return string
     */
    public function getRowUrl($row) {
        /**
         * Return link url.
         */
        return $this->getUrl ( 'adminhtml/catalog_product/edit', array (
                'store' => $this->getRequest ()->getParam ( 'store' ),
                'id' => $row->getId () 
        ) );
    }
}