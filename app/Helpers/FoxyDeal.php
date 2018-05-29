<?php namespace App\Helpers;

use App\Advertiser;
use App\Publisher;
use App\VisionApiReport;
use DateTime;
use GrahamCampbell\Dropbox\Facades\Dropbox;
use Maatwebsite\Excel\Facades\Excel;

class FoxyDeal {

    private $dropbox_latest_file_info="";

    private $excel_file_dropbox_folder_name="";
    private $excel_file_server_upload_path="";
    private $excel_file_server_upload_file_path="";
    private $excel_file_server_upload_file_name="";

    private $db_publishers_arr=array();

    function date_compare($a, $b)
    {
        $t1 = strtotime($a['modified_datetime']);
        $t2 = strtotime($b['modified_datetime']);
        return $t2 - $t1;
    }

    public function init()
    {

        VisionApi::echo_printr("Start FoxyDeal init at ".date("j F, Y, g:i a"));


        $this->excel_file_dropbox_folder_name='/Reporting/FoxyDeal';

        $this->excel_file_server_upload_path=base_path() . '/public/files/excels/uploads/FoxyDeal/';

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
                $dt = new DateTime($row['modified']);
                $sNewFormat = $dt->format("Y-m-d H:i:s");
                $row['modified_datetime']=$sNewFormat;

                $fileMetaArr[]=$row;
            }
        }
        else
        {
            $fileMetaArr[0]=array();
        }

        usort($fileMetaArr, array("App\Helpers\FoxyDeal", "date_compare"));

        return $fileMetaArr[0];
    }

    public function downloadFileFromDropbox()
    {
        if(count($this->dropbox_latest_file_info)>0) {

            $this->excel_file_server_upload_file_name=basename($this->dropbox_latest_file_info['path']);

            $this->excel_file_server_upload_file_path = $this->excel_file_server_upload_path . $this->excel_file_server_upload_file_name;

            $fd = fopen($this->excel_file_server_upload_file_path, "wb");

            $getFileMetadata = Dropbox::getFile($this->dropbox_latest_file_info['path'], $fd);

            VisionApi::echo_printr("Downloaded file from FoxyDeal Dropbox named as ".$this->excel_file_server_upload_file_name." on ".date("j F, Y, g:i a"));

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

        $advertisers = Advertiser::where('name', '=', 'FoxyDeal')->where('is_delete', '=', 0)->with(array('publishers' => function($query)
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

    public function processExcelAndSaveInDB()
    {


        if(file_exists($this->excel_file_server_upload_file_path))
        {
            VisionApi::echo_printr("File Exists");
            $this->db_publishers_arr=$this->getPublishersInfo();

            VisionApi::echo_printr($this->db_publishers_arr,"Publishers info");

            Excel::selectSheets('Conversions')->load($this->excel_file_server_upload_file_path, function($reader)
            {

                $results = $reader->get();

                $counter=1;

                foreach ($results as $result) {

                    $subid = $result['SUB ID'];
                    $subid_name="";
                    $conversionid = $result['Conversion ID'];
                    $time = $result['Time'];

                    $country = $result['Country'];

                    $publisher_share=0;
                    $publisher_share=37.5;

                    VisionApi::echo_printr($subid,"subid");
                    VisionApi::echo_printr($country,"country");

                    if (strtoupper($subid) != "WSKY") {



                        if(isset($this->db_publishers_arr['foxydeal'][strtolower($subid)]['name']))
		                {
		                    if(trim($this->db_publishers_arr['foxydeal'][strtolower($subid)]['name'])!="")
		                    {
		                        $subid_name=trim($this->db_publishers_arr['foxydeal'][strtolower($subid)]['name']);
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

                        VisionApi::echo_printr($publisher_share,"publisher_share");

                        $time_str = "";
                        $time_exp = explode(" ", $time);
                        if (count($time_exp) == 2)
                            $time_str = $time_exp[0];


                        $payout_str = $result['Payout'];
                        VisionApi::echo_printr($payout_str,"Original Payout");
                        $payout = $payout_str;

                        if($publisher_share>0)
                            $payout = ($payout) * ($publisher_share / 100);


                        $payout_str = $payout;

                        VisionApi::echo_printr($payout_str,"Final Payout");

                        $input['date'] = $time_str;

                        $input['dl_source'] = $subid_name;
                        $input['sub_dl_source'] = $subid_name;
                        $input['widget'] = 'Saving Bar';
                        $input['country'] = $country;
                        $input['searches'] = '';
                        $input['clicks'] = 1;
                        $input['estimated_revenue'] = $payout_str;
                        $input['symbol'] = 'USD';
                        $input['file_output_type'] = 1;
                        $input['advertiser_name'] = 'FoxyDeal';

                        VisionApiReport::create($input);
                        VisionApi::echo_printr($counter,"counter");
                        $counter++;

                    }
                }
            });
        }
    }
}