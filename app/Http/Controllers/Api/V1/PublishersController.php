<?php

namespace App\Http\Controllers\Api\V1;

use App\ConfiguratorGeneratedJs;
use App\Http\Controllers\Api\V1\Classes\HelperFunctions;
use App\Http\Controllers\Api\V1\Helpers\UserAgent;
use App\Publisher;
use App\SearchClick;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Response;
use SoapBox\Formatter\Formatter;

define('PIWIK_INCLUDE_PATH', public_path().'/analytics/piwik');
define('PIWIK_USER_PATH', public_path().'/analytics/piwik');
define('PIWIK_ENABLE_DISPATCH', false);
define('PIWIK_ENABLE_ERROR_HANDLER', false);
define('PIWIK_ENABLE_SESSION_START', false);


class PublishersController extends Controller
{
	 public function __construct()
	 {

		date_default_timezone_set('America/Los_Angeles');

	 }

	public function get_all_publishers(Request $request)
	{

		$result_array=[];
		$response_json='';
		$input = $request->all();
		$publishers = Publisher::where('is_delete','=',0)->orderBy('id')->get();
		$i=0;
		foreach($publishers as $publisher)
		{
	        $result_array['publishers'][$i]['pub_id']=$publisher->id;
	        $result_array['publishers'][$i]['partner_name']=$publisher->name;
	        $i++;
        }

        $response_json = Response::json([
			'success' => true, 'message' => "", 'info' => $result_array
		], 200);

		// if callback is given set it
        if($request->has('callback') && $request->get('callback')!=null)
        {
            return $response_json->setCallback($input['callback']);
        }
        else
        {
            return $response_json;
        }
	}

	/**
	 * @param Request $request
	 *
	 * @return string
	 */
	public function add_publisher(Request $request)
    {

		$result_array=[];
        $response_json='';
        $input = $request->all();

        $partner_name="";
        if($request->has('partner_name') && $request->get('partner_name')!=null)
			$partner_name = strtoupper($input['partner_name']);

		$product_name="";
        if($request->has('product_name') && $request->get('product_name')!=null)
			$product_name = $input['product_name'];

		$sub_id="";
        if($request->has('sub_id') && $request->get('sub_id')!=null)
			$sub_id = $input['sub_id'];

		if($request->has('partner_name') && $request->get('partner_name')!=null)
		{

			$publisher = Publisher::where('name', '=', $partner_name)->where('is_delete','=',0)->first();

			if ($publisher === null) {
			   // publisher doesn't exist

				$publisher_input['name']=$partner_name;
		        $publisher_input['is_delete']=0;

		        // insert into db
		        $publisher=Publisher::create($publisher_input);
			}

			$publisher_id=$publisher->id;

			$configuratorGeneratedJs = ConfiguratorGeneratedJs::where('publisher_id', '=', $publisher_id)->where('is_delete','=',0)->first();

			if ($configuratorGeneratedJs === null)
			{
				$publisher_configurator_generated_js_no=1;

				if (trim($product_name) == "") {
					if (trim($sub_id) == "") {
						$piwik_website_name = $partner_name . ' - ' . $publisher_configurator_generated_js_no;
					}
					else {
						$piwik_website_name = $partner_name . ' - ' . $sub_id;
					}
				}
				else {
					if (trim($sub_id) == "") {
						$piwik_website_name = $partner_name . ' - ' . $product_name;
					}
					else {
						$piwik_website_name = $partner_name . ' - ' . $product_name . ' - ' . $sub_id;
					}
				}
				$configurator_unique_id=md5($piwik_website_name.' - '.$publisher_configurator_generated_js_no);

			}
			else
			{
				$configuratorGeneratedJsMaxNo=ConfiguratorGeneratedJs::select(DB::raw('max(publisher_configurator_generated_js_no) as max_no'))
				->where('publisher_id','=', $publisher_id)
				->where('is_delete','=',0)
				->first()->toArray();

				$publisher_configurator_generated_js_no=$configuratorGeneratedJsMaxNo['max_no']+1;
				if (trim($product_name) == "") {
					if (trim($sub_id) == "") {
						$piwik_website_name = $partner_name . ' - ' . $publisher_configurator_generated_js_no;
					}
					else {
						$piwik_website_name = $partner_name . ' - ' . $sub_id;
					}
				}
				else {
					if (trim($sub_id) == "") {
						$piwik_website_name = $partner_name . ' - ' . $product_name;
					}
					else {
						$piwik_website_name = $partner_name . ' - ' . $product_name . ' - ' . $sub_id;
					}
				}
				$configurator_unique_id=md5($piwik_website_name.' - '.$publisher_configurator_generated_js_no);
			}

			//$piwik_website_id=$this->add_website_in_piwik($piwik_website_name);
			$piwik_website_id=0;

			$configurator_generated_js_input['publisher_id']=$publisher_id;
			$configurator_generated_js_input['configurator_unique_id']=$configurator_unique_id;
			$configurator_generated_js_input['piwik_website_id']=$piwik_website_id;
			$configurator_generated_js_input['piwik_website_name']=$piwik_website_name;
			$configurator_generated_js_input['partner_name']=$partner_name;
			$configurator_generated_js_input['product_name']=$product_name;
			$configurator_generated_js_input['product_sub_id']=$sub_id;
			$configurator_generated_js_input['publisher_configurator_generated_js_no']=$publisher_configurator_generated_js_no;
	        $configurator_generated_js_input['is_delete']=0;

	        // insert into db
	        $publisher=ConfiguratorGeneratedJs::create($configurator_generated_js_input);

	        $result_array['configurator_unique_id']=$configurator_unique_id;

	        // displaying resultant successful JSON
			$response_json = Response::json([
				'success' => true, 'message' => "", 'info' => $result_array
			], 200);
		}
		else
		{
			// displaying resultant JSON with error
			$response_json = Response::json([
				'success' => false,  'errordescription' => "Partner name missing",
			], 200);

		}

		// if callback is given set it
        if($request->has('callback') && $request->get('callback')!=null)
        {
            return $response_json->setCallback($input['callback']);
        }
        else
        {
            return $response_json;
        }

    }

    public function add_website_in_piwik($website_name)
    {
		require_once PIWIK_INCLUDE_PATH . "/index.php";
		require_once PIWIK_INCLUDE_PATH . "/core/API/Request.php";

		$environment = new \Piwik\Application\Environment(null);
		$environment->init();

		\Piwik\FrontController::getInstance()->init();

		// This inits the API Request with the specified parameters
		$request = new \Piwik\API\Request('
			module=API
			&method=SitesManager.addSite
			&siteName='.$website_name.'
			&urls='.$website_name.'
			&token_auth=1bd31f40e4b4b0dc84a0fb659baa3c1f
		');

		// Calls the API and fetch XML data back
		$result_xml = $request->process();

		$formatter = Formatter::make($result_xml, Formatter::XML);
		$result_array = $formatter->toArray();

		return $result_array[0];

    }

    public function handle_click(Request $request)
    {
		$input = $request->all();

		$http_user_agent=$_SERVER['HTTP_USER_AGENT'];

		/* This function gets browser information by using http_user_agent */
		//$browser=UserAgent::getBrowser($http_user_agent);
		$browser=HelperFunctions::getBrowser($http_user_agent);

		$user_ip = "";
        if (!empty($_SERVER["HTTP_X_FORWARDED_FOR"])) {
            $user_ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
        } else {
            $user_ip = $_SERVER["REMOTE_ADDR"];
        }

        if(trim($user_ip)!="") {
	        $user_ip_exp = explode(',', $user_ip);
	        $user_ip = trim($user_ip_exp[count($user_ip_exp) - 1]);
        }

        $api="";
		if($request->has('api') && $request->get('api')!=null)
			$api = $input['api'];

		$url="";
		if($request->has('url') && $request->get('url')!=null)
			$url = $input['url'];

		$widget="";
		if($request->has('widget') && $request->get('widget')!=null)
			$widget = $input['widget'];

		$domain="";
		if($request->has('domain') && $request->get('domain')!=null)
			$domain = $input['domain'];

		$subid="";
		if($request->has('subid') && $request->get('subid')!=null)
			$subid = $input['subid'];

		$searchid="";
		if($request->has('searchid') && $request->get('searchid')!=null)
			$searchid = $input['searchid'];

		$configurator_unique_id="";
		if($request->has('cuid') && $request->get('cuid')!=null)
			$configurator_unique_id = $input['cuid'];

		$country_code="";
		if($request->has('co_code') && $request->get('co_code')!=null)
			$country_code = strtoupper($input['co_code']);

		$jsver="";
        if($request->has('jsver') && $request->get('jsver')!=null)
			$jsver = $input['jsver'];

		$keyword="";
        if($request->has('kw') && $request->get('kw')!=null)
			$keyword = $input['kw'];

		$cat="";
        if($request->has('cat') && $request->get('cat')!=null)
			$cat = $input['cat'];

		$api_cat="";
        if($request->has('api_cat') && $request->get('api_cat')!=null)
			$api_cat = $input['api_cat'];

		$api_cat_id="";
        if($request->has('api_cat_id') && $request->get('api_cat_id')!=null)
			$api_cat_id = $input['api_cat_id'];

		//$publisher_id=0;
		$publisher_name="";

		if(trim($configurator_unique_id)!="") {

			$configuratorGeneratedJs = ConfiguratorGeneratedJs::with('publisher')->where('configurator_unique_id', '=', $configurator_unique_id)->first();

			if ($configuratorGeneratedJs === null) {
			}
			else {
				$configuratorGeneratedJsArray=$configuratorGeneratedJs->toArray();
				//$publisher_id=$configuratorGeneratedJsArray['publisher_id'];
				$publisher_name=$configuratorGeneratedJsArray['publisher']['name'];
			}
		}

		$date=date("Y-m-d");
		$search_click_input['api']=$api;
		$search_click_input['search_id']=$searchid;
		$search_click_input['dl_source']=$publisher_name;
		$search_click_input['sub_dl_source']=$subid;
		$search_click_input['widget']=$widget;
		$search_click_input['domain']=$domain;
		$search_click_input['country_code']=$country_code;
		$search_click_input['keyword']=$keyword;
		$search_click_input['date']=$date;
		$search_click_input['jsver']=$jsver;
		$search_click_input['category']=$cat;
		$search_click_input['api_category']=$api_cat;
		$search_click_input['api_category_id']=$api_cat_id;
		$search_click_input['ip']=$user_ip;
		$search_click_input['http_user_agent']=$http_user_agent;
		$search_click_input['browser']=$browser;

		// insert into db
	    $search_click=SearchClick::create($search_click_input);

		return redirect($url);

    }
}
