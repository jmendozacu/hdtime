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
 * Base currency renderer
 * 
 * @category   Innoexts
 * @package    Innoexts_ProductBaseCurrency
 * @author     Innoexts Team <developers@innoexts.com>
 */

class Innoexts_ProductBaseCurrency_Block_Adminhtml_Catalog_Product_Edit_Tab_Basecurrency_Renderer 
    extends Innoexts_Core_Block_Adminhtml_Catalog_Form_Renderer_Fieldset_Element 
{
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->setTemplate(
            'innoexts/productbasecurrency/catalog/product/edit/tab/basecurrency/renderer.phtml'
        );
    }
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
     * Get default base currency code
     * 
     * @return string
     */
    public function getDefaultBaseCurrencyCode()
    {
        return $this->getStore()->getBaseCurrencyCode();
    }
    /**
     * Get price element
     * 
     * @return Varien_Data_Form_Element_Abstract
     */
    protected function getPriceElement()
    {
        return $this->getElement()
            ->getForm()
            ->getElement('price');
    }
    /**
     * Check if price element present
     * 
     * @return bool
     */
    protected function hasPriceElement()
    {
        return ($this->getPriceElement()) ? true : false;
    }
    /**
     * Get price element id
     * 
     * @return string
     */
    public function getPriceElementId()
    {
        return ($this->hasPriceElement()) ? $this->getPriceElement()->getHtmlId() : null;
    }
    /**
     * Get special price element
     * 
     * @return Varien_Data_Form_Element_Abstract
     */
    protected function getSpecialPriceElement()
    {
        return $this->getElement()
            ->getForm()
            ->getElement('special_price');
    }
    /**
     * Check if special price element present
     * 
     * @return bool
     */
    protected function hasSpecialPriceElement()
    {
        return ($this->getSpecialPriceElement()) ? true : false;
    }
    /**
     * Get special price element id
     * 
     * @return string
     */
    public function getSpecialPriceElementId()
    {
        return ($this->hasSpecialPriceElement()) ? $this->getSpecialPriceElement()->getHtmlId() : null;
    }
    /**
     * Get group price element
     * 
     * @return Varien_Data_Form_Element_Abstract
     */
    protected function getGroupPriceElement()
    {
        return $this->getElement()
            ->getForm()
            ->getElement('group_price');
    }
    /**
     * Check if group price element present
     * 
     * @return bool
     */
    protected function hasGroupPriceElement()
    {
        return ($this->getGroupPriceElement()) ? true : false;
    }
    /**
     * Get group price element id
     * 
     * @return string
     */
    public function getGroupPriceElementId()
    {
        return ($this->hasGroupPriceElement()) ? $this->getGroupPriceElement()->getHtmlId() : null;
    }
    /**
     * Get tier price element
     * 
     * @return Varien_Data_Form_Element_Abstract
     */
    protected function getTierPriceElement()
    {
        return $this->getElement()
            ->getForm()
            ->getElement('tier_price');
    }
    /**
     * Check if tier price element present
     * 
     * @return bool
     */
    protected function hasTierPriceElement()
    {
        return ($this->getTierPriceElement()) ? true : false;
    }
    /**
     * Get tier price element id
     * 
     * @return string
     */
    public function getTierPriceElementId()
    {
        return ($this->hasTierPriceElement()) ? $this->getTierPriceElement()->getHtmlId() : null;
    }
    /**
     * Get website base currency codes
     * 
     * @return array
     */
    protected function getWebsiteBaseCurrencyCodesJSON()
    {
        $coreHelper = $this->getCoreHelper();
        return $coreHelper->jsonEncode(
            $coreHelper->getCurrencyHelper()
                ->getWebsiteBaseCodes()
        );
    }
}