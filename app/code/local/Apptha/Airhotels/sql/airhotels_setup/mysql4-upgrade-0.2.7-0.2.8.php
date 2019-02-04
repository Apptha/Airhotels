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
 * Remove bedtype attribute.
 */
if ($installer->getAttributeId(Mage_Catalog_Model_Product::ENTITY, 'bedtype')) {
$installer->removeAttribute('catalog_product', 'bedtype');
}
/**
 * Remove pets product attribute.
 */
if ($installer->getAttributeId(Mage_Catalog_Model_Product::ENTITY, 'pets')) {
$installer->removeAttribute('catalog_product', 'pets');
}
/**
 * Remove totalsrooms product attribute.
 */
if ($installer->getAttributeId(Mage_Catalog_Model_Product::ENTITY, 'totalrooms')) {
$installer->removeAttribute('catalog_product', 'totalrooms');
}
/**
 * Remove property_type product atttribute.
 */
if ($installer->getAttributeId(Mage_Catalog_Model_Product::ENTITY, 'property_type')) {
$installer->removeAttribute('catalog_product', 'property_type');
}

/**
 * Create table airhotels_longitude.
 * @fields id
 * @fields latitude
 * @fields longitude
 */
$installer->run("DROP TABLE IF EXISTS {$this->getTable('airhotels_longitude')};
CREATE TABLE IF NOT EXISTS  {$this->getTable('airhotels_longitude')} (
`id` int(10) NOT NULL AUTO_INCREMENT,`entity_id` int(10) NOT NULL,
`latitude` varchar(500) NULL,
`longitude` varchar(500) NULL,
PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8; ");

/**
 * 
 * Table structure for table `airhotels_invite_friends_product`
 * Fields are,'id','customer_id'
 * 'invitee_id','product_id', 'website_id'
 * 'store_id', 'created_at'
 */

$installer->run("
DROP TABLE IF EXISTS {$this->getTable('airhotels_invite_friends_product')};
CREATE TABLE {$this->getTable('airhotels_invite_friends_product')} (
`id` int(11) unsigned NOT NULL auto_increment,
`customer_id` int(11) NOT NULL,`invitee_id` int(11) NOT NULL,`product_id` int(11) NOT NULL,
`website_id` int(11) NOT NULL,`store_id` int(11) NOT NULL,`created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8; ");

/**
 * Table structure for table `airhotels_invite_friends_order`
 * Fields are,
 * 'website_id'
 * 'store_id','created_at'
 * 'id','customer_id'
 * 'invitee_id','order_id'
 */

$installer->run("DROP TABLE IF EXISTS {$this->getTable('airhotels_invite_friends_order')};
CREATE TABLE {$this->getTable('airhotels_invite_friends_order')} (
`id` int(11) unsigned NOT NULL auto_increment,`customer_id` int(11) NOT NULL,`invitee_id` int(11) NOT NULL,`order_id` int(11) NOT NULL,
`website_id` int(11) NOT NULL,`store_id` int(11) NOT NULL,
`created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8; ");

/**
 * Table structure for table `airhotels_invite_friends_discount`
 * 'id'
 * 'increment_id','customer_id'
 * 'website_id'
 * 'store_id','discount_amount'
 * 'created_at'
 */

$installer->run("DROP TABLE IF EXISTS {$this->getTable('airhotels_invite_friends_discount')};
CREATE TABLE {$this->getTable('airhotels_invite_friends_discount')} (
`id` int(11) unsigned NOT NULL auto_increment,`customer_id` int(11) NOT NULL,`increment_id` int(11) NOT NULL, `discount_amount` decimal(12,4) NOT NULL,
`website_id` int(11) NOT NULL, `store_id` int(11) NOT NULL,`created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8; ");

/**
 * Table structure for table `airhotels_city`
 *  Fields added are
 *  City, City Description ,City Image 
 *  Thumb Image , Small Image
 *  Created At
 */

$installer->run("DROP TABLE IF EXISTS {$this->getTable('airhotels_city')};
CREATE TABLE {$this->getTable('airhotels_city')} (
`id` int(11) unsigned NOT NULL auto_increment,`city` varchar(255) NOT NULL default '',`city_description` varchar(255) NOT NULL default '',
`city_image` varchar(255) NOT NULL default '',`thumb_image` varchar(255) NOT NULL default '',`small_image` varchar(255) NOT NULL DEFAULT '',`created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8; ");


/**
 * To create new table `airhotels_customer_inbox`
 * to manage host(customer) information
 *  Fields added are
 *  Message Id,video_name,
 *  video_url_mp,video_url_webm
 *  image_url,thumb_image,small_image
 *  status,created_at
 */

$installer->run ( "DROP TABLE IF EXISTS {$this->getTable('airhotels_uploadvideo')};
CREATE TABLE {$this->getTable('airhotels_uploadvideo')} (`id` int(11) unsigned NOT NULL auto_increment,`video_name` varchar(255) NOT NULL default '',`video_url_mp4` varchar(255) NOT NULL default '',`video_url_webm` varchar(255) NOT NULL default '',`image_url` varchar(255) NOT NULL default '',`thumb_image` varchar(255) NOT NULL default '',`small_image` varchar(255) NOT NULL default '',`status` smallint(6) NOT NULL default '0',`created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8; " );

/**
 * To create new table `airhotels_managebankdetails`
 * to manage the Bank details
 * 'id','country_code'
 * 'currency_code','field_name','field_title'
 * 'field_required'
 */
$installer->run ( "DROP TABLE IF EXISTS {$this->getTable('airhotels_managebankdetails')};
CREATE TABLE {$this->getTable('airhotels_managebankdetails')} (`id` int(11) unsigned NOT NULL auto_increment,`country_code` text NOT NULL default '',
`currency_code` text NOT NULL default '',`field_name` varchar(255) NOT NULL default '',`field_title` varchar(255) NOT NULL default '',`field_required` int(11) unsigned NOT NULL,`created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

/**
 * To create new table `airhotels_invite_friends`
 * to manage the friends details
 * 'id','customer_id','customer_name','customer_email'
 * 'invitee_id','invitee_name', 'invitee_email','balance_credit_amount'
 * 'friends_listing_count','overall_credit_amount','website_id','store_id'
 * 'created_at'
 */
$installer->run ( "DROP TABLE IF EXISTS {$this->getTable('airhotels_invite_friends')};
CREATE TABLE {$this->getTable('airhotels_invite_friends')} (`id` int(11) unsigned NOT NULL auto_increment,`customer_id` int(11) NOT NULL,`customer_name` varchar(255) NOT NULL default '',`customer_email` varchar(255) NOT NULL default '',
`invitee_id` int(11) NOT NULL,`invitee_name` varchar(255) NOT NULL default '',`invitee_email` varchar(255) NOT NULL default '',
`balance_credit_amount` decimal(12,4) NOT NULL,`friends_purchase_count` int(11) NOT NULL,`friends_listing_count` int(11) NOT NULL,`overall_credit_amount` decimal(12,4) NOT NULL,`website_id` int(11) NOT NULL,`store_id` int(11) NOT NULL,`created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8;" );

/**
 * To create new table `airhotels_hostnotifications`
 * to manage the notification details
 * 'id','user_id','inbox'
 * 'recieve_request','response_request','account_listing'
 * 'created_at'
 */

$installer->run ( "DROP TABLE IF EXISTS {$this->getTable('airhotels_hostnotifications')};
CREATE TABLE {$this->getTable('airhotels_hostnotifications')} (
`id` int(11) unsigned NOT NULL auto_increment,`user_id` int(11) unsigned NOT NULL,`inbox` varchar(255) NOT NULL default '',`recieve_request` varchar(255) NOT NULL default '',`response_request` varchar(255) NOT NULL default '',`account_listing` varchar(255) NOT NULL default '',`created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
`view_datetime` DATETIME,PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

/**
 * To create new table `airhotels_footerpage`
 * to manage the notification details
 * 'footer_id','parent_id'
 * 'thumb_image','small_image','created_at'
 * 'main_menu_title'
 * 'is_mainmenu','sub_menu_title'
 * 'page_content','footer_banner_image
 */

$installer->run ( "DROP TABLE IF EXISTS {$this->getTable('airhotels_footerpage')};
CREATE TABLE {$this->getTable('airhotels_footerpage')} (
`footer_id` int(11) unsigned NOT NULL auto_increment,`parent_id` int(100) NOT NULL ,`is_mainmenu` int(100) NOT NULL ,`main_menu_title` varchar(255) NOT NULL default '',`sub_menu_title` varchar(255) NOT NULL default '',`page_content` Text NOT NULL default '',`footer_banner_image` varchar(255) NOT NULL default '',`small_image` varchar(255) NOT NULL default '',`thumb_image` varchar(255) NOT NULL default '',
`created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,PRIMARY KEY (`footer_id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8;" );

/**
 * To create new table `airhotels_allcurrency`
 * to manage the currency details
 * 'id','value'
 * 'label','created_at'
 */
$installer->run ( "DROP TABLE IF EXISTS {$this->getTable('airhotels_allcurrency')};
CREATE TABLE {$this->getTable('airhotels_allcurrency')} (
`id` int(11) unsigned NOT NULL auto_increment,`value` text NOT NULL default '',`label` text NOT NULL default '',
`created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8; ");

/**
 * Store all currency
 */
$allCurrrency=array (array( 'value'=>'ALL','label'=> 'Albania Lek'),
array('value'=>'AFN','label' => 'Afghanistan Afghani'),
array('value'=>'ARS','label' => 'Argentina Peso'),
array('value'=>'AWG','label' => 'Aruba Guilder'),
array('value'=>'AUD','label' => 'Australia Dollar'),
array('value'=>'AZN','label' => 'Azerbaijan New Manat'),
array('value'=>'BSD','label' => 'Bahamas Dollar'),
array('value'=>'BBD','label' => 'Barbados Dollar'),
array('value'=>'BDT','label' => 'Bangladeshi taka'),
array('value'=>'BYR','label' => 'Belarus Ruble'),
array('value'=>'BZD','label' => 'Belize Dollar'),
array('value'=>'BMD','label' => 'Bermuda Dollar'),
array('value'=>'BOB','label' => 'Bolivia Boliviano'),
array('value'=>'BAM','label' => 'Bosnia and Herzegovina Convertible Marka'),
array('value'=>'BWP','label' => 'Botswana Pula'),
array('value'=>'BGN','label' => 'Bulgaria Lev'),
array('value'=>'BRL','label' => 'Brazil Real'),
array('value'=>'BND','label' => 'Brunei Darussalam Dollar'),
array('value'=>'KHR','label' => 'Cambodia Riel'),
array('value'=>'CAD','label' => 'Canada Dollar'),
array('value'=>'KYD','label' => 'Cayman Islands Dollar'),
array('value'=>'CLP','label' => 'Chile Peso'),
array('value'=>'CNY','label' => 'China Yuan Renminbi'),
array('value'=>'COP','label' => 'Colombia Peso'),
array('value'=>'CRC','label' => 'Costa Rica Colon'),
array('value'=>'HRK','label' => 'Croatia Kuna'),
array('value'=>'CUP','label' => 'Cuba Peso'),
array('value'=>'CZK','label' => 'Czech Republic Koruna'),
array('value'=>'DKK','label' => 'Denmark Krone'),
array('value'=>'DOP','label' => 'Dominican Republic Peso'),
array('value'=>'XCD','label' => 'East Caribbean Dollar'),
array('value'=>'EGP','label' => 'Egypt Pound'),
array('value'=>'SVC','label' => 'El Salvador Colon'),
array('value'=>'EEK','label' => 'Estonia Kroon'),
array('value'=>'EUR','label' => 'Euro Member Countries'),
array('value'=>'FKP','label' => 'Falkland Islands (Malvinas) Pound'),
array('value'=>'FJD','label' => 'Fiji Dollar'),
array('value'=>'GHC','label' => 'Ghana Cedis'),
array('value'=>'GIP','label' => 'Gibraltar Pound'),
array('value'=>'GTQ','label' => 'Guatemala Quetzal'),
array('value'=>'GGP','label' => 'Guernsey Pound'),
array('value'=>'GYD','label' => 'Guyana Dollar'),
array('value'=>'HNL','label' => 'Honduras Lempira'),
array('value'=>'HKD','label' => 'Hong Kong Dollar'),
array('value'=>'HUF','label' => 'Hungary Forint'),
array('value'=>'ISK','label' => 'Iceland Krona'),
array('value'=>'INR','label' => 'India Rupee'),
array('value'=>'IDR','label' => 'Indonesia Rupiah'),
array('value'=>'IRR','label' => 'Iran Rial'),
array('value'=>'IMP','label' => 'Isle of Man Pound'),
array('value'=>'ILS','label' => 'Israel Shekel'),
array('value'=>'JMD','label' => 'Jamaica Dollar'),
array('value'=>'JPY','label' => 'Japan Yen'),
array('value'=>'JEP','label' => 'Jersey Pound'),
array('value'=>'KZT','label' => 'Kazakhstan Tenge'),
array('value'=>'KPW','label' => 'Korea (North) Won'),
array('value'=>'KRW','label' => 'Korea (South) Won'),
array('value'=>'KGS','label' => 'Kyrgyzstan Som'),
array('value'=>'LAK','label' => 'Laos Kip'),
array('value'=>'LVL','label' => 'Latvia Lat'),
array('value'=>'LBP','label' => 'Lebanon Pound'),
array('value'=>'LRD','label' => 'Liberia Dollar'),
array('value'=>'LTL','label' => 'Lithuania Litas'),
array('value'=>'MKD','label' => 'Macedonia Denar'),
array('value'=>'MYR','label' => 'Malaysia Ringgit'),
array('value'=>'MUR','label' => 'Mauritius Rupee'),
array('value'=>'MXN','label' => 'Mexico Peso'),
array('value'=>'MNT','label' => 'Mongolia Tughrik'),
array('value'=>'MZN','label' => 'Mozambique Metical'),
array('value'=>'NAD','label' => 'Namibia Dollar'),
array('value'=>'NPR','label' => 'Nepal Rupee'),
array('value'=>'ANG','label' => 'Netherlands Antilles Guilder'),
array('value'=>'NZD','label' => 'New Zealand Dollar'),
array('value'=>'NIO','label' => 'Nicaragua Cordoba'),
array('value'=>'NGN','label' => 'Nigeria Naira'),
array('value'=>'NOK','label' => 'Norway Krone'),
array('value'=>'OMR','label' => 'Oman Rial'),
array('value'=>'PKR','label' => 'Pakistan Rupee'),
array('value'=>'PAB','label' => 'Panama Balboa'),
array('value'=>'PYG','label' => 'Paraguay Guarani'),
array('value'=>'PEN','label' => 'Peru Nuevo Sol'),
array('value'=>'PHP','label' => 'Philippines Peso'),
array('value'=>'PLN','label' => 'Poland Zloty'),
array('value'=>'QAR','label' => 'Qatar Riyal'),
array('value'=>'RON','label' => 'Romania New Leu'),
array('value'=>'RUB','label' => 'Russia Ruble'),
array('value'=>'SHP','label' => 'Saint Helena Pound'),
array('value'=>'SAR','label' => 'Saudi Arabia Riyal'),
array('value'=>'RSD','label' => 'Serbia Dinar'),
array('value'=>'SCR','label' => 'Seychelles Rupee'),
array('value'=>'SGD','label' => 'Singapore Dollar'),
array('value'=>'SBD','label' => 'Solomon Islands Dollar'),
array('value'=>'SOS','label' => 'Somalia Shilling'),
array('value'=>'ZAR','label' => 'South Africa Rand'),
array('value'=>'LKR','label' => 'Sri Lanka Rupee'),
array('value'=>'SEK','label' => 'Sweden Krona'),
array('value'=>'CHF','label' => 'Switzerland Franc'),
array('value'=>'SRD','label' => 'Suriname Dollar'),
array('value'=>'SYP','label' => 'Syria Pound'),
array('value'=>'TWD','label' => 'Taiwan New Dollar'),
array('value'=>'THB','label' => 'Thailand Baht'),
array('value'=>'TTD','label' => 'Trinidad and Tobago Dollar'),
array('value'=>'TRY','label' => 'Turkey Lira'),
array('value'=>'TRL','label' => 'Turkey Lira'),
array('value'=>'TVD','label' => 'Tuvalu Dollar'),
array('value'=>'UAH','label' => 'Ukraine Hryvna'),
array('value'=>'GBP','label' => 'United Kingdom Pound'),
array('value'=>'USD','label' => 'United States Dollar'),
array('value'=>'UYU','label' => 'Uruguay Peso'),
array('value'=>'UZS','label' => 'Uzbekistan Som'),
array('value'=>'VEF','label' => 'Venezuela Bolivar'),
array('value'=>'VND','label' => 'Viet Nam Dong'),
array('value'=>'YER','label' => 'Yemen Rial'),
array('value'=>'ZWD','label' => 'Zimbabwe Dollar')
);
$count=count($allCurrrency);
/**
 * Save all currency in the airhotels_allcurrency table.
 */
for($i=0;$i<$count;$i++) {
$installer->getConnection()->insert($installer->getTable('airhotels_allcurrency'), $allCurrrency[$i]);
}

/**
 * To create new table `airhotels_managebankdetails`
 * to manage the bank details
 * 'id','country_code'
 * 'field_name','field_title','field_required'
 * 'currency_code'
 */
$installer->run ( "DROP TABLE IF EXISTS {$this->getTable('airhotels_managebankdetails')};
CREATE TABLE {$this->getTable('airhotels_managebankdetails')} (
`id` int(11) unsigned NOT NULL auto_increment,`country_code` text NOT NULL default '',`currency_code` text NOT NULL default '',
`field_name` varchar(255) NOT NULL default '',`field_title` varchar(255) NOT NULL default '',`field_required` int(11) unsigned NOT NULL,`created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8; ");

/**
 * Table structure for table `apptha_productsubscriptions`
 * to manage the subscription details
 * 'id','product_id'
 * 'subscription_type','is_subscription_only','price_per_iteration'
 * 'initial_fee','trial_period_price'
 * 'is_delete','created_date_time'
*/
$installer->run("DROP TABLE IF EXISTS {$this->getTable('apptha_productsubscriptions')};
CREATE TABLE {$this->getTable('apptha_productsubscriptions')} (
`id` int(11) unsigned NOT NULL AUTO_INCREMENT,`product_id` int(11) NOT NULL,`subscription_type` int(11) NOT NULL ,`is_subscription_only` int(11) NOT NULL ,`price_per_iteration` varchar(255)  NOT NULL ,`initial_fee` varchar(11) NOT NULL ,`trial_period_price` int(11)  NOT NULL ,`is_delete` int(11) NOT NUll,`created_date_time` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ");

/**
 * Table structure for table `apptha_subscriptiontypes`
 * to manage subscription type
 * 'id','engine_code','title'
 * 'status','billing_cycle'
 * 'is_initial_fee_enabled','occurrences_for_trialperiod'
 * 'billing_frequency','is_infinite'
*/
$installer->run("DROP TABLE IF EXISTS {$this->getTable('apptha_subscriptiontypes')};
CREATE TABLE {$this->getTable('apptha_subscriptiontypes')} (
`id` int(11) unsigned NOT NULL AUTO_INCREMENT,`engine_code` varchar(255) NOT NULL DEFAULT '',`title` varchar(255) NOT NULL ,`status` varchar(255) NOT NULL DEFAULT '',`billing_frequency` INT NOT NULL ,`billing_period_unit` varchar(255) NOT NULL ,`billing_cycle` varchar(255) NOT NULL DEFAULT 'Infinite' ,`is_infinite` varchar(255) NOT NULL ,`is_trial_enabled` varchar(255) NOT NULL ,`occurrences_for_trialperiod` INT NOT NULL ,`is_initial_fee_enabled` varchar(255) NOT NULL ,PRIMARY KEY (`id`)) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ");

/**
 * Table structure for table `apptha_managesubscriptions`
 * to manage subscription.
 * 'id',`is_subscription_only`,`product_id`
 * `product_name`,`start_date`
 * `sort_order`,`product_status`
*/
$installer->run("DROP TABLE IF EXISTS {$this->getTable('apptha_managesubscriptions')};
CREATE TABLE {$this->getTable('apptha_managesubscriptions')} (
`id` int(11) unsigned NOT NULL AUTO_INCREMENT,`is_subscription_only` int(11) NOT NULL ,`product_id` int(11) NOT NULL,
`product_name` varchar(255) NOT NULL,`start_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
`sort_order` int(11) NOT NULL,`product_status` int(11) NOT NULL,PRIMARY KEY (`id`)) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ");

/**
 * Creating the Verify Host table 
 * Manage verification details.
 * 'id','tag_id'
 * 'host_id','host_name','host_email'
 * 'file_path','host_tags'
 */
$tableName = $installer->getTable('airhotels/verifyhost');
if (!$installer->getConnection()->isTableExists($tableName)){
$table = $installer->getConnection()
->newTable($installer->getTable('airhotels/verifyhost'))
->addColumn('id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
'identity'  => true,
'unsigned'  => true,'nullable'  => false,'primary'   => true, ), 'Id')
->addColumn('tag_id', Varien_Db_Ddl_Table::TYPE_VARCHAR, null, array(
'nullable'  => false,), 'Tag Id')
->addColumn('host_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
'unsigned'  => true,
'nullable'  => false,), 'Host Id')
->addColumn('host_name', Varien_Db_Ddl_Table::TYPE_VARCHAR, null, array(
'nullable'  => false,), 'Host Name')
->addColumn('host_email', Varien_Db_Ddl_Table::TYPE_VARCHAR, null, array(
'nullable'  => false,), 
'Host Email')
->addColumn('file_path', Varien_Db_Ddl_Table::TYPE_VARCHAR, null, array(
'nullable'  => false,), 
'File Path')
->addColumn('host_tags', Varien_Db_Ddl_Table::TYPE_TINYINT, null, array(
'nullable'  => false,), 
'Verified');
$installer->getConnection()->createTable($table);
}

/**
 * Creating the tags verification table
 * Manage tag verifications
 * 'tag_id','tag_name','tag_description'
 */
$installer->startSetup();
$tableName = $installer->getTable('airhotels/tagsverification');
if (!$installer->getConnection()->isTableExists($tableName)){
$table = $installer->getConnection()
->newTable($installer->getTable('airhotels/tagsverification'))
->addColumn('tag_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
'identity'  => true,
'unsigned'  => true,
'nullable'  => false,
'primary'   => true,
), 'Tag Id')
->addColumn('tag_name', Varien_Db_Ddl_Table::TYPE_VARCHAR, null, array(
'nullable'  => false,
), 'Tag Name')
->addColumn('tag_description', Varien_Db_Ddl_Table::TYPE_VARCHAR, null, array(
'nullable'  => false,
), ' Tag Description')
->addColumn('direct_url', Varien_Db_Ddl_Table::TYPE_TINYINT, null, array(
'nullable'  => true,
'default' => null,
), 'Allow Direct Url');
$installer->getConnection()->createTable($table);
}

/**
 * Table for "airhotels_requestproperty"
 * Manage request property
 * 'id','product_id','fromdate'
 * 'todate','host_id','accomodates','customer_name',
 * 'customer_email','subtotal','qty'
 * 'status','message','date_time'
 */

$tableName = $installer->getTable('airhotels/requestproperty');
if (!$installer->getConnection()->isTableExists($tableName)){
$table = $installer->getConnection()
->newTable($installer->getTable('airhotels/requestproperty'))
->addColumn('id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array('identity'  => true,'unsigned'  => true,'nullable'  => false,'primary'   => true,), 'Id')
->addColumn('product_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array('unsigned'  => true,'nullable'  => false,), 'Product Id')
->addColumn('fromdate', Varien_Db_Ddl_Table::TYPE_DATE, null, array('nullable'  => false,), 'From Date')
->addColumn('todate', Varien_Db_Ddl_Table::TYPE_DATE, null, array('nullable'  => false,), 'To Date')
->addColumn('host_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array('unsigned'  => true,'nullable'  => false,), 'Host Id')
->addColumn('accomodates', Varien_Db_Ddl_Table::TYPE_VARCHAR, null, array('nullable'  => false,), 'Accomodates')
->addColumn('customer_name', Varien_Db_Ddl_Table::TYPE_VARCHAR, null, array('nullable'  => false,), 'Customer Name')
->addColumn('customer_email', Varien_Db_Ddl_Table::TYPE_VARCHAR, null, array('nullable'  => false,), 'Customer Email')
->addColumn('subtotal', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array('unsigned'  => true,'nullable'  => false,), 'Subtotal')
->addColumn('qty', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array('unsigned'  => true,'nullable'  => false,), 'Quantity')
->addColumn('status', Varien_Db_Ddl_Table::TYPE_VARCHAR, null, array('nullable'  => false,), 'Status')
->addColumn('message', Varien_Db_Ddl_Table::TYPE_VARCHAR, null, array('nullable'  => false,), 'Message')
->addColumn('date_time', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array('nullable'  => false,), 'Date & Time');
$installer->getConnection()->createTable($table);
}

/**
 * Create product attribute 'video_url'
 * input - text
 * type - varchar
 *  Group:property Information,
 * Visible:True,
 * searchable:true,
 * unique:false
 * required:false
 */
if (!$installer->getAttributeId(Mage_Catalog_Model_Product::ENTITY, 'video_url')) {
$installer->addAttribute('catalog_product', 'video_url', array(
'group' => 'Property Information','label' => 'Video URL',
'type' => 'varchar','input' => 'text',
'default' => '','backend' => '',
'frontend' => '','source' => '',
'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
'visible' => true,'required' => false,
'user_defined' => false,'searchable' => true,
'filterable' => false,'comparable' => false,
'visible_on_front' => true,'visible_in_advanced_search' => false,
'unique' => false,'apply_to' => 'property'
));
}

/**
 * Create catalog_product attribute "video_type"
 * input - select
 * type - varchar
 *  Group:property Information,
 * Visible:True,
 * searchable:true,
 * unique:false
 * required:false
 */
if (!$installer->getAttributeId(Mage_Catalog_Model_Product::ENTITY, 'video_type')) {
$installer->addAttribute('catalog_product', 'video_type', array('group' => 'Property Information',
'label' => 'Video Type',
'type' => 'varchar',
'input' => 'select',
'default' => '',
'class' => '',
'backend' => 'eav/entity_attribute_backend_array',
'frontend' => '',
'source' => '',
'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
'visible' => true,
'required' => false,
'user_defined' => false,'searchable' => true,
'filterable' => false,'comparable' => false,
'visible_on_front' => true,
'option' => array(
'value' => array('Youtube' => array(0 => 'Youtube'), 'Vimeo' => array(0 => 'Vimeo')),
'order' => array('Youtube' => '0', 'Vimeo' => '1')
),
'visible_in_advanced_search' => true,'unique' => false,
'apply_to' => 'property'
));
}

/**
 * Add the customer Photo table fields.
 */
$installer->run("
ALTER TABLE  {$this->getTable('airhotels_customer_photo')} ADD `name` varchar(255) NOT NULL DEFAULT '';
ALTER TABLE  {$this->getTable('airhotels_customer_photo')} ADD `email_id` varchar(255) NOT NULL DEFAULT '';
ALTER TABLE  {$this->getTable('airhotels_customer_photo')} ADD `contact_number` varchar(255) NOT NULL DEFAULT '';
ALTER TABLE  {$this->getTable('airhotels_customer_photo')} ADD `video_url` text NOT NULL;
ALTER TABLE  {$this->getTable('airhotels_customer_photo')} ADD `city` varchar(255) NOT NULL DEFAULT '';
ALTER TABLE  {$this->getTable('airhotels_customer_photo')} ADD `country` varchar(255) NOT NULL DEFAULT '';
ALTER TABLE  {$this->getTable('airhotels_customer_photo')} ADD `notification` tinyint(4) NOT NULL;
ALTER TABLE  {$this->getTable('airhotels_customer_photo')} ADD `document_url` varchar(255) NOT NULL DEFAULT '';
ALTER TABLE  {$this->getTable('airhotels_customer_photo')} ADD `time_zone` varchar(250) NOT NULL;
ALTER TABLE  {$this->getTable('airhotels_customer_photo')} ADD `document_verified` tinyint(4) NOT NULL;
ALTER TABLE  {$this->getTable('airhotels_customer_photo')} ADD `video_verified` tinyint(4) NOT NULL;
ALTER TABLE  {$this->getTable('airhotels_customer_photo')} ADD `paypal_id` varchar(250) NOT NULL;
ALTER TABLE  {$this->getTable('airhotels_customer_photo')} ADD `bank_id` varchar(250) NOT NULL;
ALTER TABLE  {$this->getTable('airhotels_customer_photo')} ADD  `account_type` varchar(100) DEFAULT NULL;
ALTER TABLE  {$this->getTable('airhotels_customer_photo')} ADD  `account_number` varchar(250) DEFAULT NULL;
ALTER TABLE  {$this->getTable('airhotels_customer_photo')} ADD  `ifsc_code` varchar(250) DEFAULT NULL;
ALTER TABLE  {$this->getTable('airhotels_customer_photo')} ADD  `branch_name` TEXT DEFAULT NULL;
ALTER TABLE  {$this->getTable('airhotels_customer_photo')} ADD  `paypal_email` varchar(250) DEFAULT NULL;
ALTER TABLE  {$this->getTable('airhotels_customer_photo')} ADD  `bank_details` text DEFAULT NULL;
ALTER TABLE  {$this->getTable('airhotels_customer_photo')} ADD  `mobile_verified_profile` varchar(250) DEFAULT NULL;
ALTER TABLE  {$this->getTable('airhotels_customer_photo')} ADD  `mobile_verified_payment` varchar(250) DEFAULT NULL;
ALTER TABLE  {$this->getTable('airhotels_customer_photo')} ADD  `school` varchar(100) DEFAULT NULL;
ALTER TABLE  {$this->getTable('airhotels_customer_photo')} ADD  `work` varchar(250) DEFAULT NULL;
ALTER TABLE  {$this->getTable('airhotels_customer_photo')} ADD  `emergency_contact` varchar(250) DEFAULT NULL;
ALTER TABLE  {$this->getTable('airhotels_customer_photo')} ADD  `language` TEXT DEFAULT NULL;
");

/**
 * Add the customer Inbox tale fields.
 */
$installer->run("
ALTER TABLE  {$this->getTable('airhotels_customer_inbox')} ADD  `start_at` varchar(100) DEFAULT NULL;
ALTER TABLE  {$this->getTable('airhotels_customer_inbox')} ADD  `end_at` varchar(250) DEFAULT NULL;
");

/**
 * Table structure for table `security`
 * To manage security data
 * Group:property Information,
 * Visible:True,
 * searchable:true,
 * unique:false
 * Required:false
 */
if (!$installer->getAttributeId(Mage_Catalog_Model_Product::ENTITY, 'security')) {
$installer->addAttribute('catalog_product', 'security', array(
'group' => 'Property Information','label' => 'Security Deposit',
'type' => 'varchar','input' => 'multiselect', 'default' => '',
'class' => '', 'backend' => 'eav/entity_attribute_backend_array',
'frontend' => '', 'source' => '',
'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE, 'visible' => true,
'required' => false,'user_defined' => false,'searchable' => true,'filterable' => false,'comparable' => false,'visible_on_front' => true,
'option' => array(
'value' => array('Security' => array(0 => 'Security',), 'Cleaning' => array(0 => 'Cleaning')),
'order' => array('Security' => '0', 'Cleaning' => '1')
),
'unique' => false,'visible_in_advanced_search' => true,
'apply_to' => 'property',
));
}

/**
 * updating the attribute Security
 */

if ($installer->getAttributeId(Mage_Catalog_Model_Product::ENTITY, 'security')) {

$installer->updateAttribute('catalog_product', 'security', 'is_global',
 Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE);
$installer->updateAttribute('catalog_product', 'security', 'apply_to', 'property');
}

$installer->getConnection()->addColumn($this->getTable('sales/quote_address'), 'security_fee_amount', 'DECIMAL( 10, 2 ) NOT NULL');
$installer->getConnection()->addColumn($this->getTable('sales/quote_address'), 'security_base_fee_amount', 'DECIMAL( 10, 2 ) NOT NULL');
$installer->getConnection()->addColumn($this->getTable('sales/order'), 'security_fee_amount', 'DECIMAL( 10, 2 ) NOT NULL');
$installer->getConnection()->addColumn($this->getTable('sales/order'), 'security_base_fee_amount', 'DECIMAL( 10, 2 ) NOT NULL');
$installer->getConnection()->addColumn($this->getTable('sales/quote_address'), 'cleaning_fee_amount', 'DECIMAL( 10, 2 ) NOT NULL');
$installer->getConnection()->addColumn($this->getTable('sales/quote_address'), 'cleaning_fee_amount', 'DECIMAL( 10, 2 ) NOT NULL');
$installer->getConnection()->addColumn($this->getTable('sales/order'), 'cleaning_fee_amount', 'DECIMAL( 10, 2 ) NOT NULL');
$installer->getConnection()->addColumn($this->getTable('sales/order'), 'cleaning_fee_amount', 'DECIMAL( 10, 2 ) NOT NULL');
$installer->endSetup();
/**
 * Table structure for table `amenity`
 * To manage amenity data
 * Group:property Information,
 * Visible:True,
 * searchable:true,
 * unique:false
 * Required:false
 */
if (!$installer->getAttributeId(Mage_Catalog_Model_Product::ENTITY, 'security')) {
$installer->addAttribute('catalog_product', 'security', array(
'group' => 'Property Information','label' => 'Security Deposit',
'type' => 'varchar',
'input' => 'multiselect', 'default' => '',
'class' => '', 'backend' => 'eav/entity_attribute_backend_array',
'frontend' => '', 'source' => '',
'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE, 'visible' => true,
'required' => false,
'user_defined' => false,'searchable' => true,
'filterable' => false,
'comparable' => false,'visible_on_front' => true,
'option' => array(
'value' => array('Security' => array(0 => 'Security',), 'Cleaning' => array(0 => 'Cleaning')),
'order' => array('Security' => '0', 'Cleaning' => '1')
),
'visible_in_advanced_search' => true,
'unique' => false,
'apply_to' => 'property',
));
}

/**
 * Create a catalog_product attribute 'people_min'
 * input - text
 * type - varchar
 *  Group:property Information,
 * Visible:True,
 * searchable:true,
 * unique:false
 */
if (!$installer->getAttributeId(Mage_Catalog_Model_Product::ENTITY, 'people_min')) {
 $installer->addAttribute('catalog_product', 'people_min', array(
   'group' => 'Property Information',
   'label' => 'Min people(s)',
   'type' => 'int',
   'input' => 'text','default' => '1','class' => 'validate-digits', 'backend' => '',  'frontend' => '',
   'source' => '',
   'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
   'visible' => true, 'required' => false,'user_defined' => false,
   'searchable' => true,
   'filterable' => false,
   'comparable' => false,
   'visible_on_front' => true, 'visible_in_advanced_search' => false, 'unique' => false,
   'apply_to' => 'property',
 ));
}
/**
 * Create a catalog_product attribute 'people_max'
 * input - text
 * type - varchar
 *  Group:property Information,
 * Visible:True,
 * searchable:true,
 * unique:false
 */
if (!$installer->getAttributeId(Mage_Catalog_Model_Product::ENTITY, 'people_max')) {
 $installer->addAttribute('catalog_product', 'people_max', array(
   'group' => 'Property Information',
   'label' => 'Max people(s)',
   'type' => 'int','input' => 'text','default' => '10','class' => 'validate-digits',
   'backend' => '','frontend' => '','source' => '',
   'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
   'visible' => true,'required' => false,'user_defined' => false,
   'searchable' => true,'filterable' => false,
   'comparable' => false,
   'visible_on_front' => true,
   'visible_in_advanced_search' => false,
   'unique' => false,
   'apply_to' => 'property',
 ));
}
/**
 * updating the attribute amenity
 */

if ($installer->getAttributeId(Mage_Catalog_Model_Product::ENTITY, 'security')) {
$installer->updateAttribute('catalog_product', 'security', 'is_global', Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE);
$installer->updateAttribute('catalog_product', 'security', 'apply_to', 'property');
}

if ($installer->getConnection()->isTableExists($tableName)){
$installer->run("
ALTER TABLE  {$this->getTable('airhotels_verify_host')} ADD  `country_code` varchar(50) NOT NULL;
ALTER TABLE  {$this->getTable('airhotels_verify_host')} ADD  `id_type` int(50) DEFAULT NULL;
");
}

$vCustomerEntityType = $installer->getEntityTypeId('customer');
$vCustAttributeSetId = $installer->getDefaultAttributeSetId($vCustomerEntityType);
$vCustAttributeGroupId = $installer->getDefaultAttributeGroupId($vCustomerEntityType, $vCustAttributeSetId);
$installer->addAttribute('customer', 'id_type', array(
        'label' => 'ID Type',
        'input' => 'select',
        'type'  => 'varchar',
        'source' => 'airhotels/entity_attribute_source_table',
        'forms' => array('customer_account_edit','customer_account_create','adminhtml_customer'),
        'required' => 0,
        'user_defined' => 0

));
$installer->addAttributeToGroup($vCustomerEntityType, $vCustAttributeSetId, $vCustAttributeGroupId, 'id_type', 0);
$oAttribute = Mage::getSingleton('eav/config')->getAttribute('customer', 'id_type');
$oAttribute->setData('used_in_forms', array('customer_account_edit','customer_account_create','adminhtml_customer'));
$oAttribute->save();

/**
 * unset installer setup.
 */
$installer->endSetup();