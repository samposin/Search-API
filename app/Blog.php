<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
    protected $table = 'wiki_save';

    protected $fillable=[
        'user',
        'title',
        'description',
        'keyword',
        'category',
        'date',

    ];




}
