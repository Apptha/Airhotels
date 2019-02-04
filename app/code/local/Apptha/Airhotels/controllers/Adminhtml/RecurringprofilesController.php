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
 * RecurringProfiles Controller
 */
class Apptha_Airhotels_Adminhtml_RecurringprofilesController extends Mage_Adminhtml_Controller_Action {
    /**
     * Initialize method
     * @return Apptha_Airhotels_Adminhtml_RecurringprofilesController
     */
    protected function _initAction() {
        $this->loadLayout ()->_setActiveMenu ( 'airhotels/recurringprofiles' )->_addBreadcrumb ( Mage::helper ( 'adminhtml' )->__ ( 'Customer Manager' ), Mage::helper ( 'adminhtml' )->__ ( 'Customer Manager' ) );
        return $this;
    }
    /**
     * Index Action
     */
    public function indexAction() {
        Mage::register ( 'ProfileStatus', 3 );
        $this->_initAction ()->renderLayout ();
    }
    /**
     * Active Action for active properties
     */
    public function activeAction() {
        /**
         * Print the Active
         */
        $active = Mage::helper ( 'airhotels' )->__ ( 'Active' );
        Mage::register ( 'ProfileStatus', 0 );
        Mage::register ( 'Status', $active );
        /**
         * render layout.
         */
        $this->_initAction ()->renderLayout ();
    }
    /**
     * Method for suspended
     */
    public function suspendedAction() {
        $suspended = Mage::helper ( 'airhotels' )->__ ( 'Suspended' );
        Mage::register ( 'ProfileStatus', 1 );
        Mage::register ( 'Status', $suspended );
        /**
         * Render layout.
         */
        $this->_initAction ()->renderLayout ();
    }
    /**
     * method for pending
     */
    public function pendingAction() {
        /**
         * Set text message
         * @var $pending
         */
        $pending = Mage::helper ( 'airhotels' )->__ ( 'Pending' );
        Mage::register ( 'ProfileStatus', 4 );
        Mage::register ( 'Status', $pending );
        /**
         * Render layout.
         */
        $this->_initAction ();
        $this->renderLayout ();
    }
    /**
     * method for Canceled
     */
    public function canceledAction() {
        /**
         * Set text message
         * @var $pending
         */
        $canceled = Mage::helper ( 'airhotels' )->__ ( 'Canceled' );
        Mage::register ( 'ProfileStatus', 2 );
        Mage::register ( 'Status', $canceled );
        $this->_initAction ();
        /**
         * Rendering the Layout
         */
        $this->renderLayout ();
    }
    /**
     * To view the recurringprofile
     */
    public function editAction() {
        $Id = $this->getRequest ()->getParam ( 'id' );
        /**
         * Get subscription type collection.
         * @var $model
         */
        $model = Mage::getModel ( 'airhotels/subscriptiontype' )->load ( $Id );
        /**
         * Get recurringprofile collection.
         * @var $modelProfile
         */
        $modelProfile = Mage::getModel ( 'airhotels/recurringprofiles' )->load ( $Id );
        if ($model->getId () || $Id == 0) {
            /**
             * Set form data in session.
             */
            $data = Mage::getSingleton ( 'adminhtml/session' )->getFormData ( true );
            if (! empty ( $data )) {
                $model->setData ( $data );
                $modelProfile->setData ( $data );
            }
            /**
             * Registering the Subscription type Data
             */
            Mage::register ( 'subscriptiontype_data', $model );
            Mage::register ( 'recurringprofiles_data', $modelProfile );
             /**
             * Load layout and set active menu.
             */
            $this->loadLayout ();
            $this->getLayout ()->getBlock ( 'head' )->setCanLoadExtJs ( true );
            $this->_setActiveMenu ( 'airhotels/recurring' );
            /**
             * Adding the Breadcrump
             */
            $this->_addBreadcrumb ( Mage::helper ( 'adminhtml' )->__ ( 'Customer Manager' ), Mage::helper ( 'adminhtml' )->__ ( 'Customer Manager' ) );
            $this->_addBreadcrumb ( Mage::helper ( 'adminhtml' )->__ ( 'Item News' ), Mage::helper ( 'adminhtml' )->__ ( 'Item News' ) );
            $this->_addContent ( $this->getLayout ()->createBlock ( 'airhotels/adminhtml_recurringprofiles_edit' ) )->_addLeft ( $this->getLayout ()->createBlock ( 'airhotels/adminhtml_recurringprofiles_edit_tabs' ) );
            /**
             * Rendering the Layout
             */
            $this->renderLayout ();
        } else {
            /**
             * Set error message.
             */
            Mage::getSingleton ( 'adminhtml/session' )->addError ( Mage::helper ( 'airhotels' )->__ ( 'customer does not exist' ) );
            $this->_redirect ( '*/*/' );
        }
    }
    /**
     * Setting for acl
     */
    protected function _isAllowed() {
        return true;
    }
}