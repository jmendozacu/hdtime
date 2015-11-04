<?php
/**
 * Innoexts
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the InnoExts Commercial License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://innoexts.com/commercial-license-agreement
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@innoexts.com so we can send you a copy immediately.
 * 
 * @category    Innoexts
 * @package     Innoexts_ProductBaseCurrency
 * @copyright   Copyright (c) 2014 Innoexts (http://www.innoexts.com)
 * @license     http://innoexts.com/commercial-license-agreement  InnoExts Commercial License
 */

/**
 * Product price helper
 * 
 * @category   Innoexts
 * @package    Innoexts_ProductBaseCurrency
 * @author     Innoexts Team <developers@innoexts.com>
 */
class Innoexts_ProductBaseCurrency_Helper_Catalog_Product_Price 
    extends Innoexts_Core_Helper_Catalog_Product_Price 
{
    /**
     * Get product base currency helper
     * 
     * @return Innoexts_ProductBaseCurrency_Helper_Data
     */
    protected function getProductBaseCurrencyHelper()
    {
        return Mage::helper('productbasecurrency');
    }
    /**
     * Get product helper
     * 
     * @return Innoexts_ProductBaseCurrency_Helper_Catalog_Product
     */
    public function getProductHelper()
    {
        return Mage::helper('productbasecurrency/catalog_product');
    }
    /**
     * Get indexer helper
     * 
     * @return Innoexts_ProductBaseCurrency_Helper_Catalog_Product_Price_Indexer
     */
    public function getIndexerHelper()
    {
        return Mage::helper('productbasecurrency/catalog_product_price_indexer');
    }
    /**
     * Get current store base currency price
     * 
     * @param Mage_Catalog_Model_Product $product
     * @param float $price
     * 
     * @return float
     */
    public function getCurrentStoreBaseCurrencyPrice($product, $price)
    {
        $productHelper      = $this->getProductHelper();
        if (
            !$price || 
            !$productHelper->isBaseCurrencyCodeSet($product)
        ) {
            return $price;
        }
        $currencyHelper     = $this->getCoreHelper()->getCurrencyHelper();
        $rate = $currencyHelper->getRate(
            $currencyHelper->getCurrentStoreBase()->getCode(), 
            $productHelper->getBaseCurrencyCode($product)
        );
        if ($rate) {
            return $price / $rate;
        } else {
            return $price;
        }
    }
    /**
     * Set price
     * 
     * @param Mage_Catalog_Model_Product $product
     * 
     * @return self
     */
    public function setPrice($product)
    {
        parent::setPrice($product);
        $productHelper      = $this->getProductHelper();
        if (
            $productHelper->isEditMode($product) || 
            !$productHelper->isBaseCurrencyCodeSet($product)
        ) {
            return $this;
        }
        $price              = $this->getCurrentStoreBaseCurrencyPrice($product, $product->getPrice());
        if (!is_null($price)) {
            $product->setFinalPrice(null);
            $product->setPrice($price);
        }
        return $this;
    }
    /**
     * Set special price
     * 
     * @param Mage_Catalog_Model_Product $product
     * 
     * @return self
     */
    public function setSpecialPrice($product)
    {
        parent::setSpecialPrice($product);
        $productHelper      = $this->getProductHelper();
        if (
            $productHelper->isEditMode($product) || 
            !$productHelper->isBaseCurrencyCodeSet($product)
        ) {
            return $this;
        }
        $price              = $this->getCurrentStoreBaseCurrencyPrice($product, $product->getSpecialPrice());
        if (!is_null($price)) {
            $product->setFinalPrice(null);
            $product->setSpecialPrice($price);
        }
        return $this;
    }
    /**
     * Check if index is enabled for collection
     * 
     * @param Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection $collection
     * 
     * @return bool
     */
    protected function isIndexCollection($collection)
    {
        $select             = $collection->getSelect();
        $fromPart           = $select->getPart(Zend_Db_Select::FROM);
        return (isset($fromPart['price_index'])) ? true : false;
    }
    /**
     * Set collection price
     * 
     * @param Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection $collection
     *
     * @return self
     */
    public function setCollectionPrice($collection)
    {
        if (!$collection || $collection->getFlag('price_set')) {
            return $this;
        }
        $productHelper      = $this->getProductHelper();
        foreach ($collection as $product) {
            if (
                (!$productHelper->isBaseCurrencyCodeSet($product)) || 
                ($collection->isEnabledFlat() && $this->isIndexCollection($collection))
            ) {
                continue;
            }
            $price              = $this->getCurrentStoreBaseCurrencyPrice($product, $product->getPrice());
            if (!is_null($price)) {
                $product->setFinalPrice(null);
                $product->setPrice($price);
            }
        }
        $collection->setFlag('price_set', true);
        return $this;
    }
    /**
     * Set collection special price
     * 
     * @param Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection $collection
     * 
     * @return self
     */
    public function setCollectionSpecialPrice($collection)
    {
        if (!$collection || $collection->getFlag('special_price_set')) {
            return $this;
        }
        $productHelper      = $this->getProductHelper();
        foreach ($collection as $product) {
            if (
                (!$productHelper->isBaseCurrencyCodeSet($product)) || 
                ($collection->isEnabledFlat() && $this->isIndexCollection($collection))
            ) {
                continue;
            }
            $price              = $this->getCurrentStoreBaseCurrencyPrice($product, $product->getSpecialPrice());
            if (!is_null($price)) {
                $product->setFinalPrice(null);
                $product->setSpecialPrice($price);
            }
        }
        $collection->setFlag('special_price_set', true);
        return $this;
    }
    /**
     * Load collection tier price
     * 
     * @param Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection $collection
     *
     * @return self
     */
    public function loadCollectionTierPrice($collection)
    {
        if ($collection->getFlag('tier_price_added')) {
            return $this;
        }
        $prices         = array();
        $productIds     = array();
        foreach ($collection as $product) {
            array_push($productIds, $product->getId());
            $prices[$product->getId()] = array();
        }
        if (!count($productIds)) {
            return $this;
        }
        $coreHelper     = $this->getCoreHelper();
        $productHelper  = $this->getProductHelper();
        $websiteId      = null;
        if (!$this->isGlobalScope()) {
            $websiteId      = $coreHelper->getWebsiteIdByStoreId($productHelper->getStoreId($product));
        }
        $adapter        = $collection->getConnection();
        $select         = $adapter->select()
            ->from(
                $coreHelper->getTable('catalog/product_attribute_tier_price'), 
                array(
                    'price_id'      => 'value_id', 
                    'website_id'    => 'website_id', 
                    'all_groups'    => 'all_groups', 
                    'cust_group'    => 'customer_group_id', 
                    'price_qty'     => 'qty', 
                    'price'         => 'value', 
                    'product_id'    => 'entity_id', 
                )
            )->where('entity_id IN(?)', $productIds)
            ->order(array('entity_id','qty'));
        if ($websiteId == '0') {
            $select->where('website_id = ?', $websiteId);
        } else {
            $select->where('website_id IN(?)', array('0', $websiteId));
        }
        $customerGroupAllId = Mage_Customer_Model_Group::CUST_GROUP_ALL;
        foreach ($adapter->fetchAll($select) as $item) {
            $prices[$item['product_id']][] = array(
                'website_id'     => $item['website_id'], 
                'cust_group'     => $item['all_groups'] ? $customerGroupAllId : $item['cust_group'], 
                'price_qty'      => $item['price_qty'], 
                'price'          => $item['price'], 
                'website_price'  => $item['price'], 
            );
        }
        foreach ($collection as $product) {
            $product->setTierPrices($prices[$product->getId()]);
            $this->setTierPrice($product);
        }
        $collection->setFlag('tier_price_added', true);
        return $this;
    }
    /**
     * Set group price
     * 
     * @param Mage_Catalog_Model_Product $product
     * @param Mage_Catalog_Model_Resource_Eav_Attribute $attribute
     * @param array $prices
     * 
     * @return self
     */
    public function _setGroupPrice($product, $attribute, $prices)
    {
        if (!$attribute) {
            return $this;
        }
        $backend        = $attribute->getBackend();
        if (!$backend) {
            return $this;
        }
        $coreHelper     = $this->getCoreHelper();
        $productHelper  = $this->getProductHelper();
        $websiteId      = null;
        if (!$this->isGlobalScope()) {
            $websiteId      = $coreHelper->getWebsiteIdByStoreId($productHelper->getStoreId($product));
        }
        if (!empty($prices) && !$productHelper->isEditMode($product)) {
            $prices = $backend->preparePriceData2(
                $prices, 
                $product->getTypeId(), 
                $websiteId, 
                $productHelper->getBaseCurrencyCode($product)
            );
        }
        $product->setFinalPrice(null);
        $product->setData($attribute->getAttributeCode(), $prices);
        return $this;
    }
}