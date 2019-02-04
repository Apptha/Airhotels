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
 *  @var $installer Mage_Core_Model_Resource_Setup 
 */
/**
 * Load Initial setup
 */
$installer->startSetup();
/**
 * Alter table airhotels_calendar
 * Added two fields
 * google calendar event uid,Block time
 * 
 */
/**
 * Alter table airhotels_customer_inbox
 * Added two fields
 * mobile number
 *
 */
$installer->run("
ALTER TABLE  {$this->getTable('airhotels_calendar')} ADD  `google_calendar_event_uid` varchar(250) DEFAULT NULL;
ALTER TABLE  {$this->getTable('airhotels_calendar')} ADD  `blocktime` varchar(250) DEFAULT NULL;
ALTER TABLE  {$this->getTable('airhotels_customer_inbox')} ADD  `mobile_no` varchar(30) NOT NULL default '';
");
$tableName = $installer->getTable('airhotels/tagsverification');
if (!$installer->getConnection()->isTableExists($tableName)){
$table = $installer->getConnection()
->newTable($installer->getTable('airhotels/tagsverification'))
->addColumn('tag_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
'identity'  => true,'unsigned'  => true,'nullable'  => false,'primary'   => true,
), 'Tag Id')
->addColumn('tag_name', Varien_Db_Ddl_Table::TYPE_VARCHAR, null, array('nullable'  => false,
), 'Tag Name')
->addColumn('tag_description', Varien_Db_Ddl_Table::TYPE_VARCHAR, null, array('nullable'  => false,), ' Tag Description')
->addColumn('direct_url', Varien_Db_Ddl_Table::TYPE_TINYINT, null, array('nullable'  => true,'default' => null,
), 'Allow Direct Url');
$installer->getConnection()->createTable($table);
}
/**
 * Table structure for table `google_calendar_ics_url`
 * To manage google_calendar_ics_url data
 * input: text
 * visible:true
 * required:false
 * unique:false
 */
if (!$installer->getAttributeId(Mage_Catalog_Model_Product::ENTITY, 'google_calendar_ics_url')) {
    $installer->addAttribute('catalog_product', 'google_calendar_ics_url', array(
        'group' => 'Property Information',
        'label' => 'Google Calendar ICS Url',
        'type' => 'varchar','input' => 'text',
        'default' => '',
        'class' => '','backend' => '',
        'frontend' => '', 'source' => '',
        'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
        'visible' => true,'required' => false,
        'user_defined' => false, 'searchable' => true,
        'filterable' => false,'comparable' => false,
        'visible_on_front' => true,
        'visible_in_advanced_search' => true,'unique' => false,
        'apply_to' => 'property',
    ));
}
/**
 * Table structure for table `auto_ics_sync`
 * To manage auto_ics_sync data
 * Group:PRoperty Information,
 * visible:true,
 *  Required::false,
 *  filterable:false
 */
if (!$installer->getAttributeId(Mage_Catalog_Model_Product::ENTITY, 'auto_ics_sync')) {
    $installer->addAttribute('catalog_product', 'auto_ics_sync', array(
        'group' => 'Property Information', 'label' => 'Auto Ics Sync Enabled',
        'type' => 'varchar','input' => 'select','default' => '',
        'class' => '',
        'backend' => 'eav/entity_attribute_backend_array', 'frontend' => '',
        'source' => '',
        'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
        'visible' => true,
        'required' => false, 'user_defined' => false,
        'searchable' => true,
        'filterable' => false,  'comparable' => false,
        'visible_on_front' => true,
        'option' => array(
            'value' => array('Yes' => array(0 => 'Yes'),'No' => array(0 => 'No')),
            'order' => array('Yes' => '0', 'No' => '1')
        ),
        'visible_in_advanced_search' => true, 'unique' => false,
        'apply_to' => 'property'
    ));
}
/**
 * To get the price attribute values
 */
if ($installer->getAttributeId(Mage_Catalog_Model_Product::ENTITY, 'price')) {
$attributeCode = 'price';
$applyTo = explode(
',',
$installer->getAttribute(
Mage_Catalog_Model_Product::ENTITY,
$attributeCode,
'apply_to'
)
);
/**
 * To update the Price attribute
 * if the attribute is not assigned for Property product type
 */
if (!in_array('property', $applyTo)) {
$applyTo[] = 'property';
$installer->updateAttribute(
Mage_Catalog_Model_Product::ENTITY,
$attributeCode,
'apply_to',
join(',', $applyTo)
);
}
}
/**
 * To create new attribute "Latitude" which will apply for Property product type as hidden field
 * input - text
 * type - varchar
 * * Group:property Information,
 * Visible:True,
 * searchable:true,
 * unique:false
 */
if (!$installer->getAttributeId(Mage_Catalog_Model_Product::ENTITY, 'latitude')) {
$installer->addAttribute('catalog_product', 'latitude', array(
'group' => 'Property Information',
'label' => 'Latitude','type' => 'varchar','input' => 'text','default' => '',
'backend' => '','frontend' => '','source' => '',
'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
'visible' => true,'required' => false,
'user_defined' => false,'searchable' => true,'filterable' => false,
'comparable' => false,
'visible_on_front' => true,'visible_in_advanced_search' => false,'unique' => false,
'apply_to' => 'property'
));
}
/**
 * To create new attribute "Longtitude" which will apply for Property product type as hidden field
 * input - text
 * type - varchar
 *  Group:property Information,
 * Visible:True,
 * searchable:true,
 * unique:false
 */
if (!$installer->getAttributeId(Mage_Catalog_Model_Product::ENTITY, 'longitude')) {
$installer->addAttribute('catalog_product', 'longitude', array(
'group' => 'Property Information','label' => 'Longitude','type' => 'varchar',
'input' => 'text','default' => '','backend' => '',
'frontend' => '','source' => '',
'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
'visible' => true,'required' => false,
'user_defined' => false,'searchable' => true,
'filterable' => false,'comparable' => false,
'visible_on_front' => true,'visible_in_advanced_search' => false,'unique' => false,
'apply_to' => 'property'
));
}
/**
 * unset installer object.
 */
$installer->endSetup();