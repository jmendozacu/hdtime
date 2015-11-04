<?php
class Komplizierte_CustomerCallbacks_Helper_Data extends Mage_Core_Helper_Abstract {

    public function sendNotificationOfCallback() {
        $emailAddress = Mage::getStoreConfig('komplizierte_customercallbacks/email/email_to');
        if($emailAddress) {
            $emailTemplate  = Mage::getModel('core/email_template')->loadDefault('callback_notification_email_template');

            $emailTemplateVariables = array();

            $emailTemplate->getProcessedTemplate($emailTemplateVariables);

            $emailTemplate->setSenderEmail(Mage::getStoreConfig('trans_email/ident_general/email'));
            $emailTemplate->setSenderName(Mage::getStoreConfig('trans_email/ident_general/name'));

            $emailTemplate->send($emailAddress, Mage::helper('komplizierte_customercallbacks')->__('Callbacks Moderator'), $emailTemplateVariables);
        }
    }

}