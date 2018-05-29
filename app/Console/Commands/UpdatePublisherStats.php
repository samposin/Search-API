<?php

namespace App\Console\Commands;

use App\Advertiser;
use App\Models\AdvertiserStats;
use App\Models\PublisherStats;
use App\Publisher;
use Illuminate\Console\Command;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

class UpdatePublisherStats extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'revenue:publishers {publisher?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update publishers stats';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $period = [strtotime("-7 days"), time()];

        $publishers = Publisher::available()->get();

        foreach ($publishers as $publisher) {

            if ($this->argument('publisher') && $publisher->name != $this->argument('publisher')) {
                continue;
            }

            $this->info('Processing: '.$publisher->name);
            for ($time = $period[0]; $time < $period[1];) {

                $searchClickData = $this->getPublisherSearchClicksForDate($publisher, date('Y-m-d', $time));

                $revenueData = $this->getRevenueForPublisherDaily($publisher, date('Y-m-d', $time));

                $result = [];
                $countedKeys = [];

                foreach ($revenueData as $key => $value) {
                    $key = $this->decodeCompositeKey($key);
                    $matched = [];
                    $relativeAmount = 0;

                    foreach ($searchClickData as $searchClickKey => &$searchClickValue) {

                        if (isset($searchClickValue['rate'])) {
                            continue;
                        }

                        $keyCheck = $searchClickKey;
                        $searchClickKey = $this->decodeCompositeKey($searchClickKey);

                        if ($ppcRate = $this->checkRevenueDataForPPC($searchClickKey)) {
                            // hardcoding ppc calculation
                            // @todo: refactoring needed
                            $searchClickValue['revenue'] = $searchClickValue['clicks'] * $ppcRate;
                            $searchClickValue['rate'] = $ppcRate;
                        } elseif (($key['sub_dl_source'] == $searchClickKey['sub_dl_source'] || $key['sub_dl_source'] == 'N/A')
                            && $key['country'] == $searchClickKey['country'] && $key['advertiser_id'] == $searchClickKey['advertiser_id']
                        ) {
                            $matched[] = array_merge($searchClickKey, $searchClickValue);
                            $relativeAmount += $searchClickValue['clicks'];
                            $countedKeys[] = $keyCheck;
                        }
                    }

                    // update revenue and rates for matched keys
                    foreach ($matched as $one) {
                        $part = $relativeAmount
                            ? $one['clicks'] / $relativeAmount
                            : 0;
                        $one['revenue'] = $value['revenue'] * $value['rate'] / 100 * $part;
                        $one['rate'] = $value['rate'];
                        $result[] = $one;
                    }
                }

                foreach ($searchClickData as $searchClickKey => $searchClickValue) {
                    if (false === in_array($searchClickKey, $countedKeys)) {
                        $searchClickKey = $this->decodeCompositeKey($searchClickKey);
                        $result[] = array_merge($searchClickKey, $searchClickValue);
                    }
                }

                $this->updatePublisherStats($publisher, $result);

                $time = strtotime("next day", $time);
            }
        }
    }

    /**
     * Get count of searches and clicks referred by specified publisher in specified date
     * @param Publisher $publisher
     * @param $date
     * @return array
     */
    protected function getPublisherSearchClicksForDate(Publisher $publisher, $date)
    {
        $data = [];
        // collect searches
        /** @var Builder $query */
        $query = DB::table('search_request_all_new')
            ->selectRaw(
                'DATE(created_at) AS date, dl_source, sub_dl_source, user_country_code AS country, widget, api_used AS api, COUNT(*) AS searches'
            )
            ->whereBetween('created_at', [$date, $date.' 23:59:59'])
            ->where('dl_source', '=', $publisher->name)
            ->groupBy('date', 'dl_source', 'sub_dl_source', 'country', 'widget', 'api')
            ->orderBy('date', 'dl_source', 'sub_dl_source', 'country', 'widget', 'api');

        $result = $query->get();

        if (count($result)) {
            foreach ($result as $row) {
                $row->advertiser_id = $this->identifyAdvertiser($row);
                $key = $this->encodeCompositeKey(
                    ['date', 'dl_source', 'sub_dl_source', 'advertiser_id', 'country', 'widget'],
                    $row
                );
                $data[$key] = ['searches' => $row->searches, 'clicks' => 0];
            }
        }

        // collect clicks
        /** @var Builder $query */
        $query = DB::table('search_clicks')
            ->selectRaw(
                'DATE(created_at) AS date, dl_source, sub_dl_source, country_code AS country, widget, api, COUNT(*) AS clicks'
            )
            ->whereBetween('created_at', [$date, $date.' 23:59:59'])
            ->where('dl_source', '=', $publisher->name)
            ->groupBy('date', 'dl_source', 'sub_dl_source', 'country', 'widget', 'api')
            ->orderBy('date', 'dl_source', 'sub_dl_source', 'country', 'widget', 'api');

        $result = $query->get();

        if (count($result)) {
            foreach ($result as $row) {
                $row->advertiser_id = $this->identifyAdvertiser($row);
                $key = $this->encodeCompositeKey(
                    ['date', 'dl_source', 'sub_dl_source', 'advertiser_id', 'country', 'widget'],
                    $row
                );
                if (isset($data[$key])) {
                    $data[$key]['clicks'] = $row->clicks;
                } else {
                    $data[$key] = ['searches' => 0, 'clicks' => $row->clicks];
                }
            }
        }

        return $data;

    }

    protected function getRevenueForPublisherDaily(Publisher $publisher, $date)
    {
        $data = [];
        $shareInfo = [];

        foreach ($publisher->advertisers as $info) {
            if ($info->pivot->publisher_id1) {
                $shareInfo[$info->pivot->publisher_id1] = $info->pivot->share;
            }
        }

        // collect searches
        /** @var Builder $query */
        $query = DB::table('advertiser_stats')
            ->selectRaw('date, dl_source, sub_dl_source, advertiser_id, country, SUM(revenue_usd) AS revenue')
            ->where('date', '=', $date)
            ->whereIn('dl_source', array_keys($shareInfo))
            ->groupBy('date', 'dl_source', 'sub_dl_source', 'advertiser_id', 'country');

        $result = $query->get();

        if (count($result)) {
            foreach ($result as $row) {
                $key = $this->encodeCompositeKey(
                    ['date', 'dl_source', 'sub_dl_source', 'advertiser_id', 'country'],
                    $row
                );
                $rate = $shareInfo[$row->dl_source];
                $data[$key] = ['revenue' => $row->revenue, 'rate' => $rate ? $rate : 50];
            }
        }

        return $data;
    }

    protected function updatePublisherStats(Publisher $publisher, $data)
    {
        for ($i = 0; $i < count($data); $i++) {
            $row = $data[$i];

            $row['country'] = empty($row['country'])
                ? 'N/A'
                : $row['country'];

            $row['sub_dl_source'] = empty($row['sub_dl_source'])
                ? 'N/A'
                : $row['sub_dl_source'];

            /** @var PublisherStats $found */
            $found = PublisherStats::where('publisher_id', '=', $publisher->id)
                ->where('advertiser_id', '=', $row['advertiser_id'])
                ->where('date', '=', $row['date'])
                ->where('sub_dl_source', '=', $row['sub_dl_source'])
                ->where('country', '=', $row['country'])
                ->where('widget', '=', $row['widget'])
                ->first();

            if ($found) {
                $found->searches = $row['searches'];
                $found->clicks = $row['clicks'];
                $found->revenue = isset($row['revenue']) ? $row['revenue'] : 0;
                $found->rate = isset($row['rate']) ? $row['rate'] : null;
                $found->save();
            } else {
                $row['publisher_id'] = $publisher->id;
                PublisherStats::create($row);
            }
        }
    }

    private function encodeCompositeKey($vars, $data)
    {
        $result = [];
        foreach ($vars as $key) {
            $result[$key] = $data->$key;
        }

        return json_encode($result);
    }

    private function decodeCompositeKey($key)
    {
        return json_decode($key, true);
    }

    /**
     * @todo: refactoring needed
     * @param $key
     * @return bool|float
     */
    private function checkRevenueDataForPPC($key)
    {
        static $advert;

        if (null === $advert) {
            $advert = Advertiser::where('name', '=', 'Dealspricer')->first();
        }

        if (in_array(
                $key['country'],
                ['HK', 'IN', 'MY', 'PH', 'SG', 'TH', 'TR']
            ) && $key['advertiser_id'] == $advert->id
        ) {
            return .005;
        }

        return false;
    }

    private function identifyAdvertiser($row)
    {
        static $list;
        if (null === $list) {
            foreach (Advertiser::all() as $one) {
                $list[strtolower($one->name)] = $one->id;
            }
        }

        $name = strtolower($row->api);

        if (strtolower($row->api) == 'ebay') {
            $name = strtolower($row->api.' '.$row->country);
        }

        return isset($list[strtolower($name)])
            ? $list[strtolower($name)]
            : null;
    }
}
