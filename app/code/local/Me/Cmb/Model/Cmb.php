<?php
/**
 * Class Me_Cmb_Model_Cmb
 *
 * @category  Me
 * @package   Me_Cmb
 * @author    Attila Sági <sagi.attila@aion.hu>
 * @copyright 2015 Magevolve Ltd. (http://magevolve.com)
 * @license   http://magevolve.com/terms-and-conditions Magevolve License
 * @link      http://magevolve.com
 */

/**
 * Class Me_Cmb_Model_Cmb
 *
 * @method string getStatus()
 * @method setStatus()
 * @method setStoreId()
 * @method string getCmbFullName()
 * @method string getCmbTelephone()
 * @method string getCmbPredefined()
 */
class Me_Cmb_Model_Cmb extends Mage_Core_Model_Abstract
{
    /**
     * var int
     */
    const STATUS_NEW = 1;

    /**
     * var int
     */
    const STATUS_PENDING = 2;

    /**
     * var int
     */
    const STATUS_DONE = 3;

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('me_cmb/cmb');
    }

    /**
     * If object is new adds post date
     *
     * @return Me_Cmb_Model_Cmb
     */
    protected function _beforeSave()
    {
        parent::_beforeSave();
        if ($this->isObjectNew()) {
            $this->setData('posted_at', Varien_Date::now());
        }
        return $this;
    }

    /**
     * Get statuses as option array
     *
     * @return array
     */
    public function getStatuses()
    {
        $_helper = Mage::helper('me_cmb');

        return array(
            array('value' => self::STATUS_NEW, 'label' => $_helper->__('New')),
            array('value' => self::STATUS_PENDING, 'label' => $_helper->__('Pending')),
            array('value' => self::STATUS_DONE, 'label' => $_helper->__('Done')),
        );
    }

    /**
     * Get status options
     *
     * @return array
     */
    public function getStatusOptions()
    {
        $_helper = Mage::helper('me_cmb');

        return array(
            self::STATUS_NEW => $_helper->__('New'),
            self::STATUS_PENDING => $_helper->__('Pending'),
            self::STATUS_DONE => $_helper->__('Done'),
        );
    }
}
