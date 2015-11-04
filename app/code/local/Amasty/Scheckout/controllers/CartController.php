<?php
/**
* @author Amasty Team
* @copyright Amasty
* @package Amasty_Scheckout
*/
require_once Mage::getModuleDir('controllers', 'Mage_Checkout').DS.'CartController.php';

class Amasty_Scheckout_CartController extends Mage_Checkout_CartController
{
    
    public function updatePostAction()
    {
        parent::updatePostAction();
        
//        $this->_getSession()->setCartWasUpdated(true);
//        
//        $updateAction = (string)$this->getRequest()->getParam('update_cart_action');
//
//        switch ($updateAction) {
//            case 'empty_cart':
//                $this->_emptyShoppingCart();
//                break;
//            case 'update_qty':
//                $this->_updateShoppingCart();
//                break;
//            default:
//                $this->_updateShoppingCart();
//        }
        
        $result['goto_section'] = 'review';
        $result['update_section'] = array(
            'name' => 'review',
            'html' => $this->_getReviewHtml()
        );
        
        $result['update_section']['checkout_cart']  = $this->_getCheckoutCartHtml();

        
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
        
    }
    
     protected function _getCheckoutCartHtml(){  
        $cart_content = '';
        
        
        
        $cart = Mage::getSingleton('checkout/cart');
                
        if ($cart->getQuote()->getItemsCount()) {
            $cart->init();
            $cart->save();
            
            $this->_initLayoutMessages('checkout/session');
            $this->_initLayoutMessages('catalog/session');
        
            $layout = Mage::getSingleton('core/layout');
            $cart = $layout->createBlock('checkout/cart');
            $cart->setTemplate('checkout/cart.phtml');
            $cart_content = $cart->toHTML();
            
        }
                
        
        return $cart_content;
    }
    
    
    protected function _getReviewHtml()
    {
        $this->loadLayout('checkout_onepage_review');
        return $this->getLayout()->getBlock('root')->toHtml();

    }
    
    public function deleteAction()
    {
        $id = (int) $this->getRequest()->getParam('id');
        
        $this->_getCart()->removeItem($id)->save();
        
        $result['goto_section'] = 'review';
        $result['update_section'] = array(
            'name' => 'review',
            'html' => $this->_getReviewHtml()
        );
        
        $result['update_section']['checkout_cart']  = $this->_getCheckoutCartHtml();
        
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }
    
    public function couponPostAction(){
       
        parent::couponPostAction();
        
        
        $this->_initLayoutMessages('checkout/session');
        $this->_initLayoutMessages('catalog/session');
            
        $layout = Mage::getSingleton('core/layout');
        $cart = $layout->createBlock('checkout/cart_coupon');
        $cart->setTemplate('checkout/cart/coupon.phtml');
        $cart_content = $cart->toHTML();
            
        print $cart_content;
        
        print $this->_getCheckoutCartHtml();
    }
    
    protected function _goBack()
    {
        return $this;
    }    
}
