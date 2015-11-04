<?php
/***************************************************************************
	@extension	: Import Products categories, multiple images and custom options
	@copyright	: Copyright (c) 2014 Capacity Web Solutions.
	( http://www.capacitywebsolutions.com )
	@author		: Capacity Web Solutions Pvt. Ltd.
	@support	: magento@capacitywebsolutions.com
	
***************************************************************************/


class CapacityWebSolutions_ImportProduct_Adminhtml_RunProfileController extends Mage_Adminhtml_Controller_Action
{
	
  

    public function runAction()
    {
		  Mage::getSingleton("core/session")->setImportFileType($this->getRequest()->getParam('importfiletype'));
		  Mage::getSingleton("core/session")->setImportFileNaming($this->getRequest()->getParam('files'));
			$this->_initProfile();
			$this->loadLayout();
			$this->renderLayout();
    }
	
	public function exportrunAction()
    {
		$this->createExportFlagIfNoExist();
		$export_file_name=Mage::getModel("importproduct/convert_parser_importproduct")->getProductExportFile();
		if($export_file_name['proceed_next']==false){

			$exportedfile = Mage::getModel('importproduct/exportedfile'); 
			$exportedfile->setFileName($export_file_name['filename']);
			$exportedfile->setExportedFileDateTime(Mage::getModel('core/date')->timestamp(time()));
			$exportedfile->save();		
		
			$download_path=Mage::helper("adminhtml")->getUrl("importproduct/adminhtml_Exportproducts/downloadExportedFile/",array("file"=>$export_file_name['filename']));		
			Mage::getSingleton("core/session")->addSuccess("Exported File : <b style='font-size:12px'><a target='_blank' href='".$download_path."' /'>".$export_file_name['filename'].'</a></b>'); 		
			$this->deleteExportFlag();
		}
 		$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($export_file_name));

    }
	
	public function exportRecordCountAction(){
	
		$data=array();
		$can_proceed=false;
		$count=0;
		
        $product = Mage::getResourceModel('catalog/product_collection');
		$store_export_id=$this->getRequest()->getParam('store_id');
		if($store_export_id!='*'){
			$product->addStoreFilter($store_export_id);
		}
		$count=$product->getSize();
		if($count>0){
			$can_proceed=true;
			$current_time=$this->createExportFlagIfNoExist();
			$data['timestamp']=$current_time;
		}
		
		if($count>200)
		{
			$data['splitExport']=true;
		}else{
			$data['splitExport']=false;
		}
		$data['export_can_proceed']=$can_proceed;
		$data['totalOrder']=$count;

		$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($data));

	}

	public function deleteExportFlag(){
		$baseDir = Mage::getBaseDir();
		$current_time=Mage::app()->getRequest()->getParam('timestamp');		
		$flagDir = $baseDir.DS.'var'.DS.'export'.DS.'cws_product_export_flag_do_not_delete-'.$current_time.'.flag';
		if(file_exists($flagDir)){
				unlink($flagDir);
		}		
	}
	
	public function initExportBasicParam(){
        foreach (Mage::getConfig()->getFieldset('catalog_product_dataflow', 'admin') as $code=>$node) {
            if ($node->is('inventory')) {
                $_inventoryFields[] = $code;
                if ($node->is('use_config')) {
                    $_inventoryFields[] = 'use_config_'.$code;
                }
            }
            if ($node->is('internal')) {
                $_internalFields[] = $code;
            }
            if ($node->is('system')) {
                $_systemFields[] = $code;
            }
            if ($node->is('external')) {
                $externalFields[$code] = $code;
            }
            if ($node->is('img')) {
                $_imageFields[] = $code;
            }
        }

		$_init_export_param['_inventoryFields']=$_inventoryFields;
		$_init_export_param['_internalFields']=$_internalFields;
		$_init_export_param['_systemFields']=$_systemFields;
		$_init_export_param['_externalFields']=$_externalFields;
		$_init_export_param['_imageFields']=$_imageFields;
		//print_R($_init_export_param);exit;
		Mage::register('load_export_basic_param', $_init_export_param);		
	}
	
	public function createExportFlagIfNoExist()
	{
		$baseDir = Mage::getBaseDir();
		$current_time=Mage::app()->getRequest()->getParam('timestamp');
		if($current_time===NULL)
		{
			$current_time=time();
		}
		$flagDir = $baseDir.DS.'var'.DS.'export'.DS.'cws_product_export_flag_do_not_delete-'.$current_time.'.flag';
		if(file_exists($flagDir)){
			
			$flag_data = file_get_contents($flagDir);
			$load_product_related_info=unserialize($flag_data); 
			//print_r($load_product_related_info['load_export_basic_param']);exit;
			if($load_product_related_info['load_export_basic_param']!==NULL){
				
				Mage::register('load_export_basic_param', $load_product_related_info['load_export_basic_param']);					
			}else{
				$this->initExportBasicParam();			
			}
			
		}else{
				$flag_file = fopen($flagDir, "w") or die("Unable to open file!");

				$this->loadAttributeSet();
				$this->loadProductType();
				$this->loadStores();		
				$this->initExportBasicParam();
				
				$data=array();
				
				$data['product_type']=Mage::registry('product_type');
				$data['product_attribute_set']=Mage::registry('product_attribute_set');
				$data['store_code']=Mage::registry('store_code');
				$data['store']=Mage::registry('store');
				$data['load_export_basic_param']=Mage::registry('load_export_basic_param');				
				
				fwrite($flag_file, serialize($data));
				fclose($flag_file);
		}	
			
		return $current_time;	
	
	}	
	
	public function mergeCsvAction()
	{
		$combiner_status=Mage::getModel('importproduct/combiner_csv')->process();		
 		$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($combiner_status));
	}
  

    public function historyAction() {
        $this->_initProfile();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('adminhtml/system_convert_profile_edit_tab_history')->toHtml()
        );
    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('admin/system/convert/profiles');
    }
	
	

	public function validateAction(){
		$this->loadLayout();
		$this->renderLayout();
	}
	
	
	public function loadAttributeSet(){

		$_productAttributeSets = array();

		$entityTypeId = Mage::getModel('eav/entity')
			->setType('catalog_product')
			->getTypeId();
		$collection = Mage::getResourceModel('eav/entity_attribute_set_collection')
			->setEntityTypeFilter($entityTypeId);
		foreach ($collection as $set) {
			$_productAttributeSets[$set->getAttributeSetName()] = $set->getId();
		}

		Mage::register('product_attribute_set', $_productAttributeSets);
	}
		
	public function loadProductType(){

		$_productTypes = array();
		$options = Mage::getModel('catalog/product_type')->getOptionArray();
		foreach ($options as $k => $v) {
			$_productTypes[$k] = $k;
		}
		
		Mage::register('product_type', $_productTypes);		
	}
	
	public function loadStores(){
	
        $_stores = Mage::app()->getStores(true, true);
		Mage::register('store_code', $_stores);		
			
        foreach ($_stores as $code => $store) {
                $_storesIdCode[$store->getId()] = $code;
        }

		
		Mage::register('store', $_storesIdCode);		
	}
	
	public function initBasicParam(){

        $fieldset = Mage::getConfig()->getFieldset('catalog_product_dataflow', 'admin');
       
		foreach ($fieldset as $code => $node) {
            /* @var $node Mage_Core_Model_Config_Element */
            if ($node->is('inventory')) {
                foreach ($node->product_type->children() as $productType) {
                    $productType = $productType->getName();
                    $_inventoryFieldsProductTypes[$productType][] = $code;
                    if ($node->is('use_config')) {
                        $_inventoryFieldsProductTypes[$productType][] = 'use_config_' . $code;
                    }
                }

                $_inventoryFields[] = $code;
                if ($node->is('use_config')) {
                    $_inventoryFields[] = 'use_config_'.$code;
                }
            }
            if ($node->is('required')) {
                $_requiredFields[] = $code;
            }
            if ($node->is('ignore')) {
                $_ignoreFields[] = $code;
            }
            if ($node->is('to_number')) {
                $_toNumber[] = $code;
            }
        }	
		
		$_init_param['_inventoryFieldsProductTypes']=$_inventoryFieldsProductTypes;
		$_init_param['_inventoryFields']=$_inventoryFields;
		$_init_param['_requiredFields']=$_requiredFields;
		$_init_param['_ignoreFields']=$_ignoreFields;
		$_init_param['_toNumber']=$_toNumber;
		
		Mage::register('load_basic_param', $_init_param);				
	}
	
	


	public function validateRecordAction()
    {
		$direction=$this->getRequest()->getParam('direction');
		if($direction=='Validated'){
			
			if($this->getRequest()->getParam('validationBehavior')=='skip')
			{
				return;
			}
			$max=ini_get('max_execution_time')/15; 
			$collection = Mage::getModel('importproduct/profiler')->getCollection()->addFieldToFilter('validate','0');
			if($max<10)
			{
				$collection->getSelect()->limit(5);
			}else{
				$collection->getSelect()->limit(15);
			}
			$next_flag=true;
			$error_ctn=0;
			foreach($collection as $d){		
				$error=Mage::getModel("importproduct/validator_validate_importvalidator")->runValidator($d->getData());

				if(count($error['error'])!=0)
				{
				//$error_ctn++;
				}
				$d->setBypassImport($error['bypass']);
				$d->setValidate(true);
				$d->save();
			}

			if(count($collection)==0){			
				$timestamp=Mage::app()->getRequest()->getParam('timestamp');			
				$baseDir = Mage::getBaseDir();
				$flagDir = $baseDir.DS.'var'.DS.'import'.DS.'cws_product_import_flag_validator_do_not_delete-'.$timestamp.'.flag';
				
				if(file_exists($flagDir)){
					unlink($flagDir);
				}
				$next_flag=false;
				$url_reditect=$this->getUrl('*/adminhtml_Importproducts/index',array('active_tab'=>'validationlog','show_import_button'=>'true','behavior'=>$this->getRequest()->getParam('behavior'),'validationBehavior'=>$this->getRequest()->getParam('validationBehavior')));
			}
			$imported_products=count($collection);			
			$this->getResponse()->setBody(Mage::helper('core')->jsonEncode(array('next'=>$next_flag,'imported'=>$imported_products,'error'=>$error_ctn,'url'=>$url_reditect)));
		
		}else if ($direction=='Imported'){

			$max=ini_get('max_execution_time')/15; 
			Mage::register('cws_import_mode', true);
			$collection = Mage::getModel('importproduct/profiler')->getCollection()->addFieldToFilter('validate','1')->addFieldToFilter('imported','0');
			
			if($max<5)
			{
				$collection->getSelect()->limit(5);
			}else{
				$collection->getSelect()->limit(5);
			}
			
			$next_flag=true;
			$error_ctn=0;
			
			if(count($collection)==0){
				//Mage::log('comes in next false',null,'count.txt');
				$next_flag=false;
				$this->deleteImportFlag();
				$url_reditect=$this->getUrl('*/adminhtml_Importproducts/index',array('active_tab'=>'importlog','show_import_alert_box'=>'true'));
				
			}else{	
				$this->createImportFlagIfNoExist();
			}
			
			$importproduct_adapter=Mage::getModel("importproduct/convert_adapter_importproduct");
			//exit;
			foreach($collection as $d){
				
 				if($d->getBypassImport())
				{	
					$error_ctn++;
										
					$product_data=unserialize($d->getProductData());
					$error_model=Mage::getModel('importproduct/importlog');
					$error_model->setErrorInformation('Product SKU: '.$product_data['sku'].' not imported due to major error.');
					$error_model->setErrorType(1);
					$error_model->setProductSku($product_data['sku']);
					$error_model->save();
					$d->setImported(true);
					$d->save();		
					//exit;					
					continue;
				} 
				
				$error=$importproduct_adapter->runImport($d->getData());
				if(count($error)!=0)
				{
					$error_ctn++;
				}
				//$d->setValidate(true);
				//$d->save();
				$d->delete();
			}
			$this->getResponse()->setBody(Mage::helper('core')->jsonEncode(array('next'=>$next_flag,'imported'=>count($collection),'error'=>$error_ctn,'url'=>$url_reditect)));
		}
    }
	
	public function createImportFlagIfNoExist()
	{
		$baseDir = Mage::getBaseDir();
		$timestamp=Mage::app()->getRequest()->getParam('timestamp');			
		$flagDir = $baseDir.DS.'var'.DS.'import'.DS.'cws_product_import_flag_do_not_delete-'.$timestamp.'.flag';				
		if(file_exists($flagDir)){
			
			$flag_data = file_get_contents($flagDir);
			$load_product_related_info=unserialize($flag_data); 

			if($load_product_related_info['product_type']!==NULL){
				Mage::register('product_type', $load_product_related_info['product_type']);		
			}else{
				$this->loadProductType();
			}
			
			if($load_product_related_info['product_attribute_set']!==NULL){
				Mage::register('product_attribute_set', $load_product_related_info['product_attribute_set']);		
			}else{
				$this->loadAttributeSet();			
			}

			if($load_product_related_info['store']!==NULL && $load_product_related_info['store_code']!==NULL){
				Mage::register('store_code', $load_product_related_info['store_code']);		
				Mage::register('store', $load_product_related_info['store']);				
			}else{
				$this->loadStores();						
			}

			if($load_product_related_info['load_basic_param']!==NULL){
				Mage::register('load_basic_param', $load_product_related_info['load_basic_param']);					
			}else{
				$this->initBasicParam();			
			}
			
		}else{
				$flag_file = fopen($flagDir, "w") or die("Unable to open file!");

				$this->loadAttributeSet();
				$this->loadProductType();
				$this->loadStores();		
				$this->initBasicParam();
				
				$data=array();
				
				$data['product_type']=Mage::registry('product_type');
				$data['product_attribute_set']=Mage::registry('product_attribute_set');
				$data['store_code']=Mage::registry('store_code');
				$data['store']=Mage::registry('store');
				$data['load_basic_param']=Mage::registry('load_basic_param');				
				
				fwrite($flag_file, serialize($data));
				fclose($flag_file);
				Mage::getResourceModel('importproduct/importlog')->truncate();						
				//Mage::getModel('importproduct/importlog')->getCollection()->walk('delete');	
		}	
	
	}
	
	public function deleteImportFlag(){
		$baseDir = Mage::getBaseDir();
		$timestamp=Mage::app()->getRequest()->getParam('timestamp');			
		$flagDir = $baseDir.DS.'var'.DS.'import'.DS.'cws_product_import_flag_do_not_delete-'.$timestamp.'.flag';				
		if(file_exists($flagDir)){
				unlink($flagDir);
		}	
	//	$this->doIndexing();
	}
	
	public function deleteImportFlagAction(){		
		$baseDir = Mage::getBaseDir();
		$timestamp=Mage::app()->getRequest()->getParam('timestamp');			
		$flagDir = $baseDir.DS.'var'.DS.'import'.DS.'cws_product_import_flag_do_not_delete-'.$timestamp.'.flag';				
		if(file_exists($flagDir)){
			unlink($flagDir);
		}		
	//	$this->doIndexing();		
	}
	
	
	protected $_indexing_error=array();
	
	public function doIndexingAction(){
			error_reporting(0); 			
			$this->initAction();
			$indexCode=Mage::app()->getRequest()->getParam('indexCode');			

			if($indexCode!==null){
				try
				{
					$process = Mage::getModel('index/process')->load($indexCode);
					$process->reindexEverything();					
				}
				catch(Exception $e)
				{
					$this->_indexing_error[]=$e->getMessage();
				}
			}
	
	}	
	
	public function initAction(){	
		register_shutdown_function(array($this,"shutdown")); 	
	}
	
	function shutdown() 
	{ 		
		foreach($this->_indexing_error as $_index_error){
			echo $_index_error.". ";
		}		
		$error=error_get_last(); 
		if($error!=null)  { 		 			  		
			  echo $error['message'].' . Please do indexing manually OR increase max_execution_time limit.';  
		}
	} 

}

?>