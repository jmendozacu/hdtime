<?php
class Komplizierte_CustomerCallbacks_Block_Adminhtml_Callbacks extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct() {
        $this->_controller     = "adminhtml_callbacks";
        $this->_blockGroup     = "komplizierte_customercallbacks";
        $this->_headerText     = Mage::helper("komplizierte_customercallbacks")->__("Customer Callbacks Manager");
        parent::__construct();
    }

}