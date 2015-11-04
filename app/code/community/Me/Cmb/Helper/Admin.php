<?php
/**
 * Class Me_Cmb_Helper_Admin
 *
 * @category  Me
 * @package   Me_Cmb
 * @author    Attila Sági <sagi.attila@aion.hu>
 * @copyright 2015 Magevolve Ltd. (http://magevolve.com)
 * @license   http://magevolve.com/terms-and-conditions Magevolve License
 * @link      http://magevolve.com
 */

/**
 * Class Me_Cmb_Helper_Admin
 */
class Me_Cmb_Helper_Admin extends Mage_Core_Helper_Abstract
{
    /**
     * Check permission for passed action
     *
     * @param string $action action
     * @return bool
     */
    public function isActionAllowed($action)
    {
        return Mage::getSingleton('admin/session')->isAllowed('customer/cmb/' . $action);
    }
}
