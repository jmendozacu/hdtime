<?php

/**
 * Unicode Systems
 * @category   Uni
 * @package    Uni_Occheckout
 * @copyright  Copyright (c) 2010-2011 Unicode Systems. (http://www.unicodesystems.in)
 * @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */
?>

<?php

/**
 * Occheckout  controller for saving checkout details
 */

class Uni_Occheckout_IndexController extends Mage_Core_Controller_Front_Action {

    /**
     * One click checkout action for oneclick popup page functionality
     */
    public function occheckoutAction() {
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Shipping methods for one click checkout.
     */
    public function shippingMethodAction() {
        $data = $this->getRequest()->getPost();
        $customerAddressId = Mage::getSingleton('customer/session')->getCustomer()->getDefaultBilling();
        $_customer = Mage::getSingleton('customer/session')->getCustomer();
        $storeId = Mage::app()->getStore()->getStoreId();
        $customAddress = Mage::getModel('customer/address');
        if ($data['bildata']) {
            $billdata = Zend_Json_Decoder::decode($data['bildata']);
        }
        if ($data['street']) {
            $streets = Zend_Json_Decoder::decode($data['street']);
        }
        if ($data['billing_address_id']) {
            $billAddId = Zend_Json_Decoder::decode($data['billing_address_id']);
        }
        if ($data['new_adderss']) {
            $newAdd = array();
            foreach ($data['new_adderss'] as $key => $value) {
                $newAdd[$value['name']] = $value['value'];
            }
        }
        if ($billAddId) {
            $customAddress->load($billAddId);
        } else {
            $_custom_address = array(
                'firstname' => $newAdd['billing[firstname]'],
                'lastname' => $newAdd['billing[lastname]'],
                'street' => array(
                    '0' => $newAdd['street'],
                    '1' => $newAdd['street2'],
                ),
                'city' => $newAdd['city'],
                'region_id' => $newAdd['region_id'],
                'region' => $newAdd['region'],
                'postcode' => $newAdd['postcode'],
                'country_id' => $newAdd['billing[country_id]'], /* Croatia */
                'telephone' => $newAdd['telephone'],
                'fax' => $newAdd['fax'],
            );
            $customAddress->setData($_custom_address);
        }
        $customAddress->setCustomerId($_customer->getId());
        if ($newAdd['use_for_shipping']) {
            $customAddress->setIsDefaultShipping('1');
        }
        if ($newAdd['save_in_address_book']) {
            $customAddress->setSaveInAddressBook('1');
        }

        try {
            if ($newAdd['save_in_address_book']) {
                $customAddress->save();
            }
        } catch (Exception $ex) {
            
        }
        Mage::getSingleton('checkout/session')->getQuote()->setBillingAddress(Mage::getSingleton('sales/quote_address')->importCustomerAddress($customAddress));
    }

    /**
     * Payment methods for one click checkout.
     */
    public function paymentmthdsAction() {
        $this->loadLayout();
        $this->renderLayout();
    }

    protected function _getPaymentMethodsHtml() {
        $layout = $this->getLayout();
        $update = $layout->getUpdate();
        $update->load('occheckout_index_paymentmethod');
        $layout->generateXml();
        $layout->generateBlocks();
        $output = $layout->getOutput();
        return $output;
    }

    protected function _getAdditionalHtml() {
        $layout = $this->getLayout();
        $update = $layout->getUpdate();
        $update->load('occheckout_index_additional');
        $layout->generateXml();
        $layout->generateBlocks();
        $output = $layout->getOutput();
        return $output;
    }

    /**
     * updating one click checkout cart through ajax
     */
    public function updateCartAction() {
        $params = $this->getRequest()->getParams();
        // if it is a ajax request
        if ($params['isAjax'] == 1) {
            $data = $this->getLayout()->getBlock('occheckout/updatecart')->setTemplate('occheckout/updatecart.phtml')->toHtml();
            $this->getResponse()->setBody($data);
        }
    }

    /**
     * updating cart grand total through ajax 
     */
    public function updategrandtotalAction() {
        $params = $this->getRequest()->getParams();
        // if it is a ajax request
        if ($params['isAjax'] == 1) {
            $data = $this->getLayout()->getBlock('checkout/cart_totals')->setTemplate('checkout/cart/totals.phtml')->toHtml();
            $this->getResponse()->setBody($data);
        }
    }

}
