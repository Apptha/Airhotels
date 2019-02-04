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
class Apptha_Airhotels_Adminhtml_AirhotelsController extends Mage_Adminhtml_Controller_Action {
    const EMAIL_ADMIN_TEMPLATE_XML_PATH = 'airhotels/refund_email/refund_template';
    /**
     * Initialising the Airhotels Controller
     *
     * @return Apptha_Airhotels_Adminhtml_AirhotelsController
     */
    protected function _initAction() {
        /**
         * To load Layout
         */
        $this->loadLayout ()->_setActiveMenu ( 'airhotels/items' )->/**
         * Setting breadcrumb
         */
        _addBreadcrumb ( Mage::helper ( 'adminhtml' )->__ ( 'Items Manager' ), Mage::helper ( 'adminhtml' )->__ ( 'Item Manager' ) );
        return $this;
    }
    /**
     * Index method for Airhotels Controller
     */
    public function indexAction() {
        /**
         * To render Layout
         */
        $this->_initAction ()->renderLayout ();
    }
    /**
     * Property record edit action
     */
    public function editAction() {
        /**
         * Getting Product Id
         */
        $id = $this->getRequest ()->getParam ( 'id' );
        /**
         * Loading Property details
         */
        $model = Mage::getModel ( 'airhotels/airhotels' )->load ( $id );
        $currencySymbol = Mage::app ()->getLocale ()->currency ( $model ['order_currency_code'] )->getSymbol ();
        /**
         * Get grand total.
         * Get service fee.
         * Get host fee.
         */
        $model ['host_total_fee'] = $currencySymbol . ($model ['grand_total'] - $model ['service_fee'] - $model ['host_fee']);
        $model ['grand_total'] = $currencySymbol . $model ['grand_total'];
        $model ['service_fee'] = $currencySymbol . $model ['service_fee'];
        $model ['host_fee'] = $currencySymbol . $model ['host_fee'];
        if ($model->getId () || $id == 0) {
            /**
             * Set data in session.
             */
            $data = Mage::getSingleton ( 'adminhtml/session' )->getFormData ( true );
            if (! empty ( $data )) {
                $model->setData ( $data );
            }
            /**
             * Registering the Module
             */
            Mage::register ( 'airhotels_data', $model );
            /**
             * load the Layout and render files 
             */
            $this->loadLayout ();
            /**
             * Add the breadCrumbs for Item Manager
             */
            $this->_addBreadcrumb ( Mage::helper ( 'adminhtml' )->__ ( 'Item Manager' ), Mage::helper ( 'adminhtml' )->__ ( 'Item Manager' ) );
            $this->_addBreadcrumb ( Mage::helper ( 'adminhtml' )->__ ( 'Item News' ), Mage::helper ( 'adminhtml' )->__ ( 'Item News' ) );
            $this->getLayout ()->getBlock ( 'head' )->setCanLoadExtJs ( true );
            /**
             * create new block to use Add Contents
             */
            $this->_addContent ( $this->getLayout ()->createBlock ( 'airhotels/adminhtml_airhotels_edit' ) )->_addLeft ( $this->getLayout ()->createBlock ( 'airhotels/adminhtml_airhotels_edit_tabs' ) );
            /**
             * Set active menu
             */
            $this->_setActiveMenu ( 'airhotels/items' );            
            $this->renderLayout ();
        } else {
            Mage::getSingleton ( 'adminhtml/session' )->addError ( Mage::helper ( 'airhotels' )->__ ( 'Item does not exist' ) );
            /**
             * Redirect URL
             */
            $this->_redirect ( '*/*/' );
        }
    }
    /**
     * Save the edited details action
     */
    public function saveAction() {
        if ($data = $this->getRequest ()->getPost ()) {
            /**
             * Getting Id
             */
            $id = $this->getRequest ()->getParam ( 'id' );
            /**
             * get Collection of Airhotels
             */
            $model = Mage::getModel ( 'airhotels/airhotels' );
            $model->setData ( $data )->setId ( $id );
            try {
                /**
                 * SaveCollection Values
                 */
                $model->save ();
                /**
                 * Loading Property details
                 */
                $dataValue = $model->load ( $id );
                /**
                 * Get details and assign to variables for sending email
                 */
                if ($dataValue->getStatus () == 1 || $dataValue->getStatus () == 2) {
                    /**
                     * Set the MailTemplte
                     */
                    $mailTemplate = Mage::helper ( 'airhotels/general' )->sendEmailTempate ( $dataValue );
                    $this->addRedirect ( $mailTemplate );
                }
                /**
                 * Set the FormData to session
                 */
                Mage::getSingleton ( 'adminhtml/session' )->setFormData ( false );
                $this->back ( $model );
                /**
                 * Redirect Url
                 */
                $this->_redirect ( '*/*/' );
            } catch ( Exception $e ) {
                /**
                 * Set error message.
                 * Set form data.
                 */
                Mage::getSingleton ( 'adminhtml/session' )->addError ( $e->getMessage () );
                Mage::getSingleton ( 'adminhtml/session' )->setFormData ( $data );
                $this->_redirect ( '*/*/edit', array (
                        'id' => $this->getRequest ()->getParam ( 'id' ) 
                ) );
            }
            return;
        }        
        Mage::getSingleton ( 'adminhtml/session' )->addError ( Mage::helper ( 'airhotels' )->__ ( 'Unable to find item to save' ) );
        $this->_redirect ( '*/*/' );
        /**
         * Adding error session Message
         * and redirect to session files
         */
    }
    /**
     * Delete property records
     */
    public function deleteAction() {
        /**
         * Get request param from ID
         */
        if ($this->getRequest ()->getParam ( 'id' ) > 0) {
            try {
                /**
                 * Get Model of Airhotels
                 */
                $modelDeleteCollection = Mage::getModel ( 'airhotels/airhotels' );                
                $modelDeleteCollection->setId ( $this->getRequest ()->getParam ( 'id' ) );
                $modelDeleteCollection->delete ();
                /**
                 * laod the ID to the collection
                 */
                /**
                 * Adding success session message
                 */
                Mage::getSingleton ( 'adminhtml/session' )->addSuccess ('Orders was successfully deleted' ) ;
                /**
                 * Adding redirect url to current page
                 */
                $this->_redirect ( '*/*/' );
            } catch ( Exception $e ) {
                /**
                 * Set session message
                 */
                Mage::getSingleton ( 'adminhtml/session' )->addError ( $e->getMessage () );
                $this->_redirect ( '*/*/edit', array ('id' => $this->getRequest ()->getParam ( 'id' )) );
            }
        }
        $this->_redirect ( '*/*/' );
    }
    /**
     * Delete multiple selected property records
     */
    public function massDeleteAction() {
        $airhotelsIds = $this->getRequest ()->getParam ( 'airhotels' );
        if (! is_array ( $airhotelsIds )) {
            /**
             * Set error message.
             */
            Mage::getSingleton ( 'adminhtml/session' )->addError ( Mage::helper ( 'adminhtml' )->__ ( 'Please select item(s)' ) );
        } else {
            try {
                /**
                 * Set the Array Value
                 */
                $airhotelsIdsArray = array ();
                /**
                 * Iterating the loop
                 */
                foreach ( $airhotelsIds as $airhotelsId ) {
                    /**
                     * Defining array
                     */
                    $airhotelsIdsArray [] = $airhotelsId;
                }
                /**
                 * Chec weather the array Value is higher
                 */
                if (count ( $airhotelsIdsArray ) >= 1) {
                    $coreResource = Mage::getSingleton ( 'core/resource' );
                    $conn = $coreResource->getConnection ( 'core_read' );
                    /**
                     * Delete Query
                     */
                    $conn->delete ( $coreResource->getTableName ( 'airhotels/airhotels' ), array (
                            'id IN(?)' => $airhotelsIdsArray 
                    ) );
                }
                /**
                 * Save the Values to session Data
                 */
                Mage::getSingleton ( 'adminhtml/session' )->addSuccess ( Mage::helper ( 'adminhtml' )->__ ( 'Total of %d record(s) were successfully deleted', count ( $airhotelsIdsArray ) ) );
            } catch ( Exception $e ) {
                /**
                 * Set error message.
                 */
                Mage::getSingleton ( 'adminhtml/session' )->addError ( $e->getMessage () );
            }
        }
        /**
         * Redirect URL
         */
        $this->_redirect ( '*/*/index' );
    }
    /**
     * Export Property records grid to CSV format
     */
    public function exportCsvAction() {
        $fileName = 'airhotels.csv';
        /**
         * Creating block
         */
        $content = $this->getLayout ()->createBlock ( 'airhotels/adminhtml_airhotels_grid' )->getCsv ();
        $this->_sendUploadResponse ( $fileName, $content );
    }
    /**
     * Export Property records grid to XML format
     */
    public function exportXmlAction() {
        $fileName = 'airhotels.xml';
        $content = $this->getLayout ()->createBlock ( 'airhotels/adminhtml_airhotels_grid' )->getXml ();
        $this->_sendUploadResponse ( $fileName, $content );
    }
    /**
     * Upload response
     *
     * @param string $fileName            
     * @param string $content            
     * @param string $contentType            
     */
    protected function _sendUploadResponse($fileName, $content, $contentType = 'application/octet-stream') {
        $response = $this->getResponse ();
        $response->setHeader ( 'HTTP/1.1 200 OK', '' );
        $response->setHeader ( 'Pragma', 'public', true );
        $response->setHeader ( 'Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true );
        $response->setHeader ( 'Content-Disposition', 'attachment; filename=' . $fileName );
        $response->setHeader ( 'Last-Modified', date ( 'r' ) );
        $response->setHeader ( 'Accept-Ranges', 'bytes' );
        $response->setHeader ( 'Content-Length', strlen ( $content ) );
        $response->setHeader ( 'Content-type', $contentType );
        $response->setBody ( $content );
        $this->getResponse ()->setBody ( $content );
    }
    /**
     * Get the back Value
     */
    public function back($model) {
        $back = $this->getRequest ()->getParam ( 'back' );
        if ($back) {
            /**
             * Redirect URL
             */
            $this->_redirect ( '*/*/edit', array (
                    'id' => $model->getId () 
            ) );
            return;
        }
    }
    /**
     * Redirect the Add Action
     */
    public function addRedirect($mailTemplate) {
        if (! $mailTemplate->getSentSuccess ()) {
            /**
             * Set error message.
             */
            $this->_getSession ()->addError ( "There is a problem in Sending Mail! Email not Sent!" );
            $this->_redirect ( '*/*/' );
            return;
        } else {
            /**
             * Adding success message
             */
            Mage::getSingleton ( 'adminhtml/session' )->addSuccess ( Mage::helper ( 'airhotels' )->__ ( 'Item was successfully saved' ) );
        }
    }
    /**
     * Setting for acl
     */
    protected function _isAllowed() {
        return true;
    }
    /**
     * Update property approval status in product grid.
     */
    public function approveAction() {
        $params = $this->getRequest ()->getParam ( 'product' );
        $approveStatus = Mage::app ()->getRequest ()->getParam ( 'approved' );
        
        if ($approveStatus == '2') {
            $approveStatus = '0';
        }
        /**
         * Get property approved collection.
         */
        $productCollection = Mage::getModel ( 'catalog/product' )->getCollection ()->addAttributeToFilter ( 'entity_id', array (
                'in' => $params 
        ) )->addAttributeToSelect ( 'propertyapproved' );
        
        foreach ( $productCollection as $product ) {
            $productInfo = Mage::getModel ( 'catalog/product' )->load ( $product ['entity_id'] );
            $product->setPeopleMin ( $productInfo ['people_min'] );
            $product->setPeopleMax ( $productInfo ['people_max'] );
            $product->setPropertyapproved ( $approveStatus );
            $product->save ();
        }
        /**
         * Set success message.
         */
        Mage::getSingleton ( 'core/session' )->addSuccess ( 'Property is updated successfully' );
        
        /**
         * Redirect URL
         */
        $this->_redirect ( 'adminhtml/catalog_product/index' );
        return;
    }
}