<?php
/**
 * Class Me_Cmb_Model_Resource_Cmb_Collection
 *
 * @category  Me
 * @package   Me_Cmb
 * @author    Attila Sági <sagi.attila@aion.hu>
 * @copyright 2015 Magevolve Ltd. (http://magevolve.com)
 * @license   http://magevolve.com/terms-and-conditions Magevolve License
 * @link      http://magevolve.com
 */

/**
 * Class Me_Cmb_Model_Resource_Cmb_Collection
 */
class Me_Cmb_Model_Resource_Cmb_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Define collection model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('me_cmb/cmb');
    }
}
