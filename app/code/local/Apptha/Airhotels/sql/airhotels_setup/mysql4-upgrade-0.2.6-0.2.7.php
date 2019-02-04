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
$installer = $this;
/**
 *  @var $installer Mage_Core_Model_Resource_Setup */

/**
 * Load Initial setup
 */
$installer->startSetup();

/**
 * To create new attribute "Total Rooms" which will apply for Property product type
 * input - textbox
 * type - varchar
 * Group:property Information
 * Visible:True
 * searchable:true,
 * unique:false
 */
if (!$installer->getAttributeId(Mage_Catalog_Model_Product::ENTITY, 'totalrooms')) {
$installer->addAttribute('catalog_product', 'totalrooms', array(
'group' => 'Property Information','label' => 'Rooms Available',
'type' => 'varchar','input' => 'text',
'default' => '30',
'class' => 'validate-digits','backend' => '',
'frontend' => '','source' => '',
'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
'visible' => true,
'required' => true,'user_defined' => false,
'searchable' => true,
'filterable' => false,'comparable' => false,
'visible_on_front' => true,'visible_in_advanced_search' => false,
'unique' => false,
'apply_to' => 'property',
));
}

/**
 * To create new attribute bedtype which will apply for Property product type
 * input - select
 * type - varchar
 * Group:property Information,
 * Visible:True,
 * searchable:true,
 * unique:false
 */
if (!$installer->getAttributeId(Mage_Catalog_Model_Product::ENTITY, 'bedtype')) {
$installer->addAttribute('catalog_product', 'bedtype', array(
'group' => 'Property Information',
'label' => 'Bed Type','type' => 'varchar','input' => 'select',
'default' => '','class' => '',
'backend' => 'eav/entity_attribute_backend_array','frontend' => '','source' => '',
'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
'visible' => true,'required' => true,'user_defined' => false,
'searchable' => true,'filterable' => false,'comparable' => false,
'visible_on_front' => true,
'option' => array(
'value' => array('Cushion' => array(0 => 'Cushion'), 'Real Bed' => array(0 => 'Real Bed'), 'Air Beds' => array(0 => 'Air Beds')),
'order' => array('Cushion' => '0', 'Real Bed' => '1', 'Air Beds' => '2')
),
'visible_in_advanced_search' => true,
'unique' => false,
'apply_to' => 'property',
));
}
/**
 * unset installer object.
 */
$installer->endSetup();