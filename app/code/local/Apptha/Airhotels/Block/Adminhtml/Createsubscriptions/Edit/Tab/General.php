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
 * Createsubscriptions_Edit General Form Block
 * 
 * @abstract Mage_Adminhtml_Block_Widget_Form
 */
class Apptha_Airhotels_Block_Adminhtml_Createsubscriptions_Edit_Tab_General extends Mage_Adminhtml_Block_Widget_Form {
 /**
  * Get the values for product to change as a subscription product.
  *
  * @abstract Mage_Adminhtml_Block_Widget_Form
  * 
  * @return void
  */
 protected function _prepareForm() {
  $varForm = new Varien_Data_Form ();
  /**
   * Set form data.
   */
  $this->setForm ( $varForm );
  /**
   * Get the Id value
   */
  $id = $this->getRequest ()->getParam ( 'id' );
  /**
   * Get the Model for id
   */
  $model = Mage::getModel ( 'catalog/product' )->load ( $id );
  /**
   * Get product name form catalog/product table
   */
  $linkedProduct = $model->getName ();
  /**
   * Get product id form catalog/product table
   */
  $productId = $model->getId ();
  $fieldset = $varForm->addFieldset ( 'managesubscriptions_form', array (
    'legend' => Mage::helper ( 'airhotels' )->__ ( 'General' ) 
  ) );
  /**
   * Add the field of product_id
   */
  $fieldset->addField ( 'product_id', 'hidden', array (
    'label' => Mage::helper ( 'airhotels' )->__ ( 'Product Id' ),
    'name' => 'product_id',
    'value' => $productId 
  )
   );
  /**
   * Add the field of name
   */
  $fieldset->addField ( 'name', 'link', array (
    'label' => Mage::helper ( 'airhotels' )->__ ( 'Product Name' ),
    'name' => 'name',
    'value' => $linkedProduct 
  ) );
  /**
   * Add the field of is_subscription_only
   */
  $fieldset->addField ( 'is_subscription_only', 'select', array (
    'label' => Mage::helper ( 'airhotels' )->__ ( 'Is Subscription Only' ),
    'name' => 'is_subscription_only',
    'values' => array (
      array (
        'value' => 1,
        'label' => Mage::helper ( 'airhotels' )->__ ( 'Enabled' ) 
      ) 
    ) 
  )
   );
  /**
   * Add the field of start_date
   */
  $isstartDateValue = $fieldset->addField ( 'start_date', 'select', array (
    'label' => Mage::helper ( 'airhotels' )->__ ( 'Start Date' ),
    'name' => 'start_date',
    'values' => array (
      array (
        'value' => 0,
        'label' => Mage::helper ( 'airhotels' )->__ ( 'Moment Of Purchase' ) 
      ),
      array (
        'value' => 1,
        'label' => Mage::helper ( 'airhotels' )->__ ( 'Defined by Customer' ) 
      ) 
    ) 
  )
   );
  /**
   * Add the field of day_of_month
   */
  $dayOfMonth = $fieldset->addField ( 'day_of_month', 'text', array ('label' => Mage::helper ( 'airhotels' )->__ ( 'Day of Month' ), 'name' => 'day_of_month', 'required' => 'true' ));
  $this->setChild ( 'form_after', $this->getLayout ()->createBlock( 'adminhtml/widget_form_element_dependence' )->addFieldMap ( $isstartDateValue->getHtmlId (), $isstartDateValue->getName () )->addFieldMap ( $dayOfMonth->getHtmlId (), $dayOfMonth->getName () )->addFieldDependence ( $dayOfMonth->getName (), $isstartDateValue->getName (), 4 ) );
  if (Mage::getSingleton ( 'adminhtml/session' )->getManagesubscriptionsData ()) {
  /**
   * Set manage subscription data.
   */
   $varForm->setValues ( Mage::getSingleton( 'adminhtml/session' )->getManagesubscriptionsData () );
   Mage::getSingleton ( 'adminhtml/session')->setManagesubscriptionsData ( null );
  }
  if (Mage::registry( 'managesubscriptions_data' )) {
   $varForm->setValues ( Mage::registry( 'managesubscriptions_data')->getData());
  }
  /**
   * Calling the parent Construct Method.
   */
  return parent::_prepareForm ();
 }
}