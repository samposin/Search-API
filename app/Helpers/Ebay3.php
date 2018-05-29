<?php namespace App\Helpers;

use Akeneo\Component\SpreadsheetParser\SpreadsheetParser;
use App\Advertiser;
use App\ClickAdvertiserInternal;
use App\VisionApiReport;
use App\VisionApiReportAll;
use DateTime;
use GrahamCampbell\Dropbox\Facades\Dropbox;
use Illuminate\Support\Facades\File;

class Ebay3 {

	private $dropbox_files_geo_arr=array(
		"US"=>array(),
		"FR"=>array(),
		"UK"=>array(),
		"DE"=>array(),
		"AU"=>array()
	);

	private $geo_arr=array(
		"US","FR","UK","DE","AU"
	);




	private $geo_info_arr=array();



	private $db_publishers_arr=array();

	private $excel_file_dropbox_folder_name="";
	private $excel_file_server_upload_path="";
	private $dropbox_latest_file_info_arr=array();
	private $todays_date='';
	private $yesterdays_date='';
	private $internal_clicks_arr=array();
	private $external_clicks_arr=array();
	private $api_display_name='Ebay';
	private $api_name='ebay';

	public function init()
	{
		VisionApi::echo_printr("Start Ebay init at " . date("j F, Y, g:i a"));

		$this->todays_date = date("Y-m-d");


		VisionApi::echo_printr($this->todays_date,"Current Date");

		$dt = new DateTime($this->todays_date);
        $dt->modify('-1 day');

        $this->yesterdays_date=$dt->format("Y-m-d");

        VisionApi::echo_printr($this->yesterdays_date,"Yesterdays Date");


		$this->excel_file_dropbox_folder_name='/iLeviathan-Reporting/Ebay';

		$this->excel_file_server_upload_path = base_path() . '/public/files/excels/uploads/Ebay/';

		$this->dropbox_latest_file_info_arr=$this->getLatestFileInfoFromDropbox($this->excel_file_dropbox_folder_name);

		foreach($this->geo_arr as $geo)
        {
	        if($this->downloadFileFromDropboxByGeo($geo))
	        {
				if(isset($this->geo_info_arr[$geo])) {
					$this->processFile($geo);
				}
	        }
        }

        VisionApi::echo_printr($this->geo_info_arr,"geo_info_arr");
        VisionApi::echo_printr($this->internal_clicks_arr,"internal_clicks_arr");
        VisionApi::echo_printr($this->external_clicks_arr,"external_clicks_arr");

        $this->insertExternalInternalClicks();

        foreach($this->geo_arr as $geo)
        {
	        $this->getEstimatedReport($geo);
        }

		// insert into table which contains temporary report for today
        $this->clicks_report_db_insert();
	}

	public function getEstimatedReport($geo)
	{
		$api=$this->api_display_name;
		$visionapi_report_all = \DB::table('visionapi_report_all as a')
			->selectRaw('a.*')
			->where('advertiser_name', '=', $api)
			->where('country', '=', strtoupper($geo))
			->groupBy('date')
			->orderBy('date','DESC')
			->first();

		if ($visionapi_report_all !== null)
		{
			VisionApi::echo_printr("Estimated visionapi_report_all is not null ".$geo);
			VisionApi::echo_printr($visionapi_report_all->date,"last date");

			$start=date('Y-m-d', strtotime($visionapi_report_all->date . " +1 day"));

			VisionApi::echo_printr($start,"Estimated start date");
			VisionApi::echo_printr($this->yesterdays_date,"Estimated yesterday date");

			$tmp_start=$start;

			if(strtotime($tmp_start)<=strtotime($this->yesterdays_date))
			{
				while (strtotime($tmp_start) <= strtotime($this->yesterdays_date))
				{
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
						->where('country', '=', strtoupper($geo))
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

					$this->clicks_report_db_insert_in_all($geo,$api,$tmp_start,$final_total_clicks,$cost_per_click_avg,1);

					$tmp_start = date('Y-m-d', strtotime($tmp_start . " +1 day"));
				}
			}
		}
		else
		{
			VisionApi::echo_printr("Estimated visionapi_report_all is null ".$geo);
		}
	}

	public function processFile($geo)
	{
		$api=$this->api_display_name;
		VisionApi::echo_printr("geo is ".$geo);

		if(file_exists($this->geo_info_arr[$geo]['excel_file_server_upload_file_path']))
		{
			$this->db_publishers_arr = $this->getPublishersInfo();


			$csv_data=$this->getCsvData($geo);
            VisionApi::echo_printr($csv_data,"CSV Data");

            $visionapi_report_all = \DB::table('visionapi_report_all as a')
				->selectRaw('a.*')
				->where('advertiser_name', '=', $api)
				->where('country', '=', strtoupper($geo))
				->groupBy('date')
				->orderBy('date')
				->first();

			// if we are inserting first time
			if ($visionapi_report_all === null)
			{
				VisionApi::echo_printr("visionapi_report_all is null");

				$start=date("Y-m-01",strtotime($this->yesterdays_date));
				VisionApi::echo_printr($start,"start");
				$csv_data_reversed = array_reverse($csv_data);
				$this->processFile1($geo,$api,$csv_data_reversed,$start);
			}
			else
			{
				VisionApi::echo_printr("visionapi_report_all is not null");

				$visionapi_report_all = \DB::table('visionapi_report_all as a')
					->selectRaw('a.*')
					->where('advertiser_name', '=', $api)
					->where('country', '=', strtoupper($geo))
					->where('is_estimated', '=', 0)
					->groupBy('date')
					->orderBy('date','desc')
					->get();

				if (count($visionapi_report_all) > 0)
				{
					$visionapi_report_all_arr = json_decode(json_encode($visionapi_report_all), true);
					//VisionApi::echo_printr($visionapi_report_all_arr,"visionapi_report_all_arr");

					$start=date('Y-m-d', strtotime($visionapi_report_all_arr[0]['date'] . " +1 day"));
					VisionApi::echo_printr($start,"start");
					$csv_data_reversed = array_reverse($csv_data);
					$this->processFile1($geo,$api,$csv_data_reversed,$start);
				}
			}
		}
	}

	public function processFile1($geo,$api,$csv_data,$start)
	{
		//get estimated reports date array
		$visionapi_report_all_estimated_date_arr=$this->getEstimatedReportDatesArray($geo,$api);

		VisionApi::echo_printr($visionapi_report_all_estimated_date_arr,"visionapi_report_all_estimated_date_arr");
		VisionApi::echo_printr($csv_data,"CSV Data");

		$date1="";

		for($i=0;$i<count($csv_data)-1;$i++)
		{
            $result = $csv_data[$i];

            echo "<pre>";
            VisionApi::echo_printr($result,"result");

            $result_date=$result['DATE'];
            $result_date_db=$result['DATE'];

            if($result_date!="") {
	            $result_date=str_replace('/', '-',$result_date);
	            $result_date_exp=explode('-', $result_date);

	            if(count($result_date_exp)==3)
                {
                    $mm=$result_date_exp[0];
                    $dd=$result_date_exp[1];
                    $yy=$result_date_exp[2];

                    if (strlen($yy) == 2)
                        $yy = '20' . $yy;

                    $result_date = $dd . '-' . $mm . '-' . $yy;
                    $result_date_db=$yy . '-' . $mm . '-' . $dd;



                    VisionApi::echo_printr($result_date,"result_date");
                    VisionApi::echo_printr($result_date_db,"result_date_db");
                    $result_date_db=date("Y-m-d",strtotime($result_date_db));
                    VisionApi::echo_printr($result_date_db,"result_date_db");
                    VisionApi::echo_printr($start,"start");

                    $date1=$result_date_db;

                    if(strtotime($start)<=strtotime($result_date_db))
                    {
                        VisionApi::echo_printr("condition 1");

						//if we found actual report delete estimated report from db
                        if(in_array($result_date_db,$visionapi_report_all_estimated_date_arr))
                        {
                            VisionApi::echo_printr("Estimated record for date ".$result_date_db." found ");

							VisionApiReportAll::where('date', '=', $result_date_db)
							->where('advertiser_name', '=', $api)
							->where('country', '=', strtoupper($geo))
							->delete();
                        }

						// insert into table which contains complete report
                        $this->clicks_report_db_insert_in_all($geo,$api,$result_date_db,$result['LEADS'],$result['REVENUE_PER_LEAD'],0);
                    }
                    else
                    {
                        VisionApi::echo_printr("condition 2");
                    }
                }
            }
        }


	}

	public function clicks_report_db_insert_in_all($geo,$api,$result_date_db,$result_clicks,$result_cost_per_click,$is_estimated=0)
    {
		$query1 = \DB::table('search_clicks as a')
				->selectRaw('SUM(clicks) AS total_clicks1,a.*')
				->where('date', '=', $result_date_db)
				->where('api', '=', strtolower($api))
				->where('country_code', '=', strtoupper($geo))
				->groupBy('api')
				->groupBy('dl_source')
				->groupBy('sub_dl_source')
				->groupBy('widget')
				->groupBy('country_code')
				->groupBy('date')
				->orderBy('date');


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

		if($total_clicks_db!=0)
		{
			$click_ratio = $result_clicks / $total_clicks_db;

			VisionApi::echo_printr($result_clicks,"result_clicks");
			VisionApi::echo_printr($total_clicks_db,"total_clicks_db");
			VisionApi::echo_printr($click_ratio,"click_ratio");
			VisionApi::echo_printr(count($db_reports1),"count db_reports1");

			if (count($db_reports1) > 0)
			{
				$total_records=count($db_reports1);
				$searches="";

				$db_reports = json_decode(json_encode($db_reports1), true);

				VisionApi::echo_printr(count($db_reports),"count db_reports");


				for ($i = 0; $i < count($db_reports); $i++)
				{
					VisionApi::echo_printr($db_reports[$i],"db report i");

					$publisher_share=37.5;

					$country_code=$db_reports[$i]['country_code'];
					$dl_source=$db_reports[$i]['dl_source'];
                    $sub_dl_source=$db_reports[$i]['sub_dl_source'];
                    $widget=$db_reports[$i]['widget'];
                    $date=$db_reports[$i]['date'];

                    $clicks=($db_reports[$i]['total_clicks1']*$click_ratio);

                    $estimated_revenue=$db_reports[$i]['total_clicks1']*$click_ratio*$result_cost_per_click;

					VisionApi::echo_printr(strtolower($dl_source),"strtolower dl_source");
					VisionApi::echo_printr($geo,"geo");

                    if(isset($this->db_publishers_arr['ebay'][$geo][strtolower($dl_source)]['pivot']['share']))
	                {
	                    VisionApi::echo_printr($this->db_publishers_arr['ebay'][$geo][strtolower($dl_source)],"");
	                    if($this->db_publishers_arr['ebay'][$geo][strtolower($dl_source)]['pivot']['share']>0)
	                    {
	                        $publisher_share=$this->db_publishers_arr['ebay'][$geo][strtolower($dl_source)]['pivot']['share'];
	                    }
	                }

	                VisionApi::echo_printr($publisher_share,"publisher_share");
	                VisionApi::echo_printr($db_reports[$i]['total_clicks1'],"db report total_clicks");
					VisionApi::echo_printr($estimated_revenue,"estimated_revenue");

					if($publisher_share>0)
                        $estimated_revenue = ($estimated_revenue) * ($publisher_share / 100);

					VisionApi::echo_printr($estimated_revenue,"estimated_revenue");

                    $input['date'] = $date;
	                $input['dl_source'] = $dl_source;
	                $input['sub_dl_source'] = $sub_dl_source;
	                $input['widget'] = $widget;
	                $input['country'] = $country_code;
	                $input['searches'] = $searches;
	                $input['clicks'] = $clicks;
	                $input['estimated_revenue'] = $estimated_revenue;
	                $input['advertiser_name'] = 'Ebay';
	                $input['total_clicks'] = $result_clicks;
	                $input['cost_per_click'] = $result_cost_per_click;
	                $input['is_estimated'] = $is_estimated;

	                VisionApiReportAll::create($input);
				}
			}
		}
    }

	public function getCsvData($geo)
    {
        $workbook = SpreadsheetParser::open($this->geo_info_arr[$geo]['excel_file_server_upload_file_path']);

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

	public function getPublishersInfo()
    {
        $publishers_arr=[];

        $advertisers = Advertiser::where('name', 'like', 'Ebay%')->where('is_delete', '=', 0)->with(array('publishers' => function($query)
        {
            $query->where('is_delete', '=', 0);

        }))->get()->toArray();

        $publishers_arr['ebay']=array();

        foreach($advertisers as $advertiser)
        {
            $advertiser_name=$advertiser['name'];
            $advertiser_name_exp=explode(' ',$advertiser_name);

            if(count($advertiser_name_exp)>1)
            {
                $advertiser_name=trim($advertiser_name_exp[0]);
                $geo=strtoupper(trim($advertiser_name_exp[1]));



	            for($i=0;$i<count($advertiser['publishers']);$i++)
	            {
	                $publisher=$advertiser['publishers'][$i];
	                $publishers_arr[strtolower($advertiser_name)][$geo][strtolower($publisher['name'])] = $publisher;
	            }
            }
        }

        return $publishers_arr;
    }

	public function downloadFileFromDropboxByGeo($geo)
    {
        if(count($this->dropbox_files_geo_arr[$geo])>0)
        {
            VisionApi::echo_printr("File found");

            $this->geo_info_arr[$geo]['excel_file_server_upload_file_name']=basename($this->dropbox_files_geo_arr[$geo][0]['path']);

            $this->geo_info_arr[$geo]['excel_file_server_upload_file_path'] = $this->excel_file_server_upload_path .$geo.'/'. $this->geo_info_arr[$geo]['excel_file_server_upload_file_name'];

            if (!File::exists($this->excel_file_server_upload_path))
            {
                VisionApi::echo_printr("ebay folder not exists");
                $result = File::makeDirectory($this->excel_file_server_upload_path, 0775, true, true);
            }
            else
            {
                VisionApi::echo_printr("ebay folder exists");
            }

            if (!File::exists($this->excel_file_server_upload_path.'/'.$geo))
            {
                VisionApi::echo_printr($geo." geo folder not exists");
                $result = File::makeDirectory($this->excel_file_server_upload_path.'/'.$geo, 0775, true, true);
            }
            else
            {
                VisionApi::echo_printr($geo." geo folder exists");
            }

            $fd = fopen( $this->geo_info_arr[$geo]['excel_file_server_upload_file_path'], "wb");

            $getFileMetadata = Dropbox::getFile($this->dropbox_files_geo_arr[$geo][0]['path'], $fd);

            VisionApi::echo_printr("Downloaded file from Ebay Dropbox named as ".$this->geo_info_arr[$geo]['excel_file_server_upload_file_name']." on ".date("j F, Y, g:i a"));

            fclose($fd);

            return true;
        }
        else
        {
            VisionApi::echo_printr("File not found");
            return false;
        }
    }

	function date_compare($a, $b)
    {
        $t1 = strtotime($a['modified_datetime']);
        $t2 = strtotime($b['modified_datetime']);
        return $t2 - $t1;
    }

	public function getLatestFileInfoFromDropbox($dropbox_file_path)
    {
        $metadata=Dropbox::getMetadataWithChildren($dropbox_file_path);



        if(isset($metadata['contents']))
        {
            VisionApi::echo_printr("metadata set");
	        if (count($metadata['contents']) > 0)
	        {
		        for ($i = 0; $i < count($metadata['contents']); $i++)
		        {
			        $row = $metadata['contents'][$i];

			        $dt = new DateTime($row['modified']);
			        $sNewFormat = $dt->format("Y-m-d H:i:s");
			        $row['modified_datetime'] = $sNewFormat;

			        $basename=basename($row['path']);
			        VisionApi::echo_printr($basename,"basename");

			        foreach($this->geo_arr as $geo)
			        {
				        VisionApi::echo_printr($geo, "geo");

				        if($geo=='UK')
							$basename_substr = 'ileviathan_GB_';
						else
				            $basename_substr = 'ileviathan_' . strtoupper($geo) . '_';



						if (strpos($basename, $basename_substr) !== false)
				        {
							$this->dropbox_files_geo_arr[$geo][]=$row;
				        }
			        }
		        }
		        VisionApi::echo_printr($this->dropbox_files_geo_arr,"dropbox_files_geo_arr");
		        foreach($this->geo_arr as $geo)
			    {
				    usort($this->dropbox_files_geo_arr[$geo], array("App\Helpers\Ebay3", "date_compare"));
			    }
			    VisionApi::echo_printr($this->dropbox_files_geo_arr,"dropbox_files_geo_arr");
	        }
	        else
	        {
		        VisionApi::echo_printr("metadata content is 0");
	        }
        }
        else
        {
            VisionApi::echo_printr("metadata not set");
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
			->where('advertiser_name', '=', 'Ebay')
			->where('date', '<=', $month_end_date)
			->where('date', '>=', $month_start_date)
			->orderBy('date','asc')
			->get();

		if (count($visionapi_report_all) > 0)
		{
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

	public function getEstimatedReportDatesArray($geo,$api)
	{
		$visionapi_report_all_estimated_date_arr=array();

		$visionapi_report_all_estimated = \DB::table('visionapi_report_all as a')
			->selectRaw('a.*')
			->where('advertiser_name', '=', $api)
			->where('country', '=', strtoupper($geo))
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

	public function insertExternalInternalClicks()
	{
		if(count($this->internal_clicks_arr)>0)
        {
            VisionApi::echo_printr("internal_clicks_arr count greater than 0");

			foreach($this->internal_clicks_arr as $k=>$v)
			{
				$clicks_advertiser_internal=ClickAdvertiserInternal::where('api', '=', 'Ebay')->where('date', '=', $k)->first();

				$clicks_advertiser_internal_input['api']='Ebay';
				$clicks_advertiser_internal_input['date']=$k;
				$clicks_advertiser_internal_input['internal_clicks']=$this->internal_clicks_arr[$k];
				$clicks_advertiser_internal_input['api_clicks']=$this->external_clicks_arr[$k];

				if($clicks_advertiser_internal === null)
				{
					$clicks_advertiser_internal=ClickAdvertiserInternal::create($clicks_advertiser_internal_input);
				}
				else
				{
					$clicks_advertiser_internal_internal_clicks=$clicks_advertiser_internal->internal_clicks;
					$clicks_advertiser_internal_api_clicks=$clicks_advertiser_internal->api_clicks;

					VisionApi::echo_printr($clicks_advertiser_internal_internal_clicks,"clicks_advertiser_internal_internal_clicks");
					VisionApi::echo_printr($clicks_advertiser_internal_api_clicks,"clicks_advertiser_internal_api_clicks");


					$clicks_advertiser_internal_input['internal_clicks']=$clicks_advertiser_internal_internal_clicks+$this->internal_clicks_arr[$k];
					$clicks_advertiser_internal_input['api_clicks']=$clicks_advertiser_internal_api_clicks+$this->external_clicks_arr[$k];

					$clicks_advertiser_internal->fill($clicks_advertiser_internal_input)->save();
				}
			}
        }
        else
        {
            VisionApi::echo_printr("internal_clicks_arr count is 0");
        }
	}
}