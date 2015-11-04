<?php

class Raveinfosys_Showresponse_Model_Mysql4_Showresponse extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the showresponse_id refers to the key field in your database table.
        $this->_init('showresponse/showresponse', 'showresponse_id');
    }
}