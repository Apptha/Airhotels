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
 * @version     0.2.4
 * @author      Apptha Team <developers@contus.in>
 * @copyright   Copyright (c) 2014 Apptha. (http://www.apptha.com)
 * @license     http://www.apptha.com/LICENSE.txt
 * 
 */
class Apptha_Airhotels_Block_Property_Invitefriends extends Mage_Catalog_Block_Product_Abstract {
 /**
  * Google invite friends return action
  */
 function curl($url, $post = "") {
  $curl = curl_init ();
  $userAgent = 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; .NET CLR 1.1.4322)';
  curl_setopt ( $curl, CURLOPT_URL, $url );
  /**
   * The URL to fetch.
   * This can also be set when initializing a session with curl_init().
   */
  curl_setopt ( $curl, CURLOPT_RETURNTRANSFER, TRUE );
  /**
   * TRUE to return the transfer as a string of the return value of curl_exec() instead of outputting it out directly.
   */
  curl_setopt ( $curl, CURLOPT_CONNECTTIMEOUT, 5 );
  /**
   * The number of seconds to wait while trying to connect.
   */
  if ($post != "") {
   curl_setopt ( $curl, CURLOPT_POST, 5 );
   curl_setopt ( $curl, CURLOPT_POSTFIELDS, $post );
  }
  curl_setopt ( $curl, CURLOPT_USERAGENT, $userAgent );
  /**
   * The contents of the "User-Agent: " header to be used in a HTTP request.
   */
  curl_setopt ( $curl, CURLOPT_FOLLOWLOCATION, TRUE );
  /**
   * To follow any "Location: " header that the server sends as part of the HTTP header.
   */
  curl_setopt ( $curl, CURLOPT_AUTOREFERER, TRUE );
  /**
   * To automatically set the Referer: field in requests where it follows a Location: redirect.
   */
  curl_setopt ( $curl, CURLOPT_TIMEOUT, 10 );
  /**
   * The maximum number of seconds to allow cURL functions to execute.
   */
  curl_setopt ( $curl, CURLOPT_SSL_VERIFYPEER, FALSE );
  /**
   * To stop cURL from verifying the peer's certificate.
   */
  $contents = curl_exec ( $curl );
  curl_close ( $curl );
  return $contents;
 }
 /**
  * Getting friends details from google plus Api
  * 
  * @return multitype:multitype:NULL unknown
  */
 function getinvitefriends() {
  $clientId = Mage::getStoreConfig ( 'airhotels/google/google_id' );
  $clientSecret = Mage::getStoreConfig ( 'airhotels/google/google_secret' );
  $redirectUri = Mage::getStoreConfig ( 'airhotels/google/google_redirecturl' );
  /**
   * Getting count of google contacts from admin->airhotels->configuration->Google Invite Friends
   * Number of mailid you want to display.
   */
  $maxResults = Mage::getStoreConfig ( 'airhotels/google/invite_friends' );
  $authcode = $_GET ["code"];
  $fields = array (
    'code' => urlencode ( $authcode ),
    'client_id' => urlencode ( $clientId ),
    'client_secret' => urlencode ( $clientSecret ),
    'redirect_uri' => urlencode ( $redirectUri ),
    'grant_type' => urlencode ( 'authorization_code' ) 
  );
  
  $post = '';
  foreach ( $fields as $key => $value ) {
   $post .= $key . '=' . $value . '&';
  }
  $post = rtrim ( $post, '&' );
  $result = $this->curl ( 'https://accounts.google.com/o/oauth2/token', $post );
  $response = json_decode ( $result );
  $accesstoken = $response->access_token;
  $url = 'https://www.google.com/m8/feeds/contacts/default/full?max-results=' . $maxResults . '&alt=json&v=3.0&oauth_token=' . $accesstoken;
  $xmlresponse = $this->curl ( $url );
  $contacts = json_decode ( $xmlresponse, true );
  $return = array ();
  if (! empty ( $contacts ['feed'] ['entry'] )) {
   foreach ( $contacts ['feed'] ['entry'] as $contact ) {
    if (isset ( $contact ['link'] [0] ['href'] )) {
     $url = $contact ['link'] [0] ['href'];
     $url = $url . '&access_token=' . urlencode ( $accesstoken );
     $image = $this->curl ( $url );
    }
    /**
     * retrieve Name and email address and image from Api
     */
    $return [] = array (
      'name' => $contact ['title'] ['$t'],
      'email' => $contact ['gd$email'] [0] ['address'],
      'image' => $image 
    );
   }
  }
  return $return;
 }
 /**
  * Prepares the layout
  *
  * @see Mage_Catalog_Block_Product_Abstract::_prepareLayout()
  */
 protected function _prepareLayout() {
     /**
      * Calling the parent Construct Method.
      */
     parent::_prepareLayout ();
     /**
      * Getting property listings
      */
     $varTabValue = $this->getRequest()->getParam('tab');
 
     if($varTabValue == 'credited_amount' || empty($varTabValue)){
         $listingCollection = $this->getInviteCredithistory ();
     }elseif($varTabValue == 'debited_amount'){
         $listingCollection = $this->getInviteDebithistory ();
     }
     /**
      * setting pager
      */
     $pager = $this->getLayout ()->createBlock ( 'page/html_pager', 'my.pager' )->setCollection ( $listingCollection );
     $pager->setChild ( 'pager', $pager );
     $this->setChild ( 'pager', $pager );
     
    
 
     return $this;
 }
 /**
  * Get credit amount collection
  *
  * @return collection
  */
 
 public function getInviteCredithistory(){
     /**'
      * Get customer Id
      * @var $customerId
      */
     $customerId = Mage::getSingleton('customer/session')->getCustomer()->getId();
     /**
      * Get parameter limit and page
      * @var $limit
      * @var $page
      */
     $limit = empty($this->getRequest()->getParam('limit')) ? '10' : $this->getRequest()->getParam('limit');
     $page = empty($this->getRequest()->getParam('p')) ? '1' : $this->getRequest()->getParam('p');
     /**
      * Get credited discount amount collection
      *
      * @var $creidtcollection
      */
     $creidtcollection = Mage::getModel('airhotels/invitefriendsorder')->getCollection()->addFieldToFilter('invitee_id', array('eq' => $customerId))->addFieldToFilter('customer_id', array('neq' => 0));
     $creidtcollection->setPageSize($limit) ->setCurPage($page);
     
     return $creidtcollection;
 }
 
 public function getInviteDebithistory(){
     /**'
      * Get customer Id
      * @var $customerId
      */
     $customerId = Mage::getSingleton('customer/session')->getCustomer()->getId();
     /**
      * Get parameter limit and page
      * @var $limit
      * @var $page
      */
     $limit = empty($this->getRequest()->getParam('limit')) ? '10' : $this->getRequest()->getParam('limit');
     $page = empty($this->getRequest()->getParam('p')) ? '1' : $this->getRequest()->getParam('p');
     /**
      * Get debited discount amount collection
      *
      * @var $debitCollection
      */
     $debitCollection = Mage::getModel('airhotels/invitefriendsorder')->getCollection()->addFieldToFilter('invitee_id', array('eq' => $customerId))->addFieldToFilter('customer_id', array('eq' => 0))->addFieldToFilter('order_status', array('neq' => 0));
     $debitCollection->setPageSize($limit) ->setCurPage($page);
     /**
      * Add pagination
      *
      * @var $pager
      */
     $pager = $this->getLayout ()->createBlock ( 'page/html_pager', 'my.pager' )->setCollection ( $debitCollection );
     $this->setChild ( 'pager', $pager );
 
     return $debitCollection;
 }
 /**
  * Function pager html.
  */
 public function getPagerHtml()
 {
     return $this->getChildHtml('pager');
 }
}