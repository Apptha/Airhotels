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
 * @package     Apptha_Onestepcheckout
 * @version     0.1.9
 * @author      Apptha Team <developers@contus.in>
 * @copyright   Copyright (c) 2014 Apptha. (http://www.apptha.com)
 * @license     http://www.apptha.com/LICENSE.txt
 *
 * */
class Apptha_Onestepcheckout_Helper_Checkout extends Mage_Core_Helper_Abstract {

    public $methods = array();

    public function savePayment($data)
    {
        if (empty($data)) {
            return array('error' => -1, 'message' => Mage::helper('checkout')->__('Invalid data'));
        }
        if ($this->getOnepage()->getQuote()->isVirtual()) {
            $this->getOnepage()->getQuote()->getBillingAddress()->setPaymentMethod(isset($data['method']) ? $data['method'] : null);
        } else {
            $this->getOnepage()->getQuote()->getShippingAddress()->setPaymentMethod(isset($data['method']) ? $data['method'] : null);
        }

        $payment = $this->getOnepage()->getQuote()->getPayment();
        $payment->importData($data);

        $this->getOnepage()->getQuote()->save();

        return array();
    }

    public function saveShippingMethod($shippingMethod)
    {
        if (empty($shippingMethod)) {
            $res = array(
                'error' => -1,
                'message' => Mage::helper('checkout')->__('Invalid shipping method.')
            );
            return $res;
        }
        $rate = $this->getOnepage()->getQuote()->getShippingAddress()->getShippingRateByCode($shippingMethod);
        if (!$rate) {
            $res = array(
                'error' => -1,
                'message' => Mage::helper('checkout')->__('Invalid shipping method.')
            );
            return $res;
        }
        $this->getOnepage()->getQuote()->getShippingAddress()->setShippingMethod($shippingMethod);

        return array();
    }

    public function saveShipping($data, $customerAddressId)
    {
        if (empty($data)) {
            $res = array(
                'error' => -1,
                'message' => Mage::helper('checkout')->__('Invalid data')
            );
            return $res;
        }
        $address = $this->getOnepage()->getQuote()->getShippingAddress();

        if (!empty($customerAddressId)) {
            $customerAddress = Mage::getModel('customer/address')->load($customerAddressId);
            if ($customerAddress->getId()) {
                if ($customerAddress->getCustomerId() != $this->getOnepage()->getQuote()->getCustomerId()) {
                    return array('error' => 1,
                        'message' => Mage::helper('checkout')->__('Customer Address is not valid.')
                    );
                }
                $address->importCustomerAddress($customerAddress);
            }
        } else {
            unset($data['address_id']);
            $address->addData($data);
        }

        $address->implodeStreetAddress();
        $address->setCollectShippingRates(true);

        if (($validateRes = $address->validate())!==true) {
            $res = array(
                'error' => 1,
                'message' => $validateRes
            );
            return $res;
        }

        $this->getOnepage()->getQuote()
        //->collectTotals()
        ->save();

        return array();
    }

    function __construct()
    {
        $this->settings = $this->loadSettings();
    }
//get the Onestepcheckout settings
    public function loadSettings()
    {
        $settings = array();
        $items = array();
        $items = Mage::getStoreConfig('onestepcheckout');
        foreach ($items as $config) {
            foreach ($config as $key => $value) {
                $settings[$key] = $value;
            }
        }
         if(empty($settings['default_country_id']))
        {
            $settings['default_country_id'] = 'US';
        }
        return $settings;
    }
    
	


//check the exluded fields and assign - to that values
    public function load_exclude_data(&$data) {
        if ($this->settings['display_city'])
        {
            $data['city'] = '-';
        }
        if ($this->settings['display_country'])
        {
        if($this->settings['enable_geoip'] == 1  && file_exists('Net/GeoIP.php'))
                {
                    if(Mage::helper('customer')->isLoggedIn() == 1)
                    {
                        $customerAddressId = Mage::getSingleton('customer/session')->getCustomer()->getDefaultBilling();
                        if ($customerAddressId){
                            $address = Mage::getModel('customer/address')->load($customerAddressId);
                            $countryId = $address['country_id'];
                        }
                        else{
                            $countryId = $this->getGeoIp()->countryCode;
                        }
                    }
                    else
                    {
                        $countryId = $this->getGeoIp()->countryCode;
                    }
                }
        else
                {
                    if(Mage::helper('customer')->isLoggedIn() == 1)
                    {
                        $customerAddressId = Mage::getSingleton('customer/session')->getCustomer()->getDefaultBilling();
                        if ($customerAddressId){
                            $address = Mage::getModel('customer/address')->load($customerAddressId);
                            $countryId = $address['country_id'];
                        }
                        else{
                            $countryId = $this->settings['default_country_id'];
                        }

                    }
                    else
                    {
                        $countryId = $this->settings['default_country_id'];
                    }
                }
        }
        if ($this->settings['display_telephone'])
        {
            $data['telephone'] = '-';
        }
        if ($this->settings['display_state'])
        {
            $data['region'] = '-';
            $data['region_id'] = '1';
        }
        if ($this->settings['display_zipcode'])
        {
            $data['postcode'] = '-';
        }
        if ($this->settings['display_company'])
        {
            $data['company'] = '-';
        }
        if ($this->settings['display_fax'])
        {
            $data['fax'] = '-';
        }
        if ($this->settings['display_address'])
        {
            $data['street'][] = '-';
        }
        return $data;
    }
    
     //check the exclude fields and assign - to that values when ajax updates trigger

	public function load_add_data($data)
    {
    	
        if (isset($data['city']))
        {
            //$data['city'] = '-';
        	if($this->settings['enable_geoip'] == 1  && file_exists('Net/GeoIP.php'))
    		{
        		$data['city'] = $this->getGeoIp()->city;
    		}
    		else
    		{
        		 $data['city'] = '-';
    		}
        }
        if (empty($data['country_id']))
        {
        	if($this->settings['enable_geoip'] == 1 && file_exists('Net/GeoIP.php'))
                {
                    if(Mage::helper('customer')->isLoggedIn() == 1)
                    {
                        $customerAddressId = Mage::getSingleton('customer/session')->getCustomer()->getDefaultBilling();
                        if ($customerAddressId){
                            $address = Mage::getModel('customer/address')->load($customerAddressId);
                            $countryId = $address['country_id'];
                        }
                        else{
                            $countryId = $this->getGeoIp()->countryCode;
                        }
                    }
                    else
                    {
                        $countryId = $this->getGeoIp()->countryCode;
                    }
                }
        else
                {
                    if(Mage::helper('customer')->isLoggedIn() == 1)
                    {
                        $customerAddressId = Mage::getSingleton('customer/session')->getCustomer()->getDefaultBilling();
                        if ($customerAddressId){
                            $address = Mage::getModel('customer/address')->load($customerAddressId);
                            $countryId = $address['country_id'];
                        }
                        else{
                            $countryId = $this->settings['default_country_id'];
                        }

                    }
                    else
                    {
                        $countryId = $this->settings['default_country_id'];
                    }
                }
        }
        if (empty($data['telephone']))
        {
            $data['telephone'] = '-';
        }
        if (empty($data['region_id']))
        {
            $data['region_id'] = '-';
            $data['region'] = '-';
        }
        if (empty($data['postcode']))
        {
            $data['postcode'] = '-';
        }
        if (empty($data['company']))
        {
            $data['company'] = '-';
        }
        if (empty($data['fax']))
        {
            $data['fax'] = '-';
        }
        if (empty($data['street'][0]))
        {
            $data['street'][0] = '-';
        }
        return $data;
    }
	public function getGeoIp()
    {
		$enableGeoIp = Mage::getStoreConfig('onestepcheckout/general/enable_geoip');
		$database = Mage::getStoreConfig('onestepcheckout/general/geoip_database');
		
		try {
				require_once('Net/GeoIP.php');
			    $ipaddress = Mage::helper('core/http')->getRemoteAddr();
			    $geoip = Net_GeoIP::getInstance($database);
			    $location = $geoip->lookupLocation($ipaddress);
                $strCountryCode = $this->settings['default_country_id'];
				$country = Mage::getStoreConfig('onestepcheckout/general/default_country_id');
			    /**
			     * IF PEAR NET_GEOIP IS INSTALLED AND PHP CAN ACCESS THIS THEN YOU WILL SEE YOUR COUNTRY CODE DETECTED IF NOT THEN YOU SEE ERRORS INSTEAD
			     */
			    if($enableGeoIp == 1  && file_exists('Net/GeoIP.php'))
			    {
			    	$countrycode = $location->countryCode;
			    	$citycode = $location->city;
			    	return $location;
			    }
			}
			catch (Exception $e) {
		    	return $e->getMessage();
			}
    }
    
     public function getOnepage()
    {
        return Mage::getSingleton('checkout/type_onepage');
    }

    public function getVirtual()
    {
        if ($this->getOnepage()->getQuote()->isVirtual())
        {
            return true;
        }
        else
        {
            return false;
        }
    }

     public function IscustomerEmailExists($email) {

        $websiteId = null;
        $websiteId = Mage::app()->getWebsite()->getId();
        $customer = Mage::getModel('customer/customer');
        if ($websiteId) {
            $customer->setWebsiteId($websiteId);
        }
        $customer->loadByEmail($email);
        if ($customer->getId()) {
            return $customer->getId();
        }
        return false;
    }

    
}