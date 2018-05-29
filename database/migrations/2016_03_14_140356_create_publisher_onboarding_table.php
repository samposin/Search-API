<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePublisherOnboardingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('publisher_onboarding', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('publisher_id')->unsigned();
            $table->tinyInteger('created_publisher_from_configurator')->default(0)->comment = "Created Publisher from configurator";
            $table->tinyInteger('admin_section_add_publisher_name_email')->default(0)->comment = "Admin section - Add Publisher Name and email ID";
            $table->tinyInteger('email_sent_to_publisher_with_js')->default(0)->comment = "Email sent to Publisher with JS";
            $table->tinyInteger('cross_check_analytics_profile_for_this_publisher')->default(0)->comment = "Cross check analytics profile for this publisher";
            $table->tinyInteger('test_generated_js_working_or_not')->default(0)->comment = "Test Generated JS working or not";
            $table->tinyInteger('test_generated_js_entering_data_on_analytics_or_not')->default(0)->comment = "Test Generated JS entering data on Analytics or not";
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('publisher_onboarding');
    }
}
