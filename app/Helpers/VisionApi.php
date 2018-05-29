<?php

    namespace App\Helpers;

    use App\CronExecutionInfo;
    use App\CurrencyExchangeRate;
    use App\Publisher;
    use App\VisionApiReport;
    use GrahamCampbell\Dropbox\Facades\Dropbox;
    use Illuminate\Support\Facades\File;
    use Maatwebsite\Excel\Facades\Excel;
    use \Dropbox as dbx;
    use PHPMailer;
    use stdClass;

    $currency_codes_arr=array();

    $log_str='';

    class VisionApi {

        private $foxyDeal;

        private $dealspricer2;
        private $connexity;

        private $kelkoo1;
        private $twenga;
        private $adworks;

        private $ebay3;

        private $csv_file_dropbox_upload_folder_name="";
        private $csv_file_dropbox_upload_file_name1="";
        private $csv_file_dropbox_upload_file_name2="";

        private $csv_file_dropbox_upload_file_path1="";
        private $csv_file_dropbox_upload_file_path2="";


        private $excel_file_upload_file_path="";
        private $excel_file_upload_file_name="";

        private $csv_file_server_download_folder_path="";
        private $csv_file_server_download_file_path1="";
        private $csv_file_server_download_file_path2="";

        private $excel_file_download_path='';
        private $todays_date='';

        private $isdebug=0;


        public static $currency_exchange_rates;

        public function __construct()
        {
            $this->foxyDeal=new FoxyDeal();

            $this->dealspricer2=new Dealspricer2();
            $this->connexity=new Connexity();

            $this->kelkoo1=new Kelkoo1();
            $this->twenga=new Twenga();
            $this->adworks=new AdWorks();

            $this->ebay3=new Ebay3();
        }

        function __destruct()
        {
            global $log_str;

            $logDir=storage_path('logs/vision-api');

            if (!File::exists($logDir))
            {
                CurrencyLayerExchangeRateApi::echo_printr("vision-api folder not exists");
                $result = File::makeDirectory($logDir, 0775, true, true);
            }
            else
            {
                CurrencyLayerExchangeRateApi::echo_printr("vision-api folder exists");
            }

            CurrencyLayerExchangeRateApi::echo_printr("");

            echo $log_str;

            $log_str=str_replace("<pre>","",$log_str);
            $log_str=str_replace('<br />',"\n",$log_str);

            $logFile = $logDir.'/'.$this->todays_date.'_visionapi_cronlog.txt';

            $fp = fopen($logFile, 'a+');
            fwrite($fp, $log_str);
            fclose($fp);

        }

        public function init()
        {
			$this->isdebug=0;
			$this->isdebug=1;
            //increase max execution time of this script to 150 min:
            ini_set('max_execution_time', 9000);
            //increase Allowed Memory Size of this script:
            ini_set('memory_limit','960M');

            set_time_limit(0);

            VisionApi::echo_printr("");
            VisionApi::echo_printr("====================================================================================================");
            VisionApi::echo_printr("Start VisionApi init at ".date("j F, Y, g:i a"));

            date_default_timezone_set('America/Los_Angeles');

            VisionApi::echo_printr("Date after setting default timezone  ".date("j F, Y, g:i a"));

            $this->todays_date=date("Y-m-d");

            // Need to change

            $this->csv_file_dropbox_upload_folder_name='/iLeviathan-Reporting/3rd Party Reporting Output';


            $this->excel_file_download_path=base_path() . '/public/files/excels/downloads/';
            $this->csv_file_server_download_folder_path=base_path() . '/public/files/excels/downloads/';





			$execute_cron=0;
            $cron_execution_info = CronExecutionInfo::where('date', '=',$this->todays_date)->first();
			if ($cron_execution_info === null)
			{
			   // cron_execution_info doesn't exist
			   $execute_cron=1;
			   $cron_execution_input['date']=$this->todays_date;
			   $cron_execution_input['reporting']=1;

			   $cron_execution_info=CronExecutionInfo::create($cron_execution_input);

			}
			else
			{
				if($cron_execution_info->reporting==0)
				{
					$execute_cron=1;
					$cron_execution_input['reporting']=1;

					// update into db
					$cron_execution_info->fill($cron_execution_input)->save();
				}
			}



			//$execute_cron=1;
			if($execute_cron==1)
			{
				VisionApi::echo_printr("Execute cron");

	            VisionApiReport::truncate();

	            self::$currency_exchange_rates=$this->get_exchange_rates_from_db();


	            $this->foxyDeal->init();
	            $this->adworks->init();
	            $this->twenga->init();


				$this->generateCsv1();
	            $this->uploadCsvFile1OnDropbox();

	            $this->generateCsv2();
	            $this->uploadCsvFile2OnDropbox();

				$this->ebay3->init();
				$this->kelkoo1->init();
				$this->dealspricer2->init();
				$this->connexity->init();


	            $this->generateCsvAndSendToPublishers();
	            $this->generateAllDataCsvAndSendToAdmin();
	            $this->generateAllDataCsvAndSendToAdminTest();

            }
			else
			{
				VisionApi::echo_printr("Not execute cron");
			}
            //return true;
        }




        public function generateCsvAndSendToPublishers()
        {
			$publishers = Publisher::where('is_delete','=',0)->orderBy('id')->get();
			$csv_file_server_download_file_path="";

			$i=0;

			foreach($publishers as $publisher)
			{
					$publisher_name = $publisher->name;

					if(trim($publisher->email)=="")
						VisionApi::echo_printr("publisher email blank");
					else
						VisionApi::echo_printr("publisher email not blank");


					$db_reports1 = \DB::table('visionapi_reports as a')
					->selectRaw('SUM(estimated_revenue) AS total_estimated_revenue1,ROUND(SUM(estimated_revenue), 2) AS total_estimated_revenue, SUM(clicks) AS total_clicks,a.*')
					->where('dl_source', '=', $publisher_name)
					->groupBy('widget')
					->groupBy('date')
					->groupBy('country')
					->groupBy('sub_dl_source')
					->groupBy('dl_source')
					->orderBy('date')
					->get();


					$csvdataarr = array();

                    $csvdataarr[] = array('Date', 'DL_Source', 'Sub_Dlsource', 'Widget', 'Country', 'Searches', 'Clicks', 'Estimated_Revenue_In_USD');

                    if (count($db_reports1) > 0) {

		                $db_reports = json_decode(json_encode($db_reports1), true);

		                $j = 1;

		                for ($i = 0; $i < count($db_reports); $i++)
		                {
		                    $csvdataarr[$j][] = $db_reports[$i]['date'];
		                    $csvdataarr[$j][] = $db_reports[$i]['dl_source'];
		                    $csvdataarr[$j][] = $db_reports[$i]['sub_dl_source'];
		                    $csvdataarr[$j][] = $db_reports[$i]['widget'];
		                    $csvdataarr[$j][] = $db_reports[$i]['country'];
		                    $csvdataarr[$j][] = $db_reports[$i]['searches'];
		                    $csvdataarr[$j][] = $db_reports[$i]['total_clicks'];
		                    $csvdataarr[$j][] = $db_reports[$i]['total_estimated_revenue'];

			                $j++;

		                }


		                $csv_file_name=strtolower($publisher_name).'-'.date('m_d_Y',strtotime($this->todays_date));

		                $csv_file_folder_path=$this->csv_file_server_download_folder_path.'/publishers';

		                if (!File::exists($csv_file_folder_path))
			            {
			                $result = File::makeDirectory($csv_file_folder_path, 0775, true, true);
			            }

			            $csv_file_folder_path=$csv_file_folder_path.'/'.strtolower($publisher_name);
			            $csv_file_server_download_file_path=$csv_file_folder_path.'/'.$csv_file_name.'.csv';

			            if (!File::exists($csv_file_folder_path))
			            {
			                $result = File::makeDirectory($csv_file_folder_path, 0775, true, true);
			            }

						Excel::create($csv_file_name, function ($excel) use ($csvdataarr) {

							// Set the title
							$excel->setTitle('Report');

							// Chain the setters
							$excel->setCreator('Report')->setCompany('Report');

							// Call them separately
							$excel->setDescription('Report');

							$excel->sheet('Sheetname', function ($sheet) use ($csvdataarr) {

							    $sheet->fromArray($csvdataarr, null, 'A1', false, false);

							});

						})->store('csv', $csv_file_folder_path);


						$subject=   $publisher_name.' :: Daily Report '.date('m/d/Y',strtotime($this->todays_date));

						$body=  'Daily click and revenue report - '.date('m/d/Y',strtotime($this->todays_date));

						if(trim($publisher->email)!="")
						{
							VisionApi::echo_printr($subject,$publisher->email,"Email send to publisher email");


						}

						$this->sendEmailToPublishers($subject,$body,$publisher_name,"sam.posin@gmail.com",$csv_file_server_download_file_path);
						$this->sendEmailToPublishers($subject,$body,$publisher_name,"akash.posin@gmail.com",$csv_file_server_download_file_path);
						$this->sendEmailToPublishers($subject,$body,$publisher_name,"reporting@ileviathan.com",$csv_file_server_download_file_path);

		            }



		        $i++;
	        }
        }

        public function generateAllDataCsvAndSendToAdmin()
        {

			$db_reports1 = \DB::table('visionapi_reports as a')
			->selectRaw('SUM(estimated_revenue) AS total_estimated_revenue1,ROUND(SUM(estimated_revenue), 2) AS total_estimated_revenue, SUM(clicks) AS total_clicks,a.*')
			->groupBy('widget')
			->groupBy('date')
			->groupBy('country')
			->groupBy('sub_dl_source')
			->groupBy('dl_source')
			->orderBy('date')

			->get();



			$csvdataarr = array();

            $csvdataarr[] = array('Date', 'DL_Source', 'Sub_Dlsource', 'Widget', 'Country', 'Searches', 'Clicks', 'Estimated_Revenue_In_USD');

            if (count($db_reports1) > 0) {

                $db_reports = json_decode(json_encode($db_reports1), true);

                $j = 1;

                for ($i = 0; $i < count($db_reports); $i++)
                {
                    $csvdataarr[$j][] = $db_reports[$i]['date'];
                    $csvdataarr[$j][] = $db_reports[$i]['dl_source'];
                    $csvdataarr[$j][] = $db_reports[$i]['sub_dl_source'];
                    $csvdataarr[$j][] = $db_reports[$i]['widget'];
                    $csvdataarr[$j][] = $db_reports[$i]['country'];
                    $csvdataarr[$j][] = $db_reports[$i]['searches'];
                    $csvdataarr[$j][] = $db_reports[$i]['total_clicks'];
                    $csvdataarr[$j][] = $db_reports[$i]['total_estimated_revenue'];


	                $j++;

                }

                $csv_file_name='all-'.date('m_d_Y',strtotime($this->todays_date));


	            $csv_file_folder_path=$this->csv_file_server_download_folder_path.'/all';

                if (!File::exists($csv_file_folder_path))
	            {
	                $result = File::makeDirectory($csv_file_folder_path, 0775, true, true);
	            }

	            $csv_file_server_download_file_path=$csv_file_folder_path.'/'.$csv_file_name.'.csv';


	            Excel::create($csv_file_name, function ($excel) use ($csvdataarr) {

	                // Set the title
	                $excel->setTitle('Report');

	                // Chain the setters
	                $excel->setCreator('Report')->setCompany('Report');

	                // Call them separately
	                $excel->setDescription('Report');

	                $excel->sheet('Sheetname', function ($sheet) use ($csvdataarr) {

	                    $sheet->fromArray($csvdataarr, null, 'A1', false, false);
	                });

	            })->store('csv', $csv_file_folder_path);

	            $subject=   'All Clicks Data :: Daily Report '.date('m/d/Y',strtotime($this->todays_date));
	            $body=  'Daily click and revenue report - '.date('m/d/Y',strtotime($this->todays_date));

				$this->sendEmailToPublishers($subject,$body,"All","sam.posin@gmail.com",$csv_file_server_download_file_path);
				$this->sendEmailToPublishers($subject,$body,"All","akash.posin@gmail.com",$csv_file_server_download_file_path);
				$this->sendEmailToPublishers($subject,$body,"All","reporting@ileviathan.com",$csv_file_server_download_file_path);

            }
        }

        public function generateAllDataCsvAndSendToAdminTest()
        {

			$db_reports1 = \DB::table('visionapi_reports as a')
			->selectRaw('SUM(estimated_revenue) AS total_estimated_revenue1,ROUND(SUM(estimated_revenue), 2) AS total_estimated_revenue, SUM(clicks) AS total_clicks,a.*')
			->groupBy('widget')
			->groupBy('date')
			->groupBy('country')
			->groupBy('sub_dl_source')
			->groupBy('dl_source')
			->orderBy('date')

			->get();

			//print_r($db_reports1);

			$csvdataarr = array();

            $csvdataarr[] = array('Date', 'DL_Source', 'Sub_Dlsource', 'Widget', 'Country', 'Searches', 'Clicks', 'Estimated_Revenue_In_USD','Api','Estimated');

            if (count($db_reports1) > 0) {

                $db_reports = json_decode(json_encode($db_reports1), true);

                $j = 1;

                for ($i = 0; $i < count($db_reports); $i++)
                {
                    $csvdataarr[$j][] = $db_reports[$i]['date'];
                    $csvdataarr[$j][] = $db_reports[$i]['dl_source'];
                    $csvdataarr[$j][] = $db_reports[$i]['sub_dl_source'];
                    $csvdataarr[$j][] = $db_reports[$i]['widget'];
                    $csvdataarr[$j][] = $db_reports[$i]['country'];
                    $csvdataarr[$j][] = $db_reports[$i]['searches'];
                    $csvdataarr[$j][] = $db_reports[$i]['total_clicks'];
                    $csvdataarr[$j][] = $db_reports[$i]['total_estimated_revenue'];
                    $csvdataarr[$j][] = $db_reports[$i]['advertiser_name'];
                    $csvdataarr[$j][] = $db_reports[$i]['is_estimated'];


	                $j++;

                }

                $csv_file_name='all-test-'.date('m_d_Y',strtotime($this->todays_date));


	            $csv_file_folder_path=$this->csv_file_server_download_folder_path.'/all';

                if (!File::exists($csv_file_folder_path))
	            {
	                $result = File::makeDirectory($csv_file_folder_path, 0775, true, true);
	            }

	            $csv_file_server_download_file_path=$csv_file_folder_path.'/'.$csv_file_name.'.csv';


	            Excel::create($csv_file_name, function ($excel) use ($csvdataarr) {

	                // Set the title
	                $excel->setTitle('Report');

	                // Chain the setters
	                $excel->setCreator('Report')->setCompany('Report');

	                // Call them separately
	                $excel->setDescription('Report');

	                $excel->sheet('Sheetname', function ($sheet) use ($csvdataarr) {

	                    $sheet->fromArray($csvdataarr, null, 'A1', false, false);
	                });

	            })->store('csv', $csv_file_folder_path);

	            $subject=   'All Clicks Data Test:: Daily Report '.date('m/d/Y',strtotime($this->todays_date));
	            $body=  'Daily click and revenue report - '.date('m/d/Y',strtotime($this->todays_date));

				$this->sendEmailToPublishers($subject,$body,"All","sam.posin@gmail.com",$csv_file_server_download_file_path);
				$this->sendEmailToPublishers($subject,$body,"All","akash.posin@gmail.com",$csv_file_server_download_file_path);
				$this->sendEmailToPublishers($subject,$body,"All","reporting@ileviathan.com",$csv_file_server_download_file_path);

            }
        }


        public function generateCsv1()
        {

            $db_reports1 = \DB::table('visionapi_reports as a')
                ->selectRaw('SUM(estimated_revenue) AS total_estimated_revenue1,ROUND(SUM(estimated_revenue), 2) AS total_estimated_revenue, SUM(clicks) AS total_clicks,a.*')
                ->where('file_output_type',1)
                ->groupBy('widget')
                ->groupBy('date')
                ->groupBy('country')
                ->groupBy('sub_dl_source')
                ->orderBy('id')

                ->get();


            $csvdataarr = array();
            $csvdataarr[] = array('Date', 'DL_Source', 'Sub_Dlsource', 'Widget', 'Country', 'Searches', 'Clicks', 'Estimated_Revenue', 'Symbol');

            if (count($db_reports1) > 0) {

                $db_reports = json_decode(json_encode($db_reports1), true);

                $j = 1;
                VisionApi::echo_printr($db_reports,"Final data from db for file 1");
                for ($i = 0; $i < count($db_reports); $i++)
                {
                    $csvdataarr[$j][] = $db_reports[$i]['date'];
                    $csvdataarr[$j][] = $db_reports[$i]['dl_source'];
                    $csvdataarr[$j][] = $db_reports[$i]['sub_dl_source'];
                    $csvdataarr[$j][] = $db_reports[$i]['widget'];
                    $csvdataarr[$j][] = $db_reports[$i]['country'];
                    $csvdataarr[$j][] = $db_reports[$i]['searches'];
                    $csvdataarr[$j][] = $db_reports[$i]['total_clicks'];
                    $csvdataarr[$j][] = $db_reports[$i]['total_estimated_revenue'];

                    $csvdataarr[$j][] = $db_reports[$i]['symbol'];

                    $j++;
                }

            }



            $this->csv_file_dropbox_upload_file_name1=str_replace("-","_",$this->todays_date) . '_3rd_Party_Reporting_VisionAPI_Monetizations';

            $this->csv_file_server_download_file_path1=$this->csv_file_server_download_folder_path.'/'.$this->csv_file_dropbox_upload_file_name1.'.csv';

            Excel::create($this->csv_file_dropbox_upload_file_name1, function ($excel) use ($csvdataarr) {

                // Set the title
                $excel->setTitle('Report');

                // Chain the setters
                $excel->setCreator('Report')->setCompany('Report');

                // Call them separately
                $excel->setDescription('Report');

                $excel->sheet('Sheetname', function ($sheet) use ($csvdataarr) {

                    $sheet->fromArray($csvdataarr, null, 'A1', false, false);

                });

            })->store('csv', $this->csv_file_server_download_folder_path);

        }

        public function generateCsv2()
        {

            $db_reports1 = \DB::table('visionapi_reports as a')
                ->selectRaw('SUM(estimated_revenue) AS total_estimated_revenue1,ROUND(SUM(estimated_revenue), 2) AS total_estimated_revenue, SUM(clicks) AS total_clicks,a.*')
                ->where('file_output_type',2)
                ->groupBy('advertiser_name')
                ->groupBy('date')
                ->groupBy('country')
                ->groupBy('sub_dl_source')
                ->orderBy('id')

                ->get();


            $csvdataarr = array();
            $csvdataarr[] = array('Date', 'DL_Source', 'Sub_Dlsource', 'Widget', 'Country', 'Searches', 'Clicks', 'Estimated_Revenue', 'Symbol');

            if (count($db_reports1) > 0) {

                $db_reports = json_decode(json_encode($db_reports1), true);

                $j = 1;
                VisionApi::echo_printr($db_reports,"Final data from db for file 1");
                for ($i = 0; $i < count($db_reports); $i++)
                {
                    $csvdataarr[$j][] = $db_reports[$i]['date'];
                    $csvdataarr[$j][] = $db_reports[$i]['dl_source'];
                    $csvdataarr[$j][] = $db_reports[$i]['sub_dl_source'];
                    $csvdataarr[$j][] = $db_reports[$i]['widget'];
                    $csvdataarr[$j][] = $db_reports[$i]['country'];
                    $csvdataarr[$j][] = $db_reports[$i]['searches'];
                    $csvdataarr[$j][] = $db_reports[$i]['total_clicks'];
                    $csvdataarr[$j][] = $db_reports[$i]['total_estimated_revenue'];

                    $csvdataarr[$j][] = $db_reports[$i]['symbol'];

                    $j++;
                }
            }



            $this->csv_file_dropbox_upload_file_name2=$this->todays_date . '_3rd_Party_Reporting_VisionAPI_Feeds';

            $this->csv_file_server_download_file_path2=$this->csv_file_server_download_folder_path.'/'.$this->csv_file_dropbox_upload_file_name2.'.csv';

            Excel::create($this->csv_file_dropbox_upload_file_name2, function ($excel) use ($csvdataarr) {

                // Set the title
                $excel->setTitle('Report');

                // Chain the setters
                $excel->setCreator('Report')->setCompany('Report');

                // Call them separately
                $excel->setDescription('Report');

                $excel->sheet('Sheetname', function ($sheet) use ($csvdataarr) {

                    $sheet->fromArray($csvdataarr, null, 'A1', false, false);

                });

            })->store('csv', $this->csv_file_server_download_folder_path);
        }

        public function uploadCsvFile1OnDropbox()
        {
            if (File::exists($this->csv_file_server_download_file_path1)) {

                $fd = fopen($this->csv_file_server_download_file_path1, "rb");

                $this->csv_file_dropbox_upload_file_path1=$this->csv_file_dropbox_upload_folder_name.'/'.$this->csv_file_dropbox_upload_file_name1.'.csv';
                $md1 = Dropbox::uploadFile($this->csv_file_dropbox_upload_file_path1, dbx\WriteMode::add(), $fd);
                fclose($fd);

            }
        }

        public function uploadCsvFile2OnDropbox()
        {
            if (File::exists($this->csv_file_server_download_file_path2)) {

                $fd = fopen($this->csv_file_server_download_file_path2, "rb");

                $this->csv_file_dropbox_upload_file_path2=$this->csv_file_dropbox_upload_folder_name.'/'.$this->csv_file_dropbox_upload_file_name2.'.csv';
                $md1 = Dropbox::uploadFile($this->csv_file_dropbox_upload_file_path2, dbx\WriteMode::add(), $fd);
                fclose($fd);

            }
        }

        public function get_exchange_rates_from_db()
        {
            $exchange_rates_arr=[];

            $exchange_rates=CurrencyExchangeRate::orderBy('id','desc')->limit(20000)->offset(0)->get()->toArray();

            foreach($exchange_rates as $exchange_rate) {
                $exchange_rates_arr[$exchange_rate['date']][$exchange_rate['to_currency']]=$exchange_rate['rate'];
            }

            return $exchange_rates_arr;
        }

        public function get_exchange_rates_from_currencylayer_api()
        {
            echo "<br>";
            echo "get_exchange_rates_from_currencylayer_api called";
            echo "<br>";
            // set API Endpoint and Access Key (and any options of your choice)
            $endpoint = '';
            $access_key = '';

            // Initialize CURL:
            $ch = curl_init('http://apilayer.net/api/'.$endpoint.'?access_key='.$access_key.'');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            // Store the data:
            $json = curl_exec($ch);
            curl_close($ch);

            // Decode JSON response:
            $exchangeRates = json_decode($json, true);



            self::$currency_exchange_rates= $exchangeRates;


        }

        public function processExcelAndGenerateCsv()
        {
            if(file_exists($this->excel_file_upload_file_path))
            {

                Excel::selectSheets('Conversions')->load($this->excel_file_upload_file_path, function($reader) {

                    $csvdataarr=array();
                    $csvdataarr[] = array('Date','DL Source','Sub DL Dource','Widget','Country','Searches','Clicks','Estimated Revenue','Symbol');

                    // Getting all results
                    $results = $reader->get();

                    $j = 1;
                    foreach ($results as $result) {

                        $subid = $result['SUB ID'];
                        $conversionid = $result['Conversion ID'];
                        $time = $result['Time'];
                        //$payout = $result['Payout'];
                        $country = $result['Country'];

                        if (strtoupper($subid) != "WSKY") {
                            //$j = $i + 1;

                            $time_str = "";
                            $time_exp = explode(" ", $time);
                            if (count($time_exp) == 2)
                                $time_str = $time_exp[0];

                            $payout_str = $result['Payout'];
                            $payout = $payout_str;
                            $payout = ($payout) * (37.5 / 100);
                            $payout = round($payout, 2);
                            $payout_str = '$' . $payout;

                            $csvdataarr[$j][] = $time_str;
                            $csvdataarr[$j][] = 'VisionAPI';
                            $csvdataarr[$j][] = $subid;

                            if (strpos($this->excel_file_upload_file_name, 'foxydeal') !== false) {
                                $csvdataarr[$j][] = 'Saving Bar';
                            } else {
                                $csvdataarr[$j][] = '';
                            }

                            $csvdataarr[$j][] = $country;
                            $csvdataarr[$j][] = '';
                            $csvdataarr[$j][] = '';
                            $csvdataarr[$j][] = $payout_str;
                            $csvdataarr[$j][] = 'USD';
                            $j++;

                        }

                    }

                    Excel::create($this->todays_date.'_3rd Party Reporting - VisionAPI', function($excel) use($csvdataarr) {

                        // Set the title
                        $excel->setTitle('Report');

                        // Chain the setters
                        $excel->setCreator('Report')->setCompany('Report');

                        // Call them separately
                        $excel->setDescription('Report');

                        $excel->sheet('Sheetname', function($sheet) use($csvdataarr) {

                            $sheet->fromArray($csvdataarr, null, 'A1', false, false);

                        });

                    })->store('csv',$this->excel_file_download_path);

                });
            }
        }

        public static function echo_printr($val='',$label='')
        {
            global $log_str;

            $tmp_str="";

            if(is_array($val) || is_object($val))
            {
                if($label!='')
                    $tmp_str.=$label."\n";
                $tmp_str.=print_r($val,true)."\n\n";
            }
            else
            {
                if($label!='')
                    $tmp_str.=$label." = ";
                $tmp_str.=$val."\n\n";
            }

            if (\App::runningInConsole())
            {
                //echo "I'm in the console, baby!";
            }
            else
            {
                if(trim($log_str)=="")
                    $log_str.="<pre>";

                $tmp_str = str_replace("\r", "", $tmp_str);  // Remove \r
                $tmp_str = str_replace("\n", "<br />", $tmp_str);  // Replace \n with <br />
            }
            $log_str.=$tmp_str;

        }
    }