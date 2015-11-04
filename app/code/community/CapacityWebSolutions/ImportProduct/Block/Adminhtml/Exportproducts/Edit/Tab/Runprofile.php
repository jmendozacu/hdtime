<?php

/***************************************************************************
	@extension	: Import Products categories, multiple images and custom options
	@copyright	: Copyright (c) 2014 Capacity Web Solutions.
	( http://www.capacitywebsolutions.com )
	@author		: Capacity Web Solutions Pvt. Ltd.
	@support	: magento@capacitywebsolutions.com
	
***************************************************************************/

class CapacityWebSolutions_ImportProduct_Block_Adminhtml_Exportproducts_Edit_Tab_Runprofile extends Mage_Adminhtml_Block_Widget_Form
{

    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('importproduct/export.phtml');
    }

    public function getRunButtonHtml()
    {

	    $html .= $this->getLayout()->createBlock('adminhtml/widget_button')->setType('button')
            ->setClass('save')->setLabel($this->__('Export Products.'))
            ->setOnClick('runProfile(false)')
            ->toHtml();
			                
		return $html;
    }

    public function getValidateButtonHtml()
    {

	    $html .= $this->getLayout()->createBlock('adminhtml/widget_button')->setType('button')
            ->setClass('save')->setLabel($this->__('Validate File Data'))
            ->setOnClick('validateData()')
            ->toHtml();
			                
		return $html;
    }	
	


    public function getStoreFilterOptions()
    {
        if (!$this->_filterStores) {
            #$this->_filterStores = array(''=>$this->__('Any Store'));
            $this->_filterStores = array();
            foreach (Mage::getConfig()->getNode('stores')->children() as $storeNode) {
                if ($storeNode->getName()==='default') {
                    //continue;
                }
                $this->_filterStores[$storeNode->getName()] = (string)$storeNode->system->store->name;
            }
        }
        return $this->_filterStores;
    }	
	
    protected function _getStoreModel() {
        if (is_null($this->_storeModel)) {
            $this->_storeModel = Mage::getSingleton('adminhtml/system_store');
        }
        return $this->_storeModel;
    }
	
    public function getWebsiteCollection()
    {
        return $this->_getStoreModel()->getWebsiteCollection();
    }

    public function getGroupCollection()
    {
        return $this->_getStoreModel()->getGroupCollection();
    }

    public function getStoreCollection()
    {
        return $this->_getStoreModel()->getStoreCollection();
    }	
    public function getParseType()
    {

            return 'csv';
    }
	
	public function getAttriubteList(){
		
		$categorize_attribute=array();
		$extra_fields_adder=array();
        foreach (Mage::getConfig()->getFieldset('catalog_product_dataflow', 'admin') as $code=>$node) {
            if ($node->is('inventory')) {
                $categorize_attribute[$code]='inventory_fields';
				$extra_fields_adder[]=array("code"=>$code,'type'=>'inventory_fields');
                if ($node->is('use_config')) {
                    $categorize_attribute['use_config_'.$code]='inventory_fields';
					$extra_fields_adder[]=array("code"=>'use_config_'.$code,'type'=>'inventory_fields');
				}
            }
            if ($node->is('internal')) {
               $categorize_attribute[$code]='internal_fields';
            }
            if ($node->is('system')) {
               $categorize_attribute[$code]='system_fields';				
            }
            if ($node->is('external')) {
               $categorize_attribute[$code]='external_fields';				
            }
            if ($node->is('img')) {
               $categorize_attribute[$code]='img_fields';
			}
        }
		
		//print_r($categorize_attribute);exit;
		$restricted_fields=array('old_id','media_gallery','links_title','samples_title','sku');
		
		$attributes = Mage::getResourceModel('catalog/product_attribute_collection')->getItems();
		$attribute_list=array();
		foreach($attributes as $attribute){			
			$code=$attribute->getAttributeCode();
			if($code=='tier_price' || $code=='group_price'){
				$code='cws_'.$code;
			}
			
			if($code=='required_options' || $code=='has_options'){
				$attribute_list[]=array("code"=>$code,'type'=>'customoptions_fields');			
			}else if($code=='price' || $code=='cws_tier_price' || $code=='special_price'	|| $code=='price_view' || $code=='cws_group_price' || $code=='msrp_display_actual_price_type' || $code=='price_type' || $code=='minimal_price' || $code=='msrp'	){
				$attribute_list[]=array("code"=>$code,'type'=>'price_fields');						
			}else if(!in_array($code,$restricted_fields)){	
				$attribute_list[]=array("code"=>$code,'type'=>$categorize_attribute[$attribute->getAttributeCode()]);
			}
		}
		$extra_fields_adder[]=array("code"=>'categories','type'=>'');				
		$extra_fields_adder[]=array("code"=>'gallery','type'=>'img_fields');
		$extra_fields_adder[]=array("code"=>"custom_options_fields",'type'=>'customoptions_fields');
		$extra_fields_adder[]=array("code"=>"configurable_product_fields",'type'=>'configurable_fields');
		$extra_fields_adder[]=array("code"=>"bundle_product_fields",'type'=>'bundle_fields');
		$extra_fields_adder[]=array("code"=>"grouped_product_fields",'type'=>'grouped_fields');
		$extra_fields_adder[]=array("code"=>"downloadable_product_fields",'type'=>'downloadable_fields');

		$extra_fields_adder[]=array("code"=>"related_product_sku",'type'=>'');
		$extra_fields_adder[]=array("code"=>"crosssell_product_sku",'type'=>'');
		$extra_fields_adder[]=array("code"=>"upsell_product_sku",'type'=>'');
				
		return array_merge($attribute_list,$extra_fields_adder);
	}
	

}