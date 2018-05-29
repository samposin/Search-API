<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class SearchRequestAll extends Model
{
    protected $table = 'search_request_all_new';

    protected $fillable=[
        'dl_source',
        'sub_dl_source',
        'widget',
        'keyword',
        'domain',
        'ip',
        'user_country_code',
        'request_uri',
        'api_country_code',
        'api_used',
        'api_used_order',
        'category',
        'api_category',
        'api_category_id',
        'http_user_agent',
        'browser',
        'configurator_unique_id',
        'msg',
        'is_succeed',
        'is_completed',
    ];
}
