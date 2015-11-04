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
 * ShippingDate module install script
 *
 * @category	Amasty
 * @package		Amasty_ShippingDate
 * @author Ultimate Module Creator
 */
$this->startSetup();
$table = $this->getConnection()
	->newTable($this->getTable('shippingdate/weekend'))
	->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
		'identity'  => true,
		'nullable'  => false,
		'primary'   => true,
		), 'Weekend ID')
	->addColumn('title', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
		'nullable'  => false,
		), 'Title')

	->addColumn('date_from', Varien_Db_Ddl_Table::TYPE_DATETIME, 255, array(
		'nullable'  => false,
		), 'Date From')

	->addColumn('date_to', Varien_Db_Ddl_Table::TYPE_DATETIME, 255, array(
		'nullable'  => false,
		), 'Date To')

	->addColumn('comment', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
		), 'Comment')

	->addColumn('status', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
		), 'Status')

	->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
		), 'Weekend Creation Time')
	->addColumn('updated_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
		), 'Weekend Modification Time')
	->setComment('Weekend Table');
$this->getConnection()->createTable($table);

$table = $this->getConnection()
	->newTable($this->getTable('shippingdate/weekend_store'))
	->addColumn('weekend_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
		'nullable'  => false,
		'primary'   => true,
		), 'Weekend ID')
	->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
		'unsigned'  => true,
		'nullable'  => false,
		'primary'   => true,
		), 'Store ID')
	->addIndex($this->getIdxName('shippingdate/weekend_store', array('store_id')), array('store_id'))
	->addForeignKey($this->getFkName('shippingdate/weekend_store', 'weekend_id', 'shippingdate/weekend', 'entity_id'), 'weekend_id', $this->getTable('shippingdate/weekend'), 'entity_id', Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
	->addForeignKey($this->getFkName('shippingdate/weekend_store', 'store_id', 'core/store', 'store_id'), 'store_id', $this->getTable('core/store'), 'store_id', Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
	->setComment('Weekends To Store Linkage Table');
$this->getConnection()->createTable($table);
$this->endSetup();