<?php
class Amasty_ShippingDate_Model_Observer
{
    public function saveCustomData($observer) {
        if(Mage::helper('shippingdate')->isEnable()) {
            $event = $observer->getEvent();
            $order = $event->getOrder();
            $deliveryDate = Mage::app()->getFrontController()->getRequest()->getParam('delivery_date');
            if(isset($deliveryDate) && is_array($deliveryDate)) {
                if(isset($deliveryDate['from']) && isset($deliveryDate['to'])) {
                    if(!Mage::getModel('shippingdate/shippingDate')->checkDeliveryDates($deliveryDate['from'],$deliveryDate['to'])) {
                        Mage::throwException(Mage::helper('checkout')->__('We can not deliver your item to specify the time interval.'));
                    }
                    $order->setDeliveryDateFrom($deliveryDate['from']);
                    $order->setDeliveryDateTo($deliveryDate['to']);
                }
            }
        }
    }
}