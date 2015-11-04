<?php
/**
 * Class Me_Cmb create me_cmb table
 *
 * @category  Me
 * @package   Me_Cmb
 * @author    Attila Sági <sagi.attila@aion.hu>
 * @copyright 2015 Magevolve Ltd. (http://magevolve.com)
 * @license   http://magevolve.com/terms-and-conditions Magevolve License
 * @link      http://magevolve.com
 */

/**
 * @var $installer Mage_Core_Model_Resource_Setup
 */
$installer = $this;

/**
 * Create me_cmb table
 */

if (!$installer->tableExists($installer->getTable('me_cmb/cmb'))) {

    $table = $installer->getConnection()
        ->newTable($installer->getTable('me_cmb/cmb'))
        ->addColumn(
            'cmb_id',
            Varien_Db_Ddl_Table::TYPE_INTEGER,
            null,
            array(
                'unsigned' => true,
                'identity' => true,
                'nullable' => false,
                'primary' => true,
            ),
            'Entity id'
        )
        ->addColumn(
            'cmb_full_name',
            Varien_Db_Ddl_Table::TYPE_TEXT,
            255,
            array(
                'nullable' => true,
                'default' => null,
            ),
            'Full Name'
        )
        ->addColumn(
            'cmb_telephone',
            Varien_Db_Ddl_Table::TYPE_TEXT,
            255,
            array(
                'nullable' => true,
                'default' => null,
            ),
            'Telephone'
        )
        ->addColumn(
            'cmb_call_date',
            Varien_Db_Ddl_Table::TYPE_DATE,
            '64k',
            array(
                'nullable' => true,
                'default' => null,
            ),
            'Call Date'
        )
        ->addColumn(
            'cmb_predefined',
            Varien_Db_Ddl_Table::TYPE_TEXT,
            null,
            array(
                'nullable' => true,
                'default' => null,
            ),
            'Predefined Time'
        )
        ->addColumn(
            'status',
            Varien_Db_Ddl_Table::TYPE_SMALLINT,
            null,
            array(
                'nullable' => false,
                'default' => 1,
            ),
            'Status'
        )
        ->addColumn(
            'store_id',
            Varien_Db_Ddl_Table::TYPE_INTEGER,
            null,
            array(
                'unsigned' => true,
                'nullable' => false,
            ),
            'Store Id'
        )
        ->addColumn(
            'posted_at',
            Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
            null,
            array(
                'nullable' => true,
                'default' => null,
            ),
            'Post Time'
        )
        ->addIndex(
            $installer->getIdxName(
                $installer->getTable('me_cmb/cmb'),
                array('posted_at'),
                Varien_Db_Adapter_Interface::INDEX_TYPE_INDEX
            ),
            array('posted_at'),
            array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_INDEX)
        )
        ->addForeignKey(
            $installer->getFkName('me_cmb/cmb', 'store_id', 'core/store', 'store_id'),
            'store_id',
            $installer->getTable('core/store'),
            'store_id',
            Varien_Db_Ddl_Table::ACTION_CASCADE,
            Varien_Db_Ddl_Table::ACTION_CASCADE
        )
        ->setComment('CMB Item');

    $installer->getConnection()->createTable($table);
}
