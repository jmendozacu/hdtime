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

class Uni_Occheckout_Block_Updatecart extends Mage_Core_Block_Template {

    /**
     * getting product ids added in cart
     * @return type
     */
    public function removeConfigurableProduct() {
        $result = array();
        $session = Mage::getSingleton('checkout/session');
        foreach ($session->getQuote()->getAllItems() as $item) {
            $result[] = $item->getProduct()->getId();
        }
        return $result;
    }

}
