<?php
/**
 * Studioforty9_Recaptcha
 *
 * @category  Studioforty9
 * @package   Studioforty9_Recaptcha
 * @author    StudioForty9 <info@studioforty9.com>
 * @copyright 2015 StudioForty9 (http://www.studioforty9.com)
 * @license   https://github.com/studioforty9/recaptcha/blob/master/LICENCE BSD
 * @version   1.2.0
 * @link      https://github.com/studioforty9/recaptcha
 */

/**
 * Studioforty9_Recaptcha_Test_Block_Explicit
 *
 * @category   Studioforty9
 * @package    Studioforty9_Recaptcha
 * @subpackage Test
 * @author     StudioForty9 <info@studioforty9.com>
 */
class Studioforty9_Recaptcha_Test_Block_Explicit extends EcomDev_PHPUnit_Test_Case
{
    /** @var Studioforty9_Recaptcha_Block_Explicit */
    protected $block;

    public function setUp()
    {
        $this->block = new Studioforty9_Recaptcha_Block_Explicit();

        parent::setUp();
    }

    protected function getMockDataHelper($enabled, $theme = 'light', $siteKey = '123456789', $secretKey = '987654321')
    {
        $helper = $this->getHelperMock('studioforty9_recaptcha', array(
            'isEnabled', 'getSiteKey', 'getSecretKey', 'getTheme'
        ), false, array(), null, false);

        $helper->expects($this->any())
            ->method('isEnabled')
            ->will($this->returnValue($enabled));


        $helper->expects($this->any())
            ->method('getSiteKey')
            ->will($this->returnValue($siteKey));

        $helper->expects($this->any())
            ->method('getSecretKey')
            ->will($this->returnValue($secretKey));


        $helper->expects($this->any())
            ->method('getTheme')
            ->will($this->returnValue($theme));

        return $helper;
    }

    public function test_getRecaptchaScript_returns_empty_string_when_module_disabled()
    {
        $dataHelper = $this->getMockDataHelper(false);
        $this->replaceByMock('helper', 'studioforty9_recaptcha', $dataHelper);

        $expected = '';
        $actual = $this->block->getRecaptchaScript();
        $this->assertEquals($expected, $actual);
    }

    public function test_getRecaptchaScript_returns_script_tag_html_when_module_enabled()
    {
        $dataHelper = $this->getMockDataHelper(true);
        $locale = 'de_DE';
        $lang = 'de';

        Mage::app()->getLocale()->setLocaleCode($locale);
        Mage::getSingleton('core/translate')->setLocale($locale)->init('frontend', true);
        $this->replaceByMock('helper', 'studioforty9_recaptcha', $dataHelper);

        $expected = '<script src="https://www.google.com/recaptcha/api.js?onload=onloadCallback&render=explicit&hl='.$lang.'" async defer></script>';
        $actual = $this->block->getRecaptchaScript();
        $this->assertEquals($expected, $actual);

        Mage::app()->getLocale()->setLocaleCode('en_US');
        Mage::getSingleton('core/translate')->setLocale('en_US')->init('frontend', true);
    }
}
