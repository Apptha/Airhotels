<?php

/*
 * ********************************************************* */

/**
 * @name          : Apptha Paypal Adaptive
 * @version	  : 1.0
 * @package       : Apptha
 * @since         : Magento 1.5
 * @subpackage    : Paypal Adaptive
 * @author        : Apptha - http://www.apptha.com
 * @copyright     : Copyright (C) 2013 Powered by Apptha
 * @license       : GNU/GPL http://www.gnu.org/licenses/gpl-3.0.html
 * @abstract      : Apicall File
 * @Creation Date : January 13,2014
 * @Modified By   : Ramkumar M
 * @Modified Date : January 23,2014
 * */
/*
 * ********************************************************* */

class Apptha_Paypaladaptive_Model_Apicall {
	
	
	/**
	 * Collect the information to make the preapproval
	*
	* @param string $cancelUrl cancel url
	* @param string $returnUrl return url
	* @param string $currencyCode currency code
	* @param string $ipnNotificationUrl url
	* @param string $startingDate start date
	* @param string $endingDate end date
	* @param string $maxAmountPerPayment maximum amount per payment
	* @param string $maxNumberOfPayments maximum no of payments
	* @param string $maxTotalAmountOfAllPayments total amount of all payments
	* @return array of Preapproval key response
	*/
	
	public function CallPreapproval($returnUrl,$cancelUrl,$ipnNotificationUrl,$senderEmail,$currencyCode,$startingDate,$endingDate,$maxAmountPerPayment,$maxNumberOfPayments,$maxTotalAmountOfAllPayments,$periodUnit){
		 
		
		switch($periodUnit)
		{
			case 1:
				$paymentPeriod = 'DAILY';
				break;
			case 2:
				$paymentPeriod = 'WEEKLY';
				break;
			case 4:
				$paymentPeriod = 'MONTHLY';
				break;
			case 5:
				$paymentPeriod = 'ANNUALLY';
				break;
		}
		$nvpstr = "returnUrl=". urlencode($returnUrl) ."&cancelUrl=";
		$nvpstr .= urlencode($cancelUrl) . "&startingDate=";
		$nvpstr .= urlencode($startingDate) . "&endingDate=";
		$nvpstr .= urlencode($endingDate) . "&maxAmountPerPayment=";
		$nvpstr .= urlencode($maxAmountPerPayment) . "&maxNumberOfPayments=";
		$nvpstr .= urlencode($maxNumberOfPayments) . "&maxTotalAmountOfAllPayments=";		
		$nvpstr .= urlencode($maxTotalAmountOfAllPayments) . "&currencyCode=" ;
		$nvpstr .= urlencode($currencyCode) . "&paymentPeriod=". urlencode($paymentPeriod);
		if ("" != $senderEmail)
		{
			$nvpstr .= "&senderEmail=" . urlencode($senderEmail);
		}

		$resArray = $this->hashCall("Preapproval", $nvpstr);
		return $resArray;
	}
	

	/**
	 * Collect the information of the preapproval profile details
	*
	* @param string $preapprovalKey preapproval key
	* @return array of Preapproval profile details
	*/
	
	public function CallPreapprovalDetails($ack,$preapprovalKey) {
		$nvpstr = "preapprovalKey=". urlencode($preapprovalKey);
		$resArray = $this->hashCall("PreapprovalDetails", $nvpstr);
		return $resArray;
	}
	
    /**
     * Pay call to PayPal 
     * 
     * @param string $methodName call method
     * @param string $nvpStr NVPRequest
     * 
     * @return array PayPal response
     */

    function hashCall($methodName, $nvpStr) {
        /*
         * Set the curl parameters     
         */
        $ApiUserName = Mage::helper('paypaladaptive')->getApiUserName();
        $ApiPassword = Mage::helper('paypaladaptive')->getApiPassword();
        $ApiSignature = Mage::helper('paypaladaptive')->getApiSignature();
        $ApiAppID = Mage::helper('paypaladaptive')->getAppID();
        $mode = Mage::helper('paypaladaptive')->getPaymentMode();

        if ($mode == 1) {
            $ApiEndpoint = "https://svcs.sandbox.paypal.com/AdaptivePayments";
            $ApiEndpoint .= "/" . $methodName;
        } else {
            $ApiEndpoint = "https://svcs.paypal.com/AdaptivePayments";
            $ApiEndpoint .= "/" . $methodName;
        }

        try {

            $curl = new Varien_Http_Adapter_Curl();
            /*
             * See DetailLevelCode in the WSDL 
             */
            $detailLevel = urlencode("ReturnAll");

            /*
             * For valid enumerations
             * This should be the standard RFC 
             */
            $errorLanguage = urlencode("en_US");

            /*
             * NVPRequest for submitting to server
             */
            $nvpreq = "requestEnvelope.errorLanguage=$errorLanguage&requestEnvelope";
            $nvpreq .= "detailLevel=$detailLevel&$nvpStr";

            /*
             * The below line for SSL 
             */
            //$config = array('timeout' => 60,'verifypeer' => true,'verifyhost' => 2);

            $config = array('timeout' => 60, 'verifypeer' => FALSE, 'verifyhost' => FALSE);
            $curl->setConfig($config);

            /*
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
                /*
                 * Execute the Error handling module to display errors
                 */
                Mage::getSingleton('checkout/session')->addError($curl->getError());
                return;
            } else {
                /*
                 * Convert NVPResponse to an Associative Array  
                 */
                $nvpResArray = $this->deformatNVP($data);
                /*
                 * Close curl
                 */
                $curl->close();
            }
            /*
             * Return Response data
             */
            return $nvpResArray;
        } catch (Exception $e) {
            Mage::getSingleton('checkout/session')->addError($e->getMessage());
            return;
        }
    }
	
    
    
    /*
     * Prepares the parameters for the PaymentDetails API Call    
     * 
     * @param string $payKey PayPal pay key
     * @param string $transactionId PayPal transaction id
     * @param string $trackingId Paypal tracking id
     * 
     * @return array PayPal response
     */

    public function CallPaymentDetails($payKey, $transactionId, $trackingId) {

        /*
         * Collection the information to make the PaymentDetails call        
         */
        $nvpstr = "";
        if ("" != $payKey) {
            $nvpstr = "payKey=" . urlencode($payKey);
        } elseif ("" != $transactionId) {
            $nvpstr = "transactionId=" . urlencode($transactionId);
        } elseif ("" != $trackingId) {
            $nvpstr = "trackingId=" . urlencode($trackingId);
        }
        /*
         * Make the PaymentDetails call to PayPal
         */
        $resArray = $this->hashCall("PaymentDetails", $nvpstr);
        return $resArray;
    }

    /*
     * This function will take NVPString and convert it to an Associative Array   
     * 
     * @param string $nvpstr request     
     * @return array decoded request 
     */

    public function deformatNVP($nvpstr) {
        $intial = 0;
        $nvpArray = array();

        while (strlen($nvpstr)) {
            $keypos = strpos($nvpstr, '=');
            $valuepos = strpos($nvpstr, '&') ? strpos($nvpstr, '&') : strlen($nvpstr);
            $keyval = substr($nvpstr, $intial, $keypos);
            $valval = substr($nvpstr, $keypos + 1, $valuepos - $keypos - 1);
            $nvpArray[urldecode($keyval)] = urldecode($valval);
            $nvpstr = substr($nvpstr, $valuepos + 1, strlen($nvpstr));
        }
        return $nvpArray;
    }

    /*
    * Collect the information to make the Pay call
    *
    * @param string $actionType action type
    * @param string $cancelUrl cancel url
    * @param string $returnUrl return url
    * @param string $currencyCode currency code
    * @param array $receiverEmailArray receiver email
    * @param array $receiverAmountArray receiver amount
    * @param array $receiverPrimaryArray receiver primary value
    * @param array $receiverInvoiceIdArray receiver invoice
    * @param string $feesPayer fees payer type
    * @param string $ipnNotificationUrl url
    * @param string $memo memo
    * @param string $pin pin
    * @param string $preapprovalKey preapproval key
    * @param string $reverseAllParallelPaymentsOnError error type
    * @param string $senderEmail ender email
    * @param string $trackingId PayPayl tracking id
    * @return array PayPal response
    */
    
     /**
     *  Collecting the information to make the Pay call
     */
    public function CallPayFuture($actionType, $cancelUrl, $returnUrl, $currencyCode, $receiverEmailArray, $receiverAmountArray, $receiverPrimaryArray, $receiverInvoiceIdArray, $feesPayer, $ipnNotificationUrl, $memo, $pin, $preapprovalKey, $reverseAllParallelPaymentsOnError, $senderEmail, $trackingId) {
    
         $memo = $pin = $senderEmail = '';
        

        $nvpstr = "actionType=" . urlencode($actionType) . "&currencyCode=";
        $nvpstr .= urlencode($currencyCode) . "&returnUrl=";
        $nvpstr .= urlencode($returnUrl) . "&cancelUrl=" . urlencode($cancelUrl);
        
               
        if (0 != count($receiverAmountArray)) {          
            $nvpstr .= $this->receiverAmountData($receiverAmountArray,$nvpstr);                
        }
        
        if (0 != count($receiverEmailArray)) {            
        $nvpstr .= $this->receiverEmailData($receiverEmailArray,$nvpstr);       
        }
        
        if (0 != count($receiverPrimaryArray)) {            
        $nvpstr .= $this->receiverPrimaryData($receiverPrimaryArray,$nvpstr);     
        }
        
        if (0 != count($receiverInvoiceIdArray)) {            
        $nvpstr .= $this->receiverInvoiceIdData($receiverInvoiceIdArray,$nvpstr);     
        }
        
        /**
         * Optional fields for pay call
         */ 
        if ("" != $feesPayer) {
            $nvpstr .= "&feesPayer=" . urlencode($feesPayer);
        }
        if ("" != $ipnNotificationUrl) {
            $nvpstr .= "&ipnNotificationUrl=" . urlencode($ipnNotificationUrl);
        }        
       
        if ("" != $reverseAllParallelPaymentsOnError) {
            $nvpstr .= "&reverseAllParallelPaymentsOnError=";
            $nvpstr .= urlencode($reverseAllParallelPaymentsOnError);
        }
    
        if ("" != $trackingId) {
            $nvpstr .= "&trackingId=" . urlencode($trackingId);
        }

        /**
         * Make the Pay call to PayPal
         */ 
        $resArray = $this->hashCall("Pay", $nvpstr);

        /**
         * Return the response array
         */ 
        return $resArray;
    }
    
    
    public function CallPay($actionType, $cancelUrl, $returnUrl, $currencyCode, $receiverEmailArray, $receiverAmountArray, $receiverPrimaryArray, $receiverInvoiceIdArray, $feesPayer, $ipnNotificationUrl, $memo, $pin, $preapprovalKey, $reverseAllParallelPaymentsOnError, $senderEmail, $trackingId) {
    	//$receiverList 	= floatval(3);
    	//$receiverList1 	= floatval(3);
    	$memo = 'Example';
    	$pin  = '';
    	$nvpstr = "actionType=" . urlencode($actionType) . "&currencyCode=";
    	$nvpstr .= urlencode($currencyCode) . "&memo=";
    	$nvpstr .= urlencode($memo) . "&returnUrl=";
    	//$nvpstr .= urlencode($preapprovalKey) . "&returnUrl=";
    	//$nvpstr .= urlencode($returnUrl) . "&receiverList.receiver(0).amount=";
    	//$nvpstr .= urlencode($receiverList) . "&receiverList.receiver(1).amount=";
    	// $nvpstr .= urlencode($preapprovalKey) . "&returnUrl=";
    	//$nvpstr .= urlencode($preapprovalKey) . "&returnUrl=";
    	$nvpstr .= urlencode($returnUrl) . "&cancelUrl=" . urlencode($cancelUrl);
    
    	if ('' != $preapprovalKey) {
    		 
    		$nvpstr .= "&preapprovalKey=" . urlencode($preapprovalKey);
    	}
    	
    
    	if (0 != count($receiverAmountArray)) {
    		 
    		$nvpstr .= $this->receiverAmountData($receiverAmountArray, $nvpstr);
    	}
    
    	if (0 != count($receiverEmailArray)) {
    		 
    		$nvpstr .= $this->receiverEmailData($receiverEmailArray, $nvpstr);
    	}
    
    	if (0 != count($receiverPrimaryArray)) {
    		 
    		$nvpstr .= $this->receiverPrimaryData($receiverPrimaryArray, $nvpstr);
    	}
    	 
    
    	if (0 != count($receiverInvoiceIdArray)) {
    		$nvpstr .= $this->receiverInvoiceIdData($receiverInvoiceIdArray, $nvpstr);
    	}
    
    
    	/*
    	 * Optional fields for pay call
    	*/
    	if ("" != $feesPayer) {
    		$nvpstr .= "&feesPayer=" . urlencode($feesPayer);
    	}
    	if ("" != $ipnNotificationUrl) {
    		$nvpstr .= "&ipnNotificationUrl=" . urlencode($ipnNotificationUrl);
    	}
    
    	if ("" != $reverseAllParallelPaymentsOnError) {
    		$nvpstr .= "&reverseAllParallelPaymentsOnError=";
    		$nvpstr .= urlencode($reverseAllParallelPaymentsOnError);
    	}
    	if ("" != $senderEmail)
    	{
    		$nvpstr .= "&senderEmail=" . urlencode($senderEmail);
    	}
    
    	if ("" != $trackingId) {
    		$nvpstr .= "&trackingId=" . urlencode($trackingId);
    	}
    	/*
    	 * Make the Pay call to PayPal
    	*/
    	 
    	$resArray = $this->hashCall("Pay", $nvpstr);
    	return $resArray;
    }
    
    
    /*
    public function CallPay($actionType, $cancelUrl, $returnUrl, $currencyCode, $receiverEmailArray, $receiverAmountArray, $receiverPrimaryArray, $receiverInvoiceIdArray, $feesPayer, $ipnNotificationUrl, $memo, $pin, $preapprovalKey, $reverseAllParallelPaymentsOnError, $senderEmail, $trackingId) {
    
     	 $memo = $pin =  '';
      
    	$nvpstr = "actionType=" . urlencode($actionType) . "&currencyCode=";
    	$nvpstr .= urlencode($currencyCode) . "&returnUrl=";
    	$nvpstr .= urlencode($returnUrl) . "&cancelUrl=" . urlencode($cancelUrl);
    
    	if ('' != $preapprovalKey) {
    		 
    		$nvpstr .= "&preapprovalKey=" . urlencode($preapprovalKey);
    	}
    	//     	if ("" != $pin)
    		//     	{
    		//     		$nvpstr .= "&pinType=" . urlencode($pin);
    		//     	}
    
    	if (0 != count($receiverAmountArray)) {
    		 
    		$nvpstr .= $this->receiverAmountData($receiverAmountArray, $nvpstr);
    	}
    
    	if (0 != count($receiverEmailArray)) {
    		 
    		$nvpstr .= $this->receiverEmailData($receiverEmailArray, $nvpstr);
    	}
    
    	if (0 != count($receiverPrimaryArray)) {
    		 
    		$nvpstr .= $this->receiverPrimaryData($receiverPrimaryArray, $nvpstr);
    	}
    
    
    	if (0 != count($receiverInvoiceIdArray)) {
    		$nvpstr .= $this->receiverInvoiceIdData($receiverInvoiceIdArray, $nvpstr);
    	}
    
    
   
    	if ("" != $feesPayer) {
    		$nvpstr .= "&feesPayer=" . urlencode($feesPayer);
    	}
    	if ("" != $ipnNotificationUrl) {
    		$nvpstr .= "&ipnNotificationUrl=" . urlencode($ipnNotificationUrl);
    	}
    
    	if ("" != $reverseAllParallelPaymentsOnError) {
    		$nvpstr .= "&reverseAllParallelPaymentsOnError=";
    		$nvpstr .= urlencode($reverseAllParallelPaymentsOnError);
    	}
    
    
    	if ("" != $trackingId) {
    		$nvpstr .= "&trackingId=" . urlencode($trackingId);
    	}
   
    
    	$resArray = $this->hashCall("Pay", $nvpstr);
    	return $resArray;
    } 
    */
    
    /**
     * Prepares the parameters for the Refund API Call
     */    
    function CallRefund($payKey, $transactionId, $trackingId, $receiverEmailArray, $receiverAmountArray, $receiverPrimaryArray, $currencyCode) {

        /**
         * Gather the information to make the Refund call
         */        
        $nvpstr = "currencyCode=";
        $nvpstr .= urlencode($currencyCode);

        /**
         * conditionally required fields
         */ 
        if ("" != $payKey) {
            $nvpstr .= "&payKey=" . urlencode($payKey);
            if (0 != count($receiverEmailArray)) {
               $nvpstr .= $this->receiverEmailData($receiverEmailArray,$nvpstr);  
            }
            if (0 != count($receiverAmountArray)) {
                $nvpstr .= $this->receiverAmountData($receiverAmountArray,$nvpstr);
            } if (0 != count($receiverPrimaryArray)) {
            	$nvpstr .= $this->receiverPrimaryData($receiverPrimaryArray, $nvpstr);
            }
        } elseif ("" != $trackingId) {
            $nvpstr .= "&trackingId=" . urlencode($trackingId);
            if (0 != count($receiverEmailArray)) {
            $nvpstr .= $this->receiverEmailData($receiverEmailArray,$nvpstr);  
            }
            if (0 != count($receiverAmountArray)) {
            $nvpstr .= $this->receiverAmountData($receiverAmountArray,$nvpstr);
            } if (0 != count($receiverPrimaryArray)) {
            	$nvpstr .= $this->receiverPrimaryData($receiverPrimaryArray, $nvpstr);
            }
        } elseif ("" != $transactionId) {
            $nvpstr .= "&transactionId=" . urlencode($transactionId);
            /**
             * the caller should only have 1 entry in the email and amount arrays
             */ 
            if (0 != count($receiverEmailArray)) {
               $nvpstr .= $this->receiverEmailData($receiverEmailArray,$nvpstr);  
            }
            if (0 != count($receiverAmountArray)) {
              $nvpstr .= $this->receiverAmountData($receiverAmountArray,$nvpstr);
            } if (0 != count($receiverPrimaryArray)) {
            	$nvpstr .= $this->receiverPrimaryData($receiverPrimaryArray, $nvpstr);
            }
        }
             
        /**
         * Make the Refund call to PayPal
         */  
        $resArray = $this->hashCall("Refund", $nvpstr);

        /**
         * Return the response array
         */  
        return $resArray;
    }
    
       
    public function receiverAmountData($receiverAmountArray,$nvpstr){    
    reset($receiverAmountArray);
            while (list($key, $value) = each($receiverAmountArray)) {
                if ("" != $value) {
                    $nvpstr .= "&receiverList.receiver(" . $key . ").amount=" . urlencode($value);
                }
            }

    return $nvpstr;       
    }
    
     public function receiverEmailData($receiverEmailArray,$nvpstr){
     reset($receiverEmailArray);
            while (list($key, $value) = each($receiverEmailArray)) {
                if ("" != $value) {
                    $nvpstr .= "&receiverList.receiver(" . $key . ").email=" . urlencode($value);
                }
            }
     return $nvpstr;
     }
     
      public function receiverPrimaryData($receiverPrimaryArray,$nvpstr){
         reset($receiverPrimaryArray);
            while (list($key, $value) = each($receiverPrimaryArray)) {
                if ("" != $value) {
                    $nvpstr = $nvpstr . "&receiverList.receiver(" . $key . ").primary=" .
                            urlencode($value);
                }
            }
      return $nvpstr;      
      }
      
         public function receiverInvoiceIdData($receiverInvoiceIdArray,$nvpstr){    
           reset($receiverInvoiceIdArray);
            while (list($key, $value) = each($receiverInvoiceIdArray)) {
                if ("" != $value) {
                    $nvpstr = $nvpstr . "&receiverList.receiver(" . $key . ").invoiceId=" .
                            urlencode($value);
                }
            }
         return $nvpstr;
         }
    
}