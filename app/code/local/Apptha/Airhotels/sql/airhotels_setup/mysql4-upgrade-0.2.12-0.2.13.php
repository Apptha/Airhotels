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
 * To create new table `bed_rooms` 
 * for bed_rooms 
 * along with six product types which is available by default
 * Group:property Information,
 * Visible:True,
 * searchable:true,
 * unique:false
 * Required:true
 */
if (!$installer->getAttributeId(Mage_Catalog_Model_Product::ENTITY, 'bed_rooms')) {
    $installer->addAttribute('catalog_product', 'bed_rooms', array(
        'group' => 'Property Information', 'label' => 'Bed Room(s)',
        'type' => 'varchar','input' => 'select','default' => '',  'class' => '','backend' => 'eav/entity_attribute_backend_array',
        'frontend' => '',   'source' => '',
        'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
        'visible' => true,'required' => true,  'user_defined' => false,
        'searchable' => true,  'filterable' => false,
        'comparable' => false,   'visible_on_front' => true,
        'option' => array( 'value' => array('One' => array(0 => 'One'), 'Two' => array(0 => 'Two'), 'Three' => array(0 => 'Three')),
            'order' => array('One' => '0', 'Two' => '1', 'Three' => '2')
        ),
        'visible_in_advanced_search' => true,'unique' => false,
        'apply_to' => 'property'
    ));
}

/**
 * To create new table `bed_type`
 * for bed_type
 * along with six product types which is available by default
 * Group:property Information,
 * Visible:True,
 * searchable:true,
 * unique:false
 * Required:true
 */
if (!$installer->getAttributeId(Mage_Catalog_Model_Product::ENTITY, 'bed_type')) {
$installer->addAttribute('catalog_product', 'bed_type', array(
'group' => 'Property Information', 'label' => 'Bed Type',
'type' => 'varchar',
'input' => 'select',
'default' => '',  'class' => '',
'backend' => 'eav/entity_attribute_backend_array',
'frontend' => '',   'source' => '',
'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
'visible' => true,'required' => true,  'user_defined' => false,
'searchable' => true,  'filterable' => false,
'comparable' => false,   'visible_on_front' => true,
'option' => array( 'value' => array('Cushion' => array(0 => 'Cushion'), 'Frames' => array(0 => 'Frames'), 'Futon bed' => array(0 => 'Futon bed')),
            'order' => array('Cushion' => '0', 'Frames' => '1', 'Futon bed' => '2')
        ),
'visible_in_advanced_search' => true,
'unique' => false,
'apply_to' => 'property'
));
}

/**
 * Array for cms page
 * 
 */
$cms_page = array('help' =>'Help' ,'refund-policy' => 'Refund Policy','terms-and-conditions' => 'Terms and Conditions' ,'frequently-asked-questions'=>'Frequently Asked Questions',
'personal-information' =>'Personal Information','contact-us'=>'Contact Us','partners'=>'Partners','careers'=>'Career'
);

/**
 * Create CMS Pages for footer links.
 * Help page
 * Refund policy
 * Terms and condition
 * Personal Information
 * Contact Us
 * Partners 
 * Carrers
 */
foreach($cms_page as $key =>$value){
$pageId = Mage::getModel('cms/page')->checkIdentifier($key, 0);
if (empty($pageId)) {
$content = 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.';
$block = Mage::getModel('cms/page');
$block->setTitle($value);
$block->setIdentifier($key);
$block->setStores(array(0));
$block->setIsActive(1);
$block->setContent($content);
$block->setRootTemplate('one_column');
$block->save();
}
}

/**
 * Footer links block.
 * 
 * Set footer content.
 * 
 * Set page title.
 * 
 */
$cmsBlock = Mage::getModel('cms/block')->load('footer_links', 'identifier');
$cmsBlock->setStores(array(0))
->setIsActive(true)
->setTitle('The title of your block');

$cmsBlock->setContent('<div class="company_section">
<h4>SHOPPING WITH US</h4>
<ul>
<li><a title="Privacy Policy" href="{{store direct_url="privacy-policy-cookie-restriction-mode"}}">Privacy Policy</a></li>
<li><a title="Refund policy" href="{{store direct_url="refund-policy"}}">Refund policy</a></li>
<li><a title="Terms and Conditions" href="{{store direct_url="terms-and-conditions"}}">Terms and Conditions</a></li>
<li><a title="Frequently Asked Questions" href="{{store direct_url="frequently-asked-questions"}}">Frequently Asked Questions</a></li>
</ul>
</div>
<div class="discover_section">
<h4>YOUR ACCOUNT</h4>
<ul>
<li><a title="Personal Information" href="{{store direct_url="personal-information"}}">Personal Information<br /></a></li>
</ul>
</div>
<div class="hosting_section">
<h4>ABOUT US</h4>
<ul>
<li><a title="About Us" href="{{store direct_url="about-airhotel-demo-store"}}">About Us</a></li>
<li><a title="Contact Us" href="{{store direct_url="contact-us"}}">Contact Us</a></li>
<li><a title="Customer Service" href="{{store direct_url="customer-service"}}">Customer Service</a></li>
<li><a title="Partners" href="{{store direct_url="partners"}}">Partners</a></li>
<li><a title="Careers" href="{{store direct_url="careers"}}">Careers</a></li>
</ul>
</div>
<div id="spoon-plugin-kncgbdglledmjmpnikebkagnchfdehbm-2" style="display: none;">&nbsp;</div>')
->save();

/**
 * Footer link block
 * Twitter link
 * Facebook link
 * Google link
 */
$cmsBlock = Mage::getModel('cms/block');
$cmsBlock->setIdentifier('add-blocks')
->setStores(array(0))
->setIsActive(true)
->setTitle('Add Blocks');

$cmsBlock->setContent('<p><span>Join Us On</span></p>
<div class="social-media"><a class="tw_icon sprimg" title="Twitter" href="https://twitter.com/" target="blank_">&nbsp;</a><a class="fb_icon sprimg" title="Facebook" href="http://www.facebook.com" target="blank_">&nbsp;</a><a class="gplus_icon sprimg" title="Googleplus" href="https://plus.google.com/" target="blank_">&nbsp;</a><a class="in_icon sprimg" title="Linkedin" href="https://in.linkedin.com/nhome/" target="blank_">&nbsp;</a></div>')
->save();

/**
 * Create a Pucbish block page
 * 
 * Set page title.
 * 
 */
$cmsBlock = Mage::getModel('cms/block');
$cmsBlock->setIdentifier('publish_content')
->setStores(array(0))
->setIsActive(true)
->setTitle('Publish');
/**
 * Set content value.
 */
$cmsBlock->setContent('Once you click on publish button, your listing will be published in the website.')
->save();

/**
 * unset installer setup.
 */
$installer->endSetup();