<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SearchInfo extends Model
{
    protected $table = 'search_info';

    protected $fillable=[
        'widget',
        'keyword',
        'domain',
        'ip',
        'request_uri',
        'http_user_agent',
        'is_viewed'
    ];
}
