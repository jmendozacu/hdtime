<?php

class Raveinfosys_Showmap_Block_Adminhtml_Showmap_Edit_Tab_Config extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
     
      $config = Mage::getModel('showmap/config');
	  $row = $config->getRow();
	  $category = $row['category'];
	  $form = new Varien_Data_Form(array(
                                      'id' => 'config_form',
                                      'action' => $this->getUrl('*/*/config', array('id' => $this->getRequest()->getParam('id'))),
                                      'method' => 'post',
        							  'enctype' => 'multipart/form-data'
                                   ));
	  $form->setUseContainer(true); 
      $this->setForm($form);
      $fieldset = $form->addFieldset('showmap_form', array('legend'=>Mage::helper('showmap')->__('Configuration')));
	 
		 
		 
	  if($row['category']=='yes')
	  {
		   $fieldset->addField('category', 'select', array(
            'name'      => 'category',
		    'label'	  => 'Show Categories',
		    'align'     =>'left',
            'values'    => array( 
              array(
                  'value'     => yes,
                  'label'     => Mage::helper('showmap')->__('Yes'),),

              array(
                  'value'     => no,
                  'label'     => Mage::helper('showmap')->__('No')))));
	  }
	  else
	  {
		    $fieldset->addField('category', 'select', array(
            'name'      => 'category',
		    'label'	  => 'Show Categories',
		    'align'     =>'left',
            'values'    => array( 
              array(
                  'value'     => no,
                  'label'     => Mage::helper('showmap')->__('No'),),

              array(
                  'value'     => yes,
                  'label'     => Mage::helper('showmap')->__('Yes')))));
	  }	 
		  
	  if($row['product']=='yes')
	  {				  
		   $fieldset->addField('product', 'select', array(
            'name'      => 'product',
		    'label'	  => 'Show Products',
		    'align'     =>'left',
            'values'    => array(
              array(
                  'value'     => yes,
                  'label'     => Mage::helper('showmap')->__('Yes'),),

              array(
                  'value'     => no,
                  'label'     => Mage::helper('showmap')->__('No')))));
	  }
	  else
	  {
		   $fieldset->addField('product', 'select', array(
            'name'      => 'product',
		    'label'	  => 'Show Products',
		    'align'     =>'left',
            'values'    => array(
              array(
                  'value'     => no,
                  'label'     => Mage::helper('showmap')->__('No'),),

              array(
                  'value'     => yes,
                  'label'     => Mage::helper('showmap')->__('Yes')))));
	  }	  
			
	 
	  if($row['cms']=='yes')
	  {  
		   $fieldset->addField('cms', 'select', array(
            'name'      => 'cms',
		    'label'	  => 'Show CMS Pages',
		    'align'     =>'left',
            'values'    => array(
              array(
                  'value'     => yes,
                  'label'     => Mage::helper('showmap')->__('Yes'),),

              array(
                  'value'     => no,
                  'label'     => Mage::helper('showmap')->__('No')))));
	  }
	  else
	  {
		   $fieldset->addField('cms', 'select', array(
            'name'      => 'cms',
		    'label'	  => 'Show CMS Pages',
		    'align'     =>'left',
            'values'    => array(
              array(
                  'value'     => no,
                  'label'     => Mage::helper('showmap')->__('No'),),

              array(
                  'value'     => yes,
                  'label'     => Mage::helper('showmap')->__('Yes')))));
	  }
	  
	  if($row['other']=='yes')
	  {  	  
	    $fieldset->addField('other', 'select', array(
		'name'      => 'other',
		'label'	  => 'Show Header And Footer',
		'align'     =>'left',
		'values'    => array(
		   array(
			  'value'     => yes,
			  'label'     => Mage::helper('showmap')->__('Yes'),),

		   array(
			  'value'     => no,
			  'label'     => Mage::helper('showmap')->__('No')))));
	  }
	  else
	  {
	    $fieldset->addField('other', 'select', array(
		'name'      => 'other',
		'label'	  => 'Show Header And Footer',
		'align'     =>'left',
		'values'    => array(
		   array(
			  'value'     => no,
			  'label'     => Mage::helper('showmap')->__('No'),),

		   array(
			  'value'     => yes,
			  'label'     => Mage::helper('showmap')->__('Yes')))));
	  }	  
	  $fieldset->addField('submit', 'submit', array(
	  'value'  => 'Save',
	  'after_element_html' => '<small></small>',
	  'style'     => 'background-color:#FF6600; width:70px; color:white;font-family:Arial, Helvetica, sans-serif;
					  font-style:normal; ',
	  'tabindex' => 1
	  ));
	
	  return parent::_prepareForm();
  }


}
  
  
 