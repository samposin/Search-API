<?php
/**
 * Created by PhpStorm.
 * User: Alexander
 * Date: 05.09.2016
 * Time: 16:19
 */

namespace App\Helpers\FeedProvider;


use App\Helpers\FeedProvider;
use App\Helpers\FeedProviderDataNotFoundException;
use GrahamCampbell\Dropbox\Facades\Dropbox;

class Connexity extends FeedProvider
{
    protected $options = [
        'basePath' => '/iLeviathan-Reporting/Connexity',
    ];

    /**
     * @throws FeedProviderDataNotFoundException
     * @todo: complete Connexity::process
     */
    public function process()
    {
        $data = $this->obtainResourceData();

        if (empty($data)) {
            throw new FeedProviderDataNotFoundException('Resource data is empty');
        }

        // 0 DATE	1 PLACEMENT	2 IMPRESSI	3 REDIRECT	4 EARNINGS
        foreach ($data as $row) {
            $this->data[] = [
                'date'          => $row[0],
                'dl_source'     => urldecode($row[1]),
                'sub_dl_source' => 'N/A',
                'country'       => 'US',
                'currency'      => 'USD',
                'clicks'        => 0,
                'estimated_revenue' => (float) $row[4],
                'cpc'           => 0,
                'leads'         => null,
                'cts'           => null,
                'revenue_usd'   => (float) $row[4],
                'rate_usd'      => 1
            ];
        }
    }

    protected function obtainResourceData($period = null)
    {
        $metadata = Dropbox::getMetadataWithChildren($this->options['basePath']);
        if (null === $period) {
            $period = [
                date('Y-m-01'),
                date('Y-m-d')
            ];
        }

        $found = [];

        foreach ($metadata['contents'] as $item) {
            if (preg_match('/connexity\-\w+\,\s(\d+\s\w+\s\d+)/i', $item['path'], $matches)) {
                $source_timestamp = strtotime($matches[1]);
                if ($source_timestamp >= strtotime($period[0]) && $source_timestamp <= strtotime($period[1])) {
                    $found[] = $item;
                }
            }
        }

        if (!$found) {
            throw new FeedProviderDataNotFoundException('Resource not found ');
        }


        $data = [];

        foreach ($found as $item) {
            $filename = $this->getTmpFolder().'/'.uniqid();
            $fp = fopen($filename, 'wb');

            Dropbox::getFile($item['path'], $fp);
            fclose($fp);

            $list = file($filename, FILE_IGNORE_NEW_LINES);
            unlink($filename);

            array_shift($list);

            foreach ($list as $row) {
                $data[] = str_getcsv($row);
            }
        }

        return $data;

    }
}