<?php

class Raveinfosys_Showmap_Block_Adminhtml_Showmap_Edit_Config extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('showmap_config');
      $this->setDestElementId('config_form');
      $this->setTitle(Mage::helper('showmap')->__('Configuration'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
         'label'     => Mage::helper('showmap')->__('Configuration'),
         'title'     => Mage::helper('showmap')->__('Sitemap'),
        // 'content'   => $this->getLayout()->createBlock('showmap/adminhtml_showmap_edit_tab_config')->toHtml(),
      ));
     
      return parent::_beforeToHtml();
  }
}