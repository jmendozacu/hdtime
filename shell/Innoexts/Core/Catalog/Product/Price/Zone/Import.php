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
 * Product zone price import
 * 
 * @category   Innoexts
 * @package    Innoexts_Shell
 * @author     Innoexts Team <developers@innoexts.com>
 */
class Innoexts_Shell_Core_Catalog_Product_Price_Zone_Import 
    extends Innoexts_Shell_Core_Catalog_Product_Import 
{
    /**
     * Get data table name
     * 
     * @return string
     */
    protected function getDataTableName()
    {
        return 'catalog/product_zone_price';
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
                "(country_id            = {$adapter->quote($datum['country_id'])})", 
                "(region_id             = {$adapter->quote($datum['region_id'])})", 
                "(is_zip_range          = {$adapter->quote($datum['is_zip_range'])})", 
                "IF(".
                    "is_zip_range = '1', ".
                    "(".implode(' AND ', array(
                        "(from_zip = {$adapter->quote($datum['from_zip'])}) ", 
                        "(to_zip = {$adapter->quote($datum['to_zip'])})", 
                    ))."), ".
                    ((is_null($datum['zip'])) ? 'zip IS NULL' :  "zip = {$adapter->quote($datum['zip'])}").
                ")", 
            ), 
            $this->getDatumAdditionalConditionsArray($datum)
        ));
    }
    
    /**
     * Get datum additional values
     * 
     * @param type $row
     * 
     * @return array|null
     */
    protected function getDatumAdditionalValues($row)
    {
        return array();
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
        
        $countryId          = $this->getRowFieldValue($row, 'country');
        if ($countryId) {
            $countryId = $this->getCoreHelper()
                ->getAddressHelper()
                ->castCountryId($countryId);
            if (!$countryId) {
                $this->printMessage("Can't find country: {$this->getRowFieldValue($row, 'country')}");
            }
        } else {
            $countryId = 0;
        }
        
        $regionId          = $this->getRowFieldValue($row, 'region');
        if ($regionId) {
            $regionId = $this->getCoreHelper()
                ->getAddressHelper()
                ->castRegionId($countryId, $regionId);
            if (!$regionId) {
                $this->printMessage("Can't find region: {$this->getRowFieldValue($row, 'region')}");
            }
        } else {
            $regionId = 0;
        }
        
        $isZipRange         = (int) $this->getRowFieldValue($row, 'is_zip_range');
        $isZipRange         = ($isZipRange) ? 1 : 0;
        
        $zip                = $this->getRowFieldValue($row, 'zip');
        $zip                = (!$isZipRange && $zip) ? $zip : null;
        
        $fromZip            = $this->getRowFieldValue($row, 'from_zip');
        $fromZip            = ($isZipRange && $fromZip) ? $fromZip : null;
        
        if ($isZipRange && is_null($fromZip)) {
            $this->printMessage("From zip is empty");
            return $this;
        }
        
        $toZip              = $this->getRowFieldValue($row, 'to_zip');
        $toZip              = ($isZipRange && $toZip) ? $toZip : null;
        
        if ($isZipRange && is_null($toZip)) {
            $this->printMessage("To zip is empty");
            return $this;
        }
        
        $zip                = ($isZipRange) ? $fromZip.'-'.$toZip : $zip;
        
        $priceType          = $this->getRowFieldValue($row, 'price_type');
        $priceType          = ($priceType && $priceType == 'percent') ? 'percent' : 'fixed';
        
        $price              = $this->getRowFieldValue($row, 'price');
        $price              = ($price !== '') ? $this->getCoreHelper()
            ->getProductHelper()
            ->getPriceHelper()
            ->round((float) $price) : null;
        
        $datum = array(
            'product_id'        => $productId, 
            'country_id'        => $countryId, 
            'region_id'         => $regionId, 
            'is_zip_range'      => $isZipRange, 
            'zip'               => $zip, 
            'from_zip'          => $fromZip, 
            'to_zip'            => $toZip, 
            'price_type'        => $priceType, 
            'price'             => $price, 
        );
        
        $_datum             = $this->getDatumAdditionalValues($row);
        if (is_null($_datum)) {
            return $this;
        }
        $datum = array_merge($datum, $_datum);
        
        if ($this->isDatumExists($datum)) {
            if (!is_null($datum['price'])) {
                $this->updateDatum($datum);
            } else {
                $this->removeDatum($datum);
            }
        } else if (!is_null($datum['price'])) {
            $this->addDatum($datum);
        }
        
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
            ->reindexProductPrice();
        return $this;
    }
}