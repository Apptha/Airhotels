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
 * @copyright     : Copyright (C) 2013 Powered by Apptha
 * @license       : GNU/GPL http://www.gnu.org/licenses/gpl-3.0.html
 * @abstract      : Helper File
 * @Creation Date : January 02,2014
 * @Modified By   : Ramkumar M
 * @Modified Date : January 08,2014
 * */
/*
 * ********************************************************* */

class Apptha_Paypaladaptive_Helper_Data extends Mage_Core_Helper_Abstract {
	
	
	/**
	 * Collect Bookorrent Property data
	*
	* @return array $collection Bookorrent property data
	*/
	
	public function getBookorrentPropertyData() {
	
		$session = Mage::getSingleton('checkout/session');
		$order = Mage::getModel('sales/order');
		$order->loadByIncrementId($session->getLastRealOrderId());
		$orderId = $order->getId();
		$orderItem = Mage::getModel('sales/order_item');
		$orderItem->load($orderId,'order_id');
		$fromDate = Mage::getSingleton ( 'core/session' )->getFromdate ();
		$toDate = Mage::getSingleton ( 'core/session' )->getTodate ();
		$subId = Mage::getSingleton('core/session')->getSubId(); 
		$serviceFee = Mage::getSingleton('core/session')->getserviceFee();
		$hourlyEnabledOrNot = Mage::helper('airhotels/product')->getHourlyEnabledOrNot();
		/**
		*	Checking the Date	
		*/
		$curDate		=   Mage::getModel('core/date')->date('Y-m-d');
		$time			= 	Mage::getModel('core/date')->date('H:i:s');
 		$endingDate 	=   date('Y-m-d\T'.$time.'\Z',strtotime($toDate));
		

 		if(strtotime($curDate) == strtotime($fromDate)){
 			$startingDate = date('Y-m-d\TH:i:s\Z');
 		}else{
 			$time = '07:01:00';
 			$startingDate 	=  date('Y-m-d\T'.$time.'\Z',strtotime($fromDate));
 		}

		/**
		 * Calculate maximum number of payments
		 */
		$diff = abs(strtotime($fromDate) - strtotime($toDate));
		
		$yearCount 	= floor($diff / (365*60*60*24));
		$monthCount = floor(($diff - $yearCount * 365*60*60*24) / (30*60*60*24));
		$dayCount 	= floor(($diff - $yearCount * 365*60*60*24 - $monthCount*30*60*60*24)/ (60*60*24));
		//$dayCount  += 1;
		
		/**
		 * preapproval startdate and enddate
		 */
		if($subId == 0){
			
			$billPeriodUnit 		= '1';
			$billPeriodFrequency 	= '1';
			$billPeriodCycles		= (int) ($dayCount * $billPeriodFrequency);
			
			
			
			
		}else{
		$collections = Mage::getModel ('airhotels/subscriptiontype' )->getCollection()
					  ->addFieldToFilter ('id', $subId );
		foreach($collections as $collection) {
			
		/**
		*	Billing Informations
		*/
		$billPeriodUnit 		= $collection['billing_period_unit'];
		$billPeriodFrequency 	= $collection['billing_frequency'];
		$billPeriodCycles 		= $collection['billing_cycle'];

		/**
		 * Find the maxium billing Cycles
		 */
		if($billPeriodFrequency == 1 ){
		 switch($billPeriodUnit){
			case 1:
				$billPeriodCycles		= (int) ($dayCount * $billPeriodFrequency);
				break;
			case 2:
				$weekCount				= (int) $dayCount/7;
				$billPeriodCycles		= (int) ($weekCount *  $billPeriodFrequency);
				break;
			case 4:
				$billPeriodCycles		= (int) ($monthCount * $billPeriodFrequency);
				break;
			case 5:
				$billPeriodCycles		= (int) ($yearCount * $billPeriodFrequency);
				break;
		  }
		}else{
			switch($billPeriodUnit){
				case 1:
					$billPeriodCycles		= (int) ($dayCount / $billPeriodFrequency);
					break;
				case 2:
					$weekCount	= (int) $dayCount/7;
					$billPeriodCycles		= (int) ($weekCount /  $billPeriodFrequency);
					break;
				case 4:
					$billPeriodCycles		= (int)  ($monthCount / $billPeriodFrequency);
					break;
				case 5:
					$billPeriodCycles		= (int) ($yearCount / $billPeriodFrequency);
					break;
			}
		}	
		Mage::getSingleton('core/session')->setBillPeriodCycles($billPeriodCycles);
		}
	  }
		/**
		 * Calculate maximum amount per payment
		*/
		 
		$productId = $orderItem->getProductId();
		Mage::getSingleton('core/session')->setProductId($productId);
		$product = Mage::getModel('catalog/product')->load($productId);
		$userId = $product->getUserid();
		$collection = Mage::getModel('airhotels/customerphoto')->load($userId,'customer_id');
		$customer_paypal_id = $collection['paypal_email'];
		$price = $orderItem->getOriginalPrice();
		
		$sku = $orderItem->getSku();
		$productId = $orderItem->getProductId();
// 		$rowTotal = $orderItem->getRowTotal();
// 		$rowTotal += $serviceFee;
		$rowTotal = $order->getData('grand_total');
		$belowSubscription = 0;
		$totalAmount 	=	$rowTotal * $billPeriodCycles;
		Mage::getSingleton('core/session')->setProductAmount(round($rowTotal,2));
		if($hourlyEnabledOrNot == 0 ){ 
			$billPeriodCycles = $billPeriodCycles;
		}
		
		$propertyData = array();
		$propertyData['start_date'] 		= $startingDate;
		$propertyData['ending_date'] 		= $endingDate;
		$propertyData['payments_count'] 	= $billPeriodCycles;
		$propertyData['maximum_amount'] 	= round($rowTotal,2);
		$propertyData['below_subscription'] = $belowSubscription;
		$propertyData['product_id'] 		= $productId;
		$propertyData['price'] 				= $price;
		$propertyData['total_amount'] 		= $totalAmount;
		$propertyData['paypal_id'] 			= $customer_paypal_id;
		$propertyData['period_unit'] 		= $billPeriodUnit;
		$propertyData['processing_fee'] 	= $serviceFee;

		return $propertyData;
		
	}
	
	
	

    /**
     * Getting Marketplace extenstion installed or not
     */     
    public function getModuleInstalledStatus($moduleName) {
        $modules = Mage::getConfig()->getNode('modules')->children();
        $modulesArray = (array) $modules;
        
        if (isset($modulesArray[$moduleName])) {
        if($moduleName == 'Apptha_Marketplace'){     
        return Mage::getStoreConfig('marketplace/marketplace/activate');    
        }          
        } else {
        return 0;    
        }
    }
    
     // Getting commission percent value
    public function getCommissionPercent() {
        return Mage::getStoreConfig('marketplace/marketplace/percentperproduct');
    }    

    // Getting payment description    
    public function getPaymentDescription() {
        return Mage::getStoreConfig('payment/paypaladaptive/description');
    }   
    
    // Getting refund enable or not
    public function getRefundStatus() {
        return Mage::getStoreConfig('payment/paypaladaptive/order_refund');
    }
    
    // Getting refund enable or not
    public function getPaymentMethod() {
        return Mage::getStoreConfig('payment/paypaladaptive/payment');
    }
    
    // Getting refund enable or not
    public function getFeePayer() {
        return Mage::getStoreConfig('payment/paypaladaptive/feepayer');
    }

    // Getting order status
    public function getOrderStatus() {
        return Mage::getStoreConfig('payment/paypaladaptive/order_status');
    }

    // Getting successful order status
    public function getOrderSuccessStatus() {
        return Mage::getStoreConfig('payment/paypaladaptive/order_success');
    }

    // Getting payment mode
    public function getPaymentMode() {
        return Mage::getStoreConfig('payment/paypaladaptive/sandbox');
    }

    // Getting API username
    public function getApiUserName() {
        return Mage::getStoreConfig('payment/paypaladaptive/paypal_api_username');
    }

    // Getting API password
    public function getApiPassword() {
        return Mage::getStoreConfig('payment/paypaladaptive/paypal_api_password');
    }

    // Getting API signature
    public function getApiSignature() {
        return Mage::getStoreConfig('payment/paypaladaptive/paypal_api_signature');
    }

    // Getting API Id
    public function getAppID() {
        return Mage::getStoreConfig('payment/paypaladaptive/paypal_app_id');
    }

    // Getting Grand Total
    public function getGrandTotal() {
        $session = Mage::getSingleton('checkout/session');
        $order = Mage::getModel('sales/order');
        return $order->loadByIncrementId($session->getLastRealOrderId())->getGrandTotal();
    }

    // Getting admin paypal id
    public function getAdminPaypalId() {
        return Mage::getStoreConfig('payment/paypaladaptive/merchant_paypal_mail');
    }

    // Calculating defualt seller share
    public function getSellerData() {

        // Getting last order data
        $session = Mage::getSingleton('checkout/session');
        $incrementId = $session->getLastRealOrderId();
        $order = Mage::getModel('sales/order');
        $order->loadByIncrementId($incrementId);
        $orderId = $order->getId();
        if (!empty($orderId)) {

            $items = $order->getAllItems();

            $sellerData = array();

            // Preparing seller share 
            foreach ($items as $item) {
                $sellerAmount = 0;
                $productId = $item->getProductId();

                $productData = Mage::getModel('paypaladaptive/productdetails')->getCollection()
                        ->addFieldToFilter('product_id', $productId);
                                       
                $firstRow = $this->getFirstRowData($productData);             
                if (!empty($firstRow['product_paypal_id']) && $firstRow['is_enable'] == 1) {
                    $sellerId = $firstRow['product_paypal_id'];
                    $commisionValue = $firstRow['share_value'];
                    $commissionMode = $firstRow['share_mode'];

                    Mage::getModel('paypaladaptive/save')->saveCommissionData($incrementId, $productId, $commisionValue, $commissionMode, $sellerId);

                    $productAmount = $item->getPrice() * $item->getQtyToInvoice();

                    if ($commissionMode == 'percent') {
                        $productCommission = $productAmount * ($commisionValue / 100);
                        $sellerAmount = $productAmount - $productCommission;
                    } else {
                        $productCommission = $commisionValue;
                        $sellerAmount = $productAmount - $commisionValue;
                    }                  
        

                    // Calculating seller share individually
                    if (array_key_exists($sellerId, $sellerData)) {
                        $sellerData[$sellerId]['amount'] = $sellerData[$sellerId]['amount'] + $sellerAmount;
                        $sellerData[$sellerId]['commission_fee'] = $sellerData[$sellerId]['commission_fee'] + $productCommission;
                    } else {
                        $sellerData[$sellerId]['amount'] = $sellerAmount;
                        $sellerData[$sellerId]['commission_fee'] = $productCommission;
                        $sellerData[$sellerId]['seller_id'] = $sellerId;
                    }
                }
            }
            return $sellerData;
        } else {
            Mage::getSingleton('checkout/session')->addError(Mage::helper('paypaladaptive')->__("No order for processing found"));
            $this->_redirect('checkout/cart');
            return;
        }
    }
    
     // Calculating Marketplace seller share
    public function getMarketplaceSellerData() {
        
           // Getting last order data
        $session = Mage::getSingleton('checkout/session');
        $incrementId = $session->getLastRealOrderId();
        $order = Mage::getModel('sales/order');
        $order->loadByIncrementId($session->getLastRealOrderId());
        $orderId = $order->getId();
        if (!empty($orderId)) {

            $items = $order->getAllItems();

            $sellerData = array();

            // Preparing seller share 
            foreach ($items as $item) {
                $sellerAmount = 0;
                $productId = $item->getProductId();           
                
                $sellerProductData = Mage::getModel('catalog/product')->getCollection()->addAttributeToFilter('entity_id',$productId)->addAttributeToSelect('*')->setPageSize(1);
                $product = $this->getFirstRowData($sellerProductData);
                
                $marketplaceGroupId = Mage::helper('marketplace')->getGroupId();
                $productGroupId = $product->getGroupId();

                if ($marketplaceGroupId == $productGroupId) {                    
                    $sellerId = $this->getMarketplaceSellerPaypalId($product->getSellerId());
                    
                    if(empty($sellerId)){
                    Mage::getSingleton('checkout/session')->addError(Mage::helper('paypaladaptive')->__("Please contact admin partner paypal id is required"));   
                    $this->_redirect('checkout/cart');
                    return;                    
                    }
                    
                    $productAmount = $item->getPrice() * $item->getQtyToInvoice();
                    $percentPerProduct = Mage::getStoreConfig('marketplace/marketplace/percentperproduct');
                    $productCommission = $productAmount * ($percentPerProduct / 100);
                    $sellerAmount = $productAmount - $productCommission;
                    
                    Mage::getModel('paypaladaptive/save')->saveCommissionData($incrementId, $productId, $percentPerProduct, 'percent', $sellerId);
                    
                    
                    // Calculating seller share individually
                    if (array_key_exists($sellerId, $sellerData)) {
                        $sellerData[$sellerId]['amount'] = $sellerData[$sellerId]['amount'] + $sellerAmount;
                        $sellerData[$sellerId]['commission_fee'] = $sellerData[$sellerId]['commission_fee'] + $productCommission;
                    } else {
                        $sellerData[$sellerId]['amount'] = $sellerAmount;
                        $sellerData[$sellerId]['commission_fee'] = $productCommission;
                        $sellerData[$sellerId]['seller_id'] = $sellerId;
                    }
                }
            }

            return $sellerData;
        } else {
            Mage::getSingleton('checkout/session')->addError(Mage::helper('paypaladaptive')->__("No order for processing found"));
            $this->_redirect('checkout/cart');
            return;
        }     
    }
    
    // Getting marketplace seller paypal id
    public function getMarketplaceSellerPaypalId($seller_id) {
        $collection   = Mage::getModel('marketplace/sellerprofile')->getCollection()
                        ->addFieldToFilter('seller_id',$seller_id);
        foreach($collection as $data){
           return $data['paypal_id'];
        }  
    }
    
     // Getting first row data from collection 
     public function getFirstRowData($collections) {
        foreach($collections as $collection){
        return $collection;            
        }    
    }    
    
    
    public function getBookorrentHostData() {
    	/**
    	 * Getting last order data
    	*/
    	$session = Mage::getSingleton('checkout/session');
    	$incrementId = $session->getLastRealOrderId();
    	$order = Mage::getModel('sales/order');
    	$order->loadByIncrementId($session->getLastRealOrderId());
    	$orderId = $order->getId();
    	$sellerData = array();
    	if (!empty($orderId)) {
    
    		$items = $order->getAllItems();
    
    		/**
    		 * Prepare host share
    		 */
    		foreach ($items as $item) {
    			$sellerAmount = 0;
    			$productId = $item->getProductId();

    			$sellerProductData = Mage::getModel('catalog/product')->getCollection()->addAttributeToFilter('entity_id', $productId)->addAttributeToSelect('*')->setPageSize(1);				
    			$product = $this->getFirstRowData($sellerProductData);
    			/**
    			 * changes
    			*/	
    			$customerIdentifier = $product->getUserid();
    			$collection = Mage::getModel('airhotels/customerphoto')->getCollection()
    			->addFieldToFilter('customer_id',$customerIdentifier);    
    			$emailCol = $collection->getData();
    			foreach($emailCol as $_emai){    
    				$customer_paypal_id = $_emai['paypal_email'];
    			}    
    			$sellerId = $customer_paypal_id;
    			if (empty($sellerId)) {
    				Mage::getSingleton('checkout/session')->addError(Mage::helper('paypaladaptive')->__("Please contact admin partner paypal id is required"));
    				$url = Mage::getUrl('checkout/cart', array('_secure' => true));
    				Mage::app()->getResponse()->setRedirect($url);
    				return FALSE;
    			} else {    			    
    				$processingFee 	= Mage::getSingleton('core/session')->getserviceFee();
    				$productAmount 	= round(Mage::helper('paypaladaptive')->getGrandTotal(), 2);
    				//$productAmount 	= Mage::getSingleton('core/session')->getProductAmount();
    				$grandTotal		= $productAmount;
    				$productAmount -= $processingFee;    				
    				$percentPerProduct  = Mage::getStoreConfig('airhotels/custom_group/airhotels_hostfee');
    				$productCommission  = round ( ($productAmount/100) * ($percentPerProduct),2 );
    				$sellerAmount   = $productAmount - $productCommission;
    				//$sellerAmount   = $productAmount - $productCommission;
    				Mage::getModel('paypaladaptive/save')->saveCommissionData($incrementId, $productId, $percentPerProduct, 'percent', $sellerId,$processingFee, $productAmount);
    				/**
    				 * Calculate seller share individually
    				 */
    				if (array_key_exists($sellerId, $sellerData)) {
    					$sellerData[$sellerId]['amount'] = $sellerData[$sellerId]['amount'] + $sellerAmount;
    					$sellerData[$sellerId]['commission_fee'] = $sellerData[$sellerId]['commission_fee'] + $productCommission;
    				} else {
    					$sellerData[$sellerId]['amount'] 			= $sellerAmount;
    					$sellerData[$sellerId]['commission_fee'] 	= $productCommission;
    					$sellerData[$sellerId]['seller_id'] 		= $sellerId;
    					$sellerData[$sellerId]['processing_fee'] 	= $processingFee;
    					$sellerData[$sellerId]['grand_total'] 		= $grandTotal;
    					
    				}    				
    			}
    		}
    	} else {
    		Mage::getSingleton('checkout/session')->addError(Mage::helper('paypaladaptive')->__("No order for processing found"));
    		$url = Mage::getUrl('checkout/cart', array('_secure' => true));
    		Mage::app()->getResponse()->setRedirect($url);
    		return FALSE;
    	}
    	return $sellerData;
    }    
}