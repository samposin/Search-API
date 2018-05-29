<?php namespace App\Helpers;

use Akeneo\Component\SpreadsheetParser\SpreadsheetParser;
use App\Advertiser;
use App\VisionApiReport;
use Touki\FTP\Connection\Connection;
use Touki\FTP\FTPFactory;
use Touki\FTP\FTPWrapper;
use Touki\FTP\Model\Directory;

class Twenga {

    private $ftp_latest_file_info;
    private $excel_file_server_upload_path="";
    private $excel_file_server_upload_file_path="";
    private $excel_file_server_upload_file_name="";
    private $db_publishers_arr=array();

    public function init()
    {
        VisionApi::echo_printr("Start Twenga init at ".date("j F, Y, g:i a"));

        $this->excel_file_server_upload_path=base_path() . '/public/files/excels/uploads/Twenga/';

        $this->ftp_latest_file_info=$this->getLatestFileInfoFromFtp();

        VisionApi::echo_printr($this->ftp_latest_file_info,"Latest file info");


        if($this->downloadFileFromFtp())
        {
            $this->processExcelAndSaveInDB();
        }

    }

    function date_compare($a, $b)
    {
        $t1 = strtotime($a['datetime']);
        $t2 = strtotime($b['datetime']);
        return $t2 - $t1;
    }

    public function getLatestFileInfoFromFtp()
    {
        $connection = new Connection('ftp-01.twa.com', 'vionapi', '1eSAiSJf2qKo', $port = 21, $timeout = 90, $passive = true);

        $connection->open();

        $factory = new FTPFactory;
        $ftp = $factory->build($connection);




        $list = $ftp->findFilesystems(new Directory("/subid"));

        $ftp_file_arr=array();

        foreach($list as $l)
        {

            $realPath=$l->getRealpath();
            $pathinfo=pathinfo($realPath);
            $fileName=$pathinfo['filename'];
            $ext=$pathinfo['extension'];

            $fileNameExp=explode('_',$fileName);

            $date=$fileNameExp[2];

            $ftp_file_arr[]=array("real_path"=>$realPath,"datetime"=>$date.' 00:00:00');

        }

        if(count($ftp_file_arr)==0)
        {
            $ftp_file_arr[0]=array();
        }

        usort($ftp_file_arr, array("App\Helpers\Twenga", "date_compare"));

        $connection->close();

        return $ftp_file_arr[0];

    }

    public function downloadFileFromFtp()
    {
        if(count($this->ftp_latest_file_info)>0) {

            $connection = new Connection('ftp-01.twa.com', 'vionapi', '1eSAiSJf2qKo', $port = 21, $timeout = 90, $passive = true);
            $connection->open();

            $factory = new FTPFactory;
            $ftp = $factory->build($connection);

            $this->excel_file_server_upload_file_name=basename($this->ftp_latest_file_info['real_path']);

            $this->excel_file_server_upload_file_path = $this->excel_file_server_upload_path . $this->excel_file_server_upload_file_name;

            $file = $ftp->findFileByName($this->ftp_latest_file_info['real_path']);

            // To a file
            $ftp->download($this->excel_file_server_upload_file_path, $file);

            // To an handle
            ////$handle = fopen($this->excel_file_server_upload_file_path, "wb");
            //$handle = fopen('/path/to/download/file1.txt', 'w+');
            ////$ftp->download($handle, $file);

            VisionApi::echo_printr("Downloaded file from Twenga Ftp named as ".$this->excel_file_server_upload_file_name." on ".date("j F, Y, g:i a"));

            $connection->close();
            return true;
        }
        else
        {
            return false;
        }
    }

    public function getPublishersInfo()
    {

        $publishers_arr=[];

        $advertisers = Advertiser::where('name', '=', 'Twenga')->where('is_delete', '=', 0)->with(array('publishers' => function($query)
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

    public function getCsvData()
    {

        $workbook = SpreadsheetParser::open($this->excel_file_server_upload_file_path);

        $iterator = $workbook->createRowIterator(
            0,
            [
                //'encoding'  => 'UTF-8',
                'length'    => null,
                'delimiter' => "\t",
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

    public function processExcelAndSaveInDB()
    {
        if(file_exists($this->excel_file_server_upload_file_path))
        {
            $this->db_publishers_arr = $this->getPublishersInfo();

            VisionApi::echo_printr($this->db_publishers_arr,"Publishers info");

            $csv_data=$this->getCsvData();



            $exchange_rates=VisionApi::$currency_exchange_rates;



            for($i=0;$i<count($csv_data);$i++)
            {
                $result=$csv_data[$i];

                $symbol = 'EUR';
                $subid = $result['SUB_ID'];
                $subid_name="";
                $publisher_share=0;

                $dt1=strtotime($result['DATE']);
                $date=date("Y-m-d",$dt1);

                if(isset($this->db_publishers_arr['twenga'][strtolower($subid)]['pivot']['share']))
                {
                    if($this->db_publishers_arr['twenga'][strtolower($subid)]['pivot']['share']>0)
                    {
                        $publisher_share=$this->db_publishers_arr['twenga'][strtolower($subid)]['pivot']['share'];
                    }
                }

                if(isset($this->db_publishers_arr['twenga'][strtolower($subid)]['name']))
                {
                    if(trim($this->db_publishers_arr['twenga'][strtolower($subid)]['name'])!="")
                    {
                        $subid_name=trim($this->db_publishers_arr['twenga'][strtolower($subid)]['name']);
                    }
                    else
                    {
                        $subid_name=$subid;
                    }
                }
                else
                {
                    $subid_name=$subid;
                }



                $payout_str = $result['REVENUE_EUR'];
                VisionApi::echo_printr($payout_str,"Original Payout");
                $payout = $payout_str;



                if(trim(strtoupper($symbol))!="") {
                    if (isset($exchange_rates[$date][strtoupper($symbol)]) && $exchange_rates[$date][strtoupper($symbol)]>0) {
                        $payout = $payout / $exchange_rates[$date][strtoupper($symbol)];
                    }
                }
                VisionApi::echo_printr($payout,"Payout after conversion");


                if($publisher_share>0)
                    $payout = ($payout) * ($publisher_share / 100);



                $payout_str = $payout;
                VisionApi::echo_printr($payout_str,"Final Payout");


                $input['date'] = $date;

                $input['dl_source'] = $subid_name;
                $input['sub_dl_source'] = $subid_name;
                $input['widget'] = '';
                $input['country'] = $result['GEOZONE'];
                $input['searches'] = '';
                $input['clicks'] = $result['N_CLICK_VALID'];
                $input['estimated_revenue'] = $payout_str;
                $input['symbol'] = 'USD';
                $input['file_output_type'] = 2;
                $input['advertiser_name'] = 'Twenga';

                VisionApiReport::create($input);

            }
        }
    }
}