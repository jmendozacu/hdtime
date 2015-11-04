<?php

class Gurusmart_YandexMarket_Adminhtml_IndexController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        $this->loadLayout();

        $formExportBlock = $this->getLayout()
            ->createBlock('Gurusmart_YandexMarket/adminhtml_export');
        $this->_addContent($formExportBlock);

        $this->renderLayout();
    }


    public function exportAction()
    {
        // Check request
        $exportOnlyMarked = $this->getRequest()->getParam('export-marked', null) === 'on';

        // Result array
        $result = array(
            'shop_data' => array(
                'name' => Mage::getModel('core/store')->load(1)->getName(),
                //'url' => Mage::app()->getStore()->getUrl(),
                'url' => Mage::getBaseUrl( Mage_Core_Model_Store::URL_TYPE_WEB, true ),
            )
        );

        // Attributes to select (for products collection)
        $attributesToSelect = array(
            'sku',
            'price',
            'name',
            'manufacturer',
            'url_key',
            'short_description',
            'image',
            'country_of_manufacture'
        );

        // Get current currency code
        $currencyCode = Mage::app()->getStore()->getCurrentCurrencyCode();

        // Get categories collection
        $categories = Mage::getModel('catalog/category')
            ->getCollection()
            ->setOrder('entity_id', 'DESC')
            ->addAttributeToSelect('name');

        // Array of product ids to control uniq
        $productIds = array();

        // For each category do
        foreach ($categories as $_category) {

            // Put category data into result array
            $result['categories'][] = array(
                'id' => $_category->getId(),
                'parent_id' => $_category->getParentId(),
                'name' => $_category->getName()
            );

            // Get products collection and add attributes to select
            $products = $_category->getProductCollection();
            foreach ($attributesToSelect as $ats) {
                $products->addAttributeToSelect($ats);
            }
            if ($exportOnlyMarked) {
                $products->addAttributeToFilter('xml_export', array('Yes' => true));
            }

            // For each product do
            foreach ($products as $_product) {

                // Check uniq
                if (in_array($_product->getId(), $productIds)) {
                    continue;
                } else {
                    $productIds[] = $_product->getId();
                }

                // Try to get picture path
                $image = $_product->getImage();
                if ($image === 'no_selection') {
                    $picUrl = null;
                } else {
                    //$picUrl = (string)Mage::helper('catalog/image')->init($_product, 'image')->resize(210);
                    $picUrl = (string)Mage::getModel('catalog/product_media_config')->getMediaUrl($_product->getImage());
                }

                // product suffix
                $helper = Mage::helper('catalog/product');
                $suffix = $helper->getProductUrlSuffix(); // may have particular store ID

                // Save product data into result array
                $result['products'][] = array(
                    'id' => $_product->getId(),
                    'in_stock' => (bool)Mage::getModel('cataloginventory/stock_item')->loadByProduct($_product)->getIsInStock(),
                    'url' => str_replace('/index.php', null, $result['shop_data']['url']) . $_product->getUrlKey() . $suffix,
                    'price' => $_product->getPrice(),
                    'currencyId' => $currencyCode,
                    'categoryId' => $_category->getId(),
                    'picture' => $picUrl,
                    'name' => $_product->getName(),
                    'vendor' => trim($_product->getAttributeText('manufacturer')),
                    'vendorCode' => $_product->getSku(),
                    'description' => trim(strip_tags($_product->getShortDescription())),
                    'country_of_origin' => trim($_product->getAttributeText('country_of_manufacture')),
                );

            }

        }

        // Process data on remote server
        if (($response = $this->_processOnRemoteServer($result))) {

            // Success
            $this->_getSession()->addSuccess($this->__('Data successfully exported'));

            // Write response into file
            $path = Mage::getBaseDir('base') . DS . 'products.xml';
            if (!file_put_contents($path, $response)) {

                $this->_getSession()->addError(
                    $this->__('Can`t write to products.xml')
                );

            }

        } else {

            // Error occured
            $this->_getSession()->addError($this->__('Can`t process data, please upgrade extension to the latest version'));

        }

        $this->_redirect('*/*');
    }

    private function _processOnRemoteServer(array $result)
    {
        $context = stream_context_create(
            array(
                'http' => array(
                    'method' => 'POST',
                    'header' => 'Content-Type: application/x-www-form-urlencoded' . PHP_EOL,
                    'content' => http_build_query(
                        array(
                            'data' => base64_encode(gzencode(serialize($result), $level = 6))
                        )
                    )
                )
            )
        );

        return file_get_contents(
            $file = 'http://www.gurusmart.ru/converter/convert.php',
            $use_include_path = false,
            $context
        );
    }
}
