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
 * Admin product observer
 * 
 * @category   Innoexts
 * @package    Innoexts_ProductBaseCurrency
 * @author     Innoexts Team <developers@innoexts.com>
 */
class Innoexts_ProductBaseCurrency_Model_Adminhtml_Product_Observer 
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
     * Prepare edit form
     * 
     * @param Varien_Event_Observer $observer
     * 
     * @return self
     */
    public function prepareEditForm(Varien_Event_Observer $observer)
    {
        $form           = $observer->getEvent()->getForm();
        if (!$form) {
            return $this;
        }
        $baseCurrency   = $form->getElement('base_currency');
        if (!$baseCurrency) {
            return $this;
        }
        $baseCurrency->setRenderer(
            $this->getProductBaseCurrencyHelper()
                ->getCoreHelper()
                ->getLayout()
                ->createBlock('adminhtml/catalog_product_edit_tab_basecurrency_renderer')
        );
        return $this;
    }
    /**
     * Prepare grid
     * 
     * @param Varien_Event_Observer $observer
     * 
     * @return self
     */
    public function prepareGrid(Varien_Event_Observer $observer)
    {
        $block              = $observer->getEvent()->getBlock();
        if (!$block) {
            return $this;
        }
        $helper             = $this->getProductBaseCurrencyHelper();
        $coreHelper         = $helper->getCoreHelper();
        $adminHtml          = $helper->getAdminhtmlHelper();
        if (
            ($block instanceof Mage_Adminhtml_Block_Catalog_Product_Grid) || 
            ($block instanceof Mage_Adminhtml_Block_Tag_Assigned_Grid)
        ) {
            
            $adminHtml->addProductCustomPriceColumn(
                $block, 
                $coreHelper->getRequestStore()->getId()
            );
            
        } else if ($block instanceof Mage_Adminhtml_Block_Sales_Order_Create_Search_Grid) {
            
            $store = Mage::getSingleton('adminhtml/session_quote')->getStore();
            $adminHtml->addProductCustomPriceColumn(
                $block, 
                $store->getId(), 
                null, 
                'currency', 
                null, 
                'adminhtml/sales_order_create_search_grid_renderer_price', 
                $store->getCurrentCurrencyCode(), 
                $store->getBaseCurrency()->getRate($store->getCurrentCurrencyCode()), 
                'price', 
                'center'
            );
            
        } else if (
            ($block instanceof Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Upsell) || 
            ($block instanceof Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Crosssell) || 
            ($block instanceof Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Related) || 
            ($block instanceof Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Alerts_Price) || 
            ($block instanceof Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Super_Config_Grid) || 
            ($block instanceof Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Super_Group) || 
            ($block instanceof Mage_Adminhtml_Block_Catalog_Category_Tab_Product) || 
            ($block instanceof Mage_Bundle_Block_Adminhtml_Catalog_Product_Edit_Tab_Bundle_Option_Search_Grid) || 
            ($block instanceof Mage_Adminhtml_Block_Customer_Edit_Tab_Cart) || 
            ($block instanceof Mage_Adminhtml_Block_Review_Product_Grid)
        ) {
            
            $adminHtml->addProductCustomPriceColumn(
                $block, 
                0, 
                null, 
                'currency', 
                null
            );
            
        } else if ($block instanceof Mage_Adminhtml_Block_Report_Shopcart_Product_Grid) {
            
            $adminHtml->addProductCustomPriceColumn(
                $block, 
                0, 
                null, 
                'currency', 
                null, 
                'adminhtml/report_grid_column_renderer_currency', 
                $block->getCurrentCurrencyCode(), 
                $block->getRate($block->getCurrentCurrencyCode()), 
                null, 
                null, 
                '80px'
            );
            
        } else if (($block instanceof Mage_GoogleBase_Block_Adminhtml_Items_Product)) {
            
            $store = $coreHelper->getRequestStore();
            $adminHtml->addProductCustomPriceColumn(
                $block, 
                $store->getId(), 
                null, 
                'currency', 
                null, 
                null, 
                $store->getCurrentCurrencyCode(), 
                $store->getBaseCurrency()->getRate($store->getCurrentCurrencyCode()), 
                null, 
                'align'
            );
            
        }
        return $this;
    }
    /**
     * Adjust grid
     * 
     * @param Varien_Event_Observer $observer
     * 
     * @return self
     */
    public function adjustGrid(Varien_Event_Observer $observer)
    {
        $block              = $observer->getEvent()->getBlock();
        if (!$block) {
            return $this;
        }
        $helper             = $this->getProductBaseCurrencyHelper();
        $adminHtml          = $helper->getAdminhtmlHelper();
        if (
            ($block instanceof Mage_Adminhtml_Block_Catalog_Product_Grid) || 
            ($block instanceof Mage_Adminhtml_Block_Sales_Order_Create_Search_Grid) || 
            ($block instanceof Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Upsell) || 
            ($block instanceof Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Crosssell) || 
            ($block instanceof Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Related) || 
            ($block instanceof Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Alerts_Price) || 
            ($block instanceof Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Super_Config_Grid) || 
            ($block instanceof Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Super_Group) || 
            ($block instanceof Mage_Adminhtml_Block_Catalog_Category_Tab_Product) || 
            ($block instanceof Mage_Bundle_Block_Adminhtml_Catalog_Product_Edit_Tab_Bundle_Option_Search_Grid) || 
            ($block instanceof Mage_Adminhtml_Block_Customer_Edit_Tab_Cart) || 
            ($block instanceof Mage_Adminhtml_Block_Review_Product_Grid) || 
            ($block instanceof Mage_Adminhtml_Block_Tag_Assigned_Grid) || 
            ($block instanceof Mage_GoogleBase_Block_Adminhtml_Items_Product)
        ) {
            $adminHtml->adjustProductCustomPriceColumn($block, 'sku');
        } else if (($block instanceof Mage_Adminhtml_Block_Report_Shopcart_Product_Grid)) {
            $adminHtml->adjustProductCustomPriceColumn($block, 'name');
        }
        return $this;
    }
}