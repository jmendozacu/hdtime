<?php

/***************************************************************************
	@extension	: Import Products categories, multiple images and custom options
	@copyright	: Copyright (c) 2014 Capacity Web Solutions.
	( http://www.capacitywebsolutions.com )
	@author		: Capacity Web Solutions Pvt. Ltd.
	@support	: magento@capacitywebsolutions.com
	
***************************************************************************/

ini_set("memory_limit","1024M");
class CapacityWebSolutions_ImportProduct_Model_Convert_Parser_Importproduct extends Mage_Eav_Model_Convert_Parser_Abstract
{
	const MULTI_DELIMITER = ' , ';

	protected $_product_data_collection=array();
	protected $_proceed_next=true;

    protected $_attributes = array();
    protected $_inventoryFields = array();
    protected $_imageFields = array();
    protected $_systemFields = array();
    protected $_internalFields = array();
    protected $_externalFields = array();
    protected $_inventoryItems = array();	
	protected $_exporting_fields = array();	
	protected $_required_fields_for_import=array('store','websites','sku');
	protected $_export_all_fields=true;
	protected $_export_custom_options=true;
	protected $_configurable_product_fields=true;
	protected $_bundle_product_fields=true;
	protected $_grouped_product_fields=true;
	protected $_downloadable_product_fields=true;

				
	public function __construct()
    {
		$load_basic_export_param=Mage::registry('load_export_basic_param');		
		$this->_inventoryFields= $load_basic_export_param['_inventoryFields'];
		$this->_internalFields= $load_basic_export_param['_internalFields'];
		$this->_systemFields= $load_basic_export_param['_systemFields'];
		$this->_externalFields= $load_basic_export_param['_externalFields'];
		$this->_imageFields= $load_basic_export_param['_imageFields'];		
		$this->_exporting_fields=Mage::app()->getRequest()->getParam('export_fields');
		
		//print_r($load_basic_export_param);exit;
		
		switch(Mage::app()->getRequest()->getParam('export_type')){
			case 'subselection':
				$this->_export_all_fields=false;
				break;
			case '*':
				$this->_export_all_fields=true;
				break;
			default:
				$this->_export_all_fields=true;			
		}
		
		if(!$this->_export_all_fields)
		{
			if(!in_array('custom_options_fields',$this->_exporting_fields))
			{
				$this->_export_custom_options=false;
			}

			if(!in_array('configurable_product_fields',$this->_exporting_fields))
			{
				$this->_configurable_product_fields=false;
			}

			if(!in_array('bundle_product_fields',$this->_exporting_fields))
			{
				$this->_bundle_product_fields=false;
			}

			if(!in_array('grouped_product_fields',$this->_exporting_fields))
			{
				$this->_grouped_product_fields=false;
			}						

			if(!in_array('downloadable_product_fields',$this->_exporting_fields))
			{
				$this->_downloadable_product_fields=false;
			}			
			
		}
		
	//	echo $this->_export_all_fields;exit;
    }
	
	public function parse()
    {
	}
	
    public function getExternalAttributes()
    {
        $productAttributes  = Mage::getResourceModel('catalog/product_attribute_collection')->load();
        $attributes         = $this->_externalFields;

        foreach ($productAttributes as $attr) {
            $code = $attr->getAttributeCode();
            if (in_array($code, $this->_internalFields) || $attr->getFrontendInput() == 'hidden') {
                continue;
            }
            $attributes[$code] = $code;
        }

        foreach ($this->_inventoryFields as $field) {
            $attributes[$field] = $field;
        }

        return $attributes;
    }
	
	
	public function getProductExportFile(){	
		$page=Mage::app()->getRequest()->getParam('page');		
		$last_page=$this->unparse($page);

		if($last_page==$page)
		{
			$this->_proceed_next=false;
		}
		
		switch(Mage::app()->getRequest()->getParam('exportfiletype'))
		{
			default:
				return $this->exportCSV();			
			break;			
		}
	}


	public function exportCSV(){
	
			//$current_time=time();		
			$page=intval(Mage::app()->getRequest()->getParam('page'));
			$current_time=Mage::app()->getRequest()->getParam('timestamp');

			if($page!=1)
			{
				//$current_time=Mage::app()->getRequest()->getParam('timestamp');
				$cws_csv_header=Mage::app()->getConfig()->getTempVarDir().'/export/cws_csv_header-'.$current_time.'.obj';
				$header_string_obj=file_get_contents($cws_csv_header);
				$header_template=unserialize($header_string_obj);
				
			}else{
				$header_template=array();						
			}	

			$f_name=Mage::app()->getRequest()->getParam('output').'-'.$current_time.'.csv';
			$file_name=Mage::app()->getConfig()->getTempVarDir().'/export/'.$f_name;
			
			$fp = fopen($file_name.'-tmp-'.$page, 'w');
			$header=true;
			$temp_order_data=array();
			
			foreach ($this->_product_data_collection as $product) {
				
				foreach(array_keys($product) as $k=>$v)
				{
					if (!in_array($v, $header_template)) {
						array_push($header_template,$v);
					}
				}				
			}


			$cws_csv_header=Mage::app()->getConfig()->getTempVarDir().'/export/cws_csv_header-'.$current_time.'.obj';
			file_put_contents($cws_csv_header, serialize($header_template),LOCK_EX);
			
			fputcsv($fp, array_values($header_template));
					
			foreach ($this->_product_data_collection as $product) {
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
			$page++;
			return array("filename"=>$f_name,'fullpath'=>$file_name,"proceed_next"=>$this->_proceed_next,'page'=>$page,"exportedOrders"=>count($this->_product_data_collection),'timestamp'=>$current_time);			
	}
	
    protected $_store;

    public function getStore($store_export_id)
    {
        if (is_null($this->_store)) {
            try {
                $store = Mage::app()->getStore($store_export_id);
            } catch (Exception $e) {

            }
            $this->_store = $store;
        }
        return $this->_store;
    }
    public function getProductModel()
    {
        if (is_null($this->_productModel)) {
            $productModel = Mage::getModel('catalog/product');
            $this->_productModel = Mage::objects()->save($productModel);
        }
        return Mage::objects()->load($this->_productModel);
    }	
	
	protected $_productTypeInstances = array();
	public function setProductTypeInstance(Mage_Catalog_Model_Product $product)
    {
        $type = $product->getTypeId();
        if (!isset($this->_productTypeInstances[$type])) {
            $this->_productTypeInstances[$type] = Mage::getSingleton('catalog/product_type')
                ->factory($product, true);
        }
        $product->setTypeInstance($this->_productTypeInstances[$type], true);
        return $this;
    }	
	
    public function unparse($page=1)
    {

		$store_export_id = Mage::app()->getRequest()->getParam('store_id');
								
		$_product_collection = Mage::getModel('catalog/product')->getCollection()->setPageSize(200)->setCurPage($page);
		
		if($store_export_id!='*'){
			$_product_collection->addStoreFilter($store_export_id);
		}else{
			$store_export_id=Mage::app()->getWebsite()->getDefaultGroup()->getDefaultStoreId();;
		}

		$category_model = Mage::getModel('catalog/category');  //get category model
				
		foreach($_product_collection as $entiry_id){

		    $this->_productModel=null;
            $product = $this->getProductModel()
                ->setStoreId($store_export_id)
                ->load($entiry_id->getId());
				
            $this->setProductTypeInstance($product);
			
            $row = array(
                'store'         => $this->getStore($store_export_id)->getCode(),
                'websites'      => '',
                'attribute_set' => $this->getAttributeSetName($product->getEntityTypeId(),
                                        $product->getAttributeSetId()),
                'type'          => $product->getTypeId(),
                'category_ids'  => join(',', $product->getCategoryIds())
            );

            if ($this->getStore($store_export_id)->getCode() == Mage_Core_Model_Store::ADMIN_CODE) {
                $websiteCodes = array();
                foreach ($product->getWebsiteIds() as $websiteId) {
                    $websiteCode = Mage::app()->getWebsite($websiteId)->getCode();
                    $websiteCodes[$websiteCode] = $websiteCode;
                }
                $row['websites'] = join(',', $websiteCodes);
            } else {
                $row['websites'] = $this->getStore()->getWebsite()->getCode();
                if ($this->getVar('url_field')) {
                    $row['url'] = $product->getProductUrl(false);
                }
            }


            foreach ($product->getData() as $field => $value) {
                if (in_array($field, $this->_systemFields) || is_object($value)) {
                    continue;
                }

                $attribute = $this->getAttribute($field);
                if (!$attribute) {
                    continue;
                }

                if ($attribute->usesSource()) {
                    $option = $attribute->getSource()->getOptionText($value);
                    if ($value && empty($option) && $option != '0') {
                        $this->addException(
                            Mage::helper('catalog')->__('Invalid option ID specified for %s (%s), skipping the record.', $field, $value),
                            Mage_Dataflow_Model_Convert_Exception::ERROR
                        );
                        continue;
                    }
                    if (is_array($option)) {
                        $value = join(self::MULTI_DELIMITER, $option);
                    } else {
                        $value = $option;
                    }
                    unset($option);
                } elseif (is_array($value)) {
                    continue;
                }

                $row[$field] = $value;
            }

            if ($stockItem = $product->getStockItem()) {
                foreach ($stockItem->getData() as $field => $value) {
                    if (in_array($field, $this->_systemFields) || is_object($value)) {
                        continue;
                    }
                    $row[$field] = $value;
                }
            }
			
 
			
			/*Code Added By Dixit*/
			$cws_tier_price = $product->getData('tier_price');
			$row['cws_tier_price']='';
			if(is_array($cws_tier_price)) {
				foreach($cws_tier_price as $cws_tier_str){
					#print_r($tier_str);
					$row['cws_tier_price'] .= $cws_tier_str['cust_group'] . "=" . round($cws_tier_str['price_qty']) . "=" . round($cws_tier_str['price'],2) . "|";
				}
				$row['cws_tier_price']=trim($row['cws_tier_price'], "|");
			}
			
			$cws_group_price = $product->getData('group_price');
			$row['cws_group_price']='';
			if(is_array($cws_group_price)) {
				foreach($cws_group_price as $cws_group_str){
					#print_r($tier_str);
					$row['cws_group_price'] .= $cws_group_str['cust_group'] . "=" . round($cws_group_str['price']). ",";
				}
				$row['cws_group_price']=trim($row['cws_group_price'], ",");
			}
			
			
			if($product->getTypeId()=='configurable'){

				$super_attr=array();	
				$child_sku=array();		
				$option_data=array();
				$attrs  = $product->getTypeInstance(true)->getConfigurableAttributesAsArray($product);
				foreach($attrs as $attr) {					
					array_push($super_attr,$attr['attribute_code']);				
					$optiondata=$attr['values'];
					foreach($optiondata as $opt){
						
						$opt_str=$opt['default_label'].":".round($opt['pricing_value'],2).':'.$opt['is_percent'];
						array_push($option_data,$opt_str);	
					}
				}
				
				$childProducts = Mage::getModel('catalog/product_type_configurable')->getUsedProducts(null,$product);
								
				foreach($childProducts as $child){				
					array_push($child_sku,$child->getSku()); 
				}

				$row['used_attribute']=implode(",",$super_attr);
				$row['super_attribute_value']=implode("|",$option_data);
				$row['child_products_sku']=implode(",",$child_sku);	

					
				if($this->_configurable_product_fields){
					$this->_exporting_fields[]='used_attribute';
					$this->_exporting_fields[]='super_attribute_value';
					$this->_exporting_fields[]='child_products_sku';
				}

			}else if($product->getTypeId()=='bundle'){
			
				//echo "<pre>";
				//print_r($product);
				$price_view=array("as low as","price range");
				$price_type=array("dynamic","fixed");



				$optionCollection = $product->getTypeInstance()->getOptionsCollection();
				$selectionCollection = $product->getTypeInstance()->getSelectionsCollection($product->getTypeInstance()->getOptionsIds());
				$options = $optionCollection->appendSelections($selectionCollection);
				foreach( $options as $option )
				{
					$bundle_opt_str=$option->getDefaultTitle().','.$option->getType().','.$option->getRequired().','.$option->getPosition();	
				 	//print_r($option);			 
					$_selections = $option->getSelections();
					$i=0;
					foreach( $_selections as $selection )
					{
						//echo  $selection->getName();
						if($i==0){
						$bundle_opt_str=$bundle_opt_str.":".$selection->getSku().",".round($selection->getSelectionQty()).",".$selection->getSelectionCanChangeQty().",".$selection->getPosition().",".$selection->getIsDefault();
						}else{
						$bundle_opt_str=$bundle_opt_str."!".$selection->getSku().",".round($selection->getSelectionQty()).",".$selection->getSelectionCanChangeQty().",".$selection->getPosition().",".$selection->getIsDefault();
						}
						$i++;
					}
				}

				$row['price_type']=$price_type[$product->getPriceType()];
				$row['price_view']=$price_view[$product->getPriceView()];
				$row['bundle_product_options']=$bundle_opt_str;		
				
				if($this->_bundle_product_fields){
					$this->_exporting_fields[]='bundle_product_options';
				}				
			
			}else if($product->getTypeId()=='grouped'){

				$groupedassociatedProducts = $product->getTypeInstance(true)->getAssociatedProducts($product);
				$gchild_sku=array();
				
				foreach($groupedassociatedProducts as $child){
					array_push($gchild_sku,"".$child->getSku());
				}
				
				$row['grouped_product_sku']=implode(",",$gchild_sku);	
				if($this->_grouped_product_fields){
					$this->_exporting_fields[]='grouped_product_sku';
				}						

			}else if($product->getTypeId()=='downloadable'){

				$download_links=array();	
				$is_sharable=array("");
				$links=Mage::getModel('downloadable/link')
									  ->getCollection()
									  ->addFieldToFilter('product_id',array('eq'=>$product->getId()))->addTitleToResult()->addPriceToResult();

				  foreach($links as $link){
					   //do you task on links	
					//	print_r($link);
					   $download_opt_str=$link->getTitle().",".round($link->getPrice(),2).",".$link['number_of_downloads'].",".$link->getLinkType();
					    
					   if($link->getLinkType()=='url'){
					   $download_opt_str=$download_opt_str.",".$link->getLinkUrl();
					   }else{
					   $download_opt_str=$download_opt_str.",".$link->getLinkFile();
					   }

					   $download_opt_str=$download_opt_str.";".$link->getSampleType();
					   
					   if($link->getSampleType()=='url'){
					   $download_opt_str=$download_opt_str.",".$link->getSampleUrl();
					   }else{
					   $download_opt_str=$download_opt_str.",".$link->getSampleFile();
					   }
					   
					   
					   array_push($download_links,$download_opt_str);
				   }
					
				
				$row['downloadable_product_options']=implode('|',$download_links);							
				$row['link_can_purchase_separately']=$product->getLinksPurchasedSeparately();	

				if($this->_downloadable_product_fields){
					$this->_exporting_fields[]='downloadable_product_options';
					$this->_exporting_fields[]='link_can_purchase_separately';
				}						
				unset($row['links_purchased_separately']);				   				   
			}			
			
			
			/* Code is added by jalpesh*/
			/*This code is use for the get product category like ParentCategory/ChildCategory/SubchildCategory*/
			
			$all_cats = $product->getCategoryIds(); // all the categories
			$main_cnt = count($all_cats);
			$cat_str_main = ''; 
			$j = 0;
			foreach($all_cats as $ac)
			{
				$category = $category_model->load($ac);
				$pathIds = explode("/",$category->getPath());
				$coll = $category->getResourceCollection()->addAttributeToSelect('name')->addAttributeToFilter('entity_id', array('in' => $pathIds));
				$cat_str = '';
				$i=0;
				foreach($coll as $cat)
				{
					if($i == 2)
					{
						$cat_str = $cat->getName();
					}
					else if($i > 2)
					{
						$cat_str = $cat_str."/".$cat->getName();
					}
					$i = $i+1;
				}
				if($j < 1)
				{
					$cat_str_main = $cat_str;
				}
				else 
				{			
					$cat_str_main = $cat_str_main .",".$cat_str;
				}
				$j = $j+1;
			}
			$row['categories'] = $cat_str_main;
			unset($cat_str_main);
			
			if($this->_export_custom_options){
				$field_value = '';
				foreach ($product->getOptions() as $o) 
				{
					$optionType = $o->getType();
					$optionTitle = $o->getTitle(); 
					$is_required = $o->getIsRequire();
					$sort_order_cust=$o->getSortOrder();

					$price = $o->getPrice();
					
					$values = $o->getValues();
					$price_type = $o->getPriceType();
					$sku =	$o->getSku();
					$max_characters = $o->getMaxCharacters();
					
					$title='cws!'.$optionTitle.':'.$optionType.':'.$is_required.":".$sort_order_cust;
					
					switch($optionType)
					{
						case 'area':
							$field_value = "|:".$price_type.":".$price.":".$sku.":".$max_characters;
							break;
						case 'date':
							$field_value = "|:".$price_type.":".$price.":".$sku;
							break;
						case 'date_time':
							$field_value = "|:".$price_type.":".$price.":".$sku;
							break;
						case 'field':
							$field_value = "|:".$price_type.":".$price.":".$sku.":".$max_characters;
							break;
						case 'time':
							$field_value = "|:".$price_type.":".$price.":".$sku;
							break;
						case 'drop_down':
						case 'radio':
						case 'multiple':
						case 'checkbox':
						default:
							$values = $o->getValues();
							$cnt = count($values);
							$i=0;			
							foreach ($values as $k => $v)
							{
								$price = $v->getPrice();
								$price_type = $v->getPriceType();
								$sku = $v->getSku();
								$sort_order = $v->getSortOrder();
								if($cnt == 0)
								{
									
									$field_value = $v->getData('default_title').":".$price_type.":".$price.":".$sku.":".$sort_order;
								}
								else
								{
									if($cnt - 1  > $i)
									{
										$field_value = $field_value.''.$v->getData('default_title').":".$price_type.":".$price.":".$sku.":".$sort_order."|";
									}
									else
									{
										$field_value = $field_value.''.$v->getData('default_title').":".$price_type.":".$price.":".$sku.":".$sort_order;
									}
								}
								$i++;
							}
					}
					$this->_required_fields_for_import[]=$title;
					$row[$title] = $field_value;
					unset($field_value);	
				}
			
			}
			
			/* Remove comment when required all the field*/
			unset($row["category_ids"]);	


			$gimages = Mage::getModel('catalog/product')->load($product->getId())->getMediaGalleryImages();
			$mediagallery='';
			$index=0;
			foreach($gimages as $_image){
				if($_image['file']!=$product->getThumbnail() && $_image['file']!=$product->getSmallImage() && $_image['file']!=$product->getImage()){
				
				if($index==0){
				$mediagallery=$_image['file'];
				}else{
				$mediagallery=$mediagallery.'|'.$_image['file'];
				}
				$index++;
				
				}			
			}
			$row["gallery"]=$mediagallery;
			
			$related_product_array=array();
			$related_product_ids=$product->getRelatedProductIds();
			foreach($related_product_ids as $r_id){				 
				array_push($related_product_array,Mage::getModel('catalog/product')->load($r_id)->getSku());
			}
			
			if(count($related_product_array)>0){
				$row['related_product_sku']=implode(',',$related_product_array);
			}
			
			$crosssell_product_array=array();
			$crosssell_product_ids=$product->getCrossSellProductIds();
			foreach($crosssell_product_ids as $c_id){				 
				array_push($crosssell_product_array,Mage::getModel('catalog/product')->load($c_id)->getSku());
			}
			
			if(count($crosssell_product_array)>0){
				$row['crosssell_product_sku']=implode(',',$crosssell_product_array);
			}			
			
			$upsell_product_array=array();
			$upsell_product_ids=$product->getUpSellProductIds();
			foreach($upsell_product_ids as $u_id){				 
				array_push($upsell_product_array,Mage::getModel('catalog/product')->load($u_id)->getSku());
			}
			
			if(count($upsell_product_array)>0){
				$row['upsell_product_sku']=implode(',',$upsell_product_array);
			}			

			
			
           foreach ($this->_imageFields as $field) {
                if (isset($row[$field]) && $row[$field] == 'no_selection') {
                    //echo $row[$field];
					$row[$field] = null;
                }
            }
			$row=$this->generateNewData($row);					
			array_push($this->_product_data_collection,array_filter($row, 'strlen'));	
			
		}
		
		return $_product_collection->getLastPageNumber();
	}
	
	public function generateNewData($row){
		/*
		Comment this condition for export only. (store,website,sku)
		if(is_array($this->_exporting_fields) && $this->_export_all_fields==false){
		*/		
		if($this->_export_all_fields==false){
			foreach($row as $key=>$value){
					if (in_array($key, $this->_exporting_fields)) {

					}else{
						if(!in_array($key,$this->_required_fields_for_import)){
							unset($row[$key]);
						}
					}		
			}
		}
		return $row;
	}	

	public function getAttribute($code)
    {
        if (!isset($this->_attributes[$code])) {
            $this->_attributes[$code] = $this->getProductModel()->getResource()->getAttribute($code);
        }
        return $this->_attributes[$code];
    }

	
}
		

