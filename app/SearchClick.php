<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SearchClick extends Model
{
    protected $table = 'search_clicks';

    protected $fillable=[
        'search_id',
        'api',
        'dl_source',
        'sub_dl_source',
        'widget',
        'domain',
        'country_code',
        'clicks',
        'date',
        'jsver',
        'category',
        'api_category',
        'api_category_id',
        'keyword',
        'ip',
        'http_user_agent',
        'browser'
    ];
}
