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
 * This class contains verification gird actions
 */
class Apptha_Airhotels_Adminhtml_VerifyhostController extends Mage_Adminhtml_Controller_action {
    /**
     * Index Action
     */
    public function indexAction() {
        $this->loadLayout ()->_setActiveMenu ( 'airhotels' );
        /**
         * Set page title.
         */
        $this->getLayout ()->getBlock ( 'head' )->setTitle ( 'Verify Host' );
        $this->renderLayout ();
    }
    /**
     * Mass Verification Action
     */
    public function massVerifyAction() {
        /**
         * Get the vrify host Id
         */
        $hostId = $this->getRequest ()->getParam ( 'verifyhost' );
        if (! is_array ( $hostId )) {
            /**
             * Display error message.
             */
            Mage::getSingleton ( 'adminhtml/session' )->addError ( Mage::helper ( 'adminhtml' )->__ ( 'Please select item(s)' ) );
        } else {
            try {
                foreach ( $hostId as $id ) {
                    $modelCollection = Mage::getModel ( 'airhotels/verifyhost' );
                    $modelCollection->load ( $id )->setHostTags ( 1 )->save ();
                    /**
                     * Save verify host mail.
                     */
                    Mage::helper ( 'airhotels/general' )->verifyHostMail ( $id, 'verified' );
                }
                /**
                 * Set success message.
                 */
                Mage::getSingleton ( 'adminhtml/session' )->addSuccess ( Mage::helper ( 'adminhtml' )->__ ( 'Total of %d record(s) were updated.', count ( $hostId ) ) );
                $this->_redirect ( '*/*/' );
            } catch ( Exception $e ) {
                /**
                 * Set error message
                 */
                Mage::getSingleton ( 'adminhtml/session' )->addError ( $e->getMessage () );
                $this->_redirect ( '*/*/edit', array (
                        'id' => $this->getRequest ()->getParam ( 'id' ) 
                ) );
            }
        }
        /**
         * Redirect to index action.
         */
        $this->_redirect ( '*/*/index' );
    }
    /**
     * Mass unverify Action
     */
    public function massRejectedAction() {
        $hostIds = $this->getRequest ()->getParam ( 'verifyhost' );
        if (! is_array ( $hostIds )) {
            /**
             * Set error message.
             */
            Mage::getSingleton ( 'adminhtml/session' )->addError ( Mage::helper ( 'adminhtml' )->__ ( 'Please select item(s)' ) );
        } else {
            try {
                foreach ( $hostIds as $id ) {
                    $model = Mage::getModel ( 'airhotels/verifyhost' );
                    $model->load ( $id )->setHostTags ( 2 )->save ();
                    Mage::helper ( 'airhotels/general' )->verifyHostMail ( $id, 'rejected' );
                }
                /**
                 * Set success message.
                 */
                Mage::getSingleton ( 'adminhtml/session' )->addSuccess ( Mage::helper ( 'adminhtml' )->__ ( 'Total of %d record(s) were updated.', count ( $hostIds ) ) );
                $this->_redirect ( '*/*/' );
            } catch ( Exception $e ) {
                /**
                 * Set error message.
                 */
                Mage::getSingleton ( 'adminhtml/session' )->addError ( $e->getMessage () );
                $this->_redirect ( '*/*/edit', array (
                        'id' => $this->getRequest ()->getParam ( 'id' ) 
                ) );
            }
        }
        /**
         * Redirect to index action.
         */
        $this->_redirect ( '*/*/index' );
    }
    
    /**
     * Delete multiple host action
     */
    public function massDeleteAction() {
        $tagId = $this->getRequest ()->getParam ( 'verifyhost' );
        if (! is_array ( $tagId )) {
            Mage::getSingleton ( 'adminhtml/session' )->addError ( Mage::helper ( 'adminhtml' )->__ ( 'Please select item(s)' ) );
        } else {
            try {
                /**
                 * Get collection from verify host.
                 */
                foreach ( $tagId as $id ) {
                    $modelCollection = Mage::getModel ( 'airhotels/verifyhost' );
                    Mage::helper ( 'airhotels/general' )->verifyHostMail ( $id, 'deleted' );
                    $modelCollection->load ( $id )->delete ();
                }
                /**
                 * Set success message.
                 */
                Mage::getSingleton ( 'adminhtml/session' )->addSuccess ( Mage::helper ( 'adminhtml' )->__ ( 'Total of %d record(s) were deleted.', count ( $tagId ) ) );
                $this->_redirect ( '*/*/' );
            } catch ( Exception $e ) {
                /**
                 * Set error message.
                 * Redirect to edit action
                 */
                Mage::getSingleton ( 'adminhtml/session' )->addError ( $e->getMessage () );
                $this->_redirect ( '*/*/edit', array (
                        'id' => $this->getRequest ()->getParam ( 'id' ) 
                ) );
            }
        }
        /**
         * Redirect to index acion
         */
        $this->_redirect ( '*/*/index' );
    }
    /**
     * Download Action
     */
    public function downloadAction() {
        $id = $this->getRequest ()->getParam ( 'id' );
        /**
         * Get verify host collection.
         */
        $host = Mage::getModel ( 'airhotels/verifyhost' )->load ( $id );
        $filePath = $host->getFilePath ();
        $this->getResponse ()->setHttpResponseCode ( 200 )->setHeader ( 'Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true )->setHeader ( 'Pragma', 'public', true )->setHeader ( 'Content-type', 'application/force-download' )->setHeader ( 'Content-Length', filesize ( $filePath ) )->setHeader ( 'Content-Disposition', 'attachment' . '; filename=' . basename ( $filePath ) );
        $this->getResponse ()->clearBody ();
        $this->getResponse ()->sendHeaders ();
        readfile ( $filePath );
    }
    
    /**
     * Setting for acl
     */
    protected function _isAllowed() {
        return true;
    }
}