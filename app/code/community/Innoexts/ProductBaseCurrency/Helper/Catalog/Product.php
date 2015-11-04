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
 * Product helper
 * 
 * @category   Innoexts
 * @package    Innoexts_ProductBaseCurrency
 * @author     Innoexts Team <developers@innoexts.com>
 */
class Innoexts_ProductBaseCurrency_Helper_Catalog_Product 
    extends Innoexts_Core_Helper_Catalog_Product 
{
    /**
     * Base currency attribute
     * 
     * @var Mage_Catalog_Model_Resource_Eav_Attribute
     */
    protected $_baseCurrencyAttribute;
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
     * Get base currency attribute
     * 
     * @return Mage_Catalog_Model_Resource_Eav_Attribute
     */
    public function getBaseCurrencyAttribute()
    {
        if (is_null($this->_baseCurrencyAttribute)) {
            $attribute = $this->getAttribute('base_currency');
            if ($attribute) {
                $this->_baseCurrencyAttribute = $attribute;
            }
        }
        return $this->_baseCurrencyAttribute;
    }
    /**
     * Get base currency code
     * 
     * @param Mage_Catalog_Model_Product $product
     * 
     * @return string
     */
    public function getBaseCurrencyCode($product)
    {
        $code       = $product->getBaseCurrency();
        if (!$code) {
            return null;
        }
        $code       = strtoupper($code);
        if (
            !$this->getCoreHelper()
                ->getCurrencyHelper()
                ->isCurrencyExists($code)
        ) {
            return null;
        }
        return $code;
    }
    /**
     * Check if base currency code is set
     * 
     * @param Mage_Catalog_Model_Product $product
     * 
     * @return bool
     */
    public function isBaseCurrencyCodeSet($product)
    {
        return ($this->getBaseCurrencyCode($product)) ? true : false;
    }
    /**
     * Add collection base currency
     *
     * @return self
     */
    public function addCollectionBaseCurrency($collection)
    {
        if (!$collection || $collection->getFlag('base_currency_added')) {
            return $this;
        }
        $collection->addAttributeToSelect('base_currency');
        $collection->setFlag('base_currency_added', true);
        return $this;
    }
}
