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
 * Weekend model
 *
 * @category	Amasty
 * @package		Amasty_ShippingDate
 * @author Ultimate Module Creator
 */
class Amasty_ShippingDate_Model_Weekend extends Mage_Core_Model_Abstract{
	/**
	 * Entity code.
	 * Can be used as part of method name for entity processing
	 */
	const ENTITY= 'shippingdate_weekend';
	const CACHE_TAG = 'shippingdate_weekend';
	/**
	 * Prefix of model events names
	 * @var string
	 */
	protected $_eventPrefix = 'shippingdate_weekend';
	
	/**
	 * Parameter name in event
	 * @var string
	 */
	protected $_eventObject = 'weekend';
	/**
	 * constructor
	 * @access public
	 * @return void
	 * @author Ultimate Module Creator
	 */
	public function _construct(){
		parent::_construct();
		$this->_init('shippingdate/weekend');
	}
	/**
	 * before save weekend
	 * @access protected
	 * @return Amasty_ShippingDate_Model_Weekend
	 * @author Ultimate Module Creator
	 */
	protected function _beforeSave(){
		parent::_beforeSave();
		$now = Mage::getSingleton('core/date')->gmtDate();
		if ($this->isObjectNew()){
			$this->setCreatedAt($now);
		}
		$this->setUpdatedAt($now);
		return $this;
	}
	/**
	 * save weekend relation
	 * @access public
	 * @return Amasty_ShippingDate_Model_Weekend
	 * @author Ultimate Module Creator
	 */
	protected function _afterSave() {
		return parent::_afterSave();
	}
}