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
 * Subscription Type Tabs block
 */
class Apptha_Airhotels_Block_Adminhtml_Subscriptiontype_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs {
    /**
     * Creating Construct Method
     */
    public function __construct() {
        /**
         * Defining controller
         */
        parent::__construct ();
        $this->setDestElementId ( 'edit_form' );
        $this->setId ( 'subscriptiontype_tabs' );
        /**
         * Define the page title.
         */
        $this->setTitle ( Mage::helper ( 'airhotels' )->__ ( 'The Subscription Type ' ) );
    }
    /**
     * Function to add the required tabs
     *
     * @return array
     */
    protected function _beforeToHtml() {
        /**
         * Add tab for form_section
         * 
         * create new renderer block for edit tab form
         */
        $this->addTab ( 'form_section', array (
                'label' => Mage::helper ( 'airhotels' )->__ ( 'General' ),
                'title' => Mage::helper ( 'airhotels' )->__ ( 'General' ),
                'content' => $this->getLayout ()->createBlock ( 'airhotels/adminhtml_subscriptiontype_edit_tab_form' )->toHtml () 
        ) );
        /**
         * Add tab for schedule_section
         * 
         * create new renderer block for edit tab schedule
         */
        $this->addTabAfter ( 'schedule_section', array (
                'label' => Mage::helper ( 'airhotels' )->__ ( 'Schedule' ),
                'title' => Mage::helper ( 'airhotels' )->__ ( 'Schedule' ),
                'content' => $this->getLayout ()->createBlock ( 'airhotels/adminhtml_subscriptiontype_edit_tab_schedule' )->toHtml () 
        ), 'form_section' );        
        /**
         * Calling the parent Construct Method.
         * 
         * Call _beforeToHtml
         */
        return parent::_beforeToHtml ();
    }
}