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
 * Catalog rule
 * 
 * @category   Innoexts
 * @package    Innoexts_ProductBaseCurrency
 * @author     Innoexts Team <developers@innoexts.com>
 */
class Innoexts_ProductBaseCurrency_Model_Catalogrule_Rule 
    extends Mage_CatalogRule_Model_Rule 
{
    /**
     * Get product bse currency helper
     * 
     * @return Innoexts_ProductBaseCurrency_Helper_Data
     */
    protected function getProductBaseCurrencyHelper()
    {
        return Mage::helper('productbasecurrency');
    }
    /**
     * Get version helper
     * 
     * @return Innoexts_Core_Helper_Version
     */
    public function getVersionHelper()
    {
        return $this->getProductBaseCurrencyHelper()
            ->getVersionHelper();
    }
    /**
     * Calculate price
     *
     * @param Mage_Catalog_Model_Product $product
     * @param float $price
     * 
     * @return float|null
     */
    public function calcProductPriceRule(Mage_Catalog_Model_Product $product, $price)
    {
        $helper             = $this->getProductBaseCurrencyHelper();
        $coreHelper         = $helper->getCoreHelper();
        $priceHelper        = $helper->getProductPriceHelper();
        $priceRules         = null;
        $productId          = $product->getId();
        $storeId            = $product->getStoreId();
        $websiteId          = $coreHelper->getWebsiteIdByStoreId($storeId);
        if ($product->hasCustomerGroupId()) {
            $customerGroupId = $product->getCustomerGroupId();
        } else {
            $customerGroupId = $coreHelper->getCustomerHelper()->getCustomerGroupId();
        }
        $dateTs             = Mage::app()->getLocale()->storeTimeStamp($storeId);
        $cacheKey           = date('Y-m-d', $dateTs).implode('|', array(
            $websiteId, $customerGroupId, $productId, $price
        ));
        if (!array_key_exists($cacheKey, self::$_priceRulesData)) {
            $rulesData = $this->_getResource()->getRulesFromProduct(
                $dateTs, $websiteId, $customerGroupId, $productId
            );
            if ($rulesData) {
                foreach ($rulesData as $ruleData) {
                    if ($this->getVersionHelper()->isGe1610() && $product->getParentId()) {
                        if (
                            ($this->getVersionHelper()->isGe1700() && !empty($ruleData['sub_simple_action'])) || 
                            (!$this->getVersionHelper()->isGe1700() && $ruleData['sub_is_enable'])
                        ) {
                            if (in_array($ruleData['sub_simple_action'], array('to_fixed', 'by_fixed'))) {
                                $ruleData['sub_discount_amount'] = $priceHelper->getCurrentStoreBaseCurrencyPrice(
                                    $product, $ruleData['sub_discount_amount']
                                );
                            }
                            $priceRules = Mage::helper('catalogrule')->calcPriceRule(
                                $ruleData['sub_simple_action'],
                                $ruleData['sub_discount_amount'],
                                $priceRules ? $priceRules : $price
                            );
                        } else {
                            $priceRules = $price;
                        }
                        if (
                            ($this->getVersionHelper()->isGe1700() && $ruleData['action_stop']) || 
                            (!$this->getVersionHelper()->isGe1700() && $ruleData['stop_rules_processing'])
                        ) {
                            break;
                        }
                    } else {
                        if ($this->getVersionHelper()->isGe1700()) {
                            if (in_array($ruleData['action_operator'], array('to_fixed', 'by_fixed'))) {
                                $ruleData['action_amount'] = $priceHelper->getCurrentStoreBaseCurrencyPrice(
                                    $product, $ruleData['action_amount']
                                );
                            }
                            $priceRules = Mage::helper('catalogrule')->calcPriceRule(
                                $ruleData['action_operator'],
                                $ruleData['action_amount'],
                                $priceRules ? $priceRules : $price
                            );
                        } else {
                            if (in_array($ruleData['simple_action'], array('to_fixed', 'by_fixed'))) {
                                $ruleData['discount_amount'] = $priceHelper->getCurrentStoreBaseCurrencyPrice(
                                    $product, $ruleData['discount_amount']
                                );
                            }
                            $priceRules = Mage::helper('catalogrule')->calcPriceRule(
                                $ruleData['simple_action'],
                                $ruleData['discount_amount'],
                                $priceRules ? $priceRules :$price
                            );
                        }
                        if (
                            ($this->getVersionHelper()->isGe1700() && $ruleData['action_stop']) || 
                            (!$this->getVersionHelper()->isGe1700() && $ruleData['stop_rules_processing'])
                        ) {
                            break;
                        }
                    }
                }
                return self::$_priceRulesData[$cacheKey] = $priceRules;
            } else {
                self::$_priceRulesData[$cacheKey] = null;
            }
        } else {
            return self::$_priceRulesData[$cacheKey];
        }
        return null;
    }
}