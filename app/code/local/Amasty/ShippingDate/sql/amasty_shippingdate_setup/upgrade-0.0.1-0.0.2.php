<?php
$this->startSetup();
$installer = new Mage_Sales_Model_Mysql4_Setup('core_setup');
$installer->startSetup();
$installer->addAttribute("order", "delivery_date_from", array("type"=>"timestamp"));
$installer->addAttribute("quote", "delivery_date_from", array("type"=>"timestamp"));
$installer->addAttribute("order", "delivery_date_to", array("type"=>"timestamp"));
$installer->addAttribute("quote", "delivery_date_to", array("type"=>"timestamp"));
$installer->endSetup();
$this->endSetup();