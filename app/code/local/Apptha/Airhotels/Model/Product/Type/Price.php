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
class Apptha_Airhotels_Model_Product_Type_Price {
    /**
     * Define constant variable.
     */
    const CACHE_TAG = 'PRODUCT_PRICE';
    static $attributeCache = array ();
    
    /**
     * Default action to get price of product
     *
     * @return decimal
     */
    public function getPrice($product) {
        return $product->getData ( 'price' );
    }
    /**
     * Get product final price
     *
     * @param double $qty            
     * @param Mage_Catalog_Model_Product $product            
     * @return double
     */
    public function getFinalPrice($qty, $product) {
        /**
         * Calculating the Final Price
         */
        if (is_null ( $qty ) && ! is_null ( $product->getCalculatedFinalPrice () )) {
            return $product->getCalculatedFinalPrice ();
        }
        /**
         * Values are set to finalPrice
         */
        $finalPrice = $product->getPrice ();
        $finalPrice = $this->_applyTierPrice ( $product, $qty, $finalPrice );
        $finalPrice = $this->_applySpecialPrice ( $product, $finalPrice );
        $product->setFinalPrice ( $finalPrice );
        Mage::dispatchEvent ( 'catalog_product_get_final_price', array (
                'product' => $product,
                'qty' => $qty 
        ) );
        $finalPrice = $product->getData ( 'final_price' );
        $finalPrice = $this->_applyOptionsPrice ( $product, $finalPrice );
        return max ( 0, $finalPrice );
    }
    /**
     * Get Child final price
     *
     * @param
     *            $childProduct
     * @param
     *            $childProductQty
     *            
     * @return
     *
     */
    public function getChildFinalPrice($childProduct, $childProductQty) {
        return $this->getFinalPrice ( $childProductQty, $childProduct );
    }
    
    /**
     * Apply tier price for product if not return price that was before
     *
     * @param Mage_Catalog_Model_Product $product            
     * @param double $qty            
     * @param double $finalPrice            
     * @return double
     */
    protected function _applyTierPrice($product, $qty, $finalPrice) {
        /**
         * Check if qty is null
         */
        if (is_null ( $qty )) {
            return $finalPrice;
        }
        $tierPrice = $product->getTierPrice ( $qty );
        /**
         * check if tierPrice is null
         */
        if (is_numeric ( $tierPrice )) {
            $finalPrice = min ( $finalPrice, $tierPrice );
        }
        /**
         * return final price.
         */
        return $finalPrice;
    }
    
    /**
     * Get product tier price by qty
     *
     * @param double $qty            
     * @param Mage_Catalog_Model_Product $product            
     * @return double
     */
    public function getTierPrice($qty, $product) {
        $allGroups = Mage_Customer_Model_Group::CUST_GROUP_ALL;
        $prices = $product->getData ( 'tier_price' );
        /**
         * check if prices is null
         */
        if (is_null ( $prices )) {
            $attribute = $product->getResource ()->getAttribute ( 'tier_price' );
            /**
             * check wether the attribute field is set
             */
            if ($attribute) {
                $attribute->getBackend ()->afterLoad ( $product );
                $prices = $product->getData ( 'tier_price' );
            }
        }
        $this->getValue ( $prices, $qty, $product, $allGroups );
        /**
         * Set the custGroup
         */
        $custGroup = $this->_getCustomerGroupId ( $product );
        /**
         * qty exist or not
         */
        if ($qty) {
            $this->getprevPrice ( $prices, $custGroup, $product, $qty, $allGroups );
        } else {
            $qtyCache = array ();
            foreach ( $prices as $i => $price ) {
                if ($price ['cust_group'] != $custGroup && $price ['cust_group'] != $allGroups) {
                    unset ( $prices [$i] );
                } else if (isset ( $qtyCache [$price ['price_qty']] )) {
                    $j = $qtyCache [$price ['price_qty']];
                    $qtyCache [$price ['price_qty']] = Mage::helper ( 'airhotels/general' )->getTierVal ( $prices, $j, $price, $i );
                } else {
                    $qtyCache [$price ['price_qty']] = $i;
                }
            }
        }
        return ($prices) ? $prices : array ();
    }
    /**
     * Get the Custom Group Id
     *
     * @param object $product            
     */
    protected function _getCustomerGroupId($product) {
        /**
         * make sure the custom group id is set
         */
        if ($product->getCustomerGroupId ()) {
            return $product->getCustomerGroupId ();
        }
        /**
         * Setting the custom groupId Value to session
         */
        return Mage::getSingleton ( 'customer/session' )->getCustomerGroupId ();
    }
    
    /**
     * Apply special price for product if not return price that was before
     *
     * @param Mage_Catalog_Model_Product $product            
     * @param double $finalPrice            
     * @return double
     */
    protected function _applySpecialPrice($product, $finalPrice) {
        return $this->calculateSpecialPrice ( $finalPrice, $product->getSpecialPrice (), $product->getSpecialFromDate (), $product->getSpecialToDate (), $product->getStore () );
    }
    
    /**
     * Count how many tier prices we have for the product
     *
     * @param Mage_Catalog_Model_Product $product            
     * @return int
     */
    public function getTierPriceCount($product) {
        /**
         * get the price Value.
         */
        $price = $product->getTierPrice ();
        /**
         * Count the Price array
         */
        return count ( $price );
    }
    
    /**
     * Get formated by currency tier price
     *
     * @param double $qty            
     * @param Mage_Catalog_Model_Product $product            
     * @return array || double
     */
    public function getFormatedTierPrice($product, $qty = null) {
        $price = $product->getTierPrice ( $qty );
        /**
         * Check if price is exist
         */
        if (is_array ( $price )) {
            foreach ( $price as $index => $value ) {
                $price [$index] ['formated_price'] = Mage::app ()->getStore ()->convertPrice ( $price [$index] ['website_price'], true );
            }
        } else {
            /**
             * set the price Value
             */
            $price = Mage::app ()->getStore ()->formatPrice ( $price );
        }
        return $price;
    }
    
    /**
     * Get formated by currency product price
     *
     * @param Mage_Catalog_Model_Product $product            
     * @return array || double
     */
    public function getFormatedPrice($product) {
        return Mage::app ()->getStore ()->formatPrice ( $product->getFinalPrice () );
    }
    
    /**
     * Apply options price
     *
     * @param Mage_Catalog_Model_Product $product            
     * @param int $qty            
     * @param double $finalPrice            
     * @return double
     */
    protected function _applyOptionsPrice($product, $finalPrice) {
        if ($optionIds = $product->getCustomOption ( 'option_ids' )) {
            $basePrice = $finalPrice;
            foreach ( explode ( ',', $optionIds->getValue () ) as $optionId ) {
                if ($option = $product->getOptionById ( $optionId )) {
                    /**
                     * get the ItemOption
                     */
                    $confItemOption = $product->getCustomOption ( 'option_' . $option->getId () );
                    $group = $option->groupFactory ( $option->getType () )->setOption ( $option )->setConfigurationItemOption ( $confItemOption );
                    /**
                     * getting the final price Value
                     */
                    $finalPrice += $group->getOptionPrice ( $confItemOption->getValue (), $basePrice );
                }
            }
        }
        /**
         * Return final price.
         */
        return $finalPrice;
    }
    
    /**
     * Calculate product price based on special price data and price rules
     *
     * @param float $basePrice            
     * @param float $specialPrice            
     * @param string $specialPriceFrom            
     * @param string $specialPriceTo            
     * @param float|null|false $rulePrice            
     * @param mixed $wId            
     * @param mixed $gId            
     * @param null|int $productId            
     * @return float
     */
    public function calculatePrice($basePrice, $specialPrice, $specialPriceFrom, $specialPriceTo, $rulePrice = false, $wId = null, $gId = null) {
        $productId = null;
        Varien_Profiler::start ( '__PRODUCT_CALCULATE_PRICE__' );
        if ($wId instanceof Mage_Core_Model_Store) {
            $sId = $wId->getId ();
            $wId = $wId->getWebsiteId ();
        } else {
            $sId = Mage::app ()->getWebsite ( $wId )->getDefaultGroup ()->getDefaultStoreId ();
        }
        /**
         * Define base price as final price.
         */
        $finalPrice = $basePrice;
        if ($gId instanceof Mage_Customer_Model_Group) {
            $gId = $gId->getId ();
        }
        /**
         * getting the final price
         */
        $finalPrice = $this->calculateSpecialPrice ( $finalPrice, $specialPrice, $specialPriceFrom, $specialPriceTo, $sId );
        /**
         * checking the ruleprice is faslse
         */
        if ($rulePrice === false) {
            $storeTimestamp = Mage::app ()->getLocale ()->storeTimeStamp ( $sId );
            $rulePrice = Mage::getResourceModel ( 'catalogrule/rule' )->getRulePrice ( $storeTimestamp, $wId, $gId, $productId );
        }
        /**
         * get the minimum value of final,rule
         */
        if ($rulePrice !== null && $rulePrice !== false) {
            $finalPrice = min ( $finalPrice, $rulePrice );
        }
        /**
         * calculating the maximum value of $finalPrice
         */
        $finalPrice = max ( $finalPrice, 0 );
        Varien_Profiler::stop ( '__PRODUCT_CALCULATE_PRICE__' );
        /**
         * Return final price.
         */
        return $finalPrice;
    }
    
    /**
     * Calculate and apply special price
     *
     * @param float $finalPrice            
     * @param float $specialPrice            
     * @param string $specialPriceFrom            
     * @param string $specialPriceTo            
     * @param mixed $store            
     * @return float
     */
    public function calculateSpecialPrice($finalPrice, $specialPrice, $specialPriceFrom, $specialPriceTo, $store = null) {
        /**
         * Make ensure the price value does not empty
         */
        if (! is_null ( $specialPrice ) && $specialPrice && Mage::app ()->getLocale ()->isStoreDateInInterval ( $store, $specialPriceFrom, $specialPriceTo )) {
            $finalPrice = min ( $finalPrice, $specialPrice );
        }
        return $finalPrice;
    }
    
    /**
     * Check is tier price value fixed or percent of original price
     *
     * @return bool
     */
    public function isTierPriceFixed() {
        return true;
    }
    
    /**
     * Get the previous price for the property
     *
     * @param float $prices            
     * @param string $custGroup            
     * @param object $product            
     * @param int $qty            
     * @param string $allGroups            
     * @return float
     */
    public function getprevPrice($prices, $custGroup, $product, $qty, $allGroups) {
        $prevQty = 1;
        $prevPrice = $product->getPrice ();
        $prevGroup = $allGroups;
        foreach ( $prices as $price ) {
            if ($price ['cust_group'] != $custGroup && $price ['cust_group'] != $allGroups) {
                /**
                 * Tier not for current customer group nor is for all groups
                 */
                continue;
            }
            if ($qty < $price ['price_qty']) {
                /**
                 * Tier is higher than product qty
                 */
                continue;
            }
            if ($price ['price_qty'] < $prevQty) {
                /**
                 * Higher tier qty already found
                 */
                continue;
            }
            if ($price ['price_qty'] == $prevQty && $prevGroup != $allGroups && $price ['cust_group'] == $allGroups) {
                /**
                 * Found tier qty is same as current tier qty but current tier group is ALL_GROUPS
                 */
                continue;
            }
            /**
             * check wether the price and prevprice is high
             */
            if ($price ['website_price'] < $prevPrice) {
                $prevPrice = $price ['website_price'];
                $prevQty = $price ['price_qty'];
                $prevGroup = $price ['cust_group'];
            }
        }
        /**
         * return final price.
         */
        return $prevPrice;
    }
    
    /**
     * get Value for the given product
     *
     * @param float $prices            
     * @param int $qty            
     * @param object $product            
     * @param string $allGroups            
     * @return multitype
     */
    public function getValue($prices, $qty, $product, $allGroups) {
        /**
         * check wether the prices are empty.
         */
        if (is_null ( $prices ) || ! is_array ( $prices )) {
            $this->getQuantity ( $qty, $product );
            /**
             * return an array
             */
            return array (
                    array (
                            'price' => $product->getPrice (),
                            'website_price' => $product->getPrice (),
                            'price_qty' => 1,
                            'cust_group' => $allGroups 
                    ) 
            );
        }
    }
    /**
     * Get the Quantity Value
     *
     * @param int $qty            
     */
    public function getQuantity($qty, $product) {
        /**
         * check if is_null
         */
        if (! is_null ( $qty )) {
            /**
             * returning the Price
             */
            return $product->getPrice ();
        }
    }
}