<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SearchClickReport extends Model
{
    protected $table = 'search_click_report';

    protected $fillable=[
        'total_search',
        'total_search_advertiser_call',
        'total_search_advertiser_call_with_result',
        'total_viewed',
        'total_clicked',
        'dl_source',
        'sub_dl_source',
        'widget',
        'country_code',
        'api',
        'jsver',
        'date',
        'hour_int',
        'hour_display'
    ];
}
