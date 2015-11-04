<?php
class Komplizierte_Unisender_Model_System_Config_Source_Subscribe_Mode
{
    public function toOptionArray()
    {
        $options = array();
        $options[] = array('value'=>0,'label'=>Mage::helper('komplizierte_unisender')->__('Require Subscribe'));
        $options[] = array('value'=>1,'label'=>Mage::helper('komplizierte_unisender')->__('Do not need a confirmation if the limit is not exceeded'));
        $options[] = array('value'=>2,'label'=>Mage::helper('komplizierte_unisender')->__('Do not need a confirmation. Return Error if limit exceeded'));
        $options[] = array('value'=>3,'label'=>Mage::helper('komplizierte_unisender')->__('Do not need a confirmation. Add with status New if limit exceeded'));
        return $options;
    }
}