<?php
class Amasty_ShippingDate_Model_ShippingDate extends Mage_Core_Model_Abstract
{
    public function checkDeliveryDates($dateFrom, $dateTo) {
        /*$todayStartOfDayDate  = Mage::app()->getLocale()->date()
            ->setTime('00:00:00')
            ->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);*/
        $todayStartOfDayDate = $dateFrom;
        /*$todayEndOfDayDate  = Mage::app()->getLocale()->date()
            ->setTime('23:59:59')
            ->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);*/
        $todayEndOfDayDate = $dateTo;
        $weekendCollection = Mage::getModel('shippingdate/weekend')->getCollection();
        $weekendCollection
            ->addFieldToFilter('date_from', array('or'=> array(
                0 => array('date' => true, 'to' => $todayEndOfDayDate),
                1 => array('is' => new Zend_Db_Expr('null')))
            ), 'left')
            ->addFieldToFilter('date_to', array('or'=> array(
                0 => array('date' => true, 'from' => $todayStartOfDayDate),
                1 => array('is' => new Zend_Db_Expr('null')))
            ), 'left')
            ;
        if($weekendCollection->getSize()) {
            return false;
        }
        return true;
    }
}