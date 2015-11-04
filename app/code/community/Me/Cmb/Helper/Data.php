<?php
/**
 * Class Me_Cmb_Helper_Data
 *
 * @category  Me
 * @package   Me_Cmb
 * @author    Attila Sági <sagi.attila@aion.hu>
 * @copyright 2015 Magevolve Ltd. (http://magevolve.com)
 * @license   http://magevolve.com/terms-and-conditions Magevolve License
 * @link      http://magevolve.com
 */

/**
 * Class Me_Cmb_Helper_Data
 */
class Me_Cmb_Helper_Data extends Mage_Core_Helper_Data
{
    /**
     * Path to store config if front-end output is enabled
     *
     * @var string
     */
    const XML_PATH_ENABLED = 'cmb/config/enabled';

    /**
     * Path to store config block title in sidebar
     *
     * @var string
     */
    const XML_PATH_TITLE = 'cmb/display/title';

    /**
     * Path to store config block message in sidebar
     *
     * @var string
     */
    const XML_PATH_MESSAGE = 'cmb/display/message';

    /**
     * Path to store config block success response in sidebar
     *
     * @var string
     */
    const XML_PATH_RESPONSE = 'cmb/display/response';

    /**
     * Path to store config success message delay time
     *
     * @var string
     */
    const XML_PATH_DELAY = 'cmb/display/delay';

    /**
     * Path to store config display block in sidebar
     *
     * @var string
     */
    const XML_PATH_SIDEBAR = 'cmb/display/sidebar';

    /**
     * Path to store config display date in block
     *
     * @var string
     */
    const XML_PATH_DATE = 'cmb/schedule/date';

    /**
     * Path to store config if date is required in block
     *
     * @var string
     */
    const XML_PATH_MANDATORY = 'cmb/schedule/mandatory';

    /**
     * Path to store config display predefined times in block
     *
     * @var string
     */
    const XML_PATH_SHOW_PREDEFINED = 'cmb/schedule/show_predefined';

    /**
     * Path to store config predefined times
     *
     * @var string
     */
    const XML_PATH_PREDEFINED = 'cmb/schedule/predefined';

    /**
     * Path to store config email sending enabled
     *
     * @var string
     */
    const XML_PATH_EMAIL_ENABLED = 'cmb/email/enabled';

    /**
     * Path to store config recipient email address
     *
     * @var string
     */
    const XML_PATH_EMAIL_RECIPIENT = 'cmb/email/recipient_email';

    /**
     * Path to store config bcc email address
     *
     * @var string
     */
    const XML_PATH_EMAIL_BCC = 'cmb/email/bcc_email';

    /**
     * Path to store config sender email
     *
     * @var string
     */
    const XML_PATH_EMAIL_SENDER = 'cmb/email/sender_email_identity';

    /**
     * Path to store config email template
     *
     * @var string
     */
    const XML_PATH_EMAIL_TEMPLATE = 'cmb/email/email_template';

    /**
     * Checks whether extension is enabled
     *
     * @param integer|string|Mage_Core_Model_Store $store store
     * @return boolean
     */
    public function isEnabled($store = null)
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_ENABLED, $store);
    }

    /**
     * Get block title in sidebar
     *
     * @param integer|string|Mage_Core_Model_Store $store store
     * @return boolean
     */
    public function getBlockTitle($store = null)
    {
        return Mage::getStoreConfig(self::XML_PATH_TITLE, $store);
    }

    /**
     * Get block message in sidebar
     *
     * @param integer|string|Mage_Core_Model_Store $store store
     * @return boolean
     */
    public function getBlockMessage($store = null)
    {
        return Mage::getStoreConfig(self::XML_PATH_MESSAGE, $store);
    }

    /**
     * Get block success response
     *
     * @param integer|string|Mage_Core_Model_Store $store store
     * @return boolean
     */
    public function getSuccessMessage($store = null)
    {
        return Mage::getStoreConfig(self::XML_PATH_RESPONSE, $store);
    }

    /**
     * Get block success response delay time
     *
     * @param integer|string|Mage_Core_Model_Store $store store
     * @return boolean
     */
    public function getSuccessDelay($store = null)
    {
        return Mage::getStoreConfig(self::XML_PATH_DELAY, $store);
    }

    /**
     * Get where to display block in sidebar
     *
     * @param integer|string|Mage_Core_Model_Store $store store
     * @return boolean
     */
    public function getPositionInSidebar($store = null)
    {
        return Mage::getStoreConfig(self::XML_PATH_SIDEBAR, $store);
    }

    /**
     * Checks whether show date in block
     *
     * @param integer|string|Mage_Core_Model_Store $store store
     * @return boolean
     */
    public function getShowDate($store = null)
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_DATE, $store);
    }

    /**
     * Checks whether showed date is required in block
     *
     * @param integer|string|Mage_Core_Model_Store $store store
     * @return boolean
     */
    public function getDateIsMandatory($store = null)
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_MANDATORY, $store);
    }

    /**
     * Checks whether show predefined times in block
     *
     * @param integer|string|Mage_Core_Model_Store $store store
     * @return boolean
     */
    public function getShowPredefinedTimes($store = null)
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_SHOW_PREDEFINED, $store);
    }

    /**
     * Get predefined times values
     *
     * @param integer|string|Mage_Core_Model_Store $store store
     * @return boolean
     */
    public function getPredefinedTimes($store = null)
    {
        return Mage::getStoreConfig(self::XML_PATH_PREDEFINED, $store);
    }

    /**
     * Checks whether extension is enabled
     *
     * @param integer|string|Mage_Core_Model_Store $store store
     * @return boolean
     */
    public function isEmailEnabled($store = null)
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_EMAIL_ENABLED, $store);
    }

    /**
     * Get email recipient
     *
     * @param integer|string|Mage_Core_Model_Store $store store
     * @return boolean
     */
    public function getEmailRecipient($store = null)
    {
        return Mage::getStoreConfig(self::XML_PATH_EMAIL_RECIPIENT, $store);
    }

    /**
     * Get email bcc recipient
     *
     * @param integer|string|Mage_Core_Model_Store $store store
     * @return boolean
     */
    public function getEmailBcc($store = null)
    {
        return Mage::getStoreConfig(self::XML_PATH_EMAIL_BCC, $store);
    }

    /**
     * Get email sender
     *
     * @param integer|string|Mage_Core_Model_Store $store store
     * @return boolean
     */
    public function getEmailSender($store = null)
    {
        return Mage::getStoreConfig(self::XML_PATH_EMAIL_SENDER, $store);
    }

    /**
     * Get email template
     *
     * @param integer|string|Mage_Core_Model_Store $store store
     * @return boolean
     */
    public function getEmailTemplate($store = null)
    {
        return Mage::getStoreConfig(self::XML_PATH_EMAIL_TEMPLATE, $store);
    }
}
