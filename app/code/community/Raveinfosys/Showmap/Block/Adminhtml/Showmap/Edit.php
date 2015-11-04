<?php

class Raveinfosys_Showmap_Block_Adminhtml_Showmap_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct(); 
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'showmap';
        $this->_controller = 'adminhtml_showmap';
        
        $this->_updateButton('save', 'label', Mage::helper('showmap')->__('Save'));

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('showmap_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'showmap_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'showmap_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('showmap_data') && Mage::registry('showmap_data')->getId() ) {
            return Mage::helper('showmap')->__("Edit Item '%s'", $this->htmlEscape(Mage::registry('showmap_data')->getTitle()));
        } else {
            return Mage::helper('showmap')->__('Site Map');
        }
    }
}