<?php namespace App\Http\Controllers\Api\V1\Classes;

class DealsPricerProductsAPI {

	private $url = "http://uws.dealspricer.net/search/";

	private $api_key = "";

	private $pub = "";

	private $country_code="US";

	private $rows=10;

	private $final_api_url="";

	// Available country code
	public $country_arr=array(
		"IN","MY","SG","US","AU","NZ","HK",
		"TR","ID","VN","PH","TH","NG","UK"
	);

	public $country_name_arr=array(
		"IN"=>"India",
		"MY"=>"Malaysia",
		"SG"=>"Singapore",
		"US"=>"United State",
		"AU"=>"Australia",
		"NZ"=>"New Zealand",
		"HK"=>"Hong Kong",
		"TR"=>"Turkey",
		"ID"=>"Indonesia",
		"VN"=>"Vietnam",
		"PH"=>"Philippines",
		"TH"=>"Thailand",
		"NG"=>"Nigeria",
		"UK"=>"United Kingdom"
	);

	public $country_currency_arr=array(
		"IN"=>"INR",
		"MY"=>"MYR",
		"SG"=>"SGD",
		"US"=>"USD",
		"AU"=>"AUD",
		"NZ"=>"NZD",
		"HK"=>"HKD",
		"TR"=>"TRY",
		"ID"=>"IDR",
		"VN"=>"VND",
		"PH"=>"PHP",
		"TH"=>"THB",
		"NG"=>"NGN",
		"UK"=>"GBP"
	);


	public function __construct()
	{
        $this->api_key=\Config::get('dealspricer_products_search_api.APIKEY');
        $this->pub=\Config::get('dealspricer_products_search_api.PUB');
    }

	/**
	 * @return string
	 */
	public function getPub()
	{
		return $this->pub;
	}

	public function getFinalApiUrl()
	{
		return $this->final_api_url;
	}


	public function getCountryCurrencyCode()
	{
		return $this->country_currency_arr[$this->country_code];
	}

	public function setRecordsPerPage($records_per_page)
    {
        $this->rows=$records_per_page;
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
	 * @return string
	 */
	public function getCountryCode()
	{

		return $this->country_code;
	}

	/**
	 * @param string $country_code
	 */
	public function setCountryCode($country_code)
	{

		$this->country_code = $country_code;
	}



	public function findProducts($keyword)
	{
		$keyword=urlencode($keyword);

		//build query uri
        $uri = sprintf(
            "%s".
            "?country=%s".
            "&prodtype=%s".
            "&apikey=%s".
            "&q=%s".
            "&pub=%s".
            "&rows=%s",
            $this->url,
            $this->country_code,
            "pd",
            $this->api_key,
            $keyword,
            $this->pub,
	        $this->rows
		);

		$this->final_api_url=$uri;

		return HelperFunctions::curl($uri);

	}
}