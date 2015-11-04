<?php
/***************************************************************************
	@extension	: Import Products categories, multiple images and custom options
	@copyright	: Copyright (c) 2014 Capacity Web Solutions.
	( http://www.capacitywebsolutions.com )
	@author		: Capacity Web Solutions Pvt. Ltd.
	@support	: magento@capacitywebsolutions.com
	
***************************************************************************/


class CapacityWebSolutions_ImportProduct_Model_Validator_Validate_Importvalidator
extends Mage_Core_Model_Abstract
{

	protected $_error=array();
	protected $_bypass_import=false;
	protected $_website_list=array();
	protected $_attribute_sets=array();
	
	public function __construct()
    {
		$this->initWebsites();
		$this->initAttributSets();
	}
	
	public function initWebsites(){	
		foreach (Mage::app()->getWebsites() as $website) {
			$this->_website_list[]=$website->getCode();
		}
	}
	
	public function initAttributSets(){
		$attributeSetCollection = Mage::getResourceModel('eav/entity_attribute_set_collection') ->load();
		foreach ($attributeSetCollection as $attribute) {
			$this->_attribute_sets[]= $attribute->getAttributeSetName();
		}	
	}


	public function runValidator(array $data)
	{
			$timestamp=Mage::app()->getRequest()->getParam('timestamp');
			
			$baseDir = Mage::getBaseDir();
			$flagDir = $baseDir.DS.'var'.DS.'import'.DS.'cws_product_import_flag_validator_do_not_delete-'.$timestamp.'.flag';
			if(file_exists($flagDir)){

			}else{
					$flag_file = fopen($flagDir, "w") or die(" 'var/import' directory does not exist !");
					$txt = "Do not delete this flag.";
					fwrite($flag_file, $txt);
					fclose($flag_file);
					Mage::getResourceModel('importproduct/validationlog')->truncate();											
			}
			
			$this->saveRow(unserialize($data['product_data']));

			foreach($this->_error as $e)
			{
				$model=Mage::getModel('importproduct/validationlog');
				$model->setErrorInformation($e['txt']);
				$model->setErrorType($e['error_level']);
				$model->setProductSku($e['product_sku']);
				$model->save();
			}		

			return array('error'=>$this->_error,'bypass'=>$this->_bypass_import);
		
	}
	
	public function saveRow(array $importData)
    {
		$row_cws_header=$importData['cws_row_header'];
		unset($importData['cws_row_header']);

		
		if(empty($importData['sku'])){
			$message = Mage::helper('importproduct')->__('Product SKU field in "%s" is empty', 'sku');
			$this->_bypass_import=true;
			array_push($this->_error,array('txt'=>$message,'product_sku'=>$product_sku_row_shower,'error_level'=>1));
			return;		
		}else{		
			$product_sku_row_shower='Product SKU: '.$importData['sku'];
		}

		$product = Mage::getModel('catalog/product')->loadByAttribute('sku',$importData['sku']);

		$behavior=Mage::app()->getRequest()->getParam('behavior');
				
		if($product) {
			if($behavior=='append'){
			}
		}else{
			if($behavior=='delete'){
			$this->_bypass_import=true;		
			$message = Mage::helper('importproduct')->__('Product SKU# :'.$importData['sku'].' does not exists.');
			array_push($this->_error,array('txt'=>$message,'product_sku'=>$product_sku_row_shower,'error_level'=>1));		
			return;						
			}
		}

		if (empty($importData['websites'])) {
			$message = Mage::helper('importproduct')->__('Required field "%s" not defined.', 'websites');
			array_push($this->_error,array('txt'=>$message,'product_sku'=>$product_sku_row_shower,'error_level'=>1));						
		}else{
		
			$websites=explode(",",$importData['websites']);
			
			foreach($websites as $website){
				if(!in_array($website,$this->_website_list)){
					$message = Mage::helper('importproduct')->__('Website code "%s" not exists.',$website);
					array_push($this->_error,array('txt'=>$message,'product_sku'=>$product_sku_row_shower,'error_level'=>1));										
				}
			}
		
		}
		
		if (empty($importData['store'])) {
			$message = Mage::helper('importproduct')->__('Required field "%s" not defined.', 'store');
			array_push($this->_error,array('txt'=>$message,'product_sku'=>$product_sku_row_shower,'error_level'=>1));						
		}else{
			$store_available=false;
			
			$store_ids = Mage::app()->getStores();
			foreach ($store_ids as $s_id)
			{
				if ($s_id->getCode()==$importData['store'])
				{
					$store_available = true;
				}else if($importData['store']=='admin')
				{
					$store_available = true;
				}
			}
			if(!$store_available)
			{
				$message = Mage::helper('importproduct')->__('Store Code "%s" doest not exist.', $importData['store']);
				array_push($this->_error,array('txt'=>$message,'product_sku'=>$product_sku_row_shower,'error_level'=>0));
			}				
		}

		if ($importData['category']==null && isset($importData['category'])) {		
			$message = Mage::helper('importproduct')->__('"%s" field having null value.', 'category');
			array_push($this->_error,array('txt'=>$message,'product_sku'=>$product_sku_row_shower,'error_level'=>0));									
		}

		if ($importData['visibility']==null && isset($importData['visibility'])) {		
			$message = Mage::helper('importproduct')->__('"%s" field having null value.', 'visibility');
			array_push($this->_error,array('txt'=>$message,'product_sku'=>$product_sku_row_shower,'error_level'=>0));									
		}

		if ($importData['tax_class_id']==null && isset($importData['tax_class_id'])) {	

			$no_skip=true;				
			if($product)
			{
				if($product->getTypeId()=='grouped')
				{
					$no_skip=false;
				}				
			}
			if($importData['type']=='grouped')
			{
					$no_skip=false;
			}
			if($no_skip){				
				$message = Mage::helper('importproduct')->__('"%s" field having null value.', 'tax_class_id');
				array_push($this->_error,array('txt'=>$message,'product_sku'=>$product_sku_row_shower,'error_level'=>0));									
			}
		}

		if ($importData['status']==null && isset($importData['status'])) {		
			$message = Mage::helper('importproduct')->__('"%s" field having null value.', 'status');
			array_push($this->_error,array('txt'=>$message,'product_sku'=>$product_sku_row_shower,'error_level'=>0));									
		}

		if ($importData['weight']==null && isset($importData['weight'])) {

			$no_skip=true;				
			if($product)
			{
				if($product->getTypeId()=='configurable' || $product->getTypeId()=='downloadable' || $product->getTypeId()=='grouped' || $product->getTypeId()=='virtual')
				{
					$no_skip=false;
				}				
			}
			if($importData['type']=='configurable' || $importData['type']=='downloadable' || $importData['type']=='grouped' || $importData['type']=='virtual')
			{
				$no_skip=false;
			}
			if($no_skip){	
				$message = Mage::helper('importproduct')->__('"%s" field having null value.', 'weight');
				array_push($this->_error,array('txt'=>$message,'product_sku'=>$product_sku_row_shower,'error_level'=>0));
			}			
		}

		if(isset($importData['price'])){
			if ($importData['price']==null) {		
				$no_skip=true;				
				if($product)
				{
					if($product->getTypeId()=='grouped')
					{
						$no_skip=false;
					}				
				}
				if($importData['type']=='grouped')
				{
					$no_skip=false;
				}
				
				if($no_skip){
					$message = Mage::helper('importproduct')->__('"%s" field having null value.', 'price');
					array_push($this->_error,array('txt'=>$message,'product_sku'=>$product_sku_row_shower,'error_level'=>0));									
				}
				
			}else{		
				if(!is_numeric($importData['price']))
				{
					$message = Mage::helper('importproduct')->__('"%s" can not have non-integer value.', 'price');
					$this->_bypass_import=true;
					array_push($this->_error,array('txt'=>$message,'product_sku'=>$product_sku_row_shower,'error_level'=>1));									
				}
			}		
		}
		if(isset($importData['attribute_set'])){
			if ($importData['attribute_set']==null) {		
				$message = Mage::helper('importproduct')->__('"%s" field having null value.', 'attribute_set');
				array_push($this->_error,array('txt'=>$message,'product_sku'=>$product_sku_row_shower,'error_level'=>0));									
			}else{			
				if(!in_array($importData['attribute_set'],$this->_attribute_sets))
				{
					$message = Mage::helper('importproduct')->__('Attribute Set : "%s" does not exists.', $importData['attribute_set']);
					array_push($this->_error,array('txt'=>$message,'product_sku'=>$product_sku_row_shower,'error_level'=>1));									
					$this->_bypass_import=true;			
				}			
			}		
		}

		if(isset($importData['categories_ids'])){			
			if ($importData['categories_ids']==null) {		
				$message = Mage::helper('importproduct')->__('"%s" field having null value.', 'categories_ids');
				array_push($this->_error,array('txt'=>$message,'product_sku'=>$product_sku_row_shower,'error_level'=>0));									
			}else{

				$category_list=explode(",",$importData['categories_ids']);
				foreach($category_list as $category){
					if(!is_numeric($category))
					{
						$message = Mage::helper('importproduct')->__('"%s" can not have non-integer value.', 'categories_ids');
						$this->_bypass_import=true;
						array_push($this->_error,array('txt'=>$message,'product_sku'=>$product_sku_row_shower,'error_level'=>1));									
					}
				}
			}
		
		}

		
		
		if (isset($importData['image']) && $importData['image']!=null) {
			
			$image_path=Mage::getBaseDir('media')."/import".$importData['image'];
			if(!file_exists($image_path)){			
				$message = Mage::helper('importproduct')->__(' Image : "%s" does not exist media/import folder.', $importData['image']);
				array_push($this->_error,array('txt'=>$message,'product_sku'=>$product_sku_row_shower,'error_level'=>1));
			}				
		}

		if (isset($importData['small_image']) && $importData['small_image']!=null) {
		
			$small_image_path=Mage::getBaseDir('media')."/import".$importData['small_image'];
			if(!file_exists($small_image_path)){			
				$message = Mage::helper('importproduct')->__(' Image : "%s" does not exists in media/import folder.', $importData['small_image']);
				array_push($this->_error,array('txt'=>$message,'product_sku'=>$product_sku_row_shower,'error_level'=>1));
			}			
		}

		if (isset($importData['thumbnail']) && $importData['thumbnail']!=null) {
		
			$thumbnail_path=Mage::getBaseDir('media')."/import".$importData['thumbnail'];
			if(!file_exists($thumbnail_path)){			
				$message = Mage::helper('importproduct')->__(' Image : "%s" does not exists in media/import folder.', $importData['thumbnail']);
				array_push($this->_error,array('txt'=>$message,'product_sku'=>$product_sku_row_shower,'error_level'=>1));
			}					
		}		
		
		if (isset($importData['gallery']) && $importData['gallery']!=null) {

			$galler_images=explode("|",$importData['gallery']);
			foreach($galler_images as $galler_image)
			{
				$galler_image_path=Mage::getBaseDir('media')."/import".$galler_image;
				if(!file_exists($galler_image_path)){				
					$message = Mage::helper('importproduct')->__(' Image : "%s" does not exists in media/import folder.', $galler_image);
					array_push($this->_error,array('txt'=>$message,'product_sku'=>$product_sku_row_shower,'error_level'=>1));
				}
			}
		}		
		

		

		if($importData['related_product_sku']!=null)
		{
			$related_product_sku = $importData['related_product_sku'];
			$related_product_sku_single = explode(',', $related_product_sku);
			$r_data = array();
			
			foreach($related_product_sku_single as $r_sku)
			{
				$aRelatedProduct = Mage::getModel('catalog/product')->loadByAttribute('sku', $r_sku);
				if(!isset($aRelatedProduct['entity_id'])){
					$message="Product SKU:".$r_sku." does not exists. Can't assign to related product.";
					array_push($this->_error,array('txt'=>$message,'product_sku'=>$importData['sku']));				  					
				}	
				
			}
		}

		if($importData['crosssell_product_sku']!=null)
		{
			$crosssell_product_sku = $importData['crosssell_product_sku'];
			$crosssell_product_sku_single = explode(',', $crosssell_product_sku);
			$c_data = array();

			foreach($crosssell_product_sku_single as $c_sku)
			{
				$aCrossesellProduct = Mage::getModel('catalog/product')->loadByAttribute('sku', $c_sku);

				if(!isset($aCrossesellProduct['entity_id'])){				
					$message="Product SKU:".$c_sku." does not exists. Can't assign to cross-sell product.";
					array_push($this->_error,array('txt'=>$message,'product_sku'=>$importData['sku']));				  										
				}				

			}
		}		

		if($importData['upsell_product_sku']!=null)
		{
			$upsell_product_sku = $importData['upsell_product_sku'];
			$upsell_product_sku_single = explode(',', $upsell_product_sku);
			$u_data = array();
			$z = 1;

			foreach($upsell_product_sku_single as $u_sku)
			{
				$aUpesellProduct = Mage::getModel('catalog/product')->loadByAttribute('sku', $u_sku);
				if(!isset($aUpesellProduct['entity_id'])){
					$message="Product SKU:".$u_sku." does not exists. Can't assign to up-sell product.";
					array_push($this->_error,array('txt'=>$message,'product_sku'=>$importData['sku']));				  															
				}
				
			}
		}			

/* 		if($importData['grouped_product_sku']!=null)
		{		
			$simple_product_id_data = explode(",",$importData['grouped_product_sku']);
			foreach($simple_product_id_data as $groupedProd){                
				$product_id = Mage::getModel("catalog/product")->getIdBySku($groupedProd);
				if(!$product_id){
					$message="Product does not exists SKU: ".$groupedProd;
					Mage::log($message,null,'cws_import_product_log.txt');										
					array_push($this->_error,array('txt'=>$message,'product_sku'=>$importData['sku'],'error_level'=>1));	
				}
			}		
		} */
		
		
/* 		if($importData['child_products_sku']!=null)
		{		
			$childConfigurePro=explode(",", $importData['child_products_sku']);			
			foreach($childConfigurePro as $childc){
				$childProduct = Mage::getModel('catalog/product')->loadByAttribute('sku', $childc);
				if(!$childProduct){
				   $message='Product doest not exists. SKU: '.$childc;
				   array_push($this->_error,array('txt'=>$message,'product_sku'=>$importData['sku'],'error_level'=>0));
				   Mage::log($message,null,'cws_import_product_log.txt');				
				}
			}	
		} */
	
		if($importData['used_attribute']!=null)
		{		
			$used_attribute=explode(",", $importData['used_attribute']);
			foreach($used_attribute as $attrCode){
				$super_attribute= Mage::getModel('eav/entity_attribute')->loadByCode('catalog_product',$attrCode);
				if($super_attribute){
					if($super_attribute->getId()===null){
					   $message='Attribute with code : '.$attrCode.' is not configurable attribute.';
					   array_push($this->_error,array('txt'=>$message,'product_sku'=>$importData['sku'],'error_level'=>1));
					}					
				}else{
				   $message='Attribute Code : '.$attrCode.' doest not exists. ';
				   array_push($this->_error,array('txt'=>$message,'product_sku'=>$importData['sku'],'error_level'=>1));
				}
			}
			
		}	
/* 
		if($importData['bundle_product_options']!=null)
		{		
			$option_str=$importData['bundle_product_options'];						
			$single_bundle_option=explode("|",$option_str);			
			for($z=0;$z<count($single_bundle_option);$z++){
	
				$single_bundle_option_data=explode(":",$single_bundle_option[$z]);			
				$single_bundle_option_selection_value=explode("!",$single_bundle_option_data[1]);			
				
				foreach($single_bundle_option_selection_value as $singleBundleOptionData){					
					$d=explode(',',$singleBundleOptionData);				
					$product_id = Mage::getModel("catalog/product")->getIdBySku($d[0]);				
					
					if(!$product_id){
						$message='Product does not exist. SKU :'.$d[0];
						array_push($this->_error,array('txt'=>$message,'product_sku'=>$importData['sku'],'error_level'=>1));
						Mage::log($message,null,'cws_import_product_log.txt');									
					}
				}	
			}		
		}		 */
		
		if($importData['downloadable_product_options']!=null)
		{		
			$downloadable_product_main_data = explode('|',$importData['downloadable_product_options']);
			foreach ($downloadable_product_main_data as $single) {
							$single_row=explode(";",$single);
							$linkdata=$single_row[0];	
							$linkdata=explode(",",$linkdata);		
							$sampledata=$single_row[1];		
							$sampledata=explode(",",$sampledata);															
							$linkimagename=$linkdata[5];	
							$sampleimagename=$sampledata[1];

							if($linkdata[4]=='file' || $linkdata[4]==''){
								if (!file_exists(Mage::getBaseDir('media').DS. 'import'.$linkimagename)) {								
								   $message='File('.$linkimagename.') Does Not Exist. SKU: '.$importData['sku'];
								   array_push($this->_error,array('txt'=>$message,'product_sku'=>$importData['sku'],'error_level'=>1));
								}	
							}
							if($sampledata[0]=='file'){
								if (!file_exists(Mage::getBaseDir('media').DS. 'import'.$sampleimagename)) {
								   $message='Sample File('.$sampleimagename.') Does Not Exist. SKU: '.$importData['sku'];									   									  
								   array_push($this->_error,array('txt'=>$message,'product_sku'=>$importData['sku'],'error_level'=>1));	
								}								
							}		
			}
		}
	}
}
	
	