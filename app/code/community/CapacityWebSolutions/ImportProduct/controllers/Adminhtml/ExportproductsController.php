<?php
/***************************************************************************
	@extension	: Import Products categories, multiple images and custom options
	@copyright	: Copyright (c) 2014 Capacity Web Solutions.
	( http://www.capacitywebsolutions.com )
	@author		: Capacity Web Solutions Pvt. Ltd.
	@support	: magento@capacitywebsolutions.com
	
***************************************************************************/

class  CapacityWebSolutions_ImportProduct_Adminhtml_ExportproductsController extends Mage_Adminhtml_Controller_Action
{
	protected function _initAction() 
	{
	$this->loadLayout()
			->_setActiveMenu('cws')
			->_addBreadcrumb(Mage::helper('adminhtml')->__('Items Manager'), Mage::helper('adminhtml')->__('Item Manager'));
		
		return $this;
		
		
	}
	
	public function indexAction() {
	
		$this->_initAction();		
		if($data==null){		
		$this->_addContent($this->getLayout()->createBlock('importproduct/adminhtml_exportproducts_edit'))
             ->_addLeft($this->getLayout()->createBlock('importproduct/adminhtml_exportproducts_edit_tabs'));			 			 
		}
		$this->renderLayout();		
		
		Mage::getSingleton("core/session")->addNotice("Note : Exported file will be save in var/export directory.");
		
	}

	public function downloadExportedFileAction(){
	
		$filename=Mage::app()->getRequest()->getParam('file');
        $filepath = Mage::getBaseDir('base').'/var/export/'.$filename;

        if (! is_file ( $filepath ) || ! is_readable ( $filepath )) {
            throw new Exception ( );
        }
        $this->getResponse ()
                    ->setHttpResponseCode ( 200 )
                    ->setHeader ( 'Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true )
                     ->setHeader ( 'Pragma', 'public', true )
                    ->setHeader ( 'Content-type', 'application/force-download' )
                    ->setHeader ( 'Content-Length', filesize($filepath) )
                    ->setHeader ('Content-Disposition', 'attachment' . '; filename=' . basename($filepath) );
        $this->getResponse ()->clearBody ();
        $this->getResponse ()->sendHeaders ();
        readfile ( $filepath );
        exit;
		
	
	}
	public function downloadExportedFileFromGridAction(){
	
		$file_id=Mage::app()->getRequest()->getParam('id');
		$file=Mage::getModel('importproduct/exportedfile')->load($file_id);
        $filename=$file->getFileName(); 
		
		$filepath = Mage::getBaseDir('base').'/var/export/'.$filename;

        if (! is_file ( $filepath ) || ! is_readable ( $filepath )) {
            throw new Exception ( );
        }
        $this->getResponse ()
                    ->setHttpResponseCode ( 200 )
                    ->setHeader ( 'Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true )
                     ->setHeader ( 'Pragma', 'public', true )
                    ->setHeader ( 'Content-type', 'application/force-download' )
                    ->setHeader ( 'Content-Length', filesize($filepath) )
                    ->setHeader ('Content-Disposition', 'attachment' . '; filename=' . basename($filepath) );
        $this->getResponse ()->clearBody ();
        $this->getResponse ()->sendHeaders ();
        readfile ( $filepath );
        exit;
		
	
	}

	
	public function deleteExportedFileFromGridAction(){
	
		$file_id=Mage::app()->getRequest()->getParam('id');
		$file=Mage::getModel('importproduct/exportedfile')->load($file_id);   
		$filename=$file->getFileName();					
		$file->delete();
		$baseDir = Mage::getBaseDir();		
		$filepath = $baseDir.DS.'var'.DS.'export'.DS.$filename;						
		
		if(file_exists($filepath)){
			unlink($filepath);
			Mage::getSingleton("core/session")->addSuccess("File: ".$filename." successfully deleted."); 
		}else{
				Mage::getSingleton("core/session")->addError("File: ".$filename." does not exists.");			
		}
		
		$this->_redirect('*/*/index');

	}	
	
}
?>