<?php
/**
 * Created by PhpStorm.
 * User: Игорь
 * Date: 27.02.2015
 * Time: 19:19
 */ 
/* @var $installer Mage_Sales_Model_Mysql4_Setup */
$installer = $this;

$installer->startSetup();

$attribute  = array(
    'type'            => 'text',
    'backend_type'    => 'text',
    'frontend_input'  => 'text',
    'is_user_defined' => true,
    'label'           => 'Order comment',
    'visible'         => true,
    'required'        => false,
    'user_defined'    => false,
    'searchable'      => false,
    'filterable'      => false,
    'comparable'      => false,
    'default'         => ''
);

$installer->addAttribute('order', 'comment', $attribute);
$installer->addAttribute('quote', 'comment', $attribute);

$installer->endSetup();