<?php

/*
 * ********************************************************* */

/**
 * @name          : Apptha Paypal Adaptive
 * @version       : 1.0
 * @package       : Apptha
 * @since         : Magento 1.5
 * @subpackage    : Paypal Adaptive
 * @author        : Apptha - http://www.apptha.com
 * @copyright     : Copyright (C) 2015 Powered by Apptha
 * @license       : GNU/GPL http://www.gnu.org/licenses/gpl-3.0.html
 * @abstract      : Admin Controller File
 * @Creation Date : January 11,2014
 * @Modified By   : Ramkumar M
 * @Modified Date : January 11,2014
 * */
/*
 * ********************************************************* */

class Apptha_Paypaladaptive_Adminhtml_PaymentdetailsController extends Mage_Adminhtml_Controller_Action {

    protected function _initAction() {
        $this->loadLayout()
                ->_setActiveMenu('paypaladaptive/paymentdetails')
                ->_addBreadcrumb(Mage::helper('adminhtml')->__('Paypal Adaptive Payment'), Mage::helper('adminhtml')->__('Paypal Adaptive Payment'));
        
         $this->getLayout()->getBlock('head')->setTitle($this->__('Paypal Adaptive Payment Details'));
         
        return $this;
    }

    public function indexAction() {

        $this->_initAction()
                ->renderLayout();
    }
    
    /**
     * Setting for acl
     */
    protected function _isAllowed(){
    	return true;
    }

}

