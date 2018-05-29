<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class VisionApiReport extends Model
{
    protected $table = 'visionapi_reports';

    protected $fillable=[
        'date',
        'dl_source',
        'sub_dl_source',
        'widget',
        'country',
        'searches',
        'clicks',
        'estimated_revenue',
        'symbol',
        'file_output_type',
        'advertiser_name',
        'is_estimated'
    ];
}
