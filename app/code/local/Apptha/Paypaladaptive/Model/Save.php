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
 * @abstract      : Model File
 * @Creation Date : January 16,2014
 * @Modified By   : Ramkumar M
 * @Modified Date : January 16,2014
 * */
/*
 * ********************************************************* */

class Apptha_Paypaladaptive_Model_Save {
	
	
	/**
	 * Save preapproval payment details to paypaladaptivepreapproval table
	 *
	 * @param string $preapprovalApproved approved
	 * @param int $preapprovalCurPayments currentpayments
	 * @param decimal $preapprovalCurPaymentsAmount current payment amount
	 * @param int $preapprovalCurPeriodAttempts current attempts
	 * @param date $preapprovalCurPeriodEndingDate cur period end date
	 * @param string $preapprovalCurrencyCode currency code
	 * @param int $preapprovalDateOfMonth preapproval date of month
	 * @param string $preapprovalDayOfWeek day of week
	 * @param date $preapprovalEndingDate end date
	 * @param decimal $preapprovalMaximumAmount maximum amount
	 * @param int $preapprovalMaximumNumber maximum number
	 * @param decimal $preapprovalMaxTotalAmt maximum total amount
	 * @param string $preapprovalPaymentPeriod payment period
	 * @param int $preapprovalPinType pin type
	 * @param date $preapprovalStartingDate starting date
	 * @param string $preapprovalStatus status
	 */
	
	public function savePreapprovalData($preapprovalKey, $preapprovalCurPayments, $preapprovalCurPaymentsAmount, $preapprovalCurPeriodAttempts, $preapprovalCurPeriodEndingDate,  $preapprovalDateOfMonth, $preapprovalDayOfWeek, $preapprovalEndingDate, $preapprovalMaximumAmount, $preapprovalMaximumNumber, $preapprovalMaxTotalAmt, $preapprovalPaymentPeriod,$preapprovalStartingDate, $preapprovalStatus, $realOrderId, $orderId, $productId,$custId,$storeId,$preapprovalCurrencyCode,$subId,$preapprovalPinType) {
		/*
		 * Assigning seller payment data
		*/
		try {
			if($preapprovalKey != ''){
				Mage::getModel('paypaladaptive/preapprovaldetails')
				->setPreapproval($preapprovalKey)
				->setCurPayments($preapprovalCurPayments)
				->setCurPaymentsAmount($preapprovalCurPaymentsAmount)
				->setCurPeriodAttempts($preapprovalCurPeriodAttempts)
				->setCurPeriodEndingDate($preapprovalCurPeriodEndingDate)
				->setDateOfMonth($preapprovalDateOfMonth)
				->setDayOfWeek($preapprovalDayOfWeek)
				->setEndingDate($preapprovalEndingDate)
				->setMaxAmountPerPayment($preapprovalMaximumAmount)
				->setMaxNumberOfPayments($preapprovalMaximumNumber)
				->setMaxTotalAmountOfAllPayments($preapprovalMaxTotalAmt)
				->setPaymentPeriod($preapprovalPaymentPeriod)
				->setStartingDate($preapprovalStartingDate)
				->setStatus($preapprovalStatus)
				->setRealOrderId($realOrderId)
				->setOrderId($orderId)
				->setProductId($productId)
				->setCustId($custId)
				->setStoreId($storeId)
				->setCurrencyCode($preapprovalCurrencyCode)
				->setSubId($subId)
				->setPin($preapprovalPinType)
				->save();
			}
		} catch (Mage_Core_Exception $e) {
			Mage::getSingleton('checkout/session')->addError($e->getMessage());
			echo 'Error:'.$e->getMessage();
			return;
		}
	}
	
	

    /**
     * Saving payment details to db
     */ 
    public function saveOrderData($orderId, $invoiceId, $dataSellerId, $dataAmount, $dataCommissionFee, $dataCurrencyCode, $dataPayKey, $dataGroupType, $dataTrackingId, $grandTotal) {

        /**
         * If checking whether seller or owner for store data
         */ 
        try {
            $paymentCollection = Mage::getModel('paypaladaptive/paypaladaptivedetails')->getCollection()
                    ->addFieldToFilter('seller_invoice_id', $invoiceId)
                    ->addFieldToFilter('seller_id', $dataSellerId);

            if (count($paymentCollection) >= 1) {

                /**
                 * Assign table prefix if it's exist
                 */ 
                try {
                    $table_name = Mage::getSingleton('core/resource')->getTableName('paypaladaptivedetails');
                    $connection = Mage::getSingleton('core/resource')
                            ->getConnection('core_write');
                    $connection->beginTransaction();
                    $where[] = $connection->quoteInto('seller_invoice_id = ?', $invoiceId);
                    $where[] = $connection->quoteInto('seller_id = ?', $dataSellerId);
                    $connection->delete($table_name, $where);
                    $connection->commit();
                } catch (Mage_Core_Exception $e) {
                    Mage::getSingleton('checkout/session')->addError($e->getMessage());
                    return;
                }
            }

            /**
             * Assigning seller payment data
             */ 
            $collections = Mage::getModel('paypaladaptive/paypaladaptivedetails');
            $collections->setSellerInvoiceId($invoiceId);
            $collections->setOrderId($orderId);
            $collections->setSellerId($dataSellerId);
            $collections->setSellerAmount($dataAmount);
            $collections->setCommissionAmount($dataCommissionFee);
            $collections->setGrandTotal($grandTotal);
            $collections->setCurrencyCode($dataCurrencyCode);
            $collections->setOwnerPaypalId(Mage::helper('paypaladaptive')->getAdminPaypalId());
            $collections->setPayKey($dataPayKey);
            $collections->setGroupType($dataGroupType);
            $collections->setTrackingId($dataTrackingId);
            $collections->setTransactionStatus('Pending');
            $collections->save();
        } catch (Mage_Core_Exception $e) {
            Mage::getSingleton('checkout/session')->addError($e->getMessage());
            return;
        }
    }

    /**
     * Update transaction id and status in paypaladaptivedetails table
     *
     * @param string $dataPayKey PayPal pay key
     * @param string $dataTrackingId PayPal tracking id
     * @param string $receiverTransactionId receiver transaction id
     * @param string $receiverTransactionStatus receiver transaction status
     * @param string $senderEmail sender PayPal mail id
     * @param string $receiverEmail receiver PayPal mail id
     * @param string $receiverInvoiceId receiver receiver invoice id  
     */
    public function update($payKey, $trackingId, $receiverTransactionId, $receiverTransactionStatus, $senderEmail, $receiverEmail, $receiverInvoiceId) {

        $collections = Mage::getModel('paypaladaptive/paypaladaptivedetails')->getCollection()
                ->addFieldToFilter('pay_key', $payKey)
                ->addFieldToFilter('tracking_id', $trackingId)
                ->addFieldToFilter('seller_id', $receiverEmail)
                ->addFieldToFilter('seller_invoice_id', $receiverInvoiceId);

        if (count($collections) >= 1) {
            try {
                /*
                 * Change transaction status first letter capital 
                 */
                $receiverTransactionStatus = str_replace('\' ', '\'', ucwords(str_replace('\'', '\' ', strtolower($receiverTransactionStatus))));

                $table_name = Mage::getSingleton('core/resource')->getTableName('paypaladaptivedetails');
                $connection = Mage::getSingleton('core/resource')
                        ->getConnection('core_write');
                $connection->beginTransaction();
                $fields = array();
                $fields['seller_transaction_id'] = $receiverTransactionId;
                $fields['buyer_paypal_mail'] = $senderEmail;
                $fields['transaction_status'] = $receiverTransactionStatus;
                $where[] = $connection->quoteInto('pay_key = ?', $payKey);
                $where[] = $connection->quoteInto('tracking_id = ?', $trackingId);
                $where[] = $connection->quoteInto('seller_invoice_id = ?', $receiverInvoiceId);
                $where[] = $connection->quoteInto('seller_id = ?', $receiverEmail);
                $connection->update($table_name, $fields, $where);
                $connection->commit();
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('checkout/session')->addError($e->getMessage());
                return;
            }
        }
    }

    // Saving refund details to db
    public function refund($orderId, $incrementId, $payKey, $trackingId, $transactionId, $encryptedRefundTransactionId, $refundStatus, $refundNetAmount, $refundFeeAmount, $refundGrossAmount, $refundTransactionStatus, $receiverEmail, $currencyCode) {

        try {
            $payDetails = Mage::getModel('paypaladaptive/paypaladaptivedetails')->getCollection()
                    ->addFieldToFilter('seller_invoice_id', $incrementId)
                    ->addFieldToFilter('pay_key', $payKey)
                    ->addFieldToFilter('tracking_id', $trackingId)
                    ->addFieldToFilter('seller_id', $receiverEmail);

            $firstRow = Mage::helper('paypaladaptive')->getFirstRowData($payDetails);

            if (!empty($firstRow['buyer_paypal_mail'])) {
                $buyerPaypalMail = $firstRow['buyer_paypal_mail'];
            } else {
                $buyerPaypalMail = '';
            }

            // Changing transaction status first letter capital 
            $refundStatus = str_replace('\' ', '\'', ucwords(str_replace('\'', '\' ', strtolower($refundStatus))));
            
            // Assigning seller payment data
            $collections = Mage::getModel('paypaladaptive/refunddetails');
            $collections->setIncrementId($incrementId);
            $collections->setOrderId($orderId);
            $collections->setSellerPaypalId($receiverEmail);
            $collections->setPayKey($payKey);
            $collections->setTrackingId($trackingId);
            $collections->setTransactionId($transactionId);
            $collections->setEncryptedRefundTransactionId($encryptedRefundTransactionId);
            $collections->setRefundNetAmount($refundNetAmount);
            $collections->setRefundFeeAmount($refundFeeAmount);
            $collections->setRefundGrossAmount($refundGrossAmount);
            $collections->setbuyerPaypalMail($buyerPaypalMail);
            $collections->setRefundTransactionStatus($refundTransactionStatus);
            $collections->setRefundStatus($refundStatus);
            $collections->setCurrencyCode($currencyCode);

            $collections->save();
        } catch (Mage_Core_Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            return;
        }
    }

    public function sellerPaypalIdForRefund($incrementId, $sellerId) {

        $collections = Mage::getModel('paypaladaptive/paypaladaptivedetails')->getCollection()
                ->addFieldToFilter('seller_invoice_id', $incrementId)
                ->addFieldToFilter('seller_id', $sellerId);

        $sellerPaypalId = '';
        $firstRow = Mage::helper('paypaladaptive')->getFirstRowData($collections);
        if (!empty($firstRow)) {
            $sellerPaypalId = $firstRow['seller_id'];
        }

        return $sellerPaypalId;
    }

    public function sellerDataForRefund($items, $incrementId, $flag) {

        $sellerData = array();

        /**
         * Preparing seller share
         */  

        foreach ($items as $item) {
		
            $sellerAmount = 0;
            $productId = $item->getProductId();

            $commissionData = Mage::getModel('paypaladaptive/commissiondetails')->getCollection()
                    ->addFieldToFilter('product_id', $productId)
                    ->addFieldToFilter('increment_id', $incrementId);
            $firstRow = Mage::helper('paypaladaptive')->getFirstRowData($commissionData);

            if (!empty($firstRow['seller_id'])) {
                $commisionValue = $firstRow['commission_value'];
                $commissionMode = $firstRow['commission_mode'];
                $sellerId 		= $firstRow['seller_id'];
                $processingFee	= $firstRow['processing_fee'];
                /**
                 * Geting Price From Database
                 */ 
                $price					= $firstRow['grand_total'];
                $productAmount 		    = $price - $processingFee;
                
                $config 			= Mage::getStoreConfig ( 'airhotels/custom_group' );

                if ($flag == 1) {
                   // $productAmount 	= $item->getPrice() * $item->getQtyInvoiced();
                    $productAmount		= $firstRow['grand_total'];
                } else {
                    //$productAmount = $item->getPrice() * $item->getQty();
                    $productAmount		= $firstRow['grand_total'];
                }
                $percentPerProduct = Mage::getStoreConfig('airhotels/custom_group/airhotels_hostfee');

                
                if ($commissionMode == 'percent') {
                    $productCommission = $productAmount * ($commisionValue / 100);
                    $sellerAmount = $productAmount - $productCommission;
                } else {
                    $productCommission = $commisionValue;
                    $sellerAmount = $productAmount - $commisionValue;
                }

                /**
                 * Calculating seller share individually
                 */ 
                if (array_key_exists($sellerId, $sellerData)) {
                    $sellerData[$sellerId]['amount'] = $sellerData[$sellerId]['amount'] + $sellerAmount;
                    $sellerData[$sellerId]['commission_fee'] = $sellerData[$sellerId]['commission_fee'] + $productCommission;
                } else {
                    $sellerData[$sellerId]['amount'] 			= $sellerAmount;
                    $sellerData[$sellerId]['commission_fee'] 	= $productCommission;
                    $sellerData[$sellerId]['seller_id'] 		= $sellerId;
                    $sellerData[$sellerId]['processing_fee'] 	= $processingFee;
                    $sellerData[$sellerId]['grand_total'] 		= $productAmount;
                }
            }
        }
        return $sellerData;
    }

    /**
     * Storing commission details to database table
     */ 
    public function saveCommissionData($incrementId, $productId, $commisionValue, $commissionMode, $sellerId, $processingFee, $productAmount) {

        try {
            $commissionData = Mage::getModel('paypaladaptive/commissiondetails')->getCollection()
                    ->addFieldToFilter('product_id', $productId)
                    ->addFieldToFilter('increment_id', $incrementId);
            $firstRow = Mage::helper('paypaladaptive')->getFirstRowData($commissionData);

            if (!empty($firstRow['product_id']) && $firstRow['product_id'] == $productId) {

                $table_name = Mage::getSingleton('core/resource')->getTableName('paypaladaptivecommissiondetails');
                $connection = Mage::getSingleton('core/resource')
                        ->getConnection('core_write');
                $connection->beginTransaction();
                $fields = array();
                $fields['grand_total'] = $productAmount;
                $fields['processing_fee'] = $processingFee;
                $fields['commission_mode'] = $commissionMode;
                $fields['commission_value'] = $commisionValue;
                $fields['seller_id'] = $sellerId;
                $where[] = $connection->quoteInto('product_id = ?', $productId);
                $where[] = $connection->quoteInto('increment_id = ?', $incrementId);
                $connection->update($table_name, $fields, $where);
                $connection->commit();
            } else {

                /**
                 * Assigning seller payment data
                 */ 
                $collections = Mage::getModel('paypaladaptive/commissiondetails');
                $collections->setProductId($productId);
                $collections->setIncrementId($incrementId);
                $collections->setCommissionMode($commissionMode);
                $collections->setCommissionValue($commisionValue);
                $collections->setProcessingFee($processingFee);
                $collections->setGrandTotal($productAmount);
                $collections->setSellerId($sellerId);
                $collections->save();
            }
        } catch (Exception $e) {
            Mage::getSingleton('checkout/session')->addError($e->getMessage());
            return;
        }
    }

    /**
     * Change payment status to refunded
     */ 
    public function changePaymentStatus($incrementId, $payKey, $trackingId, $receiverEmail) {

        $collections = Mage::getModel('paypaladaptive/paypaladaptivedetails')->getCollection()
                ->addFieldToFilter('pay_key', $payKey)
                ->addFieldToFilter('tracking_id', $trackingId)
                ->addFieldToFilter('seller_id', $receiverEmail)
                ->addFieldToFilter('seller_invoice_id', $incrementId);

        if (count($collections) >= 1) {

            // Assign table prefix if it's exist
            try {
                $table_name = Mage::getSingleton('core/resource')->getTableName('paypaladaptivedetails');
                $connection = Mage::getSingleton('core/resource')
                        ->getConnection('core_write');
                $connection->beginTransaction();
                $fields = array();
                $fields['transaction_status'] = 'Refunded';
                $where[] = $connection->quoteInto('pay_key = ?', $payKey);
                $where[] = $connection->quoteInto('tracking_id = ?', $trackingId);
                $where[] = $connection->quoteInto('seller_invoice_id = ?', $incrementId);
                $where[] = $connection->quoteInto('seller_id = ?', $receiverEmail);
                $connection->update($table_name, $fields, $where);
                $connection->commit();
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                return;
            }
        }
    }

    // Change payment status to canceled
    public function cancelPayment($paypalAdaptive, $payKey, $trackingId) {

        $collections = Mage::getModel('paypaladaptive/paypaladaptivedetails')->getCollection()
                ->addFieldToFilter('pay_key', $payKey)
                ->addFieldToFilter('tracking_id', $trackingId)
                ->addFieldToFilter('seller_invoice_id', $paypalAdaptive);

        if (count($collections) >= 1) {

            // Assign table prefix if it's exist
            try {
                $table_name = Mage::getSingleton('core/resource')->getTableName('paypaladaptivedetails');
                $connection = Mage::getSingleton('core/resource')
                        ->getConnection('core_write');
                $connection->beginTransaction();
                $fields = array();
                $fields['transaction_status'] = 'Canceled';
                $where[] = $connection->quoteInto('pay_key = ?', $payKey);
                $where[] = $connection->quoteInto('tracking_id = ?', $trackingId);
                $where[] = $connection->quoteInto('seller_invoice_id = ?', $paypalAdaptive);
                $connection->update($table_name, $fields, $where);
                $connection->commit();
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('checkout/session')->addError($e->getMessage());
                return;
            }
        }
    }
    
    /**
     * Save payment details to paypaladaptivedetails table
     *
     * @param int $orderId order id
     * @param int $invoiceId invoice id
     * @param string $dataSellerId receiver id
     * @param decimal $dataAmount receiver amount
     * @param decimal $dataCommissionFee receiver commission
     * @param string $dataCurrencyCode currency code
     * @param string $dataPayKey PayPal pay key
     * @param string $dataGroupType receiver group type
     * @param string $dataTrackingId PayPal tracking id
     * @param decimal $grandTotal Order grand total
     * @param string $paymentMethod payment method
     */
    public function saveCronOrderData($orderId, $invoiceId, $dataSellerId, $sellerTransactionId, $dataAmount, $dataCommissionFee, $price, $dataCurrencyCode, $dataPayKey, $dataGroupType, $dataTrackingId, $grandTotal, $paymentMethod, $preapprovalKey, $feesPayer,$buyerPaypalMail) {
    
    	/*
    	 * If checking whether seller or owner for store data
    	*/
    	try {
    		/*
    		 * Assigning seller payment data
    		*/
    		$collections = Mage::getModel('paypaladaptive/paypaladaptivedetails');
    		$collections->setSellerInvoiceId($invoiceId);
    		$collections->setOrderId($orderId);
    		$collections->setSellerId($dataSellerId);
    		$collections->setSellerAmount($dataAmount);
    		$collections->setSellerTwoAmount($dataCommissionFee);
    		$collections->setAdminAmount($price);
    		$collections->setGrandTotal($grandTotal);
    		$collections->setCurrencyCode($dataCurrencyCode);
    		$collections->setOwnerPaypalId(Mage::helper('paypaladaptive')->getAdminPaypalId());
    		$collections->setPayKey($dataPayKey);
    		$collections->setGroupType($dataGroupType);
    		$collections->setSellerTransactionId($sellerTransactionId);
    		$collections->setTrackingId($dataTrackingId);
    		$collections->setTransactionStatus('Completed');
    		$collections->setPaymentMethod($paymentMethod);
    		$collections->setPreapprovalKey($preapprovalKey);
    		$collections->setBuyerPaypalMail($buyerPaypalMail);
    		$collections->setFeesPayer($feesPayer);
    		$collections->save();
    	} catch (Mage_Core_Exception $e) {
    		Mage::getSingleton('checkout/session')->addError($e->getMessage());
    		return;
    	}
    }
    
    
    public function updatePreapprovalData($preapprovalId, $preapprovalKey, $preapprovalAck, $preapprovalApproved, $preapprovalCurPayments, $preapprovalCurPaymentsAmount, $preapprovalCurPeriodAttempts, $preapprovalCurPeriodEndingDate, $preapprovalCurrencyCode, $preapprovalDateOfMonth, $preapprovalDayOfWeek, $preapprovalEndingDate, $preapprovalMaximumAmount, $preapprovalMaximumNumber, $preapprovalMaxTotalAmt, $preapprovalPaymentPeriod, $preapprovalPinType, $preapprovalStartingDate, $preapprovalStatus){
    	$data = array('responseEnvelope_ack'=>$preapprovalAck,'approved'=>$preapprovalApproved,'cur_payments'=>$preapprovalCurPayments,'cur_payments_amount'=>$preapprovalCurPaymentsAmount,'cur_period_attempts'=>$preapprovalCurPeriodAttempts,'cur_period_ending_date'=>$preapprovalCurPeriodEndingDate,'currency_code'=>$preapprovalCurrencyCode,'date_of_month'=>$preapprovalDateOfMonth,'day_of_week'=>$preapprovalDayOfWeek,'ending_date'=>$preapprovalEndingDate,'max_amount_per_payment'=>$preapprovalMaximumAmount,'max_number_of_payments'=>$preapprovalMaximumNumber,'max_total_amount_of_all_payments'=>$preapprovalMaxTotalAmt,'payment_period'=>$preapprovalPaymentPeriod,'pin_type'=>$preapprovalPinType,'starting_date'=>$preapprovalStartingDate,'status'=>$preapprovalStatus);
    	$model = Mage::getModel('paypaladaptive/preapprovaldetails')->load($preapprovalId)->addData($data);
    	try {
    		$model->setPreapprovalId($preapprovalId)->save();
    	} catch (Mage_Core_Exception $e){
    		Mage::getSingleton('checkout/session')->addError($e->getMessage());
    	}
    
    }
    

}