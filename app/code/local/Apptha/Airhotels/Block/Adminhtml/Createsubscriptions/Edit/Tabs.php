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
 * Createsubscriptions_Edit Tabs block
 * 
 * @abstract Mage_Adminhtml_Block_Widget_Tabs
 */
class Apptha_Airhotels_Block_Adminhtml_Createsubscriptions_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs {
    
    /**
     * Prepare construct for createsubscriptions tabs
     * 
     * @abstract Mage_Adminhtml_Block_Widget_Tabs
     */
    public function __construct() {
        /**
         * creating the Construct Method
         */
        parent::__construct ();
        $this->setId ( 'createsubscriptions_tabs' );
        $this->setDestElementId ( 'edit_form' );
        /**
         * Set page title.
         */
        $this->setTitle ( Mage::helper ( 'airhotels' )->__ ( 'Subscriptions Information' ) );
    }
    /**
     * Creating the method for beforeToHtml
     *
     * @see Mage_Adminhtml_Block_Widget_Tabs::_beforeToHtml()
     */
    protected function _beforeToHtml() {        
        /**
         * assigning general tab.
         */
        $this->addTab ( 'general_section', array (
                'label' => Mage::helper ( 'airhotels' )->__ ( 'General' ),
                'title' => Mage::helper ( 'airhotels' )->__ ( 'General' ),
                'content' => $this->getLayout ()->createBlock ( 'airhotels/adminhtml_createsubscriptions_edit_tab_general' )->toHtml () 
        ) );        
        /**
         * assigning subscription Types tab.
         */
        $this->addTab ( 'subscriptiontype_section', array (
                'label' => Mage::helper ( 'airhotels' )->__ ( 'Subscription Types' ),
                'title' => Mage::helper ( 'airhotels' )->__ ( 'Subscription Types' ),
                'content' => $this->getLayout ()->createBlock ( 'airhotels/adminhtml_createsubscriptions_edit_tab_create' )->toHtml () 
        ) );
        /**
         * Calling the parent Construct Method.
         */
        return parent::_beforeToHtml ();
    }
}