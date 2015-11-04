<?php 
/**
 * Amasty_ShippingDate extension
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 * 
 * @category   	Amasty
 * @package		Amasty_ShippingDate
 * @copyright  	Copyright (c) 2014
 * @license		http://opensource.org/licenses/mit-license.php MIT License
 */
/**
 * ShippingDate default helper
 *
 * @category	Amasty
 * @package		Amasty_ShippingDate
 * @author Ultimate Module Creator
 */
class Amasty_ShippingDate_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function isEnable() {
        return Mage::getStoreConfig('amscheckout/shipping_date/enabled');
    }

    public function getShippingDateInfo() {
        return Mage::getStoreConfig('amscheckout/shipping_date/info');
    }
}