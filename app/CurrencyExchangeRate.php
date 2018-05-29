<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CurrencyExchangeRate extends Model
{
    protected $table = 'currency_exchange_rates';

    protected $fillable=[
        'date',
        'from_currency',
        'to_currency',
        'rate'
    ];
}
