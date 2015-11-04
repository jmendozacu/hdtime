<?php
/***************************************************************************
	@extension	: Import Products categories, multiple images and custom options
	@copyright	: Copyright (c) 2014 Capacity Web Solutions.
	( http://www.capacitywebsolutions.com )
	@author		: Capacity Web Solutions Pvt. Ltd.
	@support	: magento@capacitywebsolutions.com
	
***************************************************************************/


class  CapacityWebSolutions_ImportProduct_Adminhtml_LicenseManagerController extends Mage_Adminhtml_Controller_Action
{
	protected function _initAction() 
	{
	$this->loadLayout()
			->_setActiveMenu('cws')
			->_addBreadcrumb(Mage::helper('adminhtml')->__('Items Manager'), Mage::helper('adminhtml')->__('Item Manager'));
		
		return $this;
		
		
	} 
	public function indexAction() {			
		$this->_initAction()->renderLayout();
	}
		
}
?>