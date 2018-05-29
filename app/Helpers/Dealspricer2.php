<?php namespace App\Helpers;

use Akeneo\Component\SpreadsheetParser\SpreadsheetParser;
use App\Advertiser;
use App\ClickAdvertiserInternal;
use App\Country;
use App\Publisher;
use App\VisionApiReport;
use App\VisionApiReportAll;
use DateTime;
use GrahamCampbell\Dropbox\Facades\Dropbox;
use Maatwebsite\Excel\Facades\Excel;

class Dealspricer2 {

	private $dropbox_latest_file_info="";
	private $excel_file_dropbox_folder_name="";
	private $excel_file_server_upload_path="";
	private $excel_file_server_upload_file_name="";
	private $excel_file_server_upload_file_path="";
	private $db_publishers_arr=array();

	private $todays_date='';
	private $yesterdays_date='';
	private $internal_clicks_arr=array();
	private $internal_clicks_arr1=array();
	private $external_clicks_arr=array();
	private $api_display_name='Dealspricer';
	private $api_name='dealspricer';

	public function init()
	{
		VisionApi::echo_printr("Start Dealspricer init at ".date("j F, Y, g:i a"));

		$this->todays_date = date("Y-m-d");
		//$this->todays_date = '2016-06-01';

		$dt = new DateTime($this->todays_date);
		VisionApi::echo_printr($dt->format("Y-m-d H:i:s"), "date");
		$dt->modify('-1 day');
		VisionApi::echo_printr($dt->format("Y-m-d H:i:s"), "date");

		$this->yesterdays_date = $dt->format("Y-m-d");

		// Need to change

        $this->excel_file_dropbox_folder_name='/iLeviathan-Reporting/Dealspricer';

		$this->excel_file_server_upload_path=base_path() . '/public/files/excels/uploads/Dealspricer/';

		$this->dropbox_latest_file_info=$this->getLatestFileInfoFromDropbox();

        VisionApi::echo_printr($this->dropbox_latest_file_info,"Latest file info");



        if($this->downloadFileFromDropbox())
        {
			$this->processFile();
        }

        VisionApi::echo_printr($this->internal_clicks_arr,"internal_clicks_arr");
        VisionApi::echo_printr($this->internal_clicks_arr1,"internal_clicks_arr1");
        VisionApi::echo_printr($this->external_clicks_arr,"external_clicks_arr");

        $this->insertExternalInternalClicks();

		$this->getLastDatePublishersCountriesForEstimatedReport();

        $this->clicks_report_db_insert();


	}

	public function getLastDatePublishersCountriesForEstimatedReport()
	{
		$api=$this->api_display_name;




		$subQuery = \DB::table('visionapi_report_all as b')
	    ->selectRaw( 'date' )
	    ->where( 'advertiser_name','=', $api )
	    //->groupBy( 'commodity_id' );
	    ->orderBy('date','DESC')
	    ->limit(1)->offset(0);




		$visionapi_report_all = \DB::table('visionapi_report_all as a')
			->selectRaw('a.*')
			->where('advertiser_name', '=', $api)
			->join(
		        \DB::raw( sprintf( '(%s) as b', $subQuery->toSql() ) ),  // wraps the sub-query
		        'a.date', '=', 'b.date'
		    )
		    ->mergeBindings( $subQuery ) // this does the trick
			->groupBy('date')
			->groupBy('dl_source')
			->groupBy('country')
			->orderBy('date','DESC')
			->get();

		$arr=array();

		if (count($visionapi_report_all) > 0) {

			$visionapi_report_all_arr = json_decode(json_encode($visionapi_report_all), true);

			for ($i = 0; $i < count($visionapi_report_all_arr); $i++)
			{
				VisionApi::echo_printr($visionapi_report_all_arr[$i],"visionapi_report_all_arr i");

				$arr[$visionapi_report_all_arr[$i]['dl_source']][$visionapi_report_all_arr[$i]['date']][$visionapi_report_all_arr[$i]['country']]=$visionapi_report_all_arr[$i];
			}
		}

		VisionApi::echo_printr($arr,"arr");

		foreach($arr as $k=>$v)
        {
            $publisher_name=$k;

            $publisher = Publisher::where('name','=',$publisher_name)->where('is_delete', '=', 0)
            ->with(array('advertisers' => function($query) use($api)
	        {
	            $query->where('is_delete', '=', 0)
	            ->where('name', '=', $api)
	            ->limit(1);

	        }))
            ->first();

			$publisher_arr=array();
			$subid=$publisher_name;
            if ($publisher !== null)
			{
				$publisher_arr = json_decode(json_encode($publisher), true);

				if(isset($publisher_arr['advertisers']))
				{
					if(isset($publisher_arr['advertisers'][0]))
					{
						$subid=$publisher_arr['advertisers'][0]['pivot']['publisher_id1'];
					}
				}
			}
			VisionApi::echo_printr($subid,"subid");



	        foreach($v as $k1=>$v1)
	        {
	            $date=$k1;
		        foreach($v1 as $k2=>$v2)
		        {
		            $country_code=$k2;
			        $this->getEstimatedReport($date,$publisher_name,$subid,$country_code);
		        }
	        }

        }
	}

	public function getEstimatedReport($date,$publisher_name,$subid,$country_code)
	{
		$api=$this->api_display_name;

		VisionApi::echo_printr($date,"date");
		VisionApi::echo_printr($publisher_name,"publisher_name");
		VisionApi::echo_printr($country_code,"country_code");

		$start=date('Y-m-d', strtotime($date . " +1 day"));

		VisionApi::echo_printr($start,"Estimated start date");
		VisionApi::echo_printr($this->yesterdays_date,"Estimated yesterday date");

		$tmp_start=$start;

		if(strtotime($tmp_start)<=strtotime($this->yesterdays_date)) {
			while (strtotime($tmp_start) <= strtotime($this->yesterdays_date)) {

				VisionApi::echo_printr($tmp_start,"tmp_start");

				$previous_day_obj = new DateTime($tmp_start);
				$previous_day_obj->modify('-1 day');

				$previous_seven_days_obj = new DateTime($previous_day_obj->format("Y-m-d"));
				$previous_seven_days_obj->modify('-7 days');

				VisionApi::echo_printr($previous_day_obj->format("Y-m-d"), "previous_day_obj");
				VisionApi::echo_printr($previous_seven_days_obj->format("Y-m-d"), "previous_seven_days_obj");

				$visionapi_report_all_result1 = \DB::table('visionapi_report_all as a')
					->selectRaw('a.*')
					->where('date', '<=', $previous_day_obj->format("Y-m-d"))
					->where('date', '>=', $previous_seven_days_obj->format("Y-m-d"))
					->where('advertiser_name', '=', $api)
					->where('dl_source', '=', $publisher_name)
					->where('country', '=', strtoupper($country_code))
					->groupBy('date')
					->orderBy('date', 'desc')
					->get();

				$total_clicks_arr = array();
				$cost_per_click_arr = array();

				if (count($visionapi_report_all_result1) > 0)
				{
					$visionapi_report_all_result1_arr = json_decode(json_encode($visionapi_report_all_result1), true);


					for ($i = 0; $i < count($visionapi_report_all_result1_arr); $i++)
					{
						$total_clicks_arr[] = $visionapi_report_all_result1_arr[$i]['total_clicks'];
						$cost_per_click_arr[] = $visionapi_report_all_result1_arr[$i]['cost_per_click'];
					}
				}

				$total_clicks_avg = 0;
				if (count($total_clicks_arr) > 0) $total_clicks_avg = array_sum($total_clicks_arr) / count($total_clicks_arr);

				$cost_per_click_avg = 0;
				if (count($cost_per_click_arr) > 0) $cost_per_click_avg = array_sum($cost_per_click_arr) / count($cost_per_click_arr);

				VisionApi::echo_printr($total_clicks_arr, "total_clicks_arr");
				VisionApi::echo_printr($cost_per_click_arr, "cost_per_click_arr");
				VisionApi::echo_printr($total_clicks_avg, "total_clicks_avg");
				VisionApi::echo_printr($cost_per_click_avg, "cost_per_click_avg");

				$final_total_clicks=round($total_clicks_avg*(.9));
				VisionApi::echo_printr($final_total_clicks, "final_total_clicks");

				$this->clicks_report_db_insert_in_all($country_code,$api,$subid,$publisher_name,$tmp_start,$final_total_clicks,$cost_per_click_avg,1);

				$tmp_start = date('Y-m-d', strtotime($tmp_start . " +1 day"));
			}
		}

	}

	public function clicks_report_db_insert_in_all($geo,$api,$subid,$publisher_name,$result_date_db,$result_clicks,$result_cost_per_click,$is_estimated=0)
	{

		if(!isset($this->internal_clicks_arr[$result_date_db]))
		{
			$this->getInternalClicksByDate($result_date_db,$api);
		}

		$query1 = \DB::table('search_clicks as a')
				->selectRaw('SUM(clicks) AS total_clicks1,a.date,a.api,a.country_code,a.dl_source,a.sub_dl_source,a.widget')
				->where('date', '=', $result_date_db)
				->where('api', '=', strtolower($api))
				->where('country_code', '=', strtoupper($geo))
				->where('dl_source', '=', $publisher_name)
				->groupBy('api')
				->groupBy('dl_source')
				->groupBy('sub_dl_source')
				->groupBy('widget')
				->groupBy('country_code')
				->groupBy('date')
				->orderBy('date');
				//->lists('total','total_clicks','id');

		$query2 = \DB::table(\DB::raw( "( {$query1->toSql()} ) as totalS" ))
	   ->mergeBindings($query1)
	   ->selectRaw('SUM(totalS.total_clicks1) AS total_clicks,totalS.api,totalS.country_code,totalS.date');

	    $db_reports2=$query2->first();
	    $db_reports1=$query1->get();

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

		if(!isset($this->internal_clicks_arr[$result_date_db]))
			$this->internal_clicks_arr[$result_date_db]=0;

		if(!isset($this->external_clicks_arr[$result_date_db]))
			$this->external_clicks_arr[$result_date_db]=0;

		if($is_estimated==0) {
			$this->internal_clicks_arr[$result_date_db] += $total_clicks_db;
			$this->external_clicks_arr[$result_date_db] += $result_clicks;
		}


		if($total_clicks_db!=0) {
			$click_ratio = $result_clicks / $total_clicks_db;

			VisionApi::echo_printr($result_clicks, "result_clicks");
			VisionApi::echo_printr($total_clicks_db, "total_clicks_db");
			VisionApi::echo_printr($click_ratio, "click_ratio");
			VisionApi::echo_printr(count($db_reports1), "count db_reports1");

			if (count($db_reports1) > 0) {
				$total_records = count($db_reports1);

				$searches = "";

				$db_reports = json_decode(json_encode($db_reports1), true);

				VisionApi::echo_printr(count($db_reports), "count db_reports");

				for ($i = 0; $i < count($db_reports); $i++) {
					VisionApi::echo_printr($db_reports[$i], "db report i");

					$clicks=($db_reports[$i]['total_clicks1']*$click_ratio);
                    $estimated_revenue=$db_reports[$i]['total_clicks1']*$click_ratio*$result_cost_per_click;


					$publisher_share=0;
					$country_code=$db_reports[$i]['country_code'];
					$dl_source=$db_reports[$i]['dl_source'];
                    $sub_dl_source=$db_reports[$i]['sub_dl_source'];
                    $widget=$db_reports[$i]['widget'];
                    $date=$db_reports[$i]['date'];



					VisionApi::echo_printr($estimated_revenue,"estimated_revenue");
					if($publisher_share>0)
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
	                $input['advertiser_name'] = 'Dealspricer';
	                $input['total_clicks'] = $result_clicks;
	                $input['cost_per_click'] = $result_cost_per_click;
	                $input['is_estimated'] = $is_estimated;

	                VisionApiReportAll::create($input);

				}
			}
		}
	}



	public function processFile()
	{
		$api = 'Dealspricer';

		VisionApi::echo_printr($this->excel_file_server_upload_file_path,"excel_file_server_upload_file_path");

        if(file_exists($this->excel_file_server_upload_file_path))
        {
	        $this->db_publishers_arr = $this->getPublishersInfo();
            $countries_arr=$this->getCountriesAssociativeArray();



            $csv_data=$this->getCsvData();


            for($i=0;$i<count($csv_data);$i++) {

                $result=$csv_data[$i];


				$result_date=$result['Date'];

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
                $csv_data[$i]['datetime_cmp']=$date;
                $csv_data[$i]['Date1']=$date1;


            }

            usort($csv_data, array("App\Helpers\Dealspricer2", "date_sort_asc"));


            $exchange_rates=VisionApi::$currency_exchange_rates;



            $visionapi_report_all = \DB::table('visionapi_report_all as a')
				->selectRaw('a.*')
				->where('advertiser_name', '=', $api)
				->groupBy('date')
				->orderBy('date')
				->first();

			if ($visionapi_report_all === null)
			{
				VisionApi::echo_printr("visionapi_report_all is null");

				$start=date("Y-m-01",strtotime($this->yesterdays_date));
				VisionApi::echo_printr($start,"start");


				$this->processFile1($api,$csv_data,$countries_arr,$start);
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


					$this->processFile1($api,$csv_data,$countries_arr,$start);

				}
			}
        }
	}

	public function processFile1($api,$csv_data,$countries_arr,$start)
	{
		//get estimated reports date array
		$visionapi_report_all_estimated_date_arr=$this->getEstimatedReportDatesArray($api);

		VisionApi::echo_printr($visionapi_report_all_estimated_date_arr,"visionapi_report_all_estimated_date_arr");

		for($i=0;$i<count($csv_data);$i++) {

			$result = $csv_data[$i];


			VisionApi::echo_printr($result, "result");

			$result_date_db=$result['Date1'];
			$result_geo=$result['Country'];
			$payout_str = $result['Vaa share in total Revenue'];
			$symbol = $result['Local Currency Symbol'];
			$subid_str= $result['Sub ID'];

			VisionApi::echo_printr($result_date_db,"result_date_db");
			VisionApi::echo_printr($result_geo,"result_geo");

			if(isset($countries_arr[strtolower($result_geo)]) && $countries_arr[strtolower($result_geo)]!="")
                $result_geo=$countries_arr[strtolower($result_geo)];

			VisionApi::echo_printr($result_geo,"result_geo");

			VisionApi::echo_printr($subid_str,"subid_str");

            $subid_str_exp=explode('-',$subid_str);
            VisionApi::echo_printr($subid_str_exp,"subid_str_exp");

			$subid='';
            if(isset($subid_str_exp[1]))
                $subid=$subid_str_exp[1];

			VisionApi::echo_printr($subid,"subid");

			$publisher_name='';
			if(isset($this->db_publishers_arr['dealspricer'][strtolower($subid)]['name']))
            {
                if(trim($this->db_publishers_arr['dealspricer'][strtolower($subid)]['name'])!="")
                {
                    $publisher_name=trim($this->db_publishers_arr['dealspricer'][strtolower($subid)]['name']);
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
			VisionApi::echo_printr($subid,"subid");
			VisionApi::echo_printr($publisher_name,"publisher_name");
			VisionApi::echo_printr($payout_str,"Original Payout");

            $payout = $payout_str;



            VisionApi::echo_printr($payout,"Payout after conversion");

            $result_clicks=$result['Clicks'];

            $payout=$result_clicks*0.005;

            VisionApi::echo_printr($payout,"Payout");

			$result_cost_per_click=0;
			if($result_clicks>0)
                $result_cost_per_click=$payout/$result_clicks;

            VisionApi::echo_printr($result_clicks,"result_clicks");
            VisionApi::echo_printr($result_cost_per_click,"result_cost_per_click");

            if(strtotime($start)<=strtotime($result_date_db))
            {
                VisionApi::echo_printr("condition 1");

                //if we found actual report delete estimated report from db
                if(in_array($result_date_db,$visionapi_report_all_estimated_date_arr))
                {

                    VisionApi::echo_printr("Estimated record for date ".$result_date_db." found ");

					VisionApiReportAll::where('date', '=', $result_date_db)
					->where('advertiser_name', '=', $api)
					->delete();
					VisionApi::echo_printr($visionapi_report_all_estimated_date_arr,"visionapi_report_all_estimated_date_arr");

					if(($key = array_search($result_date_db, $visionapi_report_all_estimated_date_arr)) !== false) {
					    unset($visionapi_report_all_estimated_date_arr[$key]);
					}

                }

                VisionApi::echo_printr($visionapi_report_all_estimated_date_arr,"visionapi_report_all_estimated_date_arr");

                $this->clicks_report_db_insert_in_all($result_geo,$api,$subid,$publisher_name,$result_date_db,$result_clicks,$result_cost_per_click,0);


            }
            else
            {
                VisionApi::echo_printr("condition 2");
            }

		}
	}

	public function getEstimatedReportDatesArray($api)
	{
		$visionapi_report_all_estimated_date_arr=array();

		$visionapi_report_all_estimated = \DB::table('visionapi_report_all as a')
			->selectRaw('a.*')
			->where('advertiser_name', '=', $api)
			->where('is_estimated', '=', 1)
			->groupBy('date')
			->orderBy('date','desc')
			->get();

		if (count($visionapi_report_all_estimated) > 0)
		{
			$visionapi_report_all_estimated_arr = json_decode(json_encode($visionapi_report_all_estimated), true);

			VisionApi::echo_printr($visionapi_report_all_estimated_arr,"visionapi_report_all_estimated_arr");

			for ($i = 0; $i < count($visionapi_report_all_estimated_arr); $i++)
			{
				$visionapi_report_all_estimated_date_arr[]=$visionapi_report_all_estimated_arr[$i]['date'];
			}
		}

		return $visionapi_report_all_estimated_date_arr;
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

	public function getCsvData()
    {
        $workbook = SpreadsheetParser::open($this->excel_file_server_upload_file_path);

        $iterator = $workbook->createRowIterator(
            0,
            [
                //'encoding'  => 'UTF-8',
                'length'    => null,
                'delimiter' => ',',
                'enclosure' => '"',
                'escape'    => '\\'
            ]
        );
        $i=0;
        $k=0;
        $arr=array();
        $column_arr=array();

        foreach ($iterator as $rowIndex => $values)
        {
            if($i==0)
            {
                for($j=0;$j<count($values);$j++)
                {
                    $values[$j] = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $values[$j]);
                    $column_arr[$j]=trim($values[$j]);
                }
            }
            else
            {
                for($j=0;$j<count($values);$j++)
                {
                    $arr[$k][$column_arr[$j]]=$values[$j];
                }
                $k++;
            }
            $i++;
        }
        return $arr;
    }

	public function getCountriesAssociativeArray()
    {
        $countries_arr=[];
        $countries=Country::orderBy('name','asc')->get()->toArray();

        foreach($countries as $country) {
            $countries_arr[strtolower($country['name'])]=$country['code'];
        }

        return $countries_arr;
    }

	public function getPublishersInfo()
    {
        $publishers_arr=[];

        $advertisers = Advertiser::where('name', '=', 'Dealspricer')->where('is_delete', '=', 0)->with(array('publishers' => function($query)
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

	public function downloadFileFromDropbox()
    {
        if(count($this->dropbox_latest_file_info)>0) {

            $this->excel_file_server_upload_file_name=basename($this->dropbox_latest_file_info['path']);

            $this->excel_file_server_upload_file_path = $this->excel_file_server_upload_path . $this->excel_file_server_upload_file_name;

            $fd = fopen($this->excel_file_server_upload_file_path, "wb");

            $getFileMetadata = Dropbox::getFile($this->dropbox_latest_file_info['path'], $fd);

            VisionApi::echo_printr("Downloaded file from Dealspricer Dropbox named as ".$this->excel_file_server_upload_file_name." on ".date("j F, Y, g:i a"));

            fclose($fd);

            return true;
        }
        else
        {
            return false;
        }
    }

	public function getLatestFileInfoFromDropbox()
    {
        $metadata=Dropbox::getMetadataWithChildren($this->excel_file_dropbox_folder_name);

        $fileMetaArr=array();
        if(count($metadata['contents'])>0)
        {
            for($i=0;$i<count($metadata['contents']);$i++)
            {
                $row=$metadata['contents'][$i];

                $arr=explode(".",strtolower(trim(basename($row['path']))));
                $ext=end($arr);

                if($ext=='csv') {
                    $dt = new DateTime($row['modified']);
                    $sNewFormat = $dt->format("Y-m-d H:i:s");
                    $row['modified_datetime'] = $sNewFormat;

                    $fileMetaArr[] = $row;
                }
            }
        }
        else
        {
            $fileMetaArr[0]=array();
        }



        usort($fileMetaArr, array("App\Helpers\Dealspricer2", "date_compare"));

        return $fileMetaArr[0];
    }

    public function clicks_report_db_insert()
	{
		$month_start_date=date("Y-m-01",strtotime($this->yesterdays_date));
		$month_end_date=date("Y-m-t",strtotime($month_start_date));

		VisionApi::echo_printr($month_start_date, "month_start_date");
		VisionApi::echo_printr($month_end_date, "month_end_date");

		$visionapi_report_all = \DB::table('visionapi_report_all as a')
			->selectRaw('a.*')
			->where('advertiser_name', '=', 'Dealspricer')
			->where('date', '<=', $month_end_date)
			->where('date', '>=', $month_start_date)
			->orderBy('date','asc')
			->get();

		if (count($visionapi_report_all) > 0) {

			$visionapi_report_all_arr = json_decode(json_encode($visionapi_report_all), true);

			//VisionApi::echo_printr($visionapi_report_all_arr,"visionapi_report_all_arr");

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

    public function insertExternalInternalClicks()
	{
		if(count($this->internal_clicks_arr1)>0)
        {
            VisionApi::echo_printr("internal_clicks_arr1 count greater than 0");

			foreach($this->internal_clicks_arr1 as $k=>$v)
			{

				$clicks_advertiser_internal=ClickAdvertiserInternal::where('api', '=', 'Dealspricer')->where('date', '=', $k)->first();

				$clicks_advertiser_internal_input['api']='Dealspricer';
				$clicks_advertiser_internal_input['date']=$k;
				$clicks_advertiser_internal_input['internal_clicks']=$this->internal_clicks_arr1[$k];
				$clicks_advertiser_internal_input['api_clicks']=$this->external_clicks_arr[$k];

				if($clicks_advertiser_internal === null)
				{
					$clicks_advertiser_internal=ClickAdvertiserInternal::create($clicks_advertiser_internal_input);
				}

			}
        }
        else
        {
            VisionApi::echo_printr("internal_clicks_arr count is 0");
        }
	}

	function date_compare($a, $b)
    {
        $t1 = strtotime($a['modified_datetime']);
        $t2 = strtotime($b['modified_datetime']);
        return $t2 - $t1;
    }

    function date_sort_asc($a, $b)
    {
        $t1 = strtotime($a['datetime_cmp']);
        $t2 = strtotime($b['datetime_cmp']);
        return $t1 - $t2;
    }

}