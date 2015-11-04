<?php
/* published by the Septsite.pl */
class Septsite_TypeChange_Model_Observer
{

	public function addMassactionToProductGrid($observer)
	{
		$block = $observer->getBlock();
		if(($block instanceof Mage_Adminhtml_Block_Catalog_Product_Grid) or ($block instanceof TBT_Enhancedgrid_Block_Catalog_Product_Grid)){
			
				
			$sets[] = array( 'value' => 1, 'label' => Mage::helper('typechange')->__('Configurable to Grouped') );
			$sets[] = array( 'value' => 2, 'label' => Mage::helper('typechange')->__('Simple to Configurable') );
			$sets[] = array( 'value' => 3, 'label' => Mage::helper('typechange')->__('Simple to Grouped') );
		
			$block->getMassactionBlock()->addItem('septsite_typechange', array(
				'label'=> Mage::helper('typechange')->__('Change type product'),
				'url'  => $block->getUrl('*/*/typechange', array('_current'=>true)),
				'additional' => array(
					'visibility' => array(
						'name' => 'attribute_set',
						'type' => 'select',
						'class' => 'required-entry',
						'label' => Mage::helper('typechange')->__('Change'),
						'values' => $sets
					)
				)
			)); 			
		}
	}
	
}
