<?php

/**
 * In this class contains delayed chained method functionality like update execute date and execute pay action
 *
 * @package         Apptha PayPal Adaptive
 * @version         0.1.1
 * @since           Magento 1.5
 * @author          Apptha Team
 * @copyright       Copyright (C) 2015 Powered by Apptha
 * @license         http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @Creation Date   January 02,2014
 * @Modified By     Ramkumar M
 * @Modified Date   March 24,2014
 *
 * */
class Apptha_Paypaladaptive_Adminhtml_PreapprovaldetailsController extends Mage_Adminhtml_Controller_Action {
    /**
     * Initiate delay chained details grid 
     */

    protected function _initAction() {
        $this->loadLayout()
                ->_setActiveMenu('paypaladaptive/preapprovaldetails')
                ->_addBreadcrumb(Mage::helper('adminhtml')->__('Pending Preapproval Chained Payment'), Mage::helper('adminhtml')->__('Pending Preapproval Chained Payment'));

        $this->getLayout()->getBlock('head')->setTitle($this->__('Pending Preapproval Chained Payment'));

        return $this;
    }

    /**
     *  Exit action
     */

    public function editAction() {
        $this->loadLayout();
        $this->_addContent($this->getLayout()->createBlock('paypaladaptive/adminhtml_preapprovaldetails_edit'));
        $this->renderLayout();
    }

    /**
     *  Render grid layout
     */

    public function indexAction() {

        $this->_initAction()
                ->renderLayout();
    }

    /**
     *  Render form for execute payment date
     */

    public function setexecutepaymentdateAction() {
        $this->_initAction()
                ->renderLayout();
    }

    /**
     *  Render form for execute payment date
     */

    public function saveexecutepaymentdateAction() {

        $data = $this->getRequest()->getPost();
        $orderId = $this->getRequest()->getParam('order_id');
        $executePaymentDate = $data['executepaymentdate'];
        $paymentExecuteDate = date("Y-m-d", strtotime($executePaymentDate));

        $collections = Mage::getModel('paypaladaptive/preapprovaldetails')->getCollection()
                ->addFieldToFilter('order_id', $orderId);

        if (count($collections) >= 1) {

            foreach ($collections as $order) {
                $createdAt = $order->getCreatedAt();
                break;
            }

            $currentDate = date("Y-m-d H:i:s", Mage::getModel('core/date')->timestamp(time()));
            $currentTime = strtotime(date("Y-m-d H:i:s", strtotime($currentDate)));
            $firstExecuteTime = strtotime(date("Y-m-d H:i:s", strtotime($createdAt)));
            $lastExecuteTime = strtotime(date("Y-m-d H:i:s", strtotime($createdAt)) . " +89 days");
            $executeTime = strtotime(date("Y-m-d H:i:s", strtotime($executePaymentDate)));

            if ($firstExecuteTime >= $executeTime || $currentTime >= $executeTime) {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('paypaladaptive')->__('Error occurred while processing your request. Kindly check execute payment date'));
            } elseif ($lastExecuteTime >= $executeTime) {

                // Assign table prefix if it's exist
                try {

                    $table_name = Mage::getSingleton('core/resource')->getTableName('paypaladaptivedelaychained');
                    $connection = Mage::getSingleton('core/resource')
                            ->getConnection('core_write');
                    $connection->beginTransaction();
                    $fields = array();
                    $fields['executepayment_date'] = $paymentExecuteDate;
                    $where[] = $connection->quoteInto('order_id = ?', $orderId);
                    $connection->update($table_name, $fields, $where);
                    $connection->commit();
                    Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('paypaladaptive')->__('Execute Payment Date Updated successfully'));
                } catch (Mage_Core_Exception $e) {
                    Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                }
            } else {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('paypaladaptive')->__("Execute Payment date  exceeds 90 days from payment credited date. It should be less than 90 days."));
            }
        }
        $this->_redirect('*/*/');
    }

    /**
     * To pay splited money to secondary receivers (seller or host from admin
     */

    public function payAction() {

        $orderId = $this->getRequest()->getParam('order_id');
        $payKey = $this->getRequest()->getParam('pay_key');
        $trackingId = $this->getRequest()->getParam('tracking_id');
        $transactionId = '';

        $resArray = Mage::getModel('paypaladaptive/apicall')->executePayment($payKey, $trackingId, $transactionId);

        $ack = strtoupper($resArray["responseEnvelope.ack"]);
        $ackStatus = strtoupper($resArray["paymentExecStatus"]);
        if ($ack == 'SUCCESS' && $ackStatus == 'COMPLETED') {
            /**
             * Update adaptive payment details table
             */
            Mage::getModel('paypaladaptive/save')->updateAdaptivePaymentDetails($payKey, $trackingId, $transactionId);
            /**
             * update  delay chained payment data
             */
            Mage::getModel('paypaladaptive/save')->updateDelayedChainedPaymentDetails($orderId, $payKey, $trackingId);
            Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('paypaladaptive')->__('Your payment has been sent successfully'));
        } else {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('paypaladaptive')->__('Error occurred while processing your request'));
        }

        $this->_redirect('*/*/');
    }
    
    /**
     * Setting for acl
     */
    protected function _isAllowed(){
    	return true;
    }

}

