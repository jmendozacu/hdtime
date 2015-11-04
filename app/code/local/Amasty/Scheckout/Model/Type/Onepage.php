<?php
    class Amasty_Scheckout_Model_Type_Onepage extends Mage_Checkout_Model_Type_Onepage
    {
        public function initCheckout(){
            $hlr = Mage::helper("amscheckout");
            
            parent::initCheckout();
            
            $this->getQuote()->getBillingAddress()
                    ->setCountryId($hlr->getDefaultCountry())
                    ->setCity($hlr->getDefaultCity())
                    ->setPostcode($hlr->getDefaultPostcode());
            
            $this->getQuote()->getShippingAddress()
                    ->setCountryId($hlr->getDefaultCountry())
                    ->setCity($hlr->getDefaultCity())
                    ->setPostcode($hlr->getDefaultPostcode())
                    ->setSameAsBilling(1)
                    ->setSaveInAddressBook(0)
                    ->setCollectShippingRates(true)
                    ->setShippingMethod($hlr->getDefaultShippingMethod($this->getQuote()));
            
            $this->getQuote()->getPayment()->setMethod($hlr->getDefaultPeymentMethod($this->getQuote()));
            
        }
    }
?>