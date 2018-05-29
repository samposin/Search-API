<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ClickAdvertiserInternal extends Model
{
    protected $table = 'clicks_advertiser_internal';

    protected $fillable=[
        'date',
        'api',
        'api_clicks',
        'internal_clicks'
    ];
}
