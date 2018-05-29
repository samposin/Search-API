<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\Classes\HelperFunctions;
use App\WhitelistDomain;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;

class WhitelistDomainController extends Controller
{

	private $domain_json='[]';

    public function __construct()
	{
		//echo "date =  ".date("j F, Y, g:i a")."<br>";
		date_default_timezone_set('America/Los_Angeles');
		//echo "date =  ".date("j F, Y, g:i a")."<br>";
	}

	public function check(Request $request)
	{

		$jsonDir=storage_path('json');

        if (!File::exists($jsonDir))
        {
            File::makeDirectory($jsonDir, 0775, true, true);
        }

        $path = $jsonDir . "/whitelist_domains.json";
        // ie: /var/www/laravel/app/storage/json/filename.json

	    if (File::exists($path)) {
			$this->domain_json = File::get($path);
	    }

		$input = $request->all();

		$domain_name="";
        if($request->has('domain') && $request->get('domain')!=null)
			$domain_name = $input['domain'];

		if ($request->has('domain') && $request->get('domain') != null)
		{
			$domain_arr = HelperFunctions::separateWordByLastDot($domain_name);

			if (count($domain_arr) > 0)
			{
				//$domain = WhitelistDomain::where('domain', '=', $domain_name)->first();

				$domain_info_arr=json_decode($this->domain_json);

				//if ($domain !== null)
				if(in_array($domain_name,$domain_info_arr))
				{
					// displaying resultant successful JSON
					$response_json = Response::json([
						'success' => true, 'message' => "Domain is in Whitelist", 'info' => array()
					], 200);
				}
				else {
					// displaying resultant JSON with error
					$response_json = Response::json([
						'success' => false, 'errorcode' => 'error4', 'errordescription' => "Domain not in Whitelist",
					], 200);
				}
			}
			else {
				// displaying resultant JSON with error
				$response_json = Response::json([
					'success' => false, 'errorcode' => 'error2', 'errordescription' => "Domain must have at least one dot",
				], 200);
			}
		}
		else {
			// displaying resultant JSON with error
			$response_json = Response::json([
				'success' => false, 'errorcode' => 'error2', 'errordescription' => "Domain missing",
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
}
