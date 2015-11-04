<?php
/***************************************************************************
	@extension	: Import Products categories, multiple images and custom options
	@copyright	: Copyright (c) 2014 Capacity Web Solutions.
	( http://www.capacitywebsolutions.com )
	@author		: Capacity Web Solutions Pvt. Ltd.
	@support	: magento@capacitywebsolutions.com
	
***************************************************************************/

class CapacityWebSolutions_ImportProduct_Block_Field_Keyrenderer extends  Mage_Adminhtml_Block_System_Config_Form_Field
{
  
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
	{	
		
		  $model = Mage::getModel('importproduct/profile');	
  		  $headers = array(
				'Content-Type: text/xml; charset=utf-8'
		   );

		  // Build the cURL session
		  $ch = curl_init();
		  curl_setopt($ch, CURLOPT_URL, $model->getWebServiceURL());
		  curl_setopt($ch, CURLOPT_POST, FALSE);
		  curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		  curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);


		  // Send the request and check the response
		  if (($result = curl_exec($ch)) === FALSE) {
		  } else 
		  {
			$xml = simplexml_load_string($result);			
			if($xml->cws->status==1){
			$element->setDisabled('disabled');		
			}
		}
		
		curl_close($ch);



		return parent::_getElementHtml($element);  		 
	}	
}

?>
