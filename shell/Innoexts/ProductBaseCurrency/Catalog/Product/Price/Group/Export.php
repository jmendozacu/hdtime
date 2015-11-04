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
 * @package     Innoexts_Shell
 * @copyright   Copyright (c) 2014 Innoexts (http://www.innoexts.com)
 * @license     http://innoexts.com/commercial-license-agreement  InnoExts Commercial License
 */

require_once rtrim(dirname(__FILE__), '/').'/../../../../../Core/Catalog/Product/Price/Group/Export.php';

/**
 * Product group price export
 * 
 * @category   Innoexts
 * @package    Innoexts_Shell
 * @author     Innoexts Team <developers@innoexts.com>
 */
class Innoexts_Shell_ProductBaseCurrency_Catalog_Product_Price_Group_Export 
    extends Innoexts_Shell_Core_Catalog_Product_Price_Group_Export 
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
}

$shell = new Innoexts_Shell_ProductBaseCurrency_Catalog_Product_Price_Group_Export();
$shell->run();

/**
 * 

php shell/Innoexts/ProductBaseCurrency/Catalog/Product/Price/Group/Export.php \
    --file-path /var/export/ \
    --file-filename localfilename.csv

php shell/Innoexts/ProductBaseCurrency/Catalog/Product/Price/Group/Export.php \
    --file-path /var/export/Innoexts/ProductBaseCurrency/ \
    --file-filename product-group-price.csv

 **/