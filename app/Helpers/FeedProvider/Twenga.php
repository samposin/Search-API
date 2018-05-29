<?php
namespace App\Helpers\FeedProvider;

use App\Helpers\FeedProvider;
use App\Helpers\FeedProviderDataNotFoundException;
use App\Helpers\FeedProviderResourceConnectionErrordException;
use App\Helpers\FeedProviderResourceDownloadFailed;
use Touki\FTP\Connection\Connection as FTPConnection;
use Touki\FTP\Exception\ConnectionException;
use Touki\FTP\FTPFactory;
use Touki\FTP\FTPWrapper;
use Touki\FTP\Model\Directory;

class Twenga extends FeedProvider
{
    protected $options = [
        'connection' => [
            'server' => '',
            'username' => '',
            'password' => '',
        ],
    ];

    public function process()
    {
        $data = $this->obtainResourceData();

        if ($data) {
            foreach ($data as $row) {
                $row = explode("\t", $row);

                $currency = 'EUR';
                $amount = (float) $row[7];
                $date = $row[0];
                list($revenueUSD, $rateUSD) = $this->currencyConvertToUSD($currency, $amount, $date);

                $this->data[] = [
                    'date'          => $date,
                    'dl_source'     => $row[2], //SUB_ID
                    'sub_dl_source' => 'N/A',
                    'country'       => $row[1], //GEOZONE
                    'currency'      => $currency,
                    'clicks'        => (int)$row[3], //N_CLICK_VALID
                    'estimated_revenue' => $amount, //REVENUE_EUR
                    'cpc'           => (float)$row[8], //ECPC
                    'cts'           => (float) $row[11], //CVR_1DAY
                    'revenue_usd'   => $revenueUSD,
                    'rate_usd'      => $rateUSD
                ];
            }
        } else {
            throw new FeedProviderDataNotFoundException('Can not find last file or resource data is empty');
        }
    }

    protected function obtainResourceData(array $period = null)
    {
        if (null === $period) {
            $period = [
                date('Y-m-d', strtotime('-7 days')),
                date('Y-m-d'),
            ];
        }

        $params = $this->options['connection'];
        $connection = new FTPConnection($params['server'], $params['username'], $params['password'], 21, 90, true);

        try {
            $connection->open();
        } catch (ConnectionException $e) {
            throw new FeedProviderResourceConnectionErrordException('Unable to connect Twega resource');
        }

        $factory = new FTPFactory($connection);
        $ftp = $factory->build($connection);

        $files = $ftp->findFiles(new Directory("/subid"));
        $matched = [];

        /** @var \Touki\FTP\Model\File $file */
        foreach ($files as $file) {
            for ($i=strtotime($period[0]); $i<=strtotime($period[1]);) {
                $date = date('Y-m-d', $i);
                if (strstr($file->getRealpath(), $date)) {
                    $matched[] = $file;
                }
                $i = strtotime('next day', $i);
            }
        }

        $local = $this->getTmpFolder() . '/twenga.csv';
        $result = [];
        /** @var \Touki\FTP\Model\File $file */
        foreach ($matched as $file) {
            $ftp->download($local, $file);
            $data = file($local, FILE_IGNORE_NEW_LINES);
            if ($data && sizeof($data) > 1) {
                array_shift($data);

                foreach ($data as $row) {
                    $result[] = $row;
                }
            }
        }

        $connection->close();

        return $result;
    }
}