<?php

class Raveinfosys_Showresponse_Model_Mysql4_Showresponse_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('showresponse/showresponse');
    }
}