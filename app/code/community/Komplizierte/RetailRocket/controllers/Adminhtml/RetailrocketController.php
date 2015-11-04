<?php
/**
 * Created by JetBrains PhpStorm.
 * User: user
 * Date: 13.09.13
 * Time: 16:47
 * To change this template use File | Settings | File Templates.
 */
class Komplizierte_RetailRocket_Adminhtml_RetailrocketController extends Mage_Adminhtml_Controller_Action
{

    public function indexAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    public function ymlGenerateAction() {

        Mage::dispatchEvent('make_yml_generate', array());

    }
}