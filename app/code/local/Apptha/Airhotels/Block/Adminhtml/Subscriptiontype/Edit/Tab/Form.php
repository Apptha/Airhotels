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
 * Subscription Type Edit Form block.
 */
class Apptha_Airhotels_Block_Adminhtml_Subscriptiontype_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form {
    
    /**
     * Prepare Form For SubscriptionType
     *
     * @see Mage_Adminhtml_Block_Widget_Form::_prepareForm()
     * @return array
     */
    protected function _prepareForm() {
        /**
         * Set form data to varien data form
         */
        $form = new Varien_Data_Form ();
        $this->setForm ( $form );
        /**
         * Add fieldset for subscriptiontype form
         */
        $fieldset = $form->addFieldset ( 'subscriptiontype_form', array (
                'legend' => Mage::helper ( 'airhotels' )->__ ( 'General' ) 
        ) );
        /**
         * Add Column for engine_code
         */
        $fieldset->addField ( 'engine_code', 'hidden', array (
                'label' => Mage::helper ( 'airhotels' )->__ ( 'Engine' ),
                'required' => false,
                'name' => 'engine_code',
                'values' => array (
                        'Paypal' 
                ) 
        )
         );
        /**
         * Add the Column for Title
         */
        $fieldset->addField ( 'title', 'text', array (
                'label' => Mage::helper ( 'airhotels' )->__ ( 'Title' ),
                'required' => true,
                'name' => 'title' 
        ) );
        /**
         * Add the column for Status
         */
        $fieldset->addField ( 'status', 'select', array (
                'label' => Mage::helper ( 'airhotels' )->__ ( 'Status' ),
                'name' => 'status',
                'values' => array (
                        'Invisible',
                        'Visible' 
                ) 
        ) );
        /**
         * Set subscription type data to admin session
         */
        if (Mage::getSingleton ( 'adminhtml/session' )->getSubscriptiontypeData ()) {
            $form->setValues ( Mage::getSingleton ( 'adminhtml/session' )->getSubscriptiontypeData () );
            /**
             * Set subscription type data as null.
             */
            Mage::getSingleton ( 'adminhtml/session' )->setSubscriptiontypeData ( null );
        }
        if (Mage::registry ( 'subscriptiontype_data' )) {
            /**
             * Set subscription type data.
             */
            $form->setValues ( Mage::registry ( 'subscriptiontype_data' )->getData () );
        }
        return parent::_prepareForm ();
    }
}