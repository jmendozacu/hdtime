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

require_once rtrim(dirname(__FILE__), '/').'/../../Export.php';

/**
 * Customer group export
 * 
 * @category   Innoexts
 * @package    Innoexts_Shell
 * @author     Innoexts Team <developers@innoexts.com>
 */
abstract class Innoexts_Shell_Core_Customer_Group_Export 
    extends Innoexts_Shell_Core_Export 
{
    /**
     * Get model
     * 
     * @return Mage_Catalog_Model_Product
     */
    protected function getModel()
    {
        if (is_null($this->_model)) {
            $this->_model = $this->getCoreHelper()
                ->getCustomerHelper()
                ->getGroup();
        }
        return $this->_model;
    }
}
