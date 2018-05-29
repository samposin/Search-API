<?php namespace App\Helpers;

use App\CronExecutionInfo;
use App\CurrencyExchangeRate;
use Illuminate\Support\Facades\File;

$log_str='';

class CurrencyLayerExchangeRateApi {

    private $currency_exchange_rates;
    private $todays_date='';

    public function __construct()
    {

    }

    function __destruct() {

        global $log_str;

        $logDir=storage_path('logs/currency-layer-exchange-rate-api');

        if (!File::exists($logDir))
        {
            CurrencyLayerExchangeRateApi::echo_printr("currency-layer-exchange-rate-api folder not exists");
            $result = File::makeDirectory($logDir, 0775, true, true);
        }
        else
        {
            CurrencyLayerExchangeRateApi::echo_printr("currency-layer-exchange-rate-api folder exists");
        }

        CurrencyLayerExchangeRateApi::echo_printr("");


        $log_str=str_replace("<pre>","",$log_str);
        $log_str=str_replace('<br />',"\n",$log_str);

        $logFile = $logDir.'/'.$this->todays_date.'_currencyapi_cronlog.txt';

        $fp = fopen($logFile, 'a+');
        fwrite($fp, $log_str);
        fclose($fp);

    }

    public function init($indate = null)
    {
        //increase max execution time of this script to 150 min:
        ini_set('max_execution_time', 9000);
        //increase Allowed Memory Size of this script:
        ini_set('memory_limit','960M');

        set_time_limit(0);

        CurrencyLayerExchangeRateApi::echo_printr("");
        CurrencyLayerExchangeRateApi::echo_printr("====================================================================================================");
        CurrencyLayerExchangeRateApi::echo_printr("Start CurrencyLayerExchangeRateApi init at ".date("j F, Y, g:i a"));

        date_default_timezone_set('America/Los_Angeles');

        CurrencyLayerExchangeRateApi::echo_printr("Date after setting default timezone  ".date("j F, Y, g:i a"));

        $this->todays_date= empty($indate) ? date("Y-m-d") : $indate;

        $execute_cron=0;
        $cron_execution_info = CronExecutionInfo::where('date', '=',$this->todays_date)->first();
		if ($cron_execution_info === null)
		{
		   // cron_execution_info doesn't exist
		   $execute_cron=1;
		   $cron_execution_input['date']=$this->todays_date;
		   $cron_execution_input['currency']=1;

		   $cron_execution_info=CronExecutionInfo::create($cron_execution_input);

		}
		else
		{
			if($cron_execution_info->currency==0)
			{
				$execute_cron=1;
				$cron_execution_input['currency']=1;

				// update into db
				$cron_execution_info->fill($cron_execution_input)->save();
			}
		}

		if($execute_cron==1)
		{
			CurrencyLayerExchangeRateApi::echo_printr("Execute cron");

	        //For Todays
	        $this->currency_exchange_rates=$this->get_exchange_rates_from_currencylayer_api();

	        CurrencyLayerExchangeRateApi::echo_printr($this->currency_exchange_rates,"Currency Exchange Rates");

	        if(isset($this->currency_exchange_rates['success']) && $this->currency_exchange_rates['success']==1)
	        {
	            $timestamp=$this->currency_exchange_rates['timestamp'];

	            $date=date("Y-m-d",$timestamp);

	            $i=0;
	            foreach($this->currency_exchange_rates['quotes'] as $k=>$v)
	            {

	                $from_currency="USD";
	                $to_currency= str_replace("USD","",$k);
	                if(trim($to_currency)=="")
	                    $to_currency="USD";
	                $rate=$v;

	                $input['date'] = $date;
	                $input['from_currency'] = $from_currency;
	                $input['to_currency'] = $to_currency;
	                $input['rate'] = $rate;

	                $currency_exchange_rate = CurrencyExchangeRate::where('date', '=',$date)->where('from_currency', '=',$from_currency)->where('to_currency', '=',$to_currency)->first();
					if ($currency_exchange_rate === null) {
						CurrencyLayerExchangeRateApi::echo_printr("Record not exists");
						CurrencyExchangeRate::create($input);
						CurrencyLayerExchangeRateApi::echo_printr($input,"Saved in DB");
					}
					else
					{
						CurrencyLayerExchangeRateApi::echo_printr("Record exists");
					}
	                CurrencyLayerExchangeRateApi::echo_printr("counter = ".$i);

	                $i++;
	            }
	        }

	    }
		else
		{
			CurrencyLayerExchangeRateApi::echo_printr("Not execute cron");
		}
    }

    public function get_previous_exchange_rates_from_currencylayer_api()
    {
        $date="2015-09-30";

        $todays_date=date("Y-m-d");

        $todayes_timestamp=strtotime($todays_date);

        $timestamp1=strtotime($date);

        while($timestamp1<$todayes_timestamp)
        {
            echo "date = ".date("Y-m-d H:i:s A",$timestamp1);
            echo "<br>";

            $current_date=date("Y-m-d",$timestamp1);

            $exchange_rates_arr=$this->get_exchange_rates_from_currencylayer_api_by_date($current_date);

            print_r($exchange_rates_arr);

            if(isset($exchange_rates_arr['success']) && $exchange_rates_arr['success']==1)
            {
                $timestamp=$exchange_rates_arr['timestamp'];

                $date=date("Y-m-d",$timestamp);

                foreach($exchange_rates_arr['quotes'] as $k=>$v)
                {

                    $from_currency="USD";
                    $to_currency= str_replace("USD","",$k);
                    $rate=$v;

                    $input['date'] = $date;
                    $input['from_currency'] = $from_currency;
                    $input['to_currency'] = $to_currency;
                    $input['rate'] = $rate;

                    CurrencyExchangeRate::create($input);
                }
            }

            sleep(3);

            $timestamp1+=24*3600;

        }
    }

    public function get_exchange_rates_from_currencylayer_api()
    {
        CurrencyLayerExchangeRateApi::echo_printr("get_exchange_rates_from_currencylayer_api called");
        // set API Endpoint and Access Key (and any options of your choice)

        $params = ['access_key' => '36652c958ad364ee913d5f7eef29f296', 'date' => $this->todays_date];

        // Initialize CURL:
        $ch = curl_init('http://apilayer.net/api/historical?' . http_build_query($params));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Store the data:
        $json = curl_exec($ch);
        curl_close($ch);

        // Decode JSON response:
        $exchangeRates = json_decode($json, true);

        return $exchangeRates;
    }

    public function get_exchange_rates_from_currencylayer_api_by_date($date)
    {

        // set API Endpoint and Access Key (and any options of your choice)
        $endpoint = 'historical';
        $access_key = '36652c958ad364ee913d5f7eef29f296';

        // Initialize CURL:
        $ch = curl_init('http://apilayer.net/api/'.$endpoint.'?access_key='.$access_key.'&date='.$date.'');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Store the data:
        $json = curl_exec($ch);
        curl_close($ch);

        // Decode JSON response:
        $exchangeRates = json_decode($json, true);

        return $exchangeRates;
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