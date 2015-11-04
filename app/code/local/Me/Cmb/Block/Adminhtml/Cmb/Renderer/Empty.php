<?php
/**
 * Class Me_Cmb_Block_Adminhtml_Cmb_Renderer_Empty
 *
 * @category  Me
 * @package   Me_Cmb
 * @author    Attila Sági <sagi.attila@aion.hu>
 * @copyright 2015 Magevolve Ltd. (http://magevolve.com)
 * @license   http://magevolve.com/terms-and-conditions Magevolve License
 * @link      http://magevolve.com
 */

/**
 * Class Me_Cmb_Block_Adminhtml_Cmb_Renderer_Empty
 */
class Me_Cmb_Block_Adminhtml_Cmb_Renderer_Empty extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * Renders grid column
     *
     * @param Varien_Object $row row
     * @return string
     */
    public function render(Varien_Object $row)
    {
        $value = $row->getData($this->getColumn()->getIndex());
        if (!$value) {
            return '-';
        } else {
            return $value;
        }
    }

}
