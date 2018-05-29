<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CronExecutionInfo extends Model
{
    protected $table = 'cron_execution_info';

    protected $fillable=[
        'date',
        'currency',
        'reporting'
    ];
}
