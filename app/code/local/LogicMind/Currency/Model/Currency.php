<?php
/**
 * NOTICE OF LICENSE
 *
 * You may not sell, sub-license, rent or lease
 * any portion of the Software or Documentation to anyone.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade to newer
 * versions in the future.
 *
 * @category   ET
 * @package    ET_CurrencyManager
 * @copyright  Copyright (c) 2012 ET Web Solutions (http://etwebsolutions.com)
 * @contacts   support@etwebsolutions.com
 * @license    http://shop.etwebsolutions.com/etws-license-free-v1/   ETWS Free License (EFL1)
 */

class LogicMind_Currency_Model_Currency extends Mage_Directory_Model_Currency
{
  public function format($price, $options = array(), $includeContainer = true, $addBrackets = false)
  {
    $helper = Mage::helper('currencymanager');
    if (method_exists($this, "formatPrecision")) {
        $options = $helper->getOptions($options);

        return $this->formatPrecision(
            $price,
            isset($options["precision"]) ? $options["precision"] : 2,
            $helper->clearOptions($options),
            $includeContainer,
            $addBrackets
        );
    }
    return parent::format($price, $options, $includeContainer, $addBrackets);
  }

  public function formatTxt($price, $options = array())
  {
      /* @var $helper ET_CurrencyManager_Helper_Data */
      $helper = Mage::helper('currencymanager');
      $options['format'] = '#,##0.00 <i class="fa fa-rub"></i>'; // TODO: add ability to change format
      $answer = parent::formatTxt($price, $helper->clearOptions($options));

      if ($helper->isEnabled()) {
          $moduleName = Mage::app()->getRequest()->getModuleName();

          $optionsAdvanced = $helper->getOptions($options, false, $this->getCurrencyCode());
          $options = $helper->getOptions($options, true, $this->getCurrencyCode());
          if (isset($options["precision"])) {
              $price = round($price, $options["precision"]);
          }
          $price = number_format( $price, $options["precision"], ',', ' ' );

          $answer = $price.'<i class="fa fa-rub"></i>'; //parent::formatTxt($price, $options);
/*
          if (count($options) > 0) {
              if (($moduleName == 'admin')) {
                  $answer = parent::formatTxt($price, $helper->clearOptions($options));
              }
              $minDecimalCount = $optionsAdvanced['min_decimal_count'];
              $finalDecimalCount = $this->getPrecisionToCutZeroDecimals($price, $minDecimalCount);
              if ($finalDecimalCount <= $options['precision']) {
                  $options['precision'] = $finalDecimalCount;
              }

              //check against -0
              $answer = $this->_formatWithPrecision($options, $optionsAdvanced, $price, $answer);
              if (!($helper->isInOrder() && $optionsAdvanced['excludecheckout'])) {
                  if ($price == 0) {
                      if (isset($optionsAdvanced['zerotext']) && $optionsAdvanced['zerotext'] != "") {
                          return $optionsAdvanced['zerotext'];
                      }
                  }

                  $answer = $this->_cutZeroDecimal($options, $optionsAdvanced, $price, $answer);
              }
          }*/
      }
      return $answer;
  }

  protected function _formatWithPrecision($options, $optionsAdvanced, &$price, $answer)
  {
      $helper = Mage::helper('currencymanager');
      if (isset($optionsAdvanced['precision'])) {
          $price = round($price, $optionsAdvanced['precision']);
          if ($optionsAdvanced['precision'] < 0) {
              $options['precision'] = 0;
          }

          //for correction -0 float zero
          if ($price == 0) {
              $price = 0;
          }
          //if no need to cut zero we must recreate default answer
          return parent::formatTxt($price, $helper->clearOptions($options));
      }
      return $answer;
  }
}
