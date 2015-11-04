<?php

/***************************************************************************
	@extension	: Import/Export Orders.
	@copyright	: Copyright (c) 2014 Capacity Web Solutions.
	( http://www.capacitywebsolutions.com )
	@author		: Capacity Web Solutions Pvt. Ltd.
	@support	: magento@capacitywebsolutions.com	
***************************************************************************/

class CapacityWebSolutions_ImportProduct_Model_Resource_Exportedfile_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract {
    protected function _construct()
    {
        $this->_init('importproduct/exportedfile');
    }
} 