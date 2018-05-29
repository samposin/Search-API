<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;


class AdvertiserTypesTableSeeder extends Seeder
{

    public function run()
    {
        DB::table('advertiser_types')->delete();

        $advertiser_types = array(
            array('id'=>1,'type'=>"shopping","is_delete"=>0,"created_at"=>Carbon::now(),"updated_at"=>Carbon::now()),
            array('id'=>2,'type'=>"jobs","is_delete"=>0,"created_at"=>Carbon::now(),"updated_at"=>Carbon::now()),
            array('id'=>3,'type'=>"search augment","is_delete"=>0,"created_at"=>Carbon::now(),"updated_at"=>Carbon::now())
        );

        DB::table('advertiser_types')->insert($advertiser_types);
    }
}