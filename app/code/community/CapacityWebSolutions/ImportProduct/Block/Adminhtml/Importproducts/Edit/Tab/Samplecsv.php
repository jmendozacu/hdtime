<?php
/***************************************************************************
	@extension	: Import Products categories, multiple images and custom options
	@copyright	: Copyright (c) 2014 Capacity Web Solutions.
	( http://www.capacitywebsolutions.com )
	@author		: Capacity Web Solutions Pvt. Ltd.
	@support	: magento@capacitywebsolutions.com
	
***************************************************************************/

class CapacityWebSolutions_ImportProduct_Block_Adminhtml_Importproducts_Edit_Tab_Samplecsv extends Mage_Adminhtml_Block_Widget_Form
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('importproduct/samplecsv.phtml');
    }
   	
}