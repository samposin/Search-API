<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AdvertiserWidget extends Model
{
    protected $fillable=[
        "name"
    ];

    public function advertisers()
    {
        return $this->belongsToMany('App\Advertiser',"advertiser_widgets_advertisers","advertiser_widget_id","advertiser_id");
    }
}
