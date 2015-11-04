<?php

class Raveinfosys_Showmap_Model_Mysql4_Config extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the showmap_id refers to the key field in your database table.
        $this->_init('showmap/config', 'id');
    }
}