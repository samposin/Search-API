<?php

use App\Advertiser;
use App\Publisher;
use Carbon\Carbon;
use Illuminate\Database\Seeder;


class PublishersTableSeeder extends Seeder
{

    public function run()
    {

        DB::table('publishers')->delete();

		$advertiser_foxydeal=Advertiser::where('name', '=', 'FoxyDeal')->first(['id']);
		$advertiser_adworks=Advertiser::where('name', '=', 'AdWorks')->first(['id']);
		$advertiser_shopzilla=Advertiser::where('name', '=', 'Shopzilla')->first(['id']);
        $advertiser_dealspricer=Advertiser::where('name', '=', 'Dealspricer')->first(['id']);
        $advertiser_kelkoo=Advertiser::where('name', '=', 'Kelkoo')->first(['id']);
        $advertiser_twenga=Advertiser::where('name', '=', 'Twenga')->first(['id']);
        $advertiser_ebay=Advertiser::where('name', '=', 'Ebay')->first(['id']);


        $publisher = Publisher::create(['id' => '1', 'name' => 'ALL', 'is_delete'=>0,'created_at'=>Carbon::now(),'updated_at'=>Carbon::now()]);
        $publisher->advertisers()->attach(
            array(
                $advertiser_foxydeal->id=>array('publisher_id1' => 'VAPI','share'=>37.50),
                $advertiser_adworks->id=>array('publisher_id1' => 'VAPI','share'=>37.50),
                $advertiser_shopzilla->id=>array('publisher_id1' => 1,'share'=>0),
                $advertiser_dealspricer->id=>array('publisher_id1' => 3001,'share'=>0),
                $advertiser_kelkoo->id=>array('publisher_id1' => 8001,'share'=>0),
                $advertiser_twenga->id=>array('publisher_id1' => 1001,'share'=>0),
                $advertiser_ebay->id=>array('publisher_id1' => 2001,'share'=>0)
            )
        );
        $publisher = Publisher::create(['id' => '2', 'name' => 'WSKY', 'is_delete'=>0,'created_at'=>Carbon::now(),'updated_at'=>Carbon::now()]);
        $publisher->advertisers()->attach(
            array(
                $advertiser_foxydeal->id=>array('publisher_id1' => 'WSKY','share'=>37.50),
                $advertiser_adworks->id=>array('publisher_id1' => 'WSKY','share'=>37.50),
                $advertiser_shopzilla->id=>array('publisher_id1' => 2,'share'=>0),
                $advertiser_dealspricer->id=>array('publisher_id1' => 3002,'share'=>0),
                $advertiser_kelkoo->id=>array('publisher_id1' => 8002,'share'=>0),
                $advertiser_twenga->id=>array('publisher_id1' => 1002,'share'=>0),
                $advertiser_ebay->id=>array('publisher_id1' => 2002,'share'=>0)
            )
        );
        $publisher = Publisher::create(['id' => '3', 'name' => 'RAFO', 'is_delete'=>0,'created_at'=>Carbon::now(),'updated_at'=>Carbon::now()]);
        $publisher->advertisers()->attach(
            array(
                $advertiser_foxydeal->id=>array('publisher_id1' => 'RAFO','share'=>37.50),
                $advertiser_adworks->id=>array('publisher_id1' => 'RAFO','share'=>37.50),
                $advertiser_shopzilla->id=>array('publisher_id1' => 3,'share'=>0),
                $advertiser_dealspricer->id=>array('publisher_id1' => 3003,'share'=>0),
                $advertiser_kelkoo->id=>array('publisher_id1' => 8003,'share'=>0),
                $advertiser_twenga->id=>array('publisher_id1' => 1003,'share'=>0),
                $advertiser_ebay->id=>array('publisher_id1' => 2003,'share'=>0)
            )
        );
        $publisher = Publisher::create(['id' => '4', 'name' => 'TVDM', 'is_delete'=>0,'created_at'=>Carbon::now(),'updated_at'=>Carbon::now()]);
        $publisher->advertisers()->attach(
            array(
                $advertiser_foxydeal->id=>array('publisher_id1' => 'TVDM','share'=>37.50),
                $advertiser_adworks->id=>array('publisher_id1' => 'TVDM','share'=>37.50),
                $advertiser_shopzilla->id=>array('publisher_id1' => 4,'share'=>0),
                $advertiser_dealspricer->id=>array('publisher_id1' => 3004,'share'=>0),
                $advertiser_kelkoo->id=>array('publisher_id1' => 8004,'share'=>0),
                $advertiser_twenga->id=>array('publisher_id1' => 1004,'share'=>0),
                $advertiser_ebay->id=>array('publisher_id1' => 2004,'share'=>0)
            )
        );
        $publisher = Publisher::create(['id' => '5', 'name' => 'WAJA', 'is_delete'=>0,'created_at'=>Carbon::now(),'updated_at'=>Carbon::now()]);
        $publisher->advertisers()->attach(
            array(
                $advertiser_foxydeal->id=>array('publisher_id1' => 'WAJA','share'=>37.50),
                $advertiser_adworks->id=>array('publisher_id1' => 'WAJA','share'=>37.50),
                $advertiser_shopzilla->id=>array('publisher_id1' => 5,'share'=>0),
                $advertiser_dealspricer->id=>array('publisher_id1' => 3005,'share'=>0),
                $advertiser_kelkoo->id=>array('publisher_id1' => 8005,'share'=>0),
                $advertiser_twenga->id=>array('publisher_id1' => 1005,'share'=>0),
                $advertiser_ebay->id=>array('publisher_id1' => 2005,'share'=>0)
            )
        );
        $publisher = Publisher::create(['id' => '6', 'name' => 'FIND', 'is_delete'=>0,'created_at'=>Carbon::now(),'updated_at'=>Carbon::now()]);
        $publisher->advertisers()->attach(
            array(
                $advertiser_foxydeal->id=>array('publisher_id1' => 'FIND','share'=>37.50),
                $advertiser_adworks->id=>array('publisher_id1' => 'FIND','share'=>37.50),
                $advertiser_shopzilla->id=>array('publisher_id1' => 6,'share'=>0),
                $advertiser_dealspricer->id=>array('publisher_id1' => 3006,'share'=>0),
                $advertiser_kelkoo->id=>array('publisher_id1' => 8006,'share'=>0),
                $advertiser_twenga->id=>array('publisher_id1' => 1006,'share'=>0),
                $advertiser_ebay->id=>array('publisher_id1' => 2006,'share'=>0)
            )
        );
        $publisher = Publisher::create(['id' => '7', 'name' => 'ADKW', 'is_delete'=>0,'created_at'=>Carbon::now(),'updated_at'=>Carbon::now()]);
        $publisher->advertisers()->attach(
            array(
                $advertiser_foxydeal->id=>array('publisher_id1' => 'ADKW','share'=>37.50),
                $advertiser_adworks->id=>array('publisher_id1' => 'ADKW','share'=>37.50),
                $advertiser_shopzilla->id=>array('publisher_id1' => 7,'share'=>0),
                $advertiser_dealspricer->id=>array('publisher_id1' => 3007,'share'=>0),
                $advertiser_kelkoo->id=>array('publisher_id1' => 8007,'share'=>0),
                $advertiser_twenga->id=>array('publisher_id1' => 1007,'share'=>0),
                $advertiser_ebay->id=>array('publisher_id1' => 2007,'share'=>0)
            )
        );
        $publisher = Publisher::create(['id' => '8', 'name' => 'FVDM', 'is_delete'=>0,'created_at'=>Carbon::now(),'updated_at'=>Carbon::now()]);
        $publisher->advertisers()->attach(
            array(
                $advertiser_foxydeal->id=>array('publisher_id1' => 'FVDM','share'=>37.50),
                $advertiser_adworks->id=>array('publisher_id1' => 'FVDM','share'=>37.50),
                $advertiser_shopzilla->id=>array('publisher_id1' => 8,'share'=>0),
                $advertiser_dealspricer->id=>array('publisher_id1' => 3008,'share'=>0),
                $advertiser_kelkoo->id=>array('publisher_id1' => 8008,'share'=>0),
                $advertiser_twenga->id=>array('publisher_id1' => 1008,'share'=>0),
                $advertiser_ebay->id=>array('publisher_id1' => 2008,'share'=>0)
            )
        );
        $publisher = Publisher::create(['id' => '9', 'name' => 'TAED', 'is_delete'=>0,'created_at'=>Carbon::now(),'updated_at'=>Carbon::now()]);
        $publisher->advertisers()->attach(
            array(
                $advertiser_foxydeal->id=>array('publisher_id1' => 'TAED','share'=>37.50),
                $advertiser_adworks->id=>array('publisher_id1' => 'TAED','share'=>37.50),
                $advertiser_shopzilla->id=>array('publisher_id1' => 9,'share'=>0),
                $advertiser_dealspricer->id=>array('publisher_id1' => 3009,'share'=>0),
                $advertiser_kelkoo->id=>array('publisher_id1' => 8009,'share'=>0),
                $advertiser_twenga->id=>array('publisher_id1' => 1009,'share'=>0),
                $advertiser_ebay->id=>array('publisher_id1' => 2009,'share'=>0)
            )
        );
        $publisher = Publisher::create(['id' => '10', 'name' => 'KPRA', 'is_delete'=>0,'created_at'=>Carbon::now(),'updated_at'=>Carbon::now()]);
        $publisher->advertisers()->attach(
            array(
                $advertiser_foxydeal->id=>array('publisher_id1' => 'KPRA','share'=>37.50),
                $advertiser_adworks->id=>array('publisher_id1' => 'KPRA','share'=>37.50),
                $advertiser_shopzilla->id=>array('publisher_id1' => 10,'share'=>0),
                $advertiser_dealspricer->id=>array('publisher_id1' => 3010,'share'=>0),
                $advertiser_kelkoo->id=>array('publisher_id1' => 8010,'share'=>0),
                $advertiser_twenga->id=>array('publisher_id1' => 1010,'share'=>0),
                $advertiser_ebay->id=>array('publisher_id1' => 2010,'share'=>0)
            )
        );
        $publisher = Publisher::create(['id' => '11', 'name' => 'JKRO', 'is_delete'=>0,'created_at'=>Carbon::now(),'updated_at'=>Carbon::now()]);
        $publisher->advertisers()->attach(
            array(
                $advertiser_foxydeal->id=>array('publisher_id1' => 'JKRO','share'=>37.50),
                $advertiser_adworks->id=>array('publisher_id1' => 'JKRO','share'=>37.50),
                $advertiser_shopzilla->id=>array('publisher_id1' => 11,'share'=>0),
                $advertiser_dealspricer->id=>array('publisher_id1' => 3011,'share'=>0),
                $advertiser_kelkoo->id=>array('publisher_id1' => 8011,'share'=>0),
                $advertiser_twenga->id=>array('publisher_id1' => 1011,'share'=>0),
                $advertiser_ebay->id=>array('publisher_id1' => 2011,'share'=>0)
            )
        );
        $publisher = Publisher::create(['id' => '12', 'name' => 'BLAZ', 'is_delete'=>0,'created_at'=>Carbon::now(),'updated_at'=>Carbon::now()]);
        $publisher->advertisers()->attach(
            array(
                $advertiser_foxydeal->id=>array('publisher_id1' => 'BLAZ','share'=>37.50),
                $advertiser_adworks->id=>array('publisher_id1' => 'BLAZ','share'=>37.50),
                $advertiser_shopzilla->id=>array('publisher_id1' => 12,'share'=>0),
                $advertiser_dealspricer->id=>array('publisher_id1' => 3012,'share'=>0),
                $advertiser_kelkoo->id=>array('publisher_id1' => 8012,'share'=>0),
                $advertiser_twenga->id=>array('publisher_id1' => 1012,'share'=>0),
                $advertiser_ebay->id=>array('publisher_id1' => 2012,'share'=>0)
            )
        );
        $publisher = Publisher::create(['id' => '13', 'name' => 'AZIM', 'is_delete'=>0,'created_at'=>Carbon::now(),'updated_at'=>Carbon::now()]);
        $publisher->advertisers()->attach(
            array(
                $advertiser_foxydeal->id=>array('publisher_id1' => 'AZIM','share'=>37.50),
                $advertiser_adworks->id=>array('publisher_id1' => 'AZIM','share'=>37.50),
                $advertiser_shopzilla->id=>array('publisher_id1' => 13,'share'=>0),
                $advertiser_dealspricer->id=>array('publisher_id1' => 3013,'share'=>0),
                $advertiser_kelkoo->id=>array('publisher_id1' => 8013,'share'=>0),
                $advertiser_twenga->id=>array('publisher_id1' => 1013,'share'=>0),
                $advertiser_ebay->id=>array('publisher_id1' => 2013,'share'=>0)
            )
        );
        $publisher = Publisher::create(['id' => '14', 'name' => 'AZIM1', 'is_delete'=>0,'created_at'=>Carbon::now(),'updated_at'=>Carbon::now()]);
        $publisher->advertisers()->attach(
            array(
                $advertiser_foxydeal->id=>array('publisher_id1' => 'AZIM1','share'=>37.50),
                $advertiser_adworks->id=>array('publisher_id1' => 'AZIM1','share'=>37.50),
                $advertiser_shopzilla->id=>array('publisher_id1' => 14,'share'=>0),
                $advertiser_dealspricer->id=>array('publisher_id1' => 3014,'share'=>0),
                $advertiser_kelkoo->id=>array('publisher_id1' => 8014,'share'=>0),
                $advertiser_twenga->id=>array('publisher_id1' => 1014,'share'=>0),
                $advertiser_ebay->id=>array('publisher_id1' => 2014,'share'=>0)
            )
        );
    }
}