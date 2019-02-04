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
require_once 'Mage/Checkout/controllers/CartController.php';
/**
 * Extend cart controller from Mage_Checkout_CartController
 *
 * @author user
 *        
 */
class Apptha_Airhotels_Checkout_CartController extends Mage_Checkout_CartController {
    /**
     * Add product to shopping cart action
     */
    public function addAction() {
        $cart = $this->_getCart ();
        $params = $this->getRequest ()->getParams ();
        try {
            /**
             * Setting airhotels qty (days/hours)
             */               
            $hourlyEnabledOrNot = Mage::helper ( 'airhotels/product' )->getHourlyEnabledOrNot ();
            $productCollection = Mage::getModel ( 'catalog/product' )->load ( $params ['hourly_product_id'] );
            if (Mage::getSingleton('customer/session')->isLoggedIn() && Mage::getSingleton ( 'customer/session' )->getCustomer ()->getId () == $productCollection->getUserid ()) {
                Mage::getSingleton ( 'core/session' )->addError ( Mage::helper ( 'airhotels' )->__ ( "Host can't book their list." ) );
                Mage::app ()->getResponse ()->setRedirect ( $productCollection->getProductUrl () )->sendResponse ();                    
                return;
            }
            $fromDate = date ( "Y-m-d", strtotime ( str_replace ( '@', '/', $params ['fromdate'] ) ) );
            $toDate = date ( "Y-m-d", strtotime ( str_replace ( '@', '/', $params ['todate'] ) ) );
            $peopleCount = $params ['accomodate'];
            $hourlyProductId = $params ['hourly_product_id'];
            $securityFee = $params ['securityFee'];
            $hourlyNightFee = $params ['hourly_night_fee'];
            $overallTotalHours = $params ['overall_total_hours'];
            Mage::getSingleton ( 'core/session' )->setHourlyProductId ( $hourlyProductId );                
            if ($hourlyEnabledOrNot == 0 && $subId != 0) {
                $todate = date ( 'Y-m-d', strtotime ( '-1 day', strtotime ( $todate ) ) );
            }
            /**
             * For hourly based property
             */
            if (! empty ( $hourlyProductId )) {
                $propertyTime = Mage::getModel ( 'catalog/product' )->load ( $hourlyProductId )->getPropertyTime ();
                $propertyTimeData = Mage::helper ( 'airhotels/airhotel' )->getPropertyTimeLabelByOptionId ();                    
                if ($propertyTime == $propertyTimeData && $hourlyEnabledOrNot == 0) {
                    $params ['per_hour_night_fee'] = $this->propertyOvernightFee($hourlyNightFee,$hourlyProductId);
                    Mage::getModel ( 'airhotels/order' )->getCartInfo ( $params );
                }
            }
            $subId = $params ['subid'];
            Mage::getSingleton ( 'core/session' )->setFromdate ( $fromDate );
            Mage::getSingleton ( 'core/session' )->setTodate ( $toDate );
            Mage::getSingleton ( 'core/session' )->setAccomodate ( $peopleCount );
            Mage::getSingleton ( 'core/session' )->setProdId ( $hourlyProductId );
            Mage::getSingleton ( 'core/session' )->setSubId ( $subId );
            Mage::getSingleton ( 'core/session' )->setSecurityFee ( $securityFee );
            /**
             * Set Subscription type to session
             */
            Mage::getModel ( 'airhotels/order' )->getSubscriptionInfo ( $subId ); 
            if (isset ( $params ['qty'] )) {                
                /**
                 * Calculating no of days
                 */
                $day = 86400;
                $start = strtotime ( $fromDate );
                $end = strtotime ( $toDate );
                $daysBetween = round ( ($end - $start) / $day ) + 1;
                $params ['qty'] = $daysBetween;
                if (Mage::helper ( 'airhotels/product' )->getHourlyEnabledOrNot () != 0) {
                    $start = strtotime ( $fromDate );
                    $end = strtotime ( $toDate );
                    $daysBetween = ceil ( abs ( $end - $start ) / 86400 );
                    $params ['qty'] = $daysBetween;
                }
            }
            if (isset ( $overallTotalHours ) && ($propertyTime == $propertyTimeData && $hourlyEnabledOrNot == 0)) { 
               $params ['qty'] = $overallTotalHours;                   
            }            
            $product = $this->_initProduct ();
            $related = $this->getRequest ()->getParam ( 'related_product' );
            /**
             * Check product availability
             */
            $this->getCartGoBack($product);            
            $cart->addProduct ( $product, $params );
            $cart = $this->cartEmpty($related,$cart);            
            $cart->save ();
            $this->_getSession ()->setCartWasUpdated ( true );
            Mage::dispatchEvent ( 'checkout_cart_add_product_complete', array (
                    'product' => $product,
                    'request' => $this->getRequest (),
                    'response' => $this->getResponse () 
            ) );
            if (! $this->_getSession ()->getNoCartRedirect ( true )) {
                if (! $cart->getQuote ()->getHasError ()) {
                    $message = $this->__ ( '%s has been booked.', Mage::helper ( 'core' )->htmlEscape ( $product->getName () ) );
                    Mage::getSingleton ( 'core/session' )->setSuccessMessage ( $message );
                }
                $this->_goBack ();
            }
        } catch ( Mage_Core_Exception $e ) {
            if ($this->_getSession ()->getUseNotice ( true )) {
                $this->_getSession ()->addNotice ( $e->getMessage () );
            } else {
                $messages = array_unique ( explode ( "\n", $e->getMessage () ) );
                foreach ( $messages as $message ) {
                    $this->_getSession ()->addError ( $message );
                }
            }
            $url = $this->_getSession ()->getRedirectUrl ( true );
            $this->redirectCartUrl($url);            
        } catch ( Exception $e ) {
            $this->_getSession ()->addException ( $e, $this->__ ( 'The Property cannot be Booked.' ) );
            Mage::logException ( $e );
            $this->_goBack ();
        }
    }
    /**
     * Function Name: redirectCartUrl
     * 
     * redirect url
     */
    public function redirectCartUrl($url){
        if ($url) {
            $this->getResponse ()->setRedirect ( $url );
        } else {
            $this->_redirectReferer ( Mage::helper ( 'checkout/cart' )->getCartUrl () );
        }
    }
    /**
     * Function Name: getCartGoBack
     * 
     * Redirect to back url
     */
    public function getCartGoBack($product){
        if (! $product) {
            $this->_goBack ();
            return;
        }
    }
    /**
     * Function name: cartEmpty()
     */
    public function cartEmpty($related,$cart){
        if (! empty ( $related )) {
            $cart->addProductsByIds ( explode ( ',', $related ) );
        }
        return $cart;
    }
    /**
     * Function Name: _goBack
     * Rewrite _goBack Url
     * (non-PHPdoc)
     *
     * @see Mage_Checkout_CartController::_goBack()
     */
    protected function _goBack() {
        $returnUrl = $this->getRequest ()->getParam ( 'return_url' );
        /**
         * Check condition If $returnUrl exist or not
         */
        if ($returnUrl) {
            if (! $this->_isUrlInternal ( $returnUrl )) {
                throw new Mage_Exception ( 'External urls redirect to "' . $returnUrl . '" denied!' );
            }
            /**
             * In session Set message true
             */
            $this->_getSession ()->getMessages ( true );
            $this->getResponse ()->setRedirect ( $returnUrl );
        } elseif (! Mage::getStoreConfig ( 'checkout/cart/redirect_to_cart' ) && ! $this->getRequest ()->getParam ( 'in_cart' ) && $backUrl = $this->_getRefererUrl ()) {
            $this->getResponse ()->setRedirect ( $backUrl );
        } else {
            /**
             * Check condition action name is add and is not equal to param "in_cart"
             */
            if (($this->getRequest ()->getActionName () == 'add') && ! $this->getRequest ()->getParam ( 'in_cart' )) {
                $this->_getSession ()->setContinueShoppingUrl ( $this->_getRefererUrl () );
            }
            /**
             * Redirect to onestepcheckout
             */
            $this->_redirect ( 'onestepcheckout/' );
        }
        return $this;
    /**
     * Rewrite core function for cart page
     *
     * We skip cart page and directly redirect to checkout page
     */
    }
    /**
     * Function to get property over night fee
     * @param unknown $hourlyNightFee
     * @param unknown $hourlyProductId
     */
    public function propertyOvernightFee($hourlyNightFee,$hourlyProductId){
        $propertyOvernightfees = '';
        if ($hourlyNightFee >= 1) {
            $propertyOvernightfees = Mage::helper ( 'airhotels/airhotel' )->getPropertyOverNightFeeByProductId ( $hourlyProductId );
        }
        return $propertyOvernightfees;
    }
}