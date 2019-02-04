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
class Apptha_Airhotels_Block_Property_Propertyhistory extends Mage_Catalog_Block_Product_Abstract {    
    /**
     * Function to get pagination for property history
     *
     * @return string
     */
    public function getPagersHtml(){
        return $this->getChildHtml ( 'propertyhistorypager' );
    }
    /**
     * Host property property history
     *
     * @return array $result;
     */
    public function getPropertyHistory() {
        $fromData = '';
        /**
         * Get From,To Value
         */
        $fromDate = $this->getRequest ()->getParam ( 'from' );
        $toDate = $this->getRequest ()->getParam ( 'to' );
        /**
         * Get customer Id from session
         */
        $customer = Mage::getSingleton('customer/session')->getCustomer();
        $cusId = $customer->getId();
        /**
         * Getting Property collection filter by order status
         */
        $result = Mage::getModel ( 'airhotels/airhotels' )->getCollection ();
        /**
         * Getting Property collection filter by host id
         */
        $result->addFieldToFilter ( 'host_id', array (
                'eq' => $cusId
        ) );
        /**
         * Getting Property collection filter by order status as complete and closed
         */
      
        /**
         * Adding filters to airhotels Colletion
         */
        if ($fromDate && $toDate) {
            if ($fromDate == "yyyy-mm-dd" && $toDate == "yyyy-mm-dd") {
                /**
                 * Filter by fromdate and to date
                 */
                $result->addFieldToFilter ( 'fromdate', array (
                        'gteq' => date ( "Y-m-d", strtotime ( date ( "Y" ) . "-" . date ( "m" ) . "-1" ) ) 
                ) );
                $result->addFieldToFilter ( 'todate', array (
                        'lteq' => date ( "Y-m-d", strtotime ( date ( "Y" ) . "-" . date ( "m" ) . "-" . date ( "t" ) ) ) 
                ) );
            } else {
                $result->addFieldToFilter ( 'fromdate', array (
                        'gteq' => $fromDate 
                ) );
                $result->addFieldToFilter ( 'todate', array (
                        'lteq' => $toDate 
                ) );
            }
        } else {
            $fromData = date ( "Y-m-d", strtotime ( date ( "Y" ) . "-" . date ( "m" ) . "-1" ) );
            $result->addFieldToFilter ( 'fromdate', array (
                    'gteq' => $fromData 
            ) );
        }
        
        $result->setOrder ( 'order_item_id', 'DESC' );
        /**
         * Add pagenation
         *
         * @var $paging
         */
        $paging = $this->getLayout ()->createBlock ( 'page/html_pager', 'propertyhistory.pager' );
        $paging->setCollection ( $result );
        $this->setChild ( 'propertyhistorypager', $paging );
        return $result;
    }    
}