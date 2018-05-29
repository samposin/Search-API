<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SearchFeed extends Model
{
    protected $table = 'search_feeds';

    protected $fillable=[
        'client_name',
        'url',
        'is_active',
        'is_delete'
    ];
}
