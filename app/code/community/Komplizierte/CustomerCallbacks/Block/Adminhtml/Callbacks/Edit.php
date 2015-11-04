<?php
class Komplizierte_CustomerCallbacks_Block_Adminhtml_Callbacks_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    protected function _construct()
    {
        $this->_blockGroup = 'komplizierte_customercallbacks';
        $this->_mode = 'callbacks_form';
        $this->_controller = 'adminhtml';
    }

    public function getHeaderText()
    {
        $callback = Mage::registry('callbacks_data');
        if ($callback->getId()) {
            return Mage::helper('komplizierte_customercallbacks')->__("Edit Callback '%s'", $this->escapeHtml($callback->getId()));
        } else {
            return Mage::helper('komplizierte_customercallbacks')->__("Add new Callback");
        }
    }
}