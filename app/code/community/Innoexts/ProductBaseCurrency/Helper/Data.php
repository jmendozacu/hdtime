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
 * Product base currency helper
 * 
 * @category   Innoexts
 * @package    Innoexts_ProductBaseCurrency
 * @author     Innoexts Team <developers@innoexts.com>
 */
class Innoexts_ProductBaseCurrency_Helper_Data 
    extends Innoexts_Core_Helper_Abstract 
{
    /**
     * Get version helper
     * 
     * @return Innoexts_Core_Helper_Version
     */
    public function getVersionHelper()
    {
        return $this->getCoreHelper()->getVersionHelper();
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
     * Get product price helper
     * 
     * @return Innoexts_ProductBaseCurrency_Helper_Catalog_Product_Price
     */
    public function getProductPriceHelper()
    {
        return $this->getProductHelper()->getPriceHelper();
    }
    /**
     * Get product price indexer helper
     * 
     * @return Innoexts_ProductBaseCurrency_Helper_Catalog_Product_Price_Indexer
     */
    public function getProductPriceIndexerHelper()
    {
        return $this->getProductPriceHelper()->getIndexerHelper();
    }
    /**
     * Get adminhtml helper
     * 
     * @return Innoexts_ProductBaseCurrency_Helper_Adminhtml
     */
    public function getAdminhtmlHelper()
    {
        return Mage::helper('productbasecurrency/adminhtml');
    }
    /**
     * Get config
     * 
     * @return Innoexts_ProductBaseCurrency_Model_Config
     */
    public function getConfig()
    {
        return Mage::getSingleton('productbasecurrency/config');
    }
}