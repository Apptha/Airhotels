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
 * Display Managesubscriptions grid
 */
class Apptha_Airhotels_Block_Adminhtml_Managesubscriptions_Grid extends Mage_Adminhtml_Block_Widget_Grid {
    
    /**
     * Construct the inital display of grid information
     * Set the default sort for collection
     * Set the sort order as "DESC"
     *
     * Return array of managesubscriptions data
     *
     * @return array
     */
    public function __construct() {
        /**
         * Calling the parent Construct Method.
         */
        parent::__construct ();
        /**
         * Set id.
         *
         * Set sort order by id.
         */
        $this->setId ( 'id' );
        $this->setDefaultSort ( 'id' );
        $this->setDefaultDir ( 'ASC' );
        $this->setSaveParametersInSession ( true );
    }
    
    /**
     * Get collection from apptha_managesubscriptions table
     *
     * Return array of managesubscriptions data
     *
     * @return array
     */
    protected function _prepareCollection() {
        /**
         * Get the Collection of Manage Subscription
         */
        $manageSubscription = Mage::getModel ( 'airhotels/managesubscriptions' )->getCollection ();
        
        /**
         * Get the Table Prefix Value
         */
        $tablePrefix = Mage::getConfig ()->getTablePrefix ();
        $manageSubscription->getSelect ()->group ( 'main_table.product_id' )->joinLeft ( $tablePrefix . 'apptha_productsubscriptions', 'main_table.product_id =' . $tablePrefix . 'apptha_productsubscriptions.product_id AND ' . $tablePrefix . 'apptha_productsubscriptions.is_delete = 0', array (
                'main_table.id as id',
                'main_table.product_name as product_name',
                $tablePrefix . 'apptha_productsubscriptions.subscription_type as subscription_type' 
        ) )->where ( 'main_table.is_subscription_only = ?', 1 );
        
        $collection = $manageSubscription;
        $this->setCollection ( $collection );
        /**
         * Calling the parent Construct Method.
         */
        return parent::_prepareCollection ();
    }
    
    /**
     * Function to create column to grid
     *
     * @param string $id            
     * @return string colunm value
     */
    public function addCustomColumn($id) {
        if ($id == 'id') {
            /**
             * Add Column for ID
             */
            $value = $this->addColumn ( 'id', array (
                    'header' => Mage::helper ( 'airhotels' )->__ ( 'ID' ),
                    'align' => 'right',
                    'width' => '50px',
                    'index' => 'id',
                    'filter' => false 
            ) );
        } else {
            /**
             * When building a module always utilise the installation and
             * upgrade scripts so that any database additions or changes are automatically modified
             * on module installation.
             * There are a number of built in functions to these modules allowing you to add attributes,
             * create new tables etc.
             *
             * Add Column for name
             */
            $value = $this->addColumn ( 'name', array (
                    'header' => Mage::helper ( 'airhotels' )->__ ( 'Subscription Product Name' ),
                    'align' => 'left',
                    'renderer' => 'Apptha_Airhotels_Block_Adminhtml_Managesubscriptions_Grid_Renderer_Name',
                    'filter_condition_callback' => array (
                            $this,
                            '_productFilter' 
                    ) 
            ) );
        }
        return $value;
    }
    
    /**
     * Display the subscription in grid
     *
     * Display information about product subscriptions
     *
     * @return void
     */
    protected function _prepareColumns() {
        /**
         * Add columa 'id','name'.
         */
        $this->addCustomColumn ( 'id' );
        $this->addCustomColumn ( 'name' );
        /**
         * Get the Collection of subscriptiontype
         */
        $subscriptionTitle = Mage::getModel ( 'airhotels/subscriptiontype' )->getCollection ();
        $data = $subscriptionTitle->getData ();
        $substitle = null;
        foreach ( $data as $value ) {
            $i = $value ['id'];
            $substitle [$i] = $value ['title'];
        }
        /**
         * When building a module always utilise the installation and
         * upgrade scripts so that any database additions or changes are automatically modified
         * on module installation.
         * There are a number of built in functions to these modules allowing you to add attributes,
         * create new tables etc.
         *
         * Add Column for subscription_type
         */
        $this->addColumn ( 'subscription_type', array (
                'header' => Mage::helper ( 'airhotels' )->__ ( 'Subscription Type' ),
                'align' => 'left',
                'width' => '80px',
                'index' => 'subscription_type',
                'type' => 'options',
                'options' => $substitle,
                'renderer' => 'Apptha_Airhotels_Block_Adminhtml_Managesubscriptions_Grid_Renderer_Type' 
        ) );
        /**
         * When building a module always utilise the installation and
         * upgrade scripts so that any database additions or changes are automatically modified
         * on module installation.
         * There are a number of built in functions to these modules allowing you to add attributes,
         * create new tables etc.
         *
         * Add Column for action
         */
        $this->addColumn ( 'action', array (
                'header' => Mage::helper ( 'airhotels' )->__ ( 'Action' ),
                'filter' => false,
                'width' => '100',
                'type' => 'action',
                'getter' => 'getId',                
                'index' => 'stores',
                'actions' => array (
                        array (
                                'caption' => Mage::helper ( 'airhotels' )->__ ( 'Edit' ),
                                'url' => array (
                                        'base' => '*/*/edit'
                                ),
                                'field' => 'id'
                        )
                ),
                'sortable' => false,
                'is_system' => true 
        ) );
        /**
         * Calling the parent Construct Method.
         */
        return parent::_prepareColumns ();
    }
    /**
     * It gets the current row url
     *
     * @method getRowUrl()
     * @param $row @return
     *            array
     */
    public function getRowUrl($row) {
        return $this->getUrl ( '*/*/edit', array (
                'id' => $row->getId () 
        ) );
    }
}