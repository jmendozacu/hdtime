<?php
/**
* @author Amasty Team
* @copyright Amasty
* @package Amasty_Scheckout
*/
class Amasty_Scheckout_Model_Mysql4_Config extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        $this->_init('amscheckout/config', 'config_id');
    }
}