<?php
/***************************************************************************
	@extension	: Import Products categories, multiple images and custom options
	@copyright	: Copyright (c) 2014 Capacity Web Solutions.
	( http://www.capacitywebsolutions.com )
	@author		: Capacity Web Solutions Pvt. Ltd.
	@support	: magento@capacitywebsolutions.com
	
***************************************************************************/

class CapacityWebSolutions_ImportProduct_Model_Combiner_Csv extends Mage_Core_Model_Abstract
{

	protected $_proceed_next=true;

	public function process()
	{
		$path=Mage::app()->getConfig()->getTempVarDir().'/export/';
		$timestamp=Mage::app()->getRequest()->getParam('timestamp');
		$filename=Mage::app()->getRequest()->getParam('filename');
		$page_ctn=Mage::app()->getRequest()->getParam('page');
		$currentPage=Mage::app()->getRequest()->getParam('processPage');

		$cws_csv_header=$path.'cws_csv_header-'.$timestamp.'.obj';
		$header_string_obj=file_get_contents($cws_csv_header);
		$header_template=unserialize($header_string_obj);

		
		$tempFile = fopen($path.$filename."-tmp-".$currentPage,"r") or die(" 'var/export' directory does not exist !");
		$tmp_data=array();
		$temp_header=array();
		$_temp_first=true;
		while(!feof($tempFile))
		{
		  
		  if($_temp_first)
		  {
			$temp_header=fgetcsv($tempFile);
			$_temp_first=false;
		  }else{
		  
			$temp_single_record=array();	
			foreach(fgetcsv($tempFile) as $key=>$value){
				$temp_single_record[$temp_header[$key]]=$value;	
			}
			
			if (array_filter($temp_single_record)) {
				$tmp_data[]=$temp_single_record;
			}
			
		  }
		}
		fclose($tempFile);
		unlink($path.$filename."-tmp-".$currentPage);
		//Mage::log($tmp_data,null,'tmp.data.txt');
		
		if($currentPage=='1'){
			$fp = fopen($path.$filename, 'w') or die(" 'var/export' directory does not exist !");
			fputcsv($fp, array_values($header_template));
		}else{
			$fp = fopen($path.$filename, 'a') or die(" 'var/export' directory does not exist !");			
		}

		foreach ($tmp_data as $product) {
			$o_data=array_fill_keys(array_values($header_template), '');
			
			foreach($product as $o_key=>$o_val)
			{
				if (in_array($o_key, $header_template)) {
					$o_data[$o_key]=$o_val;
				}
			}
			fputcsv($fp, array_values($o_data));
			
		}					
		
		fclose($fp);
		
		if($currentPage==$page_ctn){
			$this->_proceed_next=false;
			unlink($cws_csv_header);
			
		}
	
		$currentPage++;
		
		return array('proceed_next'=>$this->_proceed_next,'timestamp'=>$timestamp,'filename'=>$filename,'page'=>$page_ctn,'processPage'=>$currentPage);
	}
    
}
