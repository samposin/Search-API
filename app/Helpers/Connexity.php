<?php namespace App\Helpers;

use Akeneo\Component\SpreadsheetParser\SpreadsheetParser;
use App\Advertiser;
use App\VisionApiReport;
use DateTime;
use GrahamCampbell\Dropbox\Facades\Dropbox;

class Connexity {

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

        VisionApi::echo_printr("Start Connexity init at ".date("j F, Y, g:i a"));

        $this->excel_file_dropbox_folder_name='/iLeviathan-Reporting/Connexity';

        $this->excel_file_server_upload_path=base_path() . '/public/files/excels/uploads/Shopzilla/';

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

        usort($fileMetaArr, array("App\Helpers\Connexity", "date_compare"));

        return $fileMetaArr[0];
    }

    public function downloadFileFromDropbox()
    {
        if(count($this->dropbox_latest_file_info)>0) {

            $this->excel_file_server_upload_file_name=basename($this->dropbox_latest_file_info['path']);

            $this->excel_file_server_upload_file_path = $this->excel_file_server_upload_path . $this->excel_file_server_upload_file_name;

            $fd = fopen($this->excel_file_server_upload_file_path, "wb");

            $getFileMetadata = Dropbox::getFile($this->dropbox_latest_file_info['path'], $fd);

            VisionApi::echo_printr("Downloaded file from Shopzilla Dropbox named as ".$this->excel_file_server_upload_file_name." on ".date("j F, Y, g:i a"));

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

        $advertisers = Advertiser::where('name', '=', 'Shopzilla')->where('is_delete', '=', 0)->with(array('publishers' => function($query)
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


            for($i=0;$i<count($csv_data);$i++)
            {
                $result=$csv_data[$i];


                $result_date=$result['Date'];
	            $result_date_db=$result['Date'];



	            if($result_date!="") {
		            $result_date = str_replace('/', '-', $result_date);
		            $result_date_exp = explode('-', $result_date);

		            if (count($result_date_exp) == 3) {

		                $mm=$result_date_exp[0];
                        $dd=$result_date_exp[1];
                        $yy=$result_date_exp[2];

                        if (strlen($yy) == 2)
                            $yy = '20' . $yy;

                        $result_date = $dd . '-' . $mm . '-' . $yy;
                        $result_date_db=$yy . '-' . $mm . '-' . $dd;
                        VisionApi::echo_printr($result_date,"result_date");
                        VisionApi::echo_printr($result_date_db,"result_date_db");

			            $query1 = \DB::table('search_clicks as a')->selectRaw('SUM(clicks) AS total_clicks1,a.*')
			            ->where('date', '=', $result_date_db)
			            ->where('api', '=', strtolower('connexity'))
			            ->groupBy('api')
			            ->groupBy('dl_source')
			            ->groupBy('sub_dl_source')
			            ->groupBy('widget')
			            ->groupBy('country_code')
			            ->groupBy('date')
			            ->orderBy('date');

			            $query2 = \DB::table(\DB::raw("( {$query1->toSql()} ) as totalS"))
			            ->mergeBindings($query1)
			            ->selectRaw('SUM(totalS.total_clicks1) AS total_clicks,totalS.api,totalS.country_code,totalS.date');

			            $db_reports2 = $query2->first();
			            $db_reports1 = $query1->get();

			            if ($db_reports2->total_clicks == null) {
				            VisionApi::echo_printr("total_clicks is null");
				            $total_clicks_db = 0;
			            }
			            else {
				            VisionApi::echo_printr("total_clicks is not null");
				            $total_clicks_db = $db_reports2->total_clicks;
			            }

			            if ($total_clicks_db != 0) {

				            VisionApi::echo_printr($total_clicks_db, "total_clicks_db");
				            VisionApi::echo_printr(count($db_reports1), "count db_reports1");

				            if (count($db_reports1) > 0) {

					            $total_records = count($db_reports1);
					            $searches = "";

					            $db_reports = json_decode(json_encode($db_reports1), true);

					            VisionApi::echo_printr(count($db_reports), "count db_reports");

				            }
			            }
		            }
	            }
            }
        }
    }
}