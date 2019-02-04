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
 * Createsubscriptions grid block
 */
class Apptha_Airhotels_Block_Adminhtml_Createsubscriptions_Grid extends Mage_Adminhtml_Block_Widget_Grid {
 /**
  * Function Name: '__construct'
  * Construct Method
  */
 public function __construct() {
 /**
  * Calling the parent Construct Method.
  */
  parent::__construct ();
  /**
   * Set the Product ID
   */
  $this->setId ( 'productGrid' );
  /**
   * Set the defaultSort to 'entity_id'
   */
  $this->setDefaultSort ( 'entity_id' );
  /**
   * Set Default Directory
   */
  $this->setDefaultDir ( 'DESC' );
  /**
   * set Save Parameters In Session VLaue To True
   */
  $this->setSaveParametersInSession ( true );
  $this->setVarNameFilter ( 'product_filter' );
 }
 
 /**
  * Function Name: '_getStore'
  * Get Store Value
  */
 protected function _getStore() {
  /**
   * Get the StoreId
   */
  $storeId = ( int ) $this->getRequest ()->getParam ( 'store', 0 );
  /**
   * Returning the StoreId
   */
  return Mage::app ()->getStore ( $storeId );
 }
 
 /**
  * Function Name: '_prepareCollection'
  * Prepare Collection Method
  */
 protected function _prepareCollection() {
  /**
   * Get Store Value
   */
  $store = $this->_getStore ();
  /**
   * Manage Collection Value
   */
  $managesCollection = Mage::getModel ( 'airhotels/managesubscriptions' )->getCollection ();
  /**
   * Get the Collection of product and the attribute to filetr
   */
  $collection = Mage::getModel ( 'catalog/product' )->getCollection ()->addAttributeToSelect ( 'sku' )->addAttributeToSelect ( 'name' )->addAttributeToSelect ( 'attribute_set_id' )->addAttributeToSelect ( 'type_id' )->addAttributeToFilter ( 'type_id', array (
    'in' => 'property' 
  ) );
  $idValue = null;
  /**
   * Iterating the manageColletion Value
   */
  foreach ( $managesCollection as $manageCollectionValue ) {
   $idValue [] = $manageCollectionValue->getProductId ();
  }
  /**
   * Add the Filter as 'entity_id'
   */
  
  $collection->addAttributeToFilter ( 'entity_id', array (
    'nin' => $idValue 
  ) );
  /**
   * Check weather the Value is enabled
   */
  if (Mage::helper ( 'catalog' )->isModuleEnabled ( 'Mage_CatalogInventory' )) {
   $collection->joinField ( 'qty', 'cataloginventory/stock_item', 'qty', 'product_id=entity_id', '{{table}}.stock_id=1', 'left' );
  }
  /**
   * Check weather the the Store ID
   */
  if ($store->getId ()) {
   /**
    * Set the store id
    */
   $collection->setStoreId ( $store->getId () );
   /**
    * Set the Value to 'AdminStore'
    */
   $adminStore = Mage_Core_Model_App::ADMIN_STORE_ID;
   /**
    * Add the filter to the colletion
    */
   $collection->addStoreFilter ( $store );
   /**
    * Join the attribute for the Collection and 'name'
    */
   $collection->joinAttribute ( 'name', 'catalog_product/name', 'entity_id', null, 'inner', $adminStore );
   /**
    * Join the attribute for the Collection and 'custom_name'
    */
   $collection->joinAttribute ( 'custom_name', 'catalog_product/name', 'entity_id', null, 'inner', $store->getId () );
   /**
    * Join the attribute for the Collection and 'status'
    */
   $collection->joinAttribute ( 'status', 'catalog_product/status', 'entity_id', null, 'inner', $store->getId () );
   /**
    * Join the attribute for the Collection and 'visibility'
    */
   $collection->joinAttribute ( 'visibility', 'catalog_product/visibility', 'entity_id', null, 'inner', $store->getId () );
   /**
    * Join the attribute for the Collection and 'price'
    */
   $collection->joinAttribute ( 'price', 'catalog_product/price', 'entity_id', null, 'left', $store->getId () );
  } else {
   /**
    * joining attributes to collection
    */
   $collection->addAttributeToSelect ( 'price' );
   /**
    * Join the attribute for the Collection and 'status'
    */
   $collection->joinAttribute ( 'status', 'catalog_product/status', 'entity_id', null, 'inner' );
   /**
    * Join the attribute for the Collection and 'visibility'
    */
   $collection->joinAttribute ( 'visibility', 'catalog_product/visibility', 'entity_id', null, 'inner' );
  }
  /**
   * Set the COlletion Value
   */
  $this->setCollection ( $collection );
  /**
   * Calling the parent Construct Method.
   */
  parent::_prepareCollection ();
  $this->getCollection ()->addWebsiteNamesToResult ();
  return $this;
 }
 /**
  * FUnctio Name: _addColumnFilterToCollection
  * Method for filter Collection
  */
 protected function _addColumnFilterToCollection($column) {
 /**
  * Checking the websites or not.
  */
  if (($this->getCollection ()) && ($column->getId () == 'websites')) {
   $this->getCollection ()->joinField ( 'websites', 'catalog/product_website', 'website_id', 'product_id=entity_id', null, 'left' );
  }
 /**
  * Calling the parent Construct Method.
  */  
  return parent::_addColumnFilterToCollection ( $column );
 }
 /**
  * Method for columns preparing
  */
 protected function _prepareColumns() {
  /**
   * Add Column for entity Id
   */
  $this->addColumn ( 'entity_id', array (
    'header' => Mage::helper ( 'catalog' )->__ ( 'ProductId' ),
    'width' => '50px',
    'type' => 'number',
    'index' => 'entity_id' 
  ) );
  /**
   * Add Column for name
   */
  $this->addColumn ( 'name', array (
    'header' => Mage::helper ( 'catalog' )->__ ( 'Name' ),
    'index' => 'name' 
  ) );
  /**
   * Add Column for custom_name
   */
  $store = $this->_getStore ();
  if ($store->getId ()) {
   $this->addColumn ( 'custom_name', array (
     'header' => Mage::helper ( 'catalog' )->__ ( 'Name in %s', $store->getName () ),
     'index' => 'custom_name' 
   ) );
  }
  /**
   * Add Column for sku
   */
  $this->addColumn ( 'sku', array (
    'header' => Mage::helper ( 'catalog' )->__ ( 'SKU' ),
    'width' => '80px',
    'index' => 'sku' 
  ) );
  /**
   * Add Column for price
   */
  $store = $this->_getStore ();
  $this->addColumn ( 'price', array (
    'header' => Mage::helper ( 'catalog' )->__ ( 'Price' ),
    'type' => 'price',
    'currency_code' => $store->getBaseCurrency ()->getCode (),
    'index' => 'price' 
  ) );
  /**
   * Check weather the catalog Value enabled
   */
  if (Mage::helper ( 'catalog' )->isModuleEnabled ( 'Mage_CatalogInventory' )) {
   $this->addColumn ( 'qty', array (
     'header' => Mage::helper ( 'catalog' )->__ ( 'Qty' ),
     'width' => '100px',
     'type' => 'number',
     'index' => 'qty' 
   ) );
  }
  /**
   * Add Column for status
   */
  $this->addColumn ( 'status', array (
    'header' => Mage::helper ( 'catalog' )->__ ( 'Status' ),
    'width' => '70px',
    'index' => 'status',
    'type' => 'options',
    'options' => Mage::getSingleton ( 'catalog/product_status' )->getOptionArray () 
  ) );
  /**
   * Make sure the the store is single Mode
   */
  if (! Mage::app ()->isSingleStoreMode ()) {
   $this->addColumn ( 'websites', array (
     'header' => Mage::helper ( 'catalog' )->__ ( 'Websites' ),
     'width' => '100px',
     'sortable' => false,
     'index' => 'websites',
     'type' => 'options',
     'options' => Mage::getModel ( 'core/website' )->getCollection ()->toOptionHash () 
   ) );
  }
  /**
   * Calling the parent Construct Method.
   */
  return parent::_prepareColumns ();
 }
 /**
  * GetrowUrl Value
  */
 public function getRowUrl($row) {
  return $this->getUrl ( '*/*/move', array (
    'id' => $row->getId () 
  ) );
 }
}
