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
 * To create new table `airhotels`
 * to manage property information
 * Fields added are
 * Id
 * Filename
 * status
 * created at
 * 
 */
$installer->run("
            DROP TABLE IF EXISTS {$this->getTable('airhotels')};
            CREATE TABLE {$this->getTable('airhotels')} (
              `airhotels_id` int(11) unsigned NOT NULL auto_increment,`title` varchar(255) NOT NULL default '',
              `filename` varchar(255) NOT NULL default '',`content` text NOT NULL default '',
              `status` smallint(6) NOT NULL default '0',
              `created_time` datetime NULL, `update_time` datetime NULL,
              PRIMARY KEY (`airhotels_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8; ");

 /**
 * To create new table `airhotels_property`
 * to manage property(product) information
 *  Fields added are
 *  ID
 * Entity ID
 * From Date
 * To date
 * checkin time
 * accomodates
 * host fee ,subtotal
 * order itemit,status,product name
 * customer email
 * order currecny code
 * meesage
 * cancel order status
 */ 
$installer->run("         
            DROP TABLE IF EXISTS {$this->getTable('airhotels_property')};
            CREATE TABLE {$this->getTable('airhotels_property')} (
              `id` int(100) unsigned NOT NULL auto_increment,
              `entity_id` int(100) NOT NULL ,`customer_id` int(100) NOT NULL ,
              `fromdate` date NULL,              
              `todate` date NULL,
              `checkin_time` datetime NULL, `checkout_time` datetime NULL,
              `accomodates` int(100) NOT NULL ,
              `host_fee` decimal(11,2) NOT NULL, `service_fee` decimal(11,2) NOT NULL,
              `subtotal` int(11) NOT NULL ,`order_id` int(100) NOT NULL ,
              `order_item_id` int(100) NOT NULL ,
              `order_status` smallint(6) NOT NULL default '0',`status` smallint(6) NOT NULL default '0',
              `product_name` varchar(500) NOT NULL default '',
              `customer_email` varchar(250) NOT NULL default '',  `base_currency_code` varchar(25) NOT NULL default '',
              `order_currency_code` varchar(25) NOT NULL default '',
              `message` text NOT NULL default '',
              `cancel_order_status` smallint(6) NOT NULL default '0',
              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8; ");

/**
 * To create new table `airhotels_customer_inbox`
 * to manage host(customer) information
 *  Fields added are
 *  Message Id,sender id
 *  receiver id,
 *  checkout
 *  can call
 *  read flag
 *  receiver delete,is_reply
 *  Sender_read,receiver_read
 *  isdelete,Mobileno
 */
$installer->run("
            DROP TABLE IF EXISTS {$this->getTable('airhotels_customer_inbox')};
            CREATE TABLE IF NOT EXISTS {$this->getTable('airhotels_customer_inbox')} (
              `message_id` int(11) NOT NULL AUTO_INCREMENT,
              `sender_id` int(11) NOT NULL,`receiver_id` int(11) NOT NULL,
              `product_id` int(11) NOT NULL,`checkin` date NOT NULL,
              `checkout` date NOT NULL,`guest` smallint(6) NOT NULL,`message` text NOT NULL,
              `can_call` tinyint(4) NOT NULL, `timezone` text NOT NULL,
              `read_flag` tinyint(4) NOT NULL,`is_sender_delete` tinyint(4) NOT NULL,
              `is_receiver_delete` tinyint(4) NOT NULL,
              `is_reply` tinyint(1) NOT NULL,
              `sender_read` tinyint(4) NOT NULL,
              `receiver_read` tinyint(4) NOT NULL,
              `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
              `isdelete` tinyint(10) NOT NULL,
              `mobileNo` varchar(20) NOT NULL default '',
              PRIMARY KEY (`message_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8; ");
/**
 * Table structure for table `airhotels_customer_photo`
 *  Fields added are
 *  customer Id
 *  image name
 *  created at
 */
$installer->run("

            DROP TABLE IF EXISTS {$this->getTable('airhotels_customer_photo')};
            CREATE TABLE IF NOT EXISTS {$this->getTable('airhotels_customer_photo')}  (
              `customer_id` int(11) NOT NULL,
              `imagename` varchar(250) NOT NULL, 
              `response_time` varchar(300) DEFAULT NULL,  
              `more_host` text,
              `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
              PRIMARY KEY (`customer_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

/**
 * Table structure for table `airhotels_customer_reply`
 *  Fields added are
 *  reply id,
 *  meesage id,
 *  message TEXT,
 *  CREATED AT
 */
$installer->run("     
       
            DROP TABLE IF EXISTS {$this->getTable('airhotels_customer_reply')}; 
            CREATE TABLE IF NOT EXISTS {$this->getTable('airhotels_customer_reply')} (
              `reply_id` int(11) NOT NULL AUTO_INCREMENT,
              `message_id` int(11) NOT NULL, `customer_id` int(11) NOT NULL,
              `message` text NOT NULL,`is_delete` int(11) NOT NULL,
              `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
              PRIMARY KEY (`reply_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8; ");
/**
 * create userid attribute
 * Group:Property Information
 * class:validate digits
 *  Group:property Information,
 * Visible:True,
 * searchable:true,
 * unique:false
 * Required:true
 */
if (!$installer->getAttributeId(Mage_Catalog_Model_Product::ENTITY, 'userid')) {
    $installer->addAttribute('catalog_product', 'userid', array(
        'group' => 'Property Information',
        'label' => 'User ID', 'type' => 'varchar','input' => 'text',
        'default' => '',  'readonly' => 'readonly',
        'class' => 'validate-digits',
        'backend' => '', 'frontend' => '',
        'source' => '',
        'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
        'visible' => true,'required' => true,
        'user_defined' => false,  'searchable' => false,
        'filterable' => false,  'comparable' => false,
        'visible_on_front' => true, 'visible_in_advanced_search' => false,
        'unique' => false,
        'apply_to' => 'property'
    ));
}
/**
 * To create new table `propertytype` 
 * for new product type  
 * along with six product types which is available by default
 * Group:property Information,
 * Visible:True,
 * searchable:true,
 * unique:false
 * Required:true
 */
if (!$installer->getAttributeId(Mage_Catalog_Model_Product::ENTITY, 'propertytype')) {
    $installer->addAttribute('catalog_product', 'propertytype', array(
        'group' => 'Property Information', 'label' => 'Property Type',
        'type' => 'varchar',
        'input' => 'select',
        'default' => '',  'class' => '',
        'backend' => 'eav/entity_attribute_backend_array',
        'frontend' => '',   'source' => '',
        'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
        'visible' => true,
        'required' => true,  'user_defined' => false,
        'searchable' => true,  'filterable' => false,
        'comparable' => false,   'visible_on_front' => true,
        'option' => array(
            'value' => array('Apartment' => array(0 => 'Apartment'), 'House' => array(0 => 'House'), 'Cottage' => array(0 => 'Cottage')),'order' => array('Apartment' => '0', 'House' => '1', 'Cottage' => '2')
        ),
        'visible_in_advanced_search' => true,
        'unique' => false,
        'apply_to' => 'property'
    ));
}
/**
 * Table structure for table `privacy`
 * To manage host privacy information
 * Group:property Information,
 * Visible:True,
 * searchable:true,
 * unique:false
 * Required:true
 */
if (!$installer->getAttributeId(Mage_Catalog_Model_Product::ENTITY, 'privacy')) {
    $installer->addAttribute('catalog_product', 'privacy', array(
        'group' => 'Property Information',
        'label' => 'Privacy',  'type' => 'varchar',
        'input' => 'select',
        'default' => '',  'class' => '',
        'backend' => 'eav/entity_attribute_backend_array',
        'frontend' => '',
        'source' => '',   'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
        'visible' => true,
        'required' => true, 'user_defined' => false,
        'searchable' => true,
        'filterable' => false, 'comparable' => false,
        'visible_on_front' => true,
        'option' => array( 'value' => array('Private' => array(0 => 'Private'), 'Shared' => array(0 => 'Shared'), 'Public' => array(0 => 'Public')),
            'order' => array('Private' => '0', 'Shared' => '1', 'Public' => '2')
        ),
        'visible_in_advanced_search' => true,
        'unique' => false,
        'apply_to' => 'property'
    ));
}
/**
 * Table structure for table `state`
 * To manage state data
 * Group:property Information,
 * Visible:True,
 * searchable:true,
 * unique:false
 * Required:true
 */
if (!$installer->getAttributeId(Mage_Catalog_Model_Product::ENTITY, 'state')) {
    $installer->addAttribute('catalog_product', 'state', array(
        'group' => 'Property Information',   'label' => 'State',
        'type' => 'varchar',   'input' => 'text',
        'default' => '',
        'backend' => '',   'frontend' => '','source' => '',
        'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,  'visible' => true,
        'required' => true, 'user_defined' => false,
        'searchable' => true,   'filterable' => false,
        'comparable' => false,'visible_on_front' => true,
        'visible_in_advanced_search' => false,
        'unique' => false,
        'apply_to' => 'property'
    ));
}
/**
 * Table structure for table `city`
 * To manage city data
 * Group:property Information,
 * Visible:True,
 * searchable:true,
 * unique:false
 * Required:true
 */
if (!$installer->getAttributeId(Mage_Catalog_Model_Product::ENTITY, 'city')) {
    $installer->addAttribute('catalog_product', 'city', array(
        'group' => 'Property Information',
        'label' => 'City',
        'type' => 'varchar', 'input' => 'text',
        'default' => '','backend' => '',
        'frontend' => '',
        'source' => '',   'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
        'visible' => true,
        'required' => true, 'user_defined' => false,
        'searchable' => true,
        'filterable' => false,
        'comparable' => false, 'visible_on_front' => true,
        'visible_in_advanced_search' => false,
        'unique' => false,
        'apply_to' => 'property'
    ));
}
/**
 * Table structure for table `country`
 * To manage country data
 * Group:property Information,
 * Visible:True,
 * searchable:true,
 * unique:false
 * Required:true
 */
if (!$installer->getAttributeId(Mage_Catalog_Model_Product::ENTITY, 'country')) {
    $installer->addAttribute('catalog_product', 'country', array(
        'group' => 'Property Information',
        'label' => 'Country', 'type' => 'varchar',
        'input' => 'text', 'default' => '',
        'backend' => '',
        'frontend' => '',  'source' => '',
        'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
        'visible' => true,
        'required' => true,   'user_defined' => false,  'searchable' => true,
        'filterable' => false,
        'comparable' => false, 'visible_on_front' => true,
        'visible_in_advanced_search' => false,
        'unique' => false,
        'apply_to' => 'property'
    ));
}
/**
 * Table structure for table `hostemail`
 * To manage hostemail data
 * Group:property Information,
 * Visible:True,
 * searchable:true,
 * unique:false
 * Required:true
 */
if (!$installer->getAttributeId(Mage_Catalog_Model_Product::ENTITY, 'hostemail')) {
    $installer->addAttribute('catalog_product', 'hostemail', array(
        'group' => 'Property Information',
        'label' => 'Email', 'type' => 'varchar',
        'input' => 'text',
        'default' => '', 'class' => 'validate-email',
        'backend' => '',
        'frontend' => '',   'source' => '',
        'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
        'visible' => true, 'required' => true,
        'user_defined' => false,
        'searchable' => true, 'filterable' => false,
        'comparable' => false,
        'visible_on_front' => true,  'visible_in_advanced_search' => false,
        'unique' => false,
        'apply_to' => 'property'
    ));
}
/**
 * Table structure for table `propertyadd`
 * To manage propertyadd data
 * Group:property Information,
 * Visible:True,
 * searchable:true,
 * unique:false
 * Required:true
 */
if (!$installer->getAttributeId(Mage_Catalog_Model_Product::ENTITY, 'propertyadd')) {
    $installer->addAttribute('catalog_product', 'propertyadd', array(
        'group' => 'Property Information', 'label' => 'Property Address',
        'type' => 'text',  'input' => 'textarea',
        'default' => '',
        'class' => '',  'backend' => '',
        'frontend' => '',  'source' => '',
        'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
        'visible' => true,   'required' => true,'user_defined' => false, 'searchable' => true,
        'filterable' => false,'comparable' => false, 'visible_on_front' => true,
        'visible_in_advanced_search' => false,'unique' => false,
        'apply_to' => 'property'
    ));
}

/**
 * updating the attribute propertyadd
 */
if ($installer->getAttributeId(Mage_Catalog_Model_Product::ENTITY, 'propertyadd')) {
    $installer->updateAttribute('catalog_product', 'propertyadd', 'is_global', Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE);
    $installer->updateAttribute('catalog_product', 'propertyadd', 'apply_to', 'property');
}
/**
 * Table structure for table `property_website`
 * To manage property_website data
 * Group:property Information,
 * Visible:True,
 * searchable:true,
 * unique:false
 * Required:false
 */
if (!$installer->getAttributeId(Mage_Catalog_Model_Product::ENTITY, 'property_website')) {
    $installer->addAttribute('catalog_product', 'property_website', array(
        'group' => 'Property Information', 'label' => 'Property Website',
        'type' => 'varchar',   'input' => 'text',
        'default' => '',   'class' => '',
        'backend' => '',
        'frontend' => '',      'source' => '',
        'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
        'visible' => true,     'required' => false,
        'user_defined' => false,  'searchable' => true,'filterable' => false,
        'comparable' => false, 'visible_on_front' => true,'visible_in_advanced_search' => false,  'unique' => false,
        'apply_to' => 'property'
    ));
}
/**
 * Table structure for table `property_website`
 * To manage property_website data
 */
if ($installer->getAttributeId(Mage_Catalog_Model_Product::ENTITY, 'property_website')) {
    $installer->updateAttribute('catalog_product', 'property_website', 'is_global', Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE); 
    $installer->updateAttribute('catalog_product', 'property_website', 'apply_to', 'property');
}
/**
 * Table structure for table `cancelpolicy`
 * To manage cancelpolicy data
 * Group:property Information,
 * Visible:True,
 * searchable:true,
 * unique:false
 * Required:true
 */
if (!$installer->getAttributeId(Mage_Catalog_Model_Product::ENTITY, 'cancelpolicy')) {
    $installer->addAttribute('catalog_product', 'cancelpolicy', array(
        'group' => 'Property Information',
        'label' => 'Cancellation Policy', 'type' => 'varchar',
        'input' => 'select',   'default' => '',
        'class' => '','backend' => 'eav/entity_attribute_backend_array',
        'frontend' => '','source' => '',
        'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
        'visible' => true,'required' => true,'user_defined' => false,
        'searchable' => true,'filterable' => false,
        'comparable' => false,
        'visible_on_front' => true,
        'option' => array('value' => array('Flexible: Full refund 1 day prior to arrival, except fees' => array(0 => 'Flexible: Full refund 1 day prior to arrival, except fees'), 'Moderate: Full refund 5 days prior to arrival, except fees' => array(0 => 'Moderate: Full refund 5 days prior to arrival, except fees'), 'Strict: 50% refund up until 1 week prior to arrival, except fees' => array(0 => 'Strict: 50% refund up until 1 week prior to arrival, except fees')),'order' => array('Flexible: Full refund 1 day prior to arrival, except fees' => '0', 'Moderate: Full refund 5 days prior to arrival, except fees' => '1', 'Strict: 50% refund up until 1 week prior to arrival, except fees' => '2')
        ),
        'visible_in_advanced_search' => true,
        'unique' => false,
        'apply_to' => 'property'
    ));
}
/**
 * Adding some columns
 */
$installer->getConnection()->addColumn($this->getTable('sales/quote_address'), 'fee_amount', 'DECIMAL( 10, 2 ) NOT NULL');
$installer->getConnection()->addColumn($this->getTable('sales/quote_address'), 'base_fee_amount', 'DECIMAL( 10, 2 ) NOT NULL');
$installer->getConnection()->addColumn($this->getTable('sales/order'), 'fee_amount', 'DECIMAL( 10, 2 ) NOT NULL');
$installer->getConnection()->addColumn($this->getTable('sales/order'), 'base_fee_amount', 'DECIMAL( 10, 2 ) NOT NULL');
/**
 * Table structure for table `airhotels_calendar`
 *  Fields added are
 *  id,
 *  month
 *  blockfrom,
 *  updated
 */
$installer->run("
DROP TABLE IF EXISTS {$this->getTable('airhotels_calendar')};
CREATE TABLE IF NOT EXISTS  {$this->getTable('airhotels_calendar')} (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `product_id` int(10) NOT NULL,
  `book_avail` int(10) NOT NULL,
  `month` int(10) NOT NULL,
  `year` int(10) NOT NULL,
  `blockfrom` varchar(500) NOT NULL,
  `price` decimal(10,0) NOT NULL,
  `created` date NOT NULL,
  `updated` date NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
");
/**
 * Table structure for table `propertyapproved`
 * To manage propertyapproved data
 * Group:property Information,
 * Visible:True,
 * searchable:true,
 * unique:false
 * Required:false
 */
if (!$installer->getAttributeId(Mage_Catalog_Model_Product::ENTITY, 'propertyapproved')) {
    $installer->addAttribute('catalog_product', 'propertyapproved', array(
        'group' => 'Property Information','label' => 'Property Approved',
        'type' => 'int',
        'input' => 'select','default' => '',
        'backend' => '','frontend' => '',
        'source' => 'eav/entity_attribute_source_boolean',
        'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
        'visible' => true,
        'required' => false,
        'user_defined' => false, 'searchable' => false,
        'filterable' => false,'comparable' => false,
        'visible_on_front' => true,
        'visible_in_advanced_search' => false, 'unique' => false,
        'apply_to' => 'property',
        'sort_order' => 35
    ));
}
/**
 * Table structure for table `amenity`
 * To manage amenity data
 * Group:property Information,
 * Visible:True,
 * searchable:true,
 * unique:false
 * Required:false
 */
if (!$installer->getAttributeId(Mage_Catalog_Model_Product::ENTITY, 'amenity')) {
    $installer->addAttribute('catalog_product', 'amenity', array('group' => 'Property Information','label' => 'Specifications',
        'type' => 'varchar','input' => 'multiselect', 'default' => '',
        'class' => '', 'backend' => 'eav/entity_attribute_backend_array',
        'frontend' => '', 'source' => '',
        'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE, 'visible' => true,
        'required' => false,'user_defined' => false,'searchable' => true,
        'filterable' => false,'comparable' => false,'visible_on_front' => true,
        'option' => array(
            'value' => array('Smoking' => array(0 => 'Smoking',), 'Kitchen' => array(0 => 'Kitchen'), 'RoomService' => array(0 => 'Room Service')),
            'order' => array('Smoking' => '0', 'Kitchen' => '1', 'Roomservice' => '2')
        ),
        'visible_in_advanced_search' => true,'unique' => false,
        'apply_to' => 'property',
    ));
}
/**
 * updating the attribute amenity
 */
if ($installer->getAttributeId(Mage_Catalog_Model_Product::ENTITY, 'amenity')) {
    $installer->updateAttribute('catalog_product', 'amenity', 'is_global', Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE);
    $installer->updateAttribute('catalog_product', 'amenity', 'apply_to', 'property');
}
/**
 * Table structure for table `property_time`
 * To manage property_time data
 * Group:property Information,
 * Visible:True,
 * searchable:true,
 * unique:false
 * Required:true
 */
if (!$installer->getAttributeId(Mage_Catalog_Model_Product::ENTITY, 'property_time')) {

    $installer->addAttribute('catalog_product', 'property_time', array(
        'group' => 'Property Information',
        'label' => 'Property Time', 'type' => 'varchar','input' => 'select','default' => '', 'class' => '',
        'backend' => 'eav/entity_attribute_backend_array',
        'frontend' => '',
        'source' => '',
        'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
        'visible' => true, 'required' => true,
        'user_defined' => false,'searchable' => true,'filterable' => false,
        'comparable' => false,'visible_on_front' => true,
        'option' => array(
            'value' => array('Daily' => array(0 => 'Daily'), 'Hourly' => array(0 => 'Hourly')),
            'order' => array('Daily' => '0', 'Hourly' => '1')
        ),
        'visible_in_advanced_search' => true,
        'unique' => false,
        'apply_to' => 'property'
    ));
}
/**
 * Table structure for table `property_overnight_fee`
 * To manage property_overnight_fee data
 * Group:property Information,
 * Visible:True,
 * searchable:true,
 * unique:false
 * Required:false
 */
if (!$installer->getAttributeId(Mage_Catalog_Model_Product::ENTITY, 'property_overnight_fee')) {
    $installer->addAttribute('catalog_product', 'property_overnight_fee', array(
        'group' => 'Property Information',
        'label' => 'Overnight Fee',
        'type' => 'varchar','input' => 'text',
        'default' => '','class' => 'validate-number',
        'backend' => '','frontend' => '','source' => '',
        'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
        'visible' => true, 'required' => false,'user_defined' => false,
        'searchable' => true, 'filterable' => false,
        'comparable' => false,'visible_on_front' => true, 'visible_in_advanced_search' => true,'unique' => false,'apply_to' => 'property',
    ));
}
/**
 * Table structure for table `property_minimum`
 * To manage property_minimum data
 * Group:property Information,
 * Visible:True,
 * searchable:true,
 * unique:false
 * Required:false
 */
if (!$installer->getAttributeId(Mage_Catalog_Model_Product::ENTITY, 'property_minimum')) {
    $installer->addAttribute('catalog_product', 'property_minimum', array(
        'group' => 'Property Information',
        'label' => 'Property Minimum Hours/Days','type' => 'varchar',
        'input' => 'text','default' => '',
        'class' => 'validate-digits','backend' => '',
        'frontend' => '','source' => '',
        'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
        'visible' => true,
        'required' => false,'user_defined' => false,
        'searchable' => true,'filterable' => false,
        'comparable' => false,'visible_on_front' => true,
        'visible_in_advanced_search' => true,'unique' => false,
        'apply_to' => 'property',
    ));
}
/**
 * Table structure for table `property_minimum`
 * To manage property_minimum data
 * Group:property Information,
 * Visible:True,
 * searchable:true,
 * unique:false
 * Required:false
 */
if (!$installer->getAttributeId(Mage_Catalog_Model_Product::ENTITY, 'property_maximum')) {
    $installer->addAttribute('catalog_product', 'property_maximum', array(
        'group' => 'Property Information',
        'label' => 'Property Maximum Hours/Days','type' => 'varchar',
        'input' => 'text','default' => '',
        'class' => 'validate-digits','backend' => '',
        'frontend' => '', 'source' => '',
        'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,'visible' => true,
        'required' => false,'user_defined' => false,
        'searchable' => true, 'filterable' => false,
        'comparable' => false,'visible_on_front' => true,
        'visible_in_advanced_search' => true,'unique' => false,
        'apply_to' => 'property',
    ));
}
/**
 * Table structure for table `property_service_from_time`
 * To manage property_service_from_time data
 * Group:property Information,
 * Visible:True,
 * searchable:true,
 * unique:false,
 * Required:false
 */
if (!$installer->getAttributeId(Mage_Catalog_Model_Product::ENTITY, 'property_service_from_time')) {
    $installer->addAttribute('catalog_product', 'property_service_from_time', array(
        'group' => 'Property Information',
        'label' => 'Property Service From',
        'type' => 'varchar',
        'input' => 'text','default' => '','class' => '','backend' => '',
        'frontend' => '','source' => '',
        'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
        'visible' => true,'required' => false,'user_defined' => false,
        'searchable' => true,
        'filterable' => false,'comparable' => false,'visible_on_front' => true,'visible_in_advanced_search' => true,
        'unique' => false,
        'apply_to' => 'property',
    ));
}
/**
 * Table structure for table `property_service_to_time`
 * To manage property_service_to_time data
 * Group:property Information,
 * Visible:True,
 * searchable:true,
 * unique:false
 * Required:false
 */
if (!$installer->getAttributeId(Mage_Catalog_Model_Product::ENTITY, 'property_service_to_time')) {
    $installer->addAttribute('catalog_product', 'property_service_to_time', array(
        'group' => 'Property Information',
        'label' => 'Property Service To',
        'type' => 'varchar','input' => 'text','default' => '','class' => '','backend' => '',
        'frontend' => '','source' => '',
        'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
        'visible' => true,
        'required' => false,'user_defined' => false,
        'searchable' => true,'filterable' => false,'comparable' => false,
        'visible_on_front' => true,
        'visible_in_advanced_search' => true,'unique' => false,
        'apply_to' => 'property',
    ));
}
/**
 * unset installer object.
 */
$installer->endSetup();