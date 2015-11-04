<?php
/**
* @author Amasty Team
* @copyright Amasty
* @package Amasty_Scheckout
*/
class Amasty_Scheckout_Helper_Data extends Mage_Core_Helper_Abstract
{
    protected $_location;

    public function getHttpResponse($url) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }

    public function getGoodPassword() {
        return $this->getHttpResponse('http://makeagoodpassword.com/password/strong/', 200);
    }

    /*
    function getCheckoutUrl() {
        return Mage::getUrl('checkout/onepage/checkout',array('_secure'=>true));
    }*/
    
    protected function _canUseMethod($quote, $method)
    {
        if (!$method->canUseForCountry($quote->getBillingAddress()->getCountry())) {
            return false;
        }

        if (!$method->canUseForCurrency($quote->getStore()->getBaseCurrencyCode())) {
            return false;
        }

        /**
         * Checking for min/max order total for assigned payment method
         */
        $total = $quote->getBaseGrandTotal();
        $minTotal = $method->getConfigData('min_order_total');
        $maxTotal = $method->getConfigData('max_order_total');

        if((!empty($minTotal) && ($total < $minTotal)) || (!empty($maxTotal) && ($total > $maxTotal))) {
            return false;
        }
        return true;
    }
    
    protected function _getPaymentMethods($quote)
    {   
        $store = $quote ? $quote->getStoreId() : null;
        $methods = Mage::helper('payment')->getStoreMethods($store, $quote);
        $total = $quote->getBaseSubtotal() + $quote->getShippingAddress()->getBaseShippingAmount();
        foreach ($methods as $key => $method) {
            if ($this->_canUseMethod($quote, $method)
                && ($total != 0
                    || $method->getCode() == 'free'
                    || ($quote->hasRecurringItems() && $method->canManageRecurringProfiles()))) {
            } else {
                unset($methods[$key]);
            }
        }
        
        return $methods;
    }
    
    function getDefaultPeymentMethod($quote){
        
        $ret = NULL;
        $default = Mage::getStoreConfig('amscheckout/default/payment_method');
        
        $paymentMethods = $this->_getPaymentMethods($quote);
        
        if ($default){
            foreach($paymentMethods as $method){
                
                if ( $method->getCode() == $default ){
                    $ret = $default;
                    break;
                }
            }
        }

        if ($ret === NULL && isset($paymentMethods[0]))
            $ret = $paymentMethods[0]->getCode();
        
        
        return $ret;
    }
    
    function getDefaultShippingMethod($quote){
        $ret = NULL;
        $default = Mage::getStoreConfig('amscheckout/default/shipping_method');
        $first = NULL;
        $address = $quote->getShippingAddress();
        $address->collectShippingRates()->save();
        
        $_shippingRateGroups = $address->getGroupedAllShippingRates();
        
        foreach ($_shippingRateGroups as $code => $_rates){
            foreach ($_rates as $_rate){
                if ($default == $_rate->getCode()){
                    $ret = $default;
                    break;
                }
                
                if ($first === NULL)
                    $first = $_rate->getCode();
            }
        }
        
        if (!$ret){
            $ret = $first;
        }
        
        return $ret;
    }
    
    protected function _getRemoteAddr(){        
        $addr = Mage::helper('core/http')->getRemoteAddr(true);
        return $addr;
    }
    
    public function getDefaultCountry(){
        $ret = NULL;
        
        if (Mage::getModel('amscheckout/import')->isDone() && Mage::getStoreConfig('amscheckout/geoip/use') == 1){
            $longIP = $this->_getRemoteAddr();
            
            $country = Mage::getModel('amscheckout/country');
            
            $countryCollection = $country->getCollection();
            
            $countryCollection->getSelect()->where("$longIP between ip_from and ip_to");
            
            $data = $countryCollection->getData();
            if (count($data) > 0)
                $ret = $data[0]['code'];
        }
        
        
        if (empty($ret)){
            $ret = Mage::getStoreConfig('amscheckout/default/country');
        }
        
        if (empty($ret)){
            $ret = Mage::getStoreConfig('general/country/default');
        }

        return $ret;
    }
    
    protected function _getGeipLocation(){
        if (!$this->_location) {
            $longIP = $this->_getRemoteAddr();
            
            $block = Mage::getModel('amscheckout/block');
            
            $blockCollection = $block->getCollection();
            
            $blockCollection->getSelect()->join(
                    array(
                        'locations' => Mage::getSingleton('core/resource')->getTableName('amscheckout/location')
                    ), 'locations.geoip_loc_id = main_table.geoip_loc_id', 
                    array('locations.city', 'locations.postal_code'));
            
            $blockCollection->getSelect()->where("$longIP between main_table.start_ip_num and main_table.end_ip_num");
            
            $data = $blockCollection->getData();

            if (count($data) > 0)
                $this->_location = $data[0];
        }
        return $this->_location;
    }
    
    public function getDefaultCity($allowNull = FALSE){
        $ret = NULL;
        
        if (Mage::getModel('amscheckout/import')->isDone() && Mage::getStoreConfig('amscheckout/geoip/use') == 1){
            $location = $this->_getGeipLocation();
            $ret = $location['city'];
        }
        
        if ($ret == NULL && !$allowNull){
            $ret = '-';
        }
        
        return $ret;
    }
        
    public function getDefaultPostcode($allowNull = FALSE){
        $ret = NULL;
        if (Mage::getModel('amscheckout/import')->isDone() && Mage::getStoreConfig('amscheckout/geoip/use') == 1){
            $location = $this->_getGeipLocation();
            
            $ret = $location['postal_code'];
        }
        
        if ($ret == NULL && !$allowNull){
            $ret = '-';
        }
        
        return $ret;
    }
    
    function getAreas(){
        $ret = array();
        $storeId = Mage::app()->getStore()->getStoreId();
        $areas = Mage::getModel("amscheckout/area")->getAreas($storeId, TRUE);
        
        foreach($areas as $area){
           $ret[$area['area_key']] = $area;
        }
        
        return $ret;
    }
    
    public function getFields($area){
        $storeId = Mage::app()->getStore()->getStoreId();
        return Mage::getModel("amscheckout/field")->getAreaFields($storeId, $area);
    }
    
    function useBilling4Shipping(){
        return true;
    }
    
    function getCheckoutUrl(){
        return Mage::getUrl('amscheckout/onepage/checkout',array('_secure'=>true));
    }
    
    function getUpdateUrl(){
        return Mage::getUrl('amscheckout/onepage/update',array('_secure'=>true));
    }
    
    function getSuccessUrl(){
        return Mage::getUrl('checkout/onepage/success', array('_secure'=>true));
    }
    
    function getCouponUrl(){
        return Mage::getUrl('amscheckout/onepage/couponPost', array('_secure'=>true));
    }
    
    function getCartUrl(){
         return Mage::getUrl('amscheckout/onepage/cart',array('_secure'=>true));
    }
    
    function getDeleteUrl(){
         return Mage::getUrl('amscheckout/onepage/delete',array('_secure'=>true));
    }
    
    function getContinueShoppingUrl(){
        $url = Mage::getSingleton('checkout/session')->getContinueShoppingUrl(true);
        if (!$url) {
            $url = Mage::getUrl();
        }
        return $url; 
    }
    
    function getBeforeControlHtml($_field, $repl = array()){
        return strtr('<div class="amscheckout-row" style="width: ' . $_field['column_position'] .'%"><div>
            <label for="' . $_field['field_key'] .'" class="amscheckout-label">' . Mage::helper('core')->escapeHtml($_field['field_label']) .
            ($_field['field_required'] == 1 ? "<em>*</em>" : "") . '</label>
            <div class="amscheckout-control">', $repl);
    }
    
    function getAfterControlHtml($_field) {
        return '</div></div></div>';
    }
    
    function getShippingCustomerWidget($block, $tpl){

        return $block->getLayout()->createBlock('customer/widget_name')->
                setTemplate($tpl)->
                setObject($block->getAddress())->
                setFieldIdFormat('shipping:%s')->
                setFieldNameFormat('shipping[%s]')->
                setFieldParams('onchange="shipping.setSameAsBilling(false)"');
    }
    
    function getBillingCustomerWidget($block, $tpl){
        return $block->getLayout()->createBlock('customer/widget_name')->
            setTemplate($tpl)->
            setObject($block->getAddress()->getFirstname() ? $block->getAddress() : $block->getQuote()->getCustomer())->
            setForceUseCustomerRequiredAttributes(!$block->isCustomerLoggedIn())->
            setFieldIdFormat('billing:%s')->setFieldNameFormat('billing[%s]');
    }
    
    function getAttributeValidationClass($field, $requred){
        $address = Mage::helper('customer/address');
        if (method_exists($address, "getAttributeValidationClass"))
            return $requred ? $address->getAttributeValidationClass($field) : "";
        else 
            return $requred ? "required-entry" : "";
    }
    
    public function getConfigShippingRates($block){
        $resultShippingRates = array();
        
        $defaultShippingRates = $block->getShippingRates();
        
        $shippingRates = array();
        
        foreach ($defaultShippingRates as $code => $rates){
            foreach ($rates as $rate){        
                $shippingRates['s_method_'.$rate->getCode()] = array(
                    "code" => $code,
                    "rate" => $rate
                );
            }
        }
        
        foreach($this->getFields("shipping_method") as $field){
            if (isset($shippingRates[$field['field_key']])){
                $shippingRates[$field['field_key']]['field'] = $field;
                $resultShippingRates[$field['field_key']] = $shippingRates[$field['field_key']];
            }
        }
        
        foreach($shippingRates as $key => $el){
            if (!isset($resultShippingRates[$key]))
                $el["field"] = array(
                    "field_label" => $el['rate']->getMethodTitle()
                );
                $resultShippingRates[$key] = $el;
        }
        
        return $resultShippingRates;
    }
    
    function getConfigPaymentMethods($block){
        $ret = array();
        $defaultMethods = $block->getMethods();
        $methods = array();
        
        $fields = $this->getFields("payment");
        
        foreach($defaultMethods as $method){
            $methods["p_method_" . $method->getCode()] = $method;
        }
        
        foreach($fields as $field){
            if (isset($methods[$field['field_key']])){
                $ret[] = array(
                    'field' => $field,
                    'method' => $methods[$field['field_key']]
                );
            }
        }
        
        return $ret;
    }
    
    function isShoppingCartOnCheckout(){
        return Mage::getStoreConfig('amscheckout/shopping_cart/checkout');
    }
    
    function isMergeShoppingCartCheckout(){
        return Mage::getStoreConfig('amscheckout/shopping_cart/cart_to_checkout');
    }
    
    function isAllowGuestCheckout(){
        return Mage::getStoreConfig('checkout/options/guest_checkout');
    }
    
    function getBillingUpdatable(){
        $ret = array();
        
        $updatable = explode(",", Mage::getStoreConfig('amscheckout/update/shipping'));
        
        foreach($updatable as $field){
            
            switch ($field){
                case "address":
                    $ret[] = "billing:street";
                break;
                case "city":
                    $ret[] = "billing:city";
                break;
                case "region":
                    $ret[] = "billing:region_id";
                    $ret[] = "billing:region";
                break;
                case "postcode":
                    $ret[] = "billing:postcode";
                break;
                case "country":
                    $ret[] = "billing:country_id";
                break;
            }
        }
        return $ret;
    }
    
    function getShippingUpdatable(){
        $ret = array();
        
        $updatable = explode(",", Mage::getStoreConfig('amscheckout/update/shipping'));
        
        foreach($updatable as $field){
            
            switch ($field){
                case "address":
                    $ret[] = "shipping:street";
                break;
                case "city":
                    $ret[] = "shipping:city";
                break;
                case "region":
                    $ret[] = "shipping:region_id";
                    $ret[] = "shipping:region";
                break;
                case "postcode":
                    $ret[] = "shipping:postcode";
                break;
                case "country":
                    $ret[] = "shipping:country_id";
                break;
            }
        }
        return $ret;
    }
    
    function reloadAfterShippingMethodChanged(){
        return Mage::getStoreConfig('amscheckout/update/shipping_methods');
    }
    
    function reloadPaymentShippingMethodChanged(){
        return Mage::getStoreConfig('amscheckout/update/payment_methods');
    }
    
    function initAddress($address){
        $address->setCountryId($this->getDefaultCountry())
            ->setCity($this->getDefaultCity(TRUE))
            ->setPostcode($this->getDefaultPostcode(TRUE));
    }
   
    public function getLayoutType(){
        $type = NULL;
        if ($this->_isMobile()){
            $type = 'one_column';
        }
        else 
        {
            $storeId = Mage::app()->getStore()->getStoreId();
            $type = Mage::getModel("amscheckout/config")->getLayoutType($storeId)->value;
        }
        
        return $type;
    }
    
    protected function _isMobile()  
    {  
        $regex_match = "/(nokia|iphone|android|motorola|^mot\-|softbank|foma|docomo|kddi|up\.browser|up\.link|"  
                     . "htc|dopod|blazer|netfront|helio|hosin|huawei|novarra|CoolPad|webos|techfaith|palmsource|"  
                     . "blackberry|alcatel|amoi|ktouch|nexian|samsung|^sam\-|s[cg]h|^lge|ericsson|philips|sagem|wellcom|bunjalloo|maui|"  
                     . "symbian|smartphone|mmp|midp|wap|phone|windows ce|iemobile|^spice|^bird|^zte\-|longcos|pantech|gionee|^sie\-|portalmmm|"  
                     . "jig\s browser|hiptop|^ucweb|^benq|haier|^lct|opera\s*mobi|opera\*mini|320x320|240x320|176x220"  
                     . ")/i";  

        if (preg_match($regex_match, strtolower($_SERVER['HTTP_USER_AGENT']))) {  
            return TRUE;  
        }  

        if ((strpos(strtolower($_SERVER['HTTP_ACCEPT']),'application/vnd.wap.xhtml+xml') > 0) or ((isset($_SERVER['HTTP_X_WAP_PROFILE']) or isset($_SERVER['HTTP_PROFILE'])))) {  
            return TRUE;  
        }      

        $mobile_ua = strtolower(substr($_SERVER['HTTP_USER_AGENT'], 0, 4));  
        $mobile_agents = array(  
            'w3c ','acs-','alav','alca','amoi','audi','avan','benq','bird','blac',  
            'blaz','brew','cell','cldc','cmd-','dang','doco','eric','hipt','inno',  
            'ipaq','java','jigs','kddi','keji','leno','lg-c','lg-d','lg-g','lge-',  
            'maui','maxo','midp','mits','mmef','mobi','mot-','moto','mwbp','nec-',  
            'newt','noki','oper','palm','pana','pant','phil','play','port','prox',  
            'qwap','sage','sams','sany','sch-','sec-','send','seri','sgh-','shar',  
            'sie-','siem','smal','smar','sony','sph-','symb','t-mo','teli','tim-',  
            'tosh','tsm-','upg1','upsi','vk-v','voda','wap-','wapa','wapi','wapp',  
            'wapr','webc','winw','winw','xda ','xda-');  

        if (in_array($mobile_ua,$mobile_agents)) {  
            return TRUE;  
        }  

        if (isset($_SERVER['ALL_HTTP']) && strpos(strtolower($_SERVER['ALL_HTTP']),'OperaMini') > 0) {  
            return TRUE;  
        }  

        $showDesktop = $this->_customerSession()->getShowDesktop(); //AW VARIABLE
        if ($showDesktop === FALSE){
            return TRUE;  
        }

        return FALSE;  
    } 
    
    protected function _customerSession()
    {
        return Mage::getSingleton('customer/session');
    }
}
?>