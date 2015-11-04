<?php
/**
 * Amasty_ShippingDate extension
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 * 
 * @category   	Amasty
 * @package		Amasty_ShippingDate
 * @copyright  	Copyright (c) 2014
 * @license		http://opensource.org/licenses/mit-license.php MIT License
 */
/**
 * Weekend edit form tab
 *
 * @category	Amasty
 * @package		Amasty_ShippingDate
 * @author Ultimate Module Creator
 */
class Amasty_ShippingDate_Block_Adminhtml_Weekend_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form{	
	/**
	 * prepare the form
	 * @access protected
	 * @return ShippingDate_Weekend_Block_Adminhtml_Weekend_Edit_Tab_Form
	 * @author Ultimate Module Creator
	 */
	protected function _prepareForm(){
		$form = new Varien_Data_Form();
		$form->setHtmlIdPrefix('weekend_');
		$form->setFieldNameSuffix('weekend');
		$this->setForm($form);
		$fieldset = $form->addFieldset('weekend_form', array('legend'=>Mage::helper('shippingdate')->__('Weekend')));

		$fieldset->addField('title', 'text', array(
			'label' => Mage::helper('shippingdate')->__('Title'),
			'name'  => 'title',
			'required'  => true,
			'class' => 'required-entry',

		));
        $dateFormatIso = 'yyyy-MM-dd HH:mm:ss';

		$fieldset->addField('date_from', 'date', array(
			'label' => Mage::helper('shippingdate')->__('Date From'),
			'name'  => 'date_from',
            'required'  => true,
            'class' => 'required-entry',
            'image'	 => $this->getSkinUrl('images/grid-cal.gif'),
            'format'	=> $dateFormatIso,
            'time' => true
		));
		$dateFormatIso = 'yyyy-MM-dd HH:mm:ss';

		$fieldset->addField('date_to', 'date', array(
			'label' => Mage::helper('shippingdate')->__('Date To'),
			'name'  => 'date_to',
			'required'  => true,
			'class' => 'required-entry',
		    'image'	 => $this->getSkinUrl('images/grid-cal.gif'),
		    'format'	=> $dateFormatIso,
            'time' => true
		));

		$fieldset->addField('comment', 'text', array(
			'label' => Mage::helper('shippingdate')->__('Comment'),
			'name'  => 'comment',

		));
		$fieldset->addField('status', 'select', array(
			'label' => Mage::helper('shippingdate')->__('Status'),
			'name'  => 'status',
			'values'=> array(
				array(
					'value' => 1,
					'label' => Mage::helper('shippingdate')->__('Enabled'),
				),
				array(
					'value' => 0,
					'label' => Mage::helper('shippingdate')->__('Disabled'),
				),
			),
		));
		if (Mage::app()->isSingleStoreMode()){
			$fieldset->addField('store_id', 'hidden', array(
                'name'      => 'stores[]',
                'value'     => Mage::app()->getStore(true)->getId()
            ));
            Mage::registry('current_weekend')->setStoreId(Mage::app()->getStore(true)->getId());
		}
		if (Mage::getSingleton('adminhtml/session')->getWeekendData()){
			$form->setValues(Mage::getSingleton('adminhtml/session')->getWeekendData());
			Mage::getSingleton('adminhtml/session')->setWeekendData(null);
		}
		elseif (Mage::registry('current_weekend')){
			$form->setValues(Mage::registry('current_weekend')->getData());
		}
		return parent::_prepareForm();
	}
}