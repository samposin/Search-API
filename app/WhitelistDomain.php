<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WhitelistDomain extends Model
{
    protected $table = 'whitelist_domains';

    protected $fillable=[
        'domain'
    ];
}
