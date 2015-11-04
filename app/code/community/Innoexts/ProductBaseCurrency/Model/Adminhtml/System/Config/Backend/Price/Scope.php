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
 * Price scope backend
 * 
 * @category   Innoexts
 * @package    Innoexts_ProductBaseCurrency
 * @author     Innoexts Team <developers@innoexts.com>
 */
class Innoexts_ProductBaseCurrency_Model_Adminhtml_System_Config_Backend_Price_Scope 
    extends Mage_Adminhtml_Model_System_Config_Backend_Price_Scope 
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
     * After commit callback
     * 
     * @return self
     */
    public function afterCommitCallback()
    {
        parent::afterCommitCallback();
        if ($this->isValueChanged()) {
            $helper                 = $this->getProductBaseCurrencyHelper();
            $processHelper          = $helper->getCoreHelper()
                ->getProcessHelper();
            $requireReindexStatus   = Mage_Index_Model_Process::STATUS_REQUIRE_REINDEX;
            $processHelper->changeProductPriceStatus($requireReindexStatus);
            $processHelper->changeProductFlatStatus($requireReindexStatus);
            $processHelper->changeSearchStatus($requireReindexStatus);
        }
        return $this;
    }
}