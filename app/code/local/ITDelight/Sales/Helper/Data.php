<?php
/**
 * Created by PhpStorm.
 * User: Игорь
 * Date: 10.03.2015
 * Time: 14:00
 */


class ITDelight_Sales_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function generatePassword($length = 8)
    {
        $chars = Mage_Core_Helper_Data::CHARS_PASSWORD_LOWERS
            . Mage_Core_Helper_Data::CHARS_PASSWORD_UPPERS
            . Mage_Core_Helper_Data::CHARS_PASSWORD_DIGITS
            . Mage_Core_Helper_Data::CHARS_PASSWORD_SPECIALS;
        return Mage::helper('core')->getRandomString($length, $chars);
    }
}