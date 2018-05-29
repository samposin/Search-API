<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdvertiserStats extends Model
{
    protected $table = 'advertiser_stats';

    public function scopeForPeriod($query, $from, $to)
    {
        return $query->where('date', '>=', $from)
            ->where('date', '<=', $to);
    }

    public function advertiser()
    {
        return $this->belongsTo('App\Advertiser', 'advertiser_id');
    }
}
