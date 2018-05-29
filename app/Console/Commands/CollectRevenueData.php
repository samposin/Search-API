<?php

namespace App\Console\Commands;

use App\Advertiser;
use App\Helpers\FeedProvider\Twenga;
use App\Models\AdvertiserStats;
use GrahamCampbell\Dropbox\Facades\Dropbox;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;

/**
 * Class CollectRevenueData
 * @package App\Console\Commands
 * @todo: logging
 */
class CollectRevenueData extends Command
{
    protected $signature = 'revenue:collect {advertiser?}';

    protected $description = '';

    public function handle()
    {
        $filter = [
            'advertiser' => $this->argument('advertiser')
                ? explode(',', strtolower($this->argument('advertiser')))
                : null
        ];

        /** @var Collection $adverts */
        $adverts = Advertiser::available()->get();


        /** @var Advertiser $advert */
        foreach ($adverts as $advert) {

            if ($filter['advertiser'] && !in_array(strtolower($advert->name), $filter['advertiser'])) {
                continue;
            }

            $resources = $advert->resources;
            /** @var \App\Models\AdvertiserResource $resource */
            foreach ($resources as $resource) {

                try {
                    $data = $resource->feedProvider()->getData();
                    $this->processAdvertiserData($advert, $data);
                    $this->info($resource->name . ' done');
                } catch (\Exception $e) {
                    $this->error($resource->name . ' resource data processing error: '.$e->getMessage());
                }

            }
        }
    }

    protected function processAdvertiserData($advertiser, $data)
    {
        foreach ($data as $row) {
            /** @var AdvertiserStats $statsRow */
            $statsRow = \App\Models\AdvertiserStats::where('advertiser_id', $advertiser->id)
                ->where('date', $row['date'])
                ->where('dl_source', $row['dl_source'])
                ->where('sub_dl_source', $row['sub_dl_source'])
                ->where('country', $row['country'])
                ->where('currency', $row['currency'])
                ->first();
            if ($statsRow) {
                $statsRow->clicks = $row['clicks'];
                $statsRow->estimated_revenue = $row['estimated_revenue'];
                $statsRow->cpc = $row['cpc'];
                $statsRow->cts = isset($row['cts']) ? $row['cts'] : null;
                $statsRow->revenue_usd = isset($row['revenue_usd']) ? $row['revenue_usd'] : 0;
                $statsRow->rate_usd = isset($row['rate_usd']) ? $row['rate_usd'] : 0;
                $statsRow->save();
            } else {
                $row['advertiser_id'] = $advertiser->id;
                $statsRow = new AdvertiserStats();
                $statsRow->setRawAttributes([
                    'advertiser_id' => $advertiser->id,
                    'date' => $row['date'],
                    'dl_source' => $row['dl_source'],
                    'sub_dl_source' => $row['sub_dl_source'],
                    'country' => $row['country'],
                    'currency' => $row['currency'],
                    'clicks' => $row['clicks'],
                    'estimated_revenue' => $row['estimated_revenue'],
                    'cpc' => $row['cpc'],
                    'cts' => isset($row['cts']) ? $row['cts'] : null,
                    'revenue_usd' => isset($row['revenue_usd']) ? $row['revenue_usd'] : 0,
                    'rate_usd' => isset($row['rate_usd']) ? $row['rate_usd'] : 0
                ]);
                $statsRow->save();
            }
        }
    }
}