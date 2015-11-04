<?php
class Raveinfosys_Showresponse_Block_Adminhtml_Showresponse extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_showresponse';
    $this->_blockGroup = 'showresponse';
    $this->_headerText = Mage::helper('showresponse')->__('View Responses');
    parent::__construct();
	$this->removeButton('add');
  }
}