<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AdvertiserPublisherSearchDefault extends Model
{
    protected $table = 'advertiser_publisher_search_defaults';

    protected $fillable=[
        'publisher_id',
        'geo',
        'main_api',
        'first_backfill_api',
        'second_backfill_api'
    ];

    public function publisher()
    {
        return $this->belongsTo('App\Publisher','publisher_id');
    }
}
