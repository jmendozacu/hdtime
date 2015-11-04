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
 * Product price indexer helper
 * 
 * @category   Innoexts
 * @package    Innoexts_ProductBaseCurrency
 * @author     Innoexts Team <developers@innoexts.com>
 */
class Innoexts_ProductBaseCurrency_Helper_Catalog_Product_Price_Indexer 
    extends Innoexts_Core_Helper_Catalog_Product_Price_Indexer 
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
     * Get price helper
     * 
     * @return Innoexts_ProductBaseCurrency_Helper_Catalog_Product_Price
     */
    public function getPriceHelper()
    {
        return Mage::helper('productbasecurrency/catalog_product_price');
    }
    /**
     * Get product helper
     * 
     * @return Innoexts_ProductBaseCurrency_Helper_Catalog_Product
     */
    public function getProductHelper()
    {
        return $this->getPriceHelper()->getProductHelper();
    }
    /**
     * Get base currency expression
     * 
     * @param Zend_Db_Expr|string $websiteExpr
     * 
     * @return Zend_Db_Expr 
     */
    public function getBaseCurrencyExpr($websiteExpr)
    {
        return $this->getProductBaseCurrencyHelper()
            ->getCoreHelper()
            ->getCurrencyHelper()
            ->getBaseDbExpr($websiteExpr);
    }
    /**
     * Add price joins
     * 
     * @param Varien_Db_Adapter_Interface $adapter
     * @param Zend_Db_Select $select
     * 
     * @return self
     */
    protected function addPriceJoins($adapter, $select)
    {
        parent::addPriceJoins($adapter, $select);
        $baseCurrency = $this->addAttributeToSelect($adapter, $select, 'base_currency', 'e.entity_id', 'cs.store_id');
        $select->joinLeft(
            array('cr' => $this->getTable('directory/currency_rate')), 
            "(cr.currency_from = {$this->getBaseCurrencyExpr('cw.website_id')}) AND ".
            "(cr.currency_to = {$baseCurrency})", 
            array()
        );
        return $this;
    }
    /**
     * Prepare price
     * 
     * @param Varien_Db_Select $select
     * @param float $price
     * 
     * @return Zend_Db_Expr
     */
    protected function preparePrice($select, $price)
    {
        $rate           = new Zend_Db_Expr("cr.rate");
        return new Zend_Db_Expr(
            "IF (".
                "{$rate} IS NOT NULL, ".
                "ROUND({$price} / {$rate}, 2), ".
                "{$price}".
            ")"
        );
    }
    /**
     * Prepare special price
     * 
     * @param Varien_Db_Select $select
     * @param float $price
     * 
     * @return Zend_Db_Expr
     */
    protected function prepareSpecialPrice($select, $price)
    {
        $rate           = new Zend_Db_Expr("cr.rate");
        return new Zend_Db_Expr(
            "IF (".
                "({$price} IS NOT NULL) AND ({$rate} IS NOT NULL), ".
                "ROUND({$price} / {$rate}, 2), ".
                "{$price}".
            ")"
        );
    }
}