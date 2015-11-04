<?php
/***************************************************************************
	@extension	: Import Products categories, multiple images and custom options
	@copyright	: Copyright (c) 2014 Capacity Web Solutions.
	( http://www.capacitywebsolutions.com )
	@author		: Capacity Web Solutions Pvt. Ltd.
	@support	: magento@capacitywebsolutions.com
	
***************************************************************************/
 

class CapacityWebSolutions_ImportProduct_Block_System_Convert_Runvalidation extends Mage_Adminhtml_Block_Widget_Form
{
  

    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('importproduct/validateprocess.phtml');
    }

    public function getFormKey()
    {
        return Mage::getSingleton('core/session')->getFormKey();
    }
   
   
  
	
}
?>
