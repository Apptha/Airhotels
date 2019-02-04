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
class Apptha_Airhotels_Block_Adminhtml_Airhotels_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs {
    /**
     * contsruct Method
     */
    public function __construct() {
        /**
         * Calling the parent Construct Method.
         */
        parent::__construct ();
        $this->setId ( 'airhotels_tabs' );
        $this->setDestElementId ( 'edit_form' );
        /**
         * Set page title.
         */
        $this->setTitle ( Mage::helper ( 'airhotels' )->__ ( 'Property Information' ) );
    }
    /**
     * before to html
     *
     * @see Mage_Adminhtml_Block_Widget_Tabs::_beforeToHtml()
     */
    protected function _beforeToHtml() {
        /**
         * Ading tab for formSelection
         */
        $registryID = Mage::registry ( 'airhotels_data' )->getId ();
        $status = true;
        if (! empty ( $registryID )) {
            $status = false;
        }
        $this->addTab ( 'form_section', array (
                'label' => Mage::helper ( 'airhotels' )->__ ( 'Payment to Hoster' ),
                'title' => Mage::helper ( 'airhotels' )->__ ( 'Payment to Hoster' ),
                'content' => $this->getLayout ()->createBlock ( 'airhotels/adminhtml_airhotels_edit_tab_form' )->toHtml (),
                'active' => $status 
        ) );
        /**
         * Adding tab for FormDatils
         */
        $this->addTab ( 'form_details', array (
                'label' => Mage::helper ( 'airhotels' )->__ ( 'Property Details' ),
                'title' => Mage::helper ( 'airhotels' )->__ ( 'Property Details' ),
                'content' => $this->getLayout ()->createBlock ( 'airhotels/adminhtml_airhotels_edit_tab_Details' )->toHtml () 
        ) );
        /**
         * Returning the parent beforeToHtml Method
         */
        return parent::_beforeToHtml ();
    }
}