<?php
/***************************************************************************
	@extension	: Import Products categories, multiple images and custom options
	@copyright	: Copyright (c) 2014 Capacity Web Solutions.
	( http://www.capacitywebsolutions.com )
	@author		: Capacity Web Solutions Pvt. Ltd.
	@support	: magento@capacitywebsolutions.com
	
***************************************************************************/


class CapacityWebSolutions_ImportProduct_Model_Validator_Csv extends  Mage_Dataflow_Model_Convert_Parser_Abstract
{
    protected $_fields;

    protected $_mapfields = array();

	public function associateDataWithHeader($header,$data){
		
		if(count($header)==count($data))
		{
			$master_data=array();
			$i=0;
			foreach($data as $d)
			{
				$master_data[$header[$i]]=$d;
				$i++;
			}
			return $master_data;			
		}
	
	}
	
	
    public function parse()
    {
        // fixed for multibyte characters
        setlocale(LC_ALL, Mage::app()->getLocale()->getLocaleCode().'.UTF-8');
		
		$file = Mage::app()->getConfig()->getTempVarDir().'/import/'
                . urldecode(Mage::app()->getRequest()->getParam('files'));
		
		$handle = fopen($file,'r')  or die(" 'var/import' directory does not exist !");		
		$pointer='';
		$pointer_req=Mage::app()->getRequest()->getParam('pointer');

		
		$master_data=array();
		$header=true;
		$header_data=array();
		$row=0;
		while ( ($data = fgetcsv($handle) ) !== FALSE ) {
			if($header){
				$header=false;
				$header_data=$data;
				if($pointer_req!=''){
					fseek($handle,$pointer_req);				
				}
				continue;		
			}else{
				if(array_filter($data)) {
					$t_master_data=$this->associateDataWithHeader($header_data,$data);				
					$t_master_data['cws_row_header']=$row;
					array_push($master_data,$t_master_data);				
				}
			}
			if($row==200)
			{
				$pointer=ftell($handle);
				break;
			}

			$row++;
		}

		$url='';
		if($pointer!=''){
		$url=Mage::getUrl('*/adminhtml_Importproducts/validate',array('pointer'=>$pointer,'files'=>Mage::app()->getRequest()->getParam('files'),'importfiletype'=>Mage::app()->getRequest()->getParam('importfiletype'),'validationBehavior'=>Mage::app()->getRequest()->getParam('validationBehavior')));
		}
		
		return array("data"=>$master_data,'url'=>$url);

    }
	public function unparse()
	{

	}

}
