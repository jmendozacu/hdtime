<?php
/***************************************************************************
	@extension	: Import Products categories, multiple images and custom options
	@copyright	: Copyright (c) 2014 Capacity Web Solutions.
	( http://www.capacitywebsolutions.com )
	@author		: Capacity Web Solutions Pvt. Ltd.
	@support	: magento@capacitywebsolutions.com
	
***************************************************************************/


class CapacityWebSolutions_ImportProduct_Block_Adminhtml_Importproducts_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
	
        parent::__construct();
		
        $this->_blockGroup = 'importproduct';
        $this->_controller = 'adminhtml_Importproducts';
        
        $this->_removeButton('delete');
        $this->_removeButton('reset');
        $this->_removeButton('back');
        $this->_removeButton('save');
		
    }

    public function getHeaderText()
    {
        return Mage::helper('importproduct')->__('Import Products');
    }
}