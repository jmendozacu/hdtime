<?php
class Raveinfosys_Showresponse_Block_Adminhtml_Showresponse_Description extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Text
{
    public function render(Varien_Object $row)
    {
        $html = parent::render($row);
		if(strlen($html)>250)
		return substr($html,0,250).'...';
		else
		return $html;
    }
}
?>