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
/**
 * Rewrite AccountController from core file
 *
 * Class Apptha_Airhotels_Customer_AccountController
 *
 * extend Mage_Customer_AccountController
 */
require_once 'Mage/Customer/controllers/AccountController.php';
class Apptha_Airhotels_Customer_AccountController extends Mage_Customer_AccountController {
    /**
     * Function logoutAction()
     *
     * Customer logout action
     */
    public function logoutAction() {
        /**
         * Rewrite core action and redirect to homepage url
         */
        $this->_getSession ()->logout ()->renewSession ();
        /**
         * Set success message.
         *
         * Redirect to base url.
         */
        Mage::getSingleton ( 'core/session' )->addSuccess ( 'You are now logged out' );
        $this->_redirectUrl ( Mage::getBaseUrl () );
    }
    /**
     * Function loginAction()
     *
     * Redirect to home page
     */
    public function loginAction() {
        /**
         * Check customer is logged in or not.
         *
         * If loged in redirect to corresponding pages.
         */
        if ($this->_getSession ()->isLoggedIn ()) {
            $this->_redirect ( '*/*/' );
            return;
        }
        /**
         * Set header for logout action
         *
         * load and render layout
         */
        $this->getResponse ()->setHeader ( 'Login-Required', 'true' );
        $this->loadLayout ();
        $this->_initLayoutMessages ( 'customer/session' );
        $this->_initLayoutMessages ( 'catalog/session' );
        $this->renderLayout ();
        $this->_redirect ( '', array (
                '_query' => 'login=1' 
        ) );
    }
    /**
     * Function Name: editPostAction
     *
     * Change customer password action
     *
     * @var $customer
     * @var $customerForm
     * @var $customerData
     * @var $customerErrors
     *
     */
    public function editPostAction() {
        if (! $this->_validateFormKey ()) {
            /**
             * validate form key
             * Redirect to edit page.
             */
            return $this->_redirect ( '*/*/edit' );
        }
        /**
         * Get parameters value.
         */
        $actionName = $this->getRequest ()->getPost ( 'action' );
        if ($this->getRequest ()->isPost ()) {
            /**
             *
             * @var $customer Mage_Customer_Model_Customer
             */
            $customer = $this->_getSession ()->getCustomer ();
            /**
             *
             * @var $customerForm Mage_Customer_Model_Form
             */
            $customerForm = $this->_getModel ( 'customer/form' );
            $customerForm->setFormCode ( 'customer_account_edit' )->setEntity ( $customer );
            $customerData = $customerForm->extractData ( $this->getRequest () );
            /**
             * Validate customer data
             */
            $errors = array ();
            /**
             * validate customer data
             */
            $customerErrors = $customerForm->validateData ( $customerData );
            if ($customerErrors !== true) {
                /**
                 * Merge error message.
                 */
                $errors = array_merge ( $customerErrors, $errors );
            } else {
                $customerForm->compactData ( $customerData );
                $errors = array ();
                /**
                 * If password change was requested then add it to
                 * common validation scheme
                 */
                if ($this->getRequest ()->getParam ( 'change_password' )) {
                    $currPass = $this->getRequest ()->getPost ( 'current_password' );
                    $newPass = $this->getRequest ()->getPost ( 'password' );
                    $confPass = $this->getRequest ()->getPost ( 'confirmation' );
                    /**
                     * Check the old password enter is correct or not
                     * 
                     * @var $oldPass
                     */
                    $oldPass = $this->_getSession ()->getCustomer ()->getPasswordHash ();
                    if ($this->_getHelper ( 'core/string' )->strpos ( $oldPass, ':' )) {
                        list ( $_salt,$salt ) = explode ( ':', $oldPass );
                    } else {
                        $salt = false;
                        $_salt = false;
                    }
                    /**
                     * Compare old and new password
                     */
                    if ($customer->hashPassword ( $currPass, $salt ) == $oldPass && strlen ( $newPass )) {
                        /**
                         * Set entered password and its confirmation - they
                         * will be validated later to match each other and be of right length
                         */
                        $customer->setPassword ( $newPass );
                        /**
                         * Setting password confirmation
                         */
                        $customer->setPasswordConfirmation ( $confPass );
                    } else {
                        /**
                         * Set error message in password is invalid.
                         */
                        $errors [] = $this->__ ( 'New password field cannot be empty.' );
                        $errors [] = $this->__ ( 'Invalid current password' );
                    }
                }
                /**
                 * Validate account and compose list of errors if any
                 */
                $customerErrors = $customer->validate ();
                if (is_array ( $customerErrors )) {
                    $errors = array_merge ( $errors, $customerErrors );
                }
            }
            /**
             * Set customer form data to session
             */
            if (! empty ( $errors )) {
                $this->_getSession ()->setCustomerFormData ( $this->getRequest ()->getPost () );
                foreach ( $errors as $message ) {
                    /**
                     * Set error messages in session.
                     */
                    $this->_getSession ()->addError ( $message );
                }
                /**
                 * redirect to edit action
                 */
                $this->_redirect ( '*/*/edit' );
                return $this;
            }
            try {
                /**
                 * Set password confirmation field is empty.
                 */
                $customer->setPasswordConfirmation ( null );
                $customer->save ();
                /**
                 * Check if the action name is security or not
                 */
                if ($actionName == 'security') {
                    /**
                     * Set success messge.
                     * Redirecct to security action page
                     */
                    Mage::getSingleton ( 'core/session' )->addSuccess ( $this->__ ( 'The account information has been saved.' ) );
                    $this->_redirect ( 'airhotels/dashboard/security' );
                } else {
                    /**
                     * redirect to change password action
                     */
                    $this->_getSession ()->setCustomer ( $customer )->addSuccess ( $this->__ ( 'The account information has been saved.' ) );
                    $this->_redirect ( 'customer/account/edit/changepass/1/id/changepass' );
                }
                return;
            } catch ( Mage_Core_Exception $e ) {
                /**
                 * Set error message.
                 */
                $this->_getSession ()->setCustomerFormData ( $this->getRequest ()->getPost () )->addError ( $e->getMessage () );
            } catch ( Exception $e ) {
                /**
                 * Set error message in customer form data.
                 */
                $this->_getSession ()->setCustomerFormData ( $this->getRequest ()->getPost () )->addException ( $e, $this->__ ( 'Cannot save the customer.' ) );
            }
        }
        /**
         * redirect to edit action
         */
        $this->_redirect ( '*/*/edit' );
    }
    /**
     * Function Name: checkoldpasswordAction()
     *
     * Fuction to check the old password
     *
     * @var $password
     * @var $oldPass
     * @var $hashArr return boolean
     */
    public function checkoldpasswordAction() {
        /**
         * get current password
         */
        $password = $this->getRequest ()->getPost ( 'current_password' );
        /**
         * get customer password from session
         */
        $oldPass = Mage::getSingleton ( 'customer/session' )->getCustomer ()->getPasswordHash ();
        $hashArr = explode ( ':', $oldPass );
        /**
         * check the hash value of the password
         */
        if (md5 ( $hashArr [1] . $password ) == $hashArr [0]) {
            echo 'true';
        } else {
            echo 'false';
        }
    }
    /**
     * Function Name: resetPasswordPostAction()
     *
     * Reset forgotten password
     * Used to handle data recieved from reset forgotten password form
     *
     * @var $password
     * @var $passwordConfirmation
     * @var $customer
     * @var $errorMessages
     */
    public function resetPasswordPostAction() {
        list ( $customerId, $resetPasswordLinkToken ) = $this->_getRestorePasswordParameters ( $this->_getSession () );
        /**
         * Get post parameters.
         *
         * @var $password,$passwordConfirmation
         */
        $password = ( string ) $this->getRequest ()->getPost ( 'password' );
        $passwordConfirmation = ( string ) $this->getRequest ()->getPost ( 'confirmation' );
        try {
            /**
             * Validate the reset password link token.
             */
            $this->_validateResetPasswordLinkToken ( $customerId, $resetPasswordLinkToken );
        } catch ( Exception $exception ) {
            /**
             * Set error message.
             */
            $this->_getSession ()->addError ( $this->_getHelper ( 'customer' )->__ ( 'Your password reset link has expired.' ) );
            $this->_redirect ( '*/*/' );
            return;
        }
        $errorMessages = array ();
        if (iconv_strlen ( $password ) <= 0) {
            /**
             * Insert error message in array.
             */
            array_push ( $errorMessages, $this->_getHelper ( 'customer' )->__ ( 'New password field cannot be empty.' ) );
        }
        /**
         *
         * @var $customer Mage_Customer_Model_Customer
         */
        $customer = $this->_getModel ( 'customer/customer' )->load ( $customerId );
        /**
         * Set password and password confirmation.
         */
        $customer->setPassword ( $password );
        $customer->setPasswordConfirmation ( $passwordConfirmation );
        $validationErrorMessages = $customer->validate ();
        /**
         * Check validation error message.
         */
        if (is_array ( $validationErrorMessages )) {
            /**
             * Merge error message in error message array.
             *
             * @var $errorMessages
             */
            $errorMessages = array_merge ( $errorMessages, $validationErrorMessages );
        }
        /**
         * Check error message array.
         */
        if (! empty ( $errorMessages )) {
            $this->_getSession ()->setCustomerFormData ( $this->getRequest ()->getPost () );
            foreach ( $errorMessages as $errorMessage ) {
                $this->_getSession ()->addError ( $errorMessage );
            }
            /**
             * Redirect to changeforgotten action.
             */
            $this->_redirect ( '*/*/changeforgotten' );
            return;
        }
        try {
            /**
             * Empty current reset password token i.e.
             * invalidate it
             */
            $customer->setRpToken ( null );
            $customer->setRpTokenCreatedAt ( null );
            $customer->cleanPasswordsValidationData ();
            $customer->save ();
            /**
             * Unset token session name and customer id session name
             */
            $this->_getSession ()->unsetData ( static::TOKEN_SESSION_NAME );
            $this->_getSession ()->unsetData ( static::CUSTOMER_ID_SESSION_NAME );
            Mage::getSingleton ( 'core/session' )->addSuccess ( 'Your password has been updated.' );
            /**
             * Redirct to base url
             */
            $this->_redirectUrl ( Mage::getBaseUrl () );
        } catch ( Exception $exception ) {
            /**
             * Set exception error in session.
             *
             * redirect to change forgotten action
             */
            $this->_getSession ()->addException ( $exception, $this->__ ( 'Cannot save a new password.' ) );
            $this->_redirect ( '*/*/changeforgotten' );
            return;
        }
    }
}