<?php

/**
 * In this class contains delayed chained grid function.
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
class Apptha_Paypaladaptive_Block_Adminhtml_Preapprovaldetails_Grid extends Mage_Adminhtml_Block_Widget_Grid {
    /*
     * Class constructor
     */

    public function __construct() {
        parent::__construct();
        $this->setId('preapprovaldetailsGrid');
        $this->setDefaultSort('preapprovaldetails_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
    }

    /*
     * Prepare payment grid collection 
     * 
     * @return object collection
     */

    protected function _prepareCollection() {
        $collections = Mage::getModel('paypaladaptive/preapprovaldetails')->getCollection();
        $this->setCollection($collections);
        return parent::_prepareCollection();
    }

    /*
     * Preparing payment grid columns 
     * 
     * @return object collection
     */

    protected function _prepareColumns() {

        $this->addColumn('preapprovaldetails_id', array(
            'header' => Mage::helper('paypaladaptive')->__('Id'),
            'width' => '5px',
            'index' => 'preapprovaldetails_id',
            'type' => 'number',
        ));

        $this->addColumn('preapproval', array(
            'header' => Mage::helper('paypaladaptive')->__('Preapproval Key'),
            'width' => '20px',
            'index' => 'preapproval',
        ));

        $this->addColumn('starting_date', array(
        		'header' => Mage::helper('paypaladaptive')->__('Start Date'),
        		'width' => '20px',
        		'index' => 'starting_date',
        ));
        $this->addColumn('ending_date', array(
        		'header' => Mage::helper('paypaladaptive')->__('End Date'),
        		'width' => '20px',
        		'index' => 'ending_date',
        ));
        
        $this->addColumn('cur_payments', array(
            'header' => Mage::helper('paypaladaptive')->__('Cur.Payments'),
            'width' => '2px',
            'index' => 'cur_payments',
        ));

        $this->addColumn('cur_payments_amount', array(
            'header' => Mage::helper('paypaladaptive')->__('Cur.pay Amt'),
            'width' => '2px',
            'index' => 'cur_payments_amount',
        ));

        $this->addColumn('cur_period_attempts', array(
            'header' => Mage::helper('paypaladaptive')->__('Period Attempts'),
            'width' => '2px',
            'index' => 'cur_period_attempts',
        ));

        $this->addColumn('cur_period_ending_date', array(
            'header' => Mage::helper('paypaladaptive')->__('Period EndDate'),
            'width' => '2px',
            'index' => 'cur_period_ending_date',
        ));

        $this->addColumn('max_number_of_payments', array(
            'header' => Mage::helper('paypaladaptive')->__('Max No Pay'),
            'width' => '3px',
            'index' => 'max_number_of_payments',
        ));

        $this->addColumn('max_total_amount_of_all_payments', array(
            'header' => Mage::helper('paypaladaptive')->__('Max TotalAmt'),
            'width' => '3px',
            'index' => 'max_total_amount_of_all_payments',
        ));

        $this->addColumn('status', array(
            'header' => Mage::helper('paypaladaptive')->__('Order Status'),
            'width' => '3px',
            'index' => 'status',
        ));

        $this->addColumn('payment_period', array(
            'header' => Mage::helper('paypaladaptive')->__('Pay Period'),
            'width' => '3px',
            'index' => 'payment_period',
        ));

        $this->addColumn('real_order_id', array(
        		'header' => Mage::helper('paypaladaptive')->__('Oreder Id'),
        		'width' => '3px',
        		'index' => 'real_order_id',
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

    /*
     * Get row url
     * 
     * @param object $row collection
     * @return bool 
     */

    public function getRowUrl($row) {
        return FALSE;
    }

}

