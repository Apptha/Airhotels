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
 * Managesubscriptions Model class
 */
class Apptha_Airhotels_Model_Managesubscriptions extends Mage_Core_Model_Abstract {
 /**
  * Construct Method
  * 
  * @see Mage_Core_Model_Resource_Abstract::_construct()
  */
 public function _construct() {
  /**
   * Note that the airhotels_id refers to the key field in your database table.
   * Initializing managesubscriptions Block.
   */
  $this->_init ( 'airhotels/managesubscriptions' );
 }
 /**
  * Function Name: saveSubscriptionInfo
  * 
  * Save subscription information to database
  */
 public function saveSubscriptionInfo($data,$productStatus){
     /**
      * Create the Product Colletion
      */
     $isSubscriptionOnly = $startDate = null;    
     $isSubscriptionOnly = $data ['is_subscription_only'];
     $startDate = $data ['start_date'];       
     /**
      * Getting Start date from request param
      * @var unknown
      */     
     $manageModel = $manageSubscriptionsModel = array ();
     $manageProductId = $manageSubscriptionsModel = '';
     /**
      * Create the Manage Subsriptions
      */
     $manageSubscriptionsModel = Mage::getModel ( 'airhotels/managesubscriptions' )->getCollection ();
     $manageModel = $manageSubscriptionsModel->getData ();
     if (! empty ( $manageModel )) {         
         /**
          * Get collection from manage subscription.
          */
         $manageModel = Mage::getModel ( 'airhotels/managesubscriptions' )->getCollection ()->addFieldToFilter ( 'product_id', array (
                 'eq' => $data['product_id']
         ) )->getData ();
         foreach ( $manageModel as $manageModelData ) {
             $manageProductId = $manageModelData ['product_id'];
         }
         if ($manageProductId == $data['product_id']) {
             /**
              * Get Mopdel for managesubsriptions based on ProductID
              */
             $model = Mage::getModel ( 'airhotels/managesubscriptions' )->load ( $data['product_id'], 'product_id' );
             $model->setData ( 'is_subscription_only', $isSubscriptionOnly );
             $model->setData ( 'start_date', $startDate );
             $model->setData ( 'product_status', $productStatus );
             $model->save ();             
             $getId = $model->getId();
         } else {
             /**
              * Get Model for managesubsriptions with save data
              */
             $model = Mage::getModel ( 'airhotels/managesubscriptions' );
             /**
              * Set values for is_subscription_only,start_date and product_status
              */
             $model->setData ( 'product_id', $data['product_id'] );
             $model->setData ( 'is_subscription_only', $isSubscriptionOnly );
             $model->setData ( 'start_date', $startDate );
             $model->setData ( 'product_status', $productStatus );
             $model->save ();
             $getId = $model->getId();
         }
     } else {
         /**
          * Get Model for managesubsriptions with save data
          */
         $model = Mage::getModel ( 'airhotels/managesubscriptions' );
         /**
          * Set values for is_subscription_only,start_date and product_status
          */
         $model->setData ( 'product_id', $data['product_id'] );
         $model->setData ( 'is_subscription_only', $isSubscriptionOnly );
         $model->setData ( 'start_date', $startDate );
         $model->setData ( 'product_status', $productStatus );
         $model->save ();
         $getId = $model->getId();
     }
     /**
      * return $getId
      */
     return $getId;
 } 
}