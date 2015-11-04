<?php
/***************************************************************************
	@extension	: Import Products categories, multiple images and custom options
	@copyright	: Copyright (c) 2014 Capacity Web Solutions.
	( http://www.capacitywebsolutions.com )
	@author		: Capacity Web Solutions Pvt. Ltd.
	@support	: magento@capacitywebsolutions.com
	
***************************************************************************/

class CapacityWebSolutions_ImportProduct_Block_Adminhtml_Importproducts_Edit_Tab_Runprofile extends Mage_Adminhtml_Block_Widget_Form
{

    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('importproduct/run.phtml');
    }

    public function getRunButtonHtml()
    {

	    $html .= $this->getLayout()->createBlock('adminhtml/widget_button')->setType('button')
            ->setClass('save')->setLabel($this->__('Run Profile'))
            ->setOnClick('runProfile()')
            ->toHtml();
			                
		return $html;
    }

    public function getValidateButtonHtml()
    {

	    $html .= $this->getLayout()->createBlock('adminhtml/widget_button')->setType('button')
            ->setClass('save')->setLabel($this->__('Validate & Import File Data'))
            ->setOnClick('validateData()')
            ->toHtml();
			                
		return $html;
    }	
	
    public function getProfileId()
    {	
        return $this->getIm();
    }
	
	public function getIm()
	{
		$url=Mage::getModel('importproduct/profile')->getImportUrl();
		if($url[0]['profile_id']=='')
		{
		echo "Product Import Entity Not Founds.";
		exit;
		}
		return $url[0]['profile_id'];				
	}

    public function getImportedCSVFiles()
    {
	
        $files = array();
        $path = Mage::app()->getConfig()->getTempVarDir().'/import';
        if (!is_readable($path)) {
            return $files;
        }
        $dir = dir($path);
        while (false !== ($entry = $dir->read())) {
            if($entry != '.'
               && $entry != '..'
               && in_array(strtolower(substr($entry, strrpos($entry, '.')+1)), array($this->getParseType())))
            {
                $files[] = $entry;
            }
        }
        sort($files);		
        $dir->close();
        return $files;
    }
	
    public function getImportedXMLFiles()
    {
	
        $files = array();
        $path = Mage::app()->getConfig()->getTempVarDir().'/import';
        if (!is_readable($path)) {
            return $files;
        }
        $dir = dir($path);
        while (false !== ($entry = $dir->read())) {
            if($entry != '.'
               && $entry != '..'
               && in_array(strtolower(substr($entry, strrpos($entry, '.')+1)), array('xml')))
            {
                $files[] = $entry;
            }
        }
        sort($files);		
        $dir->close();
        return $files;
    }	

    public function getParseType()
    {
            return 'csv';
    }

}