<?php

namespace App\Helpers\FeedProvider;

use App\Helpers\FeedProvider;
use App\Helpers\FeedProviderDataNotFoundException;
use GrahamCampbell\Dropbox\Facades\Dropbox;

class Ebay extends FeedProvider
{
    protected $options = [
        'basePath' => '/iLeviathan-Reporting/Ebay',
    ];

    public function process()
    {
        $data = $this->obtainResourceData();

        if ($data) {
            foreach ($data as $row) {
                $row = explode(',', $row);

                $country = $row[9];

                // supports separate per country publisher account
                if ($country != $this->options['country']) {
                    continue;
                }

                $currency = $this->normalizeCurrency(strtoupper($row[5]));
                $amount = (float) $row[4];
                $date = $row[0];
                list($revenueUSD, $rateUSD) = $this->currencyConvertToUSD($currency, $amount, $date);

                $this->data[] = [
                    'date'          => $date,
                    'dl_source'     => $row[1],
                    'sub_dl_source' => $row[3],
                    'country'       => $country,
                    'currency'      => $currency,
                    'clicks'        => (int)$row[6],
                    'estimated_revenue' => $amount,
                    'cpc'           => (float)$row[7],
                    'cts'           => empty($row[8]) ? null : (float) $row[8],
                    'revenue_usd'   => $revenueUSD,
                    'rate_usd'      => $rateUSD
                ];
            }
        } else {
            throw new FeedProviderDataNotFoundException('Resource data is empty');
        }
    }

    /**
     * @return array
     * @throws FeedProviderDataNotFoundException
     */
    protected function obtainResourceData()
    {
        $metadata = Dropbox::getMetadataWithChildren($this->options['basePath']);
        $found = null; $lastTime = 0;

        foreach ($metadata['contents'] as $item) {
            if (preg_match('/\s(\d{4}\-\d{2}\-\d{2})/', $item['path'], $matches)) {
                if (null == $found) {
                    $found = $item;
                    $lastTime = strtotime($matches[1]);
                } else {
                    if (strtotime($matches[1]) > $lastTime) {
                        $found = $item;
                        $lastTime = strtotime($matches[1]);
                    }
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
        unlink($filename);

        array_shift($list);

        return $list;
    }

    /**
     * Bring currency string to local format. If string can not be normalized function return it with no changes
     *
     * @param $string
     * @return int|string
     */
    protected function normalizeCurrency($string)
    {
        $map = [
            'EUR' => 'EURO',
            'USD' => 'USD'
        ];

        if (empty($string)) {
            return 'USD';
        }

        foreach ($map as $key => $value) {
            if ($value == $string) {
                return $key;
            }
        }

        return $string;
    }
}