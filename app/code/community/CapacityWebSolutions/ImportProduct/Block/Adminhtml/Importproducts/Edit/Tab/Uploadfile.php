<?php
/***************************************************************************
	@extension	: Import Products categories, multiple images and custom options
	@copyright	: Copyright (c) 2014 Capacity Web Solutions.
	( http://www.capacitywebsolutions.com )
	@author		: Capacity Web Solutions Pvt. Ltd.
	@support	: magento@capacitywebsolutions.com
	
***************************************************************************/

class CapacityWebSolutions_ImportProduct_Block_Adminhtml_Importproducts_Edit_Tab_Uploadfile extends Mage_Adminhtml_Block_Widget_Form
{
        public function __construct()
    {
        parent::__construct();
        $this->setTemplate('importproduct/upload.phtml');
    }

    public function getPostMaxSize()
    {
        return ini_get('post_max_size');
    }
	
	public function getMaxExecutionTime()
    {
        return ini_get('max_execution_time');
    }

	

    public function getUploadMaxSize()
    {
        return ini_get('upload_max_filesize');
    }

    public function getDataMaxSize()
    {
        return min($this->getPostMaxSize(), $this->getUploadMaxSize());
    }
}