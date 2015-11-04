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
 * Weekend admin edit block
 *
 * @category	Amasty
 * @package		Amasty_ShippingDate
 * @author Ultimate Module Creator
 */
class Amasty_ShippingDate_Block_Adminhtml_Weekend_Edit extends Mage_Adminhtml_Block_Widget_Form_Container{
	/**
	 * constuctor
	 * @access public
	 * @return void
	 * @author Ultimate Module Creator
	 */
	public function __construct(){
		parent::__construct();
		$this->_blockGroup = 'shippingdate';
		$this->_controller = 'adminhtml_weekend';
		$this->_updateButton('save', 'label', Mage::helper('shippingdate')->__('Save Weekend'));
		$this->_updateButton('delete', 'label', Mage::helper('shippingdate')->__('Delete Weekend'));
		$this->_addButton('saveandcontinue', array(
			'label'		=> Mage::helper('shippingdate')->__('Save And Continue Edit'),
			'onclick'	=> 'saveAndContinueEdit()',
			'class'		=> 'save',
		), -100);
		$this->_formScripts[] = "
			function saveAndContinueEdit(){
				editForm.submit($('edit_form').action+'back/edit/');
			}
		";
	}
	/**
	 * get the edit form header
	 * @access public
	 * @return string
	 * @author Ultimate Module Creator
	 */
	public function getHeaderText(){
		if( Mage::registry('weekend_data') && Mage::registry('weekend_data')->getId() ) {
			return Mage::helper('shippingdate')->__("Edit Weekend '%s'", $this->htmlEscape(Mage::registry('weekend_data')->getTitle()));
		} 
		else {
			return Mage::helper('shippingdate')->__('Add Weekend');
		}
	}
}