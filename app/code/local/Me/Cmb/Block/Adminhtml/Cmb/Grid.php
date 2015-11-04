<?php
/**
 * Class Me_Cmb_Block_Adminhtml_Cmb_Grid
 *
 * @category  Me
 * @package   Me_Cmb
 * @author    Attila Sági <sagi.attila@aion.hu>
 * @copyright 2015 Magevolve Ltd. (http://magevolve.com)
 * @license   http://magevolve.com/terms-and-conditions Magevolve License
 * @link      http://magevolve.com
 */

/**
 * Class Me_Cmb_Block_Adminhtml_Cmb_Grid
 */
class Me_Cmb_Block_Adminhtml_Cmb_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Init Grid default properties

     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('cmb_list_grid');
        $this->setDefaultSort('posted_at');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }

    /**
     * Prepare collection for Grid
     *
     * @return Me_Cmb_Block_Adminhtml_Cmb_Grid
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('me_cmb/cmb')->getResourceCollection();

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Prepare Grid columns
     *
     * @return Mage_Adminhtml_Block_Catalog_Search_Grid
     */
    protected function _prepareColumns()
    {
        $_helper = Mage::helper('me_cmb');

        $this->addColumn(
            'cmb_id',
            array(
                'header' => $_helper->__('ID'),
                'width' => '50px',
                'index' => 'cmb_id',
            )
        );

        $this->addColumn(
            'cmb_full_name',
            array(
                'header' => $_helper->__('Full Name'),
                'index' => 'cmb_full_name',
            )
        );

        $this->addColumn(
            'cmb_telephone',
            array(
                'header' => $_helper->__('Telephone'),
                'index' => 'cmb_telephone',
            )
        );

        $this->addColumn(
            'cmb_call_date',
            array(
                'header' => $_helper->__('Call Date'),
                'sortable' => true,
                'width' => '170px',
                'index' => 'cmb_call_date',
                'type' => 'date',
                'renderer' => 'Me_Cmb_Block_Adminhtml_Cmb_Renderer_Calldate'
            )
        );

        $this->addColumn(
            'cmb_predefined',
            array(
                'header' => $_helper->__('SKU'),
                'index' => 'cmb_predefined',
                'renderer' => 'Me_Cmb_Block_Adminhtml_Cmb_Renderer_Empty'
            )
        );

        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn(
                'store_id',
                array(
                    'header' => $_helper->__('Store View'),
                    'index' => 'store_id',
                    'type' => 'store',
                    'store_all' => true,
                    'store_view' => true,
                    'sortable' => false,
                    'filter_condition_callback' => array($this, '_filterStoreCondition'),
                )
            );
        }

        $this->addColumn(
            'status',
            array(
                'header' => $_helper->__('Status'),
                'index' => 'status',
                'type' => 'options',
                'options' => Mage::getModel('me_cmb/cmb')->getStatusOptions(),
                'frame_callback' => array($this, 'decorateStatus')
            )
        );

        $this->addColumn(
            'posted_at',
            array(
                'header' => $_helper->__('Post Time'),
                'sortable' => true,
                'width' => '170px',
                'index' => 'posted_at',
                'type' => 'datetime',
            )
        );

        return parent::_prepareColumns();
    }

    /**
     * Prepare mass actions
     *
     * @return $this|Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareMassaction()
    {
        $_helper = Mage::helper('me_cmb');
        $this->setMassactionIdField('cmd_id');
        $this->getMassactionBlock()->setFormFieldName('cmb');

        $this->getMassactionBlock()->addItem(
            'delete',
            array(
                'label' => $_helper->__('Delete'),
                'url' => $this->getUrl('*/*/massDelete'),
                'confirm' => $_helper->__('Are you sure?')
            )
        );

        $statuses = Mage::getModel('me_cmb/cmb')->getStatuses();
        $this->getMassactionBlock()->addItem(
            'status',
            array(
                'label' => $_helper->__('Change Status'),
                'url' => $this->getUrl('*/*/massStatus'),
                'additional' => array(
                    'visibility' => array(
                        'name' => 'status',
                        'type' => 'select',
                        'class' => 'required-entry',
                        'label' => $_helper->__('Status'),
                        'values' => $statuses
                    )
                )
            )
        );

        return $this;
    }

    /**
     * Decorate status column values
     *
     * @param string                                  $value    value
     * @param Mage_Index_Model_Process                $row      row
     * @param Mage_Adminhtml_Block_Widget_Grid_Column $column   column
     * @param bool                                    $isExport is export
     *
     * @return string
     */
    public function decorateStatus($value, $row, $column, $isExport)
    {
        $class = '';
        switch ($row->getStatus()) {
            case Me_Cmb_Model_Cmb::STATUS_NEW :
                $class = 'grid-severity-major';
                break;
            case Me_Cmb_Model_Cmb::STATUS_PENDING :
                $class = 'grid-severity-minor';
                break;
            case Me_Cmb_Model_Cmb::STATUS_DONE :
                $class = 'grid-severity-notice';
                break;
        }
        return '<span class="' . $class . '"><span>' . $value . '</span></span>';
    }

    /**
     * Store filter
     *
     * @param Me_Cmb_Model_Resource_Cmb_Collection $collection collection
     * @param object                               $column     column
     */
    protected function _filterStoreCondition($collection, $column)
    {
        if (!$value = $column->getFilter()->getValue()) {
            return;
        }

        $this->getCollection()->addFieldToFilter('store_id', $value);
    }

    /**
     * Return row URL for js event handlers
     *
     * @param object $row row
     * @return bool|string
     */
    public function getRowUrl($row)
    {
        return false;
    }

    /**
     * Grid url getter
     *
     * @return string current grid url
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current' => true));
    }
}
