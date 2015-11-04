<?php
/**
 * Amasty_ShippingDate extension
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 * 
 * @category   	Amasty
 * @package		Amasty_ShippingDate
 * @copyright  	Copyright (c) 2014
 * @license		http://opensource.org/licenses/mit-license.php MIT License
 */
/**
 * Weekend admin controller
 *
 * @category	Amasty
 * @package		Amasty_ShippingDate
 * @author Ultimate Module Creator
 */
class Amasty_ShippingDate_Adminhtml_Shippingdate_WeekendController extends Amasty_ShippingDate_Controller_Adminhtml_ShippingDate{
	/**
	 * init the weekend
	 * @access protected
	 * @return Amasty_ShippingDate_Model_Weekend
	 */
	protected function _initWeekend(){
		$weekendId  = (int) $this->getRequest()->getParam('id');
		$weekend	= Mage::getModel('shippingdate/weekend');
		if ($weekendId) {
			$weekend->load($weekendId);
		}
		Mage::register('current_weekend', $weekend);
		return $weekend;
	}
 	/**
	 * default action
	 * @access public
	 * @return void
	 * @author Ultimate Module Creator
	 */
	public function indexAction() {
		$this->loadLayout();
		$this->_title(Mage::helper('shippingdate')->__('ShippingDate'))
			 ->_title(Mage::helper('shippingdate')->__('Weekends'));
		$this->renderLayout();
	}
	/**
	 * grid action
	 * @access public
	 * @return void
	 * @author Ultimate Module Creator
	 */
	public function gridAction() {
		$this->loadLayout()->renderLayout();
	}
	/**
	 * edit weekend - action
	 * @access public
	 * @return void
	 * @author Ultimate Module Creator
	 */
	public function editAction() {
		$weekendId	= $this->getRequest()->getParam('id');
		$weekend  	= $this->_initWeekend();
		if ($weekendId && !$weekend->getId()) {
			$this->_getSession()->addError(Mage::helper('shippingdate')->__('This weekend no longer exists.'));
			$this->_redirect('*/*/');
			return;
		}
		$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
		if (!empty($data)) {
			$weekend->setData($data);
		}
		Mage::register('weekend_data', $weekend);
		$this->loadLayout();
		$this->_title(Mage::helper('shippingdate')->__('ShippingDate'))
			 ->_title(Mage::helper('shippingdate')->__('Weekends'));
		if ($weekend->getId()){
			$this->_title($weekend->getTitle());
		}
		else{
			$this->_title(Mage::helper('shippingdate')->__('Add weekend'));
		}
		if (Mage::getSingleton('cms/wysiwyg_config')->isEnabled()) { 
			$this->getLayout()->getBlock('head')->setCanLoadTinyMce(true); 
		}
		$this->renderLayout();
	}
	/**
	 * new weekend action
	 * @access public
	 * @return void
	 * @author Ultimate Module Creator
	 */
	public function newAction() {
		$this->_forward('edit');
	}
	/**
	 * save weekend - action
	 * @access public
	 * @return void
	 * @author Ultimate Module Creator
	 */
	public function saveAction() {
		if ($data = $this->getRequest()->getPost('weekend')) {
			try {
				$weekend = $this->_initWeekend();
				$weekend->addData($data);
				$weekend->save();
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('shippingdate')->__('Weekend was successfully saved'));
				Mage::getSingleton('adminhtml/session')->setFormData(false);
				if ($this->getRequest()->getParam('back')) {
					$this->_redirect('*/*/edit', array('id' => $weekend->getId()));
					return;
				}
				$this->_redirect('*/*/');
				return;
			} 
			catch (Mage_Core_Exception $e){
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				Mage::getSingleton('adminhtml/session')->setFormData($data);
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
				return;
			}
			catch (Exception $e) {
				Mage::logException($e);
				Mage::getSingleton('adminhtml/session')->addError(Mage::helper('shippingdate')->__('There was a problem saving the weekend.'));
				Mage::getSingleton('adminhtml/session')->setFormData($data);
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
				return;
			}
		}
		Mage::getSingleton('adminhtml/session')->addError(Mage::helper('shippingdate')->__('Unable to find weekend to save.'));
		$this->_redirect('*/*/');
	}
	/**
	 * delete weekend - action
	 * @access public
	 * @return void
	 * @author Ultimate Module Creator
	 */
	public function deleteAction() {
		if( $this->getRequest()->getParam('id') > 0) {
			try {
				$weekend = Mage::getModel('shippingdate/weekend');
				$weekend->setId($this->getRequest()->getParam('id'))->delete();
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('shippingdate')->__('Weekend was successfully deleted.'));
				$this->_redirect('*/*/');
				return; 
			}
			catch (Mage_Core_Exception $e){
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
			}
			catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError(Mage::helper('shippingdate')->__('There was an error deleteing weekend.'));
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
				Mage::logException($e);
				return;
			}
		}
		Mage::getSingleton('adminhtml/session')->addError(Mage::helper('shippingdate')->__('Could not find weekend to delete.'));
		$this->_redirect('*/*/');
	}
	/**
	 * mass delete weekend - action
	 * @access public
	 * @return void
	 * @author Ultimate Module Creator
	 */
	public function massDeleteAction() {
		$weekendIds = $this->getRequest()->getParam('weekend');
		if(!is_array($weekendIds)) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('shippingdate')->__('Please select weekends to delete.'));
		}
		else {
			try {
				foreach ($weekendIds as $weekendId) {
					$weekend = Mage::getModel('shippingdate/weekend');
					$weekend->setId($weekendId)->delete();
				}
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('shippingdate')->__('Total of %d weekends were successfully deleted.', count($weekendIds)));
			}
			catch (Mage_Core_Exception $e){
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
			}
			catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError(Mage::helper('shippingdate')->__('There was an error deleteing weekends.'));
				Mage::logException($e);
			}
		}
		$this->_redirect('*/*/index');
	}
	/**
	 * mass status change - action
	 * @access public
	 * @return void
	 * @author Ultimate Module Creator
	 */
	public function massStatusAction(){
		$weekendIds = $this->getRequest()->getParam('weekend');
		if(!is_array($weekendIds)) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('shippingdate')->__('Please select weekends.'));
		} 
		else {
			try {
				foreach ($weekendIds as $weekendId) {
				$weekend = Mage::getSingleton('shippingdate/weekend')->load($weekendId)
							->setStatus($this->getRequest()->getParam('status'))
							->setIsMassupdate(true)
							->save();
				}
				$this->_getSession()->addSuccess($this->__('Total of %d weekends were successfully updated.', count($weekendIds)));
			}
			catch (Mage_Core_Exception $e){
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
			}
			catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError(Mage::helper('shippingdate')->__('There was an error updating weekends.'));
				Mage::logException($e);
			}
		}
		$this->_redirect('*/*/index');
	}
	/**
	 * export as csv - action
	 * @access public
	 * @return void
	 * @author Ultimate Module Creator
	 */
	public function exportCsvAction(){
		$fileName   = 'weekend.csv';
		$content	= $this->getLayout()->createBlock('shippingdate/adminhtml_weekend_grid')->getCsv();
		$this->_prepareDownloadResponse($fileName, $content);
	}
	/**
	 * export as MsExcel - action
	 * @access public
	 * @return void
	 * @author Ultimate Module Creator
	 */
	public function exportExcelAction(){
		$fileName   = 'weekend.xls';
		$content	= $this->getLayout()->createBlock('shippingdate/adminhtml_weekend_grid')->getExcelFile();
		$this->_prepareDownloadResponse($fileName, $content);
	}
	/**
	 * export as xml - action
	 * @access public
	 * @return void
	 * @author Ultimate Module Creator
	 */
	public function exportXmlAction(){
		$fileName   = 'weekend.xml';
		$content	= $this->getLayout()->createBlock('shippingdate/adminhtml_weekend_grid')->getXml();
		$this->_prepareDownloadResponse($fileName, $content);
	}
}