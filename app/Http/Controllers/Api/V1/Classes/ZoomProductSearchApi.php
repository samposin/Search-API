<?php namespace App\Http\Controllers\Api\V1\Classes;

class ZoomProductSearchApi {

	private $url_domain = "http://api.zoom.com.br/zoomapi";
	private $url_search_path = "/search/query/offers";

	private $rows_per_page=10;
	private $curl_auth_user = '';
	private $curl_auth_password = '';

	private $country_code="BR";
	private $final_api_url="";

	public function __construct()
	{
        $this->curl_auth_user=\Config::get('zoom_products_search_api.CURL_AUTH_USER');
        $this->curl_auth_password=\Config::get('zoom_products_search_api.CURL_AUTH_PASSWORD');
    }

    public function getCountryCode()
	{

		return $this->country_code;
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
            "%s%s?q=%s".
            "&items=%s".
            "&id_vision=%s",
            $this->url_domain,
            $this->url_search_path,
            $keyword,
            $this->rows_per_page,
            $subid
		);

		$this->final_api_url=$uri;



		return HelperFunctions::curl_with_authentication1($uri,$this->curl_auth_user,$this->curl_auth_password);
	}


}