<?php
$installer = $this;

$installer->startSetup();

$installer->run("
CREATE TABLE IF NOT EXISTS `".$this->getTable('cws_product_import_log')."` (
  `log_id` int(11) NOT NULL AUTO_INCREMENT,
  `error_type` int(11) NOT NULL,
  `product_sku` varchar(100) CHARACTER SET utf8 NOT NULL,
  `error_information` text CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`log_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `".$this->getTable('cws_product_import_profiler')."` (
  `profiler_id` int(20) NOT NULL AUTO_INCREMENT,
  `bypass_import` tinyint(1) NOT NULL,
  `validate` tinyint(1) NOT NULL,
  `imported` tinyint(1) NOT NULL,
  `product_data` longtext CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`profiler_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `".$this->getTable('cws_product_validation_log')."` (
  `log_id` int(11) NOT NULL AUTO_INCREMENT,
  `error_type` int(11) NOT NULL,
  `product_sku` varchar(100) CHARACTER SET utf8 NOT NULL,
  `error_information` text CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`log_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `".$this->getTable('cws_product_exported_file')."` (
  `export_id` int(11) NOT NULL AUTO_INCREMENT,
  `file_name` text CHARACTER SET utf8 NOT NULL,
  `exported_file_date_time` datetime NOT NULL,
  PRIMARY KEY (`export_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

");

$installer->endSetup();