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
 * Renderer to display the document status
 * 
 * @see Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract::render()
 */
class Apptha_Airhotels_Block_Adminhtml_Renderer_Hosttransactioninfo extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {
 /**
  * Function to display document status in grid
  * (non-PHPdoc)
  *
  * @see Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract::render()
  */
 public function render(Varien_Object $row) {    
     /**
      * Get data from column index
      */ 
     $value = $row->getData ( $this->getColumn ()->getIndex () );
     $collection = Mage::getModel ( 'airhotels/airhotels' )->load ($value);
     /**
      * Load order collection 
      * 
      * To transact amount to host
      */ 
     if($collection->getStatus() == 0){
         $result = "<a name=credit href='" . $this->getUrl ( '*/*/edit', array (
                 'id' => $value
         ) ) . "' title='" . Mage::helper ( 'airhotels' )->__ ( 'click to Pay' ) . "'>" . Mage::helper ( 'airhotels' )->__ ( 'Pay' ) . "</a>";
     } elseif ($collection->getStatus() == 2){
        $result = Mage::helper ( 'airhotels' )->__ ( 'Refunded' );
     } else {
        $result = Mage::helper ( 'airhotels' )->__ ( 'Paid' );
     }    
     return $result;
   }
}