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

class Apptha_Paypaladaptive_AdaptiveController extends Mage_Core_Controller_Front_Action {
    var $subId;
	/**
	 * Apptha payPal adaptive payment action
	 */
	public function redirectAction() {        
		$subId	= Mage::getSingleton('core/session')->getSubId();		
		if($subId == 0){
			
			$this->noSubscription();
		} else {
		/**
		 *  Checking whether order id available or not
		 */
		$propertyData = Mage::helper('paypaladaptive')->getBookorrentPropertyData();
		if (empty($propertyData['paypal_id'])) {
			Mage::getSingleton('checkout/session')->addError(Mage::helper('paypaladaptive')->__("This property doesn't support paypal adaptive payment choose someother payments"));
			$url = Mage::getUrl('checkout/cart', array('_secure' => true));
			Mage::app()->getResponse()->setRedirect($url);
			return FALSE;
		}		 
		/**
		 *  Checking whether preapproval and below subscription enable or not
		 */
		if($propertyData['below_subscription'] != 1){
			$returnUrl = Mage::getUrl('paypaladaptive/adaptive/response', array('_secure' => true));
			$cancelUrl = Mage::getUrl('paypaladaptive/adaptive/cancel', array('_secure' => true));
			$ipnNotificationUrl = Mage::getUrl('paypaladaptive/adaptive/ipnnotification', array('_secure' => true));
			$currencyCode 				 = Mage::app()->getStore()->getCurrentCurrencyCode();
			$startingDate				 = $propertyData['start_date'];
			$endingDate 				 = $propertyData['ending_date'];
			$maxAmountPerPayment 		 = $propertyData['maximum_amount']; 
			$maxNumberOfPayments 		 = $propertyData['payments_count'];
			$maxTotalAmountOfAllPayments = $propertyData['total_amount'];
			//$maxNumberOfPaymentsPerPeriod = $propertyData['payments_count'];
			$periodUnit 				 = $propertyData['period_unit'];
			//$pinType					 = 'REQUIRED'; 
			Mage::getSingleton('core/session')->setEndingDate($endingDate);			
			$senderEmail = '';
			$preResArray = Mage::getModel('paypaladaptive/apicall')->CallPreapproval($returnUrl,$cancelUrl,$ipnNotificationUrl,$senderEmail,$currencyCode,$startingDate,$endingDate,$maxAmountPerPayment,$maxNumberOfPayments,$maxTotalAmountOfAllPayments,$periodUnit);			
			$ack = strtoupper($preResArray["responseEnvelope.ack"]);
			Mage::getSingleton('core/session')->setPreapprovalKey($preResArray["preapprovalKey"]);			
			if ($ack == "SUCCESS") {
				$cmd = "cmd=_ap-preapproval&preapprovalkey=" . urldecode($preResArray["preapprovalKey"]);
				$this->RedirectToPayPal($cmd);
			}
			else{
				$errorMsg = urldecode($preResArray["error(0).message"]);
				Mage::getSingleton('checkout/session')->addError(Mage::helper('paypaladaptive')->__("Pay API call failed."));
				Mage::getSingleton('checkout/session')->addError(Mage::helper('paypaladaptive')->__("Error Message:") . ' ' . $errorMsg);
				$this->_redirect('checkout/cart', array('_secure' => true));
				return;
			}	
		}
		else{
			Mage::getSingleton('core/session')->unsPreapprovalKey();
			$this->_redirect('paypaladaptive/adaptive/response');
		}
	}
}	
	/**
	 * Payment response action
	 */
	public function responseAction() {
		$subId			= Mage::getSingleton('core/session')->getSubId();
		$preapprovalKey = Mage::getSingleton('core/session')->getPreapprovalKey();
		$storeId 		= Mage::app()->getStore()->getStoreId();
		$productId 		= Mage::getSingleton('core/session')->getProductId();
		$fromDate 		= Mage::getSingleton ( 'core/session' )->getFromdate ();
		$curDate		= Mage::getModel('core/date')->date('Y-m-d');
		$time			= Mage::getModel('core/date')->date('H:i:s');		
		if($preapprovalKey != ''){
			$senderEmail = '';
			$preApprovalDetails = Mage::getModel('paypaladaptive/apicall')->callPreapprovalDetails($ack,$preapprovalKey);
			$preapprovalAck = $preApprovalDetails['responseEnvelope.ack'];
			$preapprovalCorrelation = $preApprovalDetails['responseEnvelope.correlationId'];
			$preapprovalBuild = $preApprovalDetails['responseEnvelope.build'];
			$preapprovalApproved = $preApprovalDetails['approved'];
			$preapprovalCurPayments = $preApprovalDetails['curPayments'];
			$preapprovalCurPaymentsAmount = $preApprovalDetails['curPaymentsAmount'];
			$preapprovalCurPeriodAttempts = $preApprovalDetails['curPeriodAttempts'];
			$preapprovalCurPeriodEndingDate = $preApprovalDetails['curPeriodEndingDate'];
			$preapprovalCurrencyCode = $preApprovalDetails['currencyCode'];
			$preapprovalDateOfMonth = $preApprovalDetails['dateOfMonth'];
			$preapprovalDayOfWeek = $preApprovalDetails['dayOfWeek'];
			$preapprovalEndingDate = $preApprovalDetails['endingDate'];
			$preapprovalMaximumAmount = $preApprovalDetails['maxAmountPerPayment'];
			$preapprovalMaximumNumber = $preApprovalDetails['maxNumberOfPayments'];
			$preapprovalMaxTotalAmt = $preApprovalDetails['maxTotalAmountOfAllPayments'];
			$preapprovalPaymentPeriod = $preApprovalDetails['paymentPeriod'];
			$preapprovalPinType = $preApprovalDetails['pinType'];
			$preapprovalStartingDate = $preApprovalDetails['startingDate'];
			//$preapprovalStatus 		 = $preApprovalDetails['status'];
			$preapprovalStatus = 'PENDING';
		}

		/**
		 * If customer clicks No subscription
		 */ 
			$collections = Mage::getModel ('airhotels/subscriptiontype' )->getCollection()->addFieldToFilter ('id', $subId );
			/**
			 *	If the fromDate in Future
			 */
			if(strtotime($fromDate) > strtotime($curDate)){
				
				foreach($collections as $collection) {
					
					$billPeriodUnit 		= $collection['billing_period_unit'];
					$billPeriodFrequency 	= $collection['billing_frequency'];
					$billPeriodCycles 		= $collection['billing_cycle'];
					$fromDate 				=   date('Y-m-d\T'.$time.'\Z',strtotime($fromDate));
					
					switch($billPeriodUnit){
						case 1:
							$endDate		= $billPeriodFrequency * 1;
							$dayCount 		= strtotime($fromDate.'+'.$endDate. 'days');
							$endingDate 	= date('Y-m-d\TH:i:s\Z',$dayCount);
							break;
						case 2:
							$endDate		= $billPeriodFrequency * 7 ;
							$dayCount 		= strtotime($fromDate.'+'.$endDate. 'days');
							$endingDate 	= date('Y-m-d\TH:i:s\Z',$dayCount);
							break;
						case 4:
							$endDate		= $billPeriodFrequency * 30 ;
							$dayCount 		= strtotime($fromDate.'+'.$endDate. 'days');
							$endingDate 	= date('Y-m-d\TH:i:s\Z',$dayCount);
							break;
						case 5:
							$endDate		= $billPeriodFrequency * 365 ;
							$dayCount 		= strtotime($fromDate.'+'.$endDate. 'days');
							$endingDate 	= date('Y-m-d\TH:i:s\Z',$dayCount);
							break;
					}
				}
				
			}	else {	
			foreach($collections as $collection) {
			$billPeriodUnit 		= $collection['billing_period_unit'];
			$billPeriodFrequency 	= $collection['billing_frequency'];
			$billPeriodCycles 		= $collection['billing_cycle'];
			switch($billPeriodUnit){
				case 1:
					$endDate		= $billPeriodFrequency * 1;
					$dayCount 		= strtotime('+'.$endDate. 'days');
					$endingDate 	= date('Y-m-d\TH:i:s\Z',$dayCount);
					break;
				case 2:
					$endDate		= $billPeriodFrequency * 7 ;
					$dayCount 		= strtotime('+'.$endDate. 'days');
					$endingDate 	= date('Y-m-d\TH:i:s\Z',$dayCount);
					break;
				case 4:
					$endDate		= $billPeriodFrequency * 30 ;
					$dayCount 		= strtotime('+'.$endDate. 'days');
					$endingDate 	= date('Y-m-d\TH:i:s\Z',$dayCount);
					break;
				case 5:
					$endDate		= $billPeriodFrequency * 365 ;
					$dayCount 		= strtotime('+'.$endDate. 'days');
					$endingDate 	= date('Y-m-d\TH:i:s\Z',$dayCount);
					break;
			}
		  }
		}
		
		if(strtotime($curDate) == strtotime($fromDate)){
			$preapprovalCurPeriodEndingDate = $endingDate;
		}else{
			$time = '07:01:00';
			$preapprovalCurPeriodEndingDate 	=  date('Y-m-d\T'.$time.'\Z',strtotime($fromDate));
		}
		

		
		/**
		 * Getting  the Customer Id 
		 */
		if(Mage::getSingleton('customer/session')->isLoggedIn()) {
			$customerData = Mage::getSingleton('customer/session')->getCustomer();
			$custId	= $customerData->getId();		
		}		
		$session = Mage::getSingleton('checkout/session');
		$order = Mage::getModel('sales/order');
		$order->loadByIncrementId($session->getLastRealOrderId());
		$orderId = $order->getId();
		$orderStatus = $order->getStatus();
		if($preapprovalKey != ''){
			try{
			Mage::getModel('paypaladaptive/save')->savePreapprovalData($preapprovalKey,  $preapprovalCurPayments, $preapprovalCurPaymentsAmount, $preapprovalCurPeriodAttempts, $preapprovalCurPeriodEndingDate,  $preapprovalDateOfMonth, $preapprovalDayOfWeek, $preapprovalEndingDate, $preapprovalMaximumAmount, $preapprovalMaximumNumber, $preapprovalMaxTotalAmt, $preapprovalPaymentPeriod,  $preapprovalStartingDate, $preapprovalStatus, $session->getLastRealOrderId(), $orderId, $productId,$custId,$storeId,$preapprovalCurrencyCode,$subId,$preapprovalPinType);
			}catch(Exception $e){
				echo 'Error: '.$e->getMessage();
			}	
		  } 
		
		
		if (empty($orderId) || $orderStatus != 'pending') {
			Mage::getSingleton('checkout/session')->addError(Mage::helper('paypaladaptive')->__("No order for processing found"));
			$this->_redirect('checkout/cart', array('_secure' => true));
			return FALSE;
		}
		
		/**
		 * Initilize adaptive payment data
		*/
		$actionType = "CREATE";
		$cancelUrl = Mage::getUrl('paypaladaptive/adaptive/cancel', array('_secure' => true));
		$returnUrl = Mage::getUrl('paypaladaptive/adaptive/return', array('_secure' => true));
		$ipnNotificationUrl = Mage::getUrl('paypaladaptive/adaptive/ipnnotification', array('_secure' => true));
		$currencyCode = Mage::app()->getStore()->getCurrentCurrencyCode();
		$senderEmail = "";
		$feesPayer = Mage::helper('paypaladaptive')->getFeePayer();
		$memo = "";
		$pin = "";
		$reverseAllParallelPaymentsOnError = "";
		$trackingId = $this->generateTrackingID();
		 
		 $enabledMarplace 	= Mage::helper('paypaladaptive')->getModuleInstalledStatus('Apptha_Marketplace');
		 $enabledBookorrent = Mage::helper('paypaladaptive')->getModuleInstalledStatus('Apptha_Bookorrent');
		 $enabledBookorrent = 1;	
		/**
		 * Checking where marketplace enable or not
		*/
		if ($enabledMarplace == 1) {
			/**
			 * Calculating receiver data
			*/
			$receiverData = Mage::helper('paypaladaptive')->getMarketplaceSellerData();
		} elseif ($enabledBookorrent == 1) {
			/**
			 * Calculating receiver data
			*/
			$receiverData = Mage::helper('paypaladaptive')->getBookorrentHostData();			;

		} else {
			/**
			 * Calculating receiver data
			*/
			$receiverData = Mage::helper('paypaladaptive')->getSellerData();
		}

		/**
		 * If Checking whether receiver count greater than 5 or not
		*/
		
		$receiverCount = count($receiverData);
		if ($receiverCount > 9) {
			Mage::getSingleton('checkout/session')->addError(Mage::helper('paypaladaptive')->__("You have ordered more than 5 partner products"));
			$this->_redirect('checkout/cart', array('_secure' => true));
			return;
		}
		/**
		 * Geting checkout grand total amount
		*/
		 $grandTotal = round(Mage::helper('paypaladaptive')->getGrandTotal(), 2);
		 
		/**
		 * Getting receiver amount total
		*/
		 $amountTotal = $this->getAmountTotal($receiverData);
		
		 $sellerTotal = round($amountTotal, 2);
		
		if ($grandTotal >= $sellerTotal) {
			 
			/**
			 * Initilize receiver data
			*/
			$receiverAmountArray = $receiverEmailArray = $receiverPrimaryArray = $receiverInvoiceIdArray = array();
			 
			/**
			 * Getting invoice id
			*/
			$invoiceId = Mage::getSingleton('checkout/session')->getLastRealOrderId();
			$paypalInvoiceId = $invoiceId . $trackingId;
			 
			/**
			 * Preparing receiver data
			*/
			foreach ($receiverData as $data) {
				/**
				 * Getting receiver paypal id
				*/
				$receiverPaypalTotal 		= $data['grand_total'];
				$receiverPaypalId 			= $data['seller_id'];
				$receiverAmountArray[] 		= round($data['amount'], 2);
				$receiverEmailArray[] 		= $receiverPaypalId;
				$receiverPrimaryArray[] 	= 'false';
				$receiverInvoiceIdArray[] 	= $paypalInvoiceId;
				// Setting Amount to Session.
				Mage::getSingleton('core/session')->setGrandTotal(round($data['grand_total'], 2));
			}
			 
			/**
			 *  Getting admin paypal id
			*/
			 
			$adminEmail = $receiverEmailArray[] = Mage::helper('paypaladaptive')->getAdminPaypalId();
			$receiverInvoiceIdArray[] = $paypalInvoiceId;
			/**
			 * Getting payment method
			*/
			$paymentMethod = Mage::helper('paypaladaptive')->getPaymentMethod();
			/**
			 * Assign delayed chained method
			*/
			if ($paymentMethod == 'delayed_chained' && $receiverCount >= 1) {
				$actionType = "PAY_PRIMARY";
			}
			/**
			 * If no seller product available for checkout. Setting receiverPrimaryArray empty
			*/
			if ($receiverCount < 1) {
				$receiverPrimaryArray = array();
				/**
				 * Assigning store owner paypal id & amount
				*/
				$receiverAmountArray[] = round($receiverPaypalTotal, 2);
			} elseif ($paymentMethod == 'parallel') {
				$receiverPrimaryArray[] = 'false';
				/**
				 * Assigning store owner paypal id & amount
				*/
				$receiverAmountArray[] = round($receiverPaypalTotal - $sellerTotal, 2);
			} else {
				$receiverPrimaryArray[] = 'true';
				/**
				 * Assigning store owner paypal id & amount
				*/
				$adminAmount = $receiverAmountArray[] = round($receiverPaypalTotal, 2);
			}
		} else {
			 
			Mage::getSingleton('checkout/session')->addError(Mage::helper('paypaladaptive')->__("Please contact admin partner amount is greater than total amount"));
			$this->_redirect('checkout/cart', array('_secure' => true));
			return;
		}
		 
		if(strtotime($fromDate) > strtotime($curDate)){ 
			$resArray = Mage::getModel('paypaladaptive/apicall')->CallPay($actionType, $cancelUrl, $returnUrl, $currencyCode, $receiverEmailArray, $receiverAmountArray, $receiverPrimaryArray, $receiverInvoiceIdArray, $feesPayer, $ipnNotificationUrl, $memo, $pin, $preapprovalKey, $reverseAllParallelPaymentsOnError, $senderEmail, $trackingId);
		}else{
			$resArray = Mage::getModel('paypaladaptive/apicall')->CallPay($actionType, $cancelUrl, $returnUrl, $currencyCode, $receiverEmailArray, $receiverAmountArray, $receiverPrimaryArray, $receiverInvoiceIdArray, $feesPayer, $ipnNotificationUrl, $memo, $pin, $preapprovalKey, $reverseAllParallelPaymentsOnError, $senderEmail, $trackingId);
		}$dataTrackingId 	= $trackingId;
		
		/**
		 * Updating the Preapproval Details
		 */
		try {
			$connection = Mage::getSingleton('core/resource')->getConnection('core_write');
			$tPrefix = ( string ) Mage::getConfig ()->getTablePrefix ();
			$table_name = $tPrefix . 'paypaladaptivepreapproval';
			
			$connection->beginTransaction();
				
			$__fields = array();
			$__fields['seller_invoice_id'] 	= $invoiceId;
			$__fields['tracking_id'] 		= $dataTrackingId;
			$__fields['commission_amount'] 	= round($data['commission_fee'], 2);
			$__fields['seller_amount'] 		= round($data['amount'], 2);
		
			$__where = $connection->quoteInto('order_id =?', $orderId);
			$connection->update($table_name, $__fields, $__where);
				
			$connection->commit();
		} catch (Mage_Core_Exception $e) {
			echo $e->getMessage();
			Mage::getSingleton('checkout/session')->addError($e->getMessage());
			exit;
		}


		$ack = strtoupper($resArray["responseEnvelope.ack"]);
		if ($ack == "SUCCESS") {
			$cmd = "cmd=_ap-payment&paykey=" . urldecode($resArray["payKey"]);
			
			/**
			 * Assigning session valur for paykey , tracking id and order id
			*/
			$session = Mage::getSingleton('checkout/session');
			$session->setPaypalAdaptiveTrackingId($trackingId);
			$session->setPaypalAdaptivePayKey(urldecode($resArray["payKey"]));
			$session->setPaypalAdaptiveRealOrderId($invoiceId);
			$session->setPaypalAdaptivePaymentMethod($paymentMethod);
			/**
			 * Storing seller payment details to paypaladaptivedetails table
			*/
			foreach ($receiverData as $data) {
				/**
				 * Initilizing payment data for save
				*/
				$dataPaypalTotal 	= round($data['grand_total'], 2);
				$dataSellerId 		= $data['seller_id'];
				$dataAmount 		= round($data['grand_total'], 2) - round($data['commission_fee'], 2);
				$dataCommissionFee 	= round($data['commission_fee'], 2);
				$dataProcessingFee 	= round($data['processing_fee'], 2);
				$dataCurrencyCode 	= $currencyCode;				
				$dataGroupType 		= 'seller';
				$dataPayKey 		= $resArray["payKey"];
				/**
				 * Calling save function for storing seller payment data
				*/
				Mage::getModel('paypaladaptive/save')->saveOrderData($orderId, $invoiceId, $dataSellerId, $sellerTotal, $dataCommissionFee, $dataCurrencyCode, $dataPayKey, $dataGroupType, $dataTrackingId, $dataPaypalTotal, $paymentMethod, $preapprovalKey, $feesPayer);
			}
			 
			/**
			 * Initilizing payment data for save
			*/
			$dataSellerId = Mage::helper('paypaladaptive')->getAdminPaypalId();
			$dataCommissionFee = 0;
			$dataProcessingFee = 0;
			$dataCurrencyCode = $currencyCode;
			$dataGroupType = 'admin';
			$dataAmount = $dataPaypalTotal - $sellerTotal;
			$dataPayKey 	= $resArray["payKey"];
			/**
			 * Calling save function for storing owner payment data
			*/
			Mage::getModel('paypaladaptive/save')->saveOrderData($orderId, $invoiceId, $dataSellerId, $dataAmount, $dataCommissionFee, $dataCurrencyCode, $dataPayKey, $dataGroupType, $dataTrackingId, $dataPaypalTotal, $paymentMethod, $preapprovalKey, $feesPayer);
			 
			if ($paymentMethod == 'delayed_chained' && $receiverCount >= 1) {
				$session->setPaypalAdaptiveDelayedChainedMethod(1);
				/**
				 * Calling save function for storing delayed payment
				*/
				Mage::getModel('paypaladaptive/save')->saveDelayedOrderData($orderId, $invoiceId, $dataCurrencyCode, $dataPayKey, $dataTrackingId, $adminEmail, $adminAmount);
			} else {
				$session->setPaypalAdaptiveDelayedChainedMethod(0);
			}
			 
			/**
			 * Redirectr to Paypal site
			*/

			$this->RedirectToPayPal($cmd);
			return;
		} else {
			$errorMsg = urldecode($resArray["error(0).message"]);
			$errorId = urldecode($resArray["error(0).errorId"]);
			if($errorId == '579024'){
				
				/**
				 * Set the Comment History.
				 */
				$status = Mage::helper('paypaladaptive')->getOrderSuccessStatus();
				$order->setData('state', $status);
				$order->setStatus($status);
				$history = $order->addStatusHistoryComment('Preapproval for future payment Authorized successfully.', true);
				$history->setIsCustomerNotified(true);
				$order->save();
				
				$order 	  = Mage::getModel('sales/order');
				$order_id = $order->loadByIncrementId($invoiceId);
				$order->sendNewOrderEmail();
				
				// Ubdating Bookorrent Details
				$this->bookingStatus($invoiceId);
				
				$this->_redirect('checkout/onepage/success', array('_secure' => true));
				return;
			}else{
			Mage::getSingleton('checkout/session')->addError(Mage::helper('paypaladaptive')->__("Pay API call failed."));
			Mage::getSingleton('checkout/session')->addError(Mage::helper('paypaladaptive')->__("Error Message:") . ' ' . $errorMsg);
			$this->_redirect('checkout/cart', array('_secure' => true));
			return;
			}
		}
		 
	}
	
	
	
    /**
     * Payment return functionality
     */  
    public function returnAction() {
    	
    	$subId 			= Mage::getSingleton('core/session')->getSubId();

		if($subId == 0){
			$this->noReturnSubscription();			
		} else {

        $session = Mage::getSingleton('checkout/session');

        /**
         * Getting pay key and tracking id
         */  
        $payKey 		= $session->getPaypalAdaptivePayKey();
        $trackingId 	= $session->getPaypalAdaptiveTrackingId();
        $paymentMethod 	= $session->getPaypalAdaptivePaymentMethod();
        $transactionId 	= '';

        /**
         *	 Make the Pay Details call to PayPal 
         */
        $resArray = Mage::getModel('paypaladaptive/apicall')->CallPaymentDetails($payKey, $transactionId, $trackingId);

        $ack = strtoupper($resArray["responseEnvelope.ack"]);
        if ($ack == "SUCCESS" && isset($resArray["paymentInfoList.paymentInfo(0).transactionId"]) && $resArray["paymentInfoList.paymentInfo(0).transactionId"] != '' ) {

             $paypalAdaptive = $session->getPaypalAdaptiveRealOrderId();
                
            try {
                $order = Mage::getModel('sales/order');
                $order_id = $order->loadByIncrementId($paypalAdaptive);
                $order->setLastTransId($resArray["paymentInfoList.paymentInfo(0).transactionId"])->save();
                $order->sendNewOrderEmail();
                if ($order->canInvoice()) {
                	
                    $invoice = $order->prepareInvoice();
                    $invoice->register()->pay();
                    $invoice->getOrder()->setIsInProcess(true);
                    $status = Mage::helper('paypaladaptive')->getOrderSuccessStatus();
              		
                    /**
                     *  To update the bookorRent Property
                     */
    				$this->bookingStatus($paypalAdaptive);
                        
                    $invoice->getOrder()->setData('state', $status);
                    $invoice->getOrder()->setStatus($status);
                    $history = $invoice->getOrder()->addStatusHistoryComment('Partial amount of captured automatically.', true);
                    $history->setIsCustomerNotified(true);
                    //$invoice->sendEmail(true, '');

                    Mage::getModel('core/resource_transaction')
                            ->addObject($invoice)
                            ->addObject($invoice->getOrder())
                            ->save();
                    $invoice->save();

                    /**
                     * Saving payment success details
                     */ 
                    for ($inc = 0; $inc <= 5; $inc++) {

    					if (isset($resArray["paymentInfoList.paymentInfo($inc).transactionId"])) {
    						$receiverTransactionId = $resArray["paymentInfoList.paymentInfo($inc).transactionId"];
    					} else {
    						$receiverTransactionId = '';
    					}
    
    					if (isset($resArray["paymentInfoList.paymentInfo($inc).transactionStatus"])) {
    						$receiverTransactionStatus = $resArray["paymentInfoList.paymentInfo($inc).transactionStatus"];
    					} else {
    						$receiverTransactionStatus = 'Pending';
    					}

    					$senderEmail 		= $resArray["senderEmail"];
    					$receiverEmail 		= $resArray["paymentInfoList.paymentInfo($inc).receiver.email"];
    					$receiverInvoiceId 	= $resArray["paymentInfoList.paymentInfo($inc).receiver.invoiceId"];
    					/**
    					 * Updating transaction id and status
    					*/
    					Mage::getModel('paypaladaptive/save')->update($payKey, $trackingId, $receiverTransactionId, $receiverTransactionStatus, $senderEmail, $receiverEmail, $receiverInvoiceId);
    					
                            /**
                             *	Update Preapproval Payment
                             */
                            $session = Mage::getSingleton('checkout/session');
                            $order = Mage::getModel('sales/order');
                            $order->loadByIncrementId($session->getLastRealOrderId());
                            $orderId = $order->getId();
                            $grandTotal    =  Mage::getSingleton('core/session')->getGrandTotal();                            
                            try {
                            	$connection = Mage::getSingleton('core/resource')->getConnection('core_write');

                            	$tPrefix = ( string ) Mage::getConfig ()->getTablePrefix ();
                            	$table_name = $tPrefix . 'paypaladaptivepreapproval';
                            	$connection->beginTransaction();
                            	$__fields = array();
                            	$__fields['cur_payments'] = 1;
                            	$__fields['cur_payments_amount'] = $grandTotal;
                            	$__fields['cur_period_attempts'] = 1;
                            	$__fields['status'] = 'ACTIVE';
                            
                            	$__where = $connection->quoteInto('order_id =?', $orderId);
                            	$connection->update($table_name, $__fields, $__where);
                            
                            	$connection->commit();
                            } catch (Mage_Core_Exception $e) {
                            	echo $e->getMessage();
                            	Mage::getSingleton('checkout/session')->addError($e->getMessage());
                            	exit;
                            }
                    }                    
                    $session->unsPaypalAdaptivePayKey();
                    $session->unsPaypalAdaptiveTrackingId();
                    $session->unsPaypalAdaptiveRealOrderId();
                    
                    $this->_redirect('checkout/onepage/success', array('_secure' => true));
                    return;
                }
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('checkout/session')->addError($e->getMessage());
                return;
            }
        } else {
            $this->_redirect('checkout/onepage/failure');
            return;
        }
	  } 
   }

/*
     * PayPal ipn notification action
     */
    public function ipnnotificationAction() {  
   	/*
   	 * Getting pay key and tracking id
   	 */
    $payKey = $_POST['pay_key'];
   	$trackingId = $_POST['tracking_id'];
   	$transactionId = '';

   	$paymentCollection = Mage::getModel('paypaladaptive/paypaladaptivedetails')->getCollection()
   	->addFieldToFilter('pay_key', $payKey)
   	->addFieldToFilter('tracking_id', $trackingId)->getFirstItem();   
   	$paypalAdaptive = $paymentCollection->getSellerInvoiceId();
   	
   	$delayedPaymentCollection = Mage::getModel('paypaladaptive/delaychaineddetails')->getCollection()
   	->addFieldToFilter('increment_id', $paypalAdaptive);
   	
   	if(count($paymentCollection) >= 1){
   	/*
   	 * Make the Payment Details call using PayPal API
   	*/
   	$resArray = Mage::getModel('paypaladaptive/apicall')->CallPaymentDetails($payKey, $transactionId, $trackingId);
   	
   	$ack = strtoupper($resArray["responseEnvelope.ack"]);
    
   	
   	if ($ack == "SUCCESS" && isset($resArray["paymentInfoList.paymentInfo(0).transactionId"]) && $resArray["paymentInfoList.paymentInfo(0).transactionId"] != '' || $ack == "SUCCESS" && count($delayedPaymentCollection)) {
   	  		 	
   		try {   	
   			$order = Mage::getModel('sales/order');
   			$order->loadByIncrementId($paypalAdaptive);   			
   		       if (count($delayedPaymentCollection)) {
                    for ($inc = 0; $inc <= 5; $inc++) {
                        if (!empty($resArray["paymentInfoList.paymentInfo($inc).transactionId"])) {
                            $transactionIdData = $resArray["paymentInfoList.paymentInfo($inc).transactionId"];
                            $transactionStatusData = $resArray["paymentInfoList.paymentInfo($inc).transactionStatus"];
                            break;
                        }
                    }
                } else {
                    $transactionIdData = $resArray["paymentInfoList.paymentInfo(0).transactionId"];
                }
   			 	
   			$order->setLastTransId($transactionIdData)->save();   	
   			if ($order->canInvoice()) {
            	if(Mage::helper('paypaladaptive')->getModuleInstalledStatus('Apptha_Marketplace') == 1 ){
                $items = $order->getAllItems ();
                $itemCount = 0;
                $sellerProduct = array();
                foreach ( $items as $item ) {
                	$products = Mage::helper ( 'marketplace/marketplace' )->getProductInfo ( $item->getProductId () );
                	$orderEmailData [$itemCount] ['seller_id'] = $products->getSellerId ();
                	$orderEmailData [$itemCount] ['product_qty'] = $item->getQtyOrdered ();
                	$orderEmailData [$itemCount] ['product_id'] = $item->getProductId ();
                	$sellerProduct[$products->getSellerId ()][$item->getProductId ()]	= $item->getQtyOrdered ();
                	$itemCount = $itemCount + 1;
                }
                $sellerIds = array ();
                foreach ( $orderEmailData as $data ) {
                	if (! in_array ( $data ['seller_id'], $sellerIds )) {
                		$sellerIds [] = $data ['seller_id'];
                	}
                }
                foreach ( $sellerIds as $id ) {
                	$itemsarray = $itemsArr = array ();
	                foreach ( $order->getAllItems () as $item ) {
	                	$productsCol = Mage::helper ( 'marketplace/marketplace' )->getProductInfo ( $item->getProductId () );
	                	$itemId = $item->getItemId ();
	                	if($productsCol->getSellerId () == $id){ 
	                		$itemsarray [$itemId] = $sellerProduct[$id][$item->getProductId ()];
	                		$itemsArr [] = $itemId;
	                	}else{
	                		$itemsarray [$itemId] = 0;
	                	}
				   }
				   /**
				    * Generate invoice for shippment.
				    */
				   Mage::getModel ( 'sales/order_invoice_api' )->create ( $order->getIncrementId (), $itemsarray, '', 1, 1 );
				   Mage::getModel ( 'marketplace/order' )->updateSellerOrderItemsBasedOnSellerItems ( $itemsArr, $order->getEntityId(), 1 );
				   }
             }else{
             	$invoice = $order->prepareInvoice();
                $invoice->register()->pay();
                $invoice->getOrder()->setIsInProcess(true);
                $status = Mage::helper('paypaladaptive')->getOrderSuccessStatus();
                $invoice->getOrder()->setData('state', $status);
                $invoice->getOrder()->setStatus($status);
                $history = $invoice->getOrder()->addStatusHistoryComment('Partial amount of captured automatically.', true);
                $history->setIsCustomerNotified(true);
                $invoice->sendEmail(true, '');
                		
                Mage::getModel('core/resource_transaction')
                ->addObject($invoice)
                ->addObject($invoice->getOrder())
                ->save();
                $invoice->save();
                }
                /*
                 * Saving payment success details
                 */
                for ($inc = 0; $inc <= 5; $inc++) {

                if (isset($resArray["paymentInfoList.paymentInfo($inc).transactionId"])) {
                	$receiverTransactionId = $resArray["paymentInfoList.paymentInfo($inc).transactionId"];
                } else {
                	$receiverTransactionId = '';
                }

                if (isset($resArray["paymentInfoList.paymentInfo($inc).transactionStatus"])) {
                	$receiverTransactionStatus = $resArray["paymentInfoList.paymentInfo($inc).transactionStatus"];
                } else {
                	$receiverTransactionStatus = 'Pending';
                }

                $senderEmail = $resArray["senderEmail"];
                $receiverEmail = $resArray["paymentInfoList.paymentInfo($inc).receiver.email"];
                $receiverInvoiceId = $resArray["paymentInfoList.paymentInfo($inc).receiver.invoiceId"];
                /*
                 * Updating transaction id and status
                 */
                Mage::getModel('paypaladaptive/save')->update($payKey, $trackingId, $receiverTransactionId, $receiverTransactionStatus, $senderEmail, $receiverEmail, $receiverInvoiceId);
           }

           if ($paymentMethod == 'delayed_chained') {
           /*
            * Updating delayed chained method transaction id and status
            */
            Mage::getModel('paypaladaptive/save')->updateDelayedChained($payKey, $trackingId, $transactionIdData, $transactionStatusData, $senderEmail, $paypalAdaptive);
            } 	
   		}
   	} catch (Mage_Core_Exception $e) {
   	Mage::log($e->getMessage());
   		}
   	}
   	}   	
    }
   

    // Order cancel functionality 
    public function cancelAction() {
        try {
            $session = Mage::getSingleton('checkout/session');
            $paypalAdaptive = $session->getPaypalAdaptiveRealOrderId();
            $payKey = $session->getPaypalAdaptivePayKey();
            $trackingId = $session->getPaypalAdaptiveTrackingId();

            if (empty($paypalAdaptive)) {
                Mage::getSingleton('checkout/session')->addError(Mage::helper('paypaladaptive')->__("No order for processing found"));
                $this->_redirect('checkout/cart');
                return;
            }
            $order = Mage::getModel('sales/order');
            $order->loadByIncrementId($paypalAdaptive);
            $order->setState(Mage_Sales_Model_Order::STATE_CANCELED, true)->save();
            Mage::getSingleton('checkout/session')->addError(Mage::helper('paypaladaptive')->__("Payment Canceled."));

            // Changing payment status
            Mage::getModel('paypaladaptive/save')->cancelPayment($paypalAdaptive, $payKey, $trackingId);

            $session->unsPaypalAdaptivePayKey();
            $session->unsPaypalAdaptiveTrackingId();
            $session->unsPaypalAdaptiveRealOrderId();

            $this->_redirect('checkout/cart');
            return;
        } catch (Mage_Core_Exception $e) {
            Mage::getSingleton('checkout/session')->addError(Mage::helper('paypaladaptive')->__("Unable to cancel Paypal Adaptive Checkout."));
            $this->_redirect('checkout/cart');
            return;
        } catch (Exception $e) {
            Mage::getSingleton('checkout/session')->addError(Mage::helper('paypaladaptive')->__("Unable to cancel Paypal Adaptive Checkout."));
            $this->_redirect('checkout/cart');
            return;
        }
    }

    // Calculating  sum of receiver amount
    public function getAmountTotal($receiverData) {

        $amountTotal = 0;
        foreach ($receiverData as $data) {
            $amountTotal = $amountTotal + $data['amount'];
        }
        return $amountTotal;
    }
    
    public function generateCharacter() {
        $possible = "1234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $char = substr($possible, mt_rand(0, strlen($possible) - 1), 1);
        return $char;
    }

    // Generating tracking id
    public function generateTrackingID() {
        $GUID = $this->generateCharacter() . $this->generateCharacter() . $this->generateCharacter();
        $GUID .=$this->generateCharacter() . $this->generateCharacter() . $this->generateCharacter();
        $GUID .=$this->generateCharacter() . $this->generateCharacter() . $this->generateCharacter();
        return $GUID;
    }

    // Redirect to paypal.com here   
    public function RedirectToPayPal($cmd) {
        $mode = Mage::helper('paypaladaptive')->getPaymentMode();
        $payPalURL = "";
        if ($mode == 1) {
            $payPalURL = "https://www.sandbox.paypal.com/webscr?" . $cmd;
        } else {
            $payPalURL = "https://www.paypal.com/webscr?" . $cmd;
        }
        Mage::app()->getResponse()->setRedirect($payPalURL);
        return FALSE;
    }
    
    
    
    public function noSubscription(){
    	
    	// Checking whether order id available or not
    	$session = Mage::getSingleton('checkout/session');
    	$order = Mage::getModel('sales/order');
    	$order->loadByIncrementId($session->getLastRealOrderId());
    	$orderId = $order->getId();
    	$orderStatus = $order->getStatus();
    	if (empty($orderId) || $orderStatus != 'pending') {
    		Mage::getSingleton('checkout/session')->addError(Mage::helper('paypaladaptive')->__("No order for processing found"));
    		$this->_redirect('checkout/cart');
    		return FALSE;
    	}
    	
    	// Initilize adaptive payment data
    	$actionType = "PAY";
    	$cancelUrl = Mage::getUrl('paypaladaptive/adaptive/cancel');
    	$returnUrl = Mage::getUrl('paypaladaptive/adaptive/return');
    	$ipnNotificationUrl = Mage::getUrl('paypaladaptive/adaptive/ipnnotification', array('_secure' => true));
    	$currencyCode = Mage::app()->getStore()->getCurrentCurrencyCode();
    	$senderEmail = "";
    	$feesPayer = Mage::helper('paypaladaptive')->getFeePayer();
    	$memo = "";
    	$pin = "";
    	$preapprovalKey = "";
    	$reverseAllParallelPaymentsOnError = "";
    	$trackingId = $this->generateTrackingID();
    	
    	
    	$enabledBookorrent = Mage::helper('paypaladaptive')->getModuleInstalledStatus('Apptha_Bookorrent');
    	$enabledBookorrent = 1;
    	
    	// Checking where marketplace enable or not
    	if ($enabledMarplace == 1) {
    		// Calculating receiver data
    		$receiverData = Mage::helper('paypaladaptive')->getMarketplaceSellerData();
    	} elseif ($enabledBookorrent == 1) {
			/**
			 * Calculating receiver data
			*/
			$receiverData = Mage::helper('paypaladaptive')->getBookorrentHostData();

		} else {
    		// Calculating receiver data
    		$receiverData = Mage::helper('paypaladaptive')->getSellerData();
    	}
    	
    	// If Checking whether receiver count greater than 5 or not
    	$receiverCount = count($receiverData);
    	if ($receiverCount > 9) {
    		Mage::getSingleton('checkout/session')->addError(Mage::helper('paypaladaptive')->__("You have ordered more than 9 partner products"));
    		$this->_redirect('checkout/cart');
    		return;
    	}

    	
    	// Geting checkout grand total amount
    	$grandTotal = round(Mage::helper('paypaladaptive')->getGrandTotal(), 2);
    	
    	// Getting receiver amount total
    	$amountTotal = $this->getAmountTotal($receiverData);
    	
    	$sellerTotal = round($amountTotal, 2);
    	
    	if ($grandTotal >= $sellerTotal) {
    	
    		// Initilize receiver data
    		$receiverAmountArray = $receiverEmailArray = $receiverPrimaryArray = $receiverInvoiceIdArray = array();
    	
    		// Getting invoice id
    		$invoiceId = Mage::getSingleton('checkout/session')->getLastRealOrderId();
    		$paypalInvoiceId = $invoiceId . $trackingId;
    	
    		// Preparing receiver data
    		foreach ($receiverData as $data) {
  			/**
    			 * Getting receiver paypal id
    			 */
    			$receiverPaypalTotal 		= $data['grand_total'];
    			$receiverPaypalId 			= $data['seller_id'];
    			$receiverAmountArray[] 		= round($data['amount'], 2);
    			$receiverEmailArray[] 		= $receiverPaypalId;
    			$receiverPrimaryArray[] 	= 'false';
    			$receiverInvoiceIdArray[] 	= $paypalInvoiceId;
    			// Setting Amount to Session.
    			Mage::getSingleton('core/session')->setGrandTotal(round($data['grand_total'], 2));
    		}
    	
    		 
    		$receiverEmailArray[] = Mage::helper('paypaladaptive')->getAdminPaypalId();
    		$receiverInvoiceIdArray[] = $paypalInvoiceId;
    	
    		$paymentMethod =Mage::helper('paypaladaptive')->getPaymentMethod();
    	
    		// If no seller product available for checkout. Setting receiverPrimaryArray empty
    		if ($receiverCount < 1) {
    			$receiverPrimaryArray = array();
    			// Assigning store owner paypal id & amount
    			$receiverAmountArray[] = round($grandTotal, 2);
    		}elseif($paymentMethod == 'parallel'){
    			$receiverPrimaryArray[] = 'false';
    			// Assigning store owner paypal id & amount
    			$receiverAmountArray[] = round($grandTotal - $sellerTotal, 2);
    		}else{
    			$receiverPrimaryArray[] = 'true';
    			// Assigning store owner paypal id & amount
    			$receiverAmountArray[] = round($grandTotal, 2);
    		}
    		 
    	} else {
    	
    		Mage::getSingleton('checkout/session')->addError(Mage::helper('paypaladaptive')->__("Please contact admin partner amount is greater than total amount"));
    		$this->_redirect('checkout/cart');
    		return;
    	}
    	 
    	

    	
    $resArray = Mage::getModel('paypaladaptive/apicall')->CallPay($actionType, $cancelUrl, $returnUrl, $currencyCode, $receiverEmailArray, $receiverAmountArray, $receiverPrimaryArray, $receiverInvoiceIdArray, $feesPayer, $ipnNotificationUrl, $memo, $pin, $preapprovalKey, $reverseAllParallelPaymentsOnError, $senderEmail, $trackingId);
    	

    $ack = strtoupper($resArray["responseEnvelope.ack"]);
    	
    	if ($ack == "SUCCESS") {
    		$cmd = "cmd=_ap-payment&paykey=" . urldecode($resArray["payKey"]);
    	
    		// Assigning session valur for paykey , tracking id and order id
    		$session = Mage::getSingleton('checkout/session');
    		$session->setPaypalAdaptiveTrackingId($trackingId);
    		$session->setPaypalAdaptivePayKey(urldecode($resArray["payKey"]));
    		$session->setPaypalAdaptiveRealOrderId($invoiceId);
    	
    		// Storing seller payment details to paypaladaptivedetails table
    		foreach ($receiverData as $data) {
    	
    			// Initilizing payment data for save
    			$dataSellerId = $data['seller_id'];
    			$dataAmount = round($data['amount'], 2);
    			$dataCommissionFee = round($data['commission_fee'], 2);
    			$dataCurrencyCode = $currencyCode;
    			$dataPayKey = $resArray["payKey"];
    			$dataGroupType = 'seller';
    			$dataTrackingId = $trackingId;
    	
    			// Calling save function for storing seller payment data
    			Mage::getModel('paypaladaptive/save')->saveOrderData($orderId, $invoiceId, $dataSellerId, $dataAmount, $dataCommissionFee, $dataCurrencyCode, $dataPayKey, $dataGroupType, $dataTrackingId, $grandTotal);
    		}
    	
    	
    		// Initilizing payment data for save
    		$dataSellerId = Mage::helper('paypaladaptive')->getAdminPaypalId();
    		$dataCommissionFee = 0;
    		$dataCurrencyCode = $currencyCode;
    		$dataPayKey = $resArray["payKey"];
    		$dataGroupType = 'admin';
    		$dataTrackingId = $trackingId;
    		$dataAmount = $grandTotal - $sellerTotal;
    	
    		// Calling save function for storing owner payment data
    		Mage::getModel('paypaladaptive/save')->saveOrderData($orderId, $invoiceId, $dataSellerId, $dataAmount, $dataCommissionFee, $dataCurrencyCode, $dataPayKey, $dataGroupType, $dataTrackingId, $grandTotal);
    	
    		// Redirectr to Paypal site
    		$this->RedirectToPayPal($cmd);
    		return;
    	} else {
    		$errorMsg = urldecode($resArray["error(0).message"]);
    		Mage::getSingleton('checkout/session')->addError(Mage::helper('paypaladaptive')->__("Pay API call failed."));
    		Mage::getSingleton('checkout/session')->addError(Mage::helper('paypaladaptive')->__("Error Message:") . ' ' . $errorMsg);
    		$this->_redirect('checkout/cart');
    		return;
    	}
    }
    
    
    public function noReturnSubscription(){
    	
    	$session = Mage::getSingleton('checkout/session');
    	
    	// Getting pay key and tracking id
    	$payKey 	= $session->getPaypalAdaptivePayKey();
    	$trackingId = $session->getPaypalAdaptiveTrackingId();
    	$transactionId = '';
    	
    	// Make the Pay Details call to PayPal
    	$resArray = Mage::getModel('paypaladaptive/apicall')->CallPaymentDetails($payKey, $transactionId, $trackingId);
 
    	$ack = strtoupper($resArray["responseEnvelope.ack"]);
    	if ($ack == "SUCCESS" && isset($resArray["paymentInfoList.paymentInfo(0).transactionId"]) && $resArray["paymentInfoList.paymentInfo(0).transactionId"] != '') {
    	
    		$paypalAdaptive = $session->getPaypalAdaptiveRealOrderId();
    		 
    		try {
    	
    			$order 	  = Mage::getModel('sales/order');
    			$order_id = $order->loadByIncrementId($paypalAdaptive);
    			 
    			$order->setLastTransId($resArray["paymentInfoList.paymentInfo(0).transactionId"])->save();
    	
    			if ($order->canInvoice()) {
    				$invoice = $order->prepareInvoice();
    				$invoice->register()->pay();
    				$invoice->getOrder()->setIsInProcess(true);
    				$status = Mage::helper('paypaladaptive')->getOrderSuccessStatus();
    				$invoice->getOrder()->setData('state', $status);
    				$invoice->getOrder()->setStatus($status);
    				$history = $invoice->getOrder()->addStatusHistoryComment('Partial amount of captured automatically.', true);
    				$history->setIsCustomerNotified(true);
    				//$invoice->sendEmail(true, '');
    	
    				Mage::getModel('core/resource_transaction')
    				->addObject($invoice)
    				->addObject($invoice->getOrder())
    				->save();
    				$invoice->save();
    	
    				
    				/**
    				 * Saving payment success details
    				 */
    				for ($inc = 0; $inc <= 5; $inc++) {
    				
    					if (isset($resArray["paymentInfoList.paymentInfo($inc).transactionId"])) {
    						$receiverTransactionId = $resArray["paymentInfoList.paymentInfo($inc).transactionId"];
    					} else {
    						$receiverTransactionId = '';
    					}
    				
    					if (isset($resArray["paymentInfoList.paymentInfo($inc).transactionStatus"])) {
    						$receiverTransactionStatus = $resArray["paymentInfoList.paymentInfo($inc).transactionStatus"];
    					} else {
    						$receiverTransactionStatus = 'Pending';
    					}
    				
    					$senderEmail 		= $resArray["senderEmail"];
    					$receiverEmail 		= $resArray["paymentInfoList.paymentInfo($inc).receiver.email"];
    					$receiverInvoiceId 	= $resArray["paymentInfoList.paymentInfo($inc).receiver.invoiceId"];
    					/**
    					 * Updating transaction id and status
    					 */
    					Mage::getModel('paypaladaptive/save')->update($payKey, $trackingId, $receiverTransactionId, $receiverTransactionStatus, $senderEmail, $receiverEmail, $receiverInvoiceId);	
    				}
    				
    				$order->sendNewOrderEmail();    				
    				
    				// Ubdating Bookorrent Details
    				$this->bookingStatus($paypalAdaptive);
    	
    				$session->unsPaypalAdaptivePayKey();
    				$session->unsPaypalAdaptiveTrackingId();
    				$session->unsPaypalAdaptiveRealOrderId();
    				$this->_redirect('checkout/onepage/success', array('_secure' => true));
    				return;
    			} 
    		} catch (Mage_Core_Exception $e) {
    			Mage::getSingleton('checkout/session')->addError($e->getMessage());
    			return;
    		}
    	} else {
    		$this->_redirect('checkout/onepage/failure');
    		return;
    	}
    }
    
    public function bookingStatus($paypalAdaptive){
    	try {
    		$connection = Mage::getSingleton('core/resource')->getConnection('core_write');
    		
    		$tPrefix = ( string ) Mage::getConfig ()->getTablePrefix ();
    		$table_name = $tPrefix . 'airhotels_property';
    		
    		$connection->beginTransaction();
    		$__fields = array();
    		$__fields['order_status'] = 1;
    		$__where = $connection->quoteInto('order_id =?', $paypalAdaptive);
    		$connection->update($table_name, $__fields, $__where);
    		$connection->commit();
    	} catch (Mage_Core_Exception $e) {
    		echo $e->getMessage();
    		Mage::getSingleton('checkout/session')->addError($e->getMessage());
    		exit;
    	}
    	
    }
}