<?php

class Raveinfosys_Showmap_Block_Adminhtml_Showmap_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct(); 
      $this->setId('showmap_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('showmap')->__('Site Map'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('showmap')->__('Site Map'),
          //'title'     => Mage::helper('showmap')->__('Item Information'),
          'content'   => $this->getLayout()->createBlock('showmap/adminhtml_showmap_edit_tab_form')->toHtml(),
      ));
     
      return parent::_beforeToHtml();
  }
}