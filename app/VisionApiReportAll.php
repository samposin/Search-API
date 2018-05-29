<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class VisionApiReportAll extends Model
{
    protected $table = 'visionapi_report_all';

    protected $fillable=[
        'date',
        'dl_source',
        'sub_dl_source',
        'widget',
        'country',
        'searches',
        'clicks',
        'estimated_revenue',
        'advertiser_name',
        'total_clicks',
        'cost_per_click',
        'is_estimated'
    ];
}
