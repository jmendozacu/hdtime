<?php
class Komplizierte_CustomerCallbacks_Block_Adminhtml_Callbacks_Grid extends Mage_Adminhtml_Block_Widget_Grid {
    public function __construct() {
        parent::__construct();
        $this->setId("customerCallbacksGrid");
        $this->setDefaultSort("id");
        $this->setUseAjax(true);
        $this->setDefaultDir("ASC");
        $this->setSaveParametersInSession(true);
    }

    public function getRowUrl($callbacks) {
        return $this->getUrl('*/*/edit', array('id' => $callbacks->getId()));
    }

    public function getGridUrl() {
        return $this->getUrl('*/*/grid', array('_current' => true));
    }

    protected function _prepareCollection() {
        $collection = Mage::getModel("komplizierte_customercallbacks/callbacks")->getCollection();
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    protected function _prepareColumns() {
        $this->addColumn("id", array(
            "header" => Mage::helper("komplizierte_customercallbacks")->__("ID"),
            "align"  => "right",
            "width"  => "50px",
            "type"   => "number",
            "index"  => "id",
        ));

        $this->addColumn("name", array(
            "header" => Mage::helper("komplizierte_customercallbacks")->__("Customer Name"),
            "index"  => "name",
        ));

        $this->addColumn("phone", array(
            "header" => Mage::helper("komplizierte_customercallbacks")->__("Customer Phone"),
            "index"  => "phone",
        ));

        $this->addColumn("status", array(
            "header" => Mage::helper("komplizierte_customercallbacks")->__("Status"),
            "index"  => "status",
            'type'   => 'options',
            'options'=> Mage::getModel('komplizierte_customercallbacks/callbacks')->getStatuses()
        ));

        $this->addColumn("created_at", array(
            "header" => Mage::helper("komplizierte_customercallbacks")->__("Created At"),
            "index"  => "created_at",
        ));


        $this->addColumn('action', array(
            'header'   => Mage::helper('komplizierte_customercallbacks')->__('Action'),
            'width'    => '50px',
            'type'     => 'action',
            'getter'   => 'getId',
            'actions'  => array(
                array(
                    'caption' => Mage::helper('komplizierte_customercallbacks')->__('Edit'),
                    'url'     => array(
                        'base'   => '*/*/edit',
                        'params' => array('store' => $this->getRequest()->getParam('store'))
                    ),
                    'field'   => 'id'
                )
            ),
            'filter'   => false,
            'sortable' => false,
            'index'    => 'stores',
        ));

        return parent::_prepareColumns();
    }

    protected function _prepareMassaction() {
        $this->setMassactionIdField('id');
        $this->getMassactionBlock()->setFormFieldName('ids');
        $this->getMassactionBlock()->setUseSelectAll(true);
        $this->getMassactionBlock()->addItem('remove_callbacks', array(
            'label'   => Mage::helper('komplizierte_customercallbacks')->__('Remove Callbacks'),
            'url'     => $this->getUrl('*/callbacks/massRemove'),
            'confirm' => Mage::helper('komplizierte_customercallbacks')->__('Are you sure?')
        ));

        return $this;
    }
}