<?php
/**
 * Created by PhpStorm.
 * User: test
 * Date: 24.04.14
 * Time: 10:35
 */
class Komplizierte_RetailRocket_Helper_Data extends Mage_Core_Helper_Abstract {

    protected function renderItem($data) {
        $result = '';

        if(is_array($data) && count($data) > 0) {
            foreach($data as $tagName => $block) {
                $result .= "<".$tagName;
                if(isset($block['attributes']) && is_array($block['attributes']) && count($block['attributes']) > 0) {
                    foreach($block['attributes'] as $key => $val) {
                        $result .= ' ' . $key . '="' . $val . '"';
                    }
                }
                $result .= ">\r\n";
                if(isset($block['data']) && is_array($block['data']) && count($block['data']) > 0) {
                    foreach($block['data'] as $key => $val) {
                        if(is_array($val) && count($val) > 0) {
                            foreach($val as $param) {
                                $result .= "\t<".$key;
                                if(isset($param['attributes']) && is_array($param['attributes']) && count($param['attributes']) > 0) {
                                    foreach($param['attributes'] as $key2 => $val2) {
                                        $result .= ' ' . $key2 . '="' . $val2 . '"';
                                    }
                                }
                                $result .= ">";
                                $result .= $param['value'];
                                $result .= "</".$key.">\r\n";
                            }
                        } else {
                            $result .= "\t<".$key.">".$val."</".$key.">\r\n";
                        }
                    }

                }
                $result .= "</".$tagName.">\r\n";
            }
        }
        return $result;
    }

    public function renderYml($debug = false, $outputFILE = 'ymlwikimart.xml', $paramsSite = array()) {

        $start = microtime(true);

        if($debug)
            Mage::log('Start generating Xml.');

        $productsCollection = Mage::getResourceModel('catalog/product_collection')
            ->addAttributeToSelect('*')
            ->addAttributeToFilter('type_id','configurable')
            ->addFieldToFilter('status', Mage_Catalog_Model_Product_Status::STATUS_ENABLED);

        if($debug)
            Mage::log('Has been loaded ~' . count($productsCollection) . '~ items');

        if($debug)
            Mage::log('Starting rendered Xml.');

        $items = '';
        $n = 0;
        $idd=1;
        $cats = array();
        foreach($productsCollection as $product) {


            if($debug) {
                echo $n . "\r\n";
                Mage::log($n . ") ID: ".$product->getId() . " - time: " . sprintf('%.2F sec.', microtime(true) - $start));
            }


            $desc = ($product->getDescription()) ? $product->getDescription() : $product->getName();

            $baseUrl = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB);
            $currentCatIds = array();

            $categoryes = $product->getCategoryCollection();
            foreach($categoryes as $cat) {

                $catName = Mage::getModel('catalog/category')->load($cat->getId())->getName();
                $currentCatIds[] = array(
                    "id" => $cat->getId(),
                    "name" => $catName,
                );

            }

            $cats[$currentCatIds[0]['id']] = $currentCatIds[0]['name'];

            $colors = array();
            $sizes = array();
            $_attributes = $product->getTypeInstance(true)->getConfigurableAttributes($product);
            foreach($_attributes as $_attribute){
                if($_attribute->getLabel() == "Цвет") {
                    $colors = $_attribute->getData('prices'); //cvet
                }
                if($_attribute->getLabel() == "Размер") {
                    $sizes = $_attribute->getData('prices'); //size
                }

            }

            if(count($colors) == 0) {
                $colors[]['store_label'] = "Default";
            }
            if(count($sizes) == 0) {
                $sizes[]['store_label'] = "Default";
            }


            foreach($colors as $color) {
                foreach($sizes as $size) {

                    $data = array(
                        "offer" => array(
                            "attributes" => array(
                                "id" => $idd,
                                "group_id" => $product->getId(),
                                "available" => "true",
                                "type" => "vendor.model",
                            ),
                            "data" => array(
                                "url" => $baseUrl.$product->getUrlPath(),
                                "price" => (int)$product->getFinalPrice(),
                                "oldprice" => (int)$product->getPrice(),
                                "currencyId" => "RUB",
                                "description" => strip_tags($desc),
                                "categoryId" => $currentCatIds[0]['id'],
                                "market_category" => $currentCatIds[0]['name'],
                                "picture" => $baseUrl.'media/catalog/product' . $product->getImage(),
                                "name" => $product->getName(),
                                "model" => $product->getName(),
                                "vendor" => htmlspecialchars($product->getAttributeText('manufacturer')),
                                "vendorCode" => $product->getSku(),
                                "param" => array(
                                    array(
                                        "attributes" => array(
                                            "name" => "Пол",
                                        ),
                                        "value" => "unisex",//$product->getAttributeText('sex'),
                                    ),
                                    array(
                                        "attributes" => array(
                                            "name" => "Материал",
                                        ),
                                        "value" => $product->getMaterials(),
                                    ),
                                    array(
                                        "attributes" => array(
                                            "name" => "Цвет",
                                            "type" => "colour",
                                        ),
                                        "value" => $color['store_label'],
                                    ),
                                    array(
                                        "attributes" => array(
                                            "name" => "Размер",
                                            "type" => "size",
                                            "unit" => "RU",
                                        ),
                                        "value" => $size['store_label'],
                                    ),
                                ),
                            )
                        )
                    );

                    $items .= $this->renderItem($data);

                    $idd++;
                }
            }


            $n++;
        }

        $category = '';
        foreach($cats as $id => $catName) {
            $category .= '<category id="'.$id.'">'.$catName.'</category>';
        }

        $body = '<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE xml_catalog SYSTEM "shops.dtd">
<yml_catalog date="'.date('Y-m-d H:i').'">
    <shop>
        <name>'.@$paramsSite['name'].'</name>
        <company>'.@$paramsSite['company'].'</company>
        <url>'.@$paramsSite['url'].'</url>
        <platform>'.@$paramsSite['platform'].'</platform>
        <version>'.@$paramsSite['version'].'</version>
        <agency>'.@$paramsSite['agency'].'</agency>
        <currencies>
            <currency id="RUB" rate="1"/>
        </currencies>
        <categories>
            '.$category.'
        </categories>
        <local_delivery_cost>250</local_delivery_cost>
        <offers>
            '.$items.'
        </offers>
    </shop>
</yml_catalog>';


        if($debug)
            Mage::log('Finish rendered XML.');

        file_put_contents($outputFILE, $body);

        if($debug)
            Mage::log("Saved >>> " . $outputFILE);

        return $body;


    }
}