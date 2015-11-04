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
 * Weekend admin edit tabs
 *
 * @category	Amasty
 * @package		Amasty_ShippingDate
 * @author Ultimate Module Creator
 */
class Amasty_ShippingDate_Block_Adminhtml_Weekend_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs{
	/**
	 * constructor
	 * @access public
	 * @return void
	 * @author Ultimate Module Creator
	 */
	public function __construct(){
		parent::__construct();
		$this->setId('weekend_tabs');
		$this->setDestElementId('edit_form');
		$this->setTitle(Mage::helper('shippingdate')->__('Weekend'));
	}
	/**
	 * before render html
	 * @access protected
	 * @return Amasty_ShippingDate_Block_Adminhtml_Weekend_Edit_Tabs
	 * @author Ultimate Module Creator
	 */
	protected function _beforeToHtml(){
		$this->addTab('form_weekend', array(
			'label'		=> Mage::helper('shippingdate')->__('Weekend'),
			'title'		=> Mage::helper('shippingdate')->__('Weekend'),
			'content' 	=> $this->getLayout()->createBlock('shippingdate/adminhtml_weekend_edit_tab_form')->toHtml(),
		));
		if (!Mage::app()->isSingleStoreMode()){
			$this->addTab('form_store_weekend', array(
				'label'		=> Mage::helper('shippingdate')->__('Store views'),
				'title'		=> Mage::helper('shippingdate')->__('Store views'),
				'content' 	=> $this->getLayout()->createBlock('shippingdate/adminhtml_weekend_edit_tab_stores')->toHtml(),
			));
		}
		return parent::_beforeToHtml();
	}
}