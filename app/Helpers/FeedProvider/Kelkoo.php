<?php
/**
 * Created by PhpStorm.
 * User: Alexander
 * Date: 18.08.2016
 * Time: 5:04
 */

namespace App\Helpers\FeedProvider;


use App\Helpers\FeedProvider;
use App\Helpers\FeedProviderResourceDownloadFailed;
use App\Helpers\FeedProviderWrongResourceFormat;
use Curl\Curl;

class Kelkoo extends FeedProvider
{
    // require options:
    // url, pageType, username, password, currency, from, to
    public function process()
    {
        $xml = $this->obtainResourceData();
        $rows = $xml->xpath('/trackings/tracking');

        foreach ($rows as $row) {

            $currency = strtoupper($row->currency);
            $amount = (float) $row->revenue;
            $date = (string) $row->day;
            list($revenueUSD, $rateUSD) = $this->currencyConvertToUSD($currency, $amount, $date);

            $this->data[] = [
                'date'          => $date,
                'dl_source'     => urldecode((string) $row->Custom1),
                'sub_dl_source' => urldecode((string) $row->Custom2),
                'country'       => strtoupper($row->country),
                'currency'      => $currency,
                'clicks'        => 0,
                'estimated_revenue' => $amount,
                'cpc'           => 0,
                'leads'         => (int) $row->numberOfLeads,
                'cts'           => null,
                'revenue_usd'   => $revenueUSD,
                'rate_usd'      => $rateUSD
            ];
        }
    }

    /**
     * @param array $period
     * @return \SimpleXMLElement
     * @throws FeedProviderResourceDownloadFailed
     * @throws FeedProviderWrongResourceFormat
     */
    protected function obtainResourceData($period = null)
    {
        if (null === $period) {
            $timestamp = strtotime('yesterday');
            $period = [
                date('Y-m-01', $timestamp),
                date('Y-m-d', $timestamp),
            ];
        }

        $params = [
            'pageType' => $this->options['pageType'],
            'username' => $this->options['username'],
            'password' => $this->options['password'],
            'currency' => $this->options['currency'],
            'from'     => $period[0],
            'to'       => $period[1],
        ];
        $url = $this->options['url'].'?'.http_build_query($params);

        $curl = new Curl();
        $curl->setOpt(CURLOPT_SSL_VERIFYPEER, false);

        try {
            $curl->get($url);
        } catch (\Exception $e) {
            $curl->close();
            throw new FeedProviderResourceDownloadFailed('Kelkoo resource download failed: ' . $url);
        }

        if (false == $curl->response instanceof \SimpleXMLElement) {
            throw new FeedProviderWrongResourceFormat('Wrong resource format, XML expected');
        }

        return $curl->response;
    }
}