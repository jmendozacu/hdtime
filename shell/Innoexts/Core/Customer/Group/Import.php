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

require_once rtrim(dirname(__FILE__), '/').'/../../Import.php';

/**
 * Customer group import
 * 
 * @category   Innoexts
 * @package    Innoexts_Shell
 * @author     Innoexts Team <developers@innoexts.com>
 */
abstract class Innoexts_Shell_Core_Customer_Group_Import 
    extends Innoexts_Shell_Core_Import 
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
    /**
     * Get customer group id by row
     * 
     * @param array $row
     * 
     * @return int
     */
    protected function getCustomerGroupIdByRow($row)
    {
        if (!isset($row['customer_group'])) {
            return null;
        }
        $customerGroupId = $row['customer_group'];
        if (is_null($customerGroupId)) {
            return $customerGroupId;
        }
        $customerHelper     = $this->getCoreHelper()->getCustomerHelper();
        $customerGroup      = $customerHelper->getGroupById($customerGroupId);
        if ($customerGroup) {
            $customerGroupId    = (int) $customerGroup->getId();
        } else {
            $customerGroup      = $customerHelper->getGroupByCode($customerGroupId);
            if ($customerGroup) {
                $customerGroupId     = (int) $customerGroup->getId();
            } else {
                $customerGroupId     = null;
            }
        }
        return $customerGroupId;
    }
}
