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
 * @package     Innoexts_Shell
 * @copyright   Copyright (c) 2014 Innoexts (http://www.innoexts.com)
 * @license     http://innoexts.com/commercial-license-agreement  InnoExts Commercial License
 */

require_once rtrim(dirname(__FILE__), '/').'/../../Import.php';

/**
 * Product price import
 * 
 * @category   Innoexts
 * @package    Innoexts_Shell
 * @author     Innoexts Team <developers@innoexts.com>
 */
class Innoexts_Shell_Core_Catalog_Product_Price_Price_Import 
    extends Innoexts_Shell_Core_Catalog_Product_Import 
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
     * Get data table name
     * 
     * @return string
     */
    protected function getDataTableName()
    {
        return 'catalog_product_entity_decimal';
    }
    /**
     * Get attribute
     * 
     * @return Mage_Catalog_Model_Resource_Eav_Attribute
     */
    protected function getAttribute()
    {
        return $this->getCoreHelper()
            ->getProductHelper()
            ->getPriceHelper()
            ->getPriceAttribute();
    }
    /**
     * Get attribute id
     * 
     * @return int
     */
    protected function getAttributeId()
    {
        return $this->getAttribute()->getId();
    }
    /**
     * Get entity type id
     * 
     * @return int
     */
    protected function getEntityTypeId()
    {
        return $this->getAttribute()->getEntityTypeId();
    }
    /**
     * Get datum conditions
     * 
     * @param array $data
     * 
     * @return string
     */
    protected function getDatumConditions($datum)
    {
        $adapter = $this->getWriteAdapter();
        return implode(' AND ', array(
            "(entity_id         = {$adapter->quote($datum['entity_id'])})", 
            "(entity_type_id    = {$adapter->quote($datum['entity_type_id'])})", 
            "(attribute_id      = {$adapter->quote($datum['attribute_id'])})", 
            "(store_id          = {$adapter->quote($datum['store_id'])})", 
        ));
    }
    /**
     * Import row
     * 
     * @param array $row
     * 
     * @return self
     */
    protected function importRow($row)
    {
        $productId          = $this->getRowFieldValue($row, 'sku');
        if (!$productId) {
            $this->printMessage("Sku is empty");
            return $this;
        }        
        $productId          = $this->getCoreHelper()
            ->getProductHelper()
            ->getProductIdBySku($productId);
        if (!$productId) {
            $this->printMessage("Can't find product by sku: {$this->getRowFieldValue($row, 'sku')}");
            return $this;
        }
        
        $storeId            = $this->getRowFieldValue($row, 'store');
        if ($storeId) {
            $storeId          = $this->getCoreHelper()
                ->getStoreIdByCodeOrId($storeId);
            if (is_null($storeId)) {
                $this->printMessage("Can't find store: {$this->getRowFieldValue($row, 'store')}");
                return $this;
            }
        } else {
            $storeId          = 0;
        }
        
        $value              = $this->getCoreHelper()
            ->getProductHelper()
            ->getPriceHelper()
            ->round((float) $this->getRowFieldValue($row, 'price'));
        
        $datum = array(
            'entity_id'         => $productId, 
            'entity_type_id'    => $this->getEntityTypeId(), 
            'attribute_id'      => $this->getAttributeId(), 
            'store_id'          => $storeId, 
            'value'             => $value, 
        );
        
        $this->appendDatum($datum);
        
        return $this;
    }
    /**
     * Reindex
     * 
     * @return self
     */
    protected function reindex()
    {
        $this->printMessage('Reindexing.');
        $this->getCoreHelper()
            ->getProcessHelper()
            ->reindexProductFlat()
            ->reindexProductPrice();
        return $this;
    }
    
}