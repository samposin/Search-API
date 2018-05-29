<?php namespace App\Http\Controllers\Api\V1\Classes;

use SoapBox\Formatter\Formatter;

class FindingApi {

	private $uri_finding = "http://svcs.ebay.com/services/search/FindingService/v1";
	private $appid = "";
    private $version;
    private $format = "XML";
    private $records_per_page=3;

    private $country_code="US";

    private $final_api_url="";



	public $country_arr=array(
		"AT","AU","CH","DE","US"
	);

	public $country_name_arr=array(
		"AT"=>"Austria",
		"AU"=>"Australia",
		"CH"=>"Switzerland",
		"DE"=>"Germany",
		"US"=>"United States"
	);


	public $country_currency_arr=array(
		"AT"=>"EUR",
		"AU"=>"AUD",
		"CH"=>"CHF",
		"DE"=>"EUR",
		"US"=>"USD"
	);

	public $country_ebay_site_arr=array(
		"AT"=>"EBAY-AT",
		"AU"=>"EBAY-AU",
		"CH"=>"EBAY-CH",
		"DE"=>"EBAY-DE",
		"US"=>"EBAY-US"
	);


    private $ebay_site="EBAY-US";
    /*
     *  some ebay sites values
     *  EBAY-US
     *  EBAY-GB
     *  EBAY-FR
     *  EBAY-IT
     *
     *  http://developer.ebay.com/devzone/finding/Concepts/SiteIDToGlobalID.html
     *
     * */

    private $filter_array=array();
    /*
     *  some filter array values
     *  BestOfferOnly       :true/false
     *  FeaturedOnly        :true/false
     *  FreeShippingOnly    :true/false
     *  HideDuplicateItems  :true/false
     *
     *  http://developer.ebay.com/devzone/finding/callref/types/ItemFilterType.html
     *
     * */

    private $sortOrder="";
    /*
     *  some sortOrder values
     *  BestMatch (Default)
     *  CurrentPriceHighest
     *  PricePlusShippingHighest
     *  PricePlusShippingLowest
     *  http://developer.ebay.com/devzone/finding/callref/extra/fndItmsByKywrds.Rqst.srtOrdr.html
     *
     * */



    /**
    * Constructor
    *
    * Sets the eBay version to the current API version
    *
    */
    public function __construct(){
        $this->appid=\Config::get('ebay.APPID');
        $this->version = $this->getCurrentVersion();
    }


    public function getCountryCurrencyCode()
	{
		return $this->country_currency_arr[$this->country_code];
	}

	public function getFinalApiUrl()
	{
		return $this->final_api_url;
	}

	public function getCountryEbaySite()
	{
		return $this->country_ebay_site_arr[$this->country_code];
	}

	public function setCountryCodeByDomainLastWord($domain_last_word)
	{
		$domain_last_word=strtoupper($domain_last_word);
		$this->country_code="US";

		if(in_array($domain_last_word,$this->country_arr))
		{
			$this->country_code=$domain_last_word;
		}

	}

    /**
    * Get Current Version
    *
    * Returns a string of the current eBay Finding API version
    *
    */
    private function getCurrentVersion(){

        $uri = sprintf(
            "%s".
            "?OPERATION-NAME=getVersion".
            "&SECURITY-APPNAME=%s".
            "&RESPONSE-DATA-FORMAT=%s",
            $this->uri_finding,
            $this->appid,
            $this->format
		);

        $response_xml = $this->curl($uri);

        $formatter = Formatter::make($response_xml, Formatter::XML);

        $response_array = $formatter->toArray();

        return $response_array['version'];
    }

    public function setAppId($appid)
    {
        $this->appid=$appid;
    }

    public function setEbaySite($ebay_site)
    {
        $this->ebay_site=$ebay_site;
    }

    public function setSortOrder($sortorder)
    {
        $this->sortOrder=$sortorder;
    }

    public function setRecordsPerPage($records_per_page)
    {
        $this->records_per_page=$records_per_page;
    }

    public function setFilterArray($filter_array)
    {
        foreach($filter_array as $k=>$v)
        {
            if(trim($v)!="")
	            $this->filter_array[]=array("name"=>$k,"value"=>$v);
        }
    }

	/**
	 * Find Products
	 *
	 * Allows you to search for eBay products based on keyword, product id or
	 * keywords (default).  Available values for search_type include
	 * findItemsByKeywords, findItemsByCategory, and findItemsByProduct
	 *
	 * @param string $search_type
	 * @param string $search_value
	 *
	 * @return mixed
	 */
    public function findProduct($search_type = 'findItemsByKeywords', $search_value = '10181'){

        //determine how to structure the search query parameter based on search type
        $search_field = "";

        switch ($search_type){

            case 'findItemsByCategory':
                $search_field = "categoryId=$search_value";
            break;

            case 'findItemsByProduct':
                $search_field = "productId.@type=ReferenceID&productId=$search_value";
            break;
	        case 'findItemsAdvanced':
            case 'findItemsByKeywords':
            default:
                $search_field = "keywords=" . urlencode($search_value);
            break;

        }

        //build query uri
        $uri = sprintf(
            "%s".
            "?OPERATION-NAME=%s".
            "&SERVICE-VERSION=%s".
            "&SECURITY-APPNAME=%s".
            "&RESPONSE-DATA-FORMAT=%s".
            "&REST-PAYLOAD&%s".
            "&paginationInput.entriesPerPage=%s",
            $this->uri_finding,
            $search_type,
            $this->version,
            $this->appid,
            $this->format,
            $search_field,
            $this->records_per_page
		);

		$uri.="&outputSelector(0)=StoreInfo";
		$uri.="&outputSelector(1)=SellerInfo";

		//echo $uri."<br>";

		if($this->ebay_site!="")
			$uri.="&GLOBAL-ID=".$this->ebay_site;


		if(count($this->filter_array)>0)
		{
			for($i=0;$i<count($this->filter_array);$i++){
				$uri.="&itemFilter(".$i.").name=".$this->filter_array[$i]['name']."&itemFilter(".$i.").value(0)=".$this->filter_array[$i]['value'];
				//$uri.="&itemFilter(".$i.").name=".$this->filter_array[$i]['name']."&itemFilter(".$i.").value=".$this->filter_array[$i]['value'];
			}
		}
		//PricePlusShippingLowest
		if($this->sortOrder!="")
			$uri.="&sortOrder=".$this->sortOrder;

		//echo $uri;die();

        //return json_decode($this->curl($uri),true);

        $this->final_api_url=$uri;
        return $this->curl($uri);
    }

	/**
	 * cURL
	 *
	 * Standard cURL function to run GET & POST requests
	 *
	 * @param $url
	 * @param string $method
	 * @param null $headers
	 * @param null $postvals
	 *
	 * @return mixed
	 */
    private function curl($url, $method = 'GET', $headers = null, $postvals = null){

        $ch = curl_init($url);

        if ($method == 'GET'){
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        } else {
            $options = array(
                CURLOPT_HEADER => true,
                CURLINFO_HEADER_OUT => true,
                CURLOPT_VERBOSE => true,
                CURLOPT_HTTPHEADER => $headers,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POSTFIELDS => $postvals,
                CURLOPT_CUSTOMREQUEST => $method,
                CURLOPT_TIMEOUT => 3
            );
            curl_setopt_array($ch, $options);
        }

        $response = curl_exec($ch);
        curl_close($ch);

        //echo $url;
        //echo "\n";

        //echo "response = ".$response;

        return $response;
    }

}