<?php

class Raveinfosys_Showmap_Block_Adminhtml_Showmap_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $showmap = Mage::getModel('showmap/showmap');
	  $row = $showmap->getRow();
	  if($row['google']=='yes')$google=true;
	  if($row['bing']=='yes')$bing=true;
	  if($row['window']=='yes')$window=true;
	  if($row['yahoo']=='yes')$yahoo=true;
	  
	  $form = new Varien_Data_Form(); 
      $this->setForm($form);
      $fieldset = $form->addFieldset('showmap_form', array('legend'=>Mage::helper('showmap')->__('Site Map')));
	 
	  if($row['ping_interval']!='')
	  {
	    $fieldset->addField('ping_interval', 'text', array(
          'label'     => Mage::helper('showmap')->__('Ping Interval'),
          'class'     => 'validate-greater-than-zero',
          'required'  => true,
          'name'      => 'ping_interval',
		  'value'	  => "".$row['ping_interval'].""));
	  }
	  else
	  { 
	    $fieldset->addField('ping_interval', 'text', array(
          'label'     => Mage::helper('showmap')->__('Ping Interval'),
          'class'     => 'validate-greater-than-zero',
          'required'  => true,
          'name'      => 'ping_interval',
		  'value'	  => '30'));
	  }

	  if($row['format']== 'hours')
	  {
        $fieldset->addField('format', 'select', array(
          'name'      => 'format',
		  'align'     =>'left',
          'values'    => array(
		  array(
                  'value'     => hours,
                  'label'     => Mage::helper('showmap')->__('Hour(s)')),
              array(
                  'value'     => days,
                  'label'     => Mage::helper('showmap')->__('Day(s)'),),

              )));
	  }
	  else
	  {
          $fieldset->addField('format', 'select', array(
          'name'      => 'format',
		  'align'     =>'left',
          'values'    => array(
              array(
                  'value'     => days,
                  'label'     => Mage::helper('showmap')->__('Day(s)'),),

              array(
                  'value'     => hours,
                  'label'     => Mage::helper('showmap')->__('Hour(s)')))));
	  }
		
		
		
		  
		$fieldset->addField('google', 'checkbox', array(
          'label'     => Mage::helper('showmap')->__('Choose Channel'),
          'name'      => 'google',
          'checked' => $google,
          'onclick' => "",
          'onchange' => "",
          'value'  => 'yes',
          'disabled' => false,
          'after_element_html' => '<large>Google</large>',
          'tabindex' => 1
        ));	
		
		$fieldset->addField('bing', 'checkbox', array(
          'name'      => 'bing',
          'checked' => $bing,
          'onclick' => "",
          'onchange' => "",
          'value'  => 'yes',
          'disabled' => false,
          'after_element_html' => '<large>Bing</large>',
          'tabindex' => 1
        ));	
		
		$fieldset->addField('window', 'checkbox', array(
          'name'      => 'window',
          'checked' => $window,
          'onclick' => "",
          'onchange' => "",
          'value'  => 'yes',
          'disabled' => false,
          'after_element_html' => '<large>Window Live</large>',
          'tabindex' => 1
        ));	
		
		$fieldset->addField('yahoo', 'checkbox', array(
          'name'      => 'yahoo',
          'checked' => $yahoo,
          /* 'onclick' => "if(this.value=='yes'){if(this.checked==true){
		  				document.getElementById('yahoo_appid').style.visibility='visible';
						document.getElementById('label').style.visibility='visible';}
						else{
						document.getElementById('yahoo_appid').style.visibility='hidden';
						document.getElementById('label').style.visibility='hidden';
												}}", */
          'onchange' => "",
          'value'  => 'yes',
          'disabled' => false,

          'after_element_html' => '<large>Yahoo</large>',
          'tabindex' => 1
        ));	
		
		
	   /* $fieldset->addField('label', 'label', array(
          'after_element_html' => '<div id ="label" style=visibility:hidden;>Yahoo App Id</div>'));
		 
	   $fieldset->addField('yahoo_appid', 'text', array(
          'label'     => Mage::helper('showmap')->__(''),
		  'label_style' =>  'visibility: hidden;',
          'name'      => 'yahoo_id',
		  'value'	  => ''.$yahoo_id.'',
		  'style'     => 'visibility: hidden;'));	 */
      	  

       return parent::_prepareForm();

  }


}