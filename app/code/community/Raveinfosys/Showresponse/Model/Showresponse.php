<?php

class Raveinfosys_Showresponse_Model_Showresponse extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('showresponse/showresponse');
    }
	
	public function getRow()
	{
	  $collection = $this->getCollection()
					->setOrder('showresponse_id', 'DESC');
	  $row = $collection->getFirstItem()->getData();
	  return $row;
	}
}