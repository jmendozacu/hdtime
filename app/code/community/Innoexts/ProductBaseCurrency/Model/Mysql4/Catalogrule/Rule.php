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
 * @license     http://innoexts.com/commercial-license-agreement InnoExts Commercial License
 */

/**
 * Catalog rule resource
 * 
 * @category   Innoexts
 * @package    Innoexts_ProductBaseCurrency
 * @author     Innoexts Team <developers@innoexts.com>
 */
class Innoexts_ProductBaseCurrency_Model_Mysql4_Catalogrule_Rule 
    extends Mage_CatalogRule_Model_Mysql4_Rule 
{
    /**
     * Get product bse currency helper
     * 
     * @return Innoexts_ProductBaseCurrency_Helper_Data
     */
    protected function getProductBaseCurrencyHelper()
    {
        return Mage::helper('productbasecurrency');
    }
    /**
     * Get version helper
     * 
     * @return Innoexts_Core_Helper_Version
     */
    public function getVersionHelper()
    {
        return $this->getProductBaseCurrencyHelper()
            ->getVersionHelper();
    }
    /**
     * Get product price helper
     * 
     * @return Innoexts_ProductBaseCurrency_Helper_Catalog_Product_Price
     */
    public function getProductPriceHelper()
    {
        return $this->getProductBaseCurrencyHelper()
            ->getProductPriceHelper();
    }
    /**
     * Get price join condition
     * 
     * @return string
     */
    protected function getPriceJoinCondition()
    {
        $helper                 = $this->getProductBaseCurrencyHelper();
        $productPriceHelper     = $helper->getProductPriceHelper();
        return implode(' AND ', array(
            '(%1$s.entity_id = rp.product_id)', 
            '(%1$s.attribute_id = '.$productPriceHelper->getPriceAttributeId().')', 
            '(%1$s.store_id = %2$s)'
        ));
    }
    /**
     * Add website price join
     * 
     * @param Zend_Db_Select $select
     * 
     * @return self
     */
    protected function addWebsitePriceJoin($select)
    {
        $helper                 = $this->getProductBaseCurrencyHelper();
        $productPriceHelper     = $helper->getProductPriceHelper();
        $tableAlias             = 'pp_website';
        $fieldAlias             = 'website_price';
        $storeId                = new Zend_Db_Expr('csg.default_store_id');
        $select->joinLeft(
            array($tableAlias => $productPriceHelper->getPriceAttributeTable()), 
            sprintf($this->getPriceJoinCondition(), $tableAlias, $storeId), 
            array()
        );
        $price                  = new Zend_Db_Expr($tableAlias.'.value');
        $rateExpr               = new Zend_Db_Expr('cr.rate');
        $price                  = new Zend_Db_Expr("IF (
            {$rateExpr} IS NOT NULL, 
            ROUND({$price} / {$rateExpr}, 4), 
            {$price}
        )");
        $select->columns(array(
            $fieldAlias => $price, 
        ));
        return $this;
    }
    /**
     * Get DB resource statement for processing query result
     *
     * @param int $fromDate
     * @param int $toDate
     * @param int|null $productId
     * @param int $websiteId
     *
     * @return Zend_Db_Statement_Interface
     */
    protected function _getRuleProductsStmt($fromDate, $toDate, $productId = null, $websiteId = null)
    {
        $helper                 = $this->getProductBaseCurrencyHelper();
        $coreHelper             = $helper->getCoreHelper();
        $productPriceHelper     = $helper->getProductPriceHelper();
        $productHelper          = $helper->getProductHelper();
        $currencyHelper         = $coreHelper->getCurrencyHelper();
        $baseCurrencyAttribute  = $productHelper->getBaseCurrencyAttribute();
        
        $adapter                = $this->_getReadAdapter();
        $order                  = array(
            'rp.website_id', 'rp.customer_group_id', 'rp.product_id', 'rp.sort_order', 'rp.rule_id', 
        );
        $select                 = $adapter->select()
            ->from(array('rp' => $this->getTable('catalogrule/rule_product')))
            ->where(
                $adapter->quoteInto('rp.from_time = 0 or rp.from_time <= ?', $toDate).' OR '.
                $adapter->quoteInto('rp.to_time = 0 or rp.to_time >= ?', $fromDate)
            )->order($order);
        if (!is_null($productId)) {
            $select->where('rp.product_id=?', $productId);
        }
        $select->joinInner(
            array('product_website' => $this->getTable('catalog/product_website')), 
            'product_website.product_id=rp.product_id ' .
            'AND rp.website_id=product_website.website_id ' .
            'AND product_website.website_id='.$websiteId, 
            array()
        );
        $select->join(
            array('cw' => $this->getTable('core/website')), 
            '(cw.website_id = rp.website_id)', 
            array()
        );
        
        if (!$productPriceHelper->isGlobalScope()) {
            $select->join(
                array('csg' => $this->getTable('core/store_group')), 
                '(csg.group_id = cw.default_group_id)', 
                array()
            );
        }
        
        $select->joinInner(
            array('bcd' => $this->getValueTable('catalog/product', $baseCurrencyAttribute->getBackendType())), 
            implode(' AND ', array(
                'bcd.entity_id = rp.product_id', 
                'bcd.store_id = 0', 
                'bcd.attribute_id = '.$baseCurrencyAttribute->getId(), 
            )), 
            array()
        );
        $baseCurrencyExpr = new Zend_Db_Expr('bcd.value');
        if (!$productPriceHelper->isGlobalScope()) {
            $select->joinLeft(
                array('bc' => $this->getValueTable('catalog/product', $baseCurrencyAttribute->getBackendType())), 
                implode(' AND ', array(
                    'bc.entity_id = rp.product_id', 
                    'bc.store_id = csg.default_store_id', 
                    'bc.attribute_id = '.$baseCurrencyAttribute->getId(), 
                )), 
                array()
            );
            $baseCurrencyExpr = $adapter->getIfNullSql('bc.value', $baseCurrencyExpr);
        }
        $select->joinLeft(
            array('cr' => $this->getTable('directory/currency_rate')), 
            implode(' AND ', array(
                "(cr.currency_from = {$currencyHelper->getBaseDbExpr('cw.website_id')})", 
                "(cr.currency_to = {$baseCurrencyExpr})", 
            )), 
            array()
        );
        $rateExpr               = new Zend_Db_Expr('cr.rate');
        
        $select->join(
            array('pp_default' => $productPriceHelper->getPriceAttributeTable()), 
            sprintf($this->getPriceJoinCondition(), 'pp_default', Mage_Core_Model_App::ADMIN_STORE_ID), 
            array()
        );
        $defaultPrice           = new Zend_Db_Expr('pp_default.value');
        $defaultPrice           = new Zend_Db_Expr("IF (
            {$rateExpr} IS NOT NULL, 
            ROUND({$defaultPrice} / {$rateExpr}, 4), 
            {$defaultPrice}
        )");
        $select->columns(array(
            'default_price'     => $defaultPrice, 
            'rate'              => $rateExpr, 
        ));
        
        if ($productPriceHelper->isWebsiteScope()) {
            $this->addWebsitePriceJoin($select);
        }
        return $adapter->query($select);
    }
    /**
     * Calculate product price based on price rule data and previous information
     *
     * @param array $ruleData
     * @param null|array $productData
     *
     * @return float
     */
    protected function _calcRuleProductPrice($ruleData, $productData = null)
    {
        $helper         = $this->getProductBaseCurrencyHelper();
        $priceHelper    = $helper->getProductPriceHelper();
        if ($productData !== null && isset($productData['rule_price'])) {
            $productPrice = $productData['rule_price'];
        } else {
            if (isset($ruleData['website_price'])) {
                $productPrice = $ruleData['website_price'];
            } else {
                $productPrice = $ruleData['default_price'];
            }
        }
        if (in_array($ruleData['action_operator'], array('to_fixed', 'by_fixed')) && $ruleData['rate']) {
            $ruleData['action_amount'] = $priceHelper->round(
                $ruleData['action_amount'] / $ruleData['rate']
            );
        }
        $productPrice = Mage::helper('catalogrule')->calcPriceRule(
            $ruleData['action_operator'], $ruleData['action_amount'], $productPrice
        );
        return $priceHelper->round($productPrice);
    }
}