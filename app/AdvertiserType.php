<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AdvertiserType extends Model
{
    public function advertisers()
    {
        return $this->hasMany('App\Advertiser');
    }
}
