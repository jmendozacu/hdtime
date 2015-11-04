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

$installer                                      = $this;

$connection                                     = $installer->getConnection();

$helper                                         = Mage::helper('productbasecurrency');
$versionHelper                                  = $helper->getVersionHelper();
$databaseHelper                                 = $helper->getCoreHelper()->getDatabaseHelper();

$installer->startSetup();

$eavAttributeTable                              = $installer->getTable('eav/attribute');
$eavEntityTypeTable                             = $installer->getTable('eav/entity_type');

$installer->addAttribute(
    'catalog_product', 
    'base_currency', 
    array(
        'attribute_model'               => null, 
        'backend'                       => 'catalog/product_attribute_backend_basecurrency', 
        'type'                          => 'varchar', 
        'table'                         => null, 
        'frontend'                      => null, 
        'input'                         => 'select', 
        'label'                         => 'Base Currency', 
        'frontend_class'                => null, 
        'source'                        => 'catalog/product_attribute_source_basecurrency', 
        'required'                      => false, 
        'user_defined'                  => false, 
        'default'                       => null, 
        'unique'                        => false, 
        'note'                          => null, 
        'input_renderer'                => null, 
        'global'                        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE, 
        'visible'                       => true, 
        'searchable'                    => false, 
        'filterable'                    => false, 
        'comparable'                    => false, 
        'visible_on_front'              => false, 
        'is_html_allowed_on_front'      => false, 
        'is_used_for_price_rules'       => true, 
        'filterable_in_search'          => false, 
        'used_in_product_listing'       => true, 
        'used_for_sort_by'              => false, 
        'is_configurable'               => true, 
        'apply_to'                      => 'simple,configurable,virtual,bundle,downloadable', 
        'visible_in_advanced_search'    => false, 
        'position'                      => 1, 
        'wysiwyg_enabled'               => false, 
        'used_for_promo_rules'          => true, 
        'group'                         => 'Prices', 
    )
);

/**
 * EAV Attribute
 */
$installer->run("UPDATE `{$eavAttributeTable}` 
SET `backend_model` = 'catalog/product_attribute_backend_finishdate' 
WHERE (`attribute_code` = 'special_to_date') AND (`entity_type_id` = (
    SELECT `entity_type_id` FROM `{$eavEntityTypeTable}` WHERE `entity_type_code` = 'catalog_product'
))");

$installer->endSetup();
