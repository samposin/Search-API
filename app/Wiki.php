<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Wiki extends Model
{
    protected $table = 'wiki_save';

    protected $fillable=[
        'user',
        'title',
        'description',
        'keyword',
        'date',
        'created_at',
        'updated_at'
    ];

}
