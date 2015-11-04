<?php
/**
 * Class Me_Cmb_AjaxController
 *
 * @category  Me
 * @package   Me_Cmb
 * @author    Attila Sági <sagi.attila@aion.hu>
 * @copyright 2015 Magevolve Ltd. (http://magevolve.com)
 * @license   http://magevolve.com/terms-and-conditions Magevolve License
 * @link      http://magevolve.com
 */

/**
 * Class Me_Cmb_AjaxController
 */
class Me_Cmb_AjaxController extends Mage_Core_Controller_Front_Action
{
    /**
     * Ajax answer
     *
     * @var array
     */
    protected $_answer = array(
        'success' => 0,
        'html' => '',
        'message' => ''
    );

    /**
     * Pre dispatch action that allows to redirect to no route page in case of disabled extension through admin panel
     *
     * @return void
     */
    public function preDispatch()
    {
        parent::preDispatch();

        if (!$this->_getCmbHelper()->isEnabled()) {
            $this->_answer['message'] = $this->_getCmbHelper()->__('An error occurred during request.');
            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($this->_answer));
        }
    }

    /**
     * Index action
     *
     * @throws Exception
     * @return void
     */
    public function updateAction()
    {
        $_helper = $this->_getCmbHelper();
        $params = $this->getRequest()->getParams();

        if ($params) {

            try {
                if (!$this->_validateFormKey()) {
                    throw new Exception($_helper->__('Invalid form key.'));
                }

                $error = false;
                $errorMsg = '';
                if (isset($params['cmb_full_name']) && isset($params['cmb_telephone'])) {

                    $data = $this->_filterPostData($params);

                    if (!Zend_Validate::is(trim($params['cmb_full_name']), 'NotEmpty')) {
                        $error = true;
                    }

                    if (!Zend_Validate::is(trim($params['cmb_telephone']), 'NotEmpty')) {
                        $error = true;
                    }

                    if (!Zend_Validate::is(trim($params['cmb_call_date']), 'NotEmpty') && $_helper->getDateIsMandatory()) {
                        $error = true;
                    }

                    if (isset($data['cmb_call_date']) && !empty($data['cmb_call_date'])) {
                        $validator = new Zend_Validate_Date(Varien_Date::DATE_INTERNAL_FORMAT);
                        if (!$validator->isValid($data['cmb_call_date'])) {
                            throw new Exception($_helper->__('Invalid date.'));
                        }
                    }

                    $now = time();
                    if (isset($data['cmb_call_date'])
                        && !empty($data['cmb_call_date'])
                        && (strtotime($data['cmb_call_date']) < ($now - ($now % (24*60*60))) || strtotime($data['cmb_call_date']) > strtotime('+1 year'))
                    ) {
                        $errorMsg = $_helper->__('Invalid date. The page will reload automatically!');
                        $error = true;
                    }

                    if ($error) {
                        if ($errorMsg) {
                            throw new Exception($errorMsg);
                        } else {
                            throw new Exception($_helper->__('Invalid form values. The page will reload automatically!'));
                        }
                    }

                    $callbackRequest = Mage::getModel('me_cmb/cmb');
                    $callbackRequest->addData($data);
                    $callbackRequest->setStatus($callbackRequest::STATUS_NEW);
                    $callbackRequest->setStoreId(Mage::app()->getStore()->getId());
                    $callbackRequest->save();

                    if ($_helper->isEmailEnabled()) {
                        Mage::dispatchEvent('me_cmb_send_notification', array('callback_info' => $callbackRequest));
                    }

                    if ($successMsg = $_helper->getSuccessMessage()) {
                        $this->_answer['html'] = $successMsg;
                    } else {
                        $this->_answer['html'] = $_helper->__('Thank you for contacting us.');
                    }

                    $this->_answer['success'] = 1;
                }

            } catch (Mage_Core_Exception $e) {
                $this->_answer['message'] = $e->getMessage();
                Mage::log($e->getMessage());
            } catch (Exception $e) {
                $this->_answer['message'] = $e->getMessage();
                Mage::logException($e);
            }
        } else {
            $this->_answer['message'] = $_helper->__('An error occurred while saving the request.');
        }

        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($this->_answer));
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

    /**
     * Normalized posted parameters
     *
     * @param array $data data
     * @return array
     */
    protected function _filterPostData($data = array())
    {
        $data = $this->_filterDates($data, array('cmb_call_date'));

        return $data;
    }
}
