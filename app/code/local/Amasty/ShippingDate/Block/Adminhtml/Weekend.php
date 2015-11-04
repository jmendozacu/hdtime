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
 * Weekend admin block
 *
 * @category	Amasty
 * @package		Amasty_ShippingDate
 * @author Ultimate Module Creator
 */
class Amasty_ShippingDate_Block_Adminhtml_Weekend extends Mage_Adminhtml_Block_Widget_Grid_Container{
	/**
	 * constructor
	 * @access public
	 * @return void
	 * @author Ultimate Module Creator
	 */
	public function __construct(){
		$this->_controller 		= 'adminhtml_weekend';
		$this->_blockGroup 		= 'shippingdate';
		$this->_headerText 		= Mage::helper('shippingdate')->__('Weekend');
		$this->_addButtonLabel 	= Mage::helper('shippingdate')->__('Add Weekend');
		parent::__construct();
	}
}