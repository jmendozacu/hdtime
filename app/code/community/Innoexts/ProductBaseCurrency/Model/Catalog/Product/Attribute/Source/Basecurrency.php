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
 * Product base currency attribute source
 * 
 * @category   Innoexts
 * @package    Innoexts_ProductBaseCurrency
 * @author     Innoexts Team <developers@innoexts.com>
 */
class Innoexts_ProductBaseCurrency_Model_Catalog_Product_Attribute_Source_Basecurrency 
    extends Mage_Eav_Model_Entity_Attribute_Source_Abstract 
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
     * Get all options
     * 
     * @return array
     */
    public function getAllOptions()
    {
        if (!$this->_options) {
            $helper             = $this->getProductBaseCurrencyHelper();
            $this->_options     = $helper
                ->getCoreHelper()
                ->getCurrencyHelper()
                ->getOptions(false, $helper->__('None'), '');
        }
        return $this->_options;
    }
    /**
     * Get option text
     *
     * @param string|integer $value
     * 
     * @return string
     */
    public function getOptionText($value)
    {
        $options = $this->getAllOptions();
        foreach ($options as $option) {
            if ($option['value'] == $value) {
                return $option['label'];
            }
        }
        return false;
    }
    /**
     * Convert to options array
     *
     * @return array
     */
    public function toOptionArray()
    {
        return $this->getAllOptions();
    }
    /**
     * Get flat column definition
     *
     * @return array
     */
    public function getFlatColums()
    {
        $attributeCode      = $this->getAttribute()
            ->getAttributeCode();
        $column             = array(
            'unsigned'          => false, 
            'default'           => null, 
            'extra'             => null, 
        );
        if (Mage::helper('core')->useDbCompatibleMode()) {
            $column['type']         = 'varchar(3)';
            $column['is_null']      = true;
        } else {
            $column['type']         = Varien_Db_Ddl_Table::TYPE_VARCHAR;
            $column['length']       = '3';
            $column['nullable']     = true;
            $column['comment']      = $attributeCode . ' column';
        }
        return array($attributeCode => $column);
   }
   /**
     * Get flat update select
     *
     * @param int $store
     * 
     * @return Varien_Db_Select|null
     */
    public function getFlatUpdateSelect($store)
    {
        return Mage::getResourceModel('eav/entity_attribute_option')
            ->getFlatUpdateSelect($this->getAttribute(), $store, false);
    }
}
