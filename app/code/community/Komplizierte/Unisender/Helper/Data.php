<?php
class Komplizierte_Unisender_Helper_Data extends Mage_Core_Helper_Abstract {

    protected $_listIds;

    public function getApiKey() {
        return Mage::getStoreConfig('komplizierte_unisender/general/api_key');
    }

    public function checkEnableSendSms() {
        return ($this->getApiKey() && Mage::getStoreConfigFlag('komplizierte_unisender/sms/enabled'));
    }

    public function getSmsSender() {
        return Mage::getStoreConfig('komplizierte_unisender/sms/sender');
    }

    public function getSmsTextNewOrder() {
        return Mage::getStoreConfig('komplizierte_unisender/sms/text_new_order');
    }

    public function getOrderStatusesSendSms() {
        return explode(',', Mage::getStoreConfig('komplizierte_unisender/sms/order_status_send_sms'));
    }

    public function getSmsTextUpdateOrderStatus() {
        return Mage::getStoreConfig('komplizierte_unisender/sms/text_update_order_status');
    }

    public function checkEnableSendNewCustomer() {
        return ($this->getApiKey() && Mage::getStoreConfigFlag('komplizierte_unisender/email/enabled_send_create_user'));
    }

    public function getConfirmationSubscribeMode() {
        return Mage::getStoreConfig('komplizierte_unisender/email/confirmation_of_subscription');
    }

    public function sendNewOrderSms($order) {

        $billingAddress = $order->getBillingAddress();

        $_telephoneTo = str_replace(array(' ', '-'), '', $billingAddress->getTelephone());

        $_customerFirstName = $billingAddress->getFirstname();
        $_customerLastName = $billingAddress->getLastname();

        $_orderNumber = $order->getIncrementId();
        $_orderSum = strip_tags(Mage::helper('core')->currency($order->getGrandTotal(),true,false));

        $_textMessage = $this->getSmsTextNewOrder();

        $swears = array(
            "{ORDER_NUMBER}" => $_orderNumber,
            "{ORDER_SUM}" => $_orderSum,
            "{CUSTOMER}" => $_customerFirstName . ' ' . $_customerLastName
        );

        $_textMessage = str_replace(array_keys($swears), array_values($swears), $_textMessage);

        $sender = $this->getSmsSender();

        Mage::helper('komplizierte_unisender/api')->sendSms($_telephoneTo, $sender, $_textMessage);
    }

    public function sendUpdateStatusOrderSms($order) {

        $availableOrderStatuses = $this->getOrderStatusesSendSms();

        if(!in_array($order->getStatus(), $availableOrderStatuses)) {
            return;
        }

        $billingAddress = $order->getBillingAddress();

        $_telephoneTo = str_replace(array(' ', '-'), '', $billingAddress->getTelephone());

        $_customerFirstName = $billingAddress->getFirstname();
        $_customerLastName = $billingAddress->getLastname();

        $_orderNumber = $order->getIncrementId();
        $_orderSum = strip_tags(Mage::helper('core')->currency($order->getGrandTotal(),true,false));

        $_textMessage = $this->getSmsTextUpdateOrderStatus();

        $swears = array(
            "{ORDER_NUMBER}" => $_orderNumber,
            "{ORDER_SUM}" => $_orderSum,
            "{CUSTOMER}" => $_customerFirstName . ' ' . $_customerLastName,
            "{ORDER_STATUS}" => $order->getStatusLabel()
        );

        $_textMessage = str_replace(array_keys($swears), array_values($swears), $_textMessage);

        $sender = $this->getSmsSender();

        Mage::helper('komplizierte_unisender/api')->sendSms($_telephoneTo, $sender, $_textMessage);
    }

    public function subscribeCustomerNewsletter($item) {

        $list_ids = $this->getListIds();

        Mage::helper('komplizierte_unisender/api')->subscribe($list_ids, $this->_prepareCustomer($item), $this->getTags($item), Mage::helper('core/http')->getRemoteAddr(), $this->getConfirmationSubscribeMode());
        return $this;
    }

    public function getListIds() {
        if(!$this->_listIds) {
            $list_ids = array();
            $list = Mage::helper('komplizierte_unisender/api')->getLists();

            foreach ($list['result'] as $value)
            {
                $list_ids[] = $value['id'];
            }
            $this->_listIds = $list_ids;
        }
        return $this->_listIds;
    }

    public function getTags($item) {
        $store = Mage::app()->getStore($item->getStoreId())->getName();
        $website = Mage::app()->getStore($item->getStoreId())->getWebsite()->getName();
        $tag = Mage::helper('komplizierte_unisender')->__('Customer');
        return $website.','.$store.','.$tag;
    }

    protected function _prepareCustomer($item)
    {
        $item_data = array();
        $item_data['email'] = $item->getEmail();
        $item_data['Name'] = $item->getFirstname() . ' ' . $item->getLastname();
        return $item_data;
    }

}