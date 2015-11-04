<?php

class Raveinfosys_Showmap_Adminhtml_ShowmapController extends Mage_Adminhtml_Controller_Action
{

 public function indexAction()  
 {
    if(Mage::getModel('showmap/showmap')->generateSitemapXML())
	$this->_forward('edit');
	else
	$this->_redirect('*/*/config');
 }

 public function editAction()
 {
	$this->loadLayout();
	$this->_setActiveMenu('showmap/items');
			 
	$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
	$this->_addContent($this->getLayout()->createBlock('showmap/adminhtml_showmap_edit'))
			->_addLeft($this->getLayout()->createBlock('showmap/adminhtml_showmap_edit_tabs'));

	$this->renderLayout();
 }
 
 public function saveAction()
 {			
	if ($data = $this->getRequest()->getPost()) 
   	{
	    $model = Mage::getModel('showmap/showmap');
	    $model->submitSitemap($data);  
		$model->setData($data)
		         ->save();
				 
		$model->changeInterval();
	} 
    $this->_redirect('*/*/');
 }
	
 public function configAction()
 {
    $this->loadLayout();
	$this->_setActiveMenu('showmap/items');
		 
	$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
	$this->_addContent($this->getLayout()->createBlock('showmap/adminhtml_showmap_config'))
			->_addLeft($this->getLayout()->createBlock('showmap/adminhtml_showmap_edit_config'));
			
	if ($data = $this->getRequest()->getPost()) 
	{
	   $model = Mage::getModel('showmap/config');
	   $data['configured'] = 1;
	   $model->setData($data)
			 ->save();
	   Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('showmap')->__('Sitemap was successfully configured'));
	   $this->_redirect('*/*/config');
	}
	$this->renderLayout();
 }	
 
}