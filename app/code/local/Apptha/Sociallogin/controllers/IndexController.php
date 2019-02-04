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
 * Social Login Login / Account Create Controller
 *
 * In this class contains the login and create account and forget password operations.
 * Also it will connects social networks such as Google, Twitter, Yahoo and Facebook oAuth connections.
 */
class Apptha_Sociallogin_IndexController extends Mage_Core_Controller_Front_Action {

    /**
     * Render Apptha sociallogin pop-up layout
     *
     * @return void
     */
    public function indexAction() {

        /**
         * To load and render layout
         */
        $this->loadLayout ();
        $this->renderLayout ();
    }

    /**
     * Customer Register Action
     *
     * @param string $firstname
     * @param string $lastname
     * @param string $email
     * @param string $provider
     *
     * @return string
     */
    public function customerAction($firstname, $lastname, $email, $provider) {

        $customer = Mage::getModel ( 'customer/customer' );
        $collection = $customer->getCollection ();
        if ($customer->getSharingConfig ()->isWebsiteScope ()) {
            $collection->addAttributeToFilter ( 'website_id', Mage::app ()->getWebsite ()->getId () );
        }
        if ($this->_getCustomerSession ()->isLoggedIn ()) {
            $collection->addFieldToFilter ( 'entity_id', array (
                    'neq' => $this->_getCustomerSession ()->getCustomerId ()
            ) );
        }

        $customer->setWebsiteId ( Mage::app ()->getStore ()->getWebsiteId () )->loadByEmail ( $email );
        $customer_id_by_email = $customer->getId ();

        if ($customer_id_by_email == '') {
            $standardInfo ['email'] = $email;
        } else {
            $standardInfo ['email'] = $email;
        }


        $standardInfo ['first_name'] = $firstname;
        $standardInfo ['last_name'] = $lastname;

        $customer->setWebsiteId ( Mage::app ()->getStore ()->getWebsiteId () )->loadByEmail ( $standardInfo ['email'] );


        if ($customer->getId ()) {


            $this->_getCustomerSession ()->setCustomerAsLoggedIn ( $customer );

            $this->_getCustomerSession ()->addSuccess ( $this->__ ( 'Your account has been successfully connected through' . ' ' . $provider ) );


            $linkValue = Mage::getSingleton ( 'customer/session' )->getLink ();


            if (! empty ( $linkValue )) {
                $CurrentRequestPath = trim ( $linkValue, '/' );
            }

            if ($CurrentRequestPath == 'checkout/onestep') {
                $this->_redirect ( $CurrentRequestPath );
                return;
            } else {
                $enableRedirectStatus = Mage::getStoreConfig ( 'sociallogin/general/enable_redirect' );

                if ($enableRedirectStatus) {
                    $redirectUrl =  Mage::helper('customer')->getAccountUrl();
                } else {
                    $redirectUrl = Mage::getSingleton ( 'core/session' )->getReLink ();
                }

                $this->_redirectUrl ( $redirectUrl );
                return;
            }
        }

        /**
         * Generate Random Password .
         *
         *
         * @return string $randomPassword
         */
        $randomPassword = $customer->generatePassword ( 8 );


        $customer->setId ( null )->setSkipConfirmationIfEmail ( $standardInfo ['email'] )->setFirstname ( $standardInfo ['first_name'] )->setLastname ( $standardInfo ['last_name'] )->setEmail ( $standardInfo ['email'] )->setPassword ( $randomPassword )->setConfirmation ( $randomPassword )->setLoginProvider ( $provider );

        if ($this->getRequest ()->getParam ( 'is_subscribed', false )) {
            $customer->setIsSubscribed ( 1 );
        }


        $errors = array ();
        $validationCustomer = $customer->validate ();
        if (is_array ( $validationCustomer )) {
            $errors = array_merge ( $validationCustomer, $errors );
        }
        $validationResult = true;


        if (true === $validationResult) {

            $customer->save ();

            $this->_getCustomerSession ()->addSuccess ( $this->__ ( 'Thank you for registering with %s', Mage::app ()->getStore ()->getFrontendName () ) . '. ' . $this->__ ( 'You will receive welcome email with registration info in a moment.' ) );

            $customer->sendNewAccountEmail ();


            $this->_getCustomerSession ()->setCustomerAsLoggedIn ( $customer );


            $link = Mage::getSingleton ( 'customer/session' )->getLink ();


            if (! empty ( $link )) {

                $requestPath = trim ( $link, '/' );
            }

            if ($requestPath == 'checkout/onestep') {
                $this->_redirect ( $requestPath );
                return;
            } else {
                $enable_redirect_status = Mage::getStoreConfig ( 'sociallogin/general/enable_redirect' );
                if ($enable_redirect_status) {
                    $redirect = Mage::helper('customer')->getAccountUrl();
                } else {
                    $redirect = Mage::app()->getRequest()->getServer('HTTP_REFERER');
                }
                $this->_redirectUrl ( $redirect );
                return;
            }

        } else {
            $this->_getCustomerSession ()->setCustomerFormData ( $customer->getData () );
            $this->_getCustomerSession ()->addError ( $this->__ ( 'User profile can\'t provide all required info, please register and then connect with Apptha Social login.' ) );
            if (is_array ( $errors )) {
                foreach ( $errors as $errorMessage ) {
                    $this->_getCustomerSession ()->addError ( $errorMessage );
                }
            }
            $this->_redirect ( 'customer/account/create' );
            return;
        }
    }

    /**
     * Retrieve customer session model object
     *
     * @return Mage_Customer_Model_Session
     */
    private function _getCustomerSession() {
        return Mage::getSingleton ( 'customer/session' );
    }

    /**
     * Redirect customer dashboard URL after logging in
     *
     * @return string URL
     */
    protected function _loginPostRedirect() {
        $session = $this->_getCustomerSession ();

        if (! $session->getBeforeAuthUrl () || $session->getBeforeAuthUrl () == Mage::getBaseUrl ()) {

            $session->setBeforeAuthUrl ( Mage::helper ( 'customer' )->getAccountUrl () );

            /**
             * Redirect customer to the last page visited after logging in
             */
            if ($session->isLoggedIn ()) {
                if (! Mage::getStoreConfigFlag ( 'customer/startup/redirect_dashboard' )) {
                    if ($referer = $this->getRequest ()->getParam ( Mage_Customer_Helper_Data::REFERER_QUERY_PARAM_NAME )) {
                        $referer = Mage::helper ( 'core' )->urlDecode ( $referer );
                        if ($this->_isUrlInternal ( $referer )) {
                            $session->setBeforeAuthUrl ( $referer );
                        }
                    }
                } else if ($session->getAfterAuthUrl ()) {
                    $session->setBeforeAuthUrl ( $session->getAfterAuthUrl ( true ) );
                }
            } else {
                $session->setBeforeAuthUrl ( Mage::helper ( 'customer' )->getLoginUrl () );
            }
        } else if ($session->getBeforeAuthUrl () == Mage::helper ( 'customer' )->getLogoutUrl ()) {
            $session->setBeforeAuthUrl ( Mage::helper ( 'customer' )->getDashboardUrl () );
        } else {
            if (! $session->getAfterAuthUrl ()) {
                $session->setAfterAuthUrl ( $session->getBeforeAuthUrl () );
            }
            if ($session->isLoggedIn ()) {
                $session->setBeforeAuthUrl ( $session->getAfterAuthUrl ( true ) );
            }
        }

        return $session->getBeforeAuthUrl ( true );
    }

    /**
     * @Twitter login action
     */
    public function twitterloginAction() {

        /**
         * Include Twitter files for oAuth connection
         */
        require 'sociallogin/twitter/twitteroauth.php';
        require 'sociallogin/config/twconfig.php';

        /**
         * Retrives @Twitter consumer key and secret key from core session
         */
        $tw_oauth_token = Mage::getSingleton ( 'customer/session' )->getTwToken ();
        $tw_oauth_token_secret = Mage::getSingleton ( 'customer/session' )->getTwSecret ();
        $twitteroauth = new TwitterOAuth ( YOUR_CONSUMER_KEY, YOUR_CONSUMER_SECRET, $tw_oauth_token, $tw_oauth_token_secret );

        /**
         * Get Accesss token from @Twitter oAuth
         */
        $oauth_verifier = $this->getRequest ()->getParam ( 'oauth_verifier' );
        $access_token = $twitteroauth->getAccessToken ( $oauth_verifier );

        /**
         * Get @Twitter User Details from twitter account
         *
         * @return string Redirect URL or Customer save action
         */
        $user_info = $twitteroauth->get ( 'account/verify_credentials' );

        /**
         * Retrieve the user details into twitter profile info.
         * @var $user_info array
         *
         * If @user_info contains error means throws the error message.
         */
        if (isset ( $user_info->error )) {
            Mage::getSingleton ( 'customer/session' )->addError ( $this->__ ( 'Twitter Login connection failed' ) );
            $url = Mage::helper ( 'customer' )->getAccountUrl ();
            return $this->_redirectUrl ( $url );
        } else {

            /**
             * Retrieve the user details into twitter profile info.
             * @var $user_info array
             */
            $firstname = $user_info->name;
            $twitter_id = $user_info->id;
            $email = Mage::getSingleton ( 'customer/session' )->getTwemail ();
            $lastname = $user_info->screen_name;

            if ($email == '' || $firstname == '') {
                Mage::getSingleton ( 'customer/session' )->addError ( $this->__ ( 'Twitter Login connection failed' ) );
                $url = Mage::helper ( 'customer' )->getAccountUrl ();
                return $this->_redirectUrl ( $url );
            } else {
                $this->customerAction ( $firstname, $lastname, $email, 'Twitter' );
            }
        }
    }

    /**
     * @Twitter post action
     *
     * @return string Returns Twitter page URL for Authendication
     */
    public function twitterpostAction() {
        $provider = '';

        /**
         * Retrieve the customer posted email to authendicate @Twitter account
         *
         * @param
         *            string email_value
         */
        $twitter_email = ( string ) $this->getRequest ()->getPost ( 'email_value' );

        /**
         * Set the $twitter_email into customer session
         */
        Mage::getSingleton ( 'customer/session' )->setTwemail ( $twitter_email );

        /**
         * Retrieve customer session model object
         *
         * @return Mage_Customer_Model_Session
         */
        $customer = Mage::getModel ( 'customer/customer' );
        $customer->setWebsiteId ( Mage::app ()->getStore ()->getWebsiteId () )->loadByEmail ( $twitter_email );
        $customer_id_by_email = $customer->getId ();
        $customer = Mage::getModel ( 'customer/customer' )->load ( $customer_id_by_email );
        $google_uid = $customer->getGoogleUid ();

        /**
         * Finds the Login Provider if customer may use
         *
         * @return string $provider
         */
        if ($google_uid != '') {
            $provider .= ' Google';
        }

        $facebook_uid = $customer->getFacebookUid ();
        if ($facebook_uid != '') {
            $provider .= ', Facebook';
        }

        $linkedin_uid = $customer->getLinkedinUid ();
        if ($linkedin_uid != '') {
            $provider .= ', Linkedin';
        }

        $yahoo_uid = $customer->getYahooUid ();
        if ($yahoo_uid != '') {
            $provider .= ', Yahoo';
        }

        $twitter_uid = $customer->getTwitterUid ();
        $provider = ltrim ( $provider, ',' );

        /**
         * Send the response to the customer request for twitter action
         *
         * @return string $url
         */
        if ($customer_id_by_email == '') {
            $url = Mage::helper ( 'sociallogin' )->getTwitterUrl ();
            $this->getResponse ()->setBody ( $url );
        } else if ($provider != '') {
            $url = Mage::helper ( 'sociallogin' )->getTwitterUrl ();
            $this->getResponse ()->setBody ( $url );
        } else if (($provider == '') && ($twitter_uid != '')) {
            $url = Mage::helper ( 'sociallogin' )->getTwitterUrl ();
            $this->getResponse ()->setBody ( $url );
        } else {
            $url = Mage::helper ( 'sociallogin' )->getTwitterUrl ();
            $this->getResponse ()->setBody ( $url );
        }
    }

    /**
     * @facebook login action
     *
     * Connect facebook Using oAuth coonection.
     *
     * @return string redirect URL
     *
     */
    public function fbloginAction() {
    	if($this->getRequest()->getParam('email')) {
    		$email = $this->getRequest()->getParam('email');
    		$firstName = $this->getRequest()->getParam('fname');
    		$lastName = $this->getRequest()->getParam('lname');
    		$data = $this->getRequest()->getParam('fb');
    		$this->customerAction($firstName, $lastName, $email, 'Facebook',$data);
    	} else {
    		Mage::getSingleton('customer/session')->addError($this->__('Facebook Login connection failed'));
    	}
    	$url = Mage::helper('customer')->getAccountUrl();
    	return $this->_redirectUrl($url);
    }

    /**
     * @Google login action
     *
     * Connect Google Using oAuth coonection.
     *
     * @return string redirect URL either customer save and loggedin or an error if any occurs
     */
    public function googlepostAction() {
        $error = $this->getRequest ()->getParam ( 'error' );
        if($error) {
            return $this->_redirectUrl ( Mage::getBaseUrl() );
        }
        /**
         * Include @Google library files for oAuth connection
         */
        require_once 'sociallogin/src/Google_Client.php';
        require_once 'sociallogin/src/contrib/Google_Oauth2Service.php';

        /**
         * Retrieves the @google_client_id, @google_client_secret
         */
        $google_client_id = Mage::getStoreConfig ( 'sociallogin/google/google_id' );
        $google_client_secret = Mage::getStoreConfig ( 'sociallogin/google/google_secret' );
        $google_developer_key = Mage::getStoreConfig ( 'sociallogin/google/google_develop' );
        $google_redirect_url = Mage::getUrl () . 'sociallogin/index/googlepost/';

        /**
         * Create the object @var $gClient from google client
         */
        $gClient = new Google_Client ();
        $gClient->setApplicationName ( 'login' );
        $gClient->setClientId ( $google_client_id );
        $gClient->setClientSecret ( $google_client_secret );
        $gClient->setRedirectUri ( $google_redirect_url );
        $gClient->setDeveloperKey ( $google_developer_key );

        /**
         * Create the object @var $google_oauthV2 from Google_Oauth2Service
         */
        $google_oauthV2 = new Google_Oauth2Service ( $gClient );
        $token = Mage::getSingleton ( 'core/session' )->getGoogleToken ();
        $reset = $this->getRequest ()->getParam ( 'reset' );
        if ($reset) {
            unset ( $token );
            $gClient->revokeToken ();
            $this->_redirectUrl ( filter_var ( $google_redirect_url, FILTER_SANITIZE_URL ) );
        }

        /**
         * If retrieve the param in request array
         *
         * @param
         *            string code
         */
        $code = $this->getRequest ()->getParam ( 'code' );

        if (isset ( $code )) {
            $gClient->authenticate ( $code );
            Mage::getSingleton ( 'core/session' )->setGoogleToken ( $gClient->getAccessToken () );
            $this->_redirectUrl ( filter_var ( $google_redirect_url, FILTER_SANITIZE_URL ) );
            $this->_redirectUrl ( $google_redirect_url );
            return;
        }

        /**
         * If $token is non-empty set the access token
         */
        if (isset ( $token )) {
            $gClient->setAccessToken ( $token );
        }
        if ($gClient->getAccessToken ()) {

            /**
             * Retrieve user details If user succesfully in Google
             */
            $user = $google_oauthV2->userinfo->get ();
            $user_id = $user ['id'];
            $user_name = filter_var ( $user ['name'], FILTER_SANITIZE_SPECIAL_CHARS );
            $email = filter_var ( $user ['email'], FILTER_SANITIZE_EMAIL );
            $profile_url = filter_var ( $user ['link'], FILTER_VALIDATE_URL );
            $token = $gClient->getAccessToken ();
            Mage::getSingleton ( 'core/session' )->setGoogleToken ( $token );
        } else {

            /**
             * get google Authendication URL
             */
            $authUrl = $gClient->createAuthUrl ();
        }

        /**
         * If user doesn't logged-in redirects the login URL
         */
        if (isset ( $authUrl )) {
            $this->_redirectUrl ( $authUrl );
        } else {

            /**
             * Fetching user infor from google array $user
             *
             * @var string $firstname, , general info for users from @google account.
             * @var string $familyname
             * @var string $email
             * @var string $id
             */
            $firstname = $user ['given_name'];
            $lastname = $user ['family_name'];

            $email = $user ['email'];
            $google_user_id = $user ['id'];

            /**
             * If @var $email is empty throws failure message.
             */
            if ($email == '') {
                Mage::getSingleton ( 'customer/session' )->addError ( $this->__ ( 'Google Login connection failed' ) );
                $url = Mage::helper ( 'customer' )->getAccountUrl ();
                return $this->_redirectUrl ( $url );
            } else {

                /**
                 * Do the customer account action with the login provider as Google
                 */
                $this->customerAction ( $firstname, $lastname, $email, 'Google' );
            }
        }
    }

    /**
     * Customer Login layout render Action
     *
     * Rendering the layout if social login extension is enabled
     *
     * @return void
     */
    public function loginAction() {

        /**
         * Check if customer is logged in or not
         *
         * @return void
         */
        if ($this->_getCustomerSession ()->isLoggedIn ()) {
            $this->_redirect ( '*/*/' );
            return;
        } else if (Mage::getStoreConfig ( 'sociallogin/general/enable_sociallogin' ) == 1) {
            return;
        }
        $this->getResponse ()->setHeader ( 'Login-Required', 'true' );
        $this->loadLayout ();
        $this->_initLayoutMessages ( 'customer/session' );
        $this->_initLayoutMessages ( 'catalog/session' );
        $this->renderLayout ();
    }

    /**
     * Customer Create Account layout render Action
     *
     * Rendering the layout if social login extension is enabled
     *
     * @return void
     */
    public function createAction() {
        if ($this->_getCustomerSession ()->isLoggedIn ()) {
            $this->_redirect ( '*/*/' );
            return;
        } else {
            $enable_status = Mage::getStoreConfig ( 'sociallogin/general/enable_sociallogin' );
            if ($enable_status == 1) {
                return;
            }
        }

        $this->loadLayout ();
        $this->_initLayoutMessages ( 'customer/session' );
        $this->renderLayout ();
    }

    /**
     * Validation for Tax/Vat field for current store
     *
     * @return boolean true|false
     */
    public function _isVatValidationEnabled($store = null) {
        return Mage::helper ( 'customer/address' )->isVatValidationEnabled ( $store );
    }

    /**
     * Customer welcome function
     *
     * Its used for print welcome message once successfully logged in
     *
     * @return string customer success page URL.
     */
    public function _welcomeCustomer(Mage_Customer_Model_Customer $customer, $isJustConfirmed = false) {

        /**
         * Throws the welcome success message when customer registered successfully
         */
        $this->_getCustomerSession ()->addSuccess ( $this->__ ( 'Thank you for registering with %s.', Mage::app ()->getStore ()->getFrontendName () ) );

        /**
         * Send the welcome mail to the customer registered email
         */
        $customer->sendNewAccountEmail ( $isJustConfirmed ? 'confirmed' : 'registered', '', Mage::app ()->getStore ()->getId () );

        $successUrl = Mage::getUrl ( 'customer/account', array (
                '_secure' => true
        ) );

        if ($this->_getCustomerSession ()->getBeforeAuthUrl ()) {
            $successUrl = $this->_getCustomerSession ()->getBeforeAuthUrl ( true );
        }
        return $successUrl;
    }

    /**
     * Customer login Action
     *
     * validate the social login form posted values if the user is registered user or not
     *
     * @return string Redirect URL.
     */
    public function customerloginpostAction() {
        $session = $this->_getCustomerSession ();
        /**
         *
         * @param array $login
         *            contains email and password
         */
        $login ['username'] = $this->getRequest ()->getParam ( 'email' );
        $login ['password'] = $this->getRequest ()->getParam ( 'password' );

        /**
         * Check customer already logged in or not using the customet session
         *
         * @return string $message
         */
        if ($session->isLoggedIn ()) {
            $message = 'Already loggedin';
            $this->getResponse ()->setBody ( $message );
            return;
        }
            /**
         * If Login data has been posted with @param array $login
         *
         * @param
         *            username
         * @param
         *            password
         *
         * @return string $messge
         */
        if ($this->getRequest ()->isPost ()) {
            if (! empty ( $login ['username'] ) && ! empty ( $login ['password'] )) {
                try {
                    $session->login ( $login ['username'], $login ['password'] );
                    if ($session->getCustomer ()->getIsJustConfirmed ()) {
                        $this->getResponse ()->setBody ( $this->_welcomeCustomer ( $session->getCustomer (), true ) );
                    }
                } catch ( Mage_Core_Exception $e ) {
                    switch ($e->getCode ()) {
                        case Mage_Customer_Model_Customer::EXCEPTION_EMAIL_NOT_CONFIRMED :
                            $value = Mage::helper ( 'customer' )->getEmailConfirmationUrl ( $login ['username'] );
                            $message = Mage::helper ( 'customer' )->__ ( 'Account Not Confirmed', $value );
                            $this->getResponse ()->setBody ( $message );
                            break;
                        case Mage_Customer_Model_Customer::EXCEPTION_INVALID_EMAIL_OR_PASSWORD :
                            $message = $this->__ ( 'Invalid Email Address or Password' );
                            $this->getResponse ()->setBody ( $message );
                            break;
                        default :
                            $message = $e->getMessage ();

                            /**
                             * Send the response message for customer login request
                             *
                             * @return string $message
                             */
                            $this->getResponse ()->setBody ( $message );
                    }
                     /**
                     * Set customer username @param username in customer session
                     */
                    $session->setUsername ( $login ['username'] );
                } catch ( Exception $e ) {
                      /**
                     *
                     * @throws Exception message
                     */
                    return $e;
                }
                /**
                 * After successful logged-in, its redirect to the respective page.
                 */
                if ($session->getCustomer ()->getId ()) {
                    $link = Mage::getSingleton ( 'customer/session' )->getLink ();
                    $requestPath = '';
                    if (! empty ( $link )) {

                        $requestPath = trim ( $link, '/' );
                    }
                    if ($requestPath == 'checkout/onestep') {
                        $this->getResponse ()->setBody ( $requestPath );
                    } else {
                        $enable_redirect_status = 0;
                        $enable_redirect_status = Mage::getStoreConfig ( 'sociallogin/general/enable_redirect' );

			$splitLink = explode ( Mage::getBaseUrl (), $link );
                    	$requestPath = end ( $splitLink );


                        if ($enable_redirect_status == 1 && $requestPath != 'onestepcheckout/') {

                            /**
                             * Sends the response for login rediect URL
                             *
                             * @return string URL
                             */
                            $this->getResponse ()->setBody ( Mage::helper('customer')->getAccountUrl());
                        } else {
                    $this->getResponse ()->setBody ( Mage::app()->getRequest()->getServer('HTTP_REFERER'));
                        }
                    }
                }
            }
        }
    }

    /**
     * Customer Register Action
     *
     * validate the social regiter form posted values
     *
     * @return string Redirect URL.
     */
    public function createPostAction() {
        $customer = Mage::getModel ( 'customer/customer' );
        $session = $this->_getCustomerSession ();
        if ($session->isLoggedIn ()) {
            $this->_redirect ( '*/*/' );
            return;
        }

        /**
         * Validate the captcha code if captcha is enabled
         *
         * @return string if incorrect capatcha it will return error message
         */
        $enable_captcha = Mage::getStoreConfig ( 'customer/captcha/enable' );

        if ($enable_captcha == '1') {
            $newcaptch = $this->getRequest ()->getPost ( 'captcha' );
            $_captcha = Mage::getModel ( 'customer/session' )->getData ( 'user_create_word' );
            $captcha_img_data = $_captcha ['data'];
            if ($newcaptch ['user_create'] != $captcha_img_data) {
                $this->getResponse ()->setBody ( $this->__ ( 'Incorrect CAPTCHA.' ) );
                return;
            }
        }

        /**
         * Preventing the Cross-site Scripting (XSS) injection from an user inputs
         */
        $session->setEscapeMessages ( true );
        if ($this->getRequest ()->isPost ()) {
            $errors = array ();

            if (! $customer = Mage::registry ( 'current_customer' )) {
                $customer = Mage::getModel ( 'customer/customer' )->setId ( null );
            }

            /**
             *
             * @var $customerForm Mage_Customer_Model_Form
             */
            $customerForm = Mage::getModel ( 'customer/form' );
            $customerForm->setFormCode ( 'customer_account_create' )->setEntity ( $customer );

            $customerData = $customerForm->extractData ( $this->getRequest () );
            if ($this->getRequest ()->getParam ( 'is_subscribed', false )) {
                $customer->setIsSubscribed ( 1 );
            }

            /**
             * Get customer group id from customer collection
             */
            $customer->getGroupId ();

            if ($this->getRequest ()->getPost ( 'create_address' )) {

                /**
                 *
                 * @var $address Mage_Customer_Model_Address
                 */
                $address = Mage::getModel ( 'customer/address' );

                /**
                 *
                 * @var $addressForm Mage_Customer_Model_Form
                 */
                $addressForm = Mage::getModel ( 'customer/form' );
                $addressForm->setFormCode ( 'customer_register_address' )->setEntity ( $address );

                /**
                 * Extracting the address Data from array $addressData
                 */
                $addressData = $addressForm->extractData ( $this->getRequest (), 'address', false );

                /**
                 * validate the address data
                 *
                 * @return boolean True|False
                 */
                $addressErrors = $addressForm->validateData ( $addressData );
                if ($addressErrors === true) {
                    $address->setId ( null )->setIsDefaultBilling ( $this->getRequest ()->getParam ( 'default_billing', false ) )->setIsDefaultShipping ( $this->getRequest ()->getParam ( 'default_shipping', false ) );
                    $addressForm->compactData ( $addressData );
                    $customer->addAddress ( $address );

                    $addressErrors = $address->validate ();
                    if (is_array ( $addressErrors )) {
                        $errors = array_merge ( $errors, $addressErrors );
                    }
                } else {
                    $errors = array_merge ( $errors, $addressErrors );
                }
            }
            try {
                $customerErrors = $customerForm->validateData ( $customerData );

                if ($customerErrors !== true) {
                    $errors = array_merge ( $customerErrors, $errors );
                } else {
                    $customerForm->compactData ( $customerData );

                    $customer->setPassword ( $this->getRequest ()->getPost ( 'password' ) );

                    $magentoVersion = Mage::getVersion ();
                    if (version_compare ( $magentoVersion, '1.9.1', '>=' )) {
                        $customer->setPasswordConfirmation ( $this->getRequest ()->getPost ( 'confirmation' ) );
                    } else {
                        $customer->setConfirmation ( $this->getRequest ()->getPost ( 'confirmation' ) );
                    }

                    $customerErrors = $customer->validate ();
                    if (is_array ( $customerErrors )) {
                        $errors = array_merge ( $customerErrors, $errors );
                    }
                }

                /**
                 * If @var $validationResult is true dispatching the event into customer_register_success
                 *
                 * @return string URL
                 */
                $validationResult = count ( $errors ) == 0;
                if (true === $validationResult) {
                    $customer->save ();

                    Mage::dispatchEvent ( 'customer_register_success', array (
                            'account_controller' => $this,
                            'customer' => $customer
                    ) );

                    if ($customer->isConfirmationRequired ()) {
                        $customer->sendNewAccountEmail ( 'confirmation', $session->getBeforeAuthUrl (), Mage::app ()->getStore ()->getId () );
                        $session->addSuccess ( $this->__ ( 'Account confirmation is required. Please, check your email for the confirmation link.' ) );
                        
                        $this->getResponse ()->setBody ( Mage::getUrl ( '/index', array (
                                '_secure' => true
                        ) ) );
                        return;
                    } else {
                        $session->setCustomerAsLoggedIn ( $customer );
                        $url = $this->_welcomeCustomer ( $customer );
                        $this->getResponse ()->setBody ( $url );
                        return;
                    }
                } else {
                    $session->setCustomerFormData ( $this->getRequest ()->getPost () );
                    if (is_array ( $errors )) {
                        foreach ( $errors as $errorMessage ) {
                            $session->$errorMessage;
                        }
                        $this->getResponse ()->setBody ( $errorMessage );
                        return;
                    } else {
                        $session->addError ( $this->__ ( 'Invalid customer data' ) );
                    }
                }
            } catch ( Mage_Core_Exception $e ) {
                $session->setCustomerFormData ( $this->getRequest ()->getPost () );
                if ($e->getCode () === Mage_Customer_Model_Customer::EXCEPTION_EMAIL_EXISTS) {
                    $message = $this->__ ( 'Email already exists' );
                    $this->getResponse ()->setBody ( $message );
                    $session->setEscapeMessages ( false );
                    return;
                } else {
                    $message = $e->getMessage ();
                    $this->getResponse ()->setBody ( $message );
                    return;
                }
                $session->addError ( $message );
            } catch ( Exception $e ) {
                $session->setCustomerFormData ( $this->getRequest ()->getPost () )->addException ( $e, $this->__ ( 'Cannot save the customer.' ) );
            }
        }
        if (! empty ( $message )) {
            $this->getResponse ()->setBody ( $message );
        }
        $redirectUrl =  Mage::helper('customer')->getAccountUrl();
        
        
        $this->getResponse ()->setBody($redirectUrl);
    }

    /**
     * ForgetPassword Action
     *
     * @param string $email
     *            Forget password action for forget password form
     *
     * @return string $message.
     */
    public function forgotPasswordPostAction() {
        $email = ( string ) $this->getRequest ()->getParam ( 'forget_password' );
        $customer = Mage::getModel ( 'customer/customer' )->setWebsiteId ( Mage::app ()->getStore ()->getWebsiteId () )->loadByEmail ( $email );
        if ($customer->getId ()) {
            try {
                $newResetPasswordLinkToken = Mage::helper ( 'customer' )->generateResetPasswordLinkToken ();
                $customer->changeResetPasswordLinkToken ( $newResetPasswordLinkToken );
                $customer->sendPasswordResetConfirmationEmail ();
            } catch ( Exception $exception ) {
                $this->_getCustomerSession ()->addError ( $exception->getMessage () );
                return;
            }
            $message = $this->__ ( 'You will receive an email ' ) . $email . ' ';
            $message = $message . $this->__ ( 'with a link to reset your password' );
        } else {
            $message = $this->__ ( 'If there is no account associated with this email please enter your correct email-id' );
        }
        $this->getResponse ()->setBody ( $message );
    }
}
