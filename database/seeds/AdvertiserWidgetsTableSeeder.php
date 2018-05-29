<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;


class AdvertiserWidgetsTableSeeder extends Seeder
{

    public function run()
    {
        DB::table('advertiser_widgets')->delete();

        $advertiser_widgets = array(
            array('id'=>1,'name'=>"filmstrip","is_delete"=>0,"created_at"=>Carbon::now(),"updated_at"=>Carbon::now()),
            array('id'=>2,'name'=>"image popover","is_delete"=>0,"created_at"=>Carbon::now(),"updated_at"=>Carbon::now()),
            array('id'=>3,'name'=>"product image","is_delete"=>0,"created_at"=>Carbon::now(),"updated_at"=>Carbon::now()),
            array('id'=>4,'name'=>"search","is_delete"=>0,"created_at"=>Carbon::now(),"updated_at"=>Carbon::now()),
            array('id'=>5,'name'=>"sidebar","is_delete"=>0,"created_at"=>Carbon::now(),"updated_at"=>Carbon::now()),
            array('id'=>6,'name'=>"top bar","is_delete"=>0,"created_at"=>Carbon::now(),"updated_at"=>Carbon::now())
        );

        DB::table('advertiser_widgets')->insert($advertiser_widgets);
    }
}