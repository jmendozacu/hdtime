<?php
/***************************************************************************
	@extension	: Import Products categories, multiple images and custom options
	@copyright	: Copyright (c) 2014 Capacity Web Solutions.
	( http://www.capacitywebsolutions.com )
	@author		: Capacity Web Solutions Pvt. Ltd.
	@support	: magento@capacitywebsolutions.com
	
***************************************************************************/

class CapacityWebSolutions_ImportProduct_Block_Adminhtml_Importproducts_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    public function __construct()
    {
        parent::__construct();

        $this->setId('import_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('importproduct')->__('Import/Export Products'));
		
    }

    protected function _beforeToHtml()
    {
        $this->addTab('upload_file', array(
            'label'   => Mage::helper('importproduct')->__('Upload File'),
            'title'   => Mage::helper('importproduct')->__('Upload File'),
            'content' => $this->getLayout()->createBlock('importproduct/adminhtml_importproducts_edit_tab_uploadfile')->toHtml(), 
            ));

        $this->addTab('run_profile', array(
            'label'   => Mage::helper('importproduct')->__('Run Profile'),
            'title'   => Mage::helper('importproduct')->__('Run Profile'),
            'content' => $this->getLayout()->createBlock('importproduct/adminhtml_importproducts_edit_tab_runprofile')->toHtml(), 
            ));			

			$direction=$this->getRequest()->getParam('direction');
			if($direction=='Imported')
			{
				$filter='1';
			}else{
				$filter='0';
			}
			$collection = Mage::getModel('importproduct/profiler')->getCollection()->addFieldToFilter('validate',$filter);
			
			switch($direction)
			{
				case 'Imported':
						
						$this->addTab('validationlog', array(
								'label'   => Mage::helper('importproduct')->__('Validation Log'),
								'title'   => Mage::helper('importproduct')->__('Validation Log'),
								'content' => $this->getLayout()->createBlock('importproduct/adminhtml_importproducts_edit_tab_validationlog')->toHtml(), 
								));
								
						if(count($collection)==0){

							$this->addTab('importlog', array(
								'label'   => Mage::helper('importproduct')->__('Import Log'),
								'title'   => Mage::helper('importproduct')->__('Import Log'),
								'content' => $this->getLayout()->createBlock('importproduct/adminhtml_importproducts_edit_tab_importlog')->toHtml(), 
								));			

						}else{
							$this->addTab('importlog', array(
								'label'   => Mage::helper('importproduct')->__('Import Log'),
								'title'   => Mage::helper('importproduct')->__('Import Log'),
								'content' => $this->getLayout()->createBlock('importproduct/system_convert_runvalidation')->toHtml(), 
								));						
						
						}
				break;
				
				case 'Validated':

						if(count($collection)==0){

							$this->addTab('validationlog', array(
								'label'   => Mage::helper('importproduct')->__('Validation Log'),
								'title'   => Mage::helper('importproduct')->__('Validation Log'),
								'content' => $this->getLayout()->createBlock('importproduct/adminhtml_importproducts_edit_tab_validationlog')->toHtml(), 
								));			

						}else{
							$this->addTab('validationlog', array(
								'label'   => Mage::helper('importproduct')->__('Validation Log'),
								'title'   => Mage::helper('importproduct')->__('Validation Log'),
								'content' => $this->getLayout()->createBlock('importproduct/system_convert_runvalidation')->toHtml(), 
								));						
						
						}
						
						$this->addTab('importlog', array(
								'label'   => Mage::helper('importproduct')->__('Import Log'),
								'title'   => Mage::helper('importproduct')->__('Import Log'),
								'content' => $this->getLayout()->createBlock('importproduct/adminhtml_importproducts_edit_tab_importlog')->toHtml(), 
								));								
				
				break;	

				default:
						$this->addTab('validationlog', array(
								'label'   => Mage::helper('importproduct')->__('Validation Log'),
								'title'   => Mage::helper('importproduct')->__('Validation Log'),
								'content' => $this->getLayout()->createBlock('importproduct/adminhtml_importproducts_edit_tab_validationlog')->toHtml(), 
								));										
						$this->addTab('importlog', array(
								'label'   => Mage::helper('importproduct')->__('Import Log'),
								'title'   => Mage::helper('importproduct')->__('Import Log'),
								'content' => $this->getLayout()->createBlock('importproduct/adminhtml_importproducts_edit_tab_importlog')->toHtml(), 
								));			
			}
			
			$this->addTab('samplecsv', array(
								'label'   => Mage::helper('importproduct')->__('Sample CSV'),
								'title'   => Mage::helper('importproduct')->__('Sample CSV'),
								'content' => $this->getLayout()->createBlock('importproduct/adminhtml_importproducts_edit_tab_samplecsv')->toHtml(), 
			));	
			
        return parent::_beforeToHtml();
    }    
	
	
	
}