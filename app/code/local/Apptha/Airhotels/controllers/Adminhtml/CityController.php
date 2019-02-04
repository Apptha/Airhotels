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
 * This class contains city gird actions
 */
class Apptha_Airhotels_Adminhtml_CityController extends Mage_Adminhtml_Controller_action {
    /**
     * Index Action
     */
    public function indexAction() {
        $this->loadLayout ()->_setActiveMenu ( 'airhotels' );
        /**
         * Set page title.
         */
        $this->getLayout ()->getBlock ( 'head' )->setTitle ( 'Add City With Image' );
        $this->renderLayout ();
    }
    /**
     * Adding New city
     */
    public function newAction() {
        $this->_forward ( 'edit' );
    }
    /**
     * Edit city image information
     */
    public function editAction() {
        $cityId = $this->getRequest ()->getParam ( 'id' );
        $modelCollection = Mage::getModel ( 'airhotels/city' )->load ( $cityId );
        if ($modelCollection->getId () || $cityId == 0) {
            /**
             * Get form data and set this to $modelCollection
             */
            $data = Mage::getSingleton ( 'adminhtml/session' )->getFormData ( true );
            if (! empty ( $data )) {
                $modelCollection->setData ( $data );
            }
            /**
             * Set city data for model collections
             */
            Mage::register ( 'city_data', $modelCollection );
            $this->loadLayout ();
            /**
             * Set airhotel menus to active
             */
            $this->_setActiveMenu ( 'airhotels' );
            if (empty ( $cityId )) {
                /**
                 * get block headings for add city images to load layouts
                 */
                $this->getLayout ()->getBlock ( 'head' )->setTitle ( 'Add City' );
            } else {                
                /**
                 * get block headings for edit city images to load layouts
                 */
                $this->getLayout ()->getBlock ( 'head' )->setTitle ( 'Edit City' );
            }            
            $this->_addContent ( $this->getLayout ()->createBlock ( 'airhotels/adminhtml_city_edit' ) );
            /**
             * creating new blocks using add content
             * 
             * load layout sections
             */
            $this->renderLayout ();
        } else {
            /**
             * Set error message.
             */
            Mage::getSingleton ( 'adminhtml/session' )->addError ( Mage::helper ( 'airhotels' )->__ ( 'Record does not exist' ) );           
            $this->_redirect ( '*/*/' );
        }
    }    
    /**
     * Save city images
     */
    public function saveAction() {        
        if ($data = $this->getRequest ()->getPost ()) {
            $imagesPath = $imageResizedPath = '';
            $id = $this->getRequest ()->getParam ( 'id' );
            $uploadsData = new Zend_File_Transfer_Adapter_Http ();
            $filesDataArray = $uploadsData->getFileInfo ();
            if (isset ( $filesDataArray ['city_image'] ['name'] ) && $filesDataArray ['city_image'] ['name'] != '') {
                try {
                    /**
                     * Storing city image
                     */
                    $imageId = $id;
                    if (empty ( $imageId )) {
                        $collectionForCount = Mage::getModel ( 'airhotels/city' )->getCollection ()->setOrder ( 'id', 'DESC' )->getFirstItem ();
                        $imageId = $collectionForCount->getId () + 1;
                    }
                    $path = Mage::getBaseDir ( 'media' ) . DS . 'airhotels' . DS . 'city' . DS;
                    $cityImageName = 'city_image';
                    /**
                     * Upload image for video image.
                     */
                    $imagesPath = Mage::helper ( 'airhotels/invitefriends' )->uploadImageForVideoImage ( $filesDataArray, $cityImageName, $path, $imageId );
                    /**
                     * Resizing city image into 639X315 resolution
                     */
                    if (! empty ( $imagesPath )) {
                        $imagepathArray = $this->cityImageUploader ( $imagesPath );
                        $imageResizedPath = $imagepathArray['0'];
                        $imageSmallResizedPath = $imagepathArray['1'];
                    }
                } catch ( Exception $e ) {
                    /**
                     * Set error message.
                     */
                    Mage::getSingleton ( 'adminhtml/session' )->addError ( $e->getMessage () );
                    $this->_redirect ( '*/*/edit', array (
                            'id' => $this->getRequest ()->getParam ( 'id' ) 
                    ) );                    
                }                
            } else {
                if (isset ( $data ['city_image'] ['delete'] ) && $data ['city_image'] ['delete'] == 1) {
                    if ($data ['city_image'] ['value'] != '') {
                        Mage::helper ( 'airhotels' )->removeFile ( $data ['city_image'] ['value'] );
                        $data ['city_image'] = '';
                    }
                } else {
                    unset ( $data ['city_image'] );
                }
            }
            /**
             * Load city collection by id
             */
            $cityInfo = Mage::getModel ( 'airhotels/city' )->load ( $id );
            $cityImageUrl = $cityInfo->getCityImage ();
            $cityThumbUrl = $cityInfo->getThumbImage ();
            $citySmallUrl = $cityInfo->getSmallImage ();
            if (! empty ( $imagesPath ) && empty ( $data ['city_image'] ['delete'] )) {
                $imageUrl = explode ( "media" . DIRECTORY_SEPARATOR, $imagesPath );
                if (isset ( $imageUrl [1] )) {
                    $data ['city_image'] = $imageUrl [1];
                } else {
                    $data ['city_image'] = $cityImageUrl;
                }
            }
            /**
             * Setting thumb image url for city
             */
            if (! empty ( $imageResizedPath )) {
                $data ['thumb_image'] = $imageResizedPath;
            } else {
                $data ['thumb_image'] = $cityThumbUrl;
            }
            /**
             * Setting small image url for city
             */
            if (! empty ( $imageSmallResizedPath )) {
                $data ['small_image'] = $imageSmallResizedPath;
            } else {
                $data ['small_image'] = $citySmallUrl;
            }
            /**
             * Uppercase the first character of each word in a string
             */
            if (isset ( $data ['city'] )) {
                $data ['city'] = ucwords ( $data ['city'] );
            }
            if (isset ( $data ['city'] )) {
                $cityCollection = Mage::getModel ( 'airhotels/city' )->getCollection ()->addFieldToFilter ( 'city', $data ['city'] )->getFirstItem ();
                $cityIdValue = $cityCollection->getId ();
                if (! empty ( $cityIdValue ) && $cityIdValue != $id) {
                    Mage::getSingleton ( 'adminhtml/session' )->addError ( Mage::helper ( 'airhotels' )->__ ( 'This City already added.' ) );
                    $this->_redirect ( '*/*/edit', array (
                            'id' => $this->getRequest ()->getParam ( 'id' ) 
                    ) );                    
                }
            }
            $model = Mage::getModel ( 'airhotels/city' );
            $model->setData ( $data )->setId ( $id );
            try {
                $model->save ();
                Mage::getSingleton ( 'adminhtml/session' )->addSuccess ( Mage::helper ( 'airhotels' )->__ ( 'Record has been saved successfully' ) );
                $this->_redirect ( '*/*/' );                
            } catch ( Exception $e ) {
                Mage::getSingleton ( 'adminhtml/session' )->addError ( $e->getMessage () );
                Mage::getSingleton ( 'adminhtml/session' )->setFormData ( $data );
                $this->_redirect ( '*/*/edit', array (
                        'id' => $this->getRequest ()->getParam ( 'id' ) 
                ) );                
            }            
        }        
        $this->_redirect ( '*/*/' );
    }
    /**
     * Delete city
     */
    public function deleteAction() {
        if ($this->getRequest ()->getParam ( 'id' ) > 0) {
            try {
                /**
                 * Delete city
                 */
                $model = Mage::getModel ( 'airhotels/city' );
                $model->setId ( $this->getRequest ()->getParam ( 'id' ) )->delete ();
                /**
                 * Set success message.
                 */
                Mage::getSingleton ( 'adminhtml/session' )->addSuccess ( Mage::helper ( 'adminhtml' )->__ ( 'Record has been removed successfully.' ) );
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
        $this->_redirect ( '*/*/' );
    }    
    /**
     * Delete multiple city action
     */
    public function massDeleteAction() {
        $citiesIds = $this->getRequest ()->getParam ( 'cities' );
        if (! is_array ( $citiesIds )) {
            /**
             * Set error message.
             */
            Mage::getSingleton ( 'adminhtml/session' )->addError ( Mage::helper ( 'adminhtml' )->__ ( 'Please select item(s)' ) );
        } else {
            try {
                foreach ( $citiesIds as $id ) {
                    $model = Mage::getModel ( 'airhotels/city' );
                    $model->setId ( $id )->delete ();
                }
                /**
                 * Set success message
                 */
                Mage::getSingleton ( 'adminhtml/session' )->addSuccess ( Mage::helper ( 'adminhtml' )->__ ( 'Total of %d record(s) were deleted.', count ( $citiesIds ) ) );
                
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
        $this->_redirect ( '*/*/index' );
    }    
    /**
     * Setting for acl
     */
    protected function _isAllowed() {
        return true;
    }
    /**
     * Function to upload images for city
     * @param unknown $imagesPath
     */
    public function cityImageUploader($imagesPath){
        $imageName = pathinfo ( $imagesPath, PATHINFO_FILENAME );
        $imageResized = Mage::getBaseDir ( 'media' ) . DS . 'airhotels' . DS . 'city' . DS . 'thumb' . DS . $imageName;
        $imageResizedPath = 'airhotels' . DS . 'city' . DS . 'thumb' . DS . $imageName;
        if (! empty ( $imageResized ) && file_exists ( $imagesPath )) {
            $imageObj = new Varien_Image ( $imagesPath );
            $imageObj->constrainOnly ( TRUE );
            $imageObj->keepAspectRatio ( FALSE );
            $imageObj->keepFrame ( FALSE );
            $imageObj->resize ( 639, 315 );
            $imageObj->save ( $imageResized );
        }
        $imageSmallResized = Mage::getBaseDir ( 'media' ) . DS . 'airhotels' . DS . 'city' . DS . 'small' . DS . $imageName;
        $imageSmallResizedPath = 'airhotels' . DS . 'city' . DS . 'small' . DS . $imageName;
        if (! empty ( $imageSmallResized ) && file_exists ( $imagesPath )) {
            $imageSmallObj = new Varien_Image ( $imagesPath );
            $imageSmallObj->constrainOnly ( TRUE );
            $imageSmallObj->keepAspectRatio ( FALSE );
            $imageSmallObj->keepFrame ( FALSE );
            $imageSmallObj->resize ( 308, 315 );
            $imageSmallObj->save ( $imageSmallResized );
        }
        return array($imageResizedPath , $imageSmallResizedPath);        
    }
}