<?php
/**
 * Class Me_Cmb_Model_System_Config_Source_Sidebar
 *
 * @category  Me
 * @package   Me_Cmb
 * @author    Attila Sági <sagi.attila@aion.hu>
 * @copyright 2015 Magevolve Ltd. (http://magevolve.com)
 * @license   http://magevolve.com/terms-and-conditions Magevolve License
 * @link      http://magevolve.com
 */

/**
 * Class Me_Cmb_Model_System_Config_Source_Sidebar
 */
class Me_Cmb_Model_System_Config_Source_Sidebar
{
    /**
     * var string
     */
    const LEFT = 'left';

    /**
     * var string
     */
    const RIGHT = 'right';

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value' => self::LEFT, 'label' => Mage::helper('me_cmb')->__('Left')),
            array('value' => self::RIGHT, 'label' => Mage::helper('me_cmb')->__('Right')),
        );
    }

}
