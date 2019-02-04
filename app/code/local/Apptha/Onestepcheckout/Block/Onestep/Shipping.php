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
class Apptha_Onestepcheckout_Block_Onestep_Shipping extends Mage_Checkout_Block_Onepage_Shipping
{
    /**
     * Sales Qoute Shipping Address instance
     *
     * @var Mage_Sales_Model_Quote_Address
     */
    protected $_address = null;

    /**
     * Initialize shipping address step
     */
    protected function _construct()
    {
        $this->getCheckout()->setStepData('shipping', array(
            'label'     => Mage::helper('checkout')->__('Shipping Information'),
            'is_show'   => $this->isShow()
        ));
        $this->settings = Mage::helper('onestepcheckout/checkout')->loadSettings();
        parent::_construct();
    }

    /**
     * Return checkout method
     *
     * @return string
     */
    public function getMethod()
    {
        return $this->getQuote()->getCheckoutMethod();
    }

    /**
     * Return Sales Quote Address model (shipping address)
     *
     * @return Mage_Sales_Model_Quote_Address
     */
    public function getAddress()
    {
        if (is_null($this->_address)) {
            if ($this->isCustomerLoggedIn()) {
                $this->_address = $this->getQuote()->getShippingAddress();
            } else {
                $this->_address = Mage::getModel('sales/quote_address');
            }
        }

        return $this->_address;
    }

    /**
     * Retrieve is allow and show block
     *
     * @return bool
     */
    public function isShow()
    {
        return !$this->getQuote()->isVirtual();
    }

    public function getCountryHtmlSelect($type)
    {
         $helper = Mage::helper('onestepcheckout/checkout');


       if(isset($this->settings['default_country_id']))
    	{
            if($this->settings['enable_geoip'] == 1 && file_exists('Net/GeoIP.php'))
            {
                if(Mage::helper('customer')->isLoggedIn() == 1)
                {
                        $customerAddressId = Mage::getSingleton('customer/session')->getCustomer()->getDefaultShipping();
                        $helper = Mage::helper('onestepcheckout/checkout');
                        if ($customerAddressId){
                            $address = Mage::getModel('customer/address')->load($customerAddressId);
                            $countryId = $address['country_id'];
                        }
                        else{
                            $countryId = $helper->getGeoIp()->countryCode;
                        }

                }
                else
                {
                        $countryId = $helper->getGeoIp()->countryCode;
                }
           }
         else
           {
                    if(Mage::helper('customer')->isLoggedIn() == 1)
                    {
                        $customerAddressId = Mage::getSingleton('customer/session')->getCustomer()->getDefaultShipping();
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
        if (is_null($countryId)) {
            $countryId = $this->settings['default_country_id'];
        }
        $select = $this->getLayout()->createBlock('core/html_select')
            ->setName($type.'[country_id]')
            ->setId($type.':country_id')
            ->setTitle(Mage::helper('checkout')->__('Country'))
            ->setClass('validate-select')
            ->setValue($countryId)
            ->setOptions($this->getCountryOptions());
        if ($type === 'shipping') {
            $select->setExtraParams('onchange="shipping.setSameAsBilling(false);"');
        }

        return $select->getHtml();
    }
}
