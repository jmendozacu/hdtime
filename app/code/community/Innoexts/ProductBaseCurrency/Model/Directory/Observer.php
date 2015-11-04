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
 * Directory observer
 * 
 * @category   Innoexts
 * @package    Innoexts_ProductBaseCurrency
 * @author     Innoexts Team <developers@innoexts.com>
 */
class Innoexts_ProductBaseCurrency_Model_Directory_Observer 
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
     * After currency rates save
     * 
     * @param Varien_Event_Observer $observer
     * 
     * @return self
     */
    public function afterCurrencyRatesSave(Varien_Event_Observer $observer)
    {
        $rates = $observer->getEvent()->getRates();
        if (count($rates)) {
            $processHelper  = $this->getProductBaseCurrencyHelper()
                ->getCoreHelper()
                ->getProcessHelper();
            $processHelper->reindexProductPrice();
        }
        return $this;
    }
}