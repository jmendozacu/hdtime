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
 * Catalog rule refresh index
 * 
 * @category   Innoexts
 * @package    Innoexts_ProductBaseCurrency
 * @author     Innoexts Team <developers@innoexts.com>
 */
class Innoexts_ProductBaseCurrency_Model_Catalogrule_Action_Index_Refresh 
    extends Mage_CatalogRule_Model_Action_Index_Refresh 
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
     * Get table
     * 
     * @param string $entityName
     * 
     * @return string
     */
    protected function getTable($entityName)
    {
        return $this->_resource->getTable($entityName);
    }
    /**
     * Prepare temporary data
     * 
     * @param Mage_Core_Model_Website $website
     * 
     * @return Varien_Db_Select
     */
    protected function _prepareTemporarySelect(Mage_Core_Model_Website $website)
    {
        $helper                 = $this->getProductBaseCurrencyHelper();
        $coreHelper             = $helper->getCoreHelper();
        $productHelper          = $helper->getProductHelper();
        $productPriceHelper     = $helper->getProductPriceHelper();
        $currencyHelper         = $coreHelper->getCurrencyHelper();
        $productFlatHelper      = $this->_factory->getHelper('catalog/product_flat');
        $baseCurrencyAttribute  = $productHelper->getBaseCurrencyAttribute();
        $priceAttribute         = $productPriceHelper->getPriceAttribute();
        $adapter                = $this->_connection;
        $websiteId              = $website->getId();
        $storeId                = $website->getDefaultStore()->getId();
        $select                 = $adapter->select()
            ->from(
                array('rp' => $this->getTable('catalogrule/rule_product')), 
                array()
            )
            ->joinInner(
                array('r' => $this->getTable('catalogrule/rule')), 
                'r.rule_id = rp.rule_id', 
                array()
            )
            ->where('rp.website_id = ?', $websiteId)
            ->order(array(
                'rp.product_id', 
                'rp.customer_group_id', 
                'rp.sort_order', 
                'rp.rule_product_id', 
            ));
        
        if ($productFlatHelper->isEnabled() && $productFlatHelper->isBuilt($storeId)) {
            $select->joinInner(
                array('p' => $this->getTable('catalog/product_flat').'_'.$storeId), 
                'p.entity_id = rp.product_id', 
                array()
            );
            $baseCurrencyExpr = new Zend_Db_Expr('p.base_currency');
        } else {
            $select->joinInner(
                array('bcd' => $this->getTable(array('catalog/product', $baseCurrencyAttribute->getBackendType()))), 
                implode(' AND ', array(
                    'bcd.entity_id = rp.product_id', 
                    'bcd.store_id = 0', 
                    'bcd.attribute_id = '.$baseCurrencyAttribute->getId(), 
                )), 
                array()
            );
            if (!$productPriceHelper->isGlobalScope()) {
                $select->joinLeft(
                    array('bc' => $this->getTable(array('catalog/product', $baseCurrencyAttribute->getBackendType()))), 
                    implode(' AND ', array(
                        'bc.entity_id = rp.product_id', 
                        'bc.store_id = '.$storeId, 
                        'bc.attribute_id = '.$baseCurrencyAttribute->getId(), 
                    )), 
                    array()
                );
            }
            $baseCurrencyExpr = new Zend_Db_Expr('bcd.value');
            if (!$productPriceHelper->isGlobalScope()) {
                $baseCurrencyExpr = $adapter->getIfNullSql('bc.value', $baseCurrencyExpr);
            }
        }
        
        $select->joinLeft(
            array('cr' => $this->getTable('directory/currency_rate')), 
            implode(' AND ', array(
                "(cr.currency_from = {$currencyHelper->getBaseDbExpr('rp.website_id')})", 
                "(cr.currency_to = {$baseCurrencyExpr})", 
            )), 
            array()
        );
        
        $select->joinLeft(
            array('pgd' => $this->getTable('catalog/product_attribute_group_price')), 
            implode(' AND ', array(
                'pgd.entity_id = rp.product_id', 
                'pgd.customer_group_id = rp.customer_group_id', 
                'pgd.website_id = 0', 
            )), 
            array()
        );
        if (!$productPriceHelper->isGlobalScope()) {
            $select->joinLeft(
                array('pg' => $this->getTable('catalog/product_attribute_group_price')), 
                implode(' AND ', array(
                    'pg.entity_id = rp.product_id', 
                    'pg.customer_group_id = rp.customer_group_id', 
                    'pg.website_id = rp.website_id', 
                )), 
                array()
            );
        }
        
        $customerGroupPriceExpr = new Zend_Db_Expr('pgd.value');
        if (!$productPriceHelper->isGlobalScope()) {
            $customerGroupPriceExpr = $adapter->getIfNullSql('pg.value', $customerGroupPriceExpr);
        }
        
        $rateExpr               = new Zend_Db_Expr("cr.rate");
        
        $customerGroupPriceExpr = new Zend_Db_Expr("IF (".
            "{$rateExpr} IS NOT NULL, ".
            "ROUND({$customerGroupPriceExpr} / {$rateExpr}, 4), ".
            "{$customerGroupPriceExpr}".
        ")");
        
        if ($productFlatHelper->isEnabled() && $storeId && $productFlatHelper->isBuilt($storeId)) {
            $priceExpr = new Zend_Db_Expr('p.price');
        } else {
            $select->joinInner(
                array('pd' => $this->getTable(array('catalog/product', $priceAttribute->getBackendType()))), 
                implode(' AND ', array(
                    'pd.entity_id = rp.product_id', 
                    'pd.store_id = 0', 
                    'pd.attribute_id = '.$priceAttribute->getId(), 
                )), 
                array()
            );
            if (!$productPriceHelper->isGlobalScope()) {
                $select->joinLeft(
                    array('p' => $this->getTable(array('catalog/product', $priceAttribute->getBackendType()))), 
                    implode(' AND ', array(
                        'p.entity_id = rp.product_id', 
                        'p.store_id = '.$storeId, 
                        'p.attribute_id = '.$priceAttribute->getId(), 
                    )), 
                    array()
                );
            }
            $priceExpr = new Zend_Db_Expr('pd.value');
            if (!$productPriceHelper->isGlobalScope()) {
                $priceExpr = $adapter->getIfNullSql('p.value', $priceExpr);
            }
        }
        
        $priceExpr = new Zend_Db_Expr("IF (".
            "{$rateExpr} IS NOT NULL, ".
            "ROUND({$priceExpr} / {$rateExpr}, 4), ".
            "{$priceExpr}".
        ")");
        
        $priceExpr = $adapter->getIfNullSql($customerGroupPriceExpr, $priceExpr);
        
        $select->columns(
            array(
                'grouped_id'        => $adapter->getConcatSql(
                    array(
                        'rp.product_id', 
                        'rp.customer_group_id', 
                    ), '-'
                ), 
                'product_id'        => 'rp.product_id', 
                'customer_group_id' => 'rp.customer_group_id', 
                'from_date'         => 'r.from_date', 
                'to_date'           => 'r.to_date', 
                'action_amount'     => 'rp.action_amount', 
                'action_operator'   => 'rp.action_operator', 
                'action_stop'       => 'rp.action_stop', 
                'sort_order'        => 'rp.sort_order', 
                'price'             => $priceExpr, 
                'rule_product_id'   => 'rp.rule_product_id', 
                'from_time'         => 'rp.from_time', 
                'to_time'           => 'rp.to_time', 
            )
        );
        return $select;
    }
    /**
     * Prepare price column
     * 
     * @return Zend_Db_Expr
     */
    protected function _calculatePrice()
    {
        $adapter            = $this->_connection;
        $toPercent          = $adapter->quote('to_percent');
        $byPercent          = $adapter->quote('by_percent');
        $toFixed            = $adapter->quote('to_fixed');
        $byFixed            = $adapter->quote('by_fixed');
        $nA                 = $adapter->quote('N/A');
        $groupIdExpr        = $adapter->getIfNullSql(new Zend_Db_Expr('@group_id'), $nA);
        $actionStopExpr     = $adapter->getIfNullSql(new Zend_Db_Expr('@action_stop'), new Zend_Db_Expr(0));
        
        $rateExpr           = new Zend_Db_Expr("cr.rate");
        $actionAmountExpr   = new Zend_Db_Expr("cppt.action_amount");
        $actionAmountExpr   = new Zend_Db_Expr("IF (".
            "{$rateExpr} IS NOT NULL, ".
            "ROUND({$actionAmountExpr} / {$rateExpr}, 4), ".
            "{$actionAmountExpr}".
        ")");
        
        return $adapter->getCaseSql('', 
            array(
                $groupIdExpr.' != cppt.grouped_id' => '@price := '.$adapter->getCaseSql(
                    $adapter->quoteIdentifier('cppt.action_operator'), 
                    array(
                        $toPercent => new Zend_Db_Expr('cppt.price * cppt.action_amount / 100'), 
                        $byPercent => new Zend_Db_Expr('cppt.price * (1 - cppt.action_amount / 100)'), 
                        $toFixed   => $adapter->getCheckSql(
                            new Zend_Db_Expr("{$actionAmountExpr} < cppt.price"), 
                            new Zend_Db_Expr("{$actionAmountExpr}"), 
                            new Zend_Db_Expr("cppt.price")
                        ),
                        $byFixed   => $adapter->getCheckSql(
                            new Zend_Db_Expr("0 > cppt.price - {$actionAmountExpr}"), 
                            new Zend_Db_Expr("0"), 
                            new Zend_Db_Expr("cppt.price - {$actionAmountExpr}")
                        ),
                    )
                ),
                $groupIdExpr.' = cppt.grouped_id AND '.$actionStopExpr.' = 0' => '@price := '.$adapter->getCaseSql(
                    $adapter->quoteIdentifier('cppt.action_operator'), 
                    array(
                        $toPercent => new Zend_Db_Expr('@price * cppt.action_amount / 100'), 
                        $byPercent => new Zend_Db_Expr('@price * (1 - cppt.action_amount / 100)'), 
                        $toFixed   => $adapter->getCheckSql(
                            new Zend_Db_Expr("{$actionAmountExpr} < @price"), 
                            new Zend_Db_Expr("{$actionAmountExpr}"), 
                            new Zend_Db_Expr("@price")
                        ), 
                        $byFixed   => $adapter->getCheckSql(
                            new Zend_Db_Expr("0 > @price - {$actionAmountExpr}"), 
                            new Zend_Db_Expr("0"), 
                            new Zend_Db_Expr("@price - {$actionAmountExpr}")
                        ), 
                    )
                )
            ),
            '@price := @price'
        );
    }
    /**
     * Prepare index select
     *
     * @param Mage_Core_Model_Website $website
     * @param $time
     * 
     * @return Varien_Db_Select
     */
    protected function _prepareIndexSelect(Mage_Core_Model_Website $website, $time)
    {
        $websiteId              = $website->getId();
        $adapter                = $this->_connection;
        $nA                     = $adapter->quote('N/A');
        $adapter->query('SET @price := NULL');
        $adapter->query('SET @group_id := NULL');
        $adapter->query('SET @action_stop := NULL');
        $groupIdExpr            = $adapter->getIfNullSql(new Zend_Db_Expr('@group_id'), $nA);
        $actionStopExpr         = $adapter->getIfNullSql(new Zend_Db_Expr('@action_stop'), new Zend_Db_Expr(0));
        $indexSelect            = $adapter->select()
            ->from(array('cppt' => $this->_getTemporaryTable()), array());
        
        $helper                 = $this->getProductBaseCurrencyHelper();
        $coreHelper             = $helper->getCoreHelper();
        $productHelper          = $helper->getProductHelper();
        $productPriceHelper     = $helper->getProductPriceHelper();
        $currencyHelper         = $coreHelper->getCurrencyHelper();
        $baseCurrencyAttribute  = $productHelper->getBaseCurrencyAttribute();
        
        $storeId                = $website->getDefaultStore()->getId();
        $productFlatHelper      = $this->_factory->getHelper('catalog/product_flat');
        
        if ($productFlatHelper->isEnabled() && $productFlatHelper->isBuilt($storeId)) {
            $indexSelect->joinInner(
                array('p' => $this->getTable('catalog/product_flat').'_'.$storeId), 
                'p.entity_id = cppt.product_id', 
                array()
            );
            $baseCurrencyExpr = new Zend_Db_Expr('p.base_currency');
        } else {
            $indexSelect->joinInner(
                array('bcd' => $this->getTable(array('catalog/product', $baseCurrencyAttribute->getBackendType()))), 
                implode(' AND ', array(
                    'bcd.entity_id = cppt.product_id', 
                    'bcd.store_id = 0', 
                    'bcd.attribute_id = '.$baseCurrencyAttribute->getId(), 
                )), 
                array()
            );
            if (!$productPriceHelper->isGlobalScope()) {
                $indexSelect->joinLeft(
                    array('bc' => $this->getTable(array('catalog/product', $baseCurrencyAttribute->getBackendType()))), 
                    implode(' AND ', array(
                        'bc.entity_id = cppt.product_id', 
                        'bc.store_id = '.$storeId, 
                        'bc.attribute_id = '.$baseCurrencyAttribute->getId(), 
                    )), 
                    array()
                );
            }
            $baseCurrencyExpr = new Zend_Db_Expr('bcd.value');
            if (!$productPriceHelper->isGlobalScope()) {
                $baseCurrencyExpr = $adapter->getIfNullSql('bc.value', $baseCurrencyExpr);
            }
        }
        
        $indexSelect->joinLeft(
            array('cr' => $this->getTable('directory/currency_rate')), 
            implode(' AND ', array(
                "(cr.currency_from = {$currencyHelper->getBaseDbExpr($websiteId)})", 
                "(cr.currency_to = {$baseCurrencyExpr})", 
            )), 
            array()
        );
        
        $indexSelect->order(array(
                'cppt.grouped_id', 
                'cppt.sort_order', 
                'cppt.rule_product_id'
            ))
            ->columns(array(
                'customer_group_id'     => 'cppt.customer_group_id', 
                'product_id'            => 'cppt.product_id', 
                'rule_price'            => $this->_calculatePrice(), 
                'latest_start_date'     => 'cppt.from_date', 
                'earliest_end_date'     => 'cppt.to_date', 
                new Zend_Db_Expr(
                    $adapter->getCaseSql('', 
                        array(
                            $groupIdExpr.' != cppt.grouped_id' => new Zend_Db_Expr('@action_stop := cppt.action_stop'), 
                            $groupIdExpr.' = cppt.grouped_id' => '@action_stop := '.$actionStopExpr.' + cppt.action_stop', 
                        )
                    )
                ), 
                new Zend_Db_Expr('@group_id := cppt.grouped_id'), 
                'from_time'         => 'cppt.from_time', 
                'to_time'           => 'cppt.to_time', 
            ));
        $select = $adapter->select()
            ->from($indexSelect, array())
            ->joinInner(
                array(
                    'dates' => $adapter->select()->union(
                        array(
                            new Zend_Db_Expr(
                                'SELECT '.$adapter->getDateAddSql(
                                    $adapter->fromUnixtime($time), -1, Varien_Db_Adapter_Interface::INTERVAL_DAY
                                ).' AS rule_date'
                            ), 
                            new Zend_Db_Expr('SELECT '.$adapter->fromUnixtime($time).' AS rule_date'), 
                            new Zend_Db_Expr(
                                'SELECT '.$adapter->getDateAddSql(
                                    $adapter->fromUnixtime($time), 1, Varien_Db_Adapter_Interface::INTERVAL_DAY
                                ).' AS rule_date'
                            ), 
                        )
                    )
                ), '1=1', array()
            )
            ->columns(array(
                'rule_product_price_id' => new Zend_Db_Expr('NULL'), 
                'rule_date'             => 'dates.rule_date', 
                'customer_group_id'     => 'customer_group_id', 
                'product_id'            => 'product_id', 
                'rule_price'            => 'MIN(rule_price)', 
                'website_id'            => new Zend_Db_Expr($websiteId), 
                'latest_start_date'     => 'latest_start_date', 
                'earliest_end_date'     => 'earliest_end_date', 
            ))
            ->where(new Zend_Db_Expr($adapter->getUnixTimestamp('dates.rule_date')." >= from_time"))
            ->where($adapter->getCheckSql(
                new Zend_Db_Expr('to_time = 0'), 
                new Zend_Db_Expr(1), 
                new Zend_Db_Expr($adapter->getUnixTimestamp('dates.rule_date')." <= to_time")
            ))
            ->group(array(
                'customer_group_id', 
                'product_id', 
                'dates.rule_date'
            ));
        return $select;
    }
}