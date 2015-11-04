<?php
/***************************************************************************
	@extension	: Import Products categories, multiple images and custom options
	@copyright	: Copyright (c) 2014 Capacity Web Solutions.
	( http://www.capacitywebsolutions.com )
	@author		: Capacity Web Solutions Pvt. Ltd.
	@support	: magento@capacitywebsolutions.com
	
***************************************************************************/

class CapacityWebSolutions_ImportProduct_Block_Adminhtml_Importproducts_Edit_Tab_Importlog extends Mage_Adminhtml_Block_Widget_Grid
{
  	public function __construct() {
		 parent::__construct();
        $this->setId('permissionsUserGrid');
        $this->setDefaultDir('asc');
        $this->setUseAjax(true);
		$this->setId('importloggrid');
	}
	
	protected $_flag=false;
	protected $_count=0;
	
	protected function _prepareCollection()	{

		$collection = Mage::getModel('importproduct/importlog')->getCollection(); 
		$collection_products = Mage::getModel('importproduct/profiler')->getCollection();
		$collection_products->addFieldToFilter('validate','1');	
		//print_r($collection);exit;
		$this->setCollection($collection);
		 parent::_prepareCollection();
		if(Mage::app()->getRequest()->getParam('show_import_alert_box')=='true' && !$this->_isExport)
		{
			$this->_flag=true;
			$this->_count=count($collection);
		}
		return $this;
	}

	
	protected function _prepareColumns() {
		  $this->addColumn('log_id', array(
            'header'    => Mage::helper('adminhtml')->__('Log ID'),
            'width'     => 5,
            'align'     => 'right',
            'sortable'  => true,
            'index'     => 'log_id'
        ));

        $this->addColumn('product_sku', array(
            'header'    => Mage::helper('adminhtml')->__('Product SKU'),
            'index'     => 'product_sku'
        ));

        $this->addColumn('error_information', array(
            'header'    => Mage::helper('adminhtml')->__('Error'),
            'index'     => 'error_information'
        ));
	
		
		$this->addColumn('error_type', array(
            'header'    => Mage::helper('adminhtml')->__('Error Level'),
            'index'     => 'error_type', 
            'width'     => '100px',
        	'type'      => 'options',
            'options'   => array('0'=>'Minor','1'=>'Major') ,
            'frame_callback' => array($this, 'decorateStatus')
			
        ));

		$this->addExportType('*/*/exportImportCsv',
         Mage::helper('adminhtml')->__('CSV'));			
		
        return parent::_prepareColumns();
	}
	
	public function decorateStatus($value, $row, $column, $isExport)
    {

            if ($value=='Major') {
                $cell = '<span class="grid-severity-critical"><span>'.$value.'</span></span>';
            } else {
                $cell = '<span class="grid-severity-minor"><span>'.$value.'</span></span>';
            }

			return $cell;
    }
	
	
	protected function _exportCsvItem(Varien_Object $item, Varien_Io_File $adapter)
    {
        $row = array();
        foreach ($this->_columns as $column) {
            if (!$column->getIsSystem()) {
                $row[] = html_entity_decode(strip_tags($column->getRowFieldExport($item)));
            }
        }
        $adapter->streamWriteCsv($row);
    }
	
	public function getGridUrl()
    {
        return $this->getUrl('*/*/importlog', array('_current' => true));
    }
	
	 public function getMainButtonsHtml()
    {
        $html = '';
        if($this->getFilterVisibility()){
            $html.= $this->getResetFilterButtonHtml();
            $html.= $this->getSearchButtonHtml();
        }
        return $html;
    }
	
 	public function _toHtml()
    {
			if($this->_flag && Mage::app()->getRequest()->getParam('isAjax')!=true)
			{
				$importlog = Mage::getResourceModel('importproduct/importlog_collection');
				$_total_error_count=$importlog->getSize();
				
				$totalRecords=Mage::app()->getRequest()->getParam('totalRecords');
				$countOfError=Mage::app()->getRequest()->getParam('countOfError');
				$properlyImported=$totalRecords-$countOfError;
				
				$cust_html="<ul class='messages'>
					<li class='success-msg'>
						<ul>
							<li><span>Please check detailed products import log bellow's</span></li>
							<li><span>Total Products: ".$totalRecords." . No of Products Imported properly : ".$properlyImported." .</span></li>					
						</ul>
					</li>
					<li class='error-msg'><span>No of Products not Imported properly: ".$countOfError.", Total errors: ".$_total_error_count." </span></li>							
				</ul>";

			}	
        return $cust_html.parent::_toHtml();
    }
 
	
	public function getImportButtonHtml()
    {
        return $this->getChildHtml('import_button');
    }
	
	protected function _prepareLayout()
    {
        $this->setChild('import_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'     => Mage::helper('adminhtml')->__('Import Products'),
                    'onclick'   => 'runImport()',
                    'class'   => 'task'
                ))
        );
        $this->setChild('reset_filter_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'     => Mage::helper('adminhtml')->__('Reset Filter'),
                    'onclick'   => $this->getJsObjectName().'.resetFilter()',
                ))
        );
        $this->setChild('search_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'     => Mage::helper('adminhtml')->__('Search'),
                    'onclick'   => $this->getJsObjectName().'.doFilter()',
                    'class'   => 'task'
                ))
        );
        return parent::_prepareLayout();
    }
    
	
}