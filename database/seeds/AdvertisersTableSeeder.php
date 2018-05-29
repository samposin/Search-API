<?php

use Illuminate\Database\Seeder;

class AdvertisersTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {

        \DB::table('advertisers')->delete();
        
        \DB::table('advertisers')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => 'Dealspricer',
                'type_id' => 1,
                'info' => '',
                'is_delete' => 0,
                'created_at' => '2016-03-10 17:34:27',
                'updated_at' => '2016-04-03 03:31:09',
            ),
            1 => 
            array (
                'id' => 2,
                'name' => 'Ebay US',
                'type_id' => 1,
                'info' => '',
                'is_delete' => 0,
                'created_at' => '2016-03-10 17:34:27',
                'updated_at' => '2016-04-02 13:05:24',
            ),
            2 => 
            array (
                'id' => 3,
                'name' => 'FoxyDeal',
                'type_id' => 1,
                'info' => '',
                'is_delete' => 0,
                'created_at' => '2016-03-10 17:34:27',
                'updated_at' => '2016-04-03 03:31:43',
            ),
            3 => 
            array (
                'id' => 4,
                'name' => 'Kelkoo',
                'type_id' => 1,
                'info' => '',
                'is_delete' => 0,
                'created_at' => '2016-03-10 17:34:27',
                'updated_at' => '2016-04-03 03:32:02',
            ),
            4 => 
            array (
                'id' => 5,
                'name' => 'Pricegrabber',
                'type_id' => 1,
                'info' => '',
                'is_delete' => 0,
                'created_at' => '2016-03-10 17:34:27',
                'updated_at' => '2016-04-03 03:32:18',
            ),
            5 => 
            array (
                'id' => 6,
                'name' => 'Connexity',
                'type_id' => 1,
                'info' => '',
                'is_delete' => 0,
                'created_at' => '2016-03-10 17:34:27',
                'updated_at' => '2016-04-03 03:32:39',
            ),
            6 => 
            array (
                'id' => 7,
                'name' => 'Twenga',
                'type_id' => 1,
                'info' => '',
                'is_delete' => 0,
                'created_at' => '2016-03-10 17:34:27',
                'updated_at' => '2016-04-03 03:33:23',
            ),
            7 => 
            array (
                'id' => 8,
                'name' => 'Visicom',
                'type_id' => 1,
                'info' => '',
                'is_delete' => 0,
                'created_at' => '2016-03-10 17:34:27',
                'updated_at' => '2016-04-03 03:33:39',
            ),
            8 => 
            array (
                'id' => 9,
                'name' => 'Zoom',
                'type_id' => 1,
                'info' => '',
                'is_delete' => 0,
                'created_at' => '2016-03-10 17:34:27',
                'updated_at' => '2016-04-03 03:33:52',
            ),
            9 => 
            array (
                'id' => 10,
                'name' => 'AdWorks',
                'type_id' => 2,
                'info' => '',
                'is_delete' => 0,
                'created_at' => '2016-03-10 17:34:27',
                'updated_at' => '2016-04-03 03:34:09',
            ),
            10 => 
            array (
                'id' => 11,
                'name' => 'Ebay UK',
                'type_id' => 1,
                'info' => '',
                'is_delete' => 0,
                'created_at' => '2016-04-02 13:05:45',
                'updated_at' => '2016-04-02 13:05:45',
            ),
            11 => 
            array (
                'id' => 12,
                'name' => 'Ebay FR',
                'type_id' => 1,
                'info' => '',
                'is_delete' => 0,
                'created_at' => '2016-04-02 13:06:14',
                'updated_at' => '2016-04-02 13:06:14',
            ),
            12 => 
            array (
                'id' => 13,
                'name' => 'Ebay DE',
                'type_id' => 1,
                'info' => '',
                'is_delete' => 0,
                'created_at' => '2016-04-02 13:06:30',
                'updated_at' => '2016-04-02 13:06:30',
            ),
            13 => 
            array (
                'id' => 14,
                'name' => 'Ebay AU',
                'type_id' => 1,
                'info' => '',
                'is_delete' => 0,
                'created_at' => '2016-04-02 13:06:46',
                'updated_at' => '2016-04-02 13:06:46',
            ),
            14 => 
            array (
                'id' => 15,
                'name' => 'Rank Dynamics',
                'type_id' => 3,
                'info' => '',
                'is_delete' => 0,
                'created_at' => '2016-04-19 06:58:32',
                'updated_at' => '2016-04-19 06:58:32',
            ),
        ));

    }
}
