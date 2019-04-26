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
class Apptha_Airhotels_Helper_Url extends Mage_Core_Helper_Abstract {
 /**
  * Function Name: getCategoriesValue
  * Retrieve property categories
  *
  * @return integer
  */
 public function getCategoriesValue() {
  /**
   * Initialise the 'propertyCategoryValue' array
   */
  $propertyCategoryValue = array ();
  /**
   * Returning the resource Model for 'propertytype'
   */
  $attribute = Mage::getModel ( 'eav/config' )->getAttribute ( 'catalog_product', 'propertytype' );
  /**
   * Iterating the loop
   */
  foreach ( $attribute->getSource ()->getAllOptions ( true ) as $option ) {
   $value = '';
   $value = $option ['value'];
   /**
    * Assign the value to '$propertyCategoryValue' array.
    */
   $propertyCategoryValue [$value] = $option ['label'];
  }
  /**
   * Returning the '$propertyCategoryValue'
   */
  return $propertyCategoryValue;
 }
 /**
  * Function Name: getRDAdapter
  * Retrieve core read adapter
  *
  * @return array
  */
 public function getRDAdapter() {
  /**
   * Returning the database 'core_read'
   */
  return Mage::getSingleton ( 'core/resource' )->getConnection ( 'core_read' );
 }
 
 /**
  * Function Name: getWRAdapter
  * Retrieve core write adapter
  *
  * @return array
  */
 public function getWRAdapter() {
  /**
   * Returning the database 'core_write'
   */
  return Mage::getSingleton ( 'core/resource' )->getConnection ( 'core_write' );
 }
 /**
  * Get random video collection to play in home page
  */
 public function getVideoCollection() {
 /**
  * Get video collection filter by status.
  */
  $videoEnabled = Mage::getModel ( 'airhotels/uploadvideo' )->getCollection ()->addFieldToFilter ( 'status', 1 );
  $videoEnabled->getSelect ()->order ( 'RAND()' );
  $videoEnabled->getSelect ()->limit ( 1 );
  return $videoEnabled;
 }
 
 /**
  * Get video url for home page section
  *
  * @param String $videoUrl         
  * @return String $videoUrl
  */
 public function getVideoUrlSection($videoUrl) {
  if ($videoUrl != '') {
   $videoPath = explode ( "media\\", $videoUrl );
  } else {
   $videoPath = array ();
  }
  if (isset ( $videoPath [1] )) {
   $videoUrl = $videoPath [1];
  }
  /**
   * Return video url.
   */
  return $videoUrl;
 }
 
 /**
  * Create video block for home page section
  *
  * @param string $videoUrl         
  * @param string $note         
  * @return string $value
  */
 public function createVideoBlock($videoUrl, $height, $width) {
  $mediaUrl = Mage::getBaseUrl ( Mage_Core_Model_Store::URL_TYPE_MEDIA ) . $videoUrl;
  return '<video width="' . $width . '" height="' . $height . '" autoplay controls>
 <source src="' . $mediaUrl . '" type="video/mp4">
 <source src="' . $mediaUrl . '" type="video/wmv">
 Your browser does not support the video tag.
 </video>';  
 }
 /**
  * Create image block for city section
  *
  * @param string $imageUrl         
  * @param string $note         
  * @return string $value
  */
 public function createImageBlockForVideoSection($imageUrl, $note, $height, $width) {
  $mediaUrl = Mage::getBaseUrl ( Mage_Core_Model_Store::URL_TYPE_MEDIA ) . $imageUrl;
  return "<span>$note</span><div><img src='$mediaUrl' height='$height' width='$width' style='margin-top:10px;' /></div>";  
 }
 
 /**
  * Create image table row
  *
  * @param string $imageValue
  *         Image url
  * @return string $imageTableHtml image row
  */
 public function imageRowActionForVideoPost($allImagePath, $postData) {
  $allImagePath = trim ( $allImagePath );
  $imageTableHtml = '';
  $imageHtmlValue = $this->createImageBlockForVideoSection ( $allImagePath, '', 60, 110 );
  /**
   * Image flag status for neighborhood post
   */
  $imageStatus = Mage::getSingleton ( 'core/session' )->getImageValidationFlagStatusForVideoPost ();
  $statusClass = '';
  /**
   * Check image status.
   */
  if ($imageStatus == 0) {
   $statusClass = 'class="validate-one-required-by-name"';
  }
  $imageTableHtml = $imageTableHtml . '<tr>';
  $imageTableHtml = $imageTableHtml . "<td><input type='hidden'name='image' id='image' value=''>$imageHtmlValue</td>";
  $imageTableHtml = $imageTableHtml . "<td class='a-center'><input type='checkbox' value='$allImagePath' name='removeImage[]'><input type='hidden' value='$allImagePath' name='imagepath[]'></td>";
  $imageTableHtml = $imageTableHtml . '</tr>';
  /**
   * Image flag status for neighborhood post
   */
  Mage::getSingleton ( 'core/session' )->setImageValidationFlagStatusForVideoPost ( 1 );
  return $imageTableHtml;
 }
 
 /**
  * Getting image uploader for Video
  *
  * @return string $html html content
  */
 public function getImageUploaderForVideo() {
  $uploader = new Mage_Adminhtml_Block_Cms_Wysiwyg_Images_Content_Uploader ();
  $htmlId = "imageuploader";
  $currentImageUpdateUrl = Mage::getModel ( 'adminhtml/url' )->addSessionParam ()->getUrl ( "airhotels/adminhtml_uploadvideo/currentImageUrl" );
  $onclickForRemove = "onclick='" . $htmlId . "UploaderJsObject.removeFile({{fileId}})" . "'";
  $html = '';
  $html = $html . "<div id='$htmlId'><div>";
  $html = $html . "<div id='$htmlId-install-flash' style='display:none'>";
  $html = $html . Mage::helper ( 'airhotels' )->__ ( 'This content requires last version of Adobe Flash Player. <a href="%s">Get Flash</a>', 'http://www.adobe.com/go/getflash/' );
  $html = $html . "</div><div></div>";
  $html = $html . "<div id='$htmlId-template' class='no-display' >";
  $html = $html . "<div id='{{id}}'><div></div><span>{{name}} ({{size}})</span>";
  $html = $html . "<span class='delete-button'><button id='{{id}}-delete' title='Remove' type='button' class='scalable delete' $onclickForRemove><span><span><span>Remove</span></span></span></button></span>";
  $html = $html . "<span class='progress-text'></span>";
  $html = $html . "<div class='progress-text'></div></div></div>";
  $html = $html . "<div id='$htmlId-template-progress' class='no-display'>{{percent}}% {{uploaded}} / {{total}}</div></div>";
  
  $html = $html . "<script type='text/javascript'>";
  $html = $html . "var uploader = new Flex.Uploader('$htmlId', '" . $uploader->getSkinUrl ( 'media/uploader.swf' ) . "'," . $this->getConfigJson () . ")" . ';';
  $html = $html . "uploader.onFilesComplete = function(completedFiles)
    {completedFiles.each(function(file){
    var currentUrl = '$currentImageUpdateUrl'+'&session_image_id='+file.response;
    new Ajax.Request(currentUrl, {
    method:'get',
    onSuccess: function(transport) {
    var response = transport.responseText;
    if($('post_image_table_body')){
    $('post_image_table_body').insert ({'bottom'  : response } );
    }
    }
    });
     
    uploader.removeFile(file.id);});
    MediabrowserInstance.handleUploadComplete();}
    if ($('$htmlId-flash') != undefined)
    {
     $('$htmlId-flash').setStyle({float: 'right',clear: 'both',padding: '1% 0%'});
    }";
  return $html . "</script>";
 }
 /**
  * Upload webm video for home page
  *
  * @param File $filesDataArray         
  * @param String $name         
  * @param String $path         
  * @return String $imagesPath
  */
 public function uploadVideoWebm($filesDataArray, $name, $path, $id) {
  $videoPath = '';
  if (isset ( $filesDataArray [$name] ['name'] ) && $filesDataArray [$name] ['name'] != '') {
   /**
    * File path to store the video
    */
   $homePageVideoName = $path . $filesDataArray [$name] ['name'];
   $splitExtension = explode ( ".", $homePageVideoName );
   $arrayCount = count ( $splitExtension );
   if (isset ( $splitExtension [$arrayCount - 1] )) {
    $videoNameForSave = $id . '.' . $splitExtension [$arrayCount - 1];
   } else {
    $videoNameForSave = $homePageVideoName;
   }
   /**
    * Define uploader object.
    * @var $uploader
    */
   $uploader = new Varien_File_Uploader ( $name );
   /**
    * Set allowed extention.
    */
   $uploader->setAllowedExtensions ( array ('webm') );
   /**
    * checking file extension.
    */
   $uploader->setAllowRenameFiles ( false );
   $uploader->setFilesDispersion ( false );
   /**
    * Save video name.
    */
   $uploader->save ( $path, $videoNameForSave );
   $videoPath = $path . $uploader->getUploadedFileName ();
  }
  /**
   * Return video path.
   */
  return $videoPath;
 }
 /**
  * Upload mp4 video for home page
  *
  * @param File $filesDataArray         
  * @param String $name         
  * @param String $path         
  * @return String $imagesPath
  */
 public function uploadVideoMp($filesDataArray, $name, $path, $id) {
  $videoPath = '';
  if (isset ( $filesDataArray [$name] ['name'] ) && $filesDataArray [$name] ['name'] != '') {
   /**
    * File path to store the video
    */
   $homePageVideoName = $path . $filesDataArray [$name] ['name'];
   $splitExtension = explode ( ".", $homePageVideoName );
   $arrayCount = count ( $splitExtension );
   if (isset ( $splitExtension [$arrayCount - 1] )) {
    $videoNameForSave = $id . '.' . $splitExtension [$arrayCount - 1];
   } else {
    $videoNameForSave = $homePageVideoName;
   }
   /**
    * checking file extension
    */
   $uploader = new Varien_File_Uploader ( $name );
   /**
    * Set allowed extenstion.
    */
   $uploader->setAllowedExtensions ( array ( 'mp4' ) );
   $uploader->setAllowRenameFiles ( false );
   $uploader->setFilesDispersion ( false );
   /**
    * Save video name.
    */
   $uploader->save ( $path, $videoNameForSave );
   $videoPath = $path . $uploader->getUploadedFileName ();
  }
  /**
   * Return video path.
   */
  return $videoPath;
 }
 /**
  * Getting image uploader configuration
  *
  * @return array configuration data
  */
 public function getConfigJson() {
  $uploader = new Mage_Adminhtml_Block_Cms_Wysiwyg_Images_Content_Uploader ();
  $uploader->getConfig ()->setParams ( array (
    'form_key' => $uploader->getFormKey (),
    "field" => '' 
  ) );
  $uploader->getConfig ()->setFileField ( 'image' );
  $uploader->getConfig ()->setUrl ( Mage::getModel ( 'adminhtml/url' )->addSessionParam ()->getUrl ( "airhotels/adminhtml_uploadvideo/upload" ) );
  /**
   * Set filer an image based on extention.
   */
  $uploader->getConfig ()->setFilters ( array (
    'images' => array (
      'label' => Mage::helper ( 'airhotels' )->__ ( 'Images (.gif, .jpg, .png)' ),
      'files' => array ('*.gif','*.jpg','*.png'  ) )
    ) );
  /**
   * Return value in json endoded format.
   */
  return Mage::helper ( 'core' )->jsonEncode ( $uploader->getConfig ()->getData () );
 }
 /**
  * Retrieve attribute id for languages
  *
  * Get geo codes
  *
  * @param unknown $propertyAddress         
  * @return multitype:NULL unknown
  */
  public function getGeocodeDatas($propertyAddress) {
    $arrayAddress = array ();
    $addrsRemoveSpace = str_replace ( ' ', ',', $propertyAddress );
    $addressAddPlus = str_replace ( ',', '+', $addrsRemoveSpace );  
    $encodeAddress = urlencode($addressAddPlus);  
   $config = Mage::getStoreConfig ( 'airhotels/custom_group' );
              $googleApiKey = $config['airhotels_googlemapapi'];
    /**
     * Google map API cal.*/
  
                  $geocode = file_get_contents ( 'https://maps.google.com/maps/api/geocode/json?address=' . rtrim ( $encodeAddress ) . '&sensor=false&key='.$googleApiKey);
   $jsondata = json_decode ( $geocode, true );
  
  /**
   * street
   */
  foreach ( $jsondata ["results"] as $result ) {
   foreach ( $result ["address_components"] as $address ) {      
    if (in_array ( "administrative_area_level_1", $address ["types"] )) {
     $arrayAddress ['state'] = $address ["long_name"];
    }
   }
  }
  /**
   * city
   */
  foreach ( $jsondata ["results"] as $result ) {
   foreach ( $result ["address_components"] as $address ) {
    if (in_array ( "locality", $address ["types"] )) {
     $arrayAddress ['city'] = $address ["long_name"];
    }
   }
  }
  /**
   * country
   */
  foreach ( $jsondata ["results"] as $result ) {
   foreach ( $result ["address_components"] as $address ) {
    if (in_array ( "country", $address ["types"] )) {
     $arrayAddress ['country'] = $address ["long_name"];
    }
   }
  }
  /**
   * country
   */
  foreach ( $jsondata ["results"] as $result ) {      
      foreach ( $result ["geometry"] as $address ) {            
              $arrayAddress ['northeastlat'] = $address ["northeast"]["lat"];
              $arrayAddress ['northeastlng'] = $address ["northeast"]["lng"];
              $arrayAddress ['southwestlat'] = $address ["southwest"]["lat"];
              $arrayAddress ['southwestlng'] = $address ["southwest"]["lng"];
      }
  }  
  /**
   * Return an address array.
   */
  return $arrayAddress;
 }
 /**
  * Retrieve attribute id by property time label(minutes)
  *
  * @return integer
  */
 public function getPropertyTimeLabelByOptionIdMin() {
  $propertyTimeId = Mage::helper ( 'airhotels/airhotel' )->getPropertyTime ();
  $propertyTimeValue = '';
  $propertyAttribute = Mage::getModel ( 'eav/config' )->getAttribute ( 'catalog_product', $propertyTimeId );
  /**
   * Get property time label.
   * Get property time value.
   */
  foreach ( $propertyAttribute->getSource ()->getAllOptions () as $propertyTimeOption ) {
   $propertyTimeLabel = $propertyTimeOption ['label'];
   $propertyTimeValue = $propertyTimeOption ['value'];
   if (! empty ( $propertyTimeLabel ) && $propertyTimeLabel == 'Minutes') {
    return $propertyTimeValue;
   }
  }
  /**
   * Return property time.
   */
  return $propertyTimeValue;
 }
 
 /**
  * Function to upload profile video
  *
  * @param string $name         
  * @param array $filesDataArray         
  * @return string
  */
 public function uploadProfileVideo($name, $filesDataArray) {
  /**
   * checking file extension
   */
  $path = Mage::getBaseDir ( 'media' ) . DS . 'catalog' . DS . 'customer' . DS;
  $uploader = new Varien_File_Uploader ( $name );
  /**
   * Set allowed extentions.
   */
  $uploader->setAllowedExtensions ( array ('mp4','avi','3gp','mov','webm','flv','mpeg4','mpegps','wmv'  ) );
  $uploader->setAllowRenameFiles ( false );
  $uploader->setFilesDispersion ( false );
  /**
   * Save upload video path.
   */
  $uploader->save ( $path, $filesDataArray [$name] ['name'] );
  return Mage::getBaseUrl ( 'media' ) . 'catalog' . DS . 'customer' . DS . $uploader->getUploadedFileName ();  
 }
 
 /**
  * Retrieve Dashboard Url
  *
  * @return string
  */
 public function getDashboardUrl() {
  return $this->_getUrl ( 'customer/account/' );
 }
 
 /**
  * Retrieve Profile Url
  *
  * @return string
  */
 public function getProfileUrl() {
  $customer = Mage::getSingleton ( 'customer/session' )->getCustomer ();
  return $this->_getUrl ( 'airhotels/index/profile/id/' . $customer->getId () );
 }
 /**
  * Get image url for city section
  *
  * @param String $imageUrl         
  * @return String $imageUrl
  */
 public function getImageUrlForVideoSection($imageUrl) {
  if ($imageUrl != '') {
   $imagePath = explode ( "media\\", $imageUrl );
  } else {
  /**
   * Set image path as empty
   */
   $imagePath = array ();
  }
  if (isset ( $imagePath [1] )) {
   $imageUrl = $imagePath [1];
  }
  /**
   * Return image url.
   */
  return $imageUrl;
 }
 /**
  * Function Name: retrievePropertyTimesData
  * Retrieving the Property times data.
  *
  * @param int $blockingTimesValue         
  * @param int $startblockingTimes         
  * @param int $propertyTimesData         
  * @return string
  */
 public function retrievePropertyTimesData($blockingTimesValue, $startblockingTimes, $propertyTimesData) {
  if ($blockingTimesValue == $startblockingTimes) {
   $propertyTimesData = $propertyTimesData . ',' . $blockingTimesValue;
  }
  return $propertyTimesData;
 }
 /**
  * Retrive ProductOptions
  *
  * @return array
  */
 public function getSecurityDepositFee($orderId) {
  /**
   * load order by id
   */
  $order = Mage::getModel ( 'sales/order' )->load ( $orderId );
  $opts = array ();
  foreach ( $order->getAllItems () as $item ) {
   $opts [] = $item->getProductOptions ();
  }
  /**
   * return secuirty fee options
   */
  return $opts;
 }
}