<?php
class Komplizierte_CustomerCallbacks_IndexController extends Mage_Core_Controller_Front_Action
{

	public function callbackAction()
	{
		$post = $this->getRequest()->getPost();
		$response = $this->getResponse()->setHeader('Content-Type', 'application/json', true);
		$result   = new Varien_Object();
		if ( $post ) {
			try {
				$postObject = new Varien_Object();
				$postObject->setData($post);

				$error = false;

				if (!Zend_Validate::is(trim($post['name']) , 'NotEmpty')) {
					$error = true;
				}

				if (!Zend_Validate::is(trim($post['phone']) , 'NotEmpty')) {
					$error = true;
				}

				if ($error) {
					throw new Exception('Please make sure to fill in all fields');
				}

                $post['status'] = 'new';
                $post['created_at'] = Mage::getModel('core/date')->date('Y-m-d H:i:s');

				Mage::getModel("komplizierte_customercallbacks/callbacks")->addData($post)->setId(null)->save();

				$result->success = $this->__("Спасибо за Ваш запрос! Мы свяжемся с вами в ближайшее время!");
			} catch (Exception $e) {
				$result->error = Mage::helper('komplizierte_customercallbacks')->__($e->getMessage());
			}
		} else {
			$result->error = "Sorry, something went wrong...";
		}
		return $response->setBody($result->toJSON());
	}

	public function successAction()
	{
		$this->loadLayout();
		$this->renderLayout();
	}
}