<?php
/**
 * Created by PhpStorm.
 * User: test
 * Date: 7/10/14
 * Time: 11:18 AM
 */ 
class Komplizierte_ManaCategory_Model_Catalog_Category extends Mage_Catalog_Model_Category {
    public function getProductCount()
    {
        if (!$this->hasProductCount()) {
            $prodCollection = Mage::getResourceModel('catalog/product_collection')->addCategoryFilter($this);
            Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($prodCollection);
            Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($prodCollection);
            $count = $prodCollection->count();
            $this->setData('product_count', $count);
        }
        return $this->getData('product_count');
    }
}