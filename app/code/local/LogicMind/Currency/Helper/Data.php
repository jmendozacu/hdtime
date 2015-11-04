<?php

class LogicMind_Currency_Helper_Data extends Mage_Core_Helper_Abstract
{

  public function productFullUrl($product){

    if(is_object($product) && $product->getSku())
    {
      $allCategoryIds = $product->getCategoryIds();
      $lastCategoryId = end($allCategoryIds);
      $lastCategory = Mage::getModel('catalog/category')->load($lastCategoryId);
      $last = $lastCategory->getLevel();
      for ($i=0; $i < count($allCategoryIds); $i++) {
        $lastCategorys = Mage::getModel('catalog/category')->load($allCategoryIds[count($allCategoryIds)-2]);
        if($last < $lastCategorys->getLevel()){
          $lastCategory = $lastCategorys;
        }
      }
      try{
        $query = "
        SELECT `request_path`
        FROM `core_url_rewrite`
        WHERE `product_id`='" . $product->getEntityId() . "'
        AND `category_id`='" . $lastCategory->getId() . "'
        AND `store_id`='" . Mage::app()->getStore()->getId() . "';
        ";
        $read = Mage::getSingleton('core/resource')->getConnection('core_read');
        $result = $read->fetchRow($query);
        return Mage::getUrl('') . $result['request_path'];
      } catch (Exception $e) {
        $allCategoryIds = $product->getCategoryIds();
        $lastCategoryId = end($allCategoryIds);
        $lastCategory = Mage::getModel('catalog/category')->load($lastCategoryId);
        $lastCategoryUrl = $lastCategory->getUrl();
        $fullProductUrl = str_replace(Mage::getStoreConfig('catalog/seo/category_url_suffix'), '/', $lastCategoryUrl) . basename( $product->getUrlKey() ) . Mage::getStoreConfig('catalog/seo/product_url_suffix');
        return $fullProductUrl;
      }
    }
    return '';
  }
}
