<?php
/**
 * Class Me_Cmb_Block_Adminhtml_Form_Field_Predefiend
 *
 * @category  Me
 * @package   Me_Cmb
 * @author    Attila Sági <sagi.attila@aion.hu>
 * @copyright 2015 Magevolve Ltd. (http://magevolve.com)
 * @license   http://magevolve.com/terms-and-conditions Magevolve License
 * @link      http://magevolve.com
 */

/**
 * Class Me_Cmb_Block_Adminhtml_Form_Field_Predefiend
 */
class Me_Cmb_Block_Adminhtml_Form_Field_Predefined extends Mage_Adminhtml_Block_System_Config_Form_Field_Array_Abstract
{
    /**
     * Prepare to render
     *
     * @return void
     */
    protected function _prepareToRender()
    {
        $this->addColumn(
            'predefined',
            array(
                'label' => Mage::helper('me_cmb')->__('Predefined Interval'),
                'style' => 'width:250px',
                'class' => 'required-entry'
            )
        );

        $this->_addButtonLabel = Mage::helper('me_cmb')->__('Add More');
    }
}
