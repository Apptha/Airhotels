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
 * This class contains bank details grid actions
 */
class Apptha_Airhotels_Adminhtml_ManagebankdetailsController extends Mage_Adminhtml_Controller_action {
    /**
     * Index Action
     */
    public function indexAction() {
        $this->loadLayout ()->_setActiveMenu ( 'airhotels' );
        /**
         * Set page title.
         */
        $this->getLayout ()->getBlock ( 'head' )->setTitle ( 'Manage Bank Details' );
        $this->renderLayout ();
    }
    /**
     * Set to edit action.
     */
    public function newAction() {
        $this->_forward ( 'edit' );
    }
    /**
     * Edit bank,country,currency fields information
     */
    public function editAction() {
        $idValue = $this->getRequest ()->getParam ( 'id' );
        /**
         * Get manage bank details.
         */
        $modelCollection = Mage::getModel ( 'airhotels/managebankdetails' )->load ( $idValue );
        if ($modelCollection->getId () || $idValue == 0) {
            $dataVal = Mage::getSingleton ( 'adminhtml/session' )->getFormData ( true );
            if (! empty ( $dataVal )) {
                $modelCollection->setData ( $dataVal );
            }
            Mage::register ( 'managebankdetails_data', $modelCollection );
            $this->loadLayout ();
            $this->_setActiveMenu ( 'airhotels' );
            if (empty ( $idValue )) {
                /**
                 * Set page title as add bank details.
                 */
                $this->getLayout ()->getBlock ( 'head' )->setTitle ( 'Add Bank Details' );
            } else {
                /**
                 * Set page title as edit bank details.
                 */
                $this->getLayout ()->getBlock ( 'head' )->setTitle ( 'Edit Bank Details' );
            }
            $this->_addContent ( $this->getLayout ()->createBlock ( 'airhotels/adminhtml_managedetails_edit' ) );
            $this->renderLayout ();
        } else {
            /**
             * Set error message.
             */
            Mage::getSingleton ( 'adminhtml/session' )->addError ('Record does not exist');
            $this->_redirect ( '*/*/' );
        }
    }
    /**
     * Save the Manage Bank details
     */
    public function saveAction() {
        if ($dataVal = $this->getRequest ()->getPost ()) {
            /**
             * Get the parameters.
             * Get country code.
             * Get currency code
             * Get field name
             * Get field title.
             */
            $idVal = $this->getRequest ()->getParam ( 'id' );
            $model = Mage::getModel ( 'airhotels/managebankdetails' );
            $storeValues ['country_code'] = implode ( ",", $dataVal ['country_code'] );
            $storeValues ['currency_code'] = implode ( ",", $dataVal ['currency_code'] );
            $storeValues ['field_name'] = $dataVal ['field_name'];
            $storeValues ['field_title'] = $dataVal ['field_title'];
            if ($dataVal ['field_required']) {
                $storeValues ['field_required'] = 1;
            }
            $model->setData ( $storeValues )->setId ( $idVal );
            $model->save ();
            /**
             * Set success message.
             */
            Mage::getSingleton ( 'adminhtml/session' )->addSuccess ( Mage::helper ( 'airhotels' )->__ ( 'Details saved successfully' ) );
            $this->_redirect ( '*/*/' );
        } else {
            /**
             * Set error message.
             */
            Mage::getSingleton ( 'adminhtml/session' )->addError ( Mage::helper ( 'airhotels' )->__ ( 'Unable to find item to save' ) );
            $this->_redirect ( '*/*/' );
        }
    }
    
    /**
     * Delete the Manage Bank details.
     */
    public function deleteAction() {
        if ($this->getRequest ()->getParam ( 'id' ) > 0) {
            try {
                /**
                 * Get the model Collection
                 */
                $modelCollection = Mage::getModel ( 'airhotels/managebankdetails' );
                /**
                 * Delete the id in Collection
                 */
                $modelCollection->setId ( $this->getRequest ()->getParam ( 'id' ) )->delete ();
                /**
                 * Set success message
                 * Redirect delete action page.
                 */
                Mage::getSingleton ( 'adminhtml/session' )->addSuccess ('Bank Details has been removed successfully.'  );
                $this->_redirect ( '*/*/' );
            } catch ( Exception $e ) {
                /**
                 * Set error message
                 * Redirect to edit action page.
                 */
                Mage::getSingleton ( 'adminhtml/session' )->addError ( $e->getMessage () );
                $this->_redirect ( '*/*/edit', array ('id' => $this->getRequest ()->getParam ( 'id' )) );
            }
        }
        /**
         * Redirect delete action page.
         */
        $this->_redirect ( '*/*/' );
    }
    /**
     * Delete multiple Manage Bank details action
     */
    public function massDeleteAction() {
        $countryCodeVal = $this->getRequest ()->getParam ( 'country_code' );
        if (! is_array ( $countryCodeVal )) {
            /**
             * Set error message.
             */
            Mage::getSingleton ( 'adminhtml/session' )->addError ( Mage::helper ( 'adminhtml' )->__ ( 'Please select item(s)' ) );
        } else {
            try {
                foreach ( $countryCodeVal as $id ) {
                    /**
                     * Delete id(s) from collection.
                     */
                    $modelCollet = Mage::getModel ( 'airhotels/managebankdetails' );
                    $modelCollet->load ( $id )->delete ();
                }
                /**
                 * Set success message.
                 * Redirect to grid page.
                 */
                Mage::getSingleton ( 'adminhtml/session' )->addSuccess ( Mage::helper ( 'adminhtml' )->__ ( 'Total of %d record(s) were deleted.', count ( $tagIds ) ) );
                /**
                 * Set values to session for total records
                 */
                $this->_redirect ( '*/*/' );
            } catch ( Exception $e ) {                
                Mage::getSingleton ( 'adminhtml/session' )->addError ( $e->getMessage () );
                $this->_redirect ( '*/*/edit', array (
                        'id' => $this->getRequest ()->getParam ( 'id' ) 
                ) );
                /**
                 * Set error messages and redirect to edit action
                 */
            }
        }       
        $this->_redirect ( '*/*/index' );
        /**
         * Redirect to index action.
         */
    }
    
    /**
     * Setting for acl
     */
    protected function _isAllowed() {
        return true;
    }
}