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
 * Weekend resource model
 *
 * @category	Amasty
 * @package		Amasty_ShippingDate
 * @author Ultimate Module Creator
 */
class Amasty_ShippingDate_Model_Resource_Weekend extends Mage_Core_Model_Resource_Db_Abstract{
	/**
	 * constructor
	 * @access public
	 * @return void
	 * @author Ultimate Module Creator
	 */
	public function _construct(){
		$this->_init('shippingdate/weekend', 'entity_id');
	}
	
	/**
	 * Get store ids to which specified item is assigned
	 * @access public
	 * @param int $weekendId
	 * @return array
	 * @author Ultimate Module Creator
	 */
	public function lookupStoreIds($weekendId){
		$adapter = $this->_getReadAdapter();
		$select  = $adapter->select()
			->from($this->getTable('shippingdate/weekend_store'), 'store_id')
			->where('weekend_id = ?',(int)$weekendId);
		return $adapter->fetchCol($select);
	}
	/**
	 * Perform operations after object load
	 * @access public
	 * @param Mage_Core_Model_Abstract $object
	 * @return Amasty_ShippingDate_Model_Resource_Weekend
	 * @author Ultimate Module Creator
	 */
	protected function _afterLoad(Mage_Core_Model_Abstract $object){
		if ($object->getId()) {
			$stores = $this->lookupStoreIds($object->getId());
			$object->setData('store_id', $stores);
		}
		return parent::_afterLoad($object);
	}

	/**
	 * Retrieve select object for load object data
	 *
	 * @param string $field
	 * @param mixed $value
	 * @param Amasty_ShippingDate_Model_Weekend $object
	 * @return Zend_Db_Select
	 */
	protected function _getLoadSelect($field, $value, $object){
		$select = parent::_getLoadSelect($field, $value, $object);
		if ($object->getStoreId()) {
			$storeIds = array(Mage_Core_Model_App::ADMIN_STORE_ID, (int)$object->getStoreId());
			$select->join(
				array('shippingdate_weekend_store' => $this->getTable('shippingdate/weekend_store')),
				$this->getMainTable() . '.entity_id = shippingdate_weekend_store.weekend_id',
				array()
			)
			->where('shippingdate_weekend_store.store_id IN (?)', $storeIds)
			->order('shippingdate_weekend_store.store_id DESC')
			->limit(1);
		}
		return $select;
	}
	/**
	 * Assign weekend to store views
	 * @access protected
	 * @param Mage_Core_Model_Abstract $object
	 * @return Amasty_ShippingDate_Model_Resource_Weekend
	 * @author Ultimate Module Creator
	 */
	protected function _afterSave(Mage_Core_Model_Abstract $object){
		$oldStores = $this->lookupStoreIds($object->getId());
		$newStores = (array)$object->getStores();
		if (empty($newStores)) {
			$newStores = (array)$object->getStoreId();
		}
		$table  = $this->getTable('shippingdate/weekend_store');
		$insert = array_diff($newStores, $oldStores);
		$delete = array_diff($oldStores, $newStores);
		if ($delete) {
			$where = array(
				'weekend_id = ?' => (int) $object->getId(),
				'store_id IN (?)' => $delete
			);
			$this->_getWriteAdapter()->delete($table, $where);
		}
		if ($insert) {
			$data = array();
			foreach ($insert as $storeId) {
				$data[] = array(
					'weekend_id'  => (int) $object->getId(),
					'store_id' => (int) $storeId
				);
			}
			$this->_getWriteAdapter()->insertMultiple($table, $data);
		}
		return parent::_afterSave($object);
	}}