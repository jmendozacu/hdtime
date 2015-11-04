<?php
class Komplizierte_CustomerCallbacks_Adminhtml_CallbacksController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction() {
        $this->_initAction();
        $this->renderLayout();
    }

    protected function _initAction() {
        $this->loadLayout()->_setActiveMenu("customer/callbacks");
        return $this;
    }

    public function editAction() {
        $id    = $this->getRequest()->getParam("id");
        $model = Mage::getModel("komplizierte_customercallbacks/callbacks")->load($id);
        if ($model->getId()) {
            Mage::register("callbacks_data", $model);
            $this->loadLayout();
            $this->_setActiveMenu("customer/callbacks");
            $this->getLayout()->getBlock("head")->setCanLoadExtJs(true);
            $this->renderLayout();
        } else {
            Mage::getSingleton("adminhtml/session")->addError(Mage::helper("komplizierte_customercallbacks")->__("Item does not exist."));
            $this->_redirect("*/*/");
        }
    }

    public function newAction() {
        $id    = $this->getRequest()->getParam("id");
        $model = Mage::getModel("komplizierte_customercallbacks/callbacks")->load($id);

        $data = Mage::getSingleton("adminhtml/session")->getFormData(true);
        if (!empty($data)) {
            $model->setData($data);
        }

        Mage::register("callbacks_data", $model);

        $this->loadLayout();
        $this->_setActiveMenu("customer/callbacks");
        $this->getLayout()->getBlock("head")->setCanLoadExtJs(true);
        $this->renderLayout();
    }

    public function saveAction() {
        $post_data = $this->getRequest()->getPost();
        if ($post_data) {
            try {
                $model = Mage::getModel("komplizierte_customercallbacks/callbacks")->addData($post_data)->setId($this->getRequest()->getParam("id"))->save();

                Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("adminhtml")->__("Callback was successfully saved"));
                Mage::getSingleton("adminhtml/session")->setCallbacksData(false);

                if ($this->getRequest()->getParam("back")) {
                    $this->_redirect("*/*/edit", array("id" => $model->getId()));

                    return;
                }
                $this->_redirect("*/*/");

                return;
            } catch (Exception $e) {
                Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
                Mage::getSingleton("adminhtml/session")->setCallbacksData($this->getRequest()->getPost());
                $this->_redirect("*/*/edit", array("id" => $this->getRequest()->getParam("id")));
                return;
            }
        }
        $this->_redirect("*/*/");
    }

    public function deleteAction() {
        if ($this->getRequest()->getParam("id") > 0) {
            try {
                $model = Mage::getModel("komplizierte_customercallbacks/callbacks");
                $model->setId($this->getRequest()->getParam("id"))->delete();
                Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("komplizierte_customercallbacks")->__("Item was successfully deleted"));
                $this->_redirect("*/*/");
            } catch (Exception $e) {
                Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
                $this->_redirect("*/*/edit", array("id" => $this->getRequest()->getParam("id")));
            }
        }
        $this->_redirect("*/*/");
    }

    public function massRemoveAction() {
        try {
            $ids = $this->getRequest()->getPost('ids', array());
            foreach ($ids as $id) {
                $model = Mage::getModel("komplizierte_customercallbacks/callbacks");
                $model->setId($id)->delete();
            }
            Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("komplizierte_customercallbacks")->__("Item(s) was successfully removed"));
        } catch (Exception $e) {
            Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
        }
        $this->_redirect('*/*/');
    }

    public function gridAction()
    {
        $this->loadLayout();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('komplizierte_customercallbacks/adminhtml_callbacks_grid')->toHtml()
        );
    }
}