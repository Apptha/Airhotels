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
 * Grid for Manage Bank details AND fields
 */
class Apptha_Airhotels_Block_Adminhtml_Managebankdetails extends Mage_Adminhtml_Block_Widget_Grid_Container {
    /**
     * Class constructor
     */
    public function __construct() {
        /**
         * Defining controller
         */
        $this->_controller = 'adminhtml_managebankdetails';
        $this->_blockGroup = 'airhotels';
        /**
         * Defining Header Text
         */
        $this->_headerText = Mage::helper ( 'airhotels' )->__ ( 'Manage Bank Details' );
        /**
         * Add button label.
         */
        $this->_addButtonLabel = Mage::helper ( 'airhotels' )->__ ( 'Add Details' );
        /**
         * Calling the parent Construct Method.
         */
        parent::__construct ();
    }
}