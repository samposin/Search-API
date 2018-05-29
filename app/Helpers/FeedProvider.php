<?php

namespace App\Helpers;

use App\CurrencyExchangeRate;

abstract class FeedProvider
{
    protected $data = [];

    protected $options = [];

    public function __construct($options = [])
    {
        $this->options = array_merge($this->options, $options);
    }

    public function getOptions()
    {
        return $this->options;
    }

    protected abstract function process();

    public function getData()
    {
        $this->data = [];
        $this->process();
        return $this->data;
    }

    public function getTmpFolder()
    {
        return storage_path('app');
    }

    /**
     * Get currency convert value and rate
     *
     * @param $currency
     * @param $amount
     * @param $date
     * @return array [0 => value, 1 => rate]
     */
    public function currencyConvertToUSD($currency, $amount, $date)
    {
        $result = [0, 0];

        if ($amount == 0) {
            return $result;
        }

        if ($currency == 'USD') {
            return [$amount, 1];
        }

        /** @var CurrencyExchangeRate $rate */
        $rate = CurrencyExchangeRate::where('date', '=', $date)
            ->where('from_currency', '=', 'USD')
            ->where('to_currency', '=', $currency)
            ->first();

        if ($rate) {
            $result = [$amount / $rate->rate, $rate->rate];
        }

        return $result;
    }

}

class FeedProviderDataNotFoundException extends \Exception {};
class FeedProviderResourceConnectionErrordException extends \Exception {};
class FeedProviderResourceDownloadFailed extends \Exception {};
class FeedProviderWrongResourceFormat extends \Exception {};