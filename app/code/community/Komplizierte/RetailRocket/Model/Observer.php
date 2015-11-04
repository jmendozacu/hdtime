<?php

class Komplizierte_RetailRocket_Model_Observer extends Varien_Event_Observer
{

    public function startGenerateYml ()
    {


        $xmlParams = array(
            "name" => "HDTIME",
            "company" => "ООО &quot;HDTIME&quot;",
            "url" => "http://hdtime.kmplzt.de",
            "platform" => "Magento",
            "version" => "1.8.0.2",
            "agency" => "Komplizierte",
        );

        Mage::helper('komplizierte_retailrocket')->renderYml(
            Mage::getStoreConfig('komplizierte_retailrocket/main/debug'),
            Mage::getBaseDir('base') . "/retailroketYml.xml",
            $xmlParams
        );


        return;
    }

    public function insertCode($observer) {


        $block = $observer->getEvent()->getData( 'block' );


        if(($block->getRequest()->getControllerName() == 'product')
            && $block->getType()=='catalog/product_view'
            && $block->getTemplate() == 'catalog/product/view/addtocart.phtml'
        )
        {

            $_product = $block->getProduct();
            $js = 'onmousedown="try { rrApi.addToBasket('.$_product->getId().') } catch(e) {}"';

            $transport = $observer->getTransport();
            $html = $transport->getHtml();


            $html = preg_replace('/onclick=\"prod/si', $js . " onclick=\"prod", $html);

            $transport->setHtml($html);
        }

    }


}
