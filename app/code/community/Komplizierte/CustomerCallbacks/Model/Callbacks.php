<?php
class Komplizierte_CustomerCallbacks_Model_Callbacks extends Mage_Core_Model_Abstract
{
    protected function _construct(){
       $this->_init("komplizierte_customercallbacks/callbacks");
    }

    public function getStatuses() {
        return array(
            'new'       => 'New',
            'processed' => 'Processed',
            'canceled'  => 'Canceled',
        );
    }

    public function getStatusesGridForm() {
        $statuses = $this->getStatuses();
        $statusValues = array();
        foreach($statuses as $key=>$status) {
            $statusValues[] = array('value'=>$key,'label'=>$status);
        }
        return $statusValues;
    }
}
	 