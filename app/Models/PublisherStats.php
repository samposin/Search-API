<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PublisherStats extends Model
{
    protected $table = "publisher_stats";
    protected $fillable = [
        'date', 'publisher_id', 'advertiser_id', 'dl_source', 'sub_dl_source', 'country', 'widget', 'searches', 'clicks', 'revenue', 'rate'
    ];
}
