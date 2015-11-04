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
 * Price indexer resource
 * 
 * @category   Innoexts
 * @package    Innoexts_ProductBaseCurrency
 * @author     Innoexts Team <developers@innoexts.com>
 */
class Innoexts_ProductBaseCurrency_Model_Mysql4_Catalog_Product_Indexer_Price 
    extends Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Indexer_Price 
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
    protected function getProductPriceHelper()
    {
        return $this->getProductBaseCurrencyHelper()
            ->getProductPriceHelper();
    }
    /**
     * Get price indexer helper
     * 
     * @return Innoexts_ProductBaseCurrency_Helper_Catalog_Product_Price_Indexer
     */
    protected function getProductPriceIndexerHelper()
    {
        return $this->getProductBaseCurrencyHelper()
            ->getProductPriceIndexerHelper();
    }
    /**
     * Get version helper
     * 
     * @return Innoexts_Core_Helper_Version
     */
    protected function getVersionHelper()
    {
        return $this->getProductBaseCurrencyHelper()
            ->getVersionHelper();
    }
    /**
     * Add attribute to select
     * 
     * @param Varien_Db_Select $select
     * @param string $attrCode
     * @param string|Zend_Db_Expr $entity
     * @param string|Zend_Db_Expr $store
     * @param Zend_Db_Expr $condition
     * @param bool $required
     * 
     * @return Zend_Db_Expr
     */
    protected function _addAttributeToSelect($select, $attrCode, $entity, $store, $condition = null, $required = false)
    {
        return $this->getProductPriceIndexerHelper()
            ->addAttributeToSelect($this->_getReadAdapter(), $select, $attrCode, $entity, $store, $condition, $required);
    }
    /**
     * Prepare tier price index table
     *
     * @param int|array $entityIds the entity ids limitation
     * 
     * @return self
     */
    protected function _prepareTierPriceIndex($entityIds = null)
    {
        $indexerHelper      = $this->getProductPriceIndexerHelper();
        $adapter            = $this->_getWriteAdapter();
        $table              = $this->_getTierPriceIndexTable();
        $adapter->delete($table);
        
        $defaultPrice       = new Zend_Db_Expr("IF (tp.website_id=0, ROUND(tp.value * cwd.rate, 8), tp.value)");
        $price              = new Zend_Db_Expr("tp.value");
        $rate               = new Zend_Db_Expr("cr.rate");
        $price              = new Zend_Db_Expr("IF ({$rate} IS NOT NULL,  ROUND({$price} / {$rate}, 8), {$defaultPrice})");
        
        $columns = array(
            'entity_id'             => new Zend_Db_Expr('tp.entity_id'), 
            'customer_group_id'     => new Zend_Db_Expr('cg.customer_group_id'), 
            'website_id'            => new Zend_Db_Expr('cw.website_id'), 
            'min_price'             => new Zend_Db_Expr("MIN({$price})"), 
        );
        $group = array('tp.entity_id', 'cg.customer_group_id', 'cw.website_id');
        $select = $adapter->select()
            ->from(array('tp' => $this->getValueTable('catalog/product', 'tier_price')), array())
            ->join(
                array('cg' => $this->getTable('customer/customer_group')), 
                'tp.all_groups = 1 OR (tp.all_groups = 0 AND tp.customer_group_id = cg.customer_group_id)', 
                array()
            )->join(
                array('cw' => $this->getTable('core/website')), 
                'tp.website_id = 0 OR tp.website_id = cw.website_id', 
                array()
            )->join(
                array('cwd' => $this->_getWebsiteDateTable()), 
                'cw.website_id = cwd.website_id', 
                array()
            )->join(
                array('csg' => $this->getTable('core/store_group')), 
                'csg.website_id = cw.website_id AND cw.default_group_id = csg.group_id', 
                array()
            )->join(
                array('cs' => $this->getTable('core/store')), 
                'csg.default_store_id = cs.store_id AND cs.store_id != 0', 
                array()
            );
            
        $baseCurrency   = $this->_addAttributeToSelect($select, 'base_currency', 'tp.entity_id', 'cs.store_id');
        
        $select->joinLeft(
                array('cr' => $this->getTable('directory/currency_rate')), 
                "(cr.currency_from = {$indexerHelper->getBaseCurrencyExpr('cw.website_id')}) AND ".
                "(cr.currency_to = {$baseCurrency})", 
                array()
            )->where('cw.website_id != 0')
            ->columns($columns)
            ->group($group);
        if (!empty($entityIds)) {
            $select->where('tp.entity_id IN(?)', $entityIds);
        }
        $query = $select->insertFromSelect($table);
        $adapter->query($query);
        return $this;
    }
    /**
     * Prepare group price index table
     *
     * @param int|array $entityIds the entity ids limitation
     * 
     * @return self
     */
    protected function _prepareGroupPriceIndex($entityIds = null)
    {
        $indexerHelper      = $this->getProductPriceIndexerHelper();
        $adapter            = $this->_getWriteAdapter();
        $table              = $this->_getGroupPriceIndexTable();
        $adapter->delete($table);
        
        $defaultPrice       = new Zend_Db_Expr("IF (gp.website_id=0, ROUND(gp.value * cwd.rate, 8), gp.value)");
        $price              = new Zend_Db_Expr("gp.value");
        $rate               = new Zend_Db_Expr("cr.rate");
        $price              = new Zend_Db_Expr("IF ({$rate} IS NOT NULL,  ROUND({$price} / {$rate}, 8), {$defaultPrice})");
        
        $columns = array(
            'entity_id'             => new Zend_Db_Expr('gp.entity_id'), 
            'customer_group_id'     => new Zend_Db_Expr('cg.customer_group_id'), 
            'website_id'            => new Zend_Db_Expr('cw.website_id'), 
            'min_price'             => new Zend_Db_Expr("MIN({$price})"), 
        );
        $group = array('gp.entity_id', 'cg.customer_group_id', 'cw.website_id');
        $select = $adapter->select()
            ->from(array('gp' => $this->getValueTable('catalog/product', 'group_price')), array())
            ->join(
                array('cg' => $this->getTable('customer/customer_group')), 
                'gp.all_groups = 1 OR (gp.all_groups = 0 AND gp.customer_group_id = cg.customer_group_id)', 
                array()
            )->join(
                array('cw' => $this->getTable('core/website')), 
                'gp.website_id = 0 OR gp.website_id = cw.website_id', 
                array()
            )->join(
                array('cwd' => $this->_getWebsiteDateTable()), 
                'cw.website_id = cwd.website_id', 
                array()
            )->join(
                array('csg' => $this->getTable('core/store_group')), 
                'csg.website_id = cw.website_id AND cw.default_group_id = csg.group_id', 
                array()
            )->join(
                array('cs' => $this->getTable('core/store')), 
                'csg.default_store_id = cs.store_id AND cs.store_id != 0', 
                array()
            );
        
        $baseCurrency   = $this->_addAttributeToSelect($select, 'base_currency', 'gp.entity_id', 'cs.store_id');
        
        $select->joinLeft(
                array('cr' => $this->getTable('directory/currency_rate')), 
                "(cr.currency_from = {$indexerHelper->getBaseCurrencyExpr('cw.website_id')}) AND ".
                "(cr.currency_to = {$baseCurrency})", 
                array()
            )->where('cw.website_id != 0')
            ->columns($columns)
            ->group($group);
        if (!empty($entityIds)) {
            $select->where('gp.entity_id IN(?)', $entityIds);
        }
        $query = $select->insertFromSelect($table);
        $adapter->query($query);
        return $this;
    }
}