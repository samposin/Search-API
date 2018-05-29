<?php

use Illuminate\Database\Seeder;

class AdvertiserSearchDefaultsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('advertiser_search_defaults')->delete();
        
        \DB::table('advertiser_search_defaults')->insert(array (
            0 => 
            array (
                'id' => 1,
                'geo' => 'AT',
                'main_api' => 'kelkoo',
                'first_backfill_api' => '',
                'second_backfill_api' => '',
                'created_at' => '2016-03-26 20:14:30',
                'updated_at' => '2016-05-14 18:32:12',
            ),
            1 => 
            array (
                'id' => 2,
                'geo' => 'AU',
                'main_api' => 'dealspricer',
                'first_backfill_api' => '',
                'second_backfill_api' => '',
                'created_at' => '2016-03-26 20:14:32',
                'updated_at' => '2016-06-23 12:35:48',
            ),
            2 => 
            array (
                'id' => 3,
                'geo' => 'BR',
                'main_api' => 'zoom',
                'first_backfill_api' => 'kelkoo',
                'second_backfill_api' => 'twenga',
                'created_at' => '2016-03-26 20:14:35',
                'updated_at' => '2016-05-31 21:01:22',
            ),
            3 => 
            array (
                'id' => 4,
                'geo' => 'CH',
                'main_api' => 'kelkoo',
                'first_backfill_api' => '',
                'second_backfill_api' => '',
                'created_at' => '2016-03-26 20:14:37',
                'updated_at' => '2016-03-26 20:14:37',
            ),
            4 => 
            array (
                'id' => 5,
                'geo' => 'DE',
                'main_api' => 'kelkoo',
                'first_backfill_api' => 'twenga',
                'second_backfill_api' => 'twenga',
                'created_at' => '2016-03-26 20:14:39',
                'updated_at' => '2016-06-23 12:34:06',
            ),
            5 => 
            array (
                'id' => 6,
                'geo' => 'DK',
                'main_api' => 'kelkoo',
                'first_backfill_api' => '',
                'second_backfill_api' => '',
                'created_at' => '2016-03-26 20:14:57',
                'updated_at' => '2016-03-26 20:14:57',
            ),
            6 => 
            array (
                'id' => 7,
                'geo' => 'ES',
                'main_api' => 'kelkoo',
                'first_backfill_api' => 'twenga',
                'second_backfill_api' => '',
                'created_at' => '2016-03-26 20:14:59',
                'updated_at' => '2016-04-21 06:11:22',
            ),
            7 => 
            array (
                'id' => 8,
                'geo' => 'FI',
                'main_api' => 'kelkoo',
                'first_backfill_api' => '',
                'second_backfill_api' => '',
                'created_at' => '2016-03-26 20:15:03',
                'updated_at' => '2016-03-26 20:15:03',
            ),
            8 => 
            array (
                'id' => 9,
                'geo' => 'FR',
                'main_api' => 'ebay_commerce_network',
                'first_backfill_api' => 'twenga',
                'second_backfill_api' => 'twenga',
                'created_at' => '2016-03-26 20:15:04',
                'updated_at' => '2016-06-23 11:24:08',
            ),
            9 => 
            array (
                'id' => 10,
                'geo' => 'HK',
                'main_api' => 'dealspricer',
                'first_backfill_api' => '',
                'second_backfill_api' => '',
                'created_at' => '2016-03-26 20:15:14',
                'updated_at' => '2016-03-26 20:15:14',
            ),
            10 => 
            array (
                'id' => 11,
                'geo' => 'ID',
                'main_api' => 'dealspricer',
                'first_backfill_api' => '',
                'second_backfill_api' => '',
                'created_at' => '2016-03-26 20:15:16',
                'updated_at' => '2016-03-26 20:15:16',
            ),
            11 => 
            array (
                'id' => 12,
                'geo' => 'IN',
                'main_api' => 'dealspricer',
                'first_backfill_api' => '',
                'second_backfill_api' => '',
                'created_at' => '2016-03-26 20:15:17',
                'updated_at' => '2016-03-26 20:15:17',
            ),
            12 => 
            array (
                'id' => 13,
                'geo' => 'IT',
                'main_api' => 'kelkoo',
                'first_backfill_api' => 'kelkoo',
                'second_backfill_api' => '',
                'created_at' => '2016-03-26 20:15:19',
                'updated_at' => '2016-05-24 00:35:26',
            ),
            13 => 
            array (
                'id' => 14,
                'geo' => 'MY',
                'main_api' => 'dealspricer',
                'first_backfill_api' => '',
                'second_backfill_api' => '',
                'created_at' => '2016-03-26 20:15:23',
                'updated_at' => '2016-03-26 20:15:23',
            ),
            14 => 
            array (
                'id' => 15,
                'geo' => 'NG',
                'main_api' => 'dealspricer',
                'first_backfill_api' => '',
                'second_backfill_api' => '',
                'created_at' => '2016-03-26 20:15:25',
                'updated_at' => '2016-03-26 20:15:25',
            ),
            15 => 
            array (
                'id' => 16,
                'geo' => 'NL',
                'main_api' => 'kelkoo',
                'first_backfill_api' => 'twenga',
                'second_backfill_api' => '',
                'created_at' => '2016-03-26 20:15:30',
                'updated_at' => '2016-03-29 11:44:52',
            ),
            16 => 
            array (
                'id' => 17,
                'geo' => 'NO',
                'main_api' => 'kelkoo',
                'first_backfill_api' => '',
                'second_backfill_api' => '',
                'created_at' => '2016-03-26 20:15:31',
                'updated_at' => '2016-03-26 20:15:31',
            ),
            17 => 
            array (
                'id' => 18,
                'geo' => 'NZ',
                'main_api' => 'dealspricer',
                'first_backfill_api' => '',
                'second_backfill_api' => '',
                'created_at' => '2016-03-26 20:15:33',
                'updated_at' => '2016-03-26 20:15:33',
            ),
            18 => 
            array (
                'id' => 19,
                'geo' => 'PH',
                'main_api' => 'dealspricer',
                'first_backfill_api' => '',
                'second_backfill_api' => '',
                'created_at' => '2016-03-26 20:15:35',
                'updated_at' => '2016-03-26 20:15:35',
            ),
            19 => 
            array (
                'id' => 20,
                'geo' => 'PL',
                'main_api' => 'kelkoo',
                'first_backfill_api' => 'twenga',
                'second_backfill_api' => '',
                'created_at' => '2016-03-26 20:15:37',
                'updated_at' => '2016-03-29 11:44:43',
            ),
            20 => 
            array (
                'id' => 21,
                'geo' => 'PT',
                'main_api' => 'kelkoo',
                'first_backfill_api' => '',
                'second_backfill_api' => '',
                'created_at' => '2016-03-26 20:15:38',
                'updated_at' => '2016-03-26 20:15:38',
            ),
            21 => 
            array (
                'id' => 22,
                'geo' => 'RU',
                'main_api' => 'kelkoo',
                'first_backfill_api' => '',
                'second_backfill_api' => '',
                'created_at' => '2016-03-26 20:15:40',
                'updated_at' => '2016-03-26 20:15:40',
            ),
            22 => 
            array (
                'id' => 23,
                'geo' => 'SE',
                'main_api' => 'kelkoo',
                'first_backfill_api' => 'twenga',
                'second_backfill_api' => '',
                'created_at' => '2016-03-26 20:15:42',
                'updated_at' => '2016-03-29 11:44:57',
            ),
            23 => 
            array (
                'id' => 24,
                'geo' => 'SG',
                'main_api' => 'dealspricer',
                'first_backfill_api' => '',
                'second_backfill_api' => '',
                'created_at' => '2016-03-26 20:15:47',
                'updated_at' => '2016-03-26 20:15:47',
            ),
            24 => 
            array (
                'id' => 25,
                'geo' => 'TH',
                'main_api' => 'dealspricer',
                'first_backfill_api' => '',
                'second_backfill_api' => '',
                'created_at' => '2016-03-26 20:15:48',
                'updated_at' => '2016-03-26 20:15:48',
            ),
            25 => 
            array (
                'id' => 26,
                'geo' => 'TR',
                'main_api' => 'dealspricer',
                'first_backfill_api' => '',
                'second_backfill_api' => '',
                'created_at' => '2016-03-26 20:15:50',
                'updated_at' => '2016-03-26 20:15:50',
            ),
            26 => 
            array (
                'id' => 27,
                'geo' => 'UK',
                'main_api' => 'kelkoo',
                'first_backfill_api' => 'twenga',
                'second_backfill_api' => 'dealspricer',
                'created_at' => '2016-03-26 20:15:51',
                'updated_at' => '2016-03-29 11:44:36',
            ),
            27 => 
            array (
                'id' => 28,
                'geo' => 'US',
                'main_api' => 'connexity',
                'first_backfill_api' => 'dealspricer',
                'second_backfill_api' => '',
                'created_at' => '2016-03-26 20:16:01',
                'updated_at' => '2016-06-23 12:34:18',
            ),
            28 => 
            array (
                'id' => 29,
                'geo' => 'VN',
                'main_api' => 'dealspricer',
                'first_backfill_api' => '',
                'second_backfill_api' => '',
                'created_at' => '2016-03-26 20:16:13',
                'updated_at' => '2016-03-26 20:16:13',
            ),
        ));
        
        
    }
}
