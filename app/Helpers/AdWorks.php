<?php namespace App\Helpers;

use Akeneo\Component\SpreadsheetParser\SpreadsheetParser;
use App\Advertiser;
use App\Country;
use App\VisionApiReport;
use DateTime;
use GrahamCampbell\Dropbox\Facades\Dropbox;

class AdWorks {

    private $dropbox_latest_file_info="";

    private $excel_file_dropbox_folder_name="";
    private $excel_file_server_upload_path="";
    private $excel_file_server_upload_file_path="";
    private $excel_file_server_upload_file_name="";

    private $db_publishers_arr=array();

    public function __construct()
    {
        CurrencyLayerExchangeRateApi::echo_printr("");
    }

    function __destruct()
    {
        CurrencyLayerExchangeRateApi::echo_printr("");
    }

    function date_compare($a, $b)
    {
        $t1 = strtotime($a['modified_datetime']);
        $t2 = strtotime($b['modified_datetime']);
        return $t2 - $t1;
    }

    public function init()
    {

        VisionApi::echo_printr("Start AdWorks init at " . date("j F, Y, g:i a"));

        $this->excel_file_dropbox_folder_name = '/Reporting/AdWorks';

        $this->excel_file_server_upload_path=base_path() . '/public/files/excels/uploads/AdWorks/';

        $this->dropbox_latest_file_info=$this->getLatestFileInfoFromDropbox();

        VisionApi::echo_printr($this->dropbox_latest_file_info,"Latest file info");

        if($this->downloadFileFromDropbox())
        {
            $this->processExcelAndSaveInDB();
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

        usort($fileMetaArr, array("App\Helpers\AdWorks", "date_compare"));

        return $fileMetaArr[0];
    }

    public function downloadFileFromDropbox()
    {
        if(count($this->dropbox_latest_file_info)>0) {

            $this->excel_file_server_upload_file_name=basename($this->dropbox_latest_file_info['path']);

            $this->excel_file_server_upload_file_path = $this->excel_file_server_upload_path . $this->excel_file_server_upload_file_name;

            $fd = fopen($this->excel_file_server_upload_file_path, "wb");

            $getFileMetadata = Dropbox::getFile($this->dropbox_latest_file_info['path'], $fd);

            VisionApi::echo_printr("Downloaded file from AdWorks Dropbox named as ".$this->excel_file_server_upload_file_name." on ".date("j F, Y, g:i a"));

            fclose($fd);

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

        $advertisers = Advertiser::where('name', '=', 'AdWorks')->where('is_delete', '=', 0)->with(array('publishers' => function($query)
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

    public function processExcelAndSaveInDB()
    {
        if(file_exists($this->excel_file_server_upload_file_path))
        {
            $this->db_publishers_arr = $this->getPublishersInfo();

            VisionApi::echo_printr($this->db_publishers_arr,"Publishers info");

            $csv_data=$this->getCsvData();

            VisionApi::echo_printr($csv_data,"CSV data");

            for($i=0;$i<count($csv_data);$i++)
            {
                $result=$csv_data[$i];

                $country_name=strtoupper($result['Geo']);

                $subid = $result['SUBID'];
                $subid_name="";
                $publisher_share=0;
                $publisher_share=37.5;

                $dt1=strtotime($result['Date']);
                $date=date("Y-m-d",$dt1);


                if(isset($this->db_publishers_arr['adworks'][strtolower($subid)]['name']))
                {
                    if(trim($this->db_publishers_arr['adworks'][strtolower($subid)]['name'])!="")
                    {
                        $subid_name=trim($this->db_publishers_arr['adworks'][strtolower($subid)]['name']);
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

                VisionApi::echo_printr($subid,"subid");
                VisionApi::echo_printr($publisher_share,"publisher_share");

                $payout_str = $result['Income'];
                VisionApi::echo_printr($payout_str,"Original Payout");
                $payout = $payout_str;

                if($publisher_share>0)
                    $payout = ($payout) * ($publisher_share / 100);


                $payout_str = $payout;

                VisionApi::echo_printr($payout_str,"Final Payout");

                $input['date'] = $date;
                //$input['dl_source'] = 'VisionAPI';
                $input['dl_source'] = $subid_name;
                $input['sub_dl_source'] = $subid_name;
                $input['widget'] = 'Career Search';
                $input['country'] = $country_name;
                $input['searches'] = '';
                $input['clicks'] = $result['Clicks'];
                $input['estimated_revenue'] = $payout_str;
                $input['symbol'] = $result['Symbol'];
                $input['file_output_type'] = 1;
                $input['advertiser_name'] = 'AdWorks';

                VisionApiReport::create($input);

            }
        }
    }
}