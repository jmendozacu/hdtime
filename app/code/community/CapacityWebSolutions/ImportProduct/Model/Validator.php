<?php
/***************************************************************************
	@extension	: Import Products categories, multiple images and custom options
	@copyright	: Copyright (c) 2014 Capacity Web Solutions.
	( http://www.capacitywebsolutions.com )
	@author		: Capacity Web Solutions Pvt. Ltd.
	@support	: magento@capacitywebsolutions.com
	
***************************************************************************/

class CapacityWebSolutions_ImportProduct_Model_Validator extends Mage_Core_Model_Abstract
{

  protected $_validation_status=false;

  protected function _construct()
  {
		parent::_construct();
	    $this->_init('importproduct/validator');	
  }
  
  public function getConverter()
  {
		$importfiletype=Mage::app()->getRequest()->getParam('importfiletype');
		$this->setValidationStatus();
		
		switch($importfiletype){		
			default:
				return Mage::getModel("importproduct/validator_csv")->parse();
				break;
		}	
  }
  
  public function setValidationStatus(){
  		$validate=Mage::app()->getRequest()->getParam('validationBehavior');
		if($validate=='skip')
		{
			$this->_validation_status=true;
		}
  }
  
  public function setProfilerData(){
	
		$data=$this->getConverter();
		$row=array();
		foreach($data['data'] as $d)
		{
				$temp_row=array();
				$temp_row['product_data']=serialize($d);
				$temp_row['validate']=$this->_validation_status;
				$row[]=$temp_row;				
											
				if(count($row)>5 || $d === end($data['data']))
				{
					try{
						Mage::getResourceModel('importproduct/profiler')->insertMultipleProduct($row);
					}catch(Exception $e){
						echo $e->getMessage(); exit;
					}					
					$row=array();
				}
		}
		
		//exit;		
		return $data['url'];
  }
  
}
