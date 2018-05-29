<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AdvertiserSearchDefault extends Model
{
    protected $table = 'advertiser_search_defaults';

    protected $fillable=[
        'geo',
        'main_api',
        'first_backfill_api',
        'second_backfill_api'
    ];
}
