<?php

namespace App\Http\Controllers\Api\V1;

use App\Advertiser;
use App\AdvertiserPublisherSearchDefault;
use App\AdvertiserSearchDefault;
use App\Http\Controllers\Api\V1\Classes\ConnexityProductSearchApi;
use App\Http\Controllers\Api\V1\Classes\DealsPricerProductsAPI;
use App\Http\Controllers\Api\V1\Classes\EbayCommerceNetworkApi;
use App\Http\Controllers\Api\V1\Classes\HelperFunctions;
use App\Http\Controllers\Api\V1\Classes\KelkooProductSearchApi;
use App\Http\Controllers\Api\V1\Classes\TwengaOfferSearchApi;
use App\Http\Controllers\Api\V1\Classes\ZoomProductSearchApi;
use App\Http\Controllers\Api\V1\Helpers\UserAgent;
use App\SearchRequestAll;
use Config;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\URL;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Pdp\Parser;
use Pdp\PublicSuffixListManager;
use PiwikTracker;
use SoapBox\Formatter\Formatter;
use App\Http\Controllers\Api\V1\Helpers\PublishersHelper;
use BrowserDetect;

include('Classes/xml2Array.php');

$msg_arr=array();
$msg_db_arr=array();

class SearchController extends Controller
{

	private $is_debug=0;
	private $showme=0;
	private $publisher_advertiser_sub_id_arr=array();
	private $user_ip="";
	private $widget="";
	private $keyword="";
	private $category_keyword="";
	private $sub_dl_source="";
	private $domain="";
	private $jsver="";
	private $user_country_code="";
	private $configurator_unique_id="";
	private $publisher_id=0;
	private $publisher_name="";
	private $request_uri="";
	private $http_user_agent="";
	private $browser="";

	private $min_price=0;
	private $max_price=0;
	private $records_per_page=0;

	private $is_update_search_table=0;
	private $search_request_table_obj;
	private $api_country_code;
	private $api_used;
	private $api_used_order;
	private $api_category;
	private $api_category_id;
	private $is_search_request_succeed;


	public function __construct()
	{
		date_default_timezone_set('America/Los_Angeles');

		//increase max execution time of this script to 150 min:
        ini_set('max_execution_time', 300);
        //increase Allowed Memory Size of this script:
        ini_set('memory_limit','256M');

	}

	public function insertSearchRequestTable()
	{
		$search_request_all_input['dl_source'] = $this->publisher_name;
		$search_request_all_input['sub_dl_source'] = $this->sub_dl_source;
		$search_request_all_input['widget'] = $this->widget;
		$search_request_all_input['keyword'] = $this->keyword;
		$search_request_all_input['domain'] = $this->domain;
		$search_request_all_input['ip'] = $this->user_ip;
		$search_request_all_input['user_country_code'] = $this->user_country_code;
		$search_request_all_input['request_uri'] = $this->request_uri;
		$search_request_all_input['http_user_agent'] = $this->http_user_agent;
		$search_request_all_input['browser'] = $this->browser;
		$search_request_all_input['configurator_unique_id'] = $this->configurator_unique_id;
		$search_request_all_input['category'] = $this->category_keyword;

		$rand_no=rand(1,200);

		if($rand_no==25 || $this->is_debug)
		{
			$this->search_request_table_obj = SearchRequestAll::create($search_request_all_input);
			$this->is_update_search_table=1;
		}

		unset($search_request_all_input);
	}

	public function updateSearchRequestTable()
	{
		global $msg_db_arr;

		$msg_db_str=implode("\n",$msg_db_arr);

		if(trim($this->api_used_order)!="")
			$this->api_used_order=substr($this->api_used_order,0,-2);

		$search_request_all_input=array();
		$search_request_all_input['api_country_code'] = $this->api_country_code;
		$search_request_all_input['api_used'] = $this->api_used;
		$search_request_all_input['api_used_order'] = $this->api_used_order;
		$search_request_all_input['api_category'] = $this->api_category;
		$search_request_all_input['api_category_id'] = $this->api_category_id;
		$search_request_all_input['msg'] = $msg_db_str;
		$search_request_all_input['is_succeed'] = $this->is_search_request_succeed;
		$search_request_all_input['is_completed'] = 1;

		if($this->is_update_search_table==1) {
			$this->search_request_table_obj->fill($search_request_all_input)->save();
		}
	}


	/**
	 * @param Request $request
	 *
	 * @return string
	 */
	public function index(Request $request)
    {
		global $msg_arr,$msg_db_arr;

        $result_array=[];
        $response_json='';
        $input = $request->all();

        $api_result_total_items=0;

        $this->user_ip = $this->getParamValue($request,'ip',$this->getUserIP());
        $this->is_debug=$this->getParamValue($request,'samdebugvar',0);
		$this->showme=$this->getParamValue($request,'showme',0);
		$this->domain=$this->getParamValue($request,'domain',"");
		$this->widget=$this->getParamValue($request,'widget',"");
		$this->keyword=$this->getParamValue($request,'kw',"");
		$this->sub_dl_source=$this->getParamValue($request,'dealsource',"");
		$this->jsver=$this->getParamValue($request,'jsver',"");
		$this->configurator_unique_id=$this->getParamValue($request,'configurator_unique_id',"");

		$this->min_price=$this->getParamValue($request,'minPrice',0);
		$this->max_price=$this->getParamValue($request,'maxPrice',0);
		$this->records_per_page=$this->getParamValue($request,'numItems',0);

		$this->request_uri=$_SERVER['REQUEST_URI'];
		$this->http_user_agent=$this->getParamValue($request,'ua',$_SERVER['HTTP_USER_AGENT']);

		/* This function gets browser information by using http_user_agent */
		//$this->browser=UserAgent::getBrowser($this->http_user_agent);
		$this->browser=HelperFunctions::getBrowser($this->http_user_agent);

		$this->category_keyword="";
		if($request->has('category') && $request->get('category')!=null)
		{
			if($request->get('category')!='undefined')
			{
				$this->category_keyword = $input['category'];
			}
		}

		$country_available_api_arr=HelperFunctions::getCountriesArrayAvailableInAllApis();

		$this->user_country_code=$this->getUserCountryCode($this->user_ip,$request);

		$publisher_info= PublishersHelper::getPublisherInfoByConfiguratorJSUniqueID($this->configurator_unique_id);
		$this->publisher_id=$publisher_info['publisher_id'];
		$this->publisher_name=$publisher_info['publisher_name'];

		$msg_arr[]=" Publisher id = ".$this->publisher_id;
		$msg_arr[]=" Publisher name = ".$this->publisher_name;

		$advertiser_search_defaults_arr=$this->getAdvertiserSearchDefaultsArr($this->publisher_id);

		$this->publisher_advertiser_sub_id_arr=array();
		if($this->publisher_id!=0)
		{
			$this->publisher_advertiser_sub_id_arr = $this->getPublisherAdvertisersSubIdArray($this->publisher_id);
		}

		// insert information into search request table
		$this->insertSearchRequestTable();

		// validate search request parameters
		$search_request_validate_response_arr=$this->validateSearchRequest();

		if(!$search_request_validate_response_arr['success'])
		{
			// displaying resultant JSON with error
			$response_json = Response::json([
				'success' => false, 'errorcode' => $search_request_validate_response_arr['errorcode'], 'errordescription' => $search_request_validate_response_arr['errordescription'],
			], 200);

			$msg_db_arr[]=$search_request_validate_response_arr['errordescription'];

			$this->updateSearchRequestTable();

			// if callback is given set it
	        if($request->has('callback') && $request->get('callback')!=null)
	        {
	            return $response_json->setCallback($input['callback']);
	        }

            return $response_json;
		}

		$domain_arr= HelperFunctions::separateWordByLastDot($this->domain);

		// Obtain an instance of the parser
		$pslManager = new PublicSuffixListManager();
		$php_domain_parser = new Parser($pslManager->getList());

		$php_domain_parser_registrable_domain=$php_domain_parser->getRegisterableDomain($this->domain);

		$php_domain_parser_domain_arr=HelperFunctions::separateWordByLastDot($php_domain_parser_registrable_domain);

		$php_domain_parser_domain_first_word=strtolower($php_domain_parser_domain_arr[0]);

		$result_array['items'] = array();
		$result_array_main=array();

		$visitor_ip_address=$this->user_ip;
		$visitor_user_agent = $this->http_user_agent;

		$domain_first_word=$domain_arr[0];

		$domain_last_word=strtolower($this->user_country_code);

		if(!in_array(strtoupper($domain_last_word),$country_available_api_arr))
		{
			$domain_last_word='us';
			$msg_arr[] = "geo not in available country in apis so geo assign to us";
			$msg_db_arr[]="geo not in available country for apis so geo assign to us";
		}

		$msg_arr[]="finally country is ".$domain_last_word;

		$results_from_connexity=array();
		$results_from_connexity_tmp=array("url"=>"","error_msg"=>"","custom_error_msg"=>"","result_array"=>array());

		$results_from_dealspricer=array();
		$results_from_dealspricer_tmp=array("url"=>"","error_msg"=>"","custom_error_msg"=>"","result_array"=>array());

		$results_from_ebay_commerce_network=array();
		$results_from_ebay_commerce_network_tmp=array("url"=>"","error_msg"=>"","custom_error_msg"=>"","result_array"=>array());

		$results_from_kelkoo=array();
		$results_from_kelkoo_tmp=array("url"=>"","error_msg"=>"","custom_error_msg"=>"","result_array"=>array());

		$results_from_twenga=array();
		$results_from_twenga_tmp=array("url"=>"","error_msg"=>"","custom_error_msg"=>"","result_array"=>array());

		$results_from_zoom=array();
		$results_from_zoom_tmp=array("url"=>"","error_msg"=>"","custom_error_msg"=>"","result_array"=>array());

		$advertiser_search_geo=$domain_last_word;

		$msg_db_arr[]="Original domain = ".$this->domain;

		if(isset($advertiser_search_defaults_arr[strtoupper($advertiser_search_geo)]))
		{
			$msg_arr[]="Domain is set.";

			if(trim($advertiser_search_defaults_arr[strtoupper($advertiser_search_geo)]['main'])=="" && trim($advertiser_search_defaults_arr[strtoupper($advertiser_search_geo)]['first'])=="" && trim($advertiser_search_defaults_arr[strtoupper($advertiser_search_geo)]['second'])=="")
			{
				$msg_arr[]="Main, First, Second api blank";
				$msg_db_arr[]="Search defaults set but empty in admin ui";
			}
			else
			{
				$this->api_used_order.=$advertiser_search_defaults_arr[strtoupper($advertiser_search_geo)]['main'].', ';

				if(trim($advertiser_search_defaults_arr[strtoupper($advertiser_search_geo)]['main'])!="") {
					$this->api_used = $advertiser_search_defaults_arr[strtoupper($advertiser_search_geo)]['main'];
					$msg_db_arr[] = "Search defaults main api = " . $this->api_used;
				}
				else
				{
					$msg_db_arr[] = "Search defaults main api is blank ";
				}

				if ($advertiser_search_defaults_arr[strtoupper($advertiser_search_geo)]['main'] == 'connexity') {
					$msg_arr[] = "connexity set in main api.";
					$results_from_connexity_tmp = $this->getDataFromConnexity($this->keyword,$php_domain_parser_domain_first_word, $domain_first_word, $domain_last_word,$this->publisher_name);
					$results_from_connexity = $results_from_connexity_tmp['result_array'];
					$results_from_connexity_tmp["custom_error_msg"] = "(geo set main api)";
				}
				elseif ($advertiser_search_defaults_arr[strtoupper($advertiser_search_geo)]['main'] == 'dealspricer') {
					$msg_arr[] = "dealspricer set in main api.";
					$results_from_dealspricer_tmp = $this->getDataFromDealsPricer($this->keyword,$php_domain_parser_domain_first_word, $domain_first_word, $domain_last_word,$this->publisher_name,$this->sub_dl_source,$this->widget);
					$results_from_dealspricer = $results_from_dealspricer_tmp['result_array'];
					$results_from_dealspricer_tmp["custom_error_msg"] = "(geo set main api)";
				}
				elseif ($advertiser_search_defaults_arr[strtoupper($advertiser_search_geo)]['main'] == 'ebay_commerce_network') {
					$msg_arr[] = "ebay_commerce_network set in main api.";
					$results_from_ebay_commerce_network_tmp = $this->getDataFromEbayCommerceNetwork($this->keyword,$php_domain_parser_domain_first_word, $domain_first_word, $domain_last_word, $visitor_user_agent, $visitor_ip_address, $this->publisher_name,$this->publisher_name,$this->category_keyword);
					$results_from_ebay_commerce_network = $results_from_ebay_commerce_network_tmp['result_array'];
					$results_from_ebay_commerce_network_tmp["custom_error_msg"] = "(geo set main api)";
				}
				elseif ($advertiser_search_defaults_arr[strtoupper($advertiser_search_geo)]['main'] == 'kelkoo') {
					$msg_arr[] = "kelkoo set in main api.";
					$results_from_kelkoo_tmp = $this->getDataFromKelkoo($this->keyword,$php_domain_parser_domain_first_word, $domain_first_word, $domain_last_word,$this->publisher_name,$this->sub_dl_source,$this->widget);
					$results_from_kelkoo = $results_from_kelkoo_tmp['result_array'];
					$results_from_kelkoo_tmp["custom_error_msg"] = "(geo set main api)";
				}
				elseif ($advertiser_search_defaults_arr[strtoupper($advertiser_search_geo)]['main'] == 'twenga') {
					$msg_arr[] = "twenga set in main api.";
					$results_from_twenga_tmp = $this->getOffersDataFromTwenga($this->keyword,$php_domain_parser_domain_first_word, $domain_first_word, $domain_last_word,$this->publisher_name);
					$results_from_twenga = $results_from_twenga_tmp['result_array'];
					$results_from_twenga_tmp["custom_error_msg"] = "(geo set main api)";
				}
				elseif ($advertiser_search_defaults_arr[strtoupper($advertiser_search_geo)]['main'] == 'zoom') {
					$msg_arr[] = "zoom set in main api.";
					$results_from_zoom_tmp = $this->getDataFromZoom($this->keyword,$php_domain_parser_domain_first_word, $domain_first_word, $domain_last_word,$this->publisher_name);
					$results_from_zoom = $results_from_zoom_tmp['result_array'];
					$results_from_zoom_tmp["custom_error_msg"] = "(geo set main api)";
				}

				$result_array_main = array_merge($results_from_connexity, $results_from_dealspricer, $results_from_ebay_commerce_network, $results_from_kelkoo, $results_from_twenga,$results_from_zoom);

				if (count($result_array_main) == 0) {
					$msg_arr[] = "main api result = 0";

					$this->api_used_order.=$advertiser_search_defaults_arr[strtoupper($advertiser_search_geo)]['first'].', ';

					if(trim($advertiser_search_defaults_arr[strtoupper($advertiser_search_geo)]['first'])!="")
					{
						$this->api_used=$advertiser_search_defaults_arr[strtoupper($advertiser_search_geo)]['first'];
						$msg_db_arr[]="Result zero from main api, search defaults first api = ".$this->api_used;
					}
					else
					{
						$msg_db_arr[]="Result zero from main api, search defaults first api is blank ";
					}

					if ($advertiser_search_defaults_arr[strtoupper($advertiser_search_geo)]['first'] == 'connexity') {
						$msg_arr[] = "connexity set in first api.";
						$results_from_connexity_tmp = $this->getDataFromConnexity($this->keyword, $php_domain_parser_domain_first_word,$domain_first_word, $domain_last_word,$this->publisher_name);
						$results_from_connexity = $results_from_connexity_tmp['result_array'];
						$results_from_connexity_tmp["custom_error_msg"] = "(geo set first api)";
					}
					elseif ($advertiser_search_defaults_arr[strtoupper($advertiser_search_geo)]['first'] == 'dealspricer') {
						$msg_arr[] = "dealspricer set in first api.";
						$results_from_dealspricer_tmp = $this->getDataFromDealsPricer($this->keyword,$php_domain_parser_domain_first_word, $domain_first_word, $domain_last_word,$this->publisher_name,$this->sub_dl_source,$this->widget);
						$results_from_dealspricer = $results_from_dealspricer_tmp['result_array'];
						$results_from_dealspricer_tmp["custom_error_msg"] = "(geo set first api)";
					}
					elseif ($advertiser_search_defaults_arr[strtoupper($advertiser_search_geo)]['first'] == 'ebay_commerce_network') {
						$msg_arr[] = "ebay_commerce_network set in first api.";
						$results_from_ebay_commerce_network_tmp = $this->getDataFromEbayCommerceNetwork($this->keyword,$php_domain_parser_domain_first_word, $domain_first_word, $domain_last_word, $visitor_user_agent, $visitor_ip_address, $this->publisher_name,$this->publisher_name,$this->category_keyword);
						$results_from_ebay_commerce_network = $results_from_ebay_commerce_network_tmp['result_array'];
						$results_from_ebay_commerce_network_tmp["custom_error_msg"] = "(geo set first api)";
					}
					elseif ($advertiser_search_defaults_arr[strtoupper($advertiser_search_geo)]['first'] == 'kelkoo') {
						$msg_arr[] = "kelkoo set in first api.";
						$results_from_kelkoo_tmp = $this->getDataFromKelkoo($this->keyword,$php_domain_parser_domain_first_word, $domain_first_word, $domain_last_word,$this->publisher_name,$this->sub_dl_source,$this->widget);
						$results_from_kelkoo = $results_from_kelkoo_tmp['result_array'];
						$results_from_kelkoo_tmp["custom_error_msg"] = "(geo set first api)";
					}
					elseif ($advertiser_search_defaults_arr[strtoupper($advertiser_search_geo)]['first'] == 'twenga') {
						$msg_arr[] = "twenga set in first api.";
						$results_from_twenga_tmp = $this->getOffersDataFromTwenga($this->keyword,$php_domain_parser_domain_first_word, $domain_first_word, $domain_last_word,$this->publisher_name);
						$results_from_twenga = $results_from_twenga_tmp['result_array'];
						$results_from_twenga_tmp["custom_error_msg"] = "(geo set first api)";
					}
					elseif ($advertiser_search_defaults_arr[strtoupper($advertiser_search_geo)]['first'] == 'zoom') {
						$msg_arr[] = "zoom set in first api.";
						$results_from_zoom_tmp = $this->getDataFromZoom($this->keyword,$php_domain_parser_domain_first_word, $domain_first_word, $domain_last_word,$this->publisher_name);
						$results_from_zoom = $results_from_zoom_tmp['result_array'];
						$results_from_zoom_tmp["custom_error_msg"] = "(geo set first api)";
					}

					$result_array_main = array_merge($results_from_connexity, $results_from_dealspricer, $results_from_ebay_commerce_network, $results_from_kelkoo, $results_from_twenga,$results_from_zoom);

					if (count($result_array_main) == 0) {
						$msg_arr[] = "first api result = 0";

						$this->api_used_order.=$advertiser_search_defaults_arr[strtoupper($advertiser_search_geo)]['second'].', ';

						if(trim($advertiser_search_defaults_arr[strtoupper($advertiser_search_geo)]['second'])!="")
						{
							$this->api_used=$advertiser_search_defaults_arr[strtoupper($advertiser_search_geo)]['second'];
							$msg_db_arr[]="Result zero from main and first api, search defaults second api = ".$this->api_used;
						}
						else
						{
							$msg_db_arr[]="Result zero from main and first api, search defaults second api is blank ";
						}

						if ($advertiser_search_defaults_arr[strtoupper($advertiser_search_geo)]['second'] == 'connexity') {
							$msg_arr[] = "connexity set in second api.";
							$results_from_connexity_tmp = $this->getDataFromConnexity($this->keyword,$php_domain_parser_domain_first_word, $domain_first_word, $domain_last_word,$this->publisher_name);
							$results_from_connexity = $results_from_connexity_tmp['result_array'];
							$results_from_connexity_tmp["custom_error_msg"] = "(geo set second api)";
						}
						elseif ($advertiser_search_defaults_arr[strtoupper($advertiser_search_geo)]['second'] == 'dealspricer') {
							$msg_arr[] = "dealspricer set in second api.";
							$results_from_dealspricer_tmp = $this->getDataFromDealsPricer($this->keyword,$php_domain_parser_domain_first_word, $domain_first_word, $domain_last_word,$this->publisher_name,$this->sub_dl_source,$this->widget);
							$results_from_dealspricer = $results_from_dealspricer_tmp['result_array'];
							$results_from_dealspricer_tmp["custom_error_msg"] = "(geo set second api)";
						}
						elseif ($advertiser_search_defaults_arr[strtoupper($advertiser_search_geo)]['second'] == 'ebay_commerce_network') {
							$msg_arr[] = "ebay_commerce_network set in second api.";
							$results_from_ebay_commerce_network_tmp = $this->getDataFromEbayCommerceNetwork($this->keyword,$php_domain_parser_domain_first_word, $domain_first_word, $domain_last_word, $visitor_user_agent, $visitor_ip_address, $this->publisher_name,$this->publisher_name,$this->category_keyword);
							$results_from_ebay_commerce_network = $results_from_ebay_commerce_network_tmp['result_array'];
							$results_from_ebay_commerce_network_tmp["custom_error_msg"] = "(geo set second api)";
						}
						elseif ($advertiser_search_defaults_arr[strtoupper($advertiser_search_geo)]['second'] == 'kelkoo') {
							$msg_arr[] = "kelkoo set in second api.";
							$results_from_kelkoo_tmp = $this->getDataFromKelkoo($this->keyword,$php_domain_parser_domain_first_word, $domain_first_word, $domain_last_word,$this->publisher_name,$this->sub_dl_source,$this->widget);
							$results_from_kelkoo = $results_from_kelkoo_tmp['result_array'];
							$results_from_kelkoo_tmp["custom_error_msg"] = "(geo set second api)";
						}
						elseif ($advertiser_search_defaults_arr[strtoupper($advertiser_search_geo)]['second'] == 'twenga') {
							$msg_arr[] = "twenga set in second api.";
							$results_from_twenga_tmp = $this->getOffersDataFromTwenga($this->keyword,$php_domain_parser_domain_first_word, $domain_first_word, $domain_last_word,$this->publisher_name);
							$results_from_twenga = $results_from_twenga_tmp['result_array'];
							$results_from_twenga_tmp["custom_error_msg"] = "(geo set second api)";
						}
						elseif ($advertiser_search_defaults_arr[strtoupper($advertiser_search_geo)]['second'] == 'zoom') {
							$msg_arr[] = "zoom set in second api.";
							$results_from_zoom_tmp = $this->getDataFromZoom($this->keyword,$php_domain_parser_domain_first_word, $domain_first_word, $domain_last_word,$this->publisher_name);
							$results_from_zoom = $results_from_zoom_tmp['result_array'];
							$results_from_zoom_tmp["custom_error_msg"] = "(geo set second api)";
						}

						$result_array_main = array_merge($results_from_connexity, $results_from_dealspricer, $results_from_ebay_commerce_network, $results_from_kelkoo, $results_from_twenga,$results_from_zoom);
					}
				}
			}
		}
		else
		{
			$msg_arr[]="Domain is not set.";
			$msg_db_arr[]="Search defaults not setin admin ui";
		}

		$apis_total_items_arr['dealspricer']=count($results_from_dealspricer);
		$apis_total_items_arr['kelkoo']=count($results_from_kelkoo);
		$apis_total_items_arr['twenga']=count($results_from_twenga);
		$apis_total_items_arr['connexity']=count($results_from_connexity);
		$apis_total_items_arr['ebay_commerce_network']=count($results_from_ebay_commerce_network);
		$apis_total_items_arr['zoom']=count($results_from_zoom);

		if($this->is_debug)
		{

			$result_array['apis']['dealspricer']['url']=$results_from_dealspricer_tmp['url'];
			$result_array['apis']['dealspricer']['total_item']=count($results_from_dealspricer_tmp['result_array']);
			$result_array['apis']['dealspricer']['error_msg']=$results_from_dealspricer_tmp['error_msg'];
			$result_array['apis']['dealspricer']['custom_error_msg']=$results_from_dealspricer_tmp['custom_error_msg'];

			$result_array['apis']['kelkoo']['url']=$results_from_kelkoo_tmp['url'];
			$result_array['apis']['kelkoo']['total_item']=count($results_from_kelkoo_tmp['result_array']);
			$result_array['apis']['kelkoo']['error_msg']=$results_from_kelkoo_tmp['error_msg'];
			$result_array['apis']['kelkoo']['custom_error_msg']=$results_from_kelkoo_tmp['custom_error_msg'];

			$result_array['apis']['twenga']['url']=$results_from_twenga_tmp['url'];
			$result_array['apis']['twenga']['total_item']=count($results_from_twenga_tmp['result_array']);
			$result_array['apis']['twenga']['error_msg']=$results_from_twenga_tmp['error_msg'];
			$result_array['apis']['twenga']['custom_error_msg']=$results_from_twenga_tmp['custom_error_msg'];

			$result_array['apis']['connexity']['url']=$results_from_connexity_tmp['url'];
			$result_array['apis']['connexity']['total_item']=count($results_from_connexity_tmp['result_array']);
			$result_array['apis']['connexity']['error_msg']=$results_from_connexity_tmp['error_msg'];
			$result_array['apis']['connexity']['custom_error_msg']=$results_from_connexity_tmp['custom_error_msg'];

			$result_array['apis']['ebay_commerce_network']['url']=$results_from_ebay_commerce_network_tmp['url'];
			$result_array['apis']['ebay_commerce_network']['total_item']=count($results_from_ebay_commerce_network_tmp['result_array']);
			$result_array['apis']['ebay_commerce_network']['error_msg']=$results_from_ebay_commerce_network_tmp['error_msg'];
			$result_array['apis']['ebay_commerce_network']['custom_error_msg']=$results_from_ebay_commerce_network_tmp['custom_error_msg'];

			$result_array['apis']['zoom']['url']=$results_from_zoom_tmp['url'];
			$result_array['apis']['zoom']['total_item']=count($results_from_zoom_tmp['result_array']);
			$result_array['apis']['zoom']['error_msg']=$results_from_zoom_tmp['error_msg'];
			$result_array['apis']['zoom']['custom_error_msg']=$results_from_zoom_tmp['custom_error_msg'];

			$result_array["apis_total_items"] = $apis_total_items_arr;

			$result_array['msg_arr']=$msg_arr;
		}
		$i=0;
		$j=0;

		$return_items=10;
		if($this->records_per_page>0)
			$return_items=$this->records_per_page;

		while($i<count($result_array_main) && $j<$return_items)
		{

			$result_array['items'][$j]["item_id"] = $result_array_main[$i]['item_id'];
			$result_array['items'][$j]["item_title"] =  mb_convert_case(mb_strtolower($result_array_main[$i]['item_title']), MB_CASE_TITLE, "UTF-8");
			$result_array['items'][$j]["item_price"] = $result_array_main[$i]['item_price'];
			$result_array['items'][$j]['item_currency_code'] = $result_array_main[$i]['item_currency_code'];
			$result_array['items'][$j]["item_image"] = $result_array_main[$i]['item_image'];
			$result_array['items'][$j]["category_id"] = $result_array_main[$i]['category_id'];
			$result_array['items'][$j]["category_name"] = $result_array_main[$i]['category_name'];
			$result_array['items'][$j]["is_free_shipping"] = $result_array_main[$i]['is_free_shipping'];
			$result_array['items'][$j]["store_name"] = $result_array_main[$i]['store_name'];
			$result_array['items'][$j]["store_url"] = $result_array_main[$i]['store_url'];
			$result_array['items'][$j]["pubadvert_subid"] = $result_array_main[$i]['pubadvert_subid'];
			$result_array['items'][$j]["api_used"] = $this->api_used;
			$result_array['items'][$j]["search_id"] = 0;

			$itemurl=URL::route('search_item_click_handler');
			$itemurl.='?url='.urlencode($result_array_main[$i]['item_url']);
			$itemurl.='&cuid='.urlencode($this->configurator_unique_id);
			$itemurl.='&widget='.urlencode($this->widget);
			$itemurl.='&subid='.urlencode($this->sub_dl_source);
			$itemurl.='&api='.urlencode($result_array_main[$i]['api']);
			$itemurl.='&co_code='.urlencode($result_array_main[$i]['country_code']);
			$itemurl.='&searchid=0';
			$itemurl.='&jsver='.urlencode($this->jsver);
			$itemurl.='&domain='.urlencode($this->domain);
			$itemurl.='&cat='.urlencode($this->category_keyword);
			$itemurl.='&api_cat='.urlencode($result_array_main[$i]['api_category']);
			$itemurl.='&api_cat_id='.urlencode($result_array_main[$i]['api_category_id']);
			$itemurl.='&kw='.urlencode($this->keyword);

			$this->api_country_code=$result_array_main[$i]['country_code'];

			$result_array['items'][$j]["item_url"] = $itemurl;

			$this->api_category=$result_array_main[$i]["api_category"];
			$this->api_category_id=$result_array_main[$i]["api_category_id"];

			if($this->is_debug)
			{
				$result_array['items'][$j]["item_url1"] = $result_array_main[$i]['item_url'];
				$result_array['items'][$j]["item_url2"] = urldecode($result_array_main[$i]['item_url']);
				$result_array['items'][$j]["item_title1"] = $result_array_main[$i]['item_title'];
				$result_array['items'][$j]["api_info"] = $result_array_main[$i]['api_info'];
				$result_array['items'][$j]["api_category"] = $result_array_main[$i]['api_category'];
				$result_array['items'][$j]["api_category_id"] = $result_array_main[$i]['api_category_id'];
			}
			$j++;
			$i++;
		}

		$this->is_search_request_succeed=1;
		$msg_db_arr[]="Total number of result = ".count($result_array['items']);
		$api_result_total_items=count($result_array['items']);

		$response_json = Response::json([
			'success' => true, 'message' => "", 'info' => $result_array
		], 200);

		$this->updateSearchRequestTable();

		// if callback is given set it
        if($request->has('callback') && $request->get('callback')!=null)
        {
            return $response_json->setCallback($input['callback']);
        }

        return $response_json;
    }

	function validateSearchRequest()
	{
		$response['success']=true;

		if($this->keyword=="")
		{
			$response['success']=false;
			$response['errorcode']="error1";
			$response['errordescription']="Keyword missing";

			return $response;
		}

		if($this->domain=="")
		{
			$response['success']=false;
			$response['errorcode']="error2";
			$response['errordescription']="Domain missing";

			return $response;
		}

		$domain_arr= HelperFunctions::separateWordByLastDot($this->domain);

		if(count($domain_arr)==0)
		{
			$response['success']=false;
			$response['errorcode']="error2";
			$response['errordescription']="Domain must have at least one dot";

			return $response;
		}

		if($this->widget=="")
		{
			$response['success']=false;
			$response['errorcode']="error5";
			$response['errordescription']="Widget name missing";

			return $response;
		}



		return $response;
	}

    public function getParamValue(Request $request, $field, $value)
	{
		$new_value=$value;
		if ($request->has($field)) {
		    $new_value=$request->get($field);
		}

		return $new_value;
	}

	public function getUserIP()
	{
		global $msg_arr;

		if (!empty($_SERVER["HTTP_X_FORWARDED_FOR"])) {
            $user_ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
            $msg_arr[]="find ip from HTTP_X_FORWARDED_FOR";
        } else {
            $user_ip = $_SERVER["REMOTE_ADDR"];
            $msg_arr[]="find ip from REMOTE_ADDR";
        }

        return $user_ip;
	}

	public function getUserCountryCode($user_ip,Request $request)
	{
		global $msg_arr,$msg_db_arr;

		$user_country_code="";

		if(trim($user_ip)!="")
        {
			if (function_exists('geoip_record_by_name'))
			{
				$msg_arr[]="geoip_record_by_name function exists";
				$user_ip_exp=explode(',',$user_ip);
				$user_ip=trim($user_ip_exp[count($user_ip_exp)-1]);

				$geoinfo = @geoip_record_by_name($user_ip);
				$msg_arr[]=$geoinfo;

				if(isset($geoinfo['country_code']))
					$user_country_code=$geoinfo['country_code'];

			}
			else
			{
				$msg_arr[]="geoip_record_by_name function does not exist";
			}
        }

        if(trim($user_country_code)=="")
        {
	        $user_country_code = 'US';
	        $msg_arr[]=" geo_country_code is blank so assign US";
	        $msg_db_arr[]="User country code is empty assign US";
        }

        if($this->is_debug)
        {
            if($request->has('g_co') && $request->get('g_co')!=null)
            {
	            $user_country_code = $request->get('g_co');
            }
			$msg_arr[]="debug geo = ".$user_country_code;
        }

        if(trim($user_country_code)=="GB")
        {
	        $user_country_code = 'UK';
	        $msg_arr[]=" geo_country_code is GB so assign UK";
        }

        return $user_country_code;
	}

    public function getAdvertiserSearchDefaultsArr($publisher_id=0)
    {
        global $msg_arr;

		$advertiser_search_defaults=AdvertiserSearchDefault::orderBy('geo','asc')->get();
		$advertiser_search_defaults_arr=array();
		foreach($advertiser_search_defaults as $advertiser_search_default)
		{
			$advertiser_search_defaults_arr[$advertiser_search_default->geo]=array('main'=>$advertiser_search_default->main_api,"first"=>$advertiser_search_default->first_backfill_api,"second"=>$advertiser_search_default->second_backfill_api);
		}

		$msg_arr[] =$advertiser_search_defaults_arr;

		if($publisher_id==0)
		{
			return $advertiser_search_defaults_arr;
		}

	    $advertiser_publisher_search_defaults = AdvertiserPublisherSearchDefault::where('publisher_id', '=', $publisher_id)->orderBy('geo', 'asc')->first();

	    if ($advertiser_publisher_search_defaults == null)
	    {
		    $msg_arr[] = "publisher search default dose not exist";
		    return $advertiser_search_defaults_arr;
	    }

	    $msg_arr[] = "publisher search default exists";

	    $advertiser_publisher_search_defaults = AdvertiserPublisherSearchDefault::where('publisher_id', '=', $publisher_id)->orderBy('geo', 'asc')->get();

	    foreach ($advertiser_publisher_search_defaults as $advertiser_publisher_search_default)
	    {
		    $publisher_db_geo = $advertiser_publisher_search_default->geo;
		    if (trim($publisher_db_geo) != "")
		    {
			    if (isset($advertiser_search_defaults_arr[$publisher_db_geo]))
			    {
				    if (trim($advertiser_publisher_search_default->main_api) != "")
					    $advertiser_search_defaults_arr[$publisher_db_geo]['main'] = $advertiser_publisher_search_default->main_api;

				    if (trim($advertiser_publisher_search_default->first_backfill_api) != "")
					    $advertiser_search_defaults_arr[$publisher_db_geo]['first'] = $advertiser_publisher_search_default->first_backfill_api;

				    if (trim($advertiser_publisher_search_default->second_backfill_api) != "")
					    $advertiser_search_defaults_arr[$publisher_db_geo]['second'] = $advertiser_publisher_search_default->second_backfill_api;
			    }
			    else
			    {
				    $advertiser_search_defaults_arr[$publisher_db_geo]['main'] = $advertiser_publisher_search_default->main_api;
				    $advertiser_search_defaults_arr[$publisher_db_geo]['first'] = $advertiser_publisher_search_default->first_backfill_api;
				    $advertiser_search_defaults_arr[$publisher_db_geo]['second'] = $advertiser_publisher_search_default->second_backfill_api;
			    }
		    }
	    }

        $msg_arr[] =$advertiser_search_defaults_arr;

		return $advertiser_search_defaults_arr;
    }


    public function getDataFromEbayCommerceNetwork($keyword,$php_domain_parser_domain_first_word,$domain_first_word,$domain_last_word,$visitor_user_agent,$visitor_ip_address,$partner_name='ALL',$publisher_name,$category_keyword='')
    {
        global $msg_arr,$msg_db_arr;

        $result_array=array();
        $result_array1=array();
        $result_array2=array();
        $result_array3=array();

        $final_api_url="";
		$error_msg="";

		$msg_db_arr[]="In Ebay Function";
		$msg_db_arr[]="Domain after parsing = ".$php_domain_parser_domain_first_word;

		$ebay_commerce_network_obj = new EbayCommerceNetworkApi();
        $ebay_commerce_network_obj->setCountryCodeAndPartnerNameByDomainLastWordAndPartnerName($domain_last_word,$partner_name);

        $ebay_commerce_network_obj->setCredentials();
        $ebay_commerce_network_obj->setRecordsPerPage(Config::get('ebay_commerce_network_api.rows_per_page'));
        $country_currency_code=$ebay_commerce_network_obj->getCountryCurrencyCode();
        $country_code=$ebay_commerce_network_obj->getCountryCode();

		$category_id=0;
		$category_name="";

	    if ($this->showme)
	    {
		    if ($category_keyword != "")
		    {
			    $results_category_xml_from_ebay_commerce_network = $ebay_commerce_network_obj->findCategories($category_keyword, $visitor_user_agent, $visitor_ip_address);

			    $formatter = Formatter::make($results_category_xml_from_ebay_commerce_network, Formatter::XML);
			    $results_category_from_ebay_commerce_network['GeneralSearchResponse'] = $formatter->toArray();

			    //Check error if any
			    $exceptionmsg = "";
			    if (isset($results_category_from_ebay_commerce_network['GeneralSearchResponse']['exceptions']['exception']))
			    {
				    if (isset($results_category_from_ebay_commerce_network['GeneralSearchResponse']['exceptions']['exception'][0]))
				    {
					    foreach ($results_category_from_ebay_commerce_network['GeneralSearchResponse']['exceptions']['exception'] as $k => $v)
					    {
						    if (is_int($k))
						    {
							    if ($v['code'] == 88)
								    $exceptionmsg = $v['message'];
						    }
					    }
				    }
				    else
				    {
					    if ($results_category_from_ebay_commerce_network['GeneralSearchResponse']['exceptions']['exception']['code'] == 88)
						    $exceptionmsg = $results_category_from_ebay_commerce_network['GeneralSearchResponse']['exceptions']['exception']['message'];
				    }
			    }

			    if (trim($exceptionmsg) == "")
			    {
				    if (isset($results_category_from_ebay_commerce_network['GeneralSearchResponse']))
				    {
					    if (isset($results_category_from_ebay_commerce_network['GeneralSearchResponse']['categories']))
					    {
						    if (isset($results_category_from_ebay_commerce_network['GeneralSearchResponse']['categories']['category']))
						    {
							    if (isset($results_category_from_ebay_commerce_network['GeneralSearchResponse']['categories']['category'][0]))
							    {
								    $cat_data_info_arr = $results_category_from_ebay_commerce_network['GeneralSearchResponse']['categories']['category'];
							    }
							    else
							    {
								    $cat_data_info_arr[0] = $results_category_from_ebay_commerce_network['GeneralSearchResponse']['categories']['category'];
							    }

							    if (count($cat_data_info_arr) > 0)
							    {
								    $category_name = $cat_data_info_arr[0]['name'];
								    $category_id = $cat_data_info_arr[0]['@attributes']['id'];
							    }
						    }
					    }
				    }
			    }
		    }
	    }

		$results_xml_from_ebay_commerce_network=$ebay_commerce_network_obj->findProducts($keyword,$visitor_user_agent,$visitor_ip_address,$category_id);
		$final_api_url=$ebay_commerce_network_obj->getFinalApiUrl();

		$results_from_ebay_commerce_network=xml2array($results_xml_from_ebay_commerce_network, 1);

        //Check error if any
        $exceptionmsg="";
        if(isset($results_from_ebay_commerce_network['GeneralSearchResponse']['exceptions']['exception']))
        {
			if(isset($results_from_ebay_commerce_network['GeneralSearchResponse']['exceptions']['exception'][0]))
			{
	            foreach($results_from_ebay_commerce_network['GeneralSearchResponse']['exceptions']['exception'] as $k=>$v)
	            {
	                if(is_int($k)) {
		                if($v['code']==88)
		                    $exceptionmsg=$v['message'];
	                }
	            }
			}
			else
			{
				if($results_from_ebay_commerce_network['GeneralSearchResponse']['exceptions']['exception']['code']==88)
					$exceptionmsg=$results_from_ebay_commerce_network['GeneralSearchResponse']['exceptions']['exception']['message'];
			}
        }

        if(trim($exceptionmsg)!="")
        {
			 $error_msg = "No item found";
        }
        else
        {
            if(isset($results_from_ebay_commerce_network['GeneralSearchResponse']))
            {
	            if(isset($results_from_ebay_commerce_network['GeneralSearchResponse']['categories']))
	            {
		            $chkproduct = $results_from_ebay_commerce_network['GeneralSearchResponse']['categories']['category']['items'];
		            if (!array_key_exists('product', $chkproduct) && !array_key_exists('offer', $chkproduct)) {
			            $error_msg = "No product or offer found";
		            }
		            else
		            {
			            $pddata = array();
			            if (isset($results_from_ebay_commerce_network['GeneralSearchResponse']['categories']['category']['items']['product']))
			            {
				            $pddata1 = $results_from_ebay_commerce_network['GeneralSearchResponse']['categories']['category']['items']['product'];

				            if (count($pddata1) > 0)
				            {
					            //Check if only one product in list
					            if (!array_key_exists('0', $pddata1))
					            {
						            $pddata[0] = $pddata1;
					            }
					            else
					            {
						            $pddata = $results_from_ebay_commerce_network['GeneralSearchResponse']['categories']['category']['items']['product'];
					            }

					            //Check all data in the list
					            foreach ($pddata as $k => $v)
					            {
						            if (is_int($k))
						            {
							            $pdata = array();
							            $pdata['item_id'] = '';
							            $pdata['item_title'] = $pddata[$k]['name'];
							            $pdata['item_url'] = $pddata[$k]['productSpecsURL'];

							            if (isset($pddata[$k]['images']['image'][0]))
								            $pdata['item_image'] = $pddata[$k]['images']['image'][0]['sourceURL'];
							            else
								            $pdata['item_image'] = $pddata[$k]['images']['image']['sourceURL'];

							            if (isset($pddata[$k]['categoryId']))
								            $pdata['category_id'] = $pddata[$k]['categoryId'];
							            else
								            $pdata['category_id'] = "";

							            if (isset($pddata[$k]['categoryName']))
								            $pdata['category_name'] = $pddata[$k]['categoryName'];
							            else
								            $pdata['category_name'] = "";

							            if ($pddata[$k]['freeShipping'] == 'true')
								            $pdata['is_free_shipping'] = 1;
							            else
								            $pdata['is_free_shipping'] = 0;

							            $item_price = 0;

							            if (isset($pddata[$k]['minPrice']) && $pddata[$k]['minPrice'] != "")
							            {
								            if ($pddata[$k]['minPrice'] > 0)
								            {
									            $item_price = $pddata[$k]['minPrice'];
								            }
							            }

							            $pdata['item_price'] = (string)$item_price;
							            $pdata['item_currency_code'] = $country_currency_code;
							            $pdata['country_code'] = $country_code;
							            $pdata['store_name'] = '';
							            $pdata['store_url'] = '';

							            if (trim($publisher_name) != "")
							            {
								            if (isset($this->publisher_advertiser_sub_id_arr[strtolower($publisher_name)]['ebay']['sub_id']) && $this->publisher_advertiser_sub_id_arr[strtolower($publisher_name)]['ebay']['sub_id'] != "")
								            {
									            $pdata['pubadvert_subid'] = $this->publisher_advertiser_sub_id_arr[strtolower($publisher_name)]['ebay']['sub_id'];
								            }
								            else
								            {
									            $pdata['pubadvert_subid'] = "";
								            }
							            }
							            else
							            {
								            $pdata['pubadvert_subid'] = "";
							            }

							            $pdata["api_info"] = "ebay commerce network product";
							            $pdata["item_type"] = "product";
							            $pdata["api"] = "ebay";
							            $pdata["api_category"] = $category_name;
							            $pdata["api_category_id"] = $category_id;

							            $result_array1[] = $pdata;
						            }
					            }
				            }
			            }

			            $pddata = array();
			            if (isset($results_from_ebay_commerce_network['GeneralSearchResponse']['categories']['category']['items']['offer']))
			            {
				            $pddata1 = $results_from_ebay_commerce_network['GeneralSearchResponse']['categories']['category']['items']['offer'];

				            if (count($pddata1) > 0)
				            {
					            //Check if only one product in list
					            if (!array_key_exists('0', $pddata1))
					            {
						            $pddata[0] = $pddata1;
					            }
					            else
					            {
						            $pddata = $results_from_ebay_commerce_network['GeneralSearchResponse']['categories']['category']['items']['offer'];
					            }

					            foreach ($pddata as $k => $v)
					            {
						            if (is_int($k))
						            {
							            $pdata = array();
							            $pdata['item_id'] = '';
							            $pdata['item_title'] = $pddata[$k]['name'];
							            $pdata['item_url'] = $pddata[$k]['offerURL'];

							            if (isset($pddata[$k]['imageList']['image'][0]))
								            $pdata['item_image'] = $pddata[$k]['imageList']['image'][0]['sourceURL'];
							            else
								            $pdata['item_image'] = $pddata[$k]['imageList']['image']['sourceURL'];

							            $pdata['category_id'] = "";
							            $pdata['category_name'] = "";

							            if (isset($pddata[$k]['shippingCost']) && $pddata[$k]['shippingCost'] == 0)
								            $pdata['is_free_shipping'] = 1;
							            else
								            $pdata['is_free_shipping'] = 0;

							            $item_price = 0;

							            if (isset($pddata[$k]['basePrice']) && $pddata[$k]['basePrice'] != "")
							            {
								            if ($pddata[$k]['basePrice'] > 0)
								            {
									            $item_price = $pddata[$k]['basePrice'];
								            }
							            }

							            $pdata['item_price'] = (string)$item_price;
							            $pdata['item_currency_code'] = $country_currency_code;
							            $pdata['country_code'] = $country_code;
							            $pdata['store_name'] = $pddata[$k]['store']['name'];
							            $pdata['store_url'] = '';

							            if (trim($publisher_name) != "")
							            {
								            if (isset($this->publisher_advertiser_sub_id_arr[strtolower($publisher_name)]['ebay']['sub_id']) && $this->publisher_advertiser_sub_id_arr[strtolower($publisher_name)]['ebay']['sub_id'] != "")
								            {
									            $pdata['pubadvert_subid'] = $this->publisher_advertiser_sub_id_arr[strtolower($publisher_name)]['ebay']['sub_id'];
								            }
								            else
								            {
									            $pdata['pubadvert_subid'] = "";
								            }
							            }
							            else
							            {
								            $pdata['pubadvert_subid'] = "";
							            }

							            $pdata["api_info"] = "ebay commerce network offer";
							            $pdata["item_type"] = "offer";
							            $pdata["api"] = "ebay";
							            $pdata["api_category"] = $category_name;
							            $pdata["api_category_id"] = $category_id;

							            $result_array1[] = $pdata;
						            }
					            }
				            }
			            }
		            }
	            }
            }
            else
            {
                $error_msg = "GeneralSearchResponse not set";
            }
        }

        if(count($result_array1)>0)
        {
			for($i=0;$i<count($result_array1);$i++)
			{
				if($result_array1[$i]['item_title']!="")
				{
					if($result_array1[$i]['item_price']>0)
					{
						$result_array2[]=$result_array1[$i];
					}
				}
			}
        }

        if(count($result_array2)>0)
        {
			for($i=0;$i<count($result_array2);$i++)
			{
				$result_array3[$result_array2[$i]['item_title']][$result_array2[$i]['item_price']][]=$result_array2[$i];
			}
        }

        foreach($result_array3 as $k=>$v)
        {
            foreach($v as $k1=>$v1)
            {
	            $result_array[] = $v1[0];
            }
        }

		return array("url"=>$final_api_url,"error_msg"=>$error_msg,"result_array"=>$result_array);
    }

    public function getDataFromConnexity($keyword,$php_domain_parser_domain_first_word,$domain_first_word,$domain_last_word,$publisher_name)
    {

        global $msg_arr;

        $placementId =0;
        if(trim($publisher_name)!="") {
	        if (isset($this->publisher_advertiser_sub_id_arr[strtolower($publisher_name)]['connexity']['sub_id']) && $this->publisher_advertiser_sub_id_arr[strtolower($publisher_name)]['connexity']['sub_id'] != "")
	            $placementId = $this->publisher_advertiser_sub_id_arr[strtolower($publisher_name)]['connexity']['sub_id'];
        }

        $msg_arr[]="Sub id = ".$placementId;

        $category_name="";
		$category_id=0;
		$error_msg="";

        $result_array=array();
        $result_array1=array();
        $result_array2=array();
        $result_array3=array();

        $connexity_obj = new ConnexityProductSearchApi();

        $connexity_obj->setResults(Config::get('connexity_products_search_api.rows_per_page'));
        if($this->records_per_page>0)
	        $connexity_obj->setResults(($this->records_per_page*5));


        $connexity_obj->setMinPrice($this->min_price);
        $connexity_obj->setMaxPrice($this->max_price);
		$results_xml_from_connexity=$connexity_obj->findProducts($keyword,$placementId);
		$final_api_url=$connexity_obj->getFinalApiUrl();

		$results_from_connexity=xml2array($results_xml_from_connexity, 1);

		if(isset($results_from_connexity['ProductResponse']['Products_attr']) && $results_from_connexity['ProductResponse']['Products_attr']['totalResults']==0)
        {
            $error_msg = "No item found";
        }
        else
        {
			$pddata=array();
			if(isset($results_from_connexity['ProductResponse']['Products']['Product']))
			{
				//Count record of return data
				$count_record = count($results_from_connexity['ProductResponse']['Products']['Product']);
				if ($count_record > 0)
				{
					//We just need only product information so directly jump to product array
					$pddata1 = $results_from_connexity['ProductResponse']['Products']['Product'];

					//Check if only one product in list
					if (!array_key_exists('0', $pddata1)) {
						$pddata[0] = $pddata1;
					}
					else
					{
						//If more than one product in list
						$pddata = $results_from_connexity['ProductResponse']['Products']['Product'];
					}

					foreach ($pddata as $k => $v)
					{
						if (is_int($k))
						{
							$pdata = array();
							$pdata['item_id'] = '';
							$pdata['item_title'] = $pddata[$k]['title'];
							$pdata['item_url'] = $pddata[$k]['url'];
							$pdata['item_image'] = $pddata[$k]['Images']['Image'][1];
							$pdata['category_id'] = '';
							$pdata['category_name'] = '';
							$pdata['is_free_shipping'] = 0;

							$item_price=0;
							if(isset($pddata[$k]['PriceSet']))
							{
								if(isset($pddata[$k]['PriceSet']['minPrice_attr']))
								{
									if (isset($pddata[$k]['PriceSet']['minPrice_attr']['integral']) && $pddata[$k]['PriceSet']['minPrice_attr']['integral'] != "") {
										if ($pddata[$k]['PriceSet']['minPrice_attr']['integral'] > 0) {
											$item_price = ($pddata[$k]['PriceSet']['minPrice_attr']['integral'] / 100);
										}
									}
								}
							}

							$pdata['item_price'] = (string) $item_price;
							$pdata['item_currency_code'] = 'USD';
							$pdata['country_code']='US';
							$pdata['store_name'] = '';
							$pdata['store_url'] = '';
							$pdata['pubadvert_subid']=$placementId;
							$pdata["api_info"] = "connexity  product";
							$pdata["api"] = "connexity";
							$pdata["api_category"] = $category_name;
							$pdata["api_category_id"] = $category_id;

							$result_array1[] = $pdata;
						}
					}
				}
			}

			$pddata=array();
			if(isset($results_from_connexity['ProductResponse']['Products']['Offer']))
			{
				//Count record of return data
				$count_record = count($results_from_connexity['ProductResponse']['Products']['Offer']);
				if ($count_record > 0)
				{
					//We just need only product information so directly jump to product array
					$pddata1 = $results_from_connexity['ProductResponse']['Products']['Offer'];

					//Check if only one product in list
					if (!array_key_exists('0', $pddata1)) {
						$pddata[0] = $pddata1;
					}
					else
					{
						//If more than one product in list
						$pddata = $results_from_connexity['ProductResponse']['Products']['Offer'];
					}

					//Check all data in the list
					foreach ($pddata as $k => $v)
					{
						if (is_int($k))
						{
							$pdata = array();

							$pdata['item_id'] = '';
							$pdata['item_title'] = $pddata[$k]['title'];
							$pdata['item_url'] = $pddata[$k]['url'];
							$pdata['item_image'] = $pddata[$k]['Images']['Image'][1];
							$pdata['category_id'] = '';
							$pdata['category_name'] = '';
							if ($pddata[$k]['shipAmount_attr']['integral'] == 0) {
								$pdata['is_free_shipping'] = 1;
							}
							else {
								$pdata['is_free_shipping'] = 0;
							}

							$item_price=0;
							if(isset($pddata[$k]['price_attr']))
							{
								if(isset($pddata[$k]['price_attr']['integral']) && $pddata[$k]['price_attr']['integral']!="")
								{
									if($pddata[$k]['price_attr']['integral']>0)
									{
										$item_price=($pddata[$k]['price_attr']['integral'] / 100);
									}
								}
							}

							$pdata['item_price'] = (string) $item_price;
							$pdata['item_currency_code'] = 'USD';
							$pdata['country_code']='US';

							if(isset($pddata[$k]['merchantName']))
								$pdata['store_name'] = $pddata[$k]['merchantName'];
							else
								$pdata['store_name'] = '';

							$pdata['store_url'] = '';
							$pdata['pubadvert_subid']=$placementId;
							$pdata["api_info"] = "connexity  offer";
							$pdata["api"] = "connexity";
							$pdata["api_category"] = $category_name;
							$pdata["api_category_id"] = $category_id;

							$result_array1[] = $pdata;
						}
					}
				}
			}
        }

        if(count($result_array1)>0)
        {
			for($i=0;$i<count($result_array1);$i++)
			{
				if($result_array1[$i]['item_title']!="")
				{
					if($result_array1[$i]['item_price']>0)
					{
						$result_array2[]=$result_array1[$i];
					}
				}
			}
        }

        if(count($result_array2)>0)
        {
			for($i=0;$i<count($result_array2);$i++)
			{
				$result_array3[$result_array2[$i]['item_title']][$result_array2[$i]['item_price']][]=$result_array2[$i];
			}

        }

        foreach($result_array3 as $k=>$v)
        {
            foreach($v as $k1=>$v1)
            {
	            $result_array[] = $v1[0];
            }
        }

		return array("url"=>$final_api_url,"error_msg"=>$error_msg,"result_array"=>$result_array);
    }

    public function getDataFromZoom($keyword,$php_domain_parser_domain_first_word,$domain_first_word,$domain_last_word,$publisher_name)
    {
	    global $msg_arr;

	    $subid=0;
        if(trim($publisher_name)!="")
		{
			if(isset($this->publisher_advertiser_sub_id_arr[strtolower($publisher_name)]['zoom']['sub_id']) && $this->publisher_advertiser_sub_id_arr[strtolower($publisher_name)]['zoom']['sub_id']!="")
				$subid = $this->publisher_advertiser_sub_id_arr[strtolower($publisher_name)]['zoom']['sub_id'];
		}

		$msg_arr[]="Sub id = ".$subid;

		$category_name="";
		$category_id=0;

		$result_array=array();
		$result_array1=array();
        $result_array2=array();
        $result_array3=array();

		$zoom_obj = new ZoomProductSearchApi();
		$zoom_obj->setRowsPerPage(Config::get('zoom_products_search_api.rows_per_page'));

		if($this->records_per_page>0)
			$zoom_obj->setRowsPerPage($this->records_per_page*5);

		$country_code=$zoom_obj->getCountryCode();

		$results_xml_from_zoom=$zoom_obj->findProducts($keyword,$subid);

		$final_api_url=$zoom_obj->getFinalApiUrl();

		$error_msg="";

		$results_from_zoom=xml2array($results_xml_from_zoom, 1);

		if (isset($results_from_zoom['result']))
		{
			if (isset($results_from_zoom['result']['offers']['offer']))
			{
				$count_record = count($results_from_zoom['result']['offers']['offer']);

				if ($count_record > 0)
				{
					$pddata_array = array();
					$pddata1 = $results_from_zoom['result']['offers']['offer'];
					if (!array_key_exists('0', $pddata1)) {
						$pddata[0] = $pddata1;
					}
					else {
						$pddata = $results_from_zoom['result']['offers']['offer'];
					}
					foreach ($pddata as $key => $value)
					{
						$pddata_array['item_id'] = '';
						if (isset($pddata[$key]['id']))
							$pddata_array['item_id'] = $pddata[$key]['id'];


						$pddata_array['item_title'] = '';
						if (isset($pddata[$key]['name']))
							$pddata_array['item_title'] = $pddata[$key]['name'];


						$pddata_array['item_url'] = '';
						if (isset($pddata[$key]['url']))
							$pddata_array['item_url'] = $pddata[$key]['url'];

						$item_price=0;

						if(isset($pddata[$key]['price']) && $pddata[$key]['price']!="")
						{
							if($pddata[$key]['price']>0)
							{
								$item_price= $pddata[$key]['price'];
							}
						}

						$pddata_array['item_price'] = (string) $item_price;

						$pddata_array['item_currency_code'] = '';
						if (isset($pddata[$key]['currency']))
							$pddata_array['item_currency_code'] = $pddata[$key]['currency'];

						$pddata_array['country_code'] = (string)$country_code;

						$pddata_array['item_image'] = '';
						if (isset($pddata[$key]['imageURL']))
							$pddata_array['item_image'] = $pddata[$key]['imageURL'];

						$pddata_array['category_id'] = '';
						if (isset($pddata[$key]['categoryId']))
							$pddata_array['category_id'] = $pddata[$key]['categoryId'];

						$pddata_array['category_name'] = '';
						$pddata_array['is_free_shipping'] = 0;

						$pddata_array['store_name'] = '';
						if (isset($pddata[$key]['storeName']))
							$pddata_array['store_name'] = $pddata[$key]['storeName'];


						$pddata_array['store_url'] = '';
						$pddata_array['pubadvert_subid'] =$subid;
						$pddata_array["api_info"] = "zoom offer";
						$pddata_array["item_type"] = "offer";
			            $pddata_array["api"] = "zoom";
			            $pddata_array["api_category"] = $category_name;
						$pddata_array["api_category_id"] = $category_id;

						$result_array1[] = $pddata_array;
					}
				}
			}

			if (isset($results_from_zoom['result']['products']['product']))
			{
				$product_data = count($results_from_zoom['result']['products']['product']);

				for ($i = 0; $i < $product_data; $i++)
				{
					if (isset($results_from_zoom['result']['products']['product'][$i]['offers']['offer']))
					{
						$count_record = count($results_from_zoom['result']['products']['product'][$i]['offers']['offer']);

						if ($count_record > 0)
						{
							$pddata_array = array();

							$pddata1 = $results_from_zoom['result']['products']['product'][$i]['offers']['offer'];

							if (!array_key_exists('0', $pddata1))
							{
								$pddata[0] = $pddata1;
							}
							else
							{
								$pddata = $results_from_zoom['result']['products']['product'][$i]['offers']['offer'];
							}
							foreach ($pddata as $key => $value)
							{
								$pddata_array['item_id'] = '';
								if (isset($pddata[$key]['id']))
									$pddata_array['item_id'] = $pddata[$key]['id'];

								$pddata_array['item_title'] = '';
								if (isset($pddata[$key]['name']))
									$pddata_array['item_title'] = $pddata[$key]['name'];

								$pddata_array['item_url'] = '';
								if (isset($pddata[$key]['url']))
									$pddata_array['item_url'] = $pddata[$key]['url'];

								$item_price = 0;

								if (isset($pddata[$key]['price']) && $pddata[$key]['price'] != "")
								{
									if ($pddata[$key]['price'] > 0)
									{
										$item_price = $pddata[$key]['price'];
									}
								}

								$pddata_array['item_price'] = (string)$item_price;

								$pddata_array['item_currency_code'] = '';
								if (isset($pddata[$key]['currency']))
									$pddata_array['item_currency_code'] = $pddata[$key]['currency'];

								$pddata_array['country_code'] = (string)$country_code;

								$pddata_array['item_image'] = '';
								if (isset($pddata[$key]['imageURL']))
									$pddata_array['item_image'] = $pddata[$key]['imageURL'];

								$pddata_array['category_id'] = '';
								if (isset($pddata[$key]['categoryId']))
									$pddata_array['category_id'] = $pddata[$key]['categoryId'];

								$pddata_array['category_name'] = '';
								$pddata_array['is_free_shipping'] = 0;

								$pddata_array['store_name'] = '';
								if (isset($pddata[$key]['storeName']))
									$pddata_array['store_name'] = $pddata[$key]['storeName'];

								$pddata_array['store_url'] = '';
								$pddata_array['pubadvert_subid'] = $subid;
								$pddata_array["api_info"] = "zoom product offer";
								$pddata_array["item_type"] = "productoffer";
								$pddata_array["api"] = "zoom";
								$pddata_array["api_category"] = $category_name;
								$pddata_array["api_category_id"] = $category_id;

								$result_array1[] = $pddata_array;
							}
						}
					}
				}
			}
		}

		if(count($result_array1)>0)
        {
			for($i=0;$i<count($result_array1);$i++)
			{
				if($result_array1[$i]['item_title']!="")
				{
					if($result_array1[$i]['item_price']>0)
					{
						$result_array2[]=$result_array1[$i];
					}
				}
			}
        }

        if(count($result_array2)>0)
        {
			for($i=0;$i<count($result_array2);$i++)
			{
				$result_array3[$result_array2[$i]['item_title']][$result_array2[$i]['item_price']][]=$result_array2[$i];
			}
        }

        foreach($result_array3 as $k=>$v)
        {
            foreach($v as $k1=>$v1)
            {
	            $result_array[] = $v1[0];
            }
        }

		return array("url"=>$final_api_url,"error_msg"=>$error_msg,"result_array"=>$result_array);

    }

    public function getOffersDataFromTwenga($keyword,$php_domain_parser_domain_first_word,$domain_first_word,$domain_last_word,$publisher_name)
    {
		global $msg_arr;

        $subid=0;
        if(trim($publisher_name)!="")
		{
			if(isset($this->publisher_advertiser_sub_id_arr[strtolower($publisher_name)]['twenga']['sub_id']) && $this->publisher_advertiser_sub_id_arr[strtolower($publisher_name)]['twenga']['sub_id']!="")
				$subid = $this->publisher_advertiser_sub_id_arr[strtolower($publisher_name)]['twenga']['sub_id'];
		}

		$msg_arr[]="Sub id = ".$subid;

		$category_name="";
		$category_id=0;

	    $result_array=array();
	    $result_array1=array();
        $result_array2=array();
        $result_array3=array();

		$twenga_obj = new TwengaOfferSearchApi();
		$twenga_obj->setRowsPerPage(Config::get('twenga_products_search_api.rows_per_page'));

		if($this->records_per_page>0)
			$twenga_obj->setRowsPerPage($this->records_per_page*5);

		$twenga_obj->setCountryCodeAndUrlDomainByDomainLastWord($domain_last_word);

		$country_currency_code=$twenga_obj->getCountryCurrencyCode();

		$country_code=$twenga_obj->getCountryCode();
		$twenga_obj->setMinPrice($this->min_price);
        $twenga_obj->setMaxPrice($this->max_price);

		$results_xml_from_twenga=$twenga_obj->findProducts($keyword,$subid);

		$final_api_url=$twenga_obj->getFinalApiUrl();
		$error_msg="";

		$results_from_twenga=xml2array($results_xml_from_twenga, 1);

		$this->logTwengaIfApiError($results_from_twenga,$final_api_url);

	    if (isset($results_from_twenga['root']))
	    {
		    if (isset($results_from_twenga['root']['code']) && $results_from_twenga['root']['code'] != 200)
		    {
			    $error_msg = $results_from_twenga['root']['infos']['error_message'];
		    }
		    else
		    {

			    if (isset($results_from_twenga['root']['tw_objects']['tw_object']['results']['result']))
			    {
				    //Count record of return data
				    $count_record = $results_from_twenga['root']['tw_objects']['tw_object']['nb_results'];

				    if ($count_record > 0)
				    {
					    //We just need only product information so directly jump to product array
					    $pddata1 = $results_from_twenga['root']['tw_objects']['tw_object']['results']['result'];

					    //Check if only one product in list
					    if (!array_key_exists('0', $pddata1))
					    {
						    $pddata[0] = $pddata1;
					    }
					    else//If more than one product in list
					    {
						    $pddata = $results_from_twenga['root']['tw_objects']['tw_object']['results']['result'];
					    }
					    //Check all data in the list
					    foreach ($pddata as $k => $v)
					    {
						    $pdata = array();
						    $pdata['item_id'] = $pddata[$k]['item_id'];
						    $pdata['item_title'] = $pddata[$k]['name'];
						    $pdata['item_url'] = $pddata[$k]['click_url'];
						    $pdata['item_image'] = $pddata[$k]['image']['small']['url'];

						    if (isset($pddata[$k]['category']))
						    {
							    $pdata['category_id'] = $pddata[$k]['category']['category_id'];
							    $pdata['category_name'] = $pddata[$k]['category']['name'];
						    }
						    else
						    {
							    $pdata['category_id'] = '';
							    $pdata['category_name'] = '';
						    }

						    if ($pddata[$k]['shipping_cost_raw'] == 0)
							    $pdata['is_free_shipping'] = 1;
						    else
							    $pdata['is_free_shipping'] = 0;

						    $item_price = 0;

						    if (isset($pddata[$k]['price_raw']) && $pddata[$k]['price_raw'] != "")
						    {
							    if ($pddata[$k]['price_raw'] > 0)
							    {
								    $item_price = $pddata[$k]['price_raw'];
							    }
						    }

						    $pdata['item_price'] = (string)$item_price;;

						    $pdata['item_currency_code'] = (string)$country_currency_code;
						    $pdata['country_code'] = (string)$country_code;

						    if (isset($pddata[$k]['merchant']))
							    $pdata['store_name'] = $pddata[$k]['merchant']['name'];
						    else
							    $pdata['store_name'] = '';

						    $pdata['store_url'] = '';
						    $pdata['pubadvert_subid'] = $subid;
						    $pdata["api_info"] = "twenga " . $domain_last_word;
						    $pdata["api"] = "twenga";
						    $pdata["api_category"] = $category_name;
						    $pdata["api_category_id"] = $category_id;

						    $result_array1[] = $pdata;
					    }
				    }
				    else
				    {
					    $error_msg = "No item found";
				    }
			    }
			    else
			    {
				    $error_msg = "root->tw_objects->tw_object->results->result not set";
			    }
		    }
	    }
	    else
	    {
		    $error_msg = "Root not found";
	    }

		if(count($result_array1)>0)
        {
			for($i=0;$i<count($result_array1);$i++)
			{
				if($result_array1[$i]['item_title']!="")
				{
					if($result_array1[$i]['item_price']>0)
					{
						$result_array2[]=$result_array1[$i];
					}
				}
			}
        }

        if(count($result_array2)>0)
        {
			for($i=0;$i<count($result_array2);$i++)
			{
				$result_array3[$result_array2[$i]['item_title']][$result_array2[$i]['item_price']][]=$result_array2[$i];
			}

        }

        foreach($result_array3 as $k=>$v)
        {
            foreach($v as $k1=>$v1)
            {
	            $result_array[] = $v1[0];
            }
        }

        return array("url"=>$final_api_url,"error_msg"=>$error_msg,"result_array"=>$result_array);
    }

    public function getDataFromKelkoo($keyword,$php_domain_parser_domain_first_word,$domain_first_word,$domain_last_word,$publisher_name='',$dealsource='',$widget='')
    {
        global $msg_arr;

        $custom1='';
        if(trim($publisher_name)!="")
		{
			if(isset($this->publisher_advertiser_sub_id_arr[strtolower($publisher_name)]['kelkoo']['sub_id']) && $this->publisher_advertiser_sub_id_arr[strtolower($publisher_name)]['kelkoo']['sub_id']!="")
			{
				$custom1 = $this->publisher_advertiser_sub_id_arr[strtolower($publisher_name)]['kelkoo']['sub_id'];
			}
		}

		$msg_arr[]="Sub id = ".$custom1;

		$category_name="";
		$category_id=0;

		$custom2=$dealsource;
		$custom3=$widget;

        $result_array=array();
        $result_array1=array();
        $result_array2=array();
        $result_array3=array();

        $error_msg="";
        $final_api_url="";

		$kelkoo_obj = new KelkooProductSearchApi();
		$kelkoo_obj->setResults(Config::get('kelkoo_products_search_api.rows_per_page'));

		if($this->records_per_page>0)
			$kelkoo_obj->setResults($this->records_per_page);


        if($kelkoo_obj->isDomainLastWordInArray($domain_last_word))
        {
            $kelkoo_obj->setCredentialsByDomainLastWord($domain_last_word);
			$country_code=$kelkoo_obj->getCountryCode();

			$kelkoo_obj->setMinPrice($this->min_price);
            $kelkoo_obj->setMaxPrice($this->max_price);
	        $results_xml_from_kelkoo = $kelkoo_obj->findProducts($keyword,$custom1,$custom2,$custom3);
	        $final_api_url=$kelkoo_obj->getFinalApiUrl();
	        $results_from_kelkoo = xml2array($results_xml_from_kelkoo, 1);

	        //Error check of data
	        if (isset($results_from_kelkoo['Error']))
	        {
	            $error_msg==$results_from_kelkoo['Error']['Message'];
	        }
	        else
	        {
		        if (isset($results_from_kelkoo['ProductSearch']))
		        {
			        $count_record = $results_from_kelkoo['ProductSearch']['Products_attr']['totalResultsReturned'];
			        if ($count_record > 0)
			        {
				        //We just need only product information so directly jump to product array
				        $pddata1 = $results_from_kelkoo['ProductSearch']['Products']['Product'];

				        //check if only one product in list
				        if (array_key_exists('Offer', $pddata1))
				        {
					        $pddata[0]['Offer'] = $pddata1['Offer'];
				        }
				        else
				        {
					        //if more than one product in list
					        $pddata = $results_from_kelkoo['ProductSearch']['Products']['Product'];
				        }

				        //Check all data in the list
				        foreach ($pddata as $k => $v)
				        {
					        $pdata = array();
					        if (isset($pddata[$k]['Offer']['Catalog_attr']['productId']))
					        {
						        $pdata['item_id'] = $pddata[$k]['Offer']['Catalog_attr']['productId'];
					        }
					        else
					        {
						        $pdata['item_id'] = '';
					        }

					        $pdata['item_title'] = $pddata[$k]['Offer']['Title'];
					        $pdata['item_url'] = $pddata[$k]['Offer']['Url'];
					        $pdata['item_image'] = $pddata[$k]['Offer']['Images']['Image']['Url'];
					        $pdata['category_id'] = $pddata[$k]['Offer']['Category_attr']['id'];
					        $pdata['category_name'] = $pddata[$k]['Offer']['Category']['Name'];

					        if (isset($pddata[$k]['Offer']['Price']['DeliveryCost']) && $pddata[$k]['Offer']['Price']['DeliveryCost'] == 0)
					        {
						        $pdata['is_free_shipping'] = 1;
					        }
					        else
					        {
						        $pdata['is_free_shipping'] = 0;
					        }

					        $item_price = 0;
					        if (isset($pddata[$k]['Offer']))
					        {
						        if (isset($pddata[$k]['Offer']['Price']))
						        {

							        if (isset($pddata[$k]['Offer']['Price']['TotalPrice']) && $pddata[$k]['Offer']['Price']['TotalPrice'] != "")
							        {
								        if ($pddata[$k]['Offer']['Price']['TotalPrice'] > 0)
								        {
									        $item_price = $pddata[$k]['Offer']['Price']['TotalPrice'];
								        }
							        }
						        }
					        }

					        $pdata['item_price'] = (string)$item_price;
					        $pdata['item_currency_code'] = $pddata[$k]['Offer']['Price_attr']['currency'];
					        $pdata['country_code'] = $country_code;
					        $pdata['store_name'] = $pddata[$k]['Offer']['Merchant']['Name'];
					        $pdata['store_url'] = '';
					        $pdata['pubadvert_subid'] = $custom1;
					        $pdata["api_info"] = "kelkoo " . $domain_last_word;
					        $pdata["api"] = "kelkoo";
					        $pdata["api_category"] = $category_name;
					        $pdata["api_category_id"] = $category_id;

					        $result_array1[] = $pdata;
				        }
			        }
			        else
			        {
				        $error_msg = "No item found";
			        }
		        }
		        else
		        {
			        $error_msg = "ProductSearch tag not set";
		        }
	        }
        }
        else
        {
            $error_msg="Domain not in kelkoo api";
        }

        if(count($result_array1)>0)
        {
			for($i=0;$i<count($result_array1);$i++)
			{
				if($result_array1[$i]['item_title']!="")
				{
					if($result_array1[$i]['item_price']>0)
					{
						$result_array2[]=$result_array1[$i];
					}
				}
			}
        }

        if(count($result_array2)>0)
        {
			for($i=0;$i<count($result_array2);$i++)
			{
				$result_array3[$result_array2[$i]['item_title']][$result_array2[$i]['item_price']][]=$result_array2[$i];
			}
        }

        foreach($result_array3 as $k=>$v)
        {
            foreach($v as $k1=>$v1)
            {
	            $result_array[] = $v1[0];
            }
        }

        return array("url"=>$final_api_url,"error_msg"=>$error_msg,"result_array"=>$result_array);
    }

    public function getDataFromDealsPricer($keyword,$php_domain_parser_domain_first_word,$domain_first_word,$domain_last_word,$publisher_name='',$dealsource='',$widget='')
    {
        global $msg_arr;

		$subid="";
		if(trim($publisher_name)!="")
		{
			if(isset($this->publisher_advertiser_sub_id_arr[strtolower($publisher_name)]['dealspricer']['sub_id']) && $this->publisher_advertiser_sub_id_arr[strtolower($publisher_name)]['dealspricer']['sub_id']!="")
				$subid = $this->publisher_advertiser_sub_id_arr[strtolower($publisher_name)]['dealspricer']['sub_id'];
		}

		$msg_arr[]="Sub id = ".$subid;

		$category_name="";
		$category_id=0;

		$result_array=array();
		$result_array1=array();
        $result_array2=array();
        $result_array3=array();

		$dealsPricerProductsAPI= new DealsPricerProductsAPI();
		$dealsPricerProductsAPI->setCountryCodeByDomainLastWord($domain_last_word);
		$dealsPricerProductsAPI->setRecordsPerPage(Config::get('dealspricer_products_search_api.rows_per_page'));

		if($this->records_per_page>0)
			$dealsPricerProductsAPI->setRecordsPerPage(($this->records_per_page));

		$country_currency_code=$dealsPricerProductsAPI->getCountryCurrencyCode();

		$pub=$dealsPricerProductsAPI->getPub();


		$pub_subid=$pub;
		$pub_subid.='-'.$subid;

		$country_code=$dealsPricerProductsAPI->getCountryCode();

		$response_json=$dealsPricerProductsAPI->findProducts($keyword);

		$final_api_url=$dealsPricerProductsAPI->getFinalApiUrl();
		$error_msg="";

		$results_from_dealspricer=json_decode($response_json,true);

		if(isset($results_from_dealspricer['response']))
		{
			if(isset($results_from_dealspricer['response']['docs']))
			{
				if(count($results_from_dealspricer['response']['docs'])>0)
				{
					for ($i = 0; $i < count($results_from_dealspricer['response']['docs']); $i++)
					{
						$item = $results_from_dealspricer['response']['docs'][$i];

						$pdata = array();

						$merchant_name=(string) $item['merchantName'];

						$item_url=(string) $item['deeplink'];
						$item_url=str_replace('YOURTRACKINGCODE',$pub_subid,$item_url);
						$is_free_shipping = 0;

						// Creating result array for JSON
						$pdata["item_id"] = (string)$item['productId'];
						$pdata["item_title"] = (string)$item['title'];
						$pdata["item_url"] = $item_url;

						$item_price=0;

						if(isset($item['price']) && $item['price']!="")
						{
							if($item['price']>0)
							{
								$item_price=$item['price'];
							}
						}

						$pdata["item_price"] = (string)$item_price;
						$pdata["item_currency_code"] = (string)$country_currency_code;
						$pdata["country_code"] = (string)$country_code;

						if(isset($item['imageUrl']))
							$pdata["item_image"] = (string)$item['imageUrl'];
						else
							$pdata["item_image"] = "";

						$pdata["category_id"] = "0";

						if(isset($item['category']))
							$pdata["category_name"] = (string)$item['category'];
						else
							$pdata["category_name"] ="";

						$pdata["is_free_shipping"] = $is_free_shipping;
						$pdata["store_name"] = (string)$item['merchantName'];
						$pdata["store_url"] = "";

						$pdata['pubadvert_subid'] =$subid;
						$pdata["api_info"] = "dealspricer";
						$pdata["item_type"] = "product";
						$pdata["api"] = "dealspricer";
						$pdata["api_category"] = $category_name;
						$pdata["api_category_id"] = $category_id;

						$result_array1[] = $pdata;

					}
				}
				else
				{
					$error_msg="No item found";
				}
			}
			else
			{
				$error_msg="Response doc not set";
			}
		}
		else
		{
			$error_msg="Response not set";
		}



		if(count($result_array1)>0)
        {
			for($i=0;$i<count($result_array1);$i++)
			{
				if($result_array1[$i]['item_title']!="")
				{
					if($result_array1[$i]['item_price']>0)
					{
						$result_array2[]=$result_array1[$i];
					}
				}
			}
        }

        if(count($result_array2)>0)
        {
			for($i=0;$i<count($result_array2);$i++)
			{
				$result_array3[$result_array2[$i]['item_title']][$result_array2[$i]['item_price']][]=$result_array2[$i];
			}
        }

        foreach($result_array3 as $k=>$v)
        {
            foreach($v as $k1=>$v1)
            {
	            $result_array[] = $v1[0];
            }
        }

		return array("url"=>$final_api_url,"error_msg"=>$error_msg,"result_array"=>$result_array);
    }

    public function getPublisherAdvertisersSubIdArray($publisher_id)
    {
        $publishers_arr=[];

        $advertisers = Advertiser::where('is_delete', '=', 0)->with(array('publishers' => function($query) use($publisher_id)
        {
            $query->where('is_delete', '=', 0);
            $query->where('publishers.id', '=', $publisher_id);

        }))->get()->toArray();

        foreach($advertisers as $advertiser) {

            for($i=0;$i<count($advertiser['publishers']);$i++)
            {
                $publisher=$advertiser['publishers'][$i];
                $publishers_arr[strtolower($publisher['name'])][strtolower($advertiser['name'])]['sub_id'] = $publisher['pivot']['publisher_id1'];
            }
        }

        return $publishers_arr;
    }

    public function logTwengaIfApiError($results_from_twenga,$final_api_url)
    {
        $error_msg="";
        $api_params_json="";
        $params_arr=array();

        if (isset($results_from_twenga['root']))
	    {
		    if (isset($results_from_twenga['root']['code']) && $results_from_twenga['root']['code'] != 200)
		    {
			    $error_msg = $results_from_twenga['root']['infos']['error_message'];
		    }
		    else
		    {
			    if (isset($results_from_twenga['root']['tw_objects']['tw_object']['results']['result']))
			    {
				    if ($results_from_twenga['root']['tw_objects']['tw_object']['nb_results'] == 0)
						$error_msg = "No item found";
			    }
			    else
			    {
				    $error_msg = "Not getting result from twenga";
			    }
		    }
	    }
	    else
	    {
		    $error_msg = "Not getting result from twenga";
	    }

	    if(trim($error_msg)!="")
	    {
		    $parse_url_arr = parse_url($final_api_url);
		    //$params_arr=parse_str($parse_url_arr['query'],$urlparams);
		    $parse_url_exp = explode('&', $parse_url_arr['query']);

		    if (count($parse_url_exp) > 0)
		    {
			    for ($i = 0; $i < count($parse_url_exp); $i++)
			    {
				    $parse_url_key_value_exp = explode('=', $parse_url_exp[$i]);
				    if (count($parse_url_key_value_exp) == 2)
					    $params_arr[$parse_url_key_value_exp[0]] = $parse_url_key_value_exp[1];
			    }
		    }

		    if (count($params_arr) > 0)
			    $api_params_json = json_encode($params_arr);

		    $log_str = date("Y-m-d") . '|' . $api_params_json . '|' . $error_msg . "\n";

			$log_file = storage_path('logs').'/twenga-api-error.log';

	        $view_log = new Logger('View Logs');
			$view_log->pushHandler(new StreamHandler($log_file, Logger::ERROR));
			$view_log->addError($log_str);

	    }
    }


    // Input: A decimal number as a String.
	// Output: The equivalent hexadecimal number as a String.
	function dec2hex($number)
	{
	    $hexvalues = array('0','1','2','3','4','5','6','7','8','9','A','B','C','D','E','F');
	    $hexval = '';
	    while($number != '0')
	    {
	        $hexval = $hexvalues[bcmod($number,'16')].$hexval;
	        $number = bcdiv($number,'16',0);
	    }
	    return $hexval;
	}

	// Input: A hexadecimal number as a String.
	// Output: The equivalent decimal number as a String.
	function hex2dec($number)
	{
	    $decvalues = array(
	                '0' => '0', '1' => '1', '2' => '2',
	                '3' => '3', '4' => '4', '5' => '5',
	                '6' => '6', '7' => '7', '8' => '8',
	                '9' => '9', 'A' => '10', 'B' => '11',
	                'C' => '12', 'D' => '13', 'E' => '14',
	                'F' => '15'
		);
	    $decval = '0';
	    $number = strrev($number);
	    for($i = 0; $i < strlen($number); $i++)
	    {
	        $decval = bcadd(bcmul(bcpow('16',$i,0),$decvalues[$number{$i}]), $decval);
	    }
	    return $decval;
	}


	function format_json($json, $html = false, $tabspaces = null)
    {
        $tabcount = 0;
        $result = '';
        $inquote = false;
        $ignorenext = false;

        if ($html) {
            $tab = str_repeat("&nbsp;", ($tabspaces == null ? 4 : $tabspaces));
            $newline = "<br/>";
        } else {
            $tab = ($tabspaces == null ? "\t" : str_repeat(" ", $tabspaces));
            $newline = "\n";
        }

        for($i = 0; $i < strlen($json); $i++) {
            $char = $json[$i];

            if ($ignorenext) {
                $result .= $char;
                $ignorenext = false;
            } else {
                switch($char) {
                    case ':':
                        $result .= $char . (!$inquote ? " " : "");
                        break;
                    case '{':
                        if (!$inquote) {
                            $tabcount++;
                            $result .= $char . $newline . str_repeat($tab, $tabcount);
                        }
                        else {
                            $result .= $char;
                        }
                        break;
                    case '}':
                        if (!$inquote) {
                            $tabcount--;
                            $result = trim($result) . $newline . str_repeat($tab, $tabcount) . $char;
                        }
                        else {
                            $result .= $char;
                        }
                        break;
                    case ',':
                        if (!$inquote) {
                            $result .= $char . $newline . str_repeat($tab, $tabcount);
                        }
                        else {
                            $result .= $char;
                        }
                        break;
                    case '"':
                        $inquote = !$inquote;
                        $result .= $char;
                        break;
                    case '\\':
                        if ($inquote) $ignorenext = true;
                        $result .= $char;
                        break;
                    default:
                        $result .= $char;
                }
            }
        }

        return $result;
    }

	public function getInputSchema()
    {
		$arr=array(
			"title"=>"Search Input Schema",
			"request_type"=>"get",
			"url_parameters"=>array(
				"widget"=>array(
					"type"=>"string",
					"description"=>"Widget name",
					"required"=>"true"
                ),
				"kw"=>array(
					"type"=>"string",
					"description"=>"Keyword to search",
					"required"=>"true"
                ),
				"domain"=>array(
					"type"=>"string",
					"description"=>"Domain name",
					"required"=>"true"
                ),
				"user"=>array(
					"type"=>"integer",
					"description"=>"User unique code",
					"example"=>"1834848185",
					"required"=>"false"
                ),
				"resolution"=>array(
					"type"=>"string",
					"description"=>"Device resolution",
					"example"=>"1366x768",
					"required"=>"false"
                ),
				"language"=>array(
					"type"=>"string",
					"description"=>"Browser language",
					"example"=>"en-US",
					"required"=>"false"
                ),
				"time"=>array(
					"type"=>"string",
					"description"=>"Local time",
					"example"=>"1:13:51 AM",
					"required"=>"false"
                ),
				"dealsource"=>array(
					"type"=>"string",
					"description"=>"Partner Id",
					"required"=>"false"
                ),
				"asin"=>array(
					"type"=>"string",
					"description"=>"asin",
					"required"=>"false"
                ),
				"callback"=>array(
					"type"=>"string",
					"description"=>"callback function for jsonp",
					"required"=>"false"
                ),
			),
		);

		$json=json_encode($arr);

		return $this->format_json($json,true);

    }

    public function getOutputSchema()
    {
		$arr=array(
			"title"=>"Search Output Schema",
			"type"=>"json",
			"properties"=>array(
				"success"=>array(
					"type"=>"boolean",
					"description"=>" if request successful values 0 or 1 ",
                ),
				"message"=>array(
					"type"=>"string",
					"description"=>"",
                ),
                "errorcode"=>array(
					"type"=>"string",
					"description"=>"If any error, contains error code.",
                ),
                "errordescription"=>array(
					"type"=>"string",
					"description"=>"If any error, contains error description.",
                ),
				"info"=>array(
					"type"=>"object",
					"description"=>"if success, contains items info",
					"properties"=>array(
						"items"=>array(
							"type"=>"array",
							"properties"=>array(
								"item_id"=>array(
									"type"=>"string",
									"description"=>"Product id",
								),
								"item_title"=>array(
									"type"=>"string",
									"description"=>"Product name",
								),
								"item_url"=>array(
									"type"=>"string",
									"description"=>"Product detail page url",
								),
								"item_price"=>array(
									"type"=>"string",
									"description"=>"Product price",
								),
								"item_image"=>array(
									"type"=>"string",
									"description"=>"Product image url",
								),
								"category_id"=>array(
									"type"=>"string",
									"description"=>"Category id",
								),
								"category_name"=>array(
									"type"=>"string",
									"description"=>"Category name",
								),
								"is_free_shipping"=>array(
									"type"=>"boolean",
									"description"=>"Is shipping free value 0 or 1",
								),
								"store_name"=>array(
									"type"=>"string",
									"description"=>"Store name",
								),
								"store_url"=>array(
									"type"=>"string",
									"description"=>"Store page url",
								),
							)
						)
					)
                ),
			)
		);
		$json=json_encode($arr);

		return $this->format_json($json,true);
    }
}
