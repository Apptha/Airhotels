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
 * Grid to show the list 
 * of orders placed for the properties
 * Fetched from airhotels_airhotels table
 * @author user
 *
 */
class Apptha_Airhotels_Block_Adminhtml_Airhotels_Grid extends Mage_Adminhtml_Block_Widget_Grid{
    /**
     * Construct method
     * 
     * @abstract Mage_Adminhtml_Block_Widget_Grid
     */
    public function __construct() {
        /**
         * Calling the parent Constructor Method with
         * 'airhotelsGrid'
         * 'id'
         * 'DESC'
         */
        parent::__construct ();
        /**
         * set id from airhotels grid
         */
        $this->setId ( 'airhotelsGrid' );
        /**
         * set magento default sort and its type of sort
         */
        /**
         * set sort type to id
         */
        $this->setDefaultSort ( 'id' );
        /**
         * set sorting by descending order
         */
        $this->setDefaultDir ( 'DESC' );
        /**
         * set session to save parameter
         * as true
         */
        $this->setSaveParametersInSession ( true );
    }
    /**
     * Prepare Collection
     * @see Mage_Adminhtml_Block_Widget_Grid::_prepareCollection()
     */
    /**
     * get collections from airhotels_airhotels
     */
    protected function _prepareCollection() {
        /**
         * To get property collection details with add
         * filer 'order_status' as One.
         */
        /**
         * get collection from airhotels/airhotels
         * add fields to filter as
         * order statuses
         * @var unknown
         */
        $collection = Mage::getModel ( 'airhotels/airhotels' )->getCollection ()->addFieldToFilter ( array (
                'order_status',
                'cancel_request_status' 
        ), array (
                '1',
                '3' 
        ) );
        /**
         * set the retreived collection 
         * to layout prepare collection
         */
        $this->setCollection ( $collection );
        /**
         * Returning the 'prepareCollection'
         */
        return parent::_prepareCollection ();
    }
    /**
     * Function to create column to grid
     * @param string $id            
     * @return string colunm value
     */
    public function addCustomColumn($id) {
        /**
         * Adding custom columns for customer email , Todate , Order Id
         * Order status , Subtotal , Default Order id
         */
        switch ($id) {
            /**
             * Add field customId.
             */
            case 'ID' :
                $value = $this->addColumn ( 'id', array (
                        'header' => Mage::helper ( 'airhotels' )->__ ( 'Id' ),
                        'align' => 'left',
                        'index' => 'id' 
                ) );
                break;
            /**
             * Add field property name.
             */
            case 'PRONAME' :
                $value = $this->addColumn ( 'propname', array (
                        'align' => 'left',
                        'index' => 'entity_id',
                        'header' => Mage::helper ( 'airhotels' )->__ ( 'Property Name' ),
                        'renderer' => 'Apptha_Airhotels_Block_Adminhtml_Host_Propertyname' 
                ) );
                break;
            case 'EMAIL' :
                $value = $this->addColumn ( 'customer_email', array (
                        'align' => 'left',
                        'header' => Mage::helper ( 'airhotels' )->__ ( 'Customer Email' ),
                        'index' => 'customer_email' 
                ) );
                break;
            /**
             * Add field from date.
             */
            case 'FROMDATE' :
                $value = $this->addColumn ( 'fromdate', array (
                        'header' => Mage::helper ( 'airhotels' )->__ ( 'From Date' ),
                        'type' => 'date',
                        'align' => 'left',
                        'index' => 'fromdate' 
                ) );
                break;
            case 'TODATE' :
                $value = $this->addColumn ( 'todate', array (
                        'type' => 'date',
                        'header' => Mage::helper ( 'airhotels' )->__ ( 'To Date' ),
                        'align' => 'left',
                        'index' => 'todate' 
                ) );
                break;
            case 'ORDERID' :
                $value = $this->addColumn ( 'order_id', array (
                        'align' => 'left',
                        'header' => Mage::helper ( 'airhotels' )->__ ( 'Order Id' ),
                        'index' => 'order_id' 
                ) );
                break;
            /**
             * Add field host email.
             */
            case 'HOSTMAIL' :
                $value = $this->addColumn ( 'hostemai', array (
                        'align' => 'left',
                        'header' => Mage::helper ( 'airhotels' )->__ ( 'Host Email (View Host)' ),
                        'index' => 'entity_id',
                        'renderer' => 'Apptha_Airhotels_Block_Adminhtml_Host_Hostemail' 
                ) );
                break;
            case 'ORDER_STATUS' :
                $value = $this->addColumn ( 'hostemai', array (
                        'align' => 'left',
                        'header' => Mage::helper ( 'airhotels' )->__ ( 'Order Status' ),
                        'index' => 'order_item_id',
                        'renderer' => 'Apptha_Airhotels_Block_Adminhtml_Orderstatus' 
                ) );
                break;
            case 'SUBTOTAL' :
                $value = $this->addColumn ( 'subtotal', array (
                        'header' => Mage::helper ( 'airhotels' )->__ ( 'Grand Total' ),
                        'align' => 'left',
                        'index' => 'subtotal',
                        'type' => 'currency',
                        'currency' => 'order_currency_code' 
                ) );
                break;
            default :
                $value = $this->addColumn ( 'order_id', array (
                        'header' => Mage::helper ( 'airhotels' )->__ ( 'Order Id' ),
                        'align' => 'left',
                        'index' => 'order_id' 
                ) );
        }
        /**
         * Return the value array
         */
        return $value;
    }
    /**
     * Prepare columns
     * @see Mage_Adminhtml_Block_Widget_Grid::_prepareColumns()
     */
    protected function _prepareColumns() {
        /**
         * adding column to grid
         */
        /**
         * assign column for id
         */
        $this->addCustomColumn ( 'ID' );
        /**
         * assign column for property name
         */
        $this->addCustomColumn ( 'PRONAME' );
        /**
         * assign column for from date
         */
        $this->addCustomColumn ( 'FROMDATE' );
        /**
         * assign column for to date
         */
        $this->addCustomColumn ( 'TODATE' );
        /**
         * Sub Total
         */
        /**
         * assign column for grand total
         */
        $this->addCustomColumn ( 'SUBTOTAL' );
        /**
         * assign column for Subtotal
         */
        $this->addColumn ( 'subtotal', array (
                'header' => Mage::helper ( 'airhotels' )->__ ( 'Grand Total' ),
                'align' => 'left',
                'index' => 'grand_total',
                'type' => 'currency',
                'currency' => 'order_currency_code' 
        ) );
        /**
         * service fee to admin
         */
        $this->addColumn ( 'service_fee', array (
                'header' => Mage::helper ( 'airhotels' )->__ ( 'Service fee' ),
                'align' => 'left',
                'index' => 'service_fee',
                'type' => 'currency',
                'currency' => 'order_currency_code' 
        ) );
        /**
         * Commmission fee to admin
         */
        $this->addColumn ( 'hostfee', array (
                'header' => Mage::helper ( 'airhotels' )->__ ( 'Commission fee' ),
                'align' => 'left',
                'index' => 'host_fee',
                'type' => 'currency',
                'currency' => 'order_currency_code' 
        ) );
        $this->addCustomColumn ( 'HOSTMAIL' );
        $this->addCustomColumn ( 'ORDER_STATUS' );
        /**
         * Status filter
         */
        $this->addColumn ( 'status', array (
                'header' => Mage::helper ( 'airhotels' )->__ ( 'Payment Status' ),
                'align' => 'left',
                'width' => '80px',
                'index' => 'status',
                'type' => 'options',
                'options' => array (
                        2 => 'Refund To Guest',
                        1 => 'Paid To Hoster',
                        0 => 'Not Paid To Hoster' 
                ) 
        ) );
        /**
         * Add the custom Column for 'orderID'
         */
        $this->addCustomColumn ( 'ORDERID' );
        /**
         * Add Column for Action
         */
        $this->addColumn ( 'action', array (
                'header' => Mage::helper ( 'airhotels' )->__ ( 'Action' ),
                'getter' => 'getId',
                'index' => 'id',
                'width' => '100',
                'type' => 'action',
                'renderer' => 'Apptha_Airhotels_Block_Adminhtml_Renderer_hosttransactioninfo',
                'sortable' => false,
                'filter' => false,
                'is_system' => true 
        ) );
        /**
         * Edit Action added for view order 
         */
        /**
         * assign column for action 1
         */
        $this->addColumn ( 'action1', array (
                'header' => Mage::helper ( 'airhotels' )->__ ( 'View Order' ),
                'actions' => array (
                        array (
                                'caption' => Mage::helper ( 'airhotels' )->__ ( 'View Order' ),
                                'url' => array (
                                        'base' => 'adminhtml/sales_order/view'
                                ),
                                'field' => 'order_id'
                        )
                ),
                'filter' => false,
                'type' => 'action',
                'getter' => 'getOrderItemId',
                'sortable' => false,
                'index' => 'stores',                
                'is_system' => true, 
                'width' => '100',
        ) );
        $this->addExportType ( '*/*/exportCsv', Mage::helper ( 'airhotels' )->__ ( 'CSV' ) );
        $this->addExportType ( '*/*/exportXml', Mage::helper ( 'airhotels' )->__ ( 'XML' ) );
        return parent::_prepareColumns ();
    }
    /**
     * Prepare Mass
     */
    protected function _prepareMassaction() {
        $this->setMassactionIdField ( 'id' );
        $this->getMassactionBlock ()->setFormFieldName ( 'airhotels' );
        /**
         * Add delete button
         */
        $this->getMassactionBlock ()->addItem ( 'delete', array (
                'label' => Mage::helper ( 'airhotels' )->__ ( 'Delete' ),
                'url' => $this->getUrl ( '*/*/massDelete' ),
                'confirm' => Mage::helper ( 'airhotels' )->__ ( 'Are you sure?' ) 
        ) );
        $statuses = Mage::getSingleton ( 'airhotels/status' )->getOptionArray ();
        /**
         * Array_unshift the Values.
         */
        array_unshift ( $statuses, array (
                'label' => '',
                'value' => '' 
        ) );
        /**
         * This was commented not to show in Change status option in the Action
         */
        return $this;
    }
    /**
     * Get the row Url
     */
    public function getRowUrl($row) {
        return $this->getUrl ( '*/*/edit', array (
                'id' => $row->getId () 
        ) );
    }
}