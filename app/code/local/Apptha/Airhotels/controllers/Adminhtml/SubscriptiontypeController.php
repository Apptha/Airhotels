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
 * SubscriptionType Controller
 */
class Apptha_Airhotels_Adminhtml_SubscriptiontypeController extends Mage_Adminhtml_Controller_Action {
    /**
     * Function Name: _initAction
     * Initialize method
     *
     * @return Apptha_airhotelsAdminhtml_SubscriptiontypeController
     */
    protected function _initAction() {
        /**
         * Add bread crumbs message.
         */
        $this->loadLayout ()->_setActiveMenu ( 'airhotels/subscriptiontype' )->_addBreadcrumb ( Mage::helper ( 'adminhtml' )->__ ( 'Subscription Type' ), Mage::helper ( 'adminhtml' )->__ ( 'Subscription Type' ) );
        
        return $this;
    }
    /**
     * Function Name: indexAction
     * index Method
     */
    public function indexAction() {
        /**
         * Load the layout
         */
        $this->_initAction ()->renderLayout ();
    }
    
    /**
     * Function Name: editAction
     * To Edit the SubscriptionType Table
     */
    public function editAction() {
        /**
         * Get the Value of 'Id'.
         */
        $varId = $this->getRequest ()->getParam ( 'id' );
        /**
         * Load the subscription type.
         */
        $subscriptionType = Mage::getModel ( 'airhotels/subscriptiontype' )->load ( $varId );
        
        /**
         * Check the getId value.
         */
        if ($subscriptionType->getId () || $varId == 0) {
            $data = Mage::getSingleton ( 'adminhtml/session' )->getFormData ( true );
            
            if (! empty ( $data )) {
                $subscriptionType->setData ( $data );
            }
            /**
             * Registering the subscriptiontype_data.
             */
            Mage::register ( 'subscriptiontype_data', $subscriptionType );
            
            /**
             * Load the layout.
             */
            $this->loadLayout ();
            /**
             * get the Head layout
             */
            $this->getLayout ()->getBlock ( 'head' )->setCanLoadExtJs ( true );
            /**
             * Set Active menu for 'airhotels/subscriptiontype'
             */
            $this->_setActiveMenu ( 'airhotels/subscriptiontype' );
            /**
             * Add Content.
             */
            $this->_addContent ( $this->getLayout ()->createBlock ( 'airhotels/adminhtml_subscriptiontype_edit' ) )->_addLeft ( $this->getLayout ()->createBlock ( 'airhotels/adminhtml_subscriptiontype_edit_tabs' ) );
            /**
             * Add the breadcrumb for 'adminhtml'.
             */
            $this->_addBreadcrumb ( Mage::helper ( 'adminhtml' )->__ ( 'Customer Manager' ), Mage::helper ( 'adminhtml' )->__ ( 'Customer Manager' ) );
            /**
             * Add the breadcrumb value.
             */
            $this->_addBreadcrumb ( Mage::helper ( 'adminhtml' )->__ ( 'Item News' ), Mage::helper ( 'adminhtml' )->__ ( 'Item News' ) );
            /**
             * Rendering the Layout.
             */
            $this->renderLayout ();
        } else {
            /**
             * Set error message.
             *
             * Add redirecxtion page.
             */
            Mage::getSingleton ( 'adminhtml/session' )->addError ( Mage::helper ( 'airhotels' )->__ ( 'customer does not exist.' ) );
            $this->_redirect ( '*/*/' );
        }
    }
    /**
     * Action for Edit.
     */
    public function newAction() {
        $this->_forward ( 'edit' );
    }
    
    /**
     * Function Name: SaveAction
     * To save the data into SubscriptionType Table
     */
    public function saveAction() {
        /**
         * Get the all request post datas
         */
        if ($data = $this->getRequest ()->getPost ()) {
            $data ['engine_code'] = 0;
            if (isset ( $data ['is_infinite'] ) && $data ['is_infinite'] == "1") {
                $data ['billing_cycle'] = "Infinite";
            }
            /**
             * Get Model for subscriptionType
             */
            $model = Mage::getModel ( 'airhotels/subscriptiontype' );
            /**
             * Get the id Value
             */
            if ($Id = $this->getRequest ()->getParam ( 'id' )) {
                $model->load ( $Id );
            }
            /**
             * addData value
             */
            $model->addData ( $data );
            /**
             * Get the Subscription type
             */
            $collection = Mage::getModel ( 'airhotels/subscriptiontype' )->getCollection ()->getLastItem ();
            $title = $collection->getTitle ();
            /**
             * Set the Billing period Unit,Billing period frequency, billing Cycle
             */
            $billingPeriodUnit = $collection->getBilling_period_unit ();
            /**
             * billing freqency Value
             */
            $billingFrequency = $collection->getBillingFrequency ();
            $billingCycle = $collection->getBillingCycle ();
            /**
             * Set the ID, Status, in_infinite Values
             */
            $id = $collection->getId ();
            /**
             * Get the status value
             */
            $collection->getStatus ();
            /**
             * Get the is_infinite Value
             */
            try {
                /**
                 * check weather the Value is empty
                 */
                if (! empty ( $title ) && $title == $data ['title'] && $billingPeriodUnit == $data ['billing_period_unit'] && $billingFrequency == $data ['billing_frequency']) {
                    if ($billingCycle == $data ['billing_cycle']) {
                        /**
                         * Get subscription type collection
                         *
                         * @var $modelCollection.
                         */
                        $modelCollection = Mage::getModel ( 'airhotels/subscriptiontype' )->load ( $id )->addData ( $data );
                        $modelCollection->setId ( $id )->save ();
                        /**
                         * success save message
                         */
                        Mage::getSingleton ( 'adminhtml/session' )->addSuccess ( 'Saved Successfully' );
                    }
                } else {
                    /**
                     * Save data and set success message.
                     */
                    $model->save ();
                    Mage::getSingleton ( 'adminhtml/session' )->addSuccess ( 'Saved Successfully' );
                }
                if (Mage::getStoreConfig ( 'airhotels/subscription/activate_subscription_enable' ) == 0 && Mage::getStoreConfig ( 'payment/paypaladaptive/active' ) == 0) {
                    $emailTemplate = Mage::getModel ( 'core/email_template' )->loadDefault ( 'airhotels_paypal_adaptive_enable_notification_template' );
                    
                    /**
                     * Getting Email values
                     */
                    $adminEmailId = Mage::getStoreConfig ( 'airhotels/custom_email/admin_email_id' );
                    $recipient = Mage::getStoreConfig ( "trans_email/ident_$adminEmailId/email" );
                    $aminName = Mage::getStoreConfig ( "trans_email/ident_$adminEmailId/name" );
                    /**
                     * Set sender name,Sender email as no-reply.
                     */
                    $emailTemplate->setSenderName ( 'no-reply' );
                    $emailTemplate->setSenderEmail ( 'noreply@' . Mage::app ()->getRequest ()->getServer ( 'HTTP_HOST' ) );
                    $emailTemplateVariables = array (
                            'cname' => $aminName 
                    );
                    $emailTemplate->setDesignConfig ( array (
                            'area' => 'frontend' 
                    ) );
                    /**
                     * Sending email to admin
                     */
                    $emailTemplate->getProcessedTemplate ( $emailTemplateVariables );
                    
                    $emailTemplate->send ( $recipient, $aminName, $emailTemplateVariables );
                }
                /**
                 * Get the request Value for 'back'
                 */
                if ($this->getRequest ()->getParam ( 'back' )) {
                    $this->_redirect ( '*/*/edit', array ('id' => $model->getId ()) );
                    return;
                }
                /**
                 * Rediect page
                 */
                $this->_redirect ( '*/*/' );
            } catch ( Exception $e ) {
                /**
                 * Added Error Notification
                 *
                 * Redirect edit page.
                 */
                Mage::getSingleton ( 'adminhtml/session' )->addError ( 'Not Saved. Error:' . $e->getMessage () );
                Mage::getSingleton ( 'adminhtml/session' )->setExampleFormData ( $data );
                $this->_redirect ( '*/*/edit', array ('id' => $model->getId (),'_current' => true) );
            }
        }
    }
    /**
     * Function Name: deleteAction.
     *
     * To delete a record in SubscriptionType Table
     */
    public function deleteAction() {
        /**
         * Get the Requst Value
         */
        if ($this->getRequest ()->getParam ( 'id' ) > 0) {
            try {
                $id = $this->getRequest ()->getParam ( 'id' );
                
                /**
                 * Get Product subscription Colletions
                 */
                $collection = Mage::getModel ( 'airhotels/productsubscriptions' )->getCollection ();
                /**
                 * Add filetr to subscription Colletions
                 */
                $subscripitonCollection = $collection->addFieldToFilter ( 'subscription_type', $id )->addFieldToFilter ( 'is_delete', 0 )->getData ();
                /**
                 * Iterating the loop
                 */
                
                /**
                 * To delete a subscription type record for particular product.
                 *
                 * @method isSubscriptionDelete
                 * @param $productModel (it
                 *            is a collection)
                 *            return boolean
                 */
                $this->subscriptionProductDelete ( $subscripitonCollection );
                /**
                 * Model for subscriptiontype
                 */
                $model = Mage::getModel ( 'airhotels/subscriptiontype' );
                $model->setId ( $this->getRequest ()->getParam ( 'id' ) )->delete ();
                /**
                 * Add success notification.
                 *
                 * Redirect to admin page.
                 */
                Mage::getSingleton ( 'adminhtml/session' )->addSuccess ( Mage::helper ( 'adminhtml' )->__ ( 'Total of %d record(s) were successfully deleted', count ( 1 ) ) );
                $this->_redirect ( '*/*/' );
            } catch ( Exception $e ) {
                /**
                 * Set error message.
                 *
                 * Redirect to edit page.
                 */
                Mage::getSingleton ( 'adminhtml/session' )->addError ( $e->getMessage () );
                $this->_redirect ( '*/*/edit', array (
                        'id' => $this->getRequest ()->getParam ( 'id' ) 
                ) );
            }
        }
        $this->_redirect ( '*/*/' );
    }
    
    /**
     * Function Name : subscriptionProductDelete
     * subscription Product Delete
     *
     * @param unknown $subscripitonCollection            
     */
    public function subscriptionProductDelete($subscripitonCollection) {
        /**
         * Declare the values for given values,
         * 'subscription'
         * 'collection'
         * 'productid'
         * 'subscriptionIds'
         */
        $subscription = $collection = $productid = $subscriptionCollectionCount = $subscriptionIds = array ();
        $subscriptionCollectionCount = $subscripitonCollection;
        foreach ( $subscriptionCollectionCount as $value ) {
            /**
             * Colletion for Produt Subscriptions
             */
            $collection = Mage::getModel ( 'airhotels/productsubscriptions' )->getCollection ()->addFieldToFilter ( 'subscription_type', $value ['subscription_type'] )->addFieldToFilter ( 'is_delete', 0 )->getData ();
        }
        
        if (count ( $collection ) >= 1) {
            foreach ( $collection as $subcollection ) {
                if (! empty ( $subcollection )) {
                    $productid [] = $subcollection ['product_id'];
                    $subscription [] = $subcollection ['subscription_type'];
                }
            }
            /**
             * SubscriptionIds to Values.
             */
            $subscriptionIds = array_values ( array_unique ( $subscription ) );
            $productCollection = $manageCollection = array ();
            if (count ( $productid ) > 1) {
                foreach ( $subscriptionIds as $subscriptionTypeId ) {
                    foreach ( $productid as $ids ) {
                        /**
                         * Product Subscription Collction
                         */
                        $productCollection = Mage::getModel ( 'airhotels/productsubscriptions' )->getCollection ()->addFieldToFilter ( 'product_id', $ids )->addFieldToFilter ( 'is_delete', 0 )->getData ();
                        /**
                         * check the ProductCOllection value is greater than one
                         */
                        $this->subscriptionProductDeleteItem ( $subscriptionTypeId, $productCollection, $ids );
                    }
                }
            } else if (count ( $productid ) == 1) {
                foreach ( $productid as $ids ) {
                    foreach ( $subscriptionIds as $subscriptionTypeId ) {
                        /**
                         * product Colletion with subscription_type filetr
                         */
                        $productCollection = Mage::getModel ( 'airhotels/productsubscriptions' )->getCollection ()->addFieldToFilter ( 'product_id', $ids )->addFieldToFilter ( 'subscription_type', $subscriptionTypeId )->addFieldToFilter ( 'is_delete', 0 );
                        $manageCollection = Mage::getModel ( 'airhotels/managesubscriptions' )->getCollection ()->addFieldToFilter ( 'product_id', $ids );
                        $this->manageSubscriptionProducts ( $productCollection, $manageCollection );
                    }
                }
            } else {
                continue;
            }
        }
    }
    
    /**
     * Setting for acl
     */
    protected function _isAllowed() {
        return true;
    }
    /**
     * Function to detele product from subscription
     * 
     * @param unknown $subscriptionTypeId            
     * @param unknown $ids            
     */
    public function subscriptionProductDeleteItem($subscriptionTypeId, $productCollection, $ids) {
        if (count ( $productCollection ) > 1) {
            $productCollection = Mage::getModel ( 'airhotels/productsubscriptions' )->getCollection ()->addFieldToFilter ( 'product_id', $ids )->addFieldToFilter ( 'subscription_type', $subscriptionTypeId )->addFieldToFilter ( 'is_delete', 0 );
            foreach ( $productCollection as $items ) {
                $items->delete ()->save ();
            }
            $subscriptionCollection = Mage::getModel ( 'airhotels/subscriptiontype' )->load ( $subscriptionTypeId );
            foreach ( $subscriptionCollection as $items ) {
                $items->delete ()->save ();
            }
        } else {
            /**
             * Product Subscription Collction with essential filters
             */
            $productCollection = Mage::getModel ( 'airhotels/productsubscriptions' )->getCollection ()->addFieldToFilter ( 'product_id', $ids )->addFieldToFilter ( 'subscription_type', $subscriptionTypeId )->addFieldToFilter ( 'is_delete', 0 );
            /**
             * Get Colletion for 'airhotels/managesubscriptions' with add an filter 'product_id'
             */
            $manageCollection = Mage::getModel ( 'airhotels/managesubscriptions' )->getCollection ()->addFieldToFilter ( 'product_id', $ids );
            $product = array ();
            /**
             * Get Model for 'catalog/product' and load the $id
             */
            $product = Mage::getModel ( 'catalog/product' )->load ( $ids );
            /**
             * check weather the IsRecurring value is enabled
             */
            if ($product->getIsRecurring ()) {
                $product->setIsRecurring ( '0' );
                $storeId = Mage::app ()->getStore ()->getStoreId ();
                Mage::app ()->setCurrentStore ( Mage_Core_Model_App::ADMIN_STORE_ID );
                /**
                 * Save Product Colleection
                 */
                $product->save ();
                Mage::app ()->setCurrentStore ( $storeId );
            }
            /**
             * Iterating the Array
             */
            foreach ( $productCollection as $items ) {
                $items->delete ()->save ();
            }
            foreach ( $manageCollection as $items ) {
                $items->delete ()->save ();
            }
        }
    }
    /**
     * Function to delete and manage products in the subscription
     * 
     * @param unknown $productCollection            
     * @param unknown $manageCollection            
     */
    public function manageSubscriptionProducts($productCollection, $manageCollection) {
        foreach ( $productCollection as $items ) {
            /**
             * Delete and save
             */
            $items->delete ()->save ();
        }
        foreach ( $manageCollection as $items ) {
            $items->delete ()->save ();
        }
    }
}