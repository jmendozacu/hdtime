<?php

class Raveinfosys_Showmap_Model_Showmap extends Mage_Core_Model_Abstract
{
    public $google_url = "http://www.google.com/webmasters/tools/ping?sitemap=";
    public $yahoo_url =  "http://www.bing.com/webmaster/ping.aspx?siteMap=";
    public $bing_url =   "http://www.bing.com/webmaster/ping.aspx?siteMap=";
    public $window_url = "http://www.bing.com/webmaster/ping.aspx?siteMap=";
	
    public function _construct()
    {
        parent::_construct();  
        $this->_init('showmap/showmap');
	}
	
	public function getRow()
	{
	  $collection = $this->getCollection()
					->setOrder('showmap_id', 'DESC');
	  $row = $collection->getFirstItem()->getData();			
	  return $row;
	}
	
    public function update()
	{
	  $arr_row = $this->getRow();	
	  $this->submitSitemap($arr_row);
	}
	
	public function submitSitemap($data)
	{
	    $responses = array();
	    $responses['google_response'] = 'Not Configured';
	    $responses['yahoo_response'] = 'Not Configured';
	    $responses['bing_response'] = 'Not Configured';
	    $responses['window_response'] = 'Not Configured';
		
		$base_url = $this->get_url()."sitemap.xml";
		$isCurl = function_exists(curl_init);
		if($isCurl)
		{
			if($data['google']=='yes')
			{
			   $url_google = $this->google_url.$base_url;
			   $string = $this->get_web_page($url_google);
			   
			   $DOM = new DOMDocument;
			   $DOM->loadHTML($string);

			   $items = $DOM->getElementsByTagName('body');
			   $array = array();
			   for ($i = 0; $i < $items->length; $i++)
			   $array = $items->item($i)->nodeValue ;
			   $responses['google_response'] = substr($array,'0',105);
			}
 
			if($data['yahoo']=='yes')
			{
			   $url_yahoo = $this->yahoo_url.$base_url;
			   $responses['yahoo_response'] = strip_tags($this->get_web_page($url_yahoo));
			}

			if($data['bing']=='yes')
			{
			   $url_bing = $this->bing_url.$base_url;
			   $responses['bing_response'] = strip_tags($this->get_web_page($url_bing));
			}
			
			if($data['window']=='yes')
			{
			   $url_window = $this->window_url.$base_url;
			   $responses['window_response'] = strip_tags($this->get_web_page($url_window));
			}
		}
			 
		if($data['google']=='yes' || $data['bing']=='yes' || $data['window']=='yes' || $data['yahoo']=='yes')
		{
		   $responses['date'] = date('Y-m-d h:i:s a', Mage::getModel('core/date')->timestamp(time()));
		   $showresponse = Mage::getModel('showresponse/showresponse')->setData($responses);
		   $showresponse->save();
		   Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('showmap')->__('sitemap was successfully submitted'));
		}
		else
	    Mage::getSingleton('adminhtml/session')->addError(Mage::helper('showmap')->__('Please choose at least 1 channel'));
  	}
	
	function get_web_page( $url )
	{
		$options = array(
			CURLOPT_RETURNTRANSFER => true,     // return web page
			CURLOPT_HEADER         => false,    // don't return headers
			CURLOPT_FOLLOWLOCATION => true,     // follow redirects
			CURLOPT_ENCODING       => "",       // handle all encodings
			CURLOPT_AUTOREFERER    => true,     // set referer on redirect
			CURLOPT_CONNECTTIMEOUT => 120,      // timeout on connect
			CURLOPT_TIMEOUT        => 120,      // timeout on response
			CURLOPT_MAXREDIRS      => 10,       // stop after 10 redirects
		);

		$ch      = curl_init( $url );
		curl_setopt_array( $ch, $options );
		$content = curl_exec( $ch );
		$err     = curl_errno( $ch );
		$errmsg  = curl_error( $ch );
		$header  = curl_getinfo( $ch );
		curl_close( $ch );
		$header['errno']   = $err;
		$header['errmsg']  = $errmsg;
		$header['content'] = $content;
		return $content;
	}
 
	public function get_url()
 	{
      	$string =  Mage::getBaseURL();
	    $string1 = str_replace("index.php/","", $string);
	 	$string2 = str_replace('/','%2F',$string1);
	    $string3 = str_replace(':','%3A',$string2);
	 	return $string3;
	  
 	}
	
	public function generateSitemapXML()
	{
	    $config = Mage::getModel('showmap/config');
		$row = $config->getRow();
		if($row['configured'] == 0)return false;
		$content = file_get_contents('sitemap/sitemap.xml');
		$content = str_replace('</urlset>','',$content);
		$content .= '<?xml version="1.0" encoding="utf-8"?><urlset>';
		$count = 0;
		if($row['category'] == 'yes')
		{
		 $cats = Mage::getModel('catalog/category')->load(2)->getChildren();
		 $catIds = explode(',',$cats);
		 foreach($catIds as $catId)
		 {
		  if($catId !='')
		  {
		   $category = Mage::getModel('catalog/category')->load($catId);
		   $subCats = Mage::getModel('catalog/category')->load($category->getId())->getChildren();
		   $subCatIds = explode(',',$subCats);
		   $url=$category->getUrl();
		   if(strpos($content,$url) === false)
		   {
			$content .= '<url><loc>'.$url.'</loc>';
			$content .= '<lastmod>'.date('Y-m-d').'</lastmod>';
			$content .= '<changefreq>daily</changefreq>';
			$content .= '<priority>0.50</priority>';
			$content .= '</url>';
			$count++;
		   }
		   if(count($subCatIds) > 1)
		   {
			 foreach($subCatIds as $subCat)
			 {
			   $subCategory = Mage::getModel('catalog/category')->load($subCat);
			   $url=$subCategory->getUrl();
			   if(strpos($content,$url) === false)
			   {
				$content .= '<url><loc>'.$url.'</loc>';
				$content .= '<lastmod>'.date('Y-m-d').'</lastmod>';
				$content .= '<changefreq>daily</changefreq>';
				$content .= '<priority>0.50</priority>';
				$content .= '</url>';
				$count++;
			   }
			 }
		   } 
		  } 
		 }
		}
		
		if($row['product'] == 'yes')
		{
		 $product    = Mage::getModel('catalog/product');
		 $products   = $product->getCollection()->addStoreFilter($storeId)->getData();
		 foreach ($products as $pro) 
		 {   
		   $Stock = Mage::getModel('cataloginventory/stock_item')->loadByProduct($pro['entity_id'])->getIsinStock();
		   if($Stock)
		   {
			$_product = $product->load($pro['entity_id']);
			$url=Mage::getUrl().$_product->getUrlPath();
			if(strpos($content,$url) === false && $_product->getStatus()!=2)
			{
			 $content .= '<url><loc>'.$url.'</loc>';
			 $content .= '<lastmod>'.date('Y-m-d').'</lastmod>';
			 $content .= '<changefreq>daily</changefreq>';
			 $content .= '<priority>0.50</priority>';
			 $content .= '</url>';
			 $count++;
			}
		  }
		 }
		}
		
		if($row['cms'] == 'yes')
		{
		 $collection = Mage::getModel('cms/page')->getCollection()->addStoreFilter(Mage::app()->getStore()->getId());
		 $collection->getSelect()
		   ->where('is_active = 1');
		 foreach ($collection as $page)
		 {
		  $PageData = $page->getData();
		  if($PageData['identifier']!='no-route' && $PageData['identifier']!='enable-cookies') 
		   {
			$url = Mage::getUrl(). $PageData['identifier'];
			if(strpos($content,$url) === false)
			{
			 $content .= '<url><loc>'.$url.'</loc>';
			 $content .= '<lastmod>'.date('Y-m-d').'</lastmod>';
			 $content .= '<changefreq>daily</changefreq>';
			 $content .= '<priority>0.50</priority>';
			 $content .= '</url>';
			 $count++;
			}
		   } 
		 }
	    }
		 
		$content .= '</urlset>';
		$url_dir = Mage::getBaseDir()."/sitemap.xml";
		$file_handle = fopen($url_dir,'w');
		fwrite($file_handle,$content);
		fclose($file_handle);
		return true;
	}
	
	public function changeInterval()
    {   
		$obj = Mage::getModel('showmap/showmap');
		$showmap = $obj->getRow();
		if($showmap['format'] == 'days')
		{ 
		  $ping_interval = $showmap['ping_interval'];
		  $cron = '0 0 */'.$ping_interval.' * *';
		}
		if($showmap['format'] == 'hours')
		{
		  $ping_interval = $showmap['ping_interval'];
		  $cron = '0 */'.$ping_interval.' * * *';
		}
		$url = Mage::getBaseDir('code').'/community/Raveinfosys/Showmap/etc/config.xml';
		$endreXML = simplexml_load_file($url);
		$endreXML->crontab[0]->jobs[0]->Raveinfosys_Showmap[0]->schedule[0]->cron_expr = $cron;
		file_put_contents($url, $endreXML->asXML());	
	}
}