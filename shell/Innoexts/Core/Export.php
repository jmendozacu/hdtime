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

require_once rtrim(dirname(__FILE__), '/').'/Abstract.php';

/**
 * Export
 * 
 * @category   Innoexts
 * @package    Innoexts_Shell
 * @author     Innoexts Team <developers@innoexts.com>
 */
abstract class Innoexts_Shell_Core_Export 
    extends Innoexts_Shell_Core_Abstract 
{
    /**
     * File config
     * 
     * @var array
     */
    protected $_fileConfig = array(
        'path'          => '/var/export/', 
        'filename'      => 'localfilename', 
        'delimiter'     => ',', 
        'enclosure'     => '"', 
    );
    /**
     * Parse arguments
     * 
     * @return bool
     */
    protected function parseArgs()
    {
        return $this->parseFileArgs();
    }
    /**
     * Get field names
     * 
     * @return array
     */
    abstract protected function getFieldNames();
    /**
     * Get rows
     * 
     * @return array
     */
    abstract protected function getRows();
    /**
     * Export
     * 
     * @return Innoexts_Shell_Core_Export
     */
    protected function export()
    {
        if (!$this->parseArgs()) {
            return $this;
        }
        $this->printMessage('Exporting to data file...');
        $file               = $this->getFile();
        if (!$file) {
            return $this;
        }
        $config             = $this->getFileConfig();
        try {
            $file->streamOpen($this->getFileFilename(), 'w');
            $fieldNames         = $this->getFieldNames();
            $file->streamWriteCsv($fieldNames, $config['delimiter'], $config['enclosure']);
            foreach ($this->getRows() as $row) {
                $csvDatum           = array();
                foreach ($fieldNames as $index => $fieldName) {
                    if (isset($row[$fieldName])) {
                        $csvDatum[$index]   = $row[$fieldName];
                    } else {
                        $csvDatum[$index]   = null;
                    }
                }
                $file->streamWriteCsv($csvDatum, $config['delimiter'], $config['enclosure']);
            }
            $this->printMessage('Exported.');
            $file->streamClose();
        } catch (Exception $e) {
            $this->printMessage($e->getMessage());
        }
        return $this;
    }
    /**
     * Run script
     */
    public function run()
    {
        if (!$this->getArg('help')) {
            $this->export();
        } else {
            $this->printHelp();
        }
    }
    /**
     * Get help message
     */
    public function usageHelp()
    {
        return <<<USAGE
Usage:  php -f Export.php -- [options]
  
  file-path <file-path>                         File path
  file-filename <file-filename>                 File filename
  file-csv-delimiter <file-csv-delimiter>       File CSV delimiter
  file-csv-enclosure <file-csv-enclosure>       File CSV enclosure
  
  help                                          This help
USAGE;
    }
}