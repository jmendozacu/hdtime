<?php
class Amasty_ShippingDate_Block_Adminhtml_Sales_Order_View_ShippingDate extends Mage_Adminhtml_Block_Sales_Order_View_Items
{
    public function _toHtml(){
        $html = parent::_toHtml();
        $deliveryDate = $this->getDeliveryDateHtml();
        return $html.$deliveryDate;
    }

    public function getDeliveryDateHtml(){
        $deliveryDateFrom = $this->getOrder()->getDeliveryDateFrom();
        $deliveryDateTo = $this->getOrder()->getDeliveryDateTo();
        $html = '';

        if ($deliveryDateFrom || $deliveryDateTo){
            $html .= '<div id="delivery-date-from" class="giftmessage-whole-order-container"><div class="entry-edit">';
            $html .= '<div class="entry-edit-head"><h4>'.$this->helper('adminhtml')->__('Delivery Date and Time').'</h4></div>';
            $html .= '<fieldset>';
            if($deliveryDateFrom) {
                $html .= '<p>'. $this->helper('adminhtml')->__('Delivery From: ') . nl2br($deliveryDateFrom).'</p>';
            }
            if($deliveryDateTo) {
                $html .= '<p>'. $this->helper('adminhtml')->__('Delivery To: ') . nl2br($deliveryDateTo).'</p>';
            }
            $html .= '</fieldset>';
            $html .= '</div></div>';
        }

        return $html;
    }
}
