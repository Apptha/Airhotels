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
 * This class contains document and video verification gird actions
 */
class Apptha_Airhotels_Adminhtml_VerificationController extends Mage_Adminhtml_Controller_action {
    /**
     * Index Action
     */
    public function indexAction() {
        /**
         * Load airhotels active menus.
         */
        $this->loadLayout ()->_setActiveMenu ( 'airhotels' );
        /**
         * Set page title.
         */
        $this->getLayout ()->getBlock ( 'head' )->setTitle ( 'Video and Document Verification' );
        $this->renderLayout ();
    }
    /**
     * Delete multiple tag action
     */
    public function massDeleteAction() {
        $tagIds = $this->getRequest ()->getParam ( 'tagData' );
        if (! is_array ( $tagIds )) {
            /**
             * Set error message.
             */
            Mage::getSingleton ( 'adminhtml/session' )->addError ( Mage::helper ( 'adminhtml' )->__ ( 'Please select item(s)' ) );
        } else {
            try {
                foreach ( $tagIds as $tag ) {
                    /**
                     * Save mass delete action.
                     */
                    $tagVerification= Mage::getModel ( 'airhotels/tagsverification' );
                    $tagVerification->load ( $tag )->delete ();
                    $tagVerification->delete ();
                }
                /**
                 * Set success message.
                 */
                Mage::getSingleton ( 'adminhtml/session' )->addSuccess ( Mage::helper ( 'adminhtml' )->__ ( 'Total of %d record(s) were deleted.', count ( $tagIds ) ) );
                $this->_redirect ( '*/*/' );
            } catch ( Exception $e ) {
                /**
                 * Set error message.
                 */
                Mage::getSingleton ( 'adminhtml/session' )->addError ( $e->getMessage () );
                /**
                 * Redirect to edit action page
                 */
                $this->_redirect ( '*/*/edit', array ('id' => $this->getRequest ()->getParam ( 'id' )) );
            }
        }
        /**
         * Redirect to index action page.
         */
        $this->_redirect ( '*/*/index' );
    }
    /**
     * New Action for verification
     */
    public function newAction() {
        /**
         * Redirect to edit action page.
         */
        $this->_forward ( 'edit' );
    }
    
    /**
     * Edit Action
     */
    public function editAction() {
        /**
         * Get parameters.
         *
         * @var $id
         */
        $id = $this->getRequest ()->getParam ( 'id', null );
        /**
         * Get tag verification collection
         *
         * @var $model
         */
        $model = Mage::getModel ( 'airhotels/tagsverification' );
        if ($id) {
            $model->load ( ( int ) $id );
            if ($model->getId ()) {
                /**
                 * Get form data.
                 */
                $data = Mage::getSingleton ( 'adminhtml/session' )->getFormData ( true );
                if ($data) {
                    $model->setData ( $data )->setId ( $id );
                }
            } else {
                /**
                 * Set error message.
                 */
                Mage::getSingleton ( 'adminhtml/session' )->addError ( Mage::helper ( 'adminhtml' )->__ ( 'Tag does not exist' ) );
                $this->_redirect ( '*/*/' );
            }
        }
        Mage::register ( 'verification_data', $model );
        /**
         * Render and load layout.
         */
        $this->loadLayout ();
        $this->getLayout ()->getBlock ( 'head' )->setCanLoadExtJs ( true );
        $this->renderLayout ();
    }
    
    /**
     * Save action for verifications
     */
    public function saveAction() {
        if ($data = $this->getRequest ()->getPost ()) {
            /**
             * Get tags verification collection
             *
             * @var $model
             */
            $model = Mage::getModel ( 'airhotels/tagsverification' );
            $id = $this->getRequest ()->getParam ( 'id' );
            if ($id) {
                $model->load ( $id );
                $directUrl = isset ( $data ['direct_url'] ) ? 1 : 0;
            }
            $model->setData ( $data );
            $model = $this->DirectUrl ( $directUrl, $model );
            /**
             * Set form data in session.
             */
            Mage::getSingleton ( 'adminhtml/session' )->setFormData ( $data );
            try {
                $model = $this->settingId ( $id, $model );
                $model->save ();
                if (! $model->getId ()) {
                    /**
                     * Set error message.
                     */
                    Mage::throwException ( Mage::helper ( 'adminhtml' )->__ ( 'Error saving example' ) );
                }
                /**
                 * Set success message.
                 */
                Mage::getSingleton ( 'adminhtml/session' )->addSuccess ( Mage::helper ( 'adminhtml' )->__ ( 'Tag was successfully saved.' ) );
                Mage::getSingleton ( 'adminhtml/session' )->setFormData ( false );
                if ($this->getRequest ()->getParam ( 'back' )) {
                    /**
                     * Redirect to edit action
                     */
                    $this->_redirect ( '*/*/edit', array (
                            'id' => $model->getId () 
                    ) );
                } else {
                    $this->_redirect ( '*/*/' );
                }
            } catch ( Exception $e ) {
                /**
                 * Set error message.
                 * Redirect to edit action.
                 */
                Mage::getSingleton ( 'adminhtml/session' )->addError ( $e->getMessage () );
                if ($model && $model->getId ()) {
                    $this->_redirect ( '*/*/edit', array (
                            'id' => $model->getId () 
                    ) );
                } else {
                    /**
                     * Set redirect url
                     */
                    $this->_redirect ( '*/*/' );
                }
            }
            return;
        }
        /**
         * Set error message.
         */
        Mage::getSingleton ( 'adminhtml/session' )->addError ( Mage::helper ( 'adminhtml' )->__ ( 'No data found to save' ) );
        $this->_redirect ( '*/*/' );
    }
    /**
     * Delete Action
     */
    public function deleteAction() {
        if ($id = $this->getRequest ()->getParam ( 'id' )) {
            try {
                /**
                 * Delete tags for verification.
                 */
                $model = Mage::getModel ( 'airhotels/tagsverification' );
                $model->setId ( $id );
                $model->delete ();
                /**
                 * Set success message.
                 */
                Mage::getSingleton ( 'adminhtml/session' )->addSuccess ( Mage::helper ( 'adminhtml' )->__ ( 'The Tag has been deleted.' ) );
                $this->_redirect ( '*/*/' );
                return;
            } catch ( Exception $e ) {
                /**
                 * Set error message.
                 * Redirect to edit action.
                 */
                Mage::getSingleton ( 'adminhtml/session' )->addError ( $e->getMessage () );
                $this->_redirect ( '*/*/edit', array (
                        'id' => $this->getRequest ()->getParam ( 'id' ) 
                ) );
                return;
            }
        }
        /**
         * Set error message.
         */
        Mage::getSingleton ( 'adminhtml/session' )->addError ( Mage::helper ( 'adminhtml' )->__ ( 'Unable to find the Tag to delete.' ) );
        $this->_redirect ( '*/*/' );
    }
    
    /**
     * Returning the DirectUrl
     *
     * @param String $directUrl            
     * @param Object $model            
     * @return Object
     */
    public function DirectUrl($directUrl, $model) {
        if (isset ( $directUrl )) {
            $model->setDirectUrl ( $directUrl );
        }
        return $model;
    }
    /**
     * Setting ID
     *
     * @param Int $id            
     * @param Object $model            
     * @return Object
     */
    public function settingId($id, $model) {
        if ($id) {
            $model->setId ( $id );
        }
        return $model;
    }
    
    /**
     * Setting for acl
     */
    protected function _isAllowed() {
        return true;
    }
}