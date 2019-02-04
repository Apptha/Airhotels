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
 * @abstract      : Admin Block File
 * @Creation Date : January 11,2014
 * @Modified By   : Ramkumar M
 * @Modified Date : January 11,2014
 * */
/*
 * ********************************************************* */

class Apptha_Paypaladaptive_Block_Adminhtml_Paymentdetails_Grid extends Mage_Adminhtml_Block_Widget_Grid {

	
    // Initilize payment grid
    public function __construct() {
        parent::__construct();
        $this->setId('paymentdetailsGrid');
        $this->setDefaultSort('seller_invoice_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
    }

    // Preparing payment grid collection
    protected function _prepareCollection() {
        $collections = Mage::getModel('paypaladaptive/paypaladaptivedetails')->getCollection();
        $this->setCollection($collections);
        return parent::_prepareCollection();
    }

    // Preparing payment grid columns
    protected function _prepareColumns() {
        $this->addColumn('seller_invoice_id', array(
            'header' => Mage::helper('paypaladaptive')->__('Order Id'),
            'width' => '20px',
            'index' => 'seller_invoice_id',
            'type' => 'number',
        ));

        $this->addColumn('seller_id', array(
            'header' => Mage::helper('paypaladaptive')->__('Paypal Id'),
            'width' => '30px',
            'index' => 'seller_id',
        ));


        $this->addColumn('created_at', array(
            'header' => Mage::helper('paypaladaptive')->__('Created At'),
            'width' => '40px',
            'index' => 'created_at',
        ));

        $this->addColumn('seller_amount', array(
            'header' => Mage::helper('paypaladaptive')->__('Earned/Commission Amount'),
            'width' => '20px',
            'index' => 'seller_amount',
        ));

//         $this->addColumn('commission_amount', array(
//             'header' => Mage::helper('paypaladaptive')->__('Commission Amount'),
//             'width' => '20px',
//             'index' => 'commission_amount',
//         ));


        $this->addColumn('group_type', array(
            'header' => Mage::helper('paypaladaptive')->__('User Type'),
            'width' => '20px',
            'index' => 'group_type',
        ));

        $this->addColumn('currency_code', array(
            'header' => Mage::helper('paypaladaptive')->__('Currency'),
            'width' => '20px',
            'index' => 'currency_code',
        ));


        $this->addColumn('buyer_paypal_mail', array(
            'header' => Mage::helper('paypaladaptive')->__('Buyer Paypal Id'),
            'width' => '30px',
            'index' => 'buyer_paypal_mail',
        ));

        $this->addColumn('seller_transaction_id', array(
            'header' => Mage::helper('paypaladaptive')->__('Transaction Id'),
            'width' => '30px',
            'index' => 'seller_transaction_id',
        ));

        $this->addColumn('transaction_status', array(
            'header' => Mage::helper('paypaladaptive')->__('Transaction Status'),
            'width' => '20px',
            'index' => 'transaction_status',
        ));

        $this->addColumn('order_status', array(
            'header' => Mage::helper('paypaladaptive')->__('Order Status'),
            'width' => '20px',
            'filter' => false,
            'index' => 'order_id',
            'renderer' => 'Apptha_Paypaladaptive_Block_Adminhtml_Renderersource_Orderstatus'
        ));


        $this->addColumn('view', array(
            'header' => Mage::helper('paypaladaptive')->__('Action'),
            'width' => '30',
            'type' => 'action',
            'getter' => 'getOrderId',
            'actions' => array(
                array(
                    'caption' => Mage::helper('paypaladaptive')->__('View'),
                    'url' => array('base' => 'adminhtml/sales_order/view/'),
                    'field' => 'order_id'
                )
            ),
            'filter' => false,
            'sortable' => false,
            'index' => 'stores',
            'is_system' => true,
        ));


        return parent::_prepareColumns();
    }

    public function getRowUrl($row) {
        return FALSE;
    }

}

