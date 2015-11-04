<?php
/**
 * Class Me_Cmb_Model_System_Config_Backend_Notification
 *
 * @category  Me
 * @package   Me_Lff
 * @author    Attila Sági <sagi.attila@magevolve.com>
 * @copyright 2015 Magevolve Ltd. (http://magevolve.com)
 * @license   http://magevolve.com/terms-and-conditions Magevolve Ltd. License
 * @link      http://magevolve.com
 */

/**
 * Class Me_Cmb_Model_System_Config_Backend_Notification
 */
class Me_Cmb_Model_System_Config_Backend_Notification extends Mage_Core_Model_Config_Data
{
    /**
     * Save object data
     *
     * @return Mage_Core_Model_Abstract
     */
//    public function save()
//    {
//        $value = $this->getValue();
//        if ($value) {
//            if (strpos($value, '%s') === false) {
//                Mage::throwException($this->_getLffHelper()->__('The notification text must include %s. Please correct it!'));
//            }
//
//        } else {
//            Mage::throwException($this->_getLffHelper()->__('The notification text can not be empty. Please correct it!'));
//        }
//
//        return parent::save();
//    }

    /**
     * Processing object after save data
     *
     * @return Mage_Core_Model_Abstract
     */
    public function save()
    {
        $hasError = false;

        $value = $this->getValue();
        if ($value) {

            $predefinedTimes = $this->getFieldsetDataValue('predefined');
            if (is_array($predefinedTimes) && count($predefinedTimes) == 1 && isset($predefinedTimes['__empty']) && empty($predefinedTimes['__empty'])) {
                $hasError = true;
            } else {
                foreach ($predefinedTimes as $key => $itemValue) {
                    if ($key != '__empty'
                        && (empty($itemValue) || (isset($itemValue['predefined']) && empty($itemValue['predefined'])))
                    ) {
                        $hasError = true;
                        break;
                    }
                }


            }

            if ($value && $hasError) {
                Mage::throwException(
                    $this->_getCmbHelper()->__('Show predefined times as drop down is enabled, but predefined times is empty. Please correct it!')
                );
            }
        }

        return parent::save();
    }

    /**
     * Get extension helper
     *
     * @return Me_Cmb_Helper_Data
     */
    protected function _getCmbHelper()
    {
        return Mage::helper('me_cmb');
    }
}
