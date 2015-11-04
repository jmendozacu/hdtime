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
 * Product group price backend attribute
 * 
 * @category   Innoexts
 * @package    Innoexts_ProductBaseCurrency
 * @author     Innoexts Team <developers@innoexts.com>
 */
class Innoexts_ProductBaseCurrency_Model_Catalog_Product_Attribute_Backend_Groupprice 
    extends Mage_Catalog_Model_Product_Attribute_Backend_Groupprice 
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
     * Get product helper
     * 
     * @return Innoexts_ProductBaseCurrency_Helper_Catalog_Product
     */
    protected function getProductHelper()
    {
        return $this->getProductBaseCurrencyHelper()->getProductHelper();
    }
    /**
     * Get product price helper
     * 
     * @return Innoexts_ProductBaseCurrency_Helper_Catalog_Product_Price
     */
    protected function getProductPriceHelper()
    {
        return $this->getProductBaseCurrencyHelper()->getProductPriceHelper();
    }
    /**
     * Set attribute instance
     * 
     * @param Mage_Catalog_Model_Resource_Eav_Attribute $attribute
     * 
     * @return self
     */
    public function setAttribute($attribute)
    {
        parent::setAttribute($attribute);
        $this->setScope($attribute);
        return $this;
    }
    /**
     * Redefine attribute scope
     *
     * @param Mage_Catalog_Model_Resource_Eav_Attribute $attribute
     * 
     * @return self
     */
    public function setScope($attribute)
    {
        $this->getProductBaseCurrencyHelper()
            ->getProductPriceHelper()
            ->setAttributeScope($attribute);
        return $this;
    }
    /**
     * Get data key
     * 
     * @param array $data
     * @param bool $allWebsites
     * 
     * @return string
     */
    protected function getDataKey($data, $allWebsites = false)
    {
        return join('-', array(
            (($allWebsites) ? 0 : $data['website_id']), 
            $data['cust_group']
        ));
    }
    /**
     * Get short data key
     * 
     * @param array $data
     * 
     * @return string 
     */
    protected function getShortDataKey($data)
    {
        return join('-', array(
            $data['cust_group']
        ));
    }
    /**
     * Sort price data
     *
     * @param array $a
     * @param array $b
     * 
     * @return int
     */
    protected function _sortPriceData($a, $b)
    {
        if ($a['website_id'] != $b['website_id']) {
            return $a['website_id'] < $b['website_id'] ? 1 : -1;
        }
        return 0;
    }
    /**
     * Prepare prices data
     *
     * @param array $priceData
     * @param string $productTypeId
     * @param int $websiteId
     * @param string $baseCurrency
     * 
     * @return array
     */
    public function preparePriceData2(array $priceData, $productTypeId, $websiteId, $baseCurrency)
    {
        $helper                 = $this->getProductBaseCurrencyHelper();
        $coreHelper             = $helper->getCoreHelper();
        $priceHelper            = $this->getProductPriceHelper();
        $currencyHelper         = $coreHelper->getCurrencyHelper();
        $websiteBaseCurrency    = $coreHelper->getWebsiteById($websiteId)
            ->getBaseCurrencyCode();
        $rate                   = ($baseCurrency) ? $currencyHelper->getRate($websiteBaseCurrency, $baseCurrency) : 1;
        $isGroupPriceFixed      = $priceHelper->isGroupPriceFixed($productTypeId);
        $data                   = array();
        usort($priceData, array($this, '_sortPriceData'));
        foreach ($priceData as $v) {
            $key = $this->getShortDataKey($v);
            if (
                !isset($data[$key]) && (
                    ( $v['website_id'] == $websiteId ) || 
                    ( $v['website_id'] == 0 )
                )
            ) {
                $data[$key] = $v;
                $data[$key]['website_id'] = $websiteId;
                if ($isGroupPriceFixed) {
                    $price                          = ($rate) ? ($v['price'] / $rate) : $v['price'];
                    $data[$key]['price']            = $price;
                    $data[$key]['website_price']    = $price;
                }
            }
        }
        return $data;
    }
    /**
     * After load
     * 
     * @param Mage_Catalog_Model_Product $object
     * 
     * @return self
     */
    public function afterLoad($object)
    {
        $helper             = $this->getProductBaseCurrencyHelper();
        $priceHelper        = $helper->getProductPriceHelper();
        $resource           = $this->_getResource();
        $storeId            = $object->getStoreId();
        $attributeName      = $this->getAttribute()->getName();
        $websiteId          = null;
        if ($priceHelper->isGlobalScope()) {
            $websiteId          = null;
        } else if ($priceHelper->isWebsiteScope() && $storeId) {
            $websiteId          = $helper->getCoreHelper()
                ->getWebsiteIdByStoreId($storeId);
        }
        $data               = $resource->loadPriceData($object->getId(), $websiteId);
        foreach ($data as $k => $v) {
            $data[$k]['website_price'] = $v['price'];
            if ($v['all_groups']) {
                $data[$k]['cust_group'] = Mage_Customer_Model_Group::CUST_GROUP_ALL;
            }
        }
        $object->setGroupPrices($data);
        $priceHelper->setGroupPrice($object);
        $object->setOrigData($attributeName, $data);
        $valueChangedKey = $attributeName.'_changed';
        $object->setOrigData($valueChangedKey, 0);
        $object->setData($valueChangedKey, 0);
        return $this;
    }
}