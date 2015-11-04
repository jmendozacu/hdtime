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

class Uni_Occheckout_Block_Checkout_Onepage_Payment_Methods extends Mage_Checkout_Block_Onepage_Payment_Methods {

    /**
     * Payment method form html getter
     * @param Mage_Payment_Model_Method_Abstract $method
     */
    public function getPaymentMethodFormHtml(Mage_Payment_Model_Method_Abstract $method) {
        return $this->getChildHtml('payment.method.' . $method->getCode());
    }
    
    

}
