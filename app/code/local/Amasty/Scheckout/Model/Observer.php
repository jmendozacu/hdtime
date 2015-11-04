<?php
/**
* @author Amasty Team
* @copyright Amasty
* @package Amasty_Ogrid
*/
class Amasty_Scheckout_Model_Observer 
{
    protected function _getOnepage()
    {
        return Mage::getSingleton('amscheckout/type_onepage');
    }
    
    public function onControllerActionPredispatch($observer){
       if($observer->getEvent()->getControllerAction()->getFullActionName() == 'checkout_cart_index'
               ){
           $hlr = Mage::helper("amscheckout");
            
           if ($hlr->isShoppingCartOnCheckout()) {
                $quote = $this->_getOnepage()->getQuote();
                     if (!$quote->hasItems() || $quote->getHasError() || !$quote->validateMinimumAmount()) {
                         return;
                     } else {
                    // Compose array of messages to add
                     $messages = array();
                     foreach ( $this->_getOnepage()->getQuote()->getMessages() as $message) {
                         if ($message) {
                             // Escape HTML entities in quote message to prevent XSS
                             $message->setCode(Mage::helper('core')->escapeHtml($message->getCode()));
                             $messages[] = $message;
                         }
                     }

                    Mage::getSingleton('customer/session')->addUniqueMessages($messages);

                    foreach(Mage::getSingleton('checkout/session')->getMessages()->getItems() as $message){

                        Mage::getSingleton('customer/session')->addMessage($message);
                    }


                    $url = Mage::getUrl('checkout/onepage', array('_secure'=>true));
                    $observer->getControllerAction()->getResponse()->setRedirect($url);    
                }
           }
        } else if ($observer->getEvent()->getControllerAction()->getFullActionName() == 'checkout_onepage_index'){
           $quote = $this->_getOnepage()->getQuote();
           $quote->collectTotals();
           $quote->save();
        }
    }
}
?>