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
 * Form for upload video
 */
class Apptha_Airhotels_Block_Adminhtml_Uploadvideo_Edit_Form extends Mage_Adminhtml_Block_Widget_Form {
 /**
  * Prepare form before rendering HTML
  *
  * @return Mage_Adminhtml_Block_Widget_Form
  *
  */
 protected function _prepareForm() {
  $form = new Varien_Data_Form ( array ('id' => 'edit_form', 'name' => 'edit_form','action' => $this->getUrl ( '*/*/save', array ('id' => $this->getRequest ()->getParam ( 'id' )) ), 'method' => 'post','enctype' => 'multipart/form-data' ) );
  $form->setUseContainer ( true );
  $this->setForm ( $form );
  $fieldset = $form->addFieldset ( 'base_fieldset', array ('legend' => Mage::helper ( 'airhotels' )->__ ( 'Post Information' ) ) );
  $fieldset->addField ( 'video_name', 'text', array ('label' => Mage::helper ( 'airhotels' )->__ ( 'Video Name' ),'class' => 'required-entry','required' => true,'name' => 'video_name') );
  $requireFlag = 1;
  if (Mage::registry ( 'video_data' )) {
   $videoDataInfo = Mage::registry ( 'video_data' )->getData ();
   if (isset ( $videoDataInfo ['video_url_mp4'] )) {
    $requireFlag = 0;
   }
  }
  $videoField = $this->VideoTypeMp4Field ( $requireFlag, $fieldset, $videoDataInfo );
  $this->VideoTypeMp4 ( $form, $videoField );
  $requireFlag = 1;
  /**
   * Get the required flag value
   */  
  $videoField = $this->VideoTypeWebmField ($fieldset );
  $this->VideoTypeWebm ( $form, $videoField );
  /**
   * Display image field.
   */
  $imageField = $fieldset->addField ( 'image_url', 'file', array (
    'name' => 'image_url[]',
    'style' => "display:none;" 
  ) );
  if (Mage::registry ( 'video_data' )) {
   $postData = Mage::registry ( 'video_data' )->getData ();
  }
  $htmlContent = '<span class="required">*</span>';
  $form->setValues ( $postData );
  /**
   * Display image uploader label.
   */
  $imageUploaderLabel = Mage::helper ( 'airhotels' )->__ ( 'Upload Image For IE8 & Mobile Devices' ) . $htmlContent;
  $imagePreview = Mage::helper ( 'airhotels' )->__ ( 'Image' );
  $imageRemove = Mage::helper ( 'airhotels' )->__ ( 'Remove' );
  $imageTableHtml = "<span class='image-resolution' style='float:left;'>Suggested Image Resolution : 1400x500</span><div class='grid' style='float:left;clear:both;margin-top:20px;'><table cellspacing='0' id='post_image'>
<tr class='headings'>
<th>$imagePreview</th>
<th>$imageRemove</th>
</tr>";
  $allImagePath = '';
  $allImagePathArray = array ();
  if (Mage::registry ( 'video_data' ) && isset ( $postData ['image_url'] )) {
   $allImagePath = $postData ['image_url'];
  }
  $allImagePathArray = unserialize ( $allImagePath );
  Mage::getSingleton ( 'core/session' )->setImageValidationFlagStatusForVideoPost ( 0 );
  $imageTableHtml = $imageTableHtml . '<tbody id="post_image_table_body">';
  /**
   * get the image html table value
   */
  $imageTableHtml = $this->getImageTableHtml ( $allImagePath, $allImagePathArray, $imageTableHtml, $postData );
  $imageTableHtml = $imageTableHtml . '</tbody>';
  $imageTableHtml = $imageTableHtml . '</table><style type="text/css">#imageuploader .progress, #imageuploader .complete{clear: both;padding: 10px;margin: 0 0 12px;border: 1px solid #90c898;background-color: #e5ffed;}</style>';
  $imagesUrlForVideoPost = array ();
  /**
   * Store image url for video post in sessions.
   */
  Mage::getSingleton ( 'core/session' )->setImagesUrlForVideoPost ( $imagesUrlForVideoPost );
  $imageField->setAfterElementHtml ( "<tr><td class='label'>$imageUploaderLabel</td><td class='value' id='image_uploader_for_post'>$imageTableHtml" . Mage::helper ( 'airhotels/url' )->getImageUploaderForVideo () . "<div class='validation-advice' id='image-validation' style='display:none;'></div></div></td></tr>" );
  if (Mage::registry ( 'video_data' )) {
   $postData = Mage::registry ( 'video_data' )->getData ();
  }
  $form->setValues ( $postData );
  $statusData = Mage::registry ( 'video_data' )->getData ();
  $fieldset->addField ( 'status', 'select', array (
    'label' => Mage::helper ( 'airhotels' )->__ ( 'Status' ),
    'class' => 'required-entry',
    'required' => true,
    'name' => 'status',
    'values' => Mage::getModel ( 'airhotels/videostatus' )->getOptionArray ( true ) 
  ) );
  if (Mage::registry ( 'video_data' )) {
   $statusData = Mage::registry ( 'video_data' )->getData ();
   if (isset ( $statusData ['status'] )) {
    $status = $statusData ['status'];
    $statusData ['status'] = $status;
   }
   $form->setValues ( $statusData );
  }
  $this->setForm ( $form );
  ?>
<script type="text/javascript">        
      function editformsubmit(){
      Validation.validate($('video_name'));
      Validation.validate($('video_url_mp4'));        
      Validation.validate($('status'));          
      $('image-validation').hide();
      document.getElementById('image-validation').innerHTML = "";     
          if(document.getElementById("image")){
          editForm.submit();              
          }else{
         respondToClick();
             function respondToClick(event) {               
                 $('image-validation').show();      
                 $('image-validation').insert('Please select at least one image');
              }
          }        
        }
      </script>
<?php
/**
 * Calling the parent Construct Method.
 */
  return parent::_prepareForm ();
 }
 
 /**
  * Video type mp4 for saving
  * Create video block
  * 
  * Set video data to form
  */
 public function VideoTypeMp4($form, $videoField) {
  if (Mage::registry ( 'video_data' )) {
   $video_data = Mage::registry ( 'video_data' )->getData ();
   if (isset ( $video_data ['video_url_mp4'] )) {
    $htmlVal = '';
    $video_url = $video_data ['video_url_mp4'];
    $htmlVal = Mage::helper ( 'airhotels/url' )->createVideoBlock ( $video_url, 240, 300 );
    $video_data ['video_url_mp4'] = Mage::helper ( 'airhotels/url' )->getVideoUrlSection ( $video_url );
    /**
     * Append video element after html
     */
    $videoField->setAfterElementHtml ( $htmlVal );
   }
   /**
    * Set video data.
    */
   $form->setValues ( $video_data );
  }
 }
 
 /**
  * Video type webm for saving
  * 
  * Set form values in $videoData
  * Set video_url_webm
  */
 public function VideoTypeWebm($form, $videoField) {
 /**
  * Store video data.
  */
  if (Mage::registry ( 'video_data' )) {
   $videoData = Mage::registry ( 'video_data' )->getData ();
   if (isset ( $videoData ['video_url_webm'] )) {
    $html = '';
    $videoUrl = $videoData ['video_url_webm'];
    $html = Mage::helper ( 'airhotels/url' )->createVideoBlock ( $videoUrl, 240, 300 );
    $videoData ['video_url_webm'] = Mage::helper ( 'airhotels/url' )->getVideoUrlSection ( $videoUrl );
    /**
     * Append video element after html
     */
    $videoField->setAfterElementHtml ( $html );
   }
   $form->setValues ( $videoData );
  }
 }
 
 /**
  * Function Name: VideoTypeMp4Field
  * Video type for mp4 type
  * 
  * @var $requireFlag 
  * @var $fieldset 
  * @var $videoDataInfo 
  * 
  */
 public function VideoTypeMp4Field($requireFlag, $fieldset, $videoDataInfo) {
  if ($requireFlag == 1) {
   $videoField = $fieldset->addField ( 'video_url_mp4', 'file', array (
     'label' => Mage::helper ( 'airhotels' )->__ ( 'Upload Video(MP4)' ),
     'name' => 'video_url_mp4',
     'required' => true,
     'class' => 'required-entry required-file' 
   ) );
  } else {
   $class = '';
   if (empty ( $videoDataInfo ['video_url_mp4'] )) {
   /**
    * Set required class.
    * 
    * @var $class
    */
    $class = $class . 'required-entry';
   }
   /**
    * Upload button
    */
 $videoField = $fieldset->addField ( 'video_url_mp4', 'file', array (
     'label' => Mage::helper ( 'airhotels' )->__ ( 'Upload Video(MP4)' ),
     'name' => 'video_url_mp4',
     'value' => 'video_url_mp4'   
   ) );
  }
  /**
   * Return video fields.
   */
  return $videoField;
 }
 
 /**
  * Function Name: VideoTypeWebmField
  * Video type for webm
  *
  * @param int $requireFlag         
  * @param object $fieldset         
  */
 public function VideoTypeWebmField($fieldset) {  
     /**
      * Upload video field (WEBM)
      */
   return $fieldset->addField ( 'video_url_webm', 'file', array (
     'label' => Mage::helper ( 'airhotels' )->__ ( 'Upload Video(WEBM)' ),
     'name' => 'video_url_webm' 
   ) );   
 }
 
 /**
  * Get Image table html Form
  * 
  * @var $allImagePath
  * @var $allImagePathArray
  * @var $imageTableHtml
  * @var $postData
  */
 public function getImageTableHtml($allImagePath, $allImagePathArray, $imageTableHtml, $postData) {
  /**
   * check condition $allImagePath is 
   * empty/not empty
   */
  if (! empty ( $allImagePath )) {
   foreach ( $allImagePathArray as $imageValue ) {
    if (! empty ( $imageValue )) {
     $imageTableHtml = $imageTableHtml . Mage::helper ( 'airhotels/url' )->imageRowActionForVideoPost ( $imageValue, $postData );
    }
   }
  }
  /**
   * Return image table html.
   */
  return $imageTableHtml;
 }
 
 /**
  * Function name: flagValue
  * Get the flag Vlaue
  *
  * @param array $videoDataInfo         
  * @return number
  */
 public function flagValue($videoDataInfo) {
     /**
      * Set flag values for video data
      */
  if (Mage::registry ( 'video_data' )) {
   $videoDataInfo = Mage::registry ( 'video_data' )->getData ();
   if (isset ( $videoDataInfo ['video_url_webm'] )) {
    $requireFlag = 0;
   }
  }
  /**
   * Return required flag.
   */
  return $requireFlag;
 }
}