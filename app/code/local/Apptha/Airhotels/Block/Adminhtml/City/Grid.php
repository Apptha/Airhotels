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
 * Grid for cities with images
 */
class Apptha_Airhotels_Block_Adminhtml_City_Grid extends Mage_Adminhtml_Block_Widget_Grid {    
    /**
     * Class constructor
     */
    public function __construct() {
        /**
         * Calling the parent Construct Method.
         */
        parent::__construct ();
        /**
         * Set grid cityId
         * Sort by id in desending order.
         * Save parameters to session
         */
        $this->setId ( 'cityGrid' );
        $this->setDefaultSort ( 'id' );
        $this->setDefaultDir ( 'DESC' );
        $this->setSaveParametersInSession ( true );
    }    
    /**
     * Prepare collection for neighbourhoods cities
     *
     * @return collection
     */
    protected function _prepareCollection() {
        $collection = Mage::getModel ( 'airhotels/city' )->getCollection ();
        /**
         * Set product collection.
         */
        $this->setCollection ( $collection );
        /**
         * Calling the parent Construct Method.
         */
        return parent::_prepareCollection ();
    }    
    /**
     * Function Name: _prepareColumns()
     * 
     * Prepare columns for neighbourboods cities
     *
     * @return collection
     */
    protected function _prepareColumns() {
        /**
         * Display id in grid list
         */
        $this->addColumn ( 'id', array (
                'header' => Mage::helper ( 'airhotels' )->__ ( 'Id' ),
                'align' => 'left','index' => 'id' 
        ) );
        /**
         * Display city name.
         */
        $this->addColumn ( 'city', array (
                'header' => Mage::helper ( 'airhotels' )->__ ( 'City' ),
                'align' => 'left',
                'index' => 'city' 
        ) );
        /**
         * Display city description.
         */
        $this->addColumn ( 'city_description', array (
                'header' => Mage::helper ( 'airhotels' )->__ ( 'Description' ),                
                'index' => 'city_description','align' => 'left'
        ) );
        /**
         * Display created date.
         */
        $this->addColumn ( 'created_at', array (
                'header' => Mage::helper ( 'airhotels' )->__ ( 'Created' ),
                'align' => 'left','type' => 'datetime','index' => 'created_at' 
        ) );
        /**
         * Display edit city link.
         */
        $this->addColumn ( 'editaction', array (
                'header' => Mage::helper ( 'airhotels' )->__ ( 'Edit Action' ),
                'width' => '50px','type' => 'action',
                'actions' => array (
                        array (
                                'caption' => Mage::helper ( 'airhotels' )->__ ( 'Edit' ),
                                'url' => array (
                                        'base' => 'airhotels/adminhtml_city/edit/',
                                        'params' => array (
                                                'store' => $this->getRequest ()->getParam ( 'store' ) 
                                        ) 
                                ),
                                'field' => 'id' 
                        ) 
                ),
                'getter' => 'getId','sortable' => false,
                'filter' => false,                
                'index' => 'stores' 
        ) );
        /**
         * Display delete city link.
         * 
         * Add confirm message to delete action
         */
        $this->addColumn ( 'deleteaction', array (
                'header' => Mage::helper ( 'airhotels' )->__ ( 'Delete Action' ),
                'width' => '50px',
                'type' => 'action',
                'getter' => 'getId',                
                'sortable' => false,                
                'index' => 'stores',
                'filter' => false,
                'actions' => array (
                        array (
                                'caption' => Mage::helper ( 'airhotels' )->__ ( 'Delete' ),
                                'url' => array (
                                        'base' => 'airhotels/adminhtml_city/delete/',
                                        'params' => array (
                                                'store' => $this->getRequest ()->getParam ( 'store' )
                                        )
                                ),
                                'field' => 'id',
                                'confirm' => Mage::helper ( 'airhotels' )->__ ( 'Are you sure?' )
                        )
                ),
        ) );
        /**
         * Calling the parent Construct Method.
         */
        return parent::_prepareColumns ();
    }    
    /**
     * Getting row url
     */
    public function getRowUrl($varRow) {
        return $this->getUrl ( '*/*/edit', array (
                
                'id' => $varRow->getId () 
        ) );
    }
    /**
     * Mass action for neighbourhoods city
     */
    protected function _prepareMassaction() {
        /**
         * Set massaction Id.
         */
        $this->setMassactionIdField ( 'id' );
        /**
         * Set form field cities
         */
        $this->getMassactionBlock ()->setFormFieldName ( 'cities' );
        /**
         * Set warning message.
         */
        $this->getMassactionBlock ()->addItem ( 'delete', array (
                'label' => Mage::helper ( 'airhotels' )->__ ( 'Delete' ),
                'confirm' => Mage::helper ( 'airhotels' )->__ ( 'Are you sure?' ),
                'url' => $this->getUrl ( '*/*/massDelete' ) 
        ) );
        return $this;
    }
}