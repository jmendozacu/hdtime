<?php

class Raveinfosys_Showresponse_Adminhtml_ShowresponseController extends Mage_Adminhtml_Controller_Action
{

	protected function _initAction()  
	{
		$this->loadLayout()
			->_setActiveMenu('showmap/items');
		
		return $this;
	}   
 
	public function indexAction() 
	{
		$config = Mage::getModel('showmap/config');
		$row = $config->getRow();
		if($row['configured'] == 0)$this->_redirect('showmap/adminhtml_showmap/config');
		$this->_initAction()
			->renderLayout();
	}

	
	public function deleteAction() 
	{
		if( $this->getRequest()->getParam('id') > 0 ) 
		{
			try 
			{
				$model = Mage::getModel('showresponse/showresponse');
				$model->setId($this->getRequest()->getParam('id'))
					->delete();
					 
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Item was successfully deleted'));
				$this->_redirect('*/*/');
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
			}
		}
		$this->_redirect('*/*/');
	}

    public function massDeleteAction() 
	{
        $showresponseIds = $this->getRequest()->getParam('showresponse');
        if(!is_array($showresponseIds)) 
		{
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } 
		else 
		{
            try 
			{
                foreach ($showresponseIds as $showresponseId) 
				{
                    $showresponse = Mage::getModel('showresponse/showresponse')->load($showresponseId);
                    $showresponse->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                Mage::helper('adminhtml')->__('Total of %d record(s) were successfully deleted', count($showresponseIds)));
            } 
			catch (Exception $e){
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
}