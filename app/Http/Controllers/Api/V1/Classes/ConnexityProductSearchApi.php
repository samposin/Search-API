<?php namespace App\Http\Controllers\Api\V1\Classes;

class ConnexityProductSearchApi {

	private $url = "http://catalog.bizrate.com/services/catalog/v1";

	private $url_search_path = '/us/product';

	private $api_key = "95de0beaf1fbahn327389qwd51bb03a1";

	private $publisher_id = "613405";

	private $start = 1;
    private $results = 10;

    private $keyword = '';
    private $sort = 'relevancy_desc';

    private $min_price = 0;

    private $max_price = 0;

    private $final_api_url="";

    public function __construct()
	{
        $this->api_key=\Config::get('connexity_products_search_api.APIKEY');
        $this->publisher_id=\Config::get('connexity_products_search_api.PUBLISHERID');
    }

    //Set Records
    public function setSort($val)
    {
        $this->sort = $val;
    }
    public function setStart($val)
    {
        $this->start = $val;
    }
    public function setResults($val)
    {
        $this->results = $val;
    }

    public function setMinPrice($val)
    {
        if($val)
            $this->min_price = $val*100;
    }

    public function setMaxPrice($val)
    {
        if($val)
            $this->max_price = $val*100;
    }

    //Get Records
    public function getSort()
    {
        return $this->sort;
    }
    public function getStart()
    {
        return $this->start;
    }
    public function getResults()
    {
        return $this->results;
    }

    public function getFinalApiUrl()
	{
		return $this->final_api_url;
	}

    public function findProducts($keyword,$placementId=0)
	{

		$keyword=urlencode($keyword);

		//build query uri
        $uri = sprintf(
            "%s%s?keyword=%s".
            "&apiKey=%s".
            "&publisherId=%s".
            "&placementId=%s".
            "&start=%s".
            "&results=%s".
            "&sort=%s",
            $this->url,
            $this->url_search_path,
            $keyword,
            $this->api_key,
            $this->publisher_id,
            $placementId,
            $this->start,
            $this->results,
            $this->sort
		);

		if($this->min_price)
			$uri.="&minPrice=".$this->min_price;

		if($this->max_price)
			$uri.="&maxPrice=".$this->max_price;

		//$uri.=$uri.'&format=json';
		$this->final_api_url=$uri;

		//echo $uri;

		return HelperFunctions::curl($uri);
	}
}