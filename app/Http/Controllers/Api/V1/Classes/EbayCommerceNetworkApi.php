<?php namespace App\Http\Controllers\Api\V1\Classes;

class EbayCommerceNetworkApi {


	private $url="http://api.ebaycommercenetwork.com";

	private $url_search_path = '/publisher/3.0/rest/GeneralSearch';

	private $apiKey = "";

	private $trackingId="";

	private $pageNumber=1;

	private $numItems=10;

	private $final_api_url="";

	private $country_code="US";

	private $partner_name="ALL";

	public $country_arr=array(
		"US","FR","UK","DE","AU"
	);

	public $country_currency_arr=array(
		"US"=>"USD",
		"FR"=>"EUR",
		"UK"=>"GBP",
		"DE"=>"EUR",
		"AU"=>"AUD",
	);

	// Credentials array according to country code and partner name
	public $country_partner_name_credential_arr= array(
		"US"    => array(
			"ALL"     => array(
				"apiKey" => "aa13ff97-9515-4db5-9a62-e8981b615d36", "trackingId" => "8023857"
			), "FIND" => array(
				"apiKey" => "aa13ff97-9515-4db5-9a62-e8981b615d36", "trackingId" => "8023857"
			), "FIND - RECETTES" => array(
				"apiKey" => "aa13ff97-9515-4db5-9a62-e8981b615d36", "trackingId" => "8023857"
			), "RAFO" => array(
				"apiKey" => "aa13ff97-9515-4db5-9a62-e8981b615d36", "trackingId" => "8023857"
			), "TVDM" => array(
				"apiKey" => "aa13ff97-9515-4db5-9a62-e8981b615d36", "trackingId" => "8023857"
			), "AZIM" => array(
				"apiKey" => "aa13ff97-9515-4db5-9a62-e8981b615d36", "trackingId" => "8023857"
			), "FIND - WIKIMOT" => array(
				"apiKey" => "aa13ff97-9515-4db5-9a62-e8981b615d36", "trackingId" => "8023857"
			), "SIRI" => array(
				"apiKey" => "aa13ff97-9515-4db5-9a62-e8981b615d36", "trackingId" => "8023857"
			), "ENIS" => array(
				"apiKey" => "aa13ff97-9515-4db5-9a62-e8981b615d36", "trackingId" => "8023857"
			)
		), "FR" => array(
			"ALL"     => array(
				"apiKey" => "46b9ba8e-6de7-433d-a202-7813b3db7f4c", "trackingId" => "8023857"
			), "FIND" => array(
				"apiKey" => "46b9ba8e-6de7-433d-a202-7813b3db7f4c", "trackingId" => "8023857"
			), "FIND - RECETTES" => array(
				"apiKey" => "46b9ba8e-6de7-433d-a202-7813b3db7f4c", "trackingId" => "8023857"
			), "RAFO" => array(
				"apiKey" => "46b9ba8e-6de7-433d-a202-7813b3db7f4c", "trackingId" => "8023857"
			), "TVDM" => array(
				"apiKey" => "46b9ba8e-6de7-433d-a202-7813b3db7f4c", "trackingId" => "8023857"
			), "AZIM" => array(
				"apiKey" => "46b9ba8e-6de7-433d-a202-7813b3db7f4c", "trackingId" => "8023857"
			),  "FIND - WIKIMOT" => array(
				"apiKey" => "46b9ba8e-6de7-433d-a202-7813b3db7f4c", "trackingId" => "8023857"
			), "SIRI" => array(
				"apiKey" => "46b9ba8e-6de7-433d-a202-7813b3db7f4c", "trackingId" => "8023857"
			), "ENIS" => array(
				"apiKey" => "46b9ba8e-6de7-433d-a202-7813b3db7f4c", "trackingId" => "8023857"
			)
		),
		"UK"=>array(
			"ALL"     => array(
				"apiKey" => "0dd70ad6-10cc-4b99-9528-a8913c221537", "trackingId" => "8023857"
			), "FIND" => array(
				"apiKey" => "0dd70ad6-10cc-4b99-9528-a8913c221537", "trackingId" => "8023857"
			), "FIND - RECETTES" => array(
				"apiKey" => "0dd70ad6-10cc-4b99-9528-a8913c221537", "trackingId" => "8023857"
			), "RAFO" => array(
				"apiKey" => "0dd70ad6-10cc-4b99-9528-a8913c221537", "trackingId" => "8023857"
			), "TVDM" => array(
				"apiKey" => "0dd70ad6-10cc-4b99-9528-a8913c221537", "trackingId" => "8023857"
			), "AZIM" => array(
				"apiKey" => "0dd70ad6-10cc-4b99-9528-a8913c221537", "trackingId" => "8023857"
			), "FIND - WIKIMOT" => array(
				"apiKey" => "0dd70ad6-10cc-4b99-9528-a8913c221537", "trackingId" => "8023857"
			), "SIRI" => array(
				"apiKey" => "0dd70ad6-10cc-4b99-9528-a8913c221537", "trackingId" => "8023857"
			), "ENIS" => array(
				"apiKey" => "0dd70ad6-10cc-4b99-9528-a8913c221537", "trackingId" => "8023857"
			)
		),
		"DE"=>array(
			"ALL"     => array(
				"apiKey" => "7acd1ac4-e6f6-41d2-87de-1e11c622cdcc", "trackingId" => "8023857"
			), "FIND" => array(
				"apiKey" => "7acd1ac4-e6f6-41d2-87de-1e11c622cdcc", "trackingId" => "8023857"
			), "FIND - RECETTES" => array(
				"apiKey" => "7acd1ac4-e6f6-41d2-87de-1e11c622cdcc", "trackingId" => "8023857"
			), "RAFO" => array(
				"apiKey" => "7acd1ac4-e6f6-41d2-87de-1e11c622cdcc", "trackingId" => "8023857"
			), "TVDM" => array(
				"apiKey" => "7acd1ac4-e6f6-41d2-87de-1e11c622cdcc", "trackingId" => "8023857"
			), "AZIM" => array(
				"apiKey" => "7acd1ac4-e6f6-41d2-87de-1e11c622cdcc", "trackingId" => "8023857"
			), "FIND - WIKIMOT" => array(
				"apiKey" => "7acd1ac4-e6f6-41d2-87de-1e11c622cdcc", "trackingId" => "8023857"
			), "SIRI" => array(
				"apiKey" => "7acd1ac4-e6f6-41d2-87de-1e11c622cdcc", "trackingId" => "8023857"
			), "ENIS" => array(
				"apiKey" => "7acd1ac4-e6f6-41d2-87de-1e11c622cdcc", "trackingId" => "8023857"
			)
		),
		"AU"=>array(
			"ALL"     => array(
				"apiKey" => "6e18f596-cda6-40a4-ac55-6d4c690bf0c8", "trackingId" => "8023857"
			), "FIND" => array(
				"apiKey" => "6e18f596-cda6-40a4-ac55-6d4c690bf0c8", "trackingId" => "8023857"
			), "FIND - RECETTES" => array(
				"apiKey" => "6e18f596-cda6-40a4-ac55-6d4c690bf0c8", "trackingId" => "8023857"
			), "RAFO" => array(
				"apiKey" => "6e18f596-cda6-40a4-ac55-6d4c690bf0c8", "trackingId" => "8023857"
			), "TVDM" => array(
				"apiKey" => "6e18f596-cda6-40a4-ac55-6d4c690bf0c8", "trackingId" => "8023857"
			), "AZIM" => array(
				"apiKey" => "6e18f596-cda6-40a4-ac55-6d4c690bf0c8", "trackingId" => "8023857"
			), "FIND - WIKIMOT" => array(
				"apiKey" => "6e18f596-cda6-40a4-ac55-6d4c690bf0c8", "trackingId" => "8023857"
			), "SIRI" => array(
				"apiKey" => "6e18f596-cda6-40a4-ac55-6d4c690bf0c8", "trackingId" => "8023857"
			), "ENIS" => array(
				"apiKey" => "6e18f596-cda6-40a4-ac55-6d4c690bf0c8", "trackingId" => "8023857"
			)
		),
	);

	public function __construct()
	{
        $this->apiKey=\Config::get('ebay_commerce_network_api.APIKEY');
        $this->trackingId=\Config::get('ebay_commerce_network_api.TRACKINGID');
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

	public function setCredentials()
	{
		if(trim($this->country_code)=="" || $this->partner_name=="")
		{
			$this->country_code="US";
			$this->partner_name="ALL";
		}


		$this->trackingId=$this->country_partner_name_credential_arr[$this->country_code][$this->partner_name]['trackingId'];
		$this->apiKey=$this->country_partner_name_credential_arr[$this->country_code][$this->partner_name]['apiKey'];

	}

	public function setCountryCodeAndPartnerNameByDomainLastWordAndPartnerName($domain_last_word,$partner_name='ALL')
	{

		$domain_last_word=strtoupper($domain_last_word);
		$partner_name=strtoupper($partner_name);
		$this->country_code="US";
		$this->partner_name="ALL";

		if(in_array($domain_last_word,$this->country_arr))
		{
			if(isset($this->country_partner_name_credential_arr[$domain_last_word][$partner_name]))
			{
				if(trim($this->country_partner_name_credential_arr[$domain_last_word][$partner_name]['trackingId'])!="")
				{
					$this->country_code=$domain_last_word;
					$this->partner_name=$partner_name;
				}
				else
				{
					if(trim($this->country_partner_name_credential_arr[$domain_last_word]['ALL']['trackingId'])!="")
					{
						$this->country_code=$domain_last_word;
						$this->partner_name="ALL";
					}
					else
					{
						$this->country_code="US";
						if(isset($this->country_partner_name_credential_arr['US'][$partner_name]))
						{
							if(trim($this->country_partner_name_credential_arr['US'][$partner_name]['trackingId'])!="")
								$this->partner_name=$partner_name;
							else
								$this->partner_name="ALL";
						}
						else
							$this->partner_name="ALL";
					}
				}
			}
			else
			{
				if(trim($this->country_partner_name_credential_arr[$domain_last_word]['ALL']['trackingId'])!="")
				{
					$this->country_code=$domain_last_word;
					$this->partner_name="ALL";
				}
				else
				{
					$this->country_code="US";
					if(isset($this->country_partner_name_credential_arr['US'][$partner_name]))
					{
						if(trim($this->country_partner_name_credential_arr['US'][$partner_name]['trackingId'])!="")
							$this->partner_name=$partner_name;
						else
							$this->partner_name="ALL";
					}
					else
						$this->partner_name="ALL";
				}
			}
		}
		else
		{
			$this->country_code="US";
			if(isset($this->country_partner_name_credential_arr['US'][$partner_name]))
			{
				if(trim($this->country_partner_name_credential_arr['US'][$partner_name]['trackingId'])!="")
					$this->partner_name=$partner_name;
				else
					$this->partner_name="ALL";
			}
			else
				$this->partner_name="ALL";
		}
	}

	public function getCountryCurrencyCode()
	{
		return $this->country_currency_arr[$this->country_code];
	}

	public function getFinalApiUrl()
	{
		return $this->final_api_url;
	}

	public function setRecordsPerPage($records_per_page)
    {
        $this->numItems=$records_per_page;
    }

    public function setPageNumber($page_number)
    {
        $this->pageNumber=$page_number;
    }

    public function findProducts($keyword,$visitor_user_agent,$visitor_ip_address,$category_id=0)
	{

		$keyword=urlencode($keyword);
		$visitor_user_agent=urlencode($visitor_user_agent);


		if($category_id!=0) {
			//build query uri
			 $uri = sprintf(
	            "%s%s?keyword=%s".
	            "&apiKey=%s".
	            "&trackingId=%s".
	            "&pageNumber=%s".
	            "&numItems=%s".
	            "&visitorUserAgent=%s".
	            "&visitorIPAddress=%s".
	            "&categoryId=%s",
	            $this->url,
	            $this->url_search_path,
	            $keyword,
	            $this->apiKey,
	            $this->trackingId,
	            $this->pageNumber,
	            $this->numItems,
		        $visitor_user_agent,
		        $visitor_ip_address,
				$category_id
			);
		}
		else
		{
			//build query uri
			 $uri = sprintf(
	            "%s%s?keyword=%s".
	            "&apiKey=%s".
	            "&trackingId=%s".
	            "&pageNumber=%s".
	            "&numItems=%s".
	            "&visitorUserAgent=%s".
	            "&visitorIPAddress=%s",
	            $this->url,
	            $this->url_search_path,
	            $keyword,
	            $this->apiKey,
	            $this->trackingId,
	            $this->pageNumber,
	            $this->numItems,
		        $visitor_user_agent,
		        $visitor_ip_address
			);
		}

		//echo $uri."<br>";
		$this->final_api_url=$uri;

		return HelperFunctions::curl($uri);
	}

	public function findCategories($keyword,$visitor_user_agent,$visitor_ip_address)
	{


		$keyword=urlencode($keyword);
		$visitor_user_agent=urlencode($visitor_user_agent);

		//build query uri
        $uri = sprintf(
            "%s%s?keyword=%s".
            "&apiKey=%s".
            "&trackingId=%s".
            "&numItems=%s".
            "&visitorUserAgent=%s".
            "&visitorIPAddress=%s",
            $this->url,
            $this->url_search_path,
            $keyword,
            $this->apiKey,
            $this->trackingId,
            0,
	        $visitor_user_agent,
	        $visitor_ip_address
		);


		return HelperFunctions::curl($uri);
	}
}