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

require_once rtrim(dirname(__FILE__), '/').'/../../Export.php';

/**
 * Product price export
 * 
 * @category   Innoexts
 * @package    Innoexts_Shell
 * @author     Innoexts Team <developers@innoexts.com>
 */
class Innoexts_Shell_Core_Catalog_Product_Price_Price_Export 
    extends Innoexts_Shell_Core_Catalog_Product_Export 
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
     * Get field names
     * 
     * @return array
     */
    protected function getFieldNames()
    {
        return array(
            'sku', 
            'store', 
            'price', 
        );
    }
    /**
     * Get row
     * 
     * @param array $item
     * 
     * @return array
     */
    protected function getRow($item)
    {
        return array(
            'sku'                   => $this->getCoreHelper()
                ->getProductHelper()
                ->getProductSkuById($item['entity_id']), 
            'store'                 => ($item['store_id']) ? $this->getCoreHelper()
                    ->getStoreCodeById($item['store_id']) : '', 
            'price'                 => $item['value'], 
        );
    }
    /**
     * Get rows
     * 
     * @return array
     */
    protected function getRows()
    {
        $rows       = array();
        $adapter    = $this->getWriteAdapter();
        $select     = $this->getSelect()
            ->from($this->getDataTable())
            ->where(
                implode(' AND ', array(
                    "(entity_type_id    = {$adapter->quote($this->getEntityTypeId())})", 
                    "(attribute_id      = {$adapter->quote($this->getAttributeId())})", 
                ))
            );
        foreach ($adapter->fetchAll($select) as $item) {
            array_push($rows, $this->getRow($item));
        }
        return $rows;
    }
}
