<?php
class Komplizierte_CustomerCallbacks_Block_Adminhtml_Callbacks_Form_Form extends Mage_Adminhtml_Block_Widget_Form {
    protected function _prepareForm() {
        $type     = Mage::registry('callbacks_data');
        $form     = new Varien_Data_Form();
        $fieldset = $form->addFieldset('edit_callbacks', array(
             'legend' => Mage::helper('komplizierte_customercallbacks')->__('Customer Callback Details')
        ));

        if ($type->getId()) {
            $fieldset->addField('id', 'hidden', array(
                 'name'     => 'id',
                 'required' => true
            ));
        }

        $fieldset->addField('name', 'text', array(
            'name'     => 'name',
            'label'    => Mage::helper('komplizierte_customercallbacks')->__('Customer Name'),
            'required' => true,
            'readonly' => true
        ));

        $fieldset->addField('phone', 'text', array(
            'name'     => 'phone',
            'label'    => Mage::helper('komplizierte_customercallbacks')->__('Customer Telephone'),
            'required' => true,
            'readonly' => true
        ));

        $fieldset->addField('status', 'select', array(
            'name'     => 'status',
            'label'    => Mage::helper('komplizierte_customercallbacks')->__('Status'),
            'required' => true,
            'values'   => Mage::getModel('komplizierte_customercallbacks/callbacks')->getStatusesGridForm()
        ));

        $fieldset->addField('created_at', 'text', array(
            'name'     => 'created_at',
            'label'    => Mage::helper('komplizierte_customercallbacks')->__('Created At'),
            'required' => true,
            'readonly' => true
        ));

        $form->setMethod('post');
        $form->setUseContainer(true);
        $form->setId('edit_form');
        $form->setAction($this->getUrl('*/*/save'));
        $form->setValues($type->getData());

        $this->setForm($form);
    }
}