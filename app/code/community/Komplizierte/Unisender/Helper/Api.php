<?php
class Komplizierte_Unisender_Helper_Api extends Mage_Core_Helper_Abstract
{
    const UNISENDER_API_URL = 'http://api.unisender.com/ru/api/';

    public function sendSms($phone, $sender, $text) {
        $data = array();
        $data['phone'] = $this->_normalizePhone($phone);
        $data['sender'] = $sender;
        $data['text'] = $text;

        return $this->_getRest('sendSms', $data);
    }

    public function checkSms($sms_id) {
        return $this->_getRest('checkSms', array('sms_id' => $sms_id));
    }

    protected function _normalizePhone ($phone) {
        if(Mage::getSingleton('core/locale')->getLocaleCode() == 'RU')
        {
            static $filter = null;
            if (is_null($filter)) {
                $filter = new Zend_Filter_Digits();
            }

            $num = $filter->filter($phone);

            if (strlen($num) == 11) {
                return '7'.substr($num, 1);
            }
            elseif (strlen($num) == 10) {
                return '7'.$num;
            }
            return '';
        }
        return $phone ;
    }

    public function getLists()
    {
        return $this->_getRest('getLists');
    }

    public function createList($listName)
    {
        return $this->_getRest('createList', array('title' => (string) $listName));
    }

    public function subscribe($list_ids, array $fields, $tags = null, $request_ip = null, $doubleOptin = 0, $overwrite = 0) {
        $data = array();

        if(is_array($list_ids))
        {
            $data['list_ids'] = implode(',', $list_ids);
        } elseif(is_string($list_ids) || is_int($list_ids))
        {
            $data['list_ids'] = $list_ids;
        } else {
            Mage::log(Mage::helper('komplizierte_unisender')->__("list_ids not valid"));
        }

        if(is_array($tags))
        {
            $data['tags'] = implode(',', $tags);
        } elseif(is_string($tags))
        {
            $data['tags'] = $tags;
        }

        $data['fields'] = $fields;

        if(is_array($data['fields'])) {
            foreach($data['fields'] as $key => $field) {
                $data['fields['.$key.']'] = $field;
            }
            unset($data['fields']);
        }

        $data['double_optin'] = $doubleOptin;
        $data['overwrite'] = $overwrite;

        if($request_ip && Mage::helper('core/http')->validateIpAddr($request_ip)) $data['request_ip'] = $request_ip;

        return $this->_getRest('subscribe', $data);
    }

    protected function _getRest($action, array $data = array()) {
        $data['api_key'] = Mage::helper('komplizierte_unisender')->getApiKey();
        $data['format'] = 'json';

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_URL,self::UNISENDER_API_URL.$action);
        $result = curl_exec($ch);

        if ($result) {
            $response = Mage::helper('core')->jsonDecode($result);

            if(null===$response) {
                Mage::log(Mage::helper('komplizierte_unisender')->__("Invalid JSON"));
            }
            elseif(!empty($response['error'])) {
                Mage::log(Mage::helper('komplizierte_unisender')->__("An error occured: %s (code: %s)", $response['error'], $response['code']));
            }
            elseif(!empty($response['warnings'])) {
                if(is_array($response['warnings'])) {
                    foreach($response['warnings'] as $respWarning) {
                        Mage::log(Mage::helper('komplizierte_unisender')->__("Warnings: %s", $respWarning['warning']));
                    }
                } else {
                    Mage::log(Mage::helper('komplizierte_unisender')->__("Warnings: %s", $response['warnings']));
                }
            }
        } else {
            Mage::log(Mage::helper('komplizierte_unisender')->__("API access error"));
        }

        return $response;
    }
}