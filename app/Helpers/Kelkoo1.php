<?php namespace App\Helpers;

use App\Advertiser;
use App\ClickAdvertiserInternal;
use App\VisionApiReport;
use App\VisionApiReportAll;
use SoapBox\Formatter\Formatter;
use DateTime;

class Kelkoo1 {

	private $trackings_arr="";
    private $db_publishers_arr=array();

    private $todays_date='';
	private $yesterdays_date='';

	private $internal_clicks_arr=array();
	private $internal_clicks_arr1=array();
	private $external_clicks_arr=array();


	public function init()
    {

        VisionApi::echo_printr("Start Kelkoo init at ".date("j F, Y, g:i a"));

        $this->todays_date = date("Y-m-d");


		$dt = new DateTime($this->todays_date);
		VisionApi::echo_printr($dt->format("Y-m-d H:i:s"), "date");
		$dt->modify('-1 day');
		VisionApi::echo_printr($dt->format("Y-m-d H:i:s"), "date");

		$this->yesterdays_date = $dt->format("Y-m-d");

		$month_start_date=date("Y-m-01",strtotime($this->yesterdays_date));


        $xmldata=$this->getXmlData($month_start_date,$this->yesterdays_date);



        $formatter = Formatter::make($xmldata, Formatter::XML);


        $trackings_arr1 = $formatter->toArray();
        $trackings_arr = $trackings_arr1['tracking'];

        for($i=0;$i<count($trackings_arr);$i++) {

            $result=$trackings_arr[$i];


			$result_date=$result['day'];

			if($result_date!="") {
				$result_date = str_replace('/', '-', $result_date);
				$result_date_exp = explode('-', $result_date);

				if (count($result_date_exp) == 3) {
					$yy = $result_date_exp[0];
					$mm = $result_date_exp[1];
					$dd = $result_date_exp[2];

					if (strlen($yy) == 2) $yy = '20' . $yy;

					$result_date = $dd . '-' . $mm . '-' . $yy;

				}

				$dt1 = strtotime($result_date);
				$date = date("Y-m-d 00:00:00", $dt1);
				$date1= date("Y-m-d", $dt1);
			}
			else
            {
                $date="";
                $date1="";
            }
            $trackings_arr[$i]['datetime_cmp']=$date;
            $trackings_arr[$i]['day1']=$date1;


        }



		usort($trackings_arr, array("App\Helpers\Kelkoo1", "date_sort_asc"));


        $this->processData($trackings_arr);

        $this->clicks_report_db_insert();

        VisionApi::echo_printr($this->internal_clicks_arr,"internal_clicks_arr");
        VisionApi::echo_printr($this->internal_clicks_arr1,"internal_clicks_arr1");
        VisionApi::echo_printr($this->external_clicks_arr,"external_clicks_arr");

        if(count($this->internal_clicks_arr1)>0)
        {
            VisionApi::echo_printr("internal_clicks_arr1 count greater than 0");

			foreach($this->internal_clicks_arr1 as $k=>$v)
			{

				$clicks_advertiser_internal=ClickAdvertiserInternal::where('api', '=', 'Kelkoo')->where('date', '=', $k)->first();

				$clicks_advertiser_internal_input['api']='Kelkoo';
				$clicks_advertiser_internal_input['date']=$k;
				$clicks_advertiser_internal_input['internal_clicks']=$this->internal_clicks_arr1[$k];
				$clicks_advertiser_internal_input['api_clicks']=$this->external_clicks_arr[$k];

				if($clicks_advertiser_internal === null) {

					$clicks_advertiser_internal=ClickAdvertiserInternal::create($clicks_advertiser_internal_input);
				}

			}
        }
        else
        {
            VisionApi::echo_printr("internal_clicks_arr count is 0");
        }

    }

    public function clicks_report_db_insert()
	{
		$month_start_date=date("Y-m-01",strtotime($this->yesterdays_date));

		$month_end_date=date("Y-m-t",strtotime($month_start_date));


		VisionApi::echo_printr($month_start_date, "month_start_date");
		VisionApi::echo_printr($month_end_date, "month_end_date");

		$visionapi_report_all = \DB::table('visionapi_report_all as a')
			->selectRaw('a.*')
			->where('advertiser_name', '=', 'Kelkoo')
			->where('date', '<=', $month_end_date)
			->where('date', '>=', $month_start_date)
			->orderBy('date','asc')
			->get();

		if (count($visionapi_report_all) > 0) {

			$visionapi_report_all_arr = json_decode(json_encode($visionapi_report_all), true);



			for ($i = 0; $i < count($visionapi_report_all_arr); $i++)
			{
				$input['date'] = $visionapi_report_all_arr[$i]['date'];
                $input['dl_source'] = $visionapi_report_all_arr[$i]['dl_source'];
                $input['sub_dl_source'] = $visionapi_report_all_arr[$i]['sub_dl_source'];
                $input['widget'] = $visionapi_report_all_arr[$i]['widget'];
                $input['country'] = $visionapi_report_all_arr[$i]['country'];
                $input['searches'] = $visionapi_report_all_arr[$i]['searches'];
                $input['clicks'] = $visionapi_report_all_arr[$i]['clicks'];
                $input['estimated_revenue'] = $visionapi_report_all_arr[$i]['estimated_revenue'];
                $input['advertiser_name'] = $visionapi_report_all_arr[$i]['advertiser_name'];
                $input['is_estimated'] = $visionapi_report_all_arr[$i]['is_estimated'];

                VisionApiReport::create($input);
			}
		}
	}

    public function processData($trackings_arr)
    {
	    $api = 'Kelkoo';

	    $this->db_publishers_arr = $this->getPublishersInfo();



		$visionapi_report_all = \DB::table('visionapi_report_all as a')
			->selectRaw('a.*')
			->where('advertiser_name', '=', $api)
			->groupBy('date')
			->orderBy('date')
			->first();

		if ($visionapi_report_all === null) {

			VisionApi::echo_printr("visionapi_report_all is null");

			$start=date("Y-m-01",strtotime($this->yesterdays_date));
			VisionApi::echo_printr($start,"start");


			$this->processData1($api,$trackings_arr,$start);

		}
		else
		{
			VisionApi::echo_printr("visionapi_report_all is not null");

			$visionapi_report_all = \DB::table('visionapi_report_all as a')
				->selectRaw('a.*')
				->where('advertiser_name', '=', $api)
				->where('is_estimated', '=', 0)
				->groupBy('date')
				->orderBy('date','desc')
				->get();


			if (count($visionapi_report_all) > 0) {

				$visionapi_report_all_arr = json_decode(json_encode($visionapi_report_all), true);
				VisionApi::echo_printr($visionapi_report_all_arr,"visionapi_report_all_arr");

				$start=date('Y-m-d', strtotime($visionapi_report_all_arr[0]['date'] . " +1 day"));
				VisionApi::echo_printr($start,"start");

				$this->processData1($api,$trackings_arr,$start);

			}
		}

    }

    public function processData1($api,$trackings_arr,$start)
	{
		for ($i = 0; $i < count($trackings_arr); $i++)
		{
			$result = $trackings_arr[$i];
			VisionApi::echo_printr($result, "trackings_arr i");

			VisionApi::echo_printr(gettype($result['Custom1']),"Custom1");
            VisionApi::echo_printr(gettype($result['Custom2']),"Custom2");
            VisionApi::echo_printr(gettype($result['Custom3']),"Custom3");

            if(gettype($result['Custom1'])=='array')
                $custom1="";
            else
                $custom1=$result['Custom1'];

            if(gettype($result['Custom2'])=='array')
                $custom2="";
            else
                $custom2=$result['Custom2'];

            if(gettype($result['Custom3'])=='array')
                $custom3="";
            else
                $custom3=$result['Custom3'];

            VisionApi::echo_printr($custom1,"custom1");
            VisionApi::echo_printr($custom2,"custom2");
            VisionApi::echo_printr($custom3,"custom3");

			$result_date_db=$result['day1'];
			$result_geo=strtoupper($result['country']);
            $subid = $custom1;

			$publisher_name='';
            if(isset($this->db_publishers_arr['kelkoo'][strtolower($subid)]['name']))
            {
                if(trim($this->db_publishers_arr['kelkoo'][strtolower($subid)]['name'])!="")
                {
                    $publisher_name=trim($this->db_publishers_arr['kelkoo'][strtolower($subid)]['name']);
                }
                else
                {
                    $publisher_name=$subid;
                }
            }
            else
            {
                $publisher_name=$subid;
            }

            if(strtotime($start)<=strtotime($result_date_db))
            {
                VisionApi::echo_printr("condition 1");

                $this->clicks_report_db_insert_in_all($result_geo,$api,$subid,$publisher_name,$custom2,$custom3,$result_date_db,$result);

                //die();
            }
            else
            {
                VisionApi::echo_printr("condition 2");
            }
		}
	}

	public function clicks_report_db_insert_in_all($geo,$api,$subid,$publisher_name,$sub_dl_source,$widget,$result_date_db,$result)
	{

		if(!isset($this->internal_clicks_arr[$result_date_db]))
		{
			$this->getInternalClicksByDate($result_date_db,$api);
		}


		$result_clicks=$result['numberOfLeads'];

		if(!isset($this->external_clicks_arr[$result_date_db]))
			$this->external_clicks_arr[$result_date_db]=0;

		$this->external_clicks_arr[$result_date_db]+=$result_clicks;

		$searches = "";
		$publisher_share=37.5;
		$country_code=$geo;
		$dl_source=$publisher_name;
        $sub_dl_source=urldecode($sub_dl_source);;
        $widget= urldecode($widget);;
        $date=$result_date_db;
        $estimated_revenue=$result['revenue'];
        $clicks=$result_clicks;

		VisionApi::echo_printr($estimated_revenue,"estimated_revenue");
		VisionApi::echo_printr($result_clicks,"result_clicks");
		$result_cost_per_click=0;
		if($result_clicks>0)
            $result_cost_per_click=$estimated_revenue/$result_clicks;
        VisionApi::echo_printr($result_cost_per_click,"result_cost_per_click");

        if (isset($this->db_publishers_arr['kelkoo'][strtolower($subid)]['pivot']['share'])) {
            if ($this->db_publishers_arr['kelkoo'][strtolower($subid)]['pivot']['share'] > 0) {
                $publisher_share = $this->db_publishers_arr['kelkoo'][strtolower($subid)]['pivot']['share'];
            }
        }

		VisionApi::echo_printr($estimated_revenue,"estimated_revenue");

        if ($publisher_share > 0)
			$estimated_revenue = ($estimated_revenue) * ($publisher_share / 100);

		VisionApi::echo_printr($estimated_revenue,"Final estimated_revenue");

		$input['date'] = $date;
        $input['dl_source'] = $dl_source;
        $input['sub_dl_source'] = $sub_dl_source;
        $input['widget'] = $widget;
        $input['country'] = $country_code;
        $input['searches'] = $searches;
        $input['clicks'] = $clicks;
        $input['estimated_revenue'] = $estimated_revenue;
        $input['advertiser_name'] = $api;
        $input['total_clicks'] = $result_clicks;
        $input['cost_per_click'] = $result_cost_per_click;
        $input['is_estimated'] = 0;

        VisionApiReportAll::create($input);


	}

	public  function getInternalClicksByDate($result_date_db,$api)
	{
		$query1 = \DB::table('search_clicks as a')
			->selectRaw('SUM(clicks) AS total_clicks1,a.date,a.api,a.country_code,a.dl_source,a.sub_dl_source,a.widget')
			->where('date', '=', $result_date_db)
			->where('api', '=', strtolower($api))
			->orderBy('date');
			//->lists('total','total_clicks','id');

		$query2 = \DB::table(\DB::raw( "( {$query1->toSql()} ) as totalS" ))
	   ->mergeBindings($query1)
	   ->selectRaw('SUM(totalS.total_clicks1) AS total_clicks,totalS.api,totalS.country_code,totalS.date');

	    $db_reports2=$query2->first();

	    if($db_reports2->total_clicks==null)
		{
			VisionApi::echo_printr("total_clicks is null");
			$total_clicks_db=0;
		}
		else
		{
			VisionApi::echo_printr("total_clicks is not null");
			$total_clicks_db=$db_reports2->total_clicks;
		}

		$this->internal_clicks_arr1[$result_date_db]=$total_clicks_db;
	}

	public function getPublishersInfo()
    {

        $publishers_arr=[];

        $advertisers = Advertiser::where('name', '=', 'Kelkoo')->where('is_delete', '=', 0)->with(array('publishers' => function($query)
        {
            $query->where('is_delete', '=', 0);

        }))->get()->toArray();

        foreach($advertisers as $advertiser) {

            $publishers_arr[strtolower($advertiser['name'])]=array();

            for($i=0;$i<count($advertiser['publishers']);$i++)
            {
                $publisher=$advertiser['publishers'][$i];

                if(isset($publisher['pivot']['publisher_id1']) && $publisher['pivot']['publisher_id1']!="")
                {
                    $publishers_arr[strtolower($advertiser['name'])][strtolower($publisher['pivot']['publisher_id1'])] = $publisher;
                }

            }
        }

        return $publishers_arr;
    }

    function date_sort_asc($a, $b)
    {
        $t1 = strtotime($a['datetime_cmp']);
        $t2 = strtotime($b['datetime_cmp']);
        return $t1 - $t2;
    }

    public function getXmlData($start_date,$end_date)
    {
        // set HTTP header
        $headers = array(
            //'Content-Type: application/json',
            'Content-Type: application/xml'
        );



        $fields = array(
            'pageType' => 'custom',
            'username' => 'ileviathan-Kelkoo',
            'password' => 'jq944s95',
	        'currency' => 'USD',
	        'from'     => $start_date,
	        'to'       => $end_date,
        );




        $url = 'https://partner.kelkoo.com/statsSelectionService.xml?' . http_build_query($fields);

        VisionApi::echo_printr($url,"URL");

        // Open connection
        $ch = curl_init();

        // Set the url, number of GET vars, GET data
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        // Execute request
        $result = curl_exec($ch);

        // Close connection
        curl_close($ch);

        return $result;
    }

}