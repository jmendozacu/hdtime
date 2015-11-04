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
 * Product compound special price export
 * 
 * @category   Innoexts
 * @package    Innoexts_Shell
 * @author     Innoexts Team <developers@innoexts.com>
 */
class Innoexts_Shell_Core_Catalog_Product_Price_CompoundSpecial_Export 
    extends Innoexts_Shell_Core_Catalog_Product_Export 
{
    /**
     * Get data table name
     * 
     * @return string
     */
    protected function getDataTableName()
    {
        return 'catalog/product_compound_special_price';
    }
    /**
     * Get datum additional conditions array
     * 
     * @param array $data
     * 
     * @return array
     */
    protected function getDatumAdditionalConditionsArray($datum)
    {
        $adapter = $this->getWriteAdapter();
        return array();
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
        return implode(' AND ', array_merge(
                array(
                "(product_id            = {$adapter->quote($datum['product_id'])})", 
            ), 
            $this->getDatumAdditionalConditionsArray($datum)
        ));
    }
    /**
     * Get additional field names
     * 
     * @return array
     */
    protected function getAdditionalFieldNames()
    {
        return array();
    }
    /**
     * Get field names
     * 
     * @return array
     */
    protected function getFieldNames()
    {
        return array_merge(
            array(
                'sku', 
            ), 
            $this->getAdditionalFieldNames(), 
            array(
                'price', 
            )
        );
    }
    /**
     * Get row additional fields
     * 
     * @param array $item
     * 
     * @return array
     */
    protected function getRowAdditionalFields($item)
    {
        return array();
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
        return array_merge(
            array(
                'sku'               => $this->getCoreHelper()
                    ->getProductHelper()
                    ->getProductSkuById($item['product_id']), 
            ), 
            $this->getRowAdditionalFields($item), 
            array(
                'price'             => (float) $item['price'], 
            )
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
            ->from($this->getDataTable());
        foreach ($adapter->fetchAll($select) as $item) {
            array_push($rows, $this->getRow($item));
        }
        return $rows;
    }
}