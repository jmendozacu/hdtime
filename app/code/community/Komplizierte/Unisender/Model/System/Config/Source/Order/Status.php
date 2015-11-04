<?php
class Komplizierte_Unisender_Model_System_Config_Source_Order_Status
{
    public function toOptionArray()
    {
        $statuses = Mage::getModel('sales/order_status')->getResourceCollection()->getData();
        $options = array();
        foreach ($statuses as $status) {
            $options[] = array(
                'value' => $status['status'],
                'label' => $status['label']
            );
        }
        return $options;
    }
}