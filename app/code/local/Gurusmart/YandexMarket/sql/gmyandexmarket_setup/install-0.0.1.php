<?php

$installer = $this;

$installer->startSetup();

$installer->addAttribute(
    'catalog_product',
    'xml_export',
    array(
        'group' => 'General',
        'backend'       => '',
        'frontend'      => '',
        'class' => '',
        'default'       => '',
        'label' => 'Yandex Market',
        'type'  => 'int',
        'input' => 'boolean',
        'source'        => '',
        'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
        'visible'       => 1,
        'required'      => 0,
        'searchable'    => 0,
        'filterable'    => 1,
        'unique'        => 0,
        'comparable'    => 0,
        'visible_on_front' => 0,
        'is_html_allowed_on_front' => 0,
        'user_defined'  => 1,
    )
);

$installer->endSetup();