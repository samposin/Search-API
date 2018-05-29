<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ConfiguratorGeneratedJs extends Model
{
    protected $table = 'configurator_generated_js';

    protected $fillable=[
        'publisher_id',
        'configurator_unique_id',
        'piwik_website_id',
        'piwik_website_name',
        'partner_name',
        'product_name',
        'product_sub_id',
        'publisher_configurator_generated_js_no',
        'is_delete'
    ];


    public function publisher()
    {
        return $this->belongsTo('App\Publisher','publisher_id');
    }


}
