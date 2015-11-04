<?php
/**
 * Class Me_Cmb_Block_Request
 *
 * @category  Me
 * @package   Me_Cmb
 * @author    Attila Sági <sagi.attila@aion.hu>
 * @copyright 2015 Magevolve Ltd. (http://magevolve.com)
 * @license   http://magevolve.com/terms-and-conditions Magevolve License
 * @link      http://magevolve.com
 */

/**
 * Class Me_Cmb_Block_Request
 */
class Me_Cmb_Block_Request extends Mage_Core_Block_Template
{
    /**
     * Get ajax submit url
     *
     * @return string
     */
    public function getAjaxUrl()
    {
        return $this->getUrl(
            'cmb/ajax/update',
            array('_secure' => Mage::app()->getStore()->isCurrentlySecure())
        );
    }

    /**
     * Get form key
     *
     * @return string
     */
    public function getFormKey()
    {
        return Mage::getSingleton('core/session')->getFormKey();
    }

    /**
     * Get date localized format
     *
     * @return string
     */
    public function getDateFormat()
    {
        $format = Mage::app()->getLocale()->getDateStrFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);

        return $format;
    }

    /**
     * Get predefined times
     *
     * @return array
     */
    public  function getPredefinedTimes()
    {
        $predefinedTimes = unserialize($this->_getCmbHelper()->getPredefinedTimes());

        return $predefinedTimes;
    }

    /**
     * Get logged in customer name
     *
     * @return string
     */
    public function getCustomerName()
    {
        $customerName = '';

        if ($this->_getCustomerSession()->isLoggedIn()) {
            $customerName = $this->_getCustomerSession()->getCustomer()->getName();
        }

        return $customerName;
    }

    /**
     * Get customer session
     *
     * @return Mage_Customer_Model_Session
     */
    protected function _getCustomerSession()
    {
        return Mage::getSingleton('customer/session');
    }

    /**
     * Prepare layout
     *
     * @return Mage_Core_Block_Abstract
     */
    protected function _prepareLayout()
    {
        $this->getLayout()->getBlock('head')->append(
            $this->getLayout()->createBlock(
                'Mage_Core_Block_Html_Calendar',
                'html_calendar',
                array('template' => 'page/js/calendar.phtml')
            )
        );

        $this->getLayout()->getBlock('head')
            ->addItem('js_css', 'calendar/calendar-win2k-1.css')
            ->addJs('calendar/calendar.js')
            ->addJs('calendar/calendar-setup.js');

        return parent::_prepareLayout();
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
