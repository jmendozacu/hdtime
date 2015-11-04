<?php
/**
 * Innoexts
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the InnoExts Commercial License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://innoexts.com/commercial-license-agreement
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@innoexts.com so we can send you a copy immediately.
 * 
 * @category    Innoexts
 * @package     Innoexts_Shell
 * @copyright   Copyright (c) 2014 Innoexts (http://www.innoexts.com)
 * @license     http://innoexts.com/commercial-license-agreement  InnoExts Commercial License
 */

require_once rtrim(dirname(__FILE__), '/').'/../../abstract.php';

/**
 * Abstract
 * 
 * @category   Innoexts
 * @package    Innoexts_Shell
 * @author     Innoexts Team <developers@innoexts.com>
 */
abstract class Innoexts_Shell_Core_Abstract 
    extends Mage_Shell_Abstract 
{
    /**
     * New line
     * 
     * @var string
     */
    protected $_nl = "\n";
    /**
     * Tab
     * 
     * @var string 
     */
    protected $_tab = "\t";
    /**
     * File
     * 
     * @var Varien_Io_File
     */
    protected $_file;
    /**
     * File config
     * 
     * @var array
     */
    protected $_fileConfig = array(
        'path'          => '/var/', 
        'filename'      => 'localfilename', 
        'delimiter'     => ',', 
        'enclosure'     => '"', 
    );
    /**
     * Model
     *
     * @var Mage_Core_Model_Abstract
     */
    protected $_model;
    /**
     * Get core helper
     * 
     * @return Innoexts_Core_Helper_Data
     */
    protected function getCoreHelper()
    {
        return Mage::helper('innoexts_core');
    }
    /**
     * Get new line
     * 
     * @return string
     */
    protected function getNl()
    {
        return $this->_nl;
    }
    /**
     * Get tab
     * 
     * @return string
     */
    protected function getTab()
    {
        return $this->_tab;
    }
    /**
     * Get File config
     * 
     * @return array
     */
    protected function getFileConfig()
    {
        return $this->_fileConfig;
    }
    /**
     * Get file name
     * 
     * @return string
     */
    protected function getFileFilename()
    {
        $config = $this->getFileConfig();
        return (isset($config['filename'])) ? $config['filename'] : null;
    }
    /**
     * Get file path
     * 
     * @return string
     */
    protected function getFilePath()
    {
        $config = $this->getFileConfig();
        return Mage::getBaseDir().DS.trim($config['path'], DS).DS.$config['filename'];
    }
    /**
     * Print message
     * 
     * @param string    $message
     * @param bool      $newLine 
     * @param int|null  $tabs
     * 
     * @return Innoexts_Shell_Core_Abstract
     */
    protected function printMessage($message, $newLine = true, $tabs = null)
    {
        if (!is_null($tabs)) {
            $message = str_pad($this->getTab(), (int) $tabs).$message;
        }
        if ($newLine) {
            $message .= $this->getNl();
        }
        echo $message;
        return $this;
    }
    /**
     * Get file
     * 
     * @return Varien_Io_File
     */
    protected function getFile()
    {
        if (is_null($this->_file)) {
            $file               = new Varien_Io_File();
            $config             = $this->getFileConfig();
            $path               = $file->getCleanPath(Mage::getBaseDir().DS.trim($config['path'], DS));
            $file->checkAndCreateFolder($path);
            $config['path']     = rtrim(realpath($path), DS);
            try {
                $file->open($config);
                $this->_file        = $file;
            } catch (Exception $e) {
                $this->printMessage($e->getMessage());
            }
        }
        return $this->_file;
    }
    /**
     * Parse file arguments
     * 
     * @return bool
     */
    protected function parseFileArgs()
    {
        $isParsed = true;
        $filePath = trim($this->getArg('file-path'));
        if (!$filePath) {
            $this->printMessage('File path is required.');
            $isParsed = false;
        } else {
            $this->_fileConfig['path'] = $filePath;
        }
        $fileFilename = trim($this->getArg('file-filename'));
        if (!$fileFilename) {
            $this->printMessage('File filename is required.');
            $isParsed = false;
        } else {
            $this->_fileConfig['filename'] = $fileFilename;
        }
        $fileCsvDelimiter = trim($this->getArg('file-csv-delimiter'));
        if ($fileCsvDelimiter) {
            $this->_fileConfig['delimiter'] = $fileCsvDelimiter;
        }
        $fileCsvEnclosure = trim($this->getArg('file-csv-enclosure'));
        if ($fileCsvEnclosure) {
            $this->_fileConfig['enclosure'] = $fileCsvEnclosure;
        }
        return $isParsed;
    }
    /**
     * Parse arguments
     * 
     * @return bool
     */
    abstract protected function parseArgs();
    /**
     * Get model
     *
     * @return Mage_Core_Model_Abstract
     */
    abstract protected function getModel();
    /**
     * Get resource
     * 
     * @return Mage_Core_Model_Resource_Abstract
     */
    protected function getResource()
    {
        return ($this->getModel()) ? $this->getModel()->getResource() : null;
    }
    /**
     * Get adapter
     * 
     * @return Varien_Db_Adapter_Interface
     */
    protected function getWriteAdapter()
    {
        return $this->getResource()->getWriteConnection();
    }
    /**
     * Get select
     * 
     * @return Varien_Db_Select
     */
    protected function getSelect()
    {
        return $this->getWriteAdapter()->select();
    }
    /**
     * Get data table name
     * 
     * @return string
     */
    abstract protected function getDataTableName();
    /**
     * Get data table 
     * 
     * @return string
     */
    protected function getDataTable()
    {
        return $this->getResource()->getTable($this->getDataTableName());
    }
    /**
     * Get datum conditions
     * 
     * @param array $data
     * 
     * @return string
     */
    abstract protected function getDatumConditions($datum);
    /**
     * Check if datum exists
     * 
     * @param array $datum
     * 
     * @return bool
     */
    protected function isDatumExists($datum)
    {
        $isExists   = false;
        $adapter    = $this->getWriteAdapter();
        $select     = $this->getSelect()
            ->from($this->getDataTable(), array('COUNT(*)'))
            ->where($this->getDatumConditions($datum));
        $query      = $adapter->query($select);
        $count      = (int) $query->fetchColumn();
        if ($count) {
            $isExists = true;
        }
        return $isExists;
    }
    /**
     * Add datum
     * 
     * @param array $datum
     * 
     * @return Innoexts_Shell_Core_Abstract
     */
    protected function addDatum($datum)
    {
        $adapter    = $this->getWriteAdapter();
        $adapter->insert($this->getDataTable(), $datum);
        return $this;
    }
    /**
     * Update datum
     * 
     * @param array $datum
     * 
     * @return Innoexts_Shell_Core_Abstract
     */
    protected function updateDatum($datum)
    {
        $adapter = $this->getWriteAdapter();
        $adapter->update($this->getDataTable(), $datum, $this->getDatumConditions($datum));
        return $this;
    }
    /**
     * Append datum
     * 
     * @param array $batchPrice
     * 
     * @return Innoexts_Shell_Core_Abstract  
     */
    protected function appendDatum($datum)
    {
        if ($this->isDatumExists($datum)) {
            $this->updateDatum($datum);
        } else {
            $this->addDatum($datum);
        }
        return $this;
    }
    /**
     * Remove datum
     * 
     * @param type $datum
     * 
     * @return Innoexts_Shell_Core_Abstract
     */
    protected function removeDatum($datum)
    {
        $adapter = $this->getWriteAdapter();
        $adapter->delete($this->getDataTable(), $this->getDatumConditions($datum));
    }
    /**
     * Print help
     * 
     * @return Innoexts_Shell_Core_Abstract
     */
    protected function printHelp()
    {
        $this->printMessage($this->usageHelp());
        return $this;
    }
}