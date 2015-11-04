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
 * Admin html helper
 * 
 * @category   Innoexts
 * @package    Innoexts_ProductBaseCurrency
 * @author     Innoexts Team <developers@innoexts.com>
 */
class Innoexts_ProductBaseCurrency_Helper_Adminhtml 
    extends Innoexts_Core_Helper_Adminhtml 
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
     * Get product base currency field expression
     * 
     * @param mixed $storeId
     * 
     * @return Zend_Db_Expr
     */
    protected function getProductBaseCurrencyFieldExpr($storeId)
    {
        return $this->getEavFieldExpr('base_currency', $storeId);
    }
    /**
     * Get product custom price field expression
     * 
     * @param mixed $storeId
     * 
     * @return Zend_Db_Expr
     */
    protected function getProductCustomPriceFieldExpr($storeId)
    {
        $price                  = $this->getEavFieldExpr('custom_price', $storeId);
        $rate                   = new Zend_Db_Expr("cr.rate");
        return new Zend_Db_Expr("IF (".
            "{$rate} IS NOT NULL, ".
            "ROUND({$price} / {$rate}, 8), ".
            "{$price}".
        ")");
    }
    /**
     * Add product custom price column filter to collection
     * 
     * @param Mage_Core_Model_Resource_Db_Collection_Abstract $collection
     * @param Mage_Adminhtml_Block_Widget_Grid_Column $column
     * 
     * @return self
     */
    public function addProductCustomPriceColumnFilterToCollection($collection, $column)
    {
        $helper             = $this->getProductBaseCurrencyHelper();
        $coreHelper         = $helper->getCoreHelper();
        $adapter            = $collection->getConnection();
        $condition          = $column->getFilter()->getCondition();
        $select             = $collection->getSelect();
        $store              = $coreHelper->getStoreById($column->getStoreId());
        $storeId            = $store->getId();
        $baseCurrencyCode   = $store->getBaseCurrencyCode();
        $collection->joinAttribute(
            'custom_price', 
            'catalog_product/price', 
            'entity_id', 
            null, 
            'left', 
            $storeId
        );
        $collection->joinAttribute(
            'base_currency', 
            'catalog_product/base_currency', 
            'entity_id', 
            null, 
            'left', 
            $storeId
        );
        $select->joinLeft(
            array('cr' => $coreHelper->getTable('directory/currency_rate')), 
            implode(' AND ', array(
                "(cr.currency_from = {$adapter->quote($baseCurrencyCode)})", 
                "(cr.currency_to = {$this->getProductBaseCurrencyFieldExpr($storeId)})"
            )), 
            array()
        );
        $price              = $this->getProductCustomPriceFieldExpr($storeId);
        $conditions         = array();
        if (isset($condition['from'])) {
            array_push($conditions, "{$price} >= {$adapter->quote($condition['from'])}");
        }
        if (isset($condition['to'])) {
            array_push($conditions, "{$price} <= {$adapter->quote($condition['to'])}");
        }
        if (count($conditions)) {
            $select->where(implode(' AND ', $conditions));
        }
        
        return $this;
    }
    /**
     * Add product custom price column
     * 
     * @param Mage_Adminhtml_Block_Sales_Order_Grid $grid
     * @param string $header
     * @param string $type
     * @param string $index
     * @param mixed $renderer
     * @param string $currencyCode
     * @param float $rate
     * @param string $columnCssClass
     * @param string $align
     * @param string $width
     * @param bool $sortable
     * 
     * @return self
     */
    public function addProductCustomPriceColumn(
        $grid, 
        $storeId = null, 
        $header = null, 
        $type = null, 
        $index = null, 
        $renderer = null, 
        $currencyCode = null, 
        $rate = null, 
        $columnCssClass = null, 
        $align = null, 
        $width = null, 
        $sortable = null
    ) {
        $helper             = $this->getProductBaseCurrencyHelper();
        $column             = array(
            'header'                    => (($header) ? $header : $helper->__('Price')), 
            'type'                      => (($type) ? $type : 'price'), 
            'index'                     => (($index) ? $index : 'price'), 
            'currency_code'             => (($currencyCode) ? $currencyCode : $helper->getCoreHelper()
                ->getStoreById($storeId)
                ->getBaseCurrencyCode()), 
            'filter_condition_callback' => array($this, 'addProductCustomPriceColumnFilterToCollection'), 
            'store_id'                  => $storeId, 
        );
        if ($renderer) {
            $column['renderer']         = $renderer;
        }
        if ($rate) {
            $column['rate']             = $rate;
        }
        if ($columnCssClass) {
            $column['column_css_class'] = $columnCssClass;
        }
        if ($align) {
            $column['align']            = $align;
        }
        if ($width) {
            $column['width']            = $width;
        }
        if ($sortable) {
            $column['sortable']         = $sortable;
        }
        $grid->addColumn('custom_price', $column);
        return $this;
    }
    /**
     * Adjust product custom price column
     * 
     * @param Mage_Adminhtml_Block_Catalog_Product_Grid $grid
     * @param string $after
     * 
     * @return self
     */
    public function adjustProductCustomPriceColumn($grid, $after)
    {
        $helper         = $this->getProductBaseCurrencyHelper();
        if ($helper->getVersionHelper()->isGe1600()) {
            $grid->removeColumn('price');
        }
        $grid->addColumnsOrder('custom_price', $after);
        $grid->sortColumnsByOrder();
        return $this;
    }
}