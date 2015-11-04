<?php
class Raveinfosys_Showmap_Block_Adminhtml_Showmap extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_showmap';
    $this->_blockGroup = 'showmap';
    $this->_headerText = Mage::helper('showmap')->__('sitemap');
    $this->_addButtonLabel = Mage::helper('showmap')->__('site map');
    parent::__construct();
  }
}