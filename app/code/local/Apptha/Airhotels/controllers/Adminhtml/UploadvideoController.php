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
 * This class contains Uploadvideo gird actions
 */
class Apptha_Airhotels_Adminhtml_UploadvideoController extends Mage_Adminhtml_Controller_action {
    /**
     * Index Action
     */
    public function indexAction() {
        $this->loadLayout ()->_setActiveMenu ( 'airhotels' );
        /**
         * Set page title.
         */
        $this->getLayout ()->getBlock ( 'head' )->setTitle ( 'Upload Video' );
        $this->renderLayout ();
    }
    /**
     * New video action
     */
    public function newAction() {
        $this->_forward ( 'edit' );
    }
    
    /**
     * Edit video action
     */
    public function editAction() {
        $idVal = $this->getRequest ()->getParam ( 'id' );
        /**
         * Get video collection.
         */
        $modelColletion = Mage::getModel ( 'airhotels/uploadvideo' )->load ( $idVal );
        if ($modelColletion->getId () || $idVal == 0) {
            $dataValue = Mage::getSingleton ( 'adminhtml/session' )->getFormData ( true );
            if (! empty ( $dataValue )) {
                $modelColletion->setData ( $dataValue );
            }
            Mage::register ( 'video_data', $modelColletion );
            $this->loadLayout ();
            $this->_setActiveMenu ( 'airhotels' );
            /**
             * Check the value is empty
             */
            $titleAdd = 'Add Video';
            $editTitle = 'Edit Video'; 
            $titleChanges = ($idVal) ?  $editTitle : $titleAdd ;
            $this->getLayout ()->getBlock ( 'head' )->setTitle ( $titleChanges );
            /**
             * Add content to to the layout
             */
            $this->_addContent ( $this->getLayout ()->createBlock ( 'airhotels/adminhtml_uploadvideo_edit' ) );            
            $this->renderLayout ();
            /**
             * Rendered the layout.
             */
        } else {
            /**
             * Display error messages in session
             */
            Mage::getSingleton ( 'adminhtml/session' )->addError ( 'Video does not exist'  );
            $this->_redirect ( '*/*/' );
        }
    }
    
    /**
     * Get the server maximum upload size limit in Bytes
     *
     * @param
     *            Upload file size $size
     * @return file size
     */
    function parse_size($size) {
        /**
         * Remove the non-unit characters from the size.
         */
        $unit = preg_replace ( '/[^bkmgtpezy]/i', '', $size );
        /**
         * Remove the non-numeric characters from the size.
         */
        $size = preg_replace ( '/[^0-9\.]/', '', $size );
        if ($unit) {
            /**
             * Find the position of the unit in the ordered string which is the power of magnitude to multiply a kilobyte by.
             */
            return round ( $size * pow ( 1024, (stripos ( 'bkmgtpezy', $unit [0] )) ) );
        } else {
            return round ( $size );
        }
    }
    /**
     * Save video action
     */
    public function saveAction() {
        $returnValue = true;
        if ($data = $this->getRequest ()->getPost ()) {
            $id = $this->getRequest ()->getParam ( 'id' );
            $uploadsData = new Zend_File_Transfer_Adapter_Http ();
            $filesDataArray = $uploadsData->getFileInfo ();
            $server_maximum_upload_size = $this->parse_size ( ini_get ( 'upload_max_filesize' ) );            
            $videoDetails = Mage::getModel('airhotels/status')->redirectAction($server_maximum_upload_size,$filesDataArray,$id);           
            if (isset ( $filesDataArray ['image_url'] ['name'] ) && $filesDataArray ['image_url'] ['name'] != '') {
                $this->saveVideo ( $filesDataArray, $id );
            }
            /**
             * Load video collection by id
             */
            $videoInfo = Mage::getModel ( 'airhotels/uploadvideo' )->load ( $id );
            $videoInfo->getImageUrl ();
            $homeImageThumbUrl = $videoInfo->getThumbImage ();
            $model = Mage::getModel ( 'airhotels/uploadvideo' );
            /**
             * Update MP4 video url
             */
            $data ['video_url_mp4'] = $this->videoUrlMp4 ( $videoDetails[0], $data );
            /**
             * Update WEBM video url
             */
            $data ['video_url_webm'] = $this->videoUrlWebm ( $videoDetails[1], $data );
            $removeImageArray = array ();
            if (isset ( $data ['removeImage'] )) {
                foreach ( $data ['removeImage'] as $removeImage ) {
                    if (! in_array ( $data ['removeImage'], $imageUrlForNeighborhoodPost )) {
                        $removeImageArray [] = $removeImage;
                    }
                }
            }
            $imageUrlForNeighborhoodPost = array ();
            /**
             * $data ['imagepath'] all image path
             */
            if (isset ( $data ['imagepath'] )) {
                foreach ( $data ['imagepath'] as $imagePath ) {
                    if (! in_array ( $imagePath, $imageUrlForNeighborhoodPost ) && ! in_array ( $imagePath, $removeImageArray ) && ! empty ( $imagePath )) {
                        $imageUrlForNeighborhoodPost [] = $imagePath;
                    }
                }
            }
            $serializeImageData = serialize ( $imageUrlForNeighborhoodPost );
            $data ['image_url'] = $serializeImageData;
            /**
             * Set thumb image url.
             */
            if (! empty ( $imageResizedPath )) {
                $data ['thumb_image'] = $imageResizedPath;
            } else {
                $data ['thumb_image'] = $homeImageThumbUrl;
            }
            if (isset ( $data ['video_name'] )) {
                $data ['video_name'] = ucwords ( $data ['video_name'] );
            }
            /**
             * check whether already video name exist or not
             */
            if (isset ( $data ['video_name'] )) {
                $videoCollection = Mage::getModel ( 'airhotels/uploadvideo' )->getCollection ()->addFieldToFilter ( 'video_name', $data ['video_name'] )->getFirstItem ();
                $videoIdValue = $videoCollection->getId ();
                if (! empty ( $videoIdValue ) && $videoIdValue != $id) {
                    /**
                     * set error message.
                     */
                    Mage::getSingleton ( 'adminhtml/session' )->addError ( Mage::helper ( 'airhotels' )->__ ( 'This video name already exist.' ) );
                    $this->_redirect ( '*/*/edit', array (
                            'id' => $this->getRequest ()->getParam ( 'id' ) 
                    ) );
                    return $returnValue;
                }
            }
            /**
             * Set data into collection.
             */            
            $model->setData ( $data )->setId ( $id );
            try {
                $model->save ();                
                Mage::getSingleton ( 'adminhtml/session' )->addSuccess ( Mage::helper ( 'airhotels' )->__ ( 'Record has been saved successfully' ) );
                $this->_redirect ( '*/*/' );                
            } catch ( Exception $e ) {
                /**
                 * Set error message.
                 */
                Mage::getSingleton ( 'adminhtml/session' )->addError ( $e->getMessage () );
                Mage::getSingleton ( 'adminhtml/session' )->setFormData ( $data );
                $this->_redirect ( '*/*/edit', array ('id' => $this->getRequest ()->getParam ( 'id' )) );                
            }
            return $returnValue;
        }
        /**
         * Set error message.
         */        
        Mage::getSingleton ( 'adminhtml/session' )->addError ( 'Unable to find item to save' );
        $this->_redirect ( '*/*/' );
    }
    
    /**
     * Delete video
     */
    public function deleteAction() {
        if ($this->getRequest ()->getParam ( 'id' ) > 0) {
            try {
                /**
                 * Get video collection.
                 */
                $videoData = Mage::getModel ( 'airhotels/uploadvideo' );
                $videoData->setId ( $this->getRequest ()->getParam ( 'id' ) );
                $videoData->delete ();
                /**
                 * Set success message.
                 */
                Mage::getSingleton ( 'adminhtml/session' )->addSuccess ( 'Video has been removed successfully.' );
                $this->_redirect ( '*/*/' );
            } catch ( Exception $e ) {
                /**
                 * Set error message.
                 */
                Mage::getSingleton ( 'adminhtml/session' )->addError ( $e->getMessage () );
                $this->_redirect ( '*/*/edit', array ('id' => $this->getRequest ()->getParam ( 'id' )) );
            }
        }
        /**
         * Set redirect url
         */
        $this->_redirect ( '*/*/' );
    }        
    /**
     * Save Video
     *
     * @param Array $filesDataArray            
     * @param Int $id            
     */
    public function saveVideo($filesDataArray, $id) {
        try {
            /**
             * Storing image for ie 8 browser
             */
            $imageId = $id;
            if (empty ( $imageId )) {
                $collectionForCount = Mage::getModel ( 'airhotels/uploadvideo' )->getCollection ()->setOrder ( 'id', 'DESC' )->getFirstItem ();
                $imageId = $collectionForCount->getId () + 1;
            }
            /**
             * Upload image instead of video.
             */
            $path = Mage::getBaseDir ( 'media' ) . DS . 'airhotels' . DS . 'video' . DS . 'image' . DS;
            $cityImageName = 'image_url';
            $imagesPath = Mage::helper ( 'airhotels/invitefriends' )->uploadImageForVideoImage ( $filesDataArray, $cityImageName, $path, $imageId );
            
            /**
             * Resizing neighborhood city image
             */
            if (! empty ( $imagesPath )) {
                $imageName = '';
                /**
                 * Remove '//' and add DIRECTORY_SEPARATOR for image path
                 */
                $imagesPathArray = explode ( DIRECTORY_SEPARATOR, $imagesPath );
                $imagesPathArrayCount = count ( $imagesPathArray );
                if (isset ( $imagesPathArray [$imagesPathArrayCount - 1] )) {
                    $imageName = $imagesPathArray [$imagesPathArrayCount - 1];
                }
                /**
                 * Resize image in 315 X 180 dimension.
                 */
                $imageResized = Mage::getBaseDir ( 'media' ) . DS . 'airhotels' . DS . 'video' . DS . 'image' . DS . 'thumb' . DS . $imageName;
                if (! empty ( $imageResized ) && file_exists ( $imagesPath )) {
                    $imageObj = new Varien_Image ( $imagesPath );
                    $imageObj->constrainOnly ( TRUE );
                    $imageObj->keepAspectRatio ( TRUE );
                    $imageObj->keepFrame ( FALSE );
                    $imageObj->resize ( 315, 180 );
                    $imageObj->save ( $imageResized );
                }
            }
        } catch ( Exception $e ) {
            /**
             * Display error message.
             */
            Mage::getSingleton ( 'adminhtml/session' )->addError ( $e->getMessage () );
            $this->_redirect ( '*/*/edit', array (
                    'id' => $this->getRequest ()->getParam ( 'id' ) 
            ) );
            return;
        }
    }
    /**
     * Setting the Video path mp4
     *
     * @param String $videoPathMP4            
     * @return String
     */
    public function videoUrlMp4($videoPathMP4, $data) {
        if (! empty ( $videoPathMP4 )) {
            $videoUrlMP4 = explode ( "media" . DIRECTORY_SEPARATOR, $videoPathMP4 );
            if (isset ( $videoUrlMP4 [1] )) {
                $data ['video_url_mp4'] = $videoUrlMP4 [1];
            } else {
                $data ['video_url_mp4'] = $homeVideoUrlMP4;
            }
        } else {
            $data ['video_url_mp4'] = $homeVideoUrlMP4;
        }
        return $data ['video_url_mp4'];
    }
    
    /**
     * Save the video url for webm files
     *
     * @param String $videoPathWEBM            
     * @return String
     */
    public function videoUrlWebm($videoPathWEBM, $data) {
        if (! empty ( $videoPathWEBM )) {
            $videoUrlWEBM = explode ( "media" . DIRECTORY_SEPARATOR, $videoPathWEBM );
            if (isset ( $videoUrlWEBM [1] )) {
                $data ['video_url_webm'] = $videoUrlWEBM [1];
            } else {
                $data ['video_url_webm'] = $homeVideoUrlWEBM;
            }
        } else {
            $data ['video_url_webm'] = $homeVideoUrlWEBM;
        }
        return $data ['video_url_webm'];
    }
    /**
     * Delete multiple video action
     */
    public function massDeleteAction() {
        $videoIds = $this->getRequest ()->getParam ( 'videos' );
        if (! is_array ( $videoIds )) {
            /**
             * Set error message.
             */
            Mage::getSingleton ( 'adminhtml/session' )->addError ( Mage::helper ( 'adminhtml' )->__ ( 'Please select item(s)' ) );
        } else {
            try {
                /**
                 * Delete uploaded video's.
                 */
                foreach ( $videoIds as $id ) {
                    $model = Mage::getModel ( 'airhotels/uploadvideo' );
                    $model->setId ( $id )->delete ();
                }
                /**
                 * Set success message
                 */
                Mage::getSingleton ( 'adminhtml/session' )->addSuccess ( Mage::helper ( 'adminhtml' )->__ ( 'Total of %d record(s) were deleted.', count ( $videoIds ) ) );
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
     * Save post image
     */
    public function uploadAction() {
        /**
         * $filesDataArray remove image urls
         */
        $uploadsData = new Zend_File_Transfer_Adapter_Http ();
        $filesDataArray = $uploadsData->getFileInfo ();
        /**
         * Storing images for uploadvideo
         */
        if (isset ( $filesDataArray ['image'] ['name'] ) && $filesDataArray ['image'] ['name'] != '') {
            $imageName = $filesDataArray ['image'] ['name'];
            $pathForSave = Mage::getBaseDir ( 'media' ) . DS . 'airhotels' . DS . 'video' . DS . 'image' . DS;
            $uploader = new Varien_File_Uploader ( 'image' );
            /**
             * Set allowed extensions.
             */
            $uploader->setAllowedExtensions ( array (
                    'jpg',
                    'jpeg',
                    'gif',
                    'png' 
            ) );
            $uploader->addValidateCallback ( 'catalog_product_image', Mage::helper ( 'catalog/image' ), 'validateUploadFile' );
            
            /**
             * If file name already exist, rename the uploading file
             * $imageUrl
             * setAllowRenameFiles
             */
            $uploader->setAllowRenameFiles ( true );
            $uploader->setFilesDispersion ( false );
            $uploader->save ( $pathForSave, $imageName );
            $imageUrl = $pathForSave . $uploader->getUploadedFileName ();
            $imagePath = '';
            /**
             * Initilizing remove image urls
             * $imageUrl
             */
            if (isset ( $imageUrl )) {
                $imagePathArray = explode ( "media", $imageUrl );
                $imageUrlArrayCount = count ( $imagePathArray );
                /**
                 * Initilizing remove image urls
                 * $imagePath
                 */
                if (isset ( $imagePathArray [$imageUrlArrayCount - 1] )) {
                    $imagePath = substr ( $imagePathArray [$imageUrlArrayCount - 1], 1 );
                }
            }
            /**
             * Initilizing $imagesUrlForVideoPost image urls
             * $imagesUrlForVideoPost
             * $imagesUrlForVideoPostCount
             */
            $imagesUrlForVideoPost = Mage::getSingleton ( 'core/session' )->getImagesUrlForVideoPost ();
            $imagesUrlForVideoPost [] = $imagePath;
            Mage::getSingleton ( 'core/session' )->setImagesUrlForVideoPost ( $imagesUrlForVideoPost );
            $imagesUrlForVideoPostCount = count ( $imagesUrlForVideoPost );
            $this->getResponse ()->setBody ( $imagesUrlForVideoPostCount - 1 );
        }
    }
    /**
     * Getting neighborhoods post image row
     */
    public function currentImageUrlAction() {
        $sessionImageId = ( int ) Mage::app ()->getRequest ()->getParam ( 'session_image_id' );
        $imagesUrlForVideoPost = Mage::getSingleton ( 'core/session' )->getImagesUrlForVideoPost ();
        /**
         * Initilizing neighborhoods image urls
         */
        if (isset ( $imagesUrlForVideoPost [$sessionImageId] )) {
            $postData = array ();
            $html = Mage::helper ( 'airhotels/url' )->imageRowActionForVideoPost ( $imagesUrlForVideoPost [$sessionImageId], $postData );
            $this->getResponse ()->setBody ( $html );
        }
    }
    
    /**
     * Setting for acl
     */
    protected function _isAllowed() {
        return true;
    }
}