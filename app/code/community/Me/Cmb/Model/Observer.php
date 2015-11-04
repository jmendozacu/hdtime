<?php
/**
 * Class Me_Cmb_Model_Observer
 *
 * @category  Me
 * @package   Me_Cmb
 * @author    Attila Sági <sagi.attila@aion.hu>
 * @copyright 2015 Magevolve Ltd. (http://magevolve.com)
 * @license   http://magevolve.com/terms-and-conditions Magevolve License
 * @link      http://magevolve.com
 */

/**
 * Class Me_Cmb_Model_Observer
 */
class Me_Cmb_Model_Observer
{
    /**
     * Event add block to sidebar automatically
     *
     * @param Varien_Event_Observer $observer observer
     * @return $this
     */
    public function addRequestBlock(Varien_Event_Observer $observer)
    {
        if (!$this->_getCmbHelper()->isEnabled()) {
            return false;
        }

        try {

            $layout = $observer->getAction()->getLayout();
            $sidebarReference = $this->_getCmbHelper()->getPositionInSidebar();

            $sidebarBlock = $layout->getBlock($sidebarReference);
            if ($sidebarBlock) {

                $requestBlock = $layout->createBlock('me_cmb/request')
                    ->setName('me.cmb.request.sidebar')
                    ->setTemplate('me/cmb/request.phtml');

                if ($requestBlock) {
                    $sidebarBlock->append($requestBlock);
                }

            }

        } catch (Mage_Core_Exception $e) {

            Mage::log($e->getMessage());

        } catch (Exception $e) {

            Mage::logException($e);

        }

        return $this;
    }

    /**
     * Event after callback request has been submitted
     *
     * @param Varien_Event_Observer $observer observer
     * @return $this|boolean
     * @throws Exception
     */
    public function sendNotificationEmail(Varien_Event_Observer $observer)
    {
        $_helper = $this->_getCmbHelper();

        if (!$_helper->isEnabled() && !$_helper->isEmailEnabled()) {
            return false;
        }

        /* @var $callbackRequest Me_Cmb_Model_Cmb */
        $callbackRequest = $observer->getCallbackInfo();

        if (!is_null($callbackRequest) && $callbackRequest->getId()) {

            $translate = Mage::getSingleton('core/translate');
            /* @var $translate Mage_Core_Model_Translate */
            $translate->setTranslateInline(false);
            try {
                $error = false;

                $callDate = '-';
                if ($callDate = $callbackRequest->getCmbCallDate()) {
                    $callDate = Mage::helper('core')->formatDate($callDate, Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM);
                }

                $mailTemplate = Mage::getModel('core/email_template');
                /* @var $mailTemplate Mage_Core_Model_Email_Template */
                $mailTemplate->setDesignConfig(array('area' => 'frontend', 'store' => Mage::app()->getStore()->getId()));
                if ($_helper->getEmailBcc()) {
                    $mailTemplate->addBcc($_helper->getEmailBcc());
                }
                $mailTemplate->sendTransactional(
                    $_helper->getEmailTemplate(),
                    $_helper->getEmailSender(),
                    $_helper->getEmailRecipient(),
                    null,
                    array(
                        'cmb_full_name' => $callbackRequest->getCmbFullName(),
                        'cmb_telephone' => $callbackRequest->getCmbTelephone(),
                        'cmb_call_date' => $callDate,
                        'cmb_predefined' => $callbackRequest->getCmbPredefined() ? $callbackRequest->getCmbPredefined() : '-'
                    )
                );

                if (!$mailTemplate->getSentSuccess()) {
                    throw new Exception('Callback request admin notification email send error.');
                }

                $translate->setTranslateInline(true);

            } catch (Exception $e) {
                $translate->setTranslateInline(true);
                Mage::logException($e);
            }

        }

        return $this;
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
