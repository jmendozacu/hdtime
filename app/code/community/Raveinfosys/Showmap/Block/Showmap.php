<?php
class Raveinfosys_Showmap_Block_Showmap extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    
     public function getShowmap()     
     { 
        $cats = Mage::getModel('catalog/category')->load(2)->getChildren();
		$catIds = explode(',',$cats);
		$this->setData('catIds', $catIds);
		  
		$product    = Mage::getModel('catalog/product');
		$products   = $product->getCollection()->addStoreFilter($storeId)->getData();
		$this->setData('products', $products);
		  
		$collection = Mage::getModel('cms/page')->getCollection()->addStoreFilter(Mage::app()->getStore()->getId());
		$collection->getSelect()
			->where('is_active = 1');
		$this->setData('collection', $collection);
		
		return Mage::getModel('showmap/config')->getRow();
    }
}