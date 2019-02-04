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
 * Form for add/edit Manage Bank details
 */
class Apptha_Airhotels_Block_Adminhtml_Managebankdetails_Edit_Form extends Mage_Adminhtml_Block_Widget_Form {
    /**
     * Prepare form before rendering HTML
     *
     * @return Mage_Adminhtml_Block_Widget_Form
     */
    protected function _prepareForm() {
        /**
         * Set save form action.
         */
        $form = new Varien_Data_Form ( array (
                'id' => 'edit_form',
                'action' => $this->getUrl ( '*/*/save', array (
                        'id' => $this->getRequest ()->getParam ( 'id' ) 
                ) ),
                'enctype' => 'multipart/form-data',
                'method' => 'post',
                
        ) );
        /**
         * Set calues to form.
         */
        $form->setUseContainer ( true );      
        /**
         * Set form data 
         */
        $this->setForm ( $form );        
        $fieldset = $form->addFieldset ( 'base_fieldset', array (
                'legend' => Mage::helper ( 'airhotels' )->__ ( 'Bank details' ) 
        ) );
        /**
         * Get collection from country list.
         *
         * @var $countryList
         */
        $countryList = Mage::getModel ( 'directory/country' )->getResourceCollection ()->loadByStore ()->toOptionArray ( false );
        /**
         * Display country code.
         * Field type 'multi select'.
         * Required entry true.
         */
        $fieldset->addField ( 'country_code', 'multiselect', array (
                'name' => 'country_code[]',
                'label' => Mage::helper ( 'airhotels' )->__ ( 'Countries' ),
                'title' => Mage::helper ( 'airhotels' )->__ ( 'Countries' ),
                'required' => true,
                'values' => $countryList 
        ) );
        /**
         * Get currency collection.
         */
        $allCurrency = Mage::getModel ( 'airhotels/allcurrency' )->getCollection ()->addFieldToSelect ( 'value' )->addFieldToSelect ( 'label' )->getData ();
        /**
         * Display currency.
         * Field Name 'currency'.
         * Fields type 'multiselect'
         * Requird entry true.
         */
        $fieldset->addField ( 'currency_code', 'multiselect', array (
                'name' => 'currency_code[]',
                'label' => Mage::helper ( 'airhotels' )->__ ( 'Curreny' ),
                'title' => Mage::helper ( 'airhotels' )->__ ( 'Currency' ),
                'required' => true,
                'values' => $allCurrency 
        ) );
        /**
         * Display note message.
         */
        $note = Mage::helper ( 'airhotels' )->__ ( 'Example: bank_name, account_number, ifsc_code, payee etc..' );
        /**
         * Display fields name.
         * Field name 'field_name'
         * Field type 'text'
         * Required entry true.
         */
        $fieldset->addField ( 'field_name', 'text', array (
                'label' => Mage::helper ( 'airhotels' )->__ ( 'Field Name' ),
                'title' => Mage::helper ( "airhotels" )->__ ( 'Field Name' ),
                'class' => 'required-entry',
                'required' => true,
                'name' => 'field_name',
                'note' => $note 
        ) );
        /**
         * Display field title.
         * Field name 'field_title'
         * Field type 'text'
         * Required entry true.
         */
        $fieldset->addField ( 'field_title', 'text', array (
                'label' => Mage::helper ( 'airhotels' )->__ ( 'Field Title' ),
                'title' => Mage::helper ( "airhotels" )->__ ( 'Field Title' ),
                'class' => 'required-entry',
                'required' => true,
                'name' => 'field_title' 
        ) );
        /**
         * Display managebankdetails.
         * Field name 'managebankdetails_data'
         * Field type 'checkbox'
         * Required entry false.
         */
        $bankDataInfo = Mage::registry ( 'managebankdetails_data' )->getData ();
        $fieldset->addField ( 'field_required', 'checkbox', array (
                'name' => 'field_required',
                'label' => Mage::helper ( 'airhotels' )->__ ( 'This Field Is Required' ),
                'title' => Mage::helper ( "airhotels" )->__ ( 'This Fieldd Is Required' ),
                'required' => false,
                'value' => 1,
                'checked' => ($bankDataInfo ['field_required'] == 1) ? 'true' : '',
                'onclick' => 'this.value = this.checked ? 1 : 0;',
                'disabled' => false,
                'readonly' => false 
        ) );
        if (Mage::registry ( 'managebankdetails_data' )) {
            /**
             * Get managebank details.
             */
            $bankDataInfo = Mage::registry ( 'managebankdetails_data' )->getData ();
        }
        /**
         * Set bank data info.
         */
        $form->setValues ( $bankDataInfo );
        /**
         * Set value to form.
         */
        $this->setForm ( $form );
        /**
         * Calling the parent Construct Method.
         */
        return parent::_prepareForm ();
    }
}