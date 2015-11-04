<?php
/**
 * Created by PhpStorm.
 * User: Игорь
 * Date: 27.02.2015
 * Time: 19:33
 */


class ITDelight_Sales_Model_Observer
{
    public function addCommentFieldToOrder($observer)
    {
        /** @var $observer Varien_Event_Observer */

        $order = $observer->getEvent()->getOrder();
        $comment = Mage::app()->getFrontController()->getRequest()->getParam('comment', '');
        $order->setComment($comment);
    }

    public function orderCommentSetChildBlock($observer)
    {
        /** @var $observer Varien_Event_Observer */
        /** @var $block Mage_Adminhtml_Block_Sales_Order_View_Info */

        $block = $observer->getEvent()->getBlock();

        if ($block->getNameInLayout() != 'order_tab_info') {
            return ;
        }

        $child = new Mage_Adminhtml_Block_Abstract();
        $child->setTemplate('itdelight_sales/order_comment.phtml');
        $block->setChild('order_comment', $child);
    }

    /**
     * @param $observer Varien_Event_Observer
     */
    public function registerCustomer($observer)
    {
        $_request = Mage::app()->getFrontController()->getRequest();
        $_billing = $_request->getParam('billing');

        $email = $_billing['email'];

        /** @var $modelCustomer Mage_Customer_Model_Customer */
        $modelCustomer = Mage::getModel('customer/customer');
        $modelCustomer->setStore(Mage::app()->getStore());
        $customer = $modelCustomer->loadByEmail($email);

        if ($customer->getId()) {
            return;
        }

        /** @var $newCustomer Mage_Customer_Model_Customer */
        $newCustomer = Mage::getModel('customer/customer');
        $newCustomer->setFirstname($_billing['firstname']);
        $newCustomer->setEmail($email);
        $newCustomer->setPassword(Mage::helper('itdelight_sales')->generatePassword());
        $newCustomer->save();
        $newCustomer->sendNewAccountEmail('confirmation', '', Mage::app()->getStore()->getId());

        Mage::getSingleton('core/session')->addSuccess('Please, check your email for the confirmation link.');
    }
}