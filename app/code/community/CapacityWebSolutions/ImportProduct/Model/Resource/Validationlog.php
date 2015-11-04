<?php

/***************************************************************************
	@extension	: Import Products categories, multiple images and custom options
	@copyright	: Copyright (c) 2014 Capacity Web Solutions.
	( http://www.capacitywebsolutions.com )
	@author		: Capacity Web Solutions Pvt. Ltd.
	@support	: magento@capacitywebsolutions.com
	
***************************************************************************/

class CapacityWebSolutions_ImportProduct_Model_Resource_Validationlog extends Mage_Core_Model_Mysql4_Abstract{
    protected function _construct()
    {
        $this->_init('importproduct/validationlog', 'log_id');
    }
	public function truncate() {
		$this->_getWriteAdapter()->query('TRUNCATE TABLE '.$this->getMainTable());
		return $this;
	}	
}