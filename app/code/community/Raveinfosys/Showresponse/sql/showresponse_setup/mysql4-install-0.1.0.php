<?php

$installer = $this;

$installer->startSetup();

$installer->run("

-- DROP TABLE IF EXISTS {$this->getTable('showresponse')};    
	CREATE TABLE {$this->getTable('showresponse')} (
  `showresponse_id` int(11) NOT NULL auto_increment,
  `google_response` text,
  `yahoo_response` text, 
  `bing_response` text,
  `window_response` text,
  `date` timestamp,
  PRIMARY KEY  (`showresponse_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

$installer->endSetup(); 