<?php
/**
 * Class Me_Cmb_Adminhtml_CmbController
 *
 * @category  Me
 * @package   Me_Cmb
 * @author    Attila Sági <sagi.attila@aion.hu>
 * @copyright 2015 Magevolve Ltd. (http://magevolve.com)
 * @license   http://magevolve.com/terms-and-conditions Magevolve License
 * @link      http://magevolve.com
 */

/**
 * Class Me_Cmb_Adminhtml_CmbController
 */
class Me_Cmb_Adminhtml_CmbController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Init actions
     *
     * @return Me_Cmb_Adminhtml_CmbController
     */
    protected function _initAction()
    {
        // load layout, set active menu and breadcrumbs
        $this->loadLayout()
            ->_setActiveMenu('customer/cmb')
            ->_addBreadcrumb(
                Mage::helper('me_cmb')->__('Callbacks'),
                Mage::helper('me_cmb')->__('Callbacks')
            )
            ->_addBreadcrumb(
                Mage::helper('me_cmb')->__('Manage Callbacks'),
                Mage::helper('me_cmb')->__('Manage Callbacks')
            );
        return $this;
    }

    /**
     * Index action
     *
     * @return void
     */
    public function indexAction()
    {
        $this->_title($this->__('Callbacks'))
            ->_title($this->__('Manage Callbacks'));

        $this->_initAction();
        $this->renderLayout();
    }

    /**
     * Check the permission to run it
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        switch ($this->getRequest()->getActionName()) {
            case 'save':
                return Mage::getSingleton('admin/session')->isAllowed('customer/cmb/save');
                break;
            case 'delete':
                return Mage::getSingleton('admin/session')->isAllowed('customer/cmb/delete');
                break;
            default:
                return Mage::getSingleton('admin/session')->isAllowed('customer/cmb');
                break;
        }
    }

    /**
     * Mass delete action
     *
     * @return void
     */
    public function massDeleteAction()
    {
        $cmbIds = $this->getRequest()->getParam('cmb');

        if (!is_array($cmbIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('me_cmb')->__('Please select callback(s)'));
        } else {
            try {
                foreach ($cmbIds as $cmbId) {
                    $cmb = Mage::getModel('me_cmb/cmb')->load($cmbId);
                    $cmb->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__('Total of %d record(s) were deleted', count($cmbIds))
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }

        $this->_redirect('*/*/index');
    }

    /**
     * Mass status action
     *
     * @return void
     */
    public function massStatusAction()
    {
        $status = $this->getRequest()->getParam('status');
        $cmbIds = $this->getRequest()->getParam('cmb');

        if (!is_array($cmbIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('me_cmb')->__('Please select callback(s)'));
        } else {
            try {
                foreach ($cmbIds as $cmbId) {
                    $cmb = Mage::getModel('me_cmb/cmb')->load($cmbId);
                    $cmb->setStatus($status);
                    $cmb->save();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__('Total of %d record(s) were updated', count($cmbIds))
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }

        $this->_redirect('*/*/index');
    }

    /**
     * Grid ajax action
     *
     * @return void
     */
    public function gridAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }
}