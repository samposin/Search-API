<?php

namespace App\Models;

use App\Helpers\FeedProvider;
use Illuminate\Database\Eloquent\Model;

class AdvertiserResource extends Model
{
    private $feedProvider;
    //
    protected $table = 'advertiser_resource';

    /**
     * @return FeedProvider
     */
    public function feedProvider()
    {
        if (null === $this->feedProvider) {
            $options = json_decode($this->pivot->options, true);
            $class = 'App\Helpers\FeedProvider\\'.$this->name;
            $this->feedProvider = new $class($options);
        }

        return $this->feedProvider;
    }
}
