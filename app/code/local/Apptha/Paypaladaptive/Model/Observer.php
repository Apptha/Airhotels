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
 * @abstract      : Observer File
 * @Creation Date : January 02,2014
 * @Modified By   : Ramkumar M
 * @Modified Date : January 25,2014
 * */
/*
 * ********************************************************* */

class Apptha_Paypaladaptive_Model_Observer {

    	/**
     	* Creditmemo(Refund process)
     	*/ 
    public function adaptiveRefundAction(Varien_Event_Observer $observer) {
		
    	
        $creditmemo = $observer->getEvent()->getCreditmemo();
       
        $orderId = $creditmemo->getOrderId();

        $order = Mage::getModel('sales/order')->load($creditmemo->getOrderId());
        $paymentMethodCode = $order->getPayment()->getMethodInstance()->getCode();

        $incrementId = $order->getIncrementId();
        $collections = Mage::getModel('paypaladaptive/paypaladaptivedetails')->getCollection()
                ->addFieldToFilter('seller_invoice_id', $incrementId);

        /**
         * Getting refund process enabled or not
         */ 
        $offlineRefundStatus = Mage::helper('paypaladaptive')->getRefundStatus();     
        
        /**
         * If not using adaptive payment method return ture to refund process
         */ 
        if ($paymentMethodCode != 'paypaladaptive' && count($collections) < 1 || $offlineRefundStatus != 1) {
            return;
        }

        $firstRow = Mage::helper('paypaladaptive')->getFirstRowData($collections);

        $adminEmail 	= $firstRow['owner_paypal_id'];
        $payKey 		= $firstRow['pay_key'];
        $trackingId 	= $firstRow['tracking_id'];
        $transactionId 	= $firstRow['seller_transaction_id'];
        $currencyCode 	= $firstRow['currency_code'];


        $items 		= $order->getAllItems();
     	$newItems 	= $creditmemo->getAllItems();
     

        $sellerData = Mage::getModel('paypaladaptive/save')->sellerDataForRefund($items, $incrementId, 1);
        $newSellerData = Mage::getModel('paypaladaptive/save')->sellerDataForRefund($newItems, $incrementId, 0);
		

        
        $receiverAmountArray = $receiverEmailArray = array();
        $adminTotalCommission = 0;
        foreach ($sellerData as $data) {
        	
        	
        	
            $sellerId = $data['seller_id'];
            $receiverAmount = $adminCommission = 0;
            if ($data['amount'] == $newSellerData[$sellerId]['amount']) {
     			$receiverAmount = $data['amount'];
                $adminCommission = $data['commission_fee'];
                $receiverPrimaryArray[] = 'false';
            } else {
                if (!empty($newSellerData[$sellerId]['amount'])) {
                    $receiverAmount = $data['amount'] - $newSellerData[$sellerId]['amount'];
                    $adminCommission = $data['commission_fee'] - $newSellerData[$sellerId]['commission_fee'];
                }
            }
            if ($receiverAmount > 0) {
                /**
                 * Getting receiver paypal id
                 */ 
                $receiverPaypalId = Mage::getModel('paypaladaptive/save')->sellerPaypalIdForRefund($incrementId, $data['seller_id']);
                $receiverAmountArray[] = round($receiverAmount, 2);
                $receiverEmailArray[] = $receiverPaypalId;
                $adminTotalCommission = round($adminTotalCommission + $adminCommission, 2);
                $receiverPrimaryArray[] = 'true';
            }
        }

        /**
         * Gather owner paypal id and amount
         */ 
        $creditmemo->getGrandTotal();
        $subTotal = array_sum($receiverAmountArray) + $adminTotalCommission;
        $receiverEmailArray[] = $adminEmail;
        $receiverAmountArray[] = round( $creditmemo->getGrandTotal() , 2);

        $resArray = Mage::getModel('paypaladaptive/apicall')->CallRefund($payKey, $transactionId, $trackingId, $receiverEmailArray, $receiverAmountArray, $receiverPrimaryArray, $currencyCode);


        $ack = strtoupper($resArray["responseEnvelope.ack"]);
        if ($ack == "SUCCESS") {

            // Saving refund details
            for ($inc = 0; $inc <= 5; $inc++) {

                if (!empty($resArray["refundInfoList.refundInfo($inc).encryptedRefundTransactionId"])) {

                    $encryptedRefundTransactionId = $resArray["refundInfoList.refundInfo($inc).encryptedRefundTransactionId"];
                    $refundStatus = $resArray["refundInfoList.refundInfo($inc).refundStatus"];
                    $refundNetAmount = $resArray["refundInfoList.refundInfo($inc).refundNetAmount"];
                    $refundFeeAmount = $resArray["refundInfoList.refundInfo($inc).refundFeeAmount"];
                    $refundGrossAmount = $resArray["refundInfoList.refundInfo($inc).refundGrossAmount"];
                    $refundTransactionStatus = $resArray["refundInfoList.refundInfo($inc).refundTransactionStatus"];
                    $receiverEmail = $resArray["refundInfoList.refundInfo($inc).receiver.email"];
                    $currencyCode = $resArray["currencyCode"];

                    Mage::getModel('paypaladaptive/save')->refund($orderId, $incrementId, $payKey, $trackingId, $transactionId, $encryptedRefundTransactionId, $refundStatus, $refundNetAmount, $refundFeeAmount, $refundGrossAmount, $refundTransactionStatus, $receiverEmail, $currencyCode);
                    Mage::getModel('paypaladaptive/save')->changePaymentStatus($incrementId, $payKey, $trackingId, $receiverEmail);
                    Mage::getModel('airhotels/property')->refundBlockedDates($orderId);
                } else {
                    if ($refundStatus != 'REFUNDED') {                        
                        $url = Mage::helper('adminhtml')
                                ->getUrl('adminhtml/sales_order_creditmemo/new', array('order_id' => $creditmemo->getOrderId()));
                        Mage::app()->getFrontController()->getResponse()->setRedirect($url);
                        Mage::app()->getResponse()->sendResponse();
                        Mage::throwException(Mage::helper('paypaladaptive')->__('API connection failed : '). $resArray["refundInfoList.refundInfo($inc).refundStatus"]);
                    }
                }
            }
        } else {
            /**
             * Error occurred while refunding process
             */     
            $url = Mage::helper('adminhtml')
                    ->getUrl('adminhtml/sales_order_creditmemo/new', array('order_id' => $creditmemo->getOrderId()));
            Mage::app()->getFrontController()->getResponse()->setRedirect($url);
            Mage::app()->getResponse()->sendResponse();
            Mage::throwException(Mage::helper('paypaladaptive')->__('API connection failed : '). $resArray["error(0).message"]);
        }
    }

    /**
     * Saving product tab data
     */ 
    public function saveProductTabData(Varien_Event_Observer $observer) {

        $enabledMarplace = Mage::helper('paypaladaptive')->getModuleInstalledStatus('Apptha_Marketplace');

        // Checking where marketplace enable or not  
        if ($enabledMarplace != 1) {

            $product = $observer->getEvent()->getProduct();

            try {
                $productId = $product->getId();
                $productPaypalId = Mage::app()->getRequest()->getPost('product_paypal_id');
                $shareMode = Mage::app()->getRequest()->getPost('share_mode');
                $shareValue = Mage::app()->getRequest()->getPost('share_value');
                $isEnable = Mage::app()->getRequest()->getPost('paypal_adaptive_activate');

                $productData = Mage::getModel('paypaladaptive/productdetails')->getCollection()
                        ->addFieldToFilter('product_id', $productId);
                $firstRow = Mage::helper('paypaladaptive')->getFirstRowData($productData);

                if (!empty($firstRow['product_id']) && $firstRow['product_id'] == $productId) {

                    $table_name = Mage::getSingleton('core/resource')->getTableName('paypaladaptiveproductdetails');
                    $connection = Mage::getSingleton('core/resource')
                            ->getConnection('core_write');
                    $connection->beginTransaction();
                    $fields = array();
                    if (!empty($productPaypalId)) {
                        $fields['product_paypal_id'] = $productPaypalId;
                    }
                    if (!empty($shareMode)) {
                        $fields['share_mode'] = $shareMode;
                    }
                    if (!empty($shareValue)) {
                        $fields['share_value'] = $shareValue;
                    }
                    $fields['is_enable'] = $isEnable;
                    $where[] = $connection->quoteInto('product_id = ?', $productId);
                    $connection->update($table_name, $fields, $where);
                    $connection->commit();
                } else {

                    // Assigning seller payment data
                    $collections = Mage::getModel('paypaladaptive/productdetails');
                    $collections->setProductId($productId);
                    $collections->setProductPaypalId($productPaypalId);
                    $collections->setShareMode($shareMode);
                    $collections->setShareValue($shareValue);
                    $collections->setIsEnable($isEnable);
                    $collections->save();
                }
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
    }

    // Adding custom product tabs 
    public function customProductTabs(Varien_Event_Observer $observer) {

        $enabledMarplace = Mage::helper('paypaladaptive')->getModuleInstalledStatus('Apptha_Marketplace');
        // Checking where marketplace enable or not 
        if ($enabledMarplace != 1) {
            $block = $observer->getEvent()->getBlock();
            if ($block instanceof Mage_Adminhtml_Block_Catalog_Product_Edit_Tabs) {
                if (Mage::app()->getRequest()->getActionName() == 'edit' || Mage::app()->getRequest()->getParam('type')) {
                    $block->addTab('adaptivepaypal', array(
                        'label' => Mage::helper('paypaladaptive')->__('Apptha Paypal Adaptive Options'),
                        'content' => $block->getLayout()->createBlock('adminhtml/template', 'adaptivepaypal-custom-tabs', array('template' => 'paypaladaptive/tabs.phtml'))->toHtml(),
                    ));
                }
            }
        }
    }

    

    /**
     *	Calling Preapproval Pay
     */
    public function preapprovalPay(){

    	/**
    	 *	Getting the paypal Details
    	 */
    	$ApiUserName = Mage::helper('paypaladaptive')->getApiUserName();
    	$ApiPassword = Mage::helper('paypaladaptive')->getApiPassword();
    	$ApiSignature = Mage::helper('paypaladaptive')->getApiSignature();
    	$ApiAppID = Mage::helper('paypaladaptive')->getAppID();
    	$mode = Mage::helper('paypaladaptive')->getPaymentMode();
    
    	/**
    	 *	Match with the Order Detils
    	*/
    	$receiverAmountArray = $receiverEmailArray = $receiverPrimaryArray = $receiverInvoiceIdArray = array();
    	$feesPayer 		= Mage::helper('paypaladaptive')->getFeePayer();
    	$paymentMethod 	= Mage::helper('paypaladaptive')->getPaymentMethod();
    
    	$currentDate = date("Y-m-d H:i:s", Mage::getModel('core/date')->timestamp(time()));
    	$collection = Mage::getModel('paypaladaptive/preapprovaldetails')->getCollection()
    	->addFieldToFilter('cur_period_ending_date', array('lteq' => $currentDate));
    
    
    
    	foreach($collection as $collections){
    		
    		$orderId 				= $collections['order_id'];
    		$paymentMethod 			= $collections['PaymentMethod'];
    		$invoiceId 				= $collections['seller_invoice_id'];
    		$trackingId 			= $collections['tracking_id'];
    		$senderEmail   			= '';
    		//$price 					= $collections['price'];
    		$sellerAmount 			= $collections['seller_amount'];
    		$commissionAmount 		= $collections['commission_amount'];
    		$buyerMail 				= $collections['buyer_paypal_mail'];
    		$currentPayments 		= $collections['cur_payments'];
    		$currentPaymentsAmount 	= $collections['cur_payments_amount'];
    		$totalAmountPayments 	= $collections['max_total_amount_of_all_payments'];
    		$maxAmountPerPayment 	= $collections['max_amount_per_payment'];
    		$endDate				= $collections['cur_period_ending_date'];
    		$paymentFail			= $collections['payment_fail'];
    		$fromDate 				= $collections['starting_date'];
    		$toDate 			   	= $collections['ending_date'];
    		$custId					= $collections['cust_id'];
    		$productId				= $collections['product_id'];
    		$preapprovalKey 		= $collections['preapproval'];
    		$status		 			= $collections['status'];
    		$pin		 			= $collections['pin'];
    		$realOrderId		 	= $collections['real_order_id'];
    		
    		

    		
    		
    		
    		$fromDate 	=  date('Y-m-d\TH:i:s\Z',strtotime($fromDate));
    		$toDate 	=  date('Y-m-d\TH:i:s\Z',strtotime($toDate));
    		
    		
    		if($status == 'ACTIVE' || $status == 'PENDING'){
    
    			$curPreapprovalKey 	= $collections['preapproval'];
    			$paymentAmount 				= $collections['max_amount_per_payment'];
    			
    			/**
    			 * Getting Email list from DB.
    			 */
    			$emailCollections = Mage::getModel('paypaladaptive/commissiondetails')->getCollection()
    						->addFieldToFilter('increment_id', $realOrderId);
    			foreach ($emailCollections as $emailCollection){
    				
    				$receiverEmailArray[]	= $sellerOneId = $emailCollection['seller_id'];
    			 	$processingFee			= $emailCollection['processing_fee'];
    				$receiverAmountArray[] 	= round($sellerAmount,2);
    				$receiverPrimaryArray[] = 'false';
    					
    				$receiverCount = count($receiverEmailArray);
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
    					$receiverAmountArray[] = round($paymentAmount, 2);
    				} elseif ($paymentMethod == 'parallel') {
    					$receiverPrimaryArray[] = 'false';
    					/**
    					 * Assigning store owner paypal id & amount
    					 */
    					$receiverAmountArray[] = round($paymentAmount - $sellerTotal, 2);
    				} else {
    					$receiverPrimaryArray[] = 'true';
    					/**
    					 * Assigning store owner paypal id & amount
    					 */
    					$adminAmount = $receiverAmountArray[] = round($paymentAmount, 2);
    				}
    
    
    	
    				$cancelUrl = Mage::getUrl('paypaladaptive/adaptive/cancel', array('_secure' => true));
    				$returnUrl = Mage::getUrl('paypaladaptive/adaptive/return', array('_secure' => true));
    
    				$adminEmail = $receiverEmailArray[] = Mage::helper('paypaladaptive')->getAdminPaypalId();
    				$trackingId = $this->generateTrackingID();
    				$receiverInvoiceIdArray[] = $invoiceId.$trackingId;
    				$receiverInvoiceIdArray[] = $invoiceId.$trackingId;
    				//$pin = 'REQUIRED';	

    				$actionType  = 'PAY';
    				$currencyCode = 'USD';
    				$memo = 'Preapproved Payments';
    				$nvpstr = "actionType=" . urlencode($actionType) . "&currencyCode=";
    				$nvpstr .= urlencode($currencyCode) . "&memo=";
    				$nvpstr .= urlencode($memo) . "&preapprovalKey=";
    				$nvpstr .= urlencode($preapprovalKey) . "&returnUrl=";
    				$nvpstr .= urlencode($returnUrl) . "&cancelUrl=" . urlencode($cancelUrl);
    				//$nvpstr .= urlencode($pin) . "&pinType=" . urlencode($pin);
    				
    				if (0 != count($receiverAmountArray)) {
    					$nvpstr .= Mage::getModel('paypaladaptive/apicall')->receiverAmountData($receiverAmountArray, $nvpstr);
    				}
    				
    				if (0 != count($receiverEmailArray)) {
    
    					$nvpstr .= Mage::getModel('paypaladaptive/apicall')->receiverEmailData($receiverEmailArray, $nvpstr);
    				}
    
    				if (0 != count($receiverPrimaryArray)) {
    
    					$nvpstr .= Mage::getModel('paypaladaptive/apicall')->receiverPrimaryData($receiverPrimaryArray, $nvpstr);
    				}
    
    
    				if (0 != count($receiverInvoiceIdArray)) {
    					$nvpstr .= Mage::getModel('paypaladaptive/apicall')->receiverInvoiceIdData($receiverInvoiceIdArray, $nvpstr);
    				}
    					
    				/**
    				 * Optional fields for pay call
    				 */
    				if ("" != $feesPayer) {
    					$nvpstr .= "&feesPayer=" . urlencode($feesPayer);
    				}
    				/*
    				if ("" != $ipnNotificationUrl) {
    					$nvpstr .= "&ipnNotificationUrl=" . urlencode($ipnNotificationUrl);
    				}
    				
    				if ("" != $reverseAllParallelPaymentsOnError) {
    					$nvpstr .= "&reverseAllParallelPaymentsOnError=";
    					$nvpstr .= urlencode($reverseAllParallelPaymentsOnError);
    				}
    				*/
    				if ("" != $senderEmail)
    				{
    					$nvpstr .= "&senderEmail=" . urlencode($senderEmail);
    				}
    
    				if ("" != $trackingId) {
    					$nvpstr .= "&trackingId=" . urlencode($trackingId);
    				}
    				
    				$resArray = Mage::getModel('paypaladaptive/apicall')->hashCall("Pay", $nvpstr);


    				$sellerTransactionId 	= $resArray["paymentInfoList.paymentInfo(0).transactionId"];
    				$adminTransactionId 	= $resArray["paymentInfoList.paymentInfo(1).transactionId"];
    				$payKey 				= $resArray['payKey'];
    				$dataSellerIdSeller 	=$sellerOneId;
    				$dataSellerIdAdmin =$adminEmail;
    				$dataGroupTypeSeller = 'seller';
    				$dataGroupTypeAdmin = 'admin';
    				//$adminCommission = 0.00;
    					
    				$adminCommission = $paymentAmount - $sellerAmount;
    				//$sellerTwoTotal = $paymentAmount - $sellerTwoAmount;
    				$price 			= $paymentAmount - $processingFee;
    				$ack = strtoupper($resArray["responseEnvelope.ack"]);
    
    
    				if ($ack == "SUCCESS") {
    					
    					$customer = Mage::getModel('customer/customer')->load($custId);
    					$transaction = Mage::getModel('core/resource_transaction');
    					$storeId = $customer->getStoreId();
    					$reservedOrderId = Mage::getSingleton('eav/config')->getEntityType('order')->fetchNewIncrementId($storeId);
    					
    					$this->orderCreation($custId,$price,$productId,$processingFee);
    					
    					/**
    					*	Get Buyer Paypal Mail Address
    					*/
    					$buerEmails = Mage::getModel('paypaladaptive/paypaladaptivedetails')->getCollection()
    					->addFieldToFilter('order_id', $orderId);
    						
    					foreach($buerEmails as $buerEmail){
    							
    						$buyerPaypalMail = $buerEmail['buyer_paypal_mail'];
    						/**
    						 * Transaction Details stored in Database
    						 */
    					}
    						Mage::getModel('paypaladaptive/save')->saveCronOrderData($orderId, $reservedOrderId, $dataSellerIdSeller, $sellerTransactionId, $sellerAmount, $adminCommission,$price, $currencyCode, $payKey, $dataGroupTypeSeller, $trackingId, $paymentAmount, $paymentMethod, $preapprovalKey, $feesPayer,$buyerPaypalMail);
    						Mage::getModel('paypaladaptive/save')->saveCronOrderData($orderId, $reservedOrderId, $dataSellerIdAdmin, $adminTransactionId, $adminCommission, $sellerTwoTotal, $price, $currencyCode, $payKey, $dataGroupTypeAdmin, $trackingId, $paymentAmount, $paymentMethod, $preapprovalKey, $feesPayer,$buyerPaypalMail);
    					
    				}else
    				{
    					/**
    					 * No of Payment Failure
    					 */
    					$model 			= Mage::getModel('catalog/product');
    					$_product 		= $model->load($productId);
    					$paymentFailure =  $_product->getPaymentFailure();

    					/**
    					 * If Payments Failed
    					 */
    						if( $paymentFailure >= $paymentFail ){
    							$count = 1;
    							$count +=$paymentFail;
    							$connection = Mage::getSingleton('core/resource')->getConnection('core_write');
    							$connection->beginTransaction();
    
    							$tPrefix = ( string ) Mage::getConfig ()->getTablePrefix ();
    							$table_name = $tPrefix . 'paypaladaptivepreapproval';
    							
    							$__fields = array();
    							$__fields['payment_fail'] = $count;
    							$__where = $connection->quoteInto( 'order_id =?', $orderId);
    							$connection->update($table_name, $__fields, $__where);
    
    							$connection->commit();
    						}else{
    							$connection = Mage::getSingleton('core/resource')->getConnection('core_write');
    							$connection->beginTransaction();
    
    							$tPrefix = ( string ) Mage::getConfig ()->getTablePrefix ();
    							$table_name = $tPrefix . 'paypaladaptivepreapproval';
    							
    							$__fields = array();
    							$__fields['status'] = 'SUSPENDED';
    							$__where = $connection->quoteInto( 'order_id =?', $orderId);
    							$connection->update($table_name, $__fields, $__where);
    
    							$connection->commit();
    						}
    				}
    				$this->updatePreapprovalKey();
    				
    			}
    		} continue;
    		break;
    	}
    }
    
    
    
    
    /**
     * Update Preapproval Pay Key
     */
    public function updatePreapprovalKey(){
		
    	
    	/**
    	 * Getting the Preapproval Details
    	 */
    	$ApiUserName = Mage::helper('paypaladaptive')->getApiUserName();
    	$ApiPassword = Mage::helper('paypaladaptive')->getApiPassword();
    	$ApiSignature = Mage::helper('paypaladaptive')->getApiSignature();
    	$ApiAppID = Mage::helper('paypaladaptive')->getAppID();
    	$mode = Mage::helper('paypaladaptive')->getPaymentMode();
    
    	$currentDate = date("Y-m-d H:i:s", Mage::getModel('core/date')->timestamp(time()));
    	$collection = Mage::getModel('paypaladaptive/preapprovaldetails')->getCollection()
    	->addFieldToFilter('cur_period_ending_date', array('lteq' => $currentDate));
    	 
    	foreach ($collection as $collections){
    
    		if( $collections['status'] == 'ACTIVE' || $collections['status'] == 'PENDING' ){
    				
    			/**
    			 * Current Payments
    			 */
    			$period 			= $collections['cur_payments'];
    			$preapprovalKey 	= $collections['preapproval'];
    			$preapprovalId 		= $collections['preapprovaldetails_id'];
    			$productId 	   		= $collections['product_id'];
    			$storeId 	   		= $collections['store_id'];
    			$subId 	   			= $collections['sub_id'];
    			
    			$nvpStr = 'preapprovalKey='.urlencode($preapprovalKey);
    			$methodName = 'PreapprovalDetails';
    			if ($mode == 1) {
    				$ApiEndpoint = "https://svcs.sandbox.paypal.com/AdaptivePayments";
    				$ApiEndpoint .= "/" . $methodName;
    			} else {
    				$ApiEndpoint = "https://svcs.paypal.com/AdaptivePayments";
    				$ApiEndpoint .= "/" . $methodName;
    			}
    			 
    			try {
    				$curl = new Varien_Http_Adapter_Curl();
    				/**
    				 * See DetailLevelCode in the WSDL
    				*/
    				$detailLevel = urlencode("ReturnAll");
    
    				/**
    				 * For valid enumerations
    				 * This should be the standard RFC
    				*/
    				$errorLanguage = urlencode("en_US");
    
    				/**
    				 * NVPRequest for submitting to server
    				*/
    				$nvpreq = "requestEnvelope.errorLanguage=$errorLanguage&requestEnvelope";
    				$nvpreq .= "detailLevel=$detailLevel&$nvpStr";
    				 
    				/**
    				 * The below line for SSL
    				 */
    				 
    				//$config = array('timeout' => 60,'verifypeer' => true,'verifyhost' => 2);
    				$config = array('timeout' => 60, 'verifypeer' => FALSE, 'verifyhost' => FALSE);
    				$curl->setConfig($config);
    
    				/**
    				 * Set the curl parameters
    				*/
    				$curl->addOption('CURLOPT_VERBOSE', 1);
    
    				$header = array(
    						'X-PAYPAL-REQUEST-DATA-FORMAT: NV',
    						'X-PAYPAL-RESPONSE-DATA-FORMAT: NV',
    						'X-PAYPAL-SECURITY-USERID: ' . $ApiUserName,
    						'X-PAYPAL-SECURITY-PASSWORD: ' . $ApiPassword,
    						'X-PAYPAL-SECURITY-SIGNATURE: ' . $ApiSignature,
    						'X-PAYPAL-SERVICE-VERSION: 1.3.0',
    						'X-PAYPAL-APPLICATION-ID: ' . $ApiAppID
    				);
    
    				$curl->write(Zend_Http_Client::POST, $ApiEndpoint, $http_ver = '1.1', $header, $nvpreq);
    
    				$data = $curl->read();
    
    				$errNo = $curl->getErrno();
    
    				if ($errNo == 60) {
    					$cacert = Mage::getBaseDir('lib') . '/paypaladaptive/cacert.pem';
    					$curl->addOption('CURLOPT_CAINFO', $cacert);
    					$data = $curl->read();
    				}
    
    				if ($curl->getErrno()) {
    					/**
    					 * Execute the Error handling module to display errors
    					 */
    					Mage::getSingleton('checkout/session')->addError($curl->getError());
    					return;
    				} else {
    
    					/**
    					 * Convert NVPResponse to an Associative Array
    					 */
    					$nvpResArray = Mage::getModel('paypaladaptive/apicall')->deformatNVP($data);
    

    					/**
    					 * Close curl
    					*/
    					$curl->close();
    				}
    				 
    				/**
    				 * Get the Current Period Ending Date.
    				 */
    				
    				if($subId == 0){
    					
    					$billPeriodUnit 		= '1';
    					$billPeriodFrequency 	= '1';
    					$endDate				= $billPeriodFrequency * 1 * $period ;
    					$dayCount 				= strtotime('+'.$endDate. 'days');
    					$endingDate 			= date('Y-m-d\TH:i:s\Z',$dayCount);
    					
    				}else{
    				$precollection = Mage::getModel ('airhotels/subscriptiontype' )->getCollection()
    								->addFieldToFilter ('id', $subId );
    				foreach($precollection as $pre) {
    					
    					/**
    					 *	Billing Informations
    					 */
    					$billPeriodUnit 		= $pre['billing_period_unit'];
    					$billPeriodFrequency 	= $pre['billing_frequency'];
    					$billPeriodMaxCycles 	= $pre['billing_cycle'];

    					switch($billPeriodUnit){
    						case 1:
    							$endDate		= $billPeriodFrequency * 1 * $period ;
    							$dayCount 		= strtotime('+'.$endDate. 'days');
    							$endingDate 	= date('Y-m-d\TH:i:s\Z',$dayCount);
    							break;
    						case 2:
    							$endDate		= $billPeriodFrequency * 7 * $period ;
    							$dayCount 		= strtotime('+'.$endDate. 'days');
    							$endingDate 	= date('Y-m-d\TH:i:s\Z',$dayCount);
    							break;
    						case 4:
    							$endDate		= $billPeriodFrequency * 30 * $period ;
    							$dayCount 		= strtotime('+'.$endDate. 'days');
    							$endingDate 	= date('Y-m-d\TH:i:s\Z',$dayCount);
    							break;
    						case 5:
    							$endDate		= $billPeriodFrequency * 365 * $period ;
    							$dayCount 		= strtotime('+'.$endDate. 'days');
    							$endingDate 	= date('Y-m-d\TH:i:s\Z',$dayCount);
    							break;
    					}
    				  }
    				}
    
    					/**
    					 * Return Response data
    					 */
    
    					$preapprovalAck = $nvpResArray['responseEnvelope.ack'];
    					$preapprovalApproved = $nvpResArray['approved'];
    					$preapprovalCurPayments = $nvpResArray['curPayments'];
    					$preapprovalCurPaymentsAmount = $nvpResArray['curPaymentsAmount'];
    					$preapprovalCurPeriodAttempts = $nvpResArray['curPeriodAttempts'];
    					$preapprovalCurPeriodEndingDate = $endingDate;
    					$preapprovalCurrencyCode = $nvpResArray['currencyCode'];
    					$preapprovalDateOfMonth = $nvpResArray['dateOfMonth'];
    					$preapprovalDayOfWeek = $nvpResArray['dayOfWeek'];
    					$preapprovalEndingDate = $nvpResArray['endingDate'];
    					$preapprovalMaximumAmount = $nvpResArray['maxAmountPerPayment'];
    					$preapprovalMaximumNumber = $nvpResArray['maxNumberOfPayments'];
    					$preapprovalMaxTotalAmt = $nvpResArray['maxTotalAmountOfAllPayments'];
    					$preapprovalPaymentPeriod = $nvpResArray['paymentPeriod'];
    					$preapprovalPinType = $nvpResArray['pinType'];
    					$preapprovalStartingDate = $nvpResArray['startingDate'];
    					$preapprovalStatus = $nvpResArray['status'];
    					if($preapprovalCurPayments == $preapprovalMaximumNumber){
    						$preapprovalStatus = 'EXPIRED';
    						Mage::getModel('paypaladaptive/save')->updatePreapprovalData($preapprovalId, $preapprovalKey, $preapprovalAck, $preapprovalApproved, $preapprovalCurPayments, $preapprovalCurPaymentsAmount, $preapprovalCurPeriodAttempts, $preapprovalCurPeriodEndingDate, $preapprovalCurrencyCode, $preapprovalDateOfMonth, $preapprovalDayOfWeek, $preapprovalEndingDate, $preapprovalMaximumAmount, $preapprovalMaximumNumber, $preapprovalMaxTotalAmt, $preapprovalPaymentPeriod, $preapprovalPinType, $preapprovalStartingDate, $preapprovalStatus);
    					}else{
    						Mage::getModel('paypaladaptive/save')->updatePreapprovalData($preapprovalId, $preapprovalKey, $preapprovalAck, $preapprovalApproved, $preapprovalCurPayments, $preapprovalCurPaymentsAmount, $preapprovalCurPeriodAttempts, $preapprovalCurPeriodEndingDate, $preapprovalCurrencyCode, $preapprovalDateOfMonth, $preapprovalDayOfWeek, $preapprovalEndingDate, $preapprovalMaximumAmount, $preapprovalMaximumNumber, $preapprovalMaxTotalAmt, $preapprovalPaymentPeriod, $preapprovalPinType, $preapprovalStartingDate, $preapprovalStatus);
    					}
    					
    				
    			} catch (Exception $e) {
    				Mage::getSingleton('checkout/session')->addError($e->getMessage());
    				return;
    			}
    		}
    	}
    }
    
    public function generateCharacter() {
    	$possible = "1234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
    	$char = substr($possible, mt_rand(0, strlen($possible) - 1), 1);
    	return $char;
    }
    public function generateTrackingID() {
    	$GUID = $this->generateCharacter() . $this->generateCharacter() . $this->generateCharacter();
    	$GUID .=$this->generateCharacter() . $this->generateCharacter() . $this->generateCharacter();
    	$GUID .=$this->generateCharacter() . $this->generateCharacter() . $this->generateCharacter();
    	return $GUID;
    }
    
    
    public function orderCreation($custId,$price,$productId,$processingFee){
    	$customer = Mage::getModel('customer/customer')->load($custId);
    	$transaction = Mage::getModel('core/resource_transaction');
    	$storeId = $customer->getStoreId();
    	$reservedOrderId = Mage::getSingleton('eav/config')->getEntityType('order')->fetchNewIncrementId($storeId);    	
    	/**
    	 * 	set Currency Info
    	*/
    	$order = Mage::getModel('sales/order')
    	->setIncrementId($reservedOrderId)
    	->setStoreId($storeId)
    	->setQuoteId(0)
    	->setGlobal_currency_code('USD')
    	->setBase_currency_code('USD')
    	->setStore_currency_code('USD')
    	->setOrder_currency_code('USD');    	
    	/**
    	 * set Customer data
    	*/
    	$order->setCustomer_email($customer->getEmail())
    	->setCustomerFirstname($customer->getFirstname())
    	->setCustomerLastname($customer->getLastname())
    	->setCustomerGroupId($customer->getGroupId())
    	->setCustomer_is_guest(0)
    	->setCustomer($customer); 	
    	/**
    	 * Sending Mails
    	 */    	
    	$templeId = (int) Mage::getStoreConfig('airhotels/custom_email/ordermail');    	
    	if ($templeId) {
    		$emailTemplate = Mage::getModel('core/email_template')->load($templeId);
    	} else {    	
    		$emailTemplate = Mage::getModel('core/email_template')
    		->loadDefault('airhotels_custom_email_ordermail');
    	}    	
    	//$customer = Mage::getSingleton('customer/session')->getCustomer();    	 
    	$toMailId = $customer->getEmail();    	 
    	$toName = $customer->getFirstname();    	 
    	$adminEmailId = Mage::getStoreConfig('airhotels/custom_email/admin_email_id');
    	$toAdminEmail = Mage::getStoreConfig("trans_email/ident_$adminEmailId/email");    	
    	$toAdminName = Mage::getStoreConfig("trans_email/ident_$adminEmailId/name");    	 
    	$emailTemplate->setSenderName($toAdminName);    	 
    	$emailTemplate->setSenderEmail($toAdminEmail);    	 
    	$emailTemplateVariables = (array('customername' => $toName, 'orderid' => $reservedOrderId));    	
    	$emailTemplate->setDesignConfig(array('area' => 'frontend'));    	 
    	$emailTemplate->getProcessedTemplate($emailTemplateVariables);    	 
    	$emailTemplate->send($toMailId, $toName,$emailTemplateVariables);    	
    	/**
    	 * set Billing Address
    	*/
    	$billing = $customer->getDefaultBillingAddress();
    	$billingAddress = Mage::getModel('sales/order_address')
    	->setStoreId($storeId)
    	->setAddressType(Mage_Sales_Model_Quote_Address::TYPE_BILLING)
    	->setCustomerId($customer->getId())
    	->setCustomerAddressId($customer->getDefaultBilling())
    	->setCustomer_address_id($billing->getEntityId())
    	->setPrefix($billing->getPrefix())
    	->setFirstname($billing->getFirstname())
    	->setMiddlename($billing->getMiddlename())
    	->setLastname($billing->getLastname())
    	->setSuffix($billing->getSuffix())
    	->setCompany($billing->getCompany())
    	->setStreet($billing->getStreet())
    	->setCity($billing->getCity())
    	->setCountry_id($billing->getCountryId())
    	->setRegion($billing->getRegion())
    	->setRegion_id($billing->getRegionId())
    	->setPostcode($billing->getPostcode())
    	->setTelephone($billing->getTelephone())
    	->setFax($billing->getFax());
    	$order->setBillingAddress($billingAddress);    	
    	/**
    	 * set Shipping Address
    	*/
    	$shipping = $customer->getDefaultShippingAddress();
    	$shippingAddress = Mage::getModel('sales/order_address')
    	->setStoreId($storeId)
    	->setAddressType(Mage_Sales_Model_Quote_Address::TYPE_SHIPPING)
    	->setCustomerId($customer->getId())
    	->setCustomerAddressId($customer->getDefaultShipping())
    	->setCustomer_address_id($shipping->getEntityId())
    	->setPrefix($shipping->getPrefix())
    	->setFirstname($shipping->getFirstname())
    	->setMiddlename($shipping->getMiddlename())
    	->setLastname($shipping->getLastname())
    	->setSuffix($shipping->getSuffix())
    	->setCompany($shipping->getCompany())
    	->setStreet($shipping->getStreet())
    	->setCity($shipping->getCity())
    	->setCountry_id($shipping->getCountryId())
    	->setRegion($shipping->getRegion())
    	->setRegion_id($shipping->getRegionId())
    	->setPostcode($shipping->getPostcode())
    	->setTelephone($shipping->getTelephone())
    	->setFax($shipping->getFax());    	
    	/**
    	 * set Shipping Flatrate
    	*/
    	$order->setShippingAddress($shippingAddress)
    	->setShipping_method('flatrate_flatrate');    	
    	/**
    	 * Payment creation
    	*/
    	$orderPayment = Mage::getModel('sales/order_payment')
    	->setStoreId($storeId)
    	->setCustomerPaymentId(0)
    	->setMethod('paypaladaptive')
    	->setPo_number(' – ');
    	$order->setPayment($orderPayment);    	
    	/**
    	 * need to add code for configurable products if any
    	*/
    	$subTotal = 0;
    	$products = array(
    			$productId => array(
    					'qty' => 1
    			)
    	);
    	foreach ($products as $productId=>$product) {
    		$_product = Mage::getModel('catalog/product')->load($productId);
    		$rowTotal = $price * $product['qty'];
    		$orderItem = Mage::getModel('sales/order_item')
    		->setStoreId($storeId)
    		->setQuoteItemId(0)
    		->setQuoteParentItemId(NULL)
    		->setProductId($productId)
    		->setProductType($_product->getTypeId())
    		->setQtyBackordered(NULL)
    		->setTotalQtyOrdered($product['rqty'])
    		->setQtyOrdered($product['qty'])
    		->setName($_product->getName())
    		->setSku($_product->getSku())
    		->setPrice($_product->getPrice())
    		->setBasePrice($_product->getPrice())
    		->setOriginalPrice($_product->getPrice())
    		->setRowTotal($rowTotal)
    		->setFeeAmount($processingFee)
    		->setbaseFeeAmount($processingFee)
    		->setBaseRowTotal($rowTotal+$processingFee);    	
    		$subTotal += $rowTotal;
    		$order->addItem($orderItem);
    	}    	
    	$order->setSubtotal($rowTotal+$processingFee)
    	->setBaseSubtotal($rowTotal+$processingFee)
    	->setGrandTotal($rowTotal+$processingFee)
    	->setBaseGrandTotal($rowTotal+$processingFee);
    	$transaction->addObject($order);
    	$transaction->addCommitCallback(array($order, 'place'));
    	$transaction->addCommitCallback(array($order, 'save'));
    	$transaction->save(); 
    	/**
    	 *  Create Invoice
    	*/
    	try {
    		if(!$order->canInvoice()){
    			Mage::throwException(Mage::helper('core')->__('Cannot create an invoice.'));
    		}    	
    		$invoice = Mage::getModel('sales/service_order', $order)->prepareInvoice(); 
    		if (!$invoice->getTotalQty()) {
    			Mage::throwException(Mage::helper('core')->__('Cannot create an invoice without products.'));
    		}    	
    		$invoice->setRequestedCaptureCase(Mage_Sales_Model_Order_Invoice::CAPTURE_ONLINE);
    		$invoice->register();
    		$transactionSave = Mage::getModel('core/resource_transaction')
    		->addObject($invoice)
    		->addObject($invoice->getOrder());    	
    		$transactionSave->save();
    	}
    	catch (Mage_Core_Exception $e) {
    		echo $e->getMessage();
    	}    	
    	/**
    	 * Set the Comment History.
    	 */
    	$status = Mage::helper('paypaladaptive')->getOrderSuccessStatus();
    	$order->setData('state', $status);
    	$order->setStatus($status);
    	$history = $order->addStatusHistoryComment('Preapproval Partial amount of captured automatically.', true);
    	$history->setIsCustomerNotified(true);
    	$order->save();
    		
    	
    	
    }
}
