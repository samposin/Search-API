<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PublisherOnboarding extends Model
{
    protected $table = 'publisher_onboarding';

    protected $fillable=[
        'publisher_id',
        'created_publisher_from_configurator',
        'admin_section_add_publisher_name_email',
        'email_sent_to_publisher_with_js',
        'cross_check_analytics_profile_for_this_publisher',
        'test_generated_js_working_or_not',
        'test_generated_js_entering_data_on_analytics_or_not'
    ];


    public function publisher()
    {
        return $this->belongsTo('App\Publisher','publisher_id');
    }

}
