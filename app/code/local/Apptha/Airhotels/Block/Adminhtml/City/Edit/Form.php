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
 * Form for add/edit cities
 */
class Apptha_Airhotels_Block_Adminhtml_City_Edit_Form extends Mage_Adminhtml_Block_Widget_Form {
 /**
  * Prepare form before rendering HTML
  *
  * @return Mage_Adminhtml_Block_Widget_Form
  */
 protected function _prepareForm() {
 /**
  * Set form action
  * Set method as post.
  */
  $form = new Varien_Data_Form ( array (
    'id' => 'edit_form',
    'action' => $this->getUrl ( '*/*/save', array (
      'id' => $this->getRequest ()->getParam ( 'id' )
    ) ),
    'method' => 'post',
    'enctype' => 'multipart/form-data'
  )
   );
  $form->setUseContainer ( true );
  /**
   * Set data to form
   */
  $this->setForm ( $form );
/**
 * Display city information field.
 */
  $fieldset = $form->addFieldset ( 'base_fieldset', array (
    'legend' => Mage::helper ( 'airhotels' )->__ ( 'City Information' )
  ) );
 /**
  * Display city name 
  */
  $fieldset->addField ( 'city', 'text', array (
    'label' => Mage::helper ( 'airhotels' )->__ ( 'City' ),
    'class' => 'required-entry',
    'required' => true,
    'name' => 'city'
  ) );
  /**
   * Display city description
   */
  $fieldset->addField ( 'city_description', 'textarea', array (
    'label' => Mage::helper ( 'airhotels' )->__ ( 'Description' ),
    'class' => 'required-entry',
    'required' => true,
    'name' => 'city_description'
  ) );
  /**
   * Assign Comment for image element
   */
  $note = Mage::helper ( 'airhotels' )->__ ( 'Suggested Image Resolution: 700x400' );
  /**
   * Set $requireFlag is one
   * @var unknown
   */
  $requireFlag = 1;
  if (Mage::registry ( 'city_data' )) {
  /**
   * Get city information.
   */
   $cityDataInfo = Mage::registry ( 'city_data' )->getData ();
   if (isset ( $cityDataInfo ['city_image'] )) {
    $note = '';
    /**
     * set $requireFlag is zero
     * 
     * @var unknown
     */
    $requireFlag = 0;
   }
  }
  if ($requireFlag == 1) {
  /**
   * Display city image.
   */
   $imageField = $fieldset->addField ( 'city_image', 'image', array (
     'label' => Mage::helper ( 'airhotels' )->__ ( 'City Image' ),
     'name' => 'city_image',
     'class' => 'required-entry required-file',
     'required' => true,
     'note' => $note
   ) )->setAfterElementHtml("<script type=\"text/javascript\">$('city_image').addClassName('required-entry');</script>");
  } else {
  /**
   * Display city image.
   */
   $imageField = $fieldset->addField ( 'city_image', 'image', array (
     'label' => Mage::helper ( 'airhotels' )->__ ( 'City Image' ),
     'name' => 'city_image',
     'class' => 'required-entry required-file',
     'note' => $note
   ) );
  }
  if (Mage::registry ( 'city_data' )) {
  /**
   * Set city information.
   */
   $cityData = Mage::registry ( 'city_data' )->getData ();
   /**
    * Displaying city image
    */
   if (isset ( $cityData ['city_image'] )) {
    $html = '';
    $imageUrl = $cityData ['city_image'];
    /**
     * Disaply note message.
     */
    $note = Mage::helper ( 'airhotels' )->__ ( 'Suggested Image Resolution: 700x400' );
    if ($imageUrl != '') {
     $html = Mage::helper ( 'airhotels/url' )->createImageBlockForVideoSection ( $imageUrl, $note, 183, 300 );
    }
    /**
     * Get video url for city section.
     */
    $cityData ['city_image'] = Mage::helper ( 'airhotels/url' )->getImageUrlForVideoSection ( $imageUrl );
    /**
     * Display image field after city image
     */
    $imageField->setAfterElementHtml ( $html );
   }
   /**
    * Set values in edit form.
    */
   $form->setValues ( $cityData );
  }
  /**
   * Calling the parent Construct Method.
   */
  return parent::_prepareForm ();
 }
}