<?php

/**
 * In this class contains delayed chained grid form function.
 *
 * @package         Apptha PayPal Adaptive
 * @version         0.1.1
 * @since           Magento 1.5
 * @author          Apptha Team
 * @copyright       Copyright (C) 2014 Powered by Apptha
 * @license         http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @Creation Date   March 26,2014
 * @Modified By     Ramkumar M
 * @Modified Date   March 26,2014
 *
 * */
class Apptha_Paypaladaptive_Block_Adminhtml_Preapprovaldetails_Edit_Form extends Mage_Adminhtml_Block_Widget_Form {
    /*
     * Prepare form for change execute date
     * 
     * @return object
     */

    protected function _prepareForm() {

        $orderId = $this->getRequest()->getParam('order_id');

        $orders = Mage::getModel('paypaladaptive/preapprovaldetails')->getCollection()
                ->addFieldToFilter('order_id', $orderId);
        foreach ($orders as $order) {
            $executePaymentDate = $order->getExecutepaymentDate();
            break;
        }
        $this->setCollection($orders);
        $form = new Varien_Data_Form(
                array(
            'id' => 'edit_form',
            'action' => $this->getUrl('*/*/saveexecutepaymentdate', array('order_id' => $orderId)
            ),
            'method' => 'post',
                )
        );

        $fieldset = $form->addFieldset('executepayment_date', array('legend' => Mage::helper('paypaladaptive')->__('Execute Payment Date')));
        $fieldset->addField('executepaymentdate', 'date', array(
            'name' => 'executepaymentdate',
            'title' => Mage::helper('paypaladaptive')->__('Execute Payment Date'),
            'label' => Mage::helper('paypaladaptive')->__('Execute Payment Date'),
            'after_element_html' => '<small>Payments to secondary receivers can be delayed for up to 90 days from payment credited date</small>',
            'tabindex' => 1,
            'image' => $this->getSkinUrl('images/grid-cal.gif'),
            'required' => false,
            'format' => Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT),
            'value' => $executePaymentDate
        ));
        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();
    }

}

