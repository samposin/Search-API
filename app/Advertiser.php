<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class Advertiser
 * @method Collection available()
 * @package App
 */
class Advertiser extends Model
{
    protected $table = 'advertisers';

    protected $fillable = [
        'name',
        'type_id',
        'info',
        'is_delete',
    ];

    public function publishers()
    {
        //return $this->hasMany('App\Publisher');
        return
            $this->belongsToMany('App\Publisher', 'advertisers_publishers', 'advertiser_id', 'publisher_id')
                ->withPivot('publisher_id1', 'share'); // first field belong to this model that is advertiser_id
    }

    public function advertiser_widgets()
    {
        return $this->belongsToMany(
            'App\AdvertiserWidget',
            "advertiser_widgets_advertisers",
            "advertiser_id",
            "advertiser_widget_id"
        );
    }

    public function advertiser_type()
    {
        return $this->belongsTo('App\AdvertiserType', 'type_id');
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builderdev $query
     * @return Builder
     */
    public function scopeAvailable($query)
    {
        return $query->where('is_delete', 0);
    }

    public function resources()
    {
        return $this->belongsToMany('App\Models\AdvertiserResource', 'advertiser_has_resource', 'advertiser_id', 'advertiser_resource_id')->withPivot('options', 'name');
    }

}
