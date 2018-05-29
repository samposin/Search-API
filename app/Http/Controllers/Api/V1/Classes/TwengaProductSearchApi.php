<?php namespace App\Http\Controllers\Api\V1\Classes;

class TwengaProductSearchApi {

	private $cack  = '';

    private $confkey  = '';

    private $e = '';

    private $url_domain = 'http://api.wtpn.twenga.com';

    private $country_code="US";


    private $url_search_path = '/search/product';

    private $rows_per_page = '10';

    private $keyword = '';
    private $sort_by = 'top_offers';

    private $final_api_url="";


    // Available country code
    public $country_arr=array(
		"FR","UK","DE","IT","NL","ES","BR","US","PL","SE"
	);

	public $country_name_arr=array(
		"FR"=>"France",
	    "UK"=>"United Kingdom",
	    "DE"=>"Germany",
	    "IT"=>"Italy",
	    "NL"=>"Netherlands",
	    "ES"=>"Spain",
	    "BR"=>"Brazil",
	    "US"=>"United States",
	    "PL"=>"Poland",
	    "SE"=>"Sweden"
	);

	public $country_url_arr=array(
		"FR"=>"http://api.wtpn.twenga.fr",
	    "UK"=>"http://api.wtpn.twenga.co.uk",
	    "DE"=>"http://api.wtpn.twenga.de",
	    "IT"=>"http://api.wtpn.twenga.it",
	    "NL"=>"http://api.wtpn.twenga.nl",
	    "ES"=>"http://api.wtpn.twenga.es",
	    "BR"=>"http://api.wtpn.twenga.com.br",
	    "US"=>"http://api.wtpn.twenga.com",
	    "PL"=>"http://api.wtpn.twenga.pl",
	    "SE"=>"http://api.wtpn.twenga.se"
	);

	public $country_currency_arr=array(
		"FR"=>"EUR",
		"UK"=>"GBP",
		"DE"=>"EUR",
	    "IT"=>"EUR",
	    "NL"=>"EUR",
	    "ES"=>"EUR",
	    "BR"=>"BRL",
		"US"=>"USD",
		"PL"=>"PLN",
	    "SE"=>"SEK"
	);

	public function __construct()
	{
        $this->cack=\Config::get('twenga_products_search_api.CACK');
        $this->confkey=\Config::get('twenga_products_search_api.CONFKEY');
        $this->e=\Config::get('twenga_products_search_api.E');
    }

	public function getCountryCurrencyCode()
	{
		return $this->country_currency_arr[$this->country_code];
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



	public function setCountryCodeAndUrlDomainByDomainLastWord($domain_last_word)
	{
		$domain_last_word=strtoupper($domain_last_word);
		$this->country_code="US";
		$this->url_domain="http://api.wtpn.twenga.com";

		if(in_array($domain_last_word,$this->country_arr))
		{
			$this->country_code=$domain_last_word;
			$this->url_domain=$this->country_url_arr[$domain_last_word];
		}

	}

	public function getFinalApiUrl()
	{
		return $this->final_api_url;
	}


	public function setRowsPerPage($rows_per_page)
    {
        $this->rows_per_page=$rows_per_page;
    }

    public function findProducts($keyword,$subid=0)
	{

		$keyword=urlencode($keyword);

		//build query uri
        $uri = sprintf(
            "%s%s?keyword=%s".
            "&cack=%s".
            "&confkey=%s".
            "&e=%s".
            "&subid=%s".
            "&nb_results=%s".
            "&sort_by=%s",
            $this->url_domain,
            $this->url_search_path,
            $keyword,
            $this->cack,
            $this->confkey,
            $this->e,
            $subid,
            $this->rows_per_page,
            $this->sort_by
		);

		$this->final_api_url=$uri;

		return HelperFunctions::curl($uri);
	}

}