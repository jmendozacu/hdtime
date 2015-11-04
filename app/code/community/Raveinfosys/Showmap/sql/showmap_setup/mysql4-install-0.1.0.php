<?php

$installer = $this;

$installer->startSetup();
$setup = new Mage_Eav_Model_Entity_Setup('core_setup');

$installer->run("

-- DROP TABLE IF EXISTS {$this->getTable('showmap')};    
	CREATE TABLE {$this->getTable('showmap')} (
  `showmap_id` int(11) NOT NULL auto_increment,  
   `ping_interval` int(12),
   `format` varchar(20),
   `google` varchar(20) NOT NULL default 'no',
   `bing` varchar(20) NOT NULL default 'no',
   `window` varchar(20) NOT NULL default 'no',
   `yahoo` varchar(20) NOT NULL default 'no',
   PRIMARY KEY  (`showmap_id`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8;



-- DROP TABLE IF EXISTS {$this->getTable('showmap_config')};    
	CREATE TABLE {$this->getTable('showmap_config')} (
  `id` int(11) NOT NULL auto_increment,
  `product` varchar(20), 
  `category` varchar(20),
  `cms` varchar(20),
  `other` varchar(20),
  `configured` int(2) NOT NULL DEFAULT 1,
   PRIMARY KEY  (`id`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

$installer->endSetup(); 