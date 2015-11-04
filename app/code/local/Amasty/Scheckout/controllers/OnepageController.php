<?php
require_once 'Mage/Checkout/controllers/OnepageController.php';
class Amasty_Scheckout_OnepageController extends Mage_Checkout_OnepageController
{
    protected $_skip_generate_html = false;

    public function checkPasswordAction(){
        $customer = Mage::getModel('customer/customer');
        $websiteId = Mage::app()->getWebsite()->getId();

        if ($websiteId) {
            $customer->setWebsiteId($websiteId);
        }

        $resp = array('success' => false);
        if (array_key_exists('email', $_POST)) {
            $customer->loadByEmail($_POST['email']);
            if($customer->getId()) {
                $resp = array(
                    'success' => true, 
                    'data' => array(
                        'customer' => array(
                            'fullname' => $customer->getName(),
                            'firstname' => $customer->getFirstname(),
                            'lastname' => $customer->getLastname()
                         )
                     )
                );
            }
        }
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($resp));
    }

    public function getOnepage()
    {
        return Mage::getSingleton('amscheckout/type_onepage');
    }
    
    protected function _saveSteps($completeOrder = FALSE){
        $ret = NULL;
        
        if ($this->_expireAjax()) {
            return;
        }
        
        if ($this->getRequest()->isPost()) {
            
            $billing = $this->getRequest()->getPost('billing', array());
            
            $beforeResponse = $this->getResponse();
            
            $amResponse = Mage::getModel("amscheckout/response");
            $this->_response = $amResponse;
            
            $this->_skip_generate_html = true;
            
            $this->saveMethodAction();
//            
            $this->saveBillingAction();
            
            $usingShippingCase = isset($billing['use_for_shipping']) ? (int)$billing['use_for_shipping'] : 0;
            
            if (!$usingShippingCase)
                $this->saveShippingAction();
            
            $this->saveShippingMethodAction();
            $this->savePaymentAction();
                                
            if ($completeOrder && $amResponse->getErrorsCount() == 0){
                $this->saveOrderAction();
            }
            
            $this->getOnepage()->getQuote()->setTotalsCollectedFlag(false);
            $this->getOnepage()->getQuote()->collectTotals();
            $this->getOnepage()->getQuote()->save();
            
            $this->_skip_generate_html = false;
            
            $this->_response = $beforeResponse;
            
            $ret = $amResponse;
        }
        
        return $ret;
    }
    
    
    protected function _getRequiredFields(){
        $ret = array(
            "billing" => array(),
            "shipping" => array(),
        );
        
        $hlr = Mage::helper("amscheckout");
        $billingFields = $hlr->getFields("billing");
        $shippingFields = $hlr->getFields("shipping");
        
        foreach($billingFields as $field){
            if ($field["field_required"] == 1 && $field["field_disabled"] == 0)
                $ret["billing"][] = str_replace("billing:", "", $field["field_key"]);
        }
        
        foreach($shippingFields as $field){
            if ($field["field_required"] == 1 && $field["field_disabled"] == 0)
                $ret["shipping"][] = str_replace("shipping:", "", $field["field_key"]);
        }
        return $ret;
        
    }
    
    protected function _reloadRequest($skipRequired = TRUE){
        
        
        $billingDefaults = array(
            'prefix' => '-',
            'postfix' => '-',
            'firstname' => '-',
            'lastname' => '-',
            'email' => 'email@example.com',
            'street' => array(
                '-'
            ),
			//'test' => '-',
            'city' => '-',
            'region_id' => '-',
            'region' => '-',
            'postcode' => '-',
            'telephone' => '-',
            'fax' => '-',
            'customer_password' => 'email@example.com',
            'confirm_password' => 'email@example.com'
        );
        
        $shippingDefaults = array(
            'prefix' => '-',
            'postfix' => '-',
            'firstname' => '-',
            'lastname' => '-',
            'street' => array(
                '-'
            ),
            'city' => '-',
            'region_id' => '-',
            'region' => '-',
            'postcode' => '-',
            'telephone' => '-',
            'fax' => '-',
        );
        
        $billing = $this->getRequest()->getPost('billing', array());
        $shipping = $this->getRequest()->getPost('shipping', array());
        
        $requiredFields = $this->_getRequiredFields();
        
        foreach($billingDefaults as $key => $def){
            $val = isset($billing[$key]) ? $billing[$key] : "";

            $empty = $val == "" || (is_array($val) && implode("", $val) == "");
            
            if ($empty){
                
                if ($skipRequired || !in_array($key, $requiredFields["billing"])){
                    $billing[$key] = $def;
                }
                
            }
        }
        
        if (isset($billing['customer_password']) && $billing['customer_password'] != $billing['confirm_password'] && $skipRequired) {
            $billing['confirm_password'] = $billing['customer_password'];
        } else {
            $billing['customer_password'] = Mage::helper('amscheckout')->getGoodPassword();
            $billing['confirm_password'] = $billing['customer_password'];

            /*
            $vars = array(
                'name' => $billing['firstname'] . ' ' . $billing['lastname'],
                'password' => $billing['customer_password'],
                'email' => $billing['email']
            );
            $this->_sendPassword($vars);
            */
        }
        
        foreach($shippingDefaults as $key => $def){
            $val = isset($shipping[$key]) ? $shipping[$key] : "";

            $empty = $val == "" || (is_array($val) && implode("", $val) == "");
            
            if ($empty){
                
                if ($skipRequired || !in_array($key, $requiredFields["shipping"])){
                    $shipping[$key] = $def;
                }
                
            } 
        }
        
        
//        foreach($billing as $key => $val){
//            $empty = $val == "" || (is_array($val) && implode("", $val) == "");
//            if ($empty && isset($billingDefaults[$key])){
//                $billing[$key] = $billingDefaults[$key];
//            }
//        }
//        
//        foreach($shipping as $key => $val){
//            $empty = $val == "" || (is_array($val) && implode("", $val) == "");
//            if ($empty && isset($shippingDefaults[$key])){
//                $shipping[$key] = $shippingDefaults[$key];
//            }
//        }
        $this->getRequest()->setPost('billing', $billing);
        $this->getRequest()->setPost('shipping', $shipping);
    }

    protected function _sendPassword($variables) {
        // Transactional Email Template's ID
        $templateId = 1;

        // Set sender information
        $sender = array(
            'name' => Mage::getStoreConfig('trans_email/ident_support/name'),
            'email' => Mage::getStoreConfig('trans_email/ident_support/email')
        );

        // Set recepient information
        $recepientEmail = $variables['email'];
        $recepientName = $variables['name'];

        // Get Store ID
        $storeId = Mage::app()->getStore()->getId();

        // Send Transactional Email
        Mage::getModel('core/email_template')
            ->sendTransactional($templateId, $sender, $recepientEmail, $recepientName, $variables, $storeId);

        $translate  = Mage::getSingleton('core/translate');
        $translate->setTranslateInline(true);
    }

    protected function _createCustomer($billing){
        $customer = Mage::getModel('customer/customer');

        $password = $billing['customer_password'];
        $email = $billing['email'];
        $customer->setWebsiteId(Mage::app()->getWebsite()->getId());
        $customer->loadByEmail($email);

        if(!$customer->getId()) {
            $customer->setEmail($email);
            $customer->setFirstname($billing['firstname']);
            $customer->setLastname($billing['lastname']);	
			$customer->setBank($billing['bank']);
			$customer->setBik($billing['bik']);
			$customer->setInn($billing['inn']);
			$customer->setKorSchet($billing['kor_schet']);
			$customer->setKpp($billing['kpp']);
			$customer->setLegalName($billing['legal_name']);
			$customer->setSchet($billing['schet']);
			
        }
        $customer->setPassword($password);
        try {
            $customer->save();
            $customer->setConfirmation(null);
            $customer->save();
            //Make a "login" of new customer
            Mage::getSingleton('customer/session')->loginById($customer->getId());
        }
        catch (Exception $ex) {
            //Zend_Debug::dump($ex->getMessage());
        }
    }
    
    public function updateAction(){
        $this->_reloadRequest();
        $amResponse = $this->_saveSteps(FALSE);
        
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode(array(
            "html" => array(
                "review" => $this->_getReviewHtml(),
                "shipping_method" => $this->_getShippingMethodsHtml(),
                "payment_method" => $this->_getPaymentMethodsHtml(),
                
            )
        )));
    }
    
    protected function _updateShoppingCart(){
        $hlr = Mage::helper("amscheckout");
        
        $cartData = $this->getRequest()->getParam($hlr->isShoppingCartOnCheckout() && !$hlr->isMergeShoppingCartCheckout() ?
            'cart' : 'review'
        , array());
            
        $filter = new Zend_Filter_LocalizedToNormalized(
            array('locale' => Mage::app()->getLocale()->getLocaleCode())
        );
        foreach ($cartData as $index => $data) {
            if (isset($data['qty'])) {
                $cartData[$index]['qty'] = $filter->filter(trim($data['qty']));
            }
        }

        $cart = $this->_getCart();
        if (! $cart->getCustomerSession()->getCustomer()->getId() && $cart->getQuote()->getCustomerId()) {
            $cart->getQuote()->setCustomerId(null);
        }

        $cartData = $cart->suggestItemsQty($cartData);
        $cart->updateItems($cartData)->save();
    }
    
    protected function _emptyShoppingCart()
    {
        $this->_getCart()->truncate()->save();   
    }
    
    public function cartAction(){
        if ($this->_expireAjax()) {
            return;
        }
        
        $updateAction = (string)$this->getRequest()->getParam('update_cart_action');

        switch ($updateAction) {
            case 'empty_cart':
                $this->_emptyShoppingCart();
                break;
            case 'update_qty':
                $this->_updateShoppingCart();
                break;
            default:
                $this->_updateShoppingCart();
        }
        
        $this->_reloadRequest();
        $amResponse = $this->_saveSteps(FALSE);
        //die($this->_getCartHtml());
        
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode(array(
            "html" => array(
                "review" => $this->_getReviewHtml(),
                "cart" => $this->_getCartHtml(),
                
                "shipping_method" => $this->_getShippingMethodsHtml(),
                "payment_method" => $this->_getPaymentMethodsHtml(),
            )
        )));
    }
    
    public function deleteAction(){
        if ($this->_expireAjax()) {
            return;
        }
        $id = (int) $this->getRequest()->getParam('delete_cart_id');
        
        $this->_getCart()->removeItem($id)
                  ->save();
        
        $this->_reloadRequest();
        $amResponse = $this->_saveSteps(FALSE);
        
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode(array(
            "html" => array(
                "review" => $this->_getReviewHtml(),
                "cart" => $this->_getCartHtml(),
                
                "shipping_method" => $this->_getShippingMethodsHtml(),
                "payment_method" => $this->_getPaymentMethodsHtml(),
            )
        )));
    }
    
    
    
    public function checkoutAction(){
        
        $res = array();
        $this->_reloadRequest(FALSE);

        $billing = $this->getRequest()->getPost('billing', array());

        if (isset($billing['customer_password']) && $billing['customer_password'] != $billing['confirm_password'] && $skipRequired) {
        } else {
            $vars = array(
                'name' => $billing['firstname'] . ' ' . $billing['lastname'],
                'password' => $billing['customer_password'],
                'email' => $billing['email']
            );
            $this->_sendPassword($vars);
        }
        $this->_createCustomer($billing);
        
//        $redirectUrl = $this->getOnepage()->getQuote()->getPayment()->getCheckoutRedirectUrl();
        
        $amResponse = $this->_saveSteps(TRUE);
        
        $redirectUrl = $amResponse->getRedirect();
        
        if ($redirectUrl){
            $res = array(
                "redirect_url" => $redirectUrl
            );
        }
        else if ($amResponse->getErrorsCount() == 0){
            $res = array(
                "status" => "ok"
            );
        } else {
            $res = array(
                "status" => "error",
                "errors" => implode("\n", $amResponse->getErrors())
            );
        }
        
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($res));
        
    }
    
    public function testAction()
    {
        print $this->_getReviewHtml();
    }
    
    public function savePaymentAction()
    {
        $result = array();
        
        if ($this->_expireAjax()) {
            return;
        }
        
        try {
            if (!$this->getRequest()->isPost()) {
                $this->_ajaxRedirectResponse();
                return;
            }
        
            // set payment to quote
            $result = array();
            $data = $this->getRequest()->getPost('payment', array());
            $result = $this->getOnepage()->savePayment($data);
        
            // get section and redirect data
            $redirectUrl = $this->getOnepage()->getQuote()->getPayment()->getCheckoutRedirectUrl();
            if (empty($result['error']) && !$redirectUrl) {
                $this->loadLayout('checkout_onepage_review');
                $result['goto_section'] = 'review';
                $result['update_section'] = array(
                    'name' => 'review',
                    'html' => $this->_getReviewHtml()
                );
            }
            if ($redirectUrl) {
                $result['redirect'] = $redirectUrl;
            }
        } catch (Exception $e) {
            $data = $this->getRequest()->getPost('payment', array());


            $onepage = Mage::getSingleton('checkout/type_onepage');

            $quote = $onepage->getQuote();

            $data = new Varien_Object($data);

            $payment = $quote->getPayment();

            $payment->setMethod($data->getMethod());
            $method = $payment->getMethodInstance();
            $method->assignData($data);

            $quote->save();
            
            $this->loadLayout('checkout_onepage_review');
            $result['goto_section'] = 'review';
            $result['update_section'] = array(
                'name' => 'review',
                'html' => $this->_getReviewHtml()
            );
        }
        
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }
    
    protected function _getShippingMethodsHtml()
    {
        $output = "";
        
        if (!$this->_skip_generate_html){
            $this->getLayout()->getUpdate()->setCacheId(uniqid("amscheckout_shipping"));
            $output = parent::_getShippingMethodsHtml();
        }
        
        return $output;
    }

    protected function _getPaymentMethodsHtml()
    {
        $output = "";
        
        if (!$this->_skip_generate_html){
            
            $this->getLayout()->getUpdate()->setCacheId(uniqid("amscheckout_payment"));
            $output = parent::_getPaymentMethodsHtml();
        }
        
        return $output;
    }

    protected function _getReviewHtml()
    {
        $output = "";
        
        if (!$this->_skip_generate_html){

            $this->getLayout()->getUpdate()->setCacheId(uniqid("amscheckout_review"));
            
            $layout = $this->getLayout();
            $update = $layout->getUpdate();
            $update->load('checkout_onepage_review');
            $layout->generateXml();
            $layout->generateBlocks();
            $output = $layout->getOutput();
            
        }
        
        return $output;
    }
    
    protected function _getCouponHtml()
    {
        $output = "";
        
        if (!$this->_skip_generate_html){
            $this->getLayout()->getUpdate()->resetHandles();
            $layout = $this->getLayout();
            $update = $layout->getUpdate();
            $update->load('amscheckout_onepage_coupon');
            $layout->generateXml();
            $layout->generateBlocks();
            $output = $layout->getOutput();
            return $output;
        }
        
        return $output;
    }
    
    protected function _getCartHtml()
    {
        $output = "";
        $hlr = Mage::helper("amscheckout");
        
        if (!$this->_skip_generate_html && $hlr->isShoppingCartOnCheckout() && !$hlr->isMergeShoppingCartCheckout()){
            $this->getLayout()->getUpdate()->resetHandles();
            $layout = $this->getLayout();
            $update = $layout->getUpdate();
            $update->load('amscheckout_cart');
            $layout->generateXml();
            $layout->generateBlocks();
            $output = $layout->getOutput(); 
            return $output;
        }
        
        return $output;
    }
    
    protected function _getCart()
    {
        return Mage::getSingleton('checkout/cart');
    }
    
    public function couponPostAction(){
       
        $response = array(
            "html" => array(
                "coupon" => array(
                    "success" => NULL,
                    "error" => NULL,
                    "output" => NULL
                )
            )
        );
        
        $success = &$response["html"]["coupon"]["success"];
        $error = &$response["html"]["coupon"]["error"];
        $output = &$response["html"]["coupon"]["output"];
        
        $couponCode = (string) $this->getRequest()->getParam('coupon_code');
        if ($this->getRequest()->getParam('remove') == 1) {
            $couponCode = '';
        }
        $oldCouponCode = $this->getOnepage()->getQuote()->getCouponCode();

        if (!strlen($couponCode) && !strlen($oldCouponCode)) {
            
        } else {

            try {
                $codeLength = strlen($couponCode);
        
                $isCodeLengthValid = $codeLength && $codeLength <= 255;

                $this->getOnepage()->getQuote()->getShippingAddress()->setCollectShippingRates(true);
                $this->getOnepage()->getQuote()->setCouponCode($isCodeLengthValid ? $couponCode : '')
                    ->collectTotals()
                    ->save();

                if ($codeLength) {
                    if ($isCodeLengthValid && $couponCode == $this->getOnepage()->getQuote()->getCouponCode()) {
                        $success = $this->__('Coupon code "%s" was applied.', Mage::helper('core')->escapeHtml($couponCode));
                    } else {
                        $error = $this->__('Coupon code "%s" is not valid.', Mage::helper('core')->escapeHtml($couponCode));
                    }
                } else {
                    $success = $this->__('Coupon code was canceled.');
                }
                
                
                if (!empty($success)){
                    $output = $this->_getCouponHtml();
                    $this->_reloadRequest();
                    $this->_saveSteps(FALSE);
                    $response["html"]["review"] = $this->_getReviewHtml();
                    $response["html"]["shipping_method"] = $this->_getShippingMethodsHtml();
                    $response["html"]["payment_method"] = $this->_getPaymentMethodsHtml();
                }

            } catch (Mage_Core_Exception $e) {
                $error = $e->getMessage();
            } catch (Exception $e) {
                $error = $this->__('Cannot apply the coupon code.');
                Mage::logException($e);
            }
        }
        
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
    }
}
?>