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

require_once 'Mage/Checkout/controllers/CartController.php';

class Uni_Occheckout_CartController extends Mage_Checkout_CartController {

    /**
     * Add product to shopping cart action 
     */
    public function addAction() {
        $cart = $this->_getCart();
        $params = $this->getRequest()->getParams();
        /**
         * adding product to cart using ajax 
         */
        if ($params['isAjax'] == 1) {
            $isTest = $params['checkData'];
            $response = array();
            try {
                if (isset($params['qty'])) {
                    $filter = new Zend_Filter_LocalizedToNormalized(
                            array('locale' => Mage::app()->getLocale()->getLocaleCode())
                    );
                    $params['qty'] = $filter->filter($params['qty']);
                }

                $product = $this->_initProduct();
                $related = $this->getRequest()->getParam('related_product');

                /**
                 * Check product availability
                 */
                if (!$product) {
                    $response['status'] = 'ERROR';
                    $response['message'] = $this->__('Unable to find Product ID');
                }

                $cart->addProduct($product, $params);
                if (!empty($related)) {
                    $cart->addProductsByIds(explode(',', $related));
                }
                if (!$isTest) {

                    $cart->save();

                    $this->_getSession()->setCartWasUpdated(true);
                }
                /**
                 * @todo remove wishlist observer processAddToCart
                 */
                Mage::dispatchEvent('checkout_cart_add_product_complete', array('product' => $product, 'request' => $this->getRequest(), 'response' => $this->getResponse())
                );

                if (!$this->_getSession()->getNoCartRedirect(true)) {
                    if (!$cart->getQuote()->getHasError()) {
                        $message = $this->__('%s was added to your shopping cart.', Mage::helper('core')->htmlEscape($product->getName()));
                        $response['status'] = 'SUCCESS';
                        $response['message'] = $message;
                    }
                }
            } catch (Mage_Core_Exception $e) {
                $msg = "";
                if ($this->_getSession()->getUseNotice(true)) {
                    $msg = $e->getMessage();
                } else {
                    $messages = array_unique(explode("\n", $e->getMessage()));
                    foreach ($messages as $message) {
                        $msg .= $message . '<br/>';
                    }
                }

                $response['status'] = 'ERROR';
                $response['message'] = strip_tags($msg);
            } catch (Exception $e) {
                $response['status'] = 'ERROR';
                $response['message'] = $this->__('Cannot add the item to shopping cart.');
                Mage::logException($e);
            }
            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
            return;
        }

        /**
         * end of ajax code
         */
        try {
            if (isset($params['qty'])) {
                $filter = new Zend_Filter_LocalizedToNormalized(
                        array('locale' => Mage::app()->getLocale()->getLocaleCode())
                );
                $params['qty'] = $filter->filter($params['qty']);
            }

            $product = $this->_initProduct();
            $related = $this->getRequest()->getParam('related_product');

            /**
             * Check product availability
             */
            if (!$product) {
                $this->_goBack();
                return;
            }

            $cart->addProduct($product, $params);
            if (!empty($related)) {
                $cart->addProductsByIds(explode(',', $related));
            }

            $cart->save();

            $this->_getSession()->setCartWasUpdated(true);

            /**
             * @todo remove wishlist observer processAddToCart
             */
            Mage::dispatchEvent('checkout_cart_add_product_complete', array('product' => $product, 'request' => $this->getRequest(), 'response' => $this->getResponse())
            );

            if (!$this->_getSession()->getNoCartRedirect(true)) {
                if (!$cart->getQuote()->getHasError()) {
                    $message = $this->__('%s was added to your shopping cart.', Mage::helper('core')->escapeHtml($product->getName()));
                    $this->_getSession()->addSuccess($message);
                }
                $this->_goBack();
            }
        } catch (Mage_Core_Exception $e) {
            if ($this->_getSession()->getUseNotice(true)) {
                $this->_getSession()->addNotice(Mage::helper('core')->escapeHtml($e->getMessage()));
            } else {
                $messages = array_unique(explode("\n", $e->getMessage()));
                foreach ($messages as $message) {
                    $this->_getSession()->addError(Mage::helper('core')->escapeHtml($message));
                }
            }

            $url = $this->_getSession()->getRedirectUrl(true);
            if ($url) {
                $this->getResponse()->setRedirect($url);
            } else {
                $this->_redirectReferer(Mage::helper('checkout/cart')->getCartUrl());
            }
        } catch (Exception $e) {
            $this->_getSession()->addException($e, $this->__('Cannot add the item to shopping cart.'));
            Mage::logException($e);
            $this->_goBack();
        }
    }

    /**
     * Update shoping cart on one click checkout
     */
    protected function _updateShoppingCart() {
        try {
            $cartData = $this->getRequest()->getParam('cart');
            if (is_array($cartData)) {
                $filter = new Zend_Filter_LocalizedToNormalized(
                        array('locale' => Mage::app()->getLocale()->getLocaleCode())
                );
                foreach ($cartData as $index => $data) {
                    if (isset($data['qty'])) {
                        $cartData[$index]['qty'] = $filter->filter(trim($data['qty']));
                    }
                }
                $cart = $this->_getCart();
                if (!$cart->getCustomerSession()->getCustomer()->getId() && $cart->getQuote()->getCustomerId()) {
                    $cart->getQuote()->setCustomerId(null);
                }

                $cartData = $cart->suggestItemsQty($cartData);
                $cart->updateItems($cartData)
                        ->save();
            }
            $this->_getSession()->setCartWasUpdated(true);
        } catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError(Mage::helper('core')->escapeHtml($e->getMessage()));
        } catch (Exception $e) {
            $this->_getSession()->addException($e, $this->__('Cannot update shopping cart.'));
            Mage::logException($e);
        }
    }

    /**
     * Delete shoping cart item action
     */
    public function updateItemOptionsAction() {
        $cart = $this->_getCart();
        $id = (int) $this->getRequest()->getParam('id');
        $params = $this->getRequest()->getParams();
        /**
         * updating cart quantity using ajax
         */
        if ($params['isAjax'] == 1) {
            if (!isset($params['options'])) {
                $params['options'] = array();
            }
            try {
                if (isset($params['qty'])) {
                    $filter = new Zend_Filter_LocalizedToNormalized(
                            array('locale' => Mage::app()->getLocale()->getLocaleCode())
                    );
                    $params['qty'] = $filter->filter($params['qty']);
                }

                $quoteItem = $cart->getQuote()->getItemById($id);
                if (!$quoteItem) {
                    Mage::throwException($this->__('Quote item is not found.'));
                }

                $item = $cart->updateItem($id, new Varien_Object($params));
                if (is_string($item)) {
                    Mage::throwException($item);
                }
                if ($item->getHasError()) {
                    Mage::throwException($item->getMessage());
                }

                $related = $this->getRequest()->getParam('related_product');
                if (!empty($related)) {
                    $cart->addProductsByIds(explode(',', $related));
                }

                $cart->save();

                $this->_getSession()->setCartWasUpdated(true);

                Mage::dispatchEvent('checkout_cart_update_item_complete', array('item' => $item, 'request' => $this->getRequest(), 'response' => $this->getResponse())
                );
                if (!$this->_getSession()->getNoCartRedirect(true)) {
                    if (!$cart->getQuote()->getHasError()) {
                        $message = $this->__('%s was updated in your shopping cart.', Mage::helper('core')->htmlEscape($item->getProduct()->getName()));
                        $response['status'] = 'SUCCESS';
                        $response['message'] = $message;
                    }
                }
            } catch (Mage_Core_Exception $e) {
                if ($this->_getSession()->getUseNotice(true)) {
                    $this->_getSession()->addNotice($e->getMessage());
                } else {
                    $messages = array_unique(explode("\n", $e->getMessage()));
                    foreach ($messages as $message) {
                        $this->_getSession()->addError($message);
                    }
                }
                $response['status'] = 'ERROR';
                $response['message'] = $message;
            } catch (Exception $e) {
                $this->_getSession()->addException($e, $this->__('Cannot update the item.'));
                $response['status'] = 'ERROR';
                $response['message'] = $this->__('Cannot add the item to shopping cart.');
                Mage::logException($e);
            }
            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
            return;
            /**
             * updating using ajax ends
             */
        } else {
            if (!isset($params['options'])) {
                $params['options'] = array();
            }
            try {
                if (isset($params['qty'])) {
                    $filter = new Zend_Filter_LocalizedToNormalized(
                            array('locale' => Mage::app()->getLocale()->getLocaleCode())
                    );
                    $params['qty'] = $filter->filter($params['qty']);
                }

                $quoteItem = $cart->getQuote()->getItemById($id);
                if (!$quoteItem) {
                    Mage::throwException($this->__('Quote item is not found.'));
                }

                $item = $cart->updateItem($id, new Varien_Object($params));
                if (is_string($item)) {
                    Mage::throwException($item);
                }
                if ($item->getHasError()) {
                    Mage::throwException($item->getMessage());
                }

                $related = $this->getRequest()->getParam('related_product');
                if (!empty($related)) {
                    $cart->addProductsByIds(explode(',', $related));
                }

                $cart->save();

                $this->_getSession()->setCartWasUpdated(true);

                Mage::dispatchEvent('checkout_cart_update_item_complete', array('item' => $item, 'request' => $this->getRequest(), 'response' => $this->getResponse())
                );
                if (!$this->_getSession()->getNoCartRedirect(true)) {
                    if (!$cart->getQuote()->getHasError()) {
                        $message = $this->__('%s was updated in your shopping cart.', Mage::helper('core')->htmlEscape($item->getProduct()->getName()));
                        $this->_getSession()->addSuccess($message);
                    }
                    $this->_goBack();
                }
            } catch (Mage_Core_Exception $e) {
                if ($this->_getSession()->getUseNotice(true)) {
                    $this->_getSession()->addNotice($e->getMessage());
                } else {
                    $messages = array_unique(explode("\n", $e->getMessage()));
                    foreach ($messages as $message) {
                        $this->_getSession()->addError($message);
                    }
                }

                $url = $this->_getSession()->getRedirectUrl(true);
                if ($url) {
                    $this->getResponse()->setRedirect($url);
                } else {
                    $this->_redirectReferer(Mage::helper('checkout/cart')->getCartUrl());
                }
            } catch (Exception $e) {
                $this->_getSession()->addException($e, $this->__('Cannot update the item.'));
                Mage::logException($e);
                $this->_goBack();
            }
            $this->_redirect('*/*');
        }
    }

    /**
     * Delete shoping cart item action
     */
    public function deleteAction() {
        $isAjaxRequest = $this->getRequest()->getParam('isAjax');
        if ($isAjaxRequest) {
            $id = (int) $this->getRequest()->getParam('id');
            if ($id) {
                try {
                    $this->_getCart()->removeItem($id)
                            ->save();
                } catch (Exception $e) {
                    $this->_getSession()->addError($this->__('Cannot remove the item.'));
                    Mage::logException($e);
                }
            }
            return;
        } else {
            $id = (int) $this->getRequest()->getParam('id');
            if ($id) {
                try {
                    $this->_getCart()->removeItem($id)
                            ->save();
                } catch (Exception $e) {
                    $this->_getSession()->addError($this->__('Cannot remove the item.'));
                    Mage::logException($e);
                }
            }
            $this->_redirectReferer(Mage::getUrl('*/*'));
        }
    }

    /**
     * checking the type of cart update action
     */
    public function updatePostAction() {
        $updateAction = (string) $this->getRequest()->getParam('update_cart_action');

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

        $this->_goBack();
    }

}
