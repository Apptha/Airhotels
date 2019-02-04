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
 * Grid for Host payout details
 * 
 * @abstract Mage_Adminhtml_Block_Widget_Grid
 */
class Apptha_Airhotels_Block_Adminhtml_Hostpayoutdetails_Grid extends Mage_Adminhtml_Block_Widget_Grid {
    /**
     * Class constructor
     * 
     * @abstract Mage_Adminhtml_Block_Widget_Grid
     */
    public function __construct() {
        /**
         * Calling the parent Construct Method.
         */
        parent::__construct ();
        $this->setId ( 'customerphotoGrid' );
        $this->setDefaultSort ( 'customer_id' );
        $this->setDefaultDir ( 'ASC' );
        /**
         * Set session parameter
         */
        $this->setSaveParametersInSession ( true );
    }
    /**
     * Prepare collection for customer details
     *
     * @return $customerPhotoCollection
     */
    protected function _prepareCollection() {
        $customerPhotoCollection = Mage::getModel ( 'airhotels/customerphoto' )->getCollection ();
        
        /**
         * set customer photo collection
         */
        $this->setCollection ( $customerPhotoCollection );
        /**
         * Calling the parent Construct Method.
         */
        return parent::_prepareCollection ();
    }
    /**
     * Prepare columns for Host payout details
     *
     * @return collection
     */
    protected function _prepareColumns() {
        /**
         * Add colums
         *
         * 'customer_id' , 'name'
         * 'email_id','paypal_email','bank_details'
         */
        $this->addColumn ( 'customer_id', array (
                'header' => Mage::helper ( 'airhotels' )->__ ( 'Host ID' ),'align' => 'left',
                'index' => 'customer_id' 
        ) );
        /**
         * Add new column for name
         */
        $this->addColumn ( 'name', array (
                'header' => Mage::helper ( 'airhotels' )->__ ( 'Host Name' ),                
                'index' => 'name','align' => 'left'
        ) );
        /**
         * Add new column for email_id
         */
        $this->addColumn ( 'email_id', array (
                'header' => Mage::helper ( 'airhotels' )->__ ( 'Host Email' ),'align' => 'left',
                'index' => 'email_id' 
        ) );
        /**
         * Add new column for paypal email
         * header - paypal email
         */
        $this->addColumn ( 'paypal_email', array (
                'header' => Mage::helper ( 'airhotels' )->__ ( 'Paypal Email' ),'align' => 'left',
                'index' => 'paypal_email' 
        ) );
        /**
         * Add new column for bank_details
         */
        $this->addColumn ( 'bank_details', array (
                'header' => Mage::helper ( 'airhotels' )->__ ( 'Payout Details' ),                
                'renderer' => 'Apptha_Airhotels_Block_Adminhtml_Renderer_Hostpayoutdetail',
                'align' => 'left','index' => 'bank_details'
        ) );
        /**
         * Calling the parent Construct Method.
         */
        return parent::_prepareColumns ();
    }
}