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
 * Managesubscriptions Controller
 */
class Apptha_Airhotels_Adminhtml_ManagesubscriptionsController extends Mage_Adminhtml_Controller_Action {    
    /**
     * Function Name: indexAction
     * Load phtml file layout
     *
     * @return void
     */
    public function indexAction() {
        /**
         * Load the layout and render the layout
         */
        $this->loadLayout ();
        /**
         * Set the Active menu
         */
        $this->_setActiveMenu ( 'managesubscriptions/items' );
        $this->renderLayout ();
    }
    /**
     * Function Name: editAction
     * Edit product subscription data
     *
     * @return void
     */
    public function editAction() {
        /**
         * Get the Id value
         */
        $id = $this->getRequest ()->getParam ( 'id' );
        /**
         * load the Id for managesubscriptons
         */
        $model = Mage::getModel ( 'airhotels/managesubscriptions' )->load ( $id );
        /**
         * Get Data for ProductId
         */
        $product_id = $model->getData ( 'product_id' );
        /**
         * Load the ProductId
         */
        $productModel = Mage::getModel ( 'catalog/product' )->load ( $product_id );
        if ($model->getId () || $id == 0) {
            /**
             * Store data in session.
             */
            $data = Mage::getSingleton ( 'adminhtml/session' )->getFormData ( true );
            if (! empty ( $data )) {
                $model->setData ( $data );
            }
            /**
             * Registering the Detils
             */
            Mage::register ( 'managesubscriptionData', $productModel );
            /**
             * Registering the 'managesubscriptions_id'
             */
            Mage::register ( 'managesubscriptions_id', $product_id );
            /**
             * Registering the 'managesubscriptions_data'
             */
            Mage::register ( 'managesubscriptions_data', $model );
            $this->loadLayout ();
            $this->_setActiveMenu ( 'managesubscriptions/items' );
            /**
             * Add breadcrumb
             */
            $this->_addBreadcrumb ( Mage::helper ( 'adminhtml' )->__ ( 'Item News' ), Mage::helper ( 'adminhtml' )->__ ( 'Item News' ) );
            $this->getLayout ()->getBlock ( 'head' )->setCanLoadExtJs ( true );
            $this->_addBreadcrumb ( Mage::helper ( 'adminhtml' )->__ ( 'Item Manager' ), Mage::helper ( 'adminhtml' )->__ ( 'Item Manager' ) );
            /**
             * Adding the Head block
             */
            $this->_addContent ( $this->getLayout ()->createBlock ( 'airhotels/adminhtml_managesubscriptions_edit' ) )->_addLeft ( $this->getLayout ()->createBlock ( 'airhotels/adminhtml_managesubscriptions_edit_tabs' ) );
            /**
             * Render the Layout
             */
            $this->renderLayout ();
        } else {
            /**
             * Set error message.
             */
            Mage::getSingleton ( 'adminhtml/session' )->addError ( Mage::helper ( 'managesubscriptions' )->__ ( 'Item does not exist' ) );
            $this->_redirect ( '*/*/' );
        }
    }
    /**
     * Function Name: newAction
     * This loads a new grid, name as CreateSubscription in new Action.
     *
     * @return void
     */
    public function newAction() {
        /**
         * Load the layout
         */
        $this->loadLayout ();
        /**
         * Add Content
         */
        $this->_addContent ( $this->getLayout ()->createBlock ( 'airhotels/adminhtml_createsubscriptions' ) );
        $this->renderLayout ();
    }
    /**
     * Function Name: moveAction
     * Its For create subscription grid in addnew action.
     * it works same as edit action but name has changed as move action in create subscription gird.
     *
     * Create subscription type to new product.
     *
     * @return void
     */
    public function moveAction() {
        /**
         * get the Product ID
         */
        $productId = $this->getRequest ()->getParam ( 'id' );
        /**
         * load Id
         */
        $productModel = Mage::getModel ( 'catalog/product' )->load ( $productId );
        if ($productModel->getId () || $productId == 0) {
            $data = Mage::getSingleton ( 'adminhtml/session' )->getFormData ( true );
            if (! empty ( $data )) {
                $productModel->setData ( $data );
            }
            /**
             * Registering the create subscriptions data
             */
            Mage::register ( 'createsubscriptions_data', $productModel );
            $this->loadLayout ();
            /**
             * Adding the breadcrump
             */
            $this->_addBreadcrumb ( Mage::helper ( 'adminhtml' )->__ ( 'Item Manager' ), Mage::helper ( 'adminhtml' )->__ ( 'Item Manager' ) );
            $this->_setActiveMenu ( 'managesubscriptions/items' );
            $this->getLayout ()->getBlock ( 'head' )->setCanLoadExtJs ( true );
            /**
             * Add the Breadcrumb for 'Item News'
             */
            $this->_addBreadcrumb ( Mage::helper ( 'adminhtml' )->__ ( 'Item News' ), Mage::helper ( 'adminhtml' )->__ ( 'Item News' ) );
            /**
             * Get the Layout
             */
            /**
             * Add Content with create Block and addLeft
             */
            $this->_addContent ( $this->getLayout ()->createBlock ( 'airhotels/adminhtml_createsubscriptions_edit' ) )->_addLeft ( $this->getLayout ()->createBlock ( 'airhotels/adminhtml_createsubscriptions_edit_tabs' ) );
            /**
             * Rendering the layout
             */
            $this->renderLayout ();
        } else {
            /**
             * set error message.
             */
            Mage::getSingleton ( 'adminhtml/session' )->addError ( 'Subscriptions does not exist');
            $this->_redirect ( '*/*/' );
        }
    }    
    /**
     * Function Name: saveAction
     * This saves the product with its assigned subscription type .     
     * Save subscriptions data and change the status
     * 
     * Set recurring profile information in admin grid section
     * 
     *
     * @return void
     */
    public function saveAction() {
        if ($data = $this->getRequest ()->getPost ()) {
            $productId = $data ['product_id'];
            /**
             * Get the Product Detils based on the ProductId
             */
            $product = Mage::getModel ( 'catalog/product' )->load ( $productId );            
            $allProduct = Mage::getModel ( 'airhotels/managesubscriptions' )->getCollection ()->addFieldToFilter ( 'product_id', $productId )->getdata ();
            /**
             * Check weather the 'allProduct' Value is set
             */
            if (! empty ( $allProduct )) {
                foreach ( $allProduct as $manageProduct ) {
                    if ($manageProduct ['product_id'] != $productId) {
                        $productStatus = $product->getIsRecurring()?1:0; 
                    } else {
                        $productStatus = $manageProduct ['product_status'];
                        break;
                    }
                }
            } else {
                $productStatus = 1;
            }
            if (! $product->getIsRecurring ()) {
                $product->setIsRecurring ( '1' );
                /**
                 * Set the recurringProfile Array for 'period_frequency'
                 * Set the recurringProfile Array for 'day'
                 * Set the recurringProfile Array for 'trial_billing_amount'
                 * Set the recurringProfile Array for 'init_amount'
                 * Set the recurringProfile Array for 'period_frequency'
                 */
                $recurringProfile ['period_frequency'] = 1;               
                $recurringProfile ['period_unit'] = "day";               
                $recurringProfile ['trial_billing_amount'] = 1;                
                $recurringProfile ['init_amount'] = 1;               
                $product->setRecurringProfile ( $recurringProfile );               
                $storeId = Mage::app ()->getStore ()->getStoreId ();
                Mage::app ()->setCurrentStore ( Mage_Core_Model_App::ADMIN_STORE_ID );
                /**
                 * Save the Product Details
                 */
                $product->save ();
                Mage::app ()->setCurrentStore ( $storeId );
            }
            /**
             * isSubscriptionOnly and startDate value null
             */
            $isSubscriptionOnly = null;
            $subscriptionType = $pricePerIteration = $productcollection = $collection = $productIdValue = $id = $isDelete = $productModel = array ();
            $isSubscriptionOnly = $data ['is_subscription_only'];            
            /**
             * Check the value of 'subscription_type'
             */
            $subscriptionType = $data ['subscription_type'];
            $maximumValue = sizeof ( $subscriptionType );
            $pricePerIteration = $data ['price_per_iteration'];           
            /**
             * Get Collection Value for 'airhotels/productsubscriptions' with add field to filter
             */
            $productcollection = Mage::getModel ( 'airhotels/productsubscriptions' )->getCollection ()->addFieldToFilter ( 'is_delete', array (
                    'eq' => '0' 
            ) );
            /**
             * Get productCollection for Data
             * Set the Id,$isDelete,$productIdValue Values
             * Load product subscription informations
             */
            $collection = $productcollection->getData ();            
            for($i = 0; $i < count ( $collection ); $i ++) {              
                $id = $collection [$i] ['id'];
                $isDelete = $collection [$i] ['is_delete'];
                $productIdValue = $collection [$i] ['product_id'];
                if (($this->getRequest ()->getPost ( 'product_id' ) == $productIdValue) && ($isDelete == 0)) {                   
                    $productcollections = Mage::getModel ( 'airhotels/productsubscriptions' )->load ( $id );
                    $productcollections->setIsDelete ( '1' );
                    $productcollections->save ();
                }
            }
            $pricePerIterationValue = $pricePerIterationValue = '';            
            for($i = 1; $i < $maximumValue; $i ++) {
                /**
                 * Get Model of product subscriptions
                 */
                $productModel = Mage::getModel ( 'airhotels/productsubscriptions' );
                $subscriptionTypeList = $subscriptionType [$i];
                $pricePerIterationValue = $pricePerIteration [$i];                
                $productModel->setData ( 'product_id', $productId );
                $productModel->setData ( 'subscription_type', $subscriptionTypeList );
                $productModel->setData ( 'is_subscription_only', $isSubscriptionOnly );
                $productModel->setData ( 'price_per_iteration', $pricePerIterationValue );
                /**
                 * Save the product Model
                 */
                $productModel->save ();
                $values = array (
                        '$subscriptionTypeList' => $subscriptionType [$i],
                        '$price' => $pricePerIteration [$i] 
                );              
                unset ( $values );
            }
            try {
                $lastId = Mage::getModel ( "airhotels/managesubscriptions" )->saveSubscriptionInfo($data,$productStatus);                
                if ($i == ($maximumValue - 1)) {
                    /**
                     * Set success message.
                     */                    
                    Mage::getSingleton ( 'adminhtml/session' )->addSuccess ( Mage::helper ( 'airhotels' )->__ ( 'Item was successfully saved' ) );
                }
                if ($this->getRequest ()->getParam ( 'back' )) {
                    $this->_redirect ( '*/*/edit', array (
                            'id' => $lastId 
                    ) );
                    return;
                }
                Mage::getSingleton ( 'adminhtml/session' )->addSuccess ( Mage::helper ( 'airhotels' )->__ ( 'Item was successfully saved' ) );
                $this->_redirect ( '*/*/' );
            } catch ( Exception $e ) {
                /**
                 * Set error message.
                 * Set form data.
                 */
                Mage::getSingleton ( 'adminhtml/session' )->addError ( 'Not Saved. Error:' . $e->getMessage () );
                Mage::getSingleton ( 'adminhtml/session' )->setExampleFormData ( $data );
                $this->_redirect ( '*/*/edit', array (
                        'id' => $lastId,
                        '_current' => true 
                ) );
            }
        } else {            
            Mage::getSingleton ( 'adminhtml/session' )->addError ( Mage::helper ( 'airhotels' )->__ ( 'Unable to find item to save' ) );
            $this->_redirect ( '*/*/' );
        }
    }    
    /**
     * Function Name: deleteAction
     * Delete a product subscription data.
     *
     * @return void
     */
    public function deleteAction() {
        if ($this->getRequest ()->getParam ( 'id' ) > 0) {
            try {
                $productId = $id = null;
                /**
                 * Get the Id Value
                 */
                $id = $this->getRequest ()->getParam ( 'id' );
                $manageCollection = Mage::getModel ( 'airhotels/managesubscriptions' )->load ( $id );
                /**
                 * Get product ID
                 */
                $productId = $manageCollection->getProductId ();
                /**
                 * From the product ID load product
                 */
                $product = Mage::getModel ( 'catalog/product' )->load ( $productId );
                if ($product->getIsRecurring ()) {
                    /**
                     * Set is_recurring as 0.
                     */
                    $product->setIsRecurring ( '0' );
                    $storeId = Mage::app ()->getStore ()->getStoreId ();
                    Mage::app ()->setCurrentStore ( Mage_Core_Model_App::ADMIN_STORE_ID );
                    /**
                     * Save the Product Save
                     */
                    $product->save ();
                    Mage::app ()->setCurrentStore ( $storeId );
                }
                /**
                 * Get the colletion of Managesubscriptions
                 */
                $modelSubscription = Mage::getModel ( 'airhotels/managesubscriptions' );
                /**
                 * Get subscription collection
                 */
                $modelSubscription->setId ( $this->getRequest ()->getParam ( 'id' ) )->delete ();
                $modelSubscription->delete ();
                /**
                 * Get delete params from subscription
                 * Redirect to same page
                 */
                Mage::getSingleton ( 'adminhtml/session' )->addSuccess ('Subscriptions was successfully deleted');
                $this->_redirect ( '*/*/' );
            } catch ( Exception $e ) {
                /**
                 * Get session redirection.
                 * Redirect to edit page.
                 */
                Mage::getSingleton ( 'adminhtml/session' )->addError ( $e->getMessage () );
                $this->_redirect ( '*/*/edit', array ('id' => $this->getRequest ()->getParam ( 'id' )) );
            }
        }
        /**
         * Reditect to same page.
         */
        $this->_redirect ( '*/*/' );
    }    
    /**
     * Setting for acl
     */
    protected function _isAllowed() {
        return true;
    }
}