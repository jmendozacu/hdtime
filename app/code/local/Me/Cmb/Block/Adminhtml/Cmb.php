<?php
/**
 * Class Me_Cmb_Block_Adminhtml_Cmb
 *
 * @category  Me
 * @package   Me_Cmb
 * @author    Attila Sági <sagi.attila@aion.hu>
 * @copyright 2015 Magevolve Ltd. (http://magevolve.com)
 * @license   http://magevolve.com/terms-and-conditions Magevolve License
 * @link      http://magevolve.com
 */

/**
 * Class Me_Cmb_Block_Adminhtml_Cmb
 */
class Me_Cmb_Block_Adminhtml_Cmb extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    /**
     * Block constructor
     */
    public function __construct()
    {
        $this->_blockGroup = 'me_cmb';
        $this->_controller = 'adminhtml_cmb';
        $this->_headerText = Mage::helper('me_cmb')->__('Заказ в один клик');

        parent::__construct();
        $this->_removeButton('add');
    }
}
