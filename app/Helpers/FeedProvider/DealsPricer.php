<?php
namespace App\Helpers\FeedProvider;

use App\Country;
use App\Helpers\FeedProvider;
use App\Helpers\FeedProviderDataNotFoundException;
use GrahamCampbell\Dropbox\Facades\Dropbox;

class DealsPricer extends FeedProvider
{
    protected $options = [
        'basePath' => null,
    ];

    public function process()
    {
        $data = $this->obtainResourceData();

        /**
         * expected format:
         *  0 Date,
         *  1 Partner Name,
         *  2 Sub ID,
         *  3 Country,
         *  4 User count,
         *  5 Impressions,
         *  6 Clicks,
         *  7 Local Currency Revenue,
         *  8 Vaa share in total Revenue,
         *  9 Local Currency Symbol
         */

        foreach ($data as $row) {
            list(, $dl_source) = explode('-', $row[2]);

            $currency = strtoupper($row[9]);
            $amount = (float) $row[7];
            $date = $this->normalizeDate($row[0]);
            list($revenueUSD, $rateUSD) = $this->currencyConvertToUSD($currency, $amount, $date);

            $this->data[] = [
                'date'          => $date,
                'dl_source'     => $dl_source,
                'sub_dl_source' => 'N/A',
                'country'       => $this->normalizeCountry($row[3]),
                'currency'      => $currency,
                'clicks'        => (int)$row[6],
                'estimated_revenue' => $amount,
                'cpc'           => empty((float) $row[6]) ? 0 : (float) $row[7] / (float) $row[6],
                'leads'         => (int) $row[4],
                'cts'           => null,
                'revenue_usd'   => $revenueUSD,
                'rate_usd'      => $rateUSD
            ];
        }
    }

    /**
     * Obtains resource data as array table
     *
     * @return array
     * @throws FeedProviderDataNotFoundException
     */
    protected function obtainResourceData()
    {
        //
        $metadata = Dropbox::getMetadataWithChildren($this->options['basePath']);

        $found = null; $lastTime = 0;

        foreach ($metadata['contents'] as $item) {
            if (null == $found) {
                $found = $item;
                $lastTime = strtotime($item['modified']);
            } else {
                if (strtotime($item['modified']) > $lastTime) {
                    $found = $item;
                    $lastTime = strtotime($item['modified']);
                }
            }
        }

        if (!$found) {
            throw new FeedProviderDataNotFoundException('Resource not found ');
        }

        $filename = $this->getTmpFolder().'/'.uniqid();
        $fp = fopen($filename, 'wb');

        Dropbox::getFile($found['path'], $fp);
        fclose($fp);

        $list = file($filename, FILE_IGNORE_NEW_LINES);
        array_shift($list);

        unlink($filename);

        $result = [];

        foreach ($list as $item) {
            $result[] = explode(',', $item);
        }

        return $result;
    }

    protected function normalizeDate($string)
    {
        return '20'.str_replace('/', '-', $string);
    }

    protected function normalizeCountry($name)
    {
        static $countries;
        if (null === $countries) {
            $countries = [];
            foreach (Country::orderBy('name','asc')->get() as $country) {
                $countries[$country->name] = $country->code;
            }
        }

        return isset($countries[$name]) ? $countries[$name] : 'N/A';
    }
}