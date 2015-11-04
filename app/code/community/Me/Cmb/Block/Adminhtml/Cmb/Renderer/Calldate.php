<?php
/**
 * Class Me_Cmb_Block_Adminhtml_Cmb_Renderer_Calldate
 *
 * @category  Me
 * @package   Me_Cmb
 * @author    Attila Sági <sagi.attila@aion.hu>
 * @copyright 2015 Magevolve Ltd. (http://magevolve.com)
 * @license   http://magevolve.com/terms-and-conditions Magevolve License
 * @link      http://magevolve.com
 */

/**
 * Class Me_Cmb_Block_Adminhtml_Cmb_Renderer_Calldate
 */
class Me_Cmb_Block_Adminhtml_Cmb_Renderer_Calldate extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Date
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
            return Mage::helper('core')->formatDate($value, Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM);
        }
    }

}
