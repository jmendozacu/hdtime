<?php
class Komplizierte_Unisender_Model_Observer
{
    public function sendSMSNewOrderNotification($observer) {
        if(Mage::helper('komplizierte_unisender')->checkEnableSendSms()) {
            $event = $observer->getEvent();
            $order = $event->getOrder();
            Mage::helper('komplizierte_unisender')->sendNewOrderSMS($order);
        }
        return $this;
    }

    public function sendSmsUpdateStatusOrderNotification($observer) {
        if(Mage::helper('komplizierte_unisender')->checkEnableSendSms()) {
            $event = $observer->getEvent();
            $order = $event->getOrder();
            Mage::helper('komplizierte_unisender')->sendUpdateStatusOrderSms($order);
        }
        return $this;
    }

    public function sendNewUserToSubscribe($observer) {
        if(Mage::helper('komplizierte_unisender')->checkEnableSendSms()) {
            $event = $observer->getEvent();
            $customer = $event->getCustomer();
            Mage::helper('komplizierte_unisender')->subscribeCustomerNewsletter($customer);
        }
        return $this;
    }
}