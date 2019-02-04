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
class Apptha_Onestepcheckout_Block_Onestepcheckout extends Mage_Checkout_Block_Onepage_Abstract {
//get default country and set estimate rates
    public function _construct() {
        parent::_construct();

        $rates = $this->getEstimateRates();

        $defaut_country = Mage::getStoreConfig('onestepcheckout/general/default_country_id');
        if (!$defaut_country) {
            $defaut_country = 'US';
        }

        //$this->getQuote()->getShippingAddress()->setCountryId($defaut_country)->setCollectShippingRates(true)->save();

	}
//get all shipping rates
    public function getEstimateRates() {
        if (empty($this->_rates)) {
            $groups = $this->getQuote()->getShippingAddress()->getGroupedAllShippingRates();
            $this->_rates = $groups;
        }
        return $this->_rates;
    }

    public function _prepareLayout() {

        $title = Mage::getStoreConfig('onestepcheckout/general/checkout_title');
        if ($title) {
            $checkout_title = $title;
        } else {
            $checkout_title = "Onestep Checkout";
        }
        $this->getLayout()->getBlock('head')->setTitle($checkout_title);
        return parent::_prepareLayout();
    }
    //get shipping methods
	public function shippingmethods($shipping,$methods)
	{
	if(($shipping)&&($methods))
	{
		return true;
	}

	}
//getting steps based on the product
    public function getSteps() {
        $steps = array();

       //steps for virtual product
        if ($this->getOnepage()->getQuote()->isVirtual())
        {
            $stepCodes = array('billing', 'payment', 'review');
        }
        //steps for other product
        else
        {
            $stepCodes = array('billing', 'shipping', 'shipping_method', 'payment', 'review');
        }

        foreach ($stepCodes as $step)
        {

            $steps[$step] = $this->getCheckout()->getStepData($step);
        }

        return $steps;
    }

//check the active step
    public function getActiveStep()
    {
        return $this->isCustomerLoggedIn() ? 'billing' : 'login';
    }

    public function getOnepage()
    {
        return Mage::getSingleton('checkout/type_onepage');
    }

    //get product is virtual product  or not
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

}