<?php

namespace App;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Publisher
 * @package App
 * @method Collection available()
 */
class Publisher extends Model
{

    protected $table = 'publishers';

    protected $fillable=[
        'name',
        'email',
        'is_delete'
    ];

    public function advertisers()
    {
        //return $this->belongsTo('App\Advertiser','advertiser_id');
        return
            $this->belongsToMany('App\Advertiser', 'advertisers_publishers','publisher_id','advertiser_id')->withPivot('publisher_id1','share'); // first field belong to this model that is publisher_id
    }

    // Get first advertiser
	public function advertiser() {
		return
            $this->belongsToMany('App\Advertiser', 'advertisers_publishers','publisher_id','advertiser_id')->withPivot('publisher_id1','share')->limit(1); // first field belong to this model that is publisher_id
	}

    public function getDisplayShareAttribute()
    {
        return $this->pivot->share . ' %';
    }

    public function configurator_generated_js()
    {
        return $this->hasMany('App\ConfiguratorGeneratedJs');
    }

    public function publisher_onboarding()
    {
        return $this->hasMany('App\PublisherOnboarding');
    }

    public function publisher_search_defaults()
    {
        return $this->hasMany('App\AdvertiserPublisherSearchDefault');
    }

    public function scopeAvailable($query)
    {
        return $query->where('is_delete', 0);
    }
}
