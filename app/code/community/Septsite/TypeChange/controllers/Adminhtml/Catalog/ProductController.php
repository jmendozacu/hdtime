<?php
/* published by the Septsite.pl */

class Septsite_TypeChange_Adminhtml_Catalog_ProductController extends Mage_Adminhtml_Controller_Action
{
	
public function typechangeAction()
{
$productIds = $this->getRequest()->getParam('product');
$storeId = (int)$this->getRequest()->getParam('store', 0);
$danex = (int)$this->getRequest()->getParam('attribute_set');
		
$czykonfig=1;
if (!is_array($productIds)) {$this->_getSession()->addError($this->__('Please select product(s)'));
}else{

// weryfikacja typu ///////////////////////////////////////////////////////////////////////
foreach ($productIds as $productId) {	
$product_old=Mage::getModel('catalog/product')->load($productId);

if($product_old->getTypeId() != 'configurable' and $danex==1){ $czykonfig=2; $nameType='Configurable'; }
if($product_old->getTypeId() != 'simple' and ($danex==2 or $danex==3)){ $czykonfig=3; $nameType='Simple'; }

}}
/////////////////////////////////////////////////////////////////////////////////////////////////////		



if( $czykonfig>1 ) {
			
$this->_getSession()->addError($this->__('Please select product(s) only %s', $nameType));
			
	
}else{
	
try {
foreach ($productIds as $productId) {
						 

$product_old=Mage::getModel('catalog/product')->load($productId);
$product = $product_old->duplicate();
$id_copy = $product->getId(); 
$product = Mage::getModel('catalog/product')->load($id_copy);


if($danex==1 or $danex==3){
	
$collectionat = Mage::getResourceModel('catalog/product_attribute_collection')->addVisibleFilter();
		 
foreach($collectionat as $itemsat){
if($itemsat->getApplyTo()!=NULL and !in_array("grouped", $itemsat->getApplyTo()) ){ 
$product->setData($itemsat->getAttributeCode(), '');		
}}
	
//$product->setPrice(''); 
//$product->setSpecialPrice('');
//$product->setSpecialFromDate('');
//$product->setSpecialToDate('');
//$product->setMsrp('');
//$product->setTaxClassId('');
$product->save();

$product = Mage::getModel('catalog/product')->load($id_copy);
	
$product->setTypeId('grouped');
$product->setSku($product_old->getSku().'-grouped');

}
if($danex==2){
	
foreach($collectionat as $itemsat){
if($itemsat->getApplyTo()!=NULL and !in_array("configurable", $itemsat->getApplyTo()) ){ 
$product->setData($itemsat->getAttributeCode(), '');		
}}	
	
$product->save();

$product = Mage::getModel('catalog/product')->load($id_copy);
		
$product->setTypeId('configurable');
$product->setSku($product_old->getSku().'-configurable');
}




$product->setStatus(1); //status wlaczony
$product->setStockData(array('is_in_stock' => 1)); 

					
$_gallery = $product->getMediaGalleryImages();
foreach ($_gallery as $image ){
$imgfile = $image['file'];
break;
}

$product->setData('image', $imgfile );
$product->setData('small_image', $imgfile );
$product->setData('thumbnail', $imgfile );
////		

//$current_ids = $product_old->getTypeInstance()->getUsedProductIds();
//foreach($current_ids as $temp_id)
//{ $data[$temp_id] = array('qty'=>1,'postion'=>1);  }


$product->setGroupedLinkData($data);
$product->save();


}


				Mage::dispatchEvent('catalog_product_massupdate_after', array('products'=>$productIds));
				$this->_getSession()->addSuccess(
					$this->__('Total of %d record(s) were successfully updated', count($productIds))
				);
			}
			
			catch (Exception $e) {
				$this->_getSession()->addException($e, $e->getMessage());
			}
			
		}
		$this->_redirect('adminhtml/catalog_product/index/', array());
	}	
}
 