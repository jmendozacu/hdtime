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
 * Product price observer
 * 
 * @category   Innoexts
 * @package    Innoexts_ProductBaseCurrency
 * @author     Innoexts Team <developers@innoexts.com>
 */
class Innoexts_ProductBaseCurrency_Model_Catalog_Product_Price_Observer 
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
     * Get product price helper
     * 
     * @return Innoexts_ProductBaseCurrency_Helper_Catalog_Product_Price
     */
    protected function getProductPriceHelper()
    {
        return $this->getProductBaseCurrencyHelper()
            ->getProductPriceHelper();
    }
    /**
     * Set collection price
     * 
     * @param Varien_Event_Observer $observer
     * 
     * @return self
     */
    public function setCollectionPrice(Varien_Event_Observer $observer)
    {
        $this->getProductPriceHelper()
            ->setCollectionPrice($observer->getEvent()->getCollection());
        return $this;
    }
    /**
     * Set collection special price
     * 
     * @param Varien_Event_Observer $observer
     * 
     * @return self
     */
    public function setCollectionSpecialPrice(Varien_Event_Observer $observer)
    {
        $this->getProductPriceHelper()
            ->setCollectionSpecialPrice($observer->getEvent()->getCollection());
        return $this;
    }
}
