<?php namespace App\Http\Controllers\Api\V1\Classes;

class KelkooProductSearchApi {

	private $url_domain = "http://uk.shoppingapis.kelkoo.com";

	private $url_search_path = "/V3/productSearch";

	private $tracking_id=96249451;

	private $affiliate_key='f7lUI7FO';

	private $sort = 'default_ranking';
    private $start = 1;
    private $results = 10;
    private $showProducts = 1;
    private $showSubcategories = 0;
    private $showRefinements = 0;

    private $min_price = 0;

    private $max_price = 0;

    private $country_code;

    private $final_api_url="";

	// Available country code
    public $country_arr=array(
		"UK","RU","IT","AT","PL","BR","DE","CH","NL","FR","ES","PT","NO","SE","DK","FI"
	);

	public $country_name_arr=array(
		"UK"=>"United Kingdom",
		"RU"=>"Russia",
		"IT"=>"Italy",
		"AT"=>"Austria",
		"PL"=>"Poland",
		"BR"=>"Brazil",
		"DE"=>"Germany",
		"CH"=>"Switzerland",
		"NL"=>"Netherlands",
		"FR"=>"France",
		"ES"=>"Spain",
		"PT"=>"Portugal",
		"NO"=>"Norway",
		"SE"=>"Sweden",
		"DK"=>"Denmark",
		"FI"=>"Finland"
	);

	// Credentials array according to country code
	public $country_credential_arr=array(
		"UK"=>array("tracking_id"=>"96932151",   "affiliate_key"=>"f7lUY7FO",    "url_domain"=>"http://uk.shoppingapis.kelkoo.com"),
		"RU"=>array("tracking_id"=>"96932151",   "affiliate_key"=>"fjlIOrip",    "url_domain"=>"http://ru.shoppingapis.kelkoo.com"),
		"IT"=>array("tracking_id"=>"96932151",   "affiliate_key"=>"1dPERsxz",    "url_domain"=>"http://it.shoppingapis.kelkoo.com"),
		"AT"=>array("tracking_id"=>"96932151",   "affiliate_key"=>"5UGvbdQ7",    "url_domain"=>"http://at.shoppingapis.kelkoo.com"),
		"PL"=>array("tracking_id"=>"96932151",   "affiliate_key"=>"JYwqwfTR",    "url_domain"=>"http://pl.shoppingapis.kelkoo.com"),
		"BR"=>array("tracking_id"=>"96932151",   "affiliate_key"=>"9kkamv5C",    "url_domain"=>"http://br.shoppingapis.kelkoo.com"),
		"DE"=>array("tracking_id"=>"96932151",   "affiliate_key"=>"ZP2233en",    "url_domain"=>"http://de.shoppingapis.kelkoo.com"),
		"CH"=>array("tracking_id"=>"96932151",   "affiliate_key"=>"lE767EZl",    "url_domain"=>"http://ch.shoppingapis.kelkoo.com"),
		"NL"=>array("tracking_id"=>"96932151",   "affiliate_key"=>"P8F31rQP",    "url_domain"=>"http://nl.shoppingapis.kelkoo.com"),
		"FR"=>array("tracking_id"=>"96932151",   "affiliate_key"=>"oXVbn5yl",    "url_domain"=>"http://fr.shoppingapis.kelkoo.com"),
		"ES"=>array("tracking_id"=>"96932151",   "affiliate_key"=>"zq53439e",    "url_domain"=>"http://es.shoppingapis.kelkoo.com"),
		"PT"=>array("tracking_id"=>"96932151",   "affiliate_key"=>"0E1phhp0",    "url_domain"=>"http://pt.shoppingapis.kelkoo.com"),
		"NO"=>array("tracking_id"=>"96932151",   "affiliate_key"=>"5NB12WBF",    "url_domain"=>"http://no.shoppingapis.kelkoo.com"),
		"SE"=>array("tracking_id"=>"96932151",   "affiliate_key"=>"Ex0433gg",    "url_domain"=>"http://se.shoppingapis.kelkoo.com"),
		"DK"=>array("tracking_id"=>"96932151",   "affiliate_key"=>"kRe223sr",    "url_domain"=>"http://dk.shoppingapis.kelkoo.com"),
		"FI"=>array("tracking_id"=>"96932151",   "affiliate_key"=>"7ox11yUG",    "url_domain"=>"http://fi.shoppingapis.kelkoo.com"),
	);

	// Set credentials according to country code
	public function setCredentialsByDomainLastWord($domain_last_word)
    {
        $domain_last_word=strtoupper($domain_last_word);
        $this->tracking_id = $this->country_credential_arr[$domain_last_word]['tracking_id'];
        $this->affiliate_key = $this->country_credential_arr[$domain_last_word]['affiliate_key'];
        $this->url_domain = $this->country_credential_arr[$domain_last_word]['url_domain'];

        $this->country_code = $domain_last_word;
    }

	/**
	 * @return mixed
	 */
	public function getCountryCode()
	{

		return $this->country_code;
	}

	/**
	 * @param mixed $country_code
	 */
	public function setCountryCode($country_code)
	{

		$this->country_code = $country_code;
	}

    //Set Tracking Id
    public function setTrackingId($val)
    {
        $this->tracking_id = $val;
    }

    //Set Affiliate Key
    public function setAffiliateKey($val)
    {
        $this->affiliate_key = $val;
    }

    //Set Kelkoo Url Domain
    public function setUrlDomain($val)
    {
        $this->url_domain = $val;
    }

    //Set Sorting
    public function setSort($val)
    {
        $this->sort = $val;
    }

    //Set Start no for result
    public function setStart($val)
    {
        $this->start = $val;
    }

    //Set total no of result
    public function setResults($val)
    {
        $this->results = $val;
    }

    public function setMinPrice($val)
    {
        if($val)
            $this->min_price = $val;
    }

    public function setMaxPrice($val)
    {
        if($val)
            $this->max_price = $val;
    }

    //Get Tracking Id
    public function getTrackingId()
    {
        return $this->tracking_id;
    }

    //Get Affiliate Key
    public function getAffiliateKey()
    {
        return $this->affiliate_key;
    }

    //Get Kelkoo Url Domain
    public function getUrlDomain()
    {
        return $this->url_domain;
    }

    //Get Sorting
    public function getSort()
    {
        return $this->sort;
    }

    //Get Start no for result
    public function getStart()
    {
        return $this->start;
    }

    //Get total no of result
    public function getResults()
    {
        return $this->results;
    }

    public function getFinalApiUrl()
	{
		return $this->final_api_url;
	}

	// Check if country code available in kelkoo api
    public function isDomainLastWordInArray($domain_last_word)
	{
		$domain_last_word=strtoupper($domain_last_word);

		if(in_array($domain_last_word,$this->country_arr))
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	public function findProducts($keyword,$custom1='',$custom2='',$custom3='')
	{



        $url_path = sprintf(
            "%s?query=%s".
            "&custom1=%s".
            "&custom2=%s".
            "&custom3=%s".
            "&sort=%s".
            "&start=%s".
            "&results=%s".
            "&show_products=%s".
            "&show_subcategories=%s".
            "&show_refinements=%s",
            $this->url_search_path,
            urlencode($keyword),
            urlencode($custom1),
            urlencode($custom2),
            urlencode($custom3),
            $this->sort,
            "1",
            $this->results,
            "1",
	        "0",
	        "0"
		);

		if($this->min_price)
			$url_path.="&price_min=".$this->min_price;

		if($this->max_price)
			$url_path.="&price_max=".$this->max_price;

		$uri= $this->UrlSigner(
			$this->url_domain,
			$url_path,
			$this->tracking_id,
			$this->affiliate_key
		);

		$this->final_api_url=$uri;

		return HelperFunctions::curl($uri);
	}

	function UrlSigner($urlDomain, $urlPath, $partner, $key)
	{
		settype($urlDomain, 'String');
	    settype($urlPath, 'String');
	    settype($partner, 'String');
	    settype($key, 'String');
		$URL_sig = "hash";
	    $URL_ts = "timestamp";
	    $URL_partner = "aid";
		$URLreturn = "";
	    $URLtmp = "";
	    $s = "";

	    // get the timestamp
	    $time = time();

		// replace " " by "+"
	    $urlPath = str_replace(" ", "+", $urlPath);

		// format URL
	    $URLtmp = $urlPath . "&" . $URL_partner . "=" . $partner . "&" . $URL_ts . "=" . $time;
		// URL needed to create the tokken
	    $s = $urlPath . "&" . $URL_partner . "=" . $partner . "&" . $URL_ts . "=" . $time . $key;

		$tokken = "";
	    $tokken = base64_encode(pack('H*', md5($s)));
	    $tokken = str_replace(array("+", "/", "="), array(".", "_", "-"), $tokken);
		$URLreturn = $urlDomain . $URLtmp . "&" . $URL_sig . "=" . $tokken;

		return $URLreturn;

    }
}