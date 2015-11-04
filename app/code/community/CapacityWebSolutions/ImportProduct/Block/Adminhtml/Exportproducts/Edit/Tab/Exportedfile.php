<?php
/***************************************************************************
	@extension	: Import/Export Orders.
	@copyright	: Copyright (c) 2014 Capacity Web Solutions.
	( http://www.capacitywebsolutions.com )
	@author		: Capacity Web Solutions Pvt. Ltd.
	@support	: magento@capacitywebsolutions.com	
***************************************************************************/
class CapacityWebSolutions_ImportProduct_Block_Adminhtml_Exportproducts_Edit_Tab_Exportedfile extends Mage_Adminhtml_Block_Widget_Grid
{
  	
  	public function __construct() {
		parent::__construct();
        $this->setDefaultDir('desc');
        $this->setUseAjax(false);
		$this->setId('exportedfilegrid');	
	}
	
	protected function _prepareLayout()
    {
        return parent::_prepareLayout();
    }	
	
	protected function _prepareCollection()	{
		$collection = Mage::getModel('importproduct/exportedfile')->getCollection(); 
		$this->setCollection($collection);
		parent::_prepareCollection();
		return $this;
	}

	
	protected function _prepareColumns() {

	    $this->addColumn('exported_file_date_time', array(
            'header'    => Mage::helper('adminhtml')->__('File Created At '),
            'index'     => 'exported_file_date_time',
			'type'      => 'date',
			'time' => true,
			'filter'   => false,			
			'format' => 'dd/MM/yyyy hh:mm:ss',			
        ));
		
        $this->addColumn('exported_file_name', array(
            'header'    => Mage::helper('adminhtml')->__('Exported File Name'),
            'index'     => 'file_name'
        ));

		$this->addColumn('action_delete', array(
			'header'   => $this->helper('catalog')->__('Delete'),
			'width'    => 200,
			'sortable' => false,
			'filter'   => false,
			'frame_callback' => array($this, 'addDeleteLink'),
		));

		
		$this->addColumn('action_download', array(
			'header'   => $this->helper('catalog')->__('Download'),
			'width'    => 200,
			'sortable' => false,
			'filter'   => false,
			'frame_callback' => array($this, 'addDownloadLink'),
		));
	
        return parent::_prepareColumns();
	}
	
	public function addDeleteLink($value, $row, $column, $isExport)
    {
		return '<a href="'.$this->getUrl('*/*/deleteExportedFileFromGrid', array('id' => $row->getExportId())).'">Delete</a>';
    }

	public function addDownloadLink($value, $row, $column, $isExport)
    {
		return '<a href="'.$this->getUrl('*/*/downloadExportedFileFromGrid', array('id' => $row->getExportId())).'">Download</a>';
    }	
	
	public function getRowUrl($row)
    {
        return $this->getUrl('*/*/downloadExportedFileFromGrid', array('id' => $row->getExportId()));
    }
}