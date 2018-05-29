<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'countries';

    public function companies()
    {
        return $this->hasMany('App\Company');
    }

    public function getNameAttribute()
    {
        return $this->attributes['name'];
    }
}
