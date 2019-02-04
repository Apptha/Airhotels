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
 * @package     Apptha_Sociallogin
 * @version     0.1.8
 * @author      Apptha Team <developers@contus.in>
 * @copyright   Copyright (c) 2014 Apptha. (http://www.apptha.com)
 * @license     http://www.apptha.com/LICENSE.txt
 *
 * */

/**
 * The observer file validate the captcha when captcha is entered
 */
class Apptha_Sociallogin_Model_Observer extends Mage_Core_Model_Abstract {
    
    /**
     * Captcha validation for create account form
     *
     * @return string $message for validation failed if any
     */
    public function checkCaptcha($observer) {
        $formId = 'Apptha_Sociallogin';
        $captchaModel = Mage::helper ( 'captcha' )->getCaptcha ( $formId );
        $request = $controller->getRequest ();
        if ($captchaModel->isRequired ()) {
            $controller = $observer->getControllerAction ();
            
            $request->getPost ( Mage_Captcha_Helper_Data::INPUT_NAME_FIELD_VALUE );
            if (! $captchaModel->isCorrect ( $this->_getCaptchaString ( $request, $formId ) )) {
                
                if ((isset ( $this->getRequest ()->isXmlHttpRequest () ) && strtolower ( $this->getRequest ()->isXmlHttpRequest () ) == 'xmlhttprequest')) {
                    
                    /**
                     * If the form using AJAX returns $message
                     */
                    $action = $request->getActionName ();
                    Mage::app ()->getFrontController ()->getAction ()->setFlag ( $action, Mage_Core_Controller_Varien_Action::FLAG_NO_DISPATCH, true );
                    
                    $controller->getResponse ()->setHttpResponseCode ( 200 );
                    $controller->getResponse ()->setHeader ( 'Content-type', 'application/json' );
                    
                    $controller->getResponse ()->setBody ( json_encode ( array (
                            "msg" => Mage::helper ( 'module' )->__ ( 'Incorrect CAPTCHA.' ) 
                    ) ) );
                } else {
                    
                    /**
                     * If the form submit returns $message
                     */
                    Mage::getSingleton ( 'customer/session' )->addError ( Mage::helper ( 'module' )->__ ( 'Incorrect CAPTCHA.' ) );
                    $controller->setFlag ( '', Mage_Core_Controller_Varien_Action::FLAG_NO_DISPATCH, true );
                    Mage::getSingleton ( 'customer/session' )->setCustomerFormData ( $controller->getRequest ()->getPost () );
                    $controller->getResponse ()->setRedirect ( Mage::getUrl ( '*/*' ) );
                }
            }
        }
        
        return $this;
    }
}
