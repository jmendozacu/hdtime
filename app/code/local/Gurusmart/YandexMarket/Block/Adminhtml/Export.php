<?php

class Gurusmart_YandexMarket_Block_Adminhtml_Export extends Mage_Adminhtml_Block_Abstract
{
    protected function _toHtml()
    {
        $helper = Mage::helper('Gurusmart_YandexMarket');
        $actionUrl = Mage::helper('adminhtml')->getUrl('*/*/export');

        $formCode = <<<HTML
<style type="text/css">
    div.form-row {
        padding-bottom: 4px;
    }

    div.form-row div.label {
        float: left;
        width: 10%;
    }
</style>
<form id="export" action="{$actionUrl}">
    <div class="form-row">
        <div class="label">
            <label for="export-marked">{$helper->__('Export only marked')}</label>
        </div>
        <div class="input">
            <input id="export-marked" type="checkbox" name="export-marked">
        </div>
    </div>
    <div class="form-row">
        <div class="label">&nbsp;</div>
        <div class="input">
            <input type="submit" value="{$helper->__('Submit')}">
        </div>
    </div>
</form>
HTML;

        return $formCode;
    }
}