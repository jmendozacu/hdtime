<?php

class Raveinfosys_Showresponse_Block_Adminhtml_Showresponse_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
      parent::__construct(); 
      $this->setId('showresponseGrid');
      $this->setDefaultSort('showresponse_id');
      $this->setDefaultDir('ASC');
      $this->setSaveParametersInSession(true);
  }

  protected function _prepareCollection()
  {
      $collection = Mage::getModel('showresponse/showresponse')->getCollection();
      $this->setCollection($collection);
      return parent::_prepareCollection();
  }

  protected function _prepareColumns()
  {
      $this->addColumn('showresponse_id', array(
          'header'    => Mage::helper('showresponse')->__('ID'),
          'align'     =>'right',
          'width'     => '10px',
          'index'     => 'showresponse_id',
      ));

      $this->addColumn('google_response', array(
          'header'    => Mage::helper('showresponse')->__('Google Response'),
          'align'     =>'left',
          'index'     => 'google_response',
		  'renderer'	=> 'showresponse/adminhtml_showresponse_description'
      ));
	  
	  $this->addColumn('yahoo_response', array(
          'header'    => Mage::helper('showresponse')->__('Yahoo Response'),
          'align'     =>'left',
          'index'     => 'yahoo_response',
		  'renderer'	=> 'showresponse/adminhtml_showresponse_description'
      ));
	  
	  $this->addColumn('bing_response', array(
          'header'    => Mage::helper('showresponse')->__('Bing Response'),
          'align'     =>'left',
          'index'     => 'bing_response',
		  'renderer'	=> 'showresponse/adminhtml_showresponse_description'
      ));
	  
	  $this->addColumn('window_response', array(
          'header'    => Mage::helper('showresponse')->__('Window Live Response'),
          'align'     =>'left',
          'index'     => 'window_response',
		  'renderer'	=> 'showresponse/adminhtml_showresponse_description'
      ));
	  
	  $this->addColumn('date', array(
          'header'    => Mage::helper('showresponse')->__('Date'),
          'align'     =>'left',
          'index'     => 'date',
      ));

	  
	  
      return parent::_prepareColumns();
  }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('showresponse_id');
        $this->getMassactionBlock()->setFormFieldName('showresponse');

        $this->getMassactionBlock()->addItem('delete', array(
             'label'    => Mage::helper('showresponse')->__('Delete'),
             'url'      => $this->getUrl('*/*/massDelete'),
             'confirm'  => Mage::helper('showresponse')->__('Are you sure?')
        ));

 
        return $this;
    }
	
   public function getRowUrl($row)
   {
     // return $this->getUrl('*/*/edit', array('id' => $row->getId()));
   }


}